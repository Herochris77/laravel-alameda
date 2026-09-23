<?php

namespace App\Services;

use App\Models\CorteMensual;
use App\Models\Detallepago;
use App\Models\Documento;
use App\Models\Pago;
use App\Models\SaldoMovimiento;
use App\Models\Sancion;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Reporte mensual de ingresos y egresos.
 *
 * Reproduce el formato que la mesa directiva provisional venía entregando en papel, con
 * las mismas líneas: cuota del mes, cuotas adelantadas, penalización por pago
 * tardío, multas y derramas o proyectos, más los egresos del periodo.
 *
 * Criterio: FLUJO DE CAJA. Cuenta el dinero por la fecha en que entró, que es
 * como se cuadra contra el estado de cuenta del banco. Los recibos liquidados
 * con saldo a favor no cuentan como ingreso del mes: ese dinero entró cuando
 * el vecino adelantó.
 */
class ReporteMensualService
{
    /**
     * Arma todas las cifras de un periodo (formato Y-m).
     */
    public function datos(string $periodo): array
    {
        $inicio = Carbon::parse($periodo.'-01')->startOfMonth();
        $fin = $inicio->copy()->endOfMonth();

        $cierreAnterior = CorteMensual::anteriorA($periodo);
        $cierre = CorteMensual::where('periodo', $periodo)->first();

        $ingresos = $this->ingresos($inicio, $fin);
        $egresos = $this->egresos($inicio, $fin);

        $totalIngresos = round(array_sum(array_column($ingresos, 'monto')), 2);
        $totalEgresos = round(array_sum(array_column($egresos, 'monto')), 2);

        $saldoInicial = $cierreAnterior ? $cierreAnterior->total() : 0.0;

        // Si el mes ya tiene cierre capturado se usa ese; si no, se proyecta.
        $saldoFinal = $cierre
            ? $cierre->total()
            : round($saldoInicial + $totalIngresos - $totalEgresos, 2);

        return [
            'periodo' => $periodo,
            'periodo_texto' => mb_strtoupper($inicio->translatedFormat('F Y')),
            'inicio' => $inicio,
            'fin' => $fin,

            'saldo_inicial' => $saldoInicial,
            'cierre_anterior' => $cierreAnterior,

            'ingresos' => $ingresos,
            'total_ingresos' => $totalIngresos,

            'egresos' => $egresos,
            'total_egresos' => $totalEgresos,

            'saldo_final' => $saldoFinal,
            'cierre' => $cierre,

            // Diferencia entre lo que debería haber y lo capturado del banco.
            //
            // Sin cierre anterior el mes arranca en cero, así que la resta daría
            // el acumulado histórico completo y se leería como un descuadre que
            // no existe. En ese caso no hay nada que comparar.
            'descuadre' => ($cierre && $cierreAnterior)
                ? round($cierre->total() - ($saldoInicial + $totalIngresos - $totalEgresos), 2)
                : 0.0,

            'firmas' => $this->firmas(),
        ];
    }

    /**
     * Ingresos del periodo, desglosados como en el reporte de papel.
     */
    private function ingresos(Carbon $inicio, Carbon $fin): array
    {
        $sqlFecha = 'COALESCE(detallepagos.fecha_pago, DATE(detallepagos.updated_at))';

        $detalles = Detallepago::with('pago')
            ->where('estado', 'pagado')
            // Un recibo cubierto con saldo no es dinero que entró este mes.
            ->sinLiquidacionesConSaldo()
            ->whereBetween(DB::raw($sqlFecha), [$inicio->toDateString(), $fin->toDateString()])
            ->get()
            ->filter(fn ($d) => $d->pago);

        $cuotaMes = 0.0;      // cuota ordinaria de mantenimiento del periodo
        $adelantos = 0.0;     // lo pagado por encima de la cuota
        $recargos = 0.0;      // penalización por pago tardío
        $porConcepto = [];    // derramas y proyectos, cada uno su línea

        foreach ($detalles as $d) {
            $desglose = $d->desglosePago();
            $esMantenimiento = stripos($d->pago->concepto, 'mantenimiento') !== false;

            $recargos += $desglose['recargo'];
            $adelantos += $desglose['excedente'];

            $base = min((float) $d->cantidad_pago, (float) $d->pago->cantidad);

            if ($esMantenimiento) {
                $cuotaMes += $base;

                continue;
            }

            $porConcepto[$d->pago->concepto] = ($porConcepto[$d->pago->concepto] ?? 0) + $base;
        }

        $multas = (float) Sancion::where('estado', 'pagado')
            ->whereBetween(
                DB::raw('COALESCE(sanciones.fecha_pago, DATE(sanciones.updated_at))'),
                [$inicio->toDateString(), $fin->toDateString()]
            )
            ->sum('monto');

        $lineas = [];

        if ($cuotaMes > 0) {
            $lineas[] = ['concepto' => 'Cuota de mantenimiento '.mb_strtolower($inicio->translatedFormat('F Y')), 'monto' => round($cuotaMes, 2)];
        }

        if ($adelantos > 0) {
            $lineas[] = ['concepto' => 'Cuotas de mantenimiento adelantadas', 'monto' => round($adelantos, 2)];
        }

        if ($recargos > 0) {
            $lineas[] = ['concepto' => 'Penalización por pago tardío', 'monto' => round($recargos, 2)];
        }

        $lineas[] = ['concepto' => 'Multas', 'monto' => round($multas, 2)];

        arsort($porConcepto);

        foreach ($porConcepto as $concepto => $monto) {
            // Un concepto sin cobros en el mes no aporta nada al reporte.
            if (round($monto, 2) <= 0) {
                continue;
            }

            $lineas[] = ['concepto' => $concepto, 'monto' => round($monto, 2)];
        }

        return $lineas;
    }

    /**
     * Egresos del periodo, un renglón por documento.
     */
    private function egresos(Carbon $inicio, Carbon $fin): array
    {
        // Se fecha por el movimiento bancario real. Para los gastos anteriores
        // a `fecha_gasto` no hay más remedio que usar la fecha de subida, y
        // por eso viaja `fecha_real`: la pantalla los marca como aproximados
        // en vez de presentarlos como exactos.
        $sqlFecha = 'COALESCE(documentos.fecha_gasto, DATE(documentos.created_at))';

        return Documento::gastos()
            ->whereRaw("$sqlFecha BETWEEN ? AND ?", [$inicio->toDateString(), $fin->toDateString()])
            ->orderByRaw($sqlFecha)
            ->get()
            ->map(fn ($d) => [
                'concepto' => $d->titulo,
                'categoria' => $d->categoria_gasto ?? 'otro',
                'monto' => round((float) $d->cantidad, 2),
                'fecha' => optional($d->fechaEfectiva())->format('d/m/Y'),
                'fecha_real' => $d->fechaEsReal(),
                'proveedor' => $d->proveedor,
                'comprobante' => $this->comprobante($d),
            ])
            ->all();
    }

    /**
     * El archivo que respalda un egreso, listo para el anexo del PDF.
     *
     * Devuelve SIEMPRE un arreglo, aunque el comprobante no se pueda mostrar:
     * el anexo tiene que poder decir "este gasto trae un PDF" o "falta el
     * archivo" en vez de omitir el renglón y dar a entender que no había nada.
     *
     * Los tres casos que no se incrustan:
     *
     *   - PDF. dompdf no sabe meter un PDF dentro de otro. El archivo sigue
     *     en el módulo de Documentos; el anexo lo nombra.
     *   - PNG en un servidor sin la extensión GD. No es que la imagen salga
     *     mal: dompdf lanza una excepción y se cae el documento ENTERO. Por
     *     eso se descarta antes, no se intenta.
     *   - El archivo ya no está en el disco.
     *
     * El tamaño se calcula aquí y se manda en pixeles exactos. Dejárselo a
     * `max-height` no sirve: dompdf lo ignora en las imágenes y un ticket
     * vertical se desborda de la hoja.
     */
    private function comprobante(Documento $d): array
    {
        $relativa = trim((string) $d->doc_path);
        $base = [
            'nombre' => $relativa !== '' ? basename($relativa) : '',
            'extension' => strtolower(pathinfo($relativa, PATHINFO_EXTENSION)),
            'ruta' => null,
            'ancho' => null,
            'alto' => null,
        ];

        if ($relativa === '') {
            return array_merge($base, ['estado' => 'falta', 'nota' => 'El egreso se capturó sin archivo de respaldo.']);
        }

        $ruta = storage_path('app/public/'.ltrim($relativa, '/'));

        if (! is_file($ruta)) {
            return array_merge($base, ['estado' => 'falta', 'nota' => 'El archivo ya no está en el servidor.']);
        }

        $base['ruta'] = $ruta;

        if ($base['extension'] === 'pdf') {
            return array_merge($base, [
                'estado' => 'pdf',
                'nota' => 'El comprobante es un archivo PDF. Se consulta y se descarga desde el módulo de Documentos.',
            ]);
        }

        if (! PdfService::puedeIncrustar($ruta)) {
            return array_merge($base, [
                'estado' => 'no_soportado',
                'nota' => 'Este servidor no puede incrustar esta imagen en el PDF. El archivo está completo en el módulo de Documentos.',
            ]);
        }

        [$ancho, $alto] = $this->medidasParaLaHoja($ruta);

        // array_merge y no "+": con el operador de union, las claves que
        // $base ya trae en null ganan, y la imagen terminaba sin medidas.
        return array_merge($base, ['estado' => 'imagen', 'nota' => null, 'ancho' => $ancho, 'alto' => $alto]);
    }

    /**
     * Escala la imagen para que quepa completa en una hoja carta.
     *
     * Área útil con los márgenes de la plantilla, menos el espacio del
     * encabezado del anexo.
     *
     * Casi todos los comprobantes son capturas de celular de unos 400 px de
     * ancho, que a tamaño natural ocupan media hoja y quedan chicas para
     * leerse impresas. Por eso se permite agrandar hasta el doble: se gana
     * legibilidad en papel sin llegar al punto en que la captura se deshace.
     */
    private function medidasParaLaHoja(string $ruta): array
    {
        $anchoMaximo = 744;
        $altoMaximo = 880;

        $info = @getimagesize($ruta);

        if ($info === false || empty($info[0]) || empty($info[1])) {
            return [$anchoMaximo, null];
        }

        [$ancho, $alto] = $info;

        $escala = min($anchoMaximo / $ancho, $altoMaximo / $alto, 2);

        return [(int) round($ancho * $escala), (int) round($alto * $escala)];
    }

    /**
     * Quiénes firman, tomado de los cargos de la mesa directiva provisional.
     *
     * Si un cargo no está asignado el renglón queda en blanco para firmarse
     * a mano, que es preferible a poner un nombre equivocado.
     */
    private function firmas(): array
    {
        $porCargo = function (string $cargo) {
            $u = User::whereIn('rol', ['administrador', 'super-administrador'])
                ->where('cargo', $cargo)
                ->where('estado', 1)
                ->first();

            return $u ? $u->nombre : '';
        };

        return [
            ['rol' => 'Elaboró', 'nombre' => $porCargo('tesorero'), 'cargo' => 'Tesorero'],
            ['rol' => 'Revisó y aprobó', 'nombre' => $porCargo('presidente'), 'cargo' => 'Presidente'],
            ['rol' => 'Vo. Bo.', 'nombre' => $porCargo('vocal'), 'cargo' => 'Vocal'],
        ];
    }

    /**
     * Periodos con movimiento, para ofrecerlos en el selector.
     */
    public function periodosDisponibles(): array
    {
        $meses = [];

        foreach (Pago::whereNotNull('vencimiento')->pluck('vencimiento') as $v) {
            $meses[substr($v, 0, 7)] = true;
        }

        foreach (Documento::whereNotNull('cantidad')->pluck('created_at') as $c) {
            $meses[substr((string) $c, 0, 7)] = true;
        }

        krsort($meses);

        return array_keys($meses);
    }
}
