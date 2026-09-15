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
 * Reproduce el formato que la mesa directiva venía entregando en papel, con
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
            ])
            ->all();
    }

    /**
     * Quiénes firman, tomado de los cargos de la mesa directiva.
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
