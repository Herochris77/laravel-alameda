<?php

namespace App\Http\Controllers\Usuario;

use App\Http\Controllers\Controller;
use App\Models\Detallepago;
use App\Models\Documento;
use App\Models\Sancion;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportesController extends Controller
{
    /**
     * Vista principal.
     */
    public function index(Request $request)
    {
        return view('usuario.reportes.index', [
            'data' => $this->datosTransparencia(),
        ]);
    }

    /**
     * Rango principal del dashboard.
     */
    private function getDateRange(): array
    {
        $fechaInicio = request(
            'fecha_inicio',
            now()->startOfYear()->format('Y-m-d')
        );

        $fechaFin = request(
            'fecha_fin',
            now()->format('Y-m-d')
        );

        return [
            'inicio' => $fechaInicio,
            'fin' => $fechaFin,
        ];
    }

    /*
     * =====================================================
     * CRITERIO DE FECHA EN LOS REPORTES
     * =====================================================
     *
     * Este módulo mezclaba tres criterios distintos para responder "cuándo
     * entró el dinero": created_at en los recibos, updated_at en las multas y
     * vencimiento en el Dashboard. Los totales no cuadraban entre sí.
     *
     * A partir de ahora hay un solo criterio, con dos lecturas declaradas:
     *
     *   FLUJO DE EFECTIVO  -> cuándo entró el dinero. Usa `fecha_pago`.
     *   DEVENGADO          -> a qué periodo corresponde. Usa `vencimiento`.
     *
     * Los reportes de ingresos son de FLUJO DE EFECTIVO.
     *
     * Para los pagos anteriores a la columna `fecha_pago` ese dato no existe.
     * En lugar de excluirlos (perdería todo el histórico) se aproxima con
     * `updated_at`, la fecha de validación, y cada respuesta informa cuántos
     * registros van aproximados para que la interfaz pueda advertirlo.
     */
    private const SQL_FECHA_EFECTIVA_PAGO = 'COALESCE(detallepagos.fecha_pago, DATE(detallepagos.updated_at))';

    private const SQL_FECHA_EFECTIVA_SANCION = 'COALESCE(sanciones.fecha_pago, DATE(sanciones.updated_at))';

    /**
     * Fecha con la que un gasto entra al mes.
     *
     * Misma regla que usa el reporte mensual: manda la fecha del movimiento
     * bancario. Antes esta pantalla fechaba por `created_at`, el día en que
     * se subió el PDF, y eso hacía que un gasto pagado el 30 apareciera en el
     * mes siguiente si el comprobante se subía dos días después. Dos pantallas
     * del mismo sistema diciendo meses distintos es justo lo que no puede
     * pasar en un módulo de transparencia.
     */
    private const SQL_FECHA_EFECTIVA_GASTO = 'COALESCE(documentos.fecha_gasto, DATE(documentos.created_at))';

    /**
     * Fecha efectiva de un registro ya cargado en memoria.
     */
    private function fechaEfectiva($modelo): Carbon
    {
        return $modelo->fecha_pago
            ? Carbon::parse($modelo->fecha_pago)
            : Carbon::parse($modelo->updated_at);
    }

    /**
     * Nombre de usuario, incluso si fue eliminado.
     */
    private function getUserName($userId): string
    {
        $user = User::withTrashed()->find($userId);

        if (! $user) {
            return 'Usuario eliminado';
        }

        if ($user->trashed()) {
            return $user->nombre.' (Eliminado)';
        }

        return $user->nombre;
    }

    /**
     * ============================================================
     * API / MÉTODOS EXISTENTES
     * ============================================================
     */

    public function resumenGeneral()
    {
        $range = $this->getDateRange();

        $detallesPago = Detallepago::where('estado', 'pagado')
            ->sinLiquidacionesConSaldo()
            ->whereBetween(
                DB::raw(self::SQL_FECHA_EFECTIVA_PAGO),
                [$range['inicio'], $range['fin']]
            )
            ->with('pago')
            ->get();

        $totalPercibido = 0;
        $totalRecibosPagados = 0;
        $pagosPorConcepto = [];
        $usuariosQuePagaron = [];

        foreach ($detallesPago as $detalle) {
            $cantidad = floatval(
                $detalle->cantidad_pago
                ?: ($detalle->pago ? $detalle->pago->cantidad : 0)
            );

            $totalPercibido += $cantidad;
            $totalRecibosPagados += $cantidad;

            $concepto = $detalle->pago
                ? $detalle->pago->concepto
                : 'Sin concepto';

            if (! isset($pagosPorConcepto[$concepto])) {
                $pagosPorConcepto[$concepto] = 0;
            }

            $pagosPorConcepto[$concepto] += $cantidad;

            $usuariosQuePagaron[$detalle->user_id] = true;
        }

        $sancionesPagadas = Sancion::where('estado', 'pagado')
            ->whereBetween(
                DB::raw(self::SQL_FECHA_EFECTIVA_SANCION),
                [$range['inicio'], $range['fin']]
            )
            ->get();

        $ingresoSanciones = floatval(
            $sancionesPagadas->sum('monto')
        );

        $totalPercibido += $ingresoSanciones;

        if ($ingresoSanciones > 0) {
            $pagosPorConcepto['Multas'] = $ingresoSanciones;
        }

        $documentos = Documento::where('tipo', 'referencia')
            ->whereBetween(
                'created_at',
                [$range['inicio'], $range['fin'].' 23:59:59']
            )
            ->whereNotNull('cantidad')
            ->get();

        $totalGastos = 0;

        $gastosPorCategoria = [
            'luz' => 0,
            'agua' => 0,
            'gas' => 0,
            'mantenimiento' => 0,
            'seguridad' => 0,
            'limpieza' => 0,
            'jardineria' => 0,
            'otro' => 0,
        ];

        foreach ($documentos as $doc) {
            $cantidad = floatval($doc->cantidad);

            $totalGastos += $cantidad;

            $cat = $doc->categoria_gasto ?? 'otro';

            if (isset($gastosPorCategoria[$cat])) {
                $gastosPorCategoria[$cat] += $cantidad;
            } else {
                $gastosPorCategoria['otro'] += $cantidad;
            }
        }

        $sancionesTotales = Sancion::whereBetween(
            'created_at',
            [$range['inicio'], $range['fin'].' 23:59:59']
        )->count();

        $sancionesPendientes = Sancion::whereBetween(
            'created_at',
            [$range['inicio'], $range['fin'].' 23:59:59']
        )
            ->where('estado', 'pendiente')
            ->count();

        return response()->json([
            'total_percibido' => $totalPercibido,
            'total_gastos' => $totalGastos,
            'balance' => $totalPercibido - $totalGastos,
            'pagos_por_concepto' => $pagosPorConcepto,
            'gastos_por_categoria' => $gastosPorCategoria,
            'ingreso_sanciones' => $ingresoSanciones,
            'total_recibos_pagados' => $totalRecibosPagados,
            'usuarios_que_pagaron' => count($usuariosQuePagaron),
            'total_sanciones' => $sancionesTotales,
            'sanciones_pendientes' => $sancionesPendientes,
            'rango' => $range,
        ]);
    }

    public function ingresosDetalle()
    {
        $range = $this->getDateRange();

        // Mismo criterio para recibos y multas: antes uno usaba created_at y
        // el otro updated_at, así que las dos mitades del mismo listado no
        // eran comparables entre sí.
        $detallesPago = Detallepago::where('estado', 'pagado')
            ->sinLiquidacionesConSaldo()
            ->whereBetween(
                DB::raw(self::SQL_FECHA_EFECTIVA_PAGO),
                [$range['inicio'], $range['fin']]
            )
            ->with('pago')
            ->get();

        $aproximados = 0;
        $recibosData = [];

        foreach ($detallesPago as $detalle) {
            if (! $detalle->fecha_pago) {
                $aproximados++;
            }

            $recibosData[] = [
                'id' => $detalle->id,

                'concepto' => $detalle->pago
                    ? $detalle->pago->concepto
                    : 'Sin concepto',

                'monto' => floatval(
                    $detalle->cantidad_pago
                    ?: ($detalle->pago ? $detalle->pago->cantidad : 0)
                ),

                'fecha_pago' => $this->fechaEfectiva($detalle)
                    ->format('Y-m-d'),

                'fecha_aproximada' => ! $detalle->fecha_pago,

                'usuario' => $this->getUserName(
                    $detalle->user_id
                ),
            ];
        }

        $sanciones = Sancion::where('estado', 'pagado')
            ->whereBetween(
                DB::raw(self::SQL_FECHA_EFECTIVA_SANCION),
                [$range['inicio'], $range['fin']]
            )
            ->get();

        $sancionesData = $sanciones->map(function ($sancion) use (&$aproximados) {
            if (! $sancion->fecha_pago) {
                $aproximados++;
            }

            return [
                'id' => $sancion->id,
                'motivo' => $sancion->motivo,
                'monto' => floatval($sancion->monto),

                'fecha_pago' => $this->fechaEfectiva($sancion)
                    ->format('Y-m-d'),

                'fecha_aproximada' => ! $sancion->fecha_pago,

                'usuario' => $this->getUserName(
                    $sancion->user_id
                ),
            ];
        });

        return response()->json([
            'criterio' => 'flujo_efectivo',
            'aproximados' => $aproximados,
            'recibos' => $recibosData,
            'sanciones' => $sancionesData,

            'total_recibos' => collect($recibosData)
                ->sum('monto'),

            'total_sanciones' => $sancionesData
                ->sum('monto'),
        ]);
    }

    public function percepcionesPorMes()
    {
        $range = $this->getDateRange();

        // Flujo de efectivo: se agrupa por la fecha en que entró el dinero.
        $detallesPago = Detallepago::where('estado', 'pagado')
            ->sinLiquidacionesConSaldo()
            ->whereBetween(
                DB::raw(self::SQL_FECHA_EFECTIVA_PAGO),
                [$range['inicio'], $range['fin']]
            )
            ->with('pago')
            ->get();

        $sanciones = Sancion::where('estado', 'pagado')
            ->whereBetween(
                DB::raw(self::SQL_FECHA_EFECTIVA_SANCION),
                [$range['inicio'], $range['fin']]
            )
            ->get();

        $meses = [];

        for ($i = 1; $i <= 12; $i++) {
            $meses[$i] = 0;
        }

        $aproximados = 0;

        foreach ($detallesPago as $detalle) {
            $mes = $this->fechaEfectiva($detalle)->month;

            if (! $detalle->fecha_pago) {
                $aproximados++;
            }

            $cantidad = floatval(
                $detalle->cantidad_pago
                ?: ($detalle->pago ? $detalle->pago->cantidad : 0)
            );

            $meses[$mes] += $cantidad;
        }

        foreach ($sanciones as $sancion) {
            $mes = $this->fechaEfectiva($sancion)->month;

            if (! $sancion->fecha_pago) {
                $aproximados++;
            }

            $meses[$mes] += floatval(
                $sancion->monto
            );
        }

        return response()->json([
            'rango' => $range,
            'criterio' => 'flujo_efectivo',
            'aproximados' => $aproximados,
            'meses' => array_values($meses),

            'labels' => [
                'Ene',
                'Feb',
                'Mar',
                'Abr',
                'May',
                'Jun',
                'Jul',
                'Ago',
                'Sep',
                'Oct',
                'Nov',
                'Dic',
            ],
        ]);
    }

    public function gastosPorMes()
    {
        $range = $this->getDateRange();

        $documentos = Documento::where('tipo', 'referencia')
            ->whereRaw(
                self::SQL_FECHA_EFECTIVA_GASTO.' BETWEEN ? AND ?',
                [$range['inicio'], $range['fin']]
            )
            ->whereNotNull('cantidad')
            ->selectRaw(
                'MONTH('.self::SQL_FECHA_EFECTIVA_GASTO.') as mes, SUM(cantidad) as total'
            )
            ->groupBy('mes')
            ->orderBy('mes')
            ->get();

        $meses = [];

        for ($i = 1; $i <= 12; $i++) {
            $meses[$i] = 0;
        }

        foreach ($documentos as $doc) {
            $meses[$doc->mes] = floatval(
                $doc->total
            );
        }

        return response()->json([
            'rango' => $range,
            'meses' => array_values($meses),

            'labels' => [
                'Ene',
                'Feb',
                'Mar',
                'Abr',
                'May',
                'Jun',
                'Jul',
                'Ago',
                'Sep',
                'Oct',
                'Nov',
                'Dic',
            ],
        ]);
    }

    /**
     * Desglose de los egresos de UN mes.
     *
     * Es lo que se abre al pulsar una barra de la gráfica. Antes había que
     * bajar a la tabla y filtrar por fechas para responder "¿en qué se fue
     * agosto?", y eso hacía que nadie lo consultara.
     *
     * Devuelve el reparto por categoría y el detalle renglón por renglón, cada
     * uno con la liga a su comprobante. Los que no tienen archivo adjunto
     * viajan marcados: en un módulo de transparencia, un gasto sin respaldo es
     * justo lo que hay que poder ver.
     */
    public function egresosDelMes(Request $request)
    {
        $periodo = (string) $request->input('periodo', now()->format('Y-m'));

        if (! preg_match('/^\d{4}-\d{2}$/', $periodo)) {
            return response()->json(['error' => 'Periodo inválido.'], 422);
        }

        $inicio = Carbon::parse($periodo.'-01')->startOfMonth();
        $fin = (clone $inicio)->endOfMonth();

        $gastos = Documento::whereNotNull('cantidad')
            ->whereRaw(
                self::SQL_FECHA_EFECTIVA_GASTO.' BETWEEN ? AND ?',
                [$inicio->toDateString(), $fin->toDateString()]
            )
            ->orderByRaw(self::SQL_FECHA_EFECTIVA_GASTO.' DESC')
            ->get();

        $etiquetas = [
            'luz' => 'Luz', 'agua' => 'Agua', 'gas' => 'Gas',
            'mantenimiento' => 'Mantenimiento', 'seguridad' => 'Seguridad',
            'limpieza' => 'Limpieza', 'jardineria' => 'Jardinería',
            'administrativo' => 'Administrativo', 'otro' => 'Otros',
        ];

        $total = round($gastos->sum(fn ($g) => (float) $g->cantidad), 2);

        $porCategoria = $gastos
            ->groupBy(fn ($g) => $g->categoria_gasto ?: 'otro')
            ->map(fn ($grupo, $cat) => [
                'categoria' => $etiquetas[$cat] ?? ucfirst((string) $cat),
                'monto' => round($grupo->sum(fn ($g) => (float) $g->cantidad), 2),
                'pct' => $total > 0
                    ? round($grupo->sum(fn ($g) => (float) $g->cantidad) / $total * 100)
                    : 0,
            ])
            ->sortByDesc('monto')
            ->values();

        return response()->json([
            'periodo' => $periodo,
            'periodo_texto' => $inicio->translatedFormat('F \d\e Y'),
            'total' => $total,
            'cuantos' => $gastos->count(),
            'sin_comprobante' => $gastos->filter(fn ($g) => ! $g->doc_path)->count(),
            'sin_fecha_real' => $gastos->filter(fn ($g) => ! $g->fechaEsReal())->count(),
            'categorias' => $porCategoria,
            'gastos' => $gastos->map(fn ($g) => [
                'titulo' => $g->titulo ?: 'Movimiento sin descripción',
                'categoria' => $etiquetas[$g->categoria_gasto ?: 'otro'] ?? 'Otros',
                'monto' => round((float) $g->cantidad, 2),
                'fecha' => optional($g->fechaEfectiva())->format('d/m/Y'),
                'fecha_real' => $g->fechaEsReal(),
                'proveedor' => $g->proveedor,
                'comprobante' => $g->doc_path ? asset('storage/'.$g->doc_path) : null,
            ])->values(),
        ]);
    }

    public function gastosPorCategoria()
    {
        $range = $this->getDateRange();

        $documentos = Documento::where('tipo', 'referencia')
            ->whereRaw(
                self::SQL_FECHA_EFECTIVA_GASTO.' BETWEEN ? AND ?',
                [$range['inicio'], $range['fin']]
            )
            ->whereNotNull('cantidad')
            ->selectRaw(
                'categoria_gasto, SUM(cantidad) as total'
            )
            ->groupBy('categoria_gasto')
            ->orderBy('total', 'desc')
            ->get();

        $categorias = [];

        foreach ($documentos as $doc) {
            $cat = $doc->categoria_gasto ?? 'otro';

            $categorias[$cat] = floatval(
                $doc->total
            );
        }

        return response()->json(
            $categorias
        );
    }

    public function pagosPorConcepto()
    {
        $range = $this->getDateRange();

        $detallesPago = Detallepago::where('estado', 'pagado')
            ->sinLiquidacionesConSaldo()
            ->whereBetween(
                DB::raw(self::SQL_FECHA_EFECTIVA_PAGO),
                [$range['inicio'], $range['fin']]
            )
            ->with('pago')
            ->get();

        $agrupado = [];

        foreach ($detallesPago as $detalle) {
            $concepto = $detalle->pago
                ? $detalle->pago->concepto
                : 'Sin concepto';

            $cantidad = floatval(
                $detalle->cantidad_pago
                ?: ($detalle->pago ? $detalle->pago->cantidad : 0)
            );

            if (! isset($agrupado[$concepto])) {
                $agrupado[$concepto] = [
                    'concepto' => $concepto,
                    'total' => 0,
                    'cantidad' => 0,
                ];
            }

            $agrupado[$concepto]['total'] += $cantidad;
            $agrupado[$concepto]['cantidad']++;
        }

        return response()->json(
            collect($agrupado)
                ->sortByDesc('total')
                ->values()
                ->all()
        );
    }

    public function documentosRecientes()
    {
        $range = $this->getDateRange();

        return response()->json(
            Documento::where('tipo', 'referencia')
                ->whereBetween(
                    'created_at',
                    [$range['inicio'], $range['fin'].' 23:59:59']
                )
                ->whereNotNull('cantidad')
                ->orderBy('created_at', 'desc')
                ->limit(20)
                ->get()
        );
    }

    public function multasPorMes()
    {
        $range = $this->getDateRange();

        // Son multas COBRADAS (estado pagado), así que el mes es el del cobro.
        $sanciones = Sancion::where('estado', 'pagado')
            ->whereBetween(
                DB::raw(self::SQL_FECHA_EFECTIVA_SANCION),
                [$range['inicio'], $range['fin']]
            )
            ->get();

        $mesesData = [];
        $mesesMonto = [];

        for ($i = 1; $i <= 12; $i++) {
            $mesesData[$i] = 0;
            $mesesMonto[$i] = 0;
        }

        foreach ($sanciones as $sancion) {
            $mes = $this->fechaEfectiva($sancion)->month;

            $mesesData[$mes]++;

            $mesesMonto[$mes] += floatval(
                $sancion->monto
            );
        }

        return response()->json([
            'rango' => $range,
            'cantidades' => array_values($mesesData),
            'montos' => array_values($mesesMonto),

            'labels' => [
                'Ene',
                'Feb',
                'Mar',
                'Abr',
                'May',
                'Jun',
                'Jul',
                'Ago',
                'Sep',
                'Oct',
                'Nov',
                'Dic',
            ],
        ]);
    }

    public function estadoPagos()
    {
        $range = $this->getDateRange();

        $detalles = Detallepago::whereBetween(
            'created_at',
            [$range['inicio'], $range['fin'].' 23:59:59']
        )->get();

        $pagados = $detalles
            ->where('estado', 'pagado');

        $pendientes = $detalles
            ->where('estado', 'pendiente');

        $rechazados = $detalles
            ->where('estado', 'rechazado');

        return response()->json([
            'total' => $detalles->count(),
            'pagados' => $pagados->count(),
            'pendientes' => $pendientes->count(),
            'rechazados' => $rechazados->count(),

            'monto_pagado' => $pagados
                ->sum('cantidad_pago'),

            'monto_pendiente' => $pendientes
                ->sum('cantidad_pago'),

            'monto_rechazado' => $rechazados
                ->sum('cantidad_pago'),
        ]);
    }

    public function usuariosPagos()
    {
        $range = $this->getDateRange();

        $detallesPago = Detallepago::where('estado', 'pagado')
            ->sinLiquidacionesConSaldo()
            ->whereBetween(
                DB::raw(self::SQL_FECHA_EFECTIVA_PAGO),
                [$range['inicio'], $range['fin']]
            )
            ->with(['user', 'pago'])
            ->get();

        $usuarios = [];

        foreach ($detallesPago as $detalle) {
            $userId = $detalle->user_id;

            if (! isset($usuarios[$userId])) {
                $usuarios[$userId] = [
                    'nombre' => $this->getUserName(
                        $userId
                    ),

                    'pagos' => 0,
                    'total' => 0,
                ];
            }

            $usuarios[$userId]['pagos']++;

            $usuarios[$userId]['total'] += floatval(
                $detalle->cantidad_pago
                ?: ($detalle->pago ? $detalle->pago->cantidad : 0)
            );
        }

        return response()->json([
            'total_usuarios' => count($usuarios),

            'usuarios' => collect($usuarios)
                ->sortByDesc('total')
                ->values()
                ->all(),
        ]);
    }

    /**
     * ============================================================
     * DATOS DEL DASHBOARD
     * ============================================================
     */
    private function datosTransparencia(): array
    {
        try {
            $range = $this->getDateRange();

            $ini = $range['inicio'];

            $fin = $range['fin'].' 23:59:59';

            /*
             * =====================================================
             * INGRESOS
             * =====================================================
             */

            // Ingresos = flujo de efectivo: el dinero se cuenta en el periodo
            // en que entró, no en aquel en que se creó el cargo.
            $recibosPagados = Detallepago::with('pago')
                ->where('estado', 'pagado')
                // Un recibo liquidado con saldo no es dinero que entra ahora:
                // entró cuando el vecino hizo la transferencia que lo generó.
                ->sinLiquidacionesConSaldo()
                ->whereBetween(
                    DB::raw(self::SQL_FECHA_EFECTIVA_PAGO),
                    [Carbon::parse($ini)->toDateString(), Carbon::parse($fin)->toDateString()]
                )
                ->get();

            $recaudadoRecibos = 0;

            foreach ($recibosPagados as $d) {
                $recaudadoRecibos += floatval(
                    $d->cantidad_pago
                    ?: ($d->pago ? $d->pago->cantidad : 0)
                );
            }

            /*
             * Multas pagadas.
             */
            $recaudadoMultas = floatval(
                Sancion::where('estado', 'pagado')
                    ->whereBetween(
                        DB::raw(self::SQL_FECHA_EFECTIVA_SANCION),
                        [Carbon::parse($ini)->toDateString(), Carbon::parse($fin)->toDateString()]
                    )
                    ->sum('monto')
            );

            $recaudado =
                $recaudadoRecibos
                + $recaudadoMultas;

            /*
             * =====================================================
             * MONTO ESPERADO
             * =====================================================
             */

            $esperado = 0;

            // Lo esperado es DEVENGADO: los cargos que VENCEN dentro del
            // periodo. Antes se filtraba por created_at, la fecha en que se
            // dio de alta el cargo, que puede caer en un periodo distinto al
            // que corresponde la cuota.
            $todosLosDetalles = Detallepago::with('pago')
                ->whereHas('pago', function ($q) use ($ini, $fin) {
                    $q->whereBetween('vencimiento', [
                        Carbon::parse($ini)->toDateString(),
                        Carbon::parse($fin)->toDateString(),
                    ]);
                })
                ->get();

            $cobradoDeLoVencido = 0;
            $excedentes = 0;

            foreach ($todosLosDetalles as $d) {
                $monto = floatval(
                    $d->pago
                        ? $d->pago->cantidad
                        : ($d->cantidad_pago ?: 0)
                );

                $esperado += $monto;

                if ($d->estado !== 'pagado') {
                    continue;
                }

                $pagado = floatval($d->cantidad_pago ?: $monto);

                // Lo que excede la cuota no es cobranza de este recibo: es
                // dinero a favor del vecino (normalmente cuotas adelantadas).
                // Se cuenta aparte para que el porcentaje no rebase el 100 %
                // y para dimensionar cuánto excedente está sin asignar.
                $cobradoDeLoVencido += min($pagado, $monto);
                $excedentes += max($pagado - $monto, 0);
            }

            /*
             * % de cobranza de recibos (NO incluye multas).
             *
             * Compara devengado contra devengado: de los recibos que VENCEN en
             * el periodo, cuánto se ha cobrado. Antes dividía el dinero que
             * entró (flujo de efectivo) entre lo que vencía, y bastaba con que
             * alguien pagara un adeudo viejo o adelantara una cuota para que el
             * resultado pasara del 100 %.
             */
            $recaudacionPct = $esperado > 0
                ? round(
                    ($cobradoDeLoVencido / $esperado) * 100
                )
                : 0;

            /*
             * =====================================================
             * GASTOS DEL PERÍODO
             * =====================================================
             */

            $gastos = Documento::whereNotNull('cantidad')
                ->whereBetween(
                    'created_at',
                    [$ini, $fin]
                )
                ->get();

            $gastado = floatval(
                $gastos->sum('cantidad')
            );

            /*
             * Gastos agrupados por categoría.
             */
            $porCat = [];

            foreach ($gastos as $g) {
                $cat = $g->categoria_gasto
                    ?: 'Otros';

                $porCat[$cat] =
                    ($porCat[$cat] ?? 0)
                    + floatval($g->cantidad);
            }

            arsort($porCat);

            $gastosCategoria = [];

            foreach ($porCat as $cat => $monto) {
                $gastosCategoria[] = [
                    'categoria' => $cat,

                    'monto' => round(
                        $monto,
                        2
                    ),

                    'pct' => $gastado > 0
                        ? round(
                            ($monto / $gastado) * 100
                        )
                        : 0,
                ];
            }

            /*
             * =====================================================
             * LISTA PAGINADA DE GASTOS
             * =====================================================
             *
             * Estos filtros únicamente modifican la lista.
             * NO modifican los KPIs.
             */

            $gastoBuscar = trim(
                request('gasto_buscar', '')
            );

            $gastoCategoria = trim(
                request('gasto_categoria', '')
            );

            $gastoPorPagina = intval(
                request('gasto_por_pagina', 10)
            );

            /*
             * Evita valores arbitrarios.
             */
            if (! in_array(
                $gastoPorPagina,
                [10, 20, 50],
                true
            )) {
                $gastoPorPagina = 10;
            }

            $gastosQuery = Documento::query()
                ->whereNotNull('cantidad')
                ->whereBetween(
                    'created_at',
                    [$ini, $fin]
                );

            /*
             * Buscar por título.
             */
            if ($gastoBuscar !== '') {
                $gastosQuery->where(
                    'titulo',
                    'like',
                    '%'.$gastoBuscar.'%'
                );
            }

            /*
             * Filtrar por categoría.
             */
            if ($gastoCategoria !== '') {
                $gastosQuery->where(
                    'categoria_gasto',
                    $gastoCategoria
                );
            }

            /*
             * Paginación independiente.
             *
             * Usamos gasto_page para evitar conflictos
             * si en el futuro existen otros paginadores.
             */
            $gastosPaginator = $gastosQuery
                ->orderBy('created_at', 'desc')
                ->paginate(
                    $gastoPorPagina,
                    ['*'],
                    'gasto_page'
                )
                ->withQueryString();

            $gastosLista = collect(
                $gastosPaginator->items()
            )
                ->map(function ($g) {
                    return [
                        'id' => $g->id,

                        'titulo' => $g->titulo
                            ?: 'Movimiento sin descripción',

                        'categoria' => $g->categoria_gasto
                            ?: 'Otros',

                        'monto' => round(
                            floatval($g->cantidad),
                            2
                        ),

                        'fecha' => Carbon::parse(
                            $g->created_at
                        )->format('d/m/Y'),
                    ];
                })
                ->values()
                ->all();

            /*
             * Categorías disponibles para el selector.
             *
             * Se obtienen únicamente del período actual.
             */
            $categoriasDisponibles = Documento::whereNotNull(
                'cantidad'
            )
                ->whereBetween(
                    'created_at',
                    [$ini, $fin]
                )
                ->whereNotNull('categoria_gasto')
                ->where(
                    'categoria_gasto',
                    '!=',
                    ''
                )
                ->distinct()
                ->orderBy('categoria_gasto')
                ->pluck('categoria_gasto')
                ->values()
                ->all();

            /*
             * =====================================================
             * SALDO HISTÓRICO
             * =====================================================
             */

            $ingresosHist = 0;

            foreach (
                Detallepago::with('pago')
                    ->where('estado', 'pagado')
                    // El fondo es dinero real. Un recibo liquidado con saldo a
                    // favor ya se contó cuando el vecino hizo la transferencia
                    // que generó ese saldo; sumarlo otra vez mostraría en el
                    // fondo un dinero que no está en la cuenta.
                    ->sinLiquidacionesConSaldo()
                    ->get()
                as $d
            ) {
                $ingresosHist += floatval(
                    $d->cantidad_pago
                    ?: ($d->pago ? $d->pago->cantidad : 0)
                );
            }

            $ingresosHist += floatval(
                Sancion::where('estado', 'pagado')
                    ->sum('monto')
            );

            $egresosHist = floatval(
                Documento::whereNotNull('cantidad')
                    ->sum('cantidad')
            );

            /*
             * Saldo a favor vigente de todos los vecinos: dinero que está en
             * la cuenta pero corresponde a cuotas futuras ya pagadas.
             */
            $saldoComprometido = 0.0;

            if (\Illuminate\Support\Facades\Schema::hasTable('saldo_movimientos')) {
                $servicioSaldos = app(\App\Services\SaldoService::class);

                foreach (\App\Models\SaldoMovimiento::select('user_id')->distinct()->pluck('user_id') as $uid) {
                    $saldoComprometido += max($servicioSaldos->saldo($uid), 0);
                }
            }

            $saldoComprometido = round($saldoComprometido, 2);

            $saldoFondo = round(
                $ingresosHist - $egresosHist,
                2
            );

            /*
             * =====================================================
             * INGRESOS VS EGRESOS
             * ÚLTIMOS 6 MESES
             * =====================================================
             */

            $meses = [];

            for ($i = 5; $i >= 0; $i--) {
                $m = Carbon::now()
                    ->subMonths($i);

                $meses[$m->format('Y-m')] = [
                    'label' => $m->translatedFormat('M'),

                    // El periodo viaja a la gráfica para que al pulsar una
                    // barra se sepa qué mes pedir, sin adivinarlo del texto.
                    'periodo' => $m->format('Y-m'),

                    'ingresos' => 0,

                    'egresos' => 0,
                ];
            }

            foreach (
                Detallepago::with('pago')
                    ->where('estado', 'pagado')
                    ->get()
                as $d
            ) {
                $k = Carbon::parse(
                    $d->created_at
                )->format('Y-m');

                if (isset($meses[$k])) {
                    $meses[$k]['ingresos'] += floatval(
                        $d->cantidad_pago
                        ?: (
                            $d->pago
                                ? $d->pago->cantidad
                                : 0
                        )
                    );
                }
            }

            /*
             * Multas como ingreso.
             */
            foreach (
                Sancion::where('estado', 'pagado')
                    ->get()
                as $sancion
            ) {
                $k = Carbon::parse(
                    $sancion->created_at
                )->format('Y-m');

                if (isset($meses[$k])) {
                    $meses[$k]['ingresos'] += floatval(
                        $sancion->monto
                    );
                }
            }

            foreach (
                Documento::whereNotNull('cantidad')
                    ->get()
                as $g
            ) {
                // Fecha del movimiento bancario, no la de subida del PDF.
                $k = optional($g->fechaEfectiva())->format('Y-m');

                if ($k && isset($meses[$k])) {
                    $meses[$k]['egresos'] += floatval(
                        $g->cantidad
                    );
                }
            }

            $ingresosEgresos = array_map(
                function ($m) {
                    return [
                        'label' => $m['label'],

                        'periodo' => $m['periodo'],

                        'ingresos' => round(
                            $m['ingresos'],
                            2
                        ),

                        'egresos' => round(
                            $m['egresos'],
                            2
                        ),
                    ];
                },

                array_values($meses)
            );

            /*
             * =====================================================
             * COBRANZA POR CASA
             * =====================================================
             */

            $casaPorUser = User::withTrashed()
                ->pluck('casa', 'id')
                ->toArray();

            $hoy = Carbon::now()
                ->startOfDay();

            $atrasadas = [];

            $pendientesPorCasa = [];

            $pendientes = Detallepago::with('pago')
                ->where('estado', 'pendiente')
                ->get();

            foreach ($pendientes as $d) {
                /*
                 * Una casa únicamente se marca
                 * como atrasada si tiene un recibo
                 * pendiente que YA venció.
                 */
                if (
                    $d->pago
                    && $d->pago->vencimiento
                    && Carbon::parse(
                        $d->pago->vencimiento
                    )->lt($hoy)
                ) {
                    $casa = $casaPorUser[
                        $d->user_id
                    ] ?? null;

                    if ($casa) {
                        $atrasadas[$casa] = true;

                        $venc = Carbon::parse(
                            $d->pago->vencimiento
                        );

                        $pendientesPorCasa[$casa][] = [
                            'concepto' => $d->pago->concepto
                                ?? 'Recibo',

                            'vence' => $venc
                                ->format('d/m/Y'),

                            'dias' => (int) $venc
                                ->diffInDays(
                                    $hoy
                                ),
                        ];
                    }
                }
            }

            /*
             * Casas únicas del condominio.
             */
            $casasTotales = User::where(
                'tipo',
                'dueño'
            )
                ->whereNotNull('casa')
                ->distinct()
                ->pluck('casa')
                ->all();

            $casasTotales = collect(
                $casasTotales
            )
                ->sort(SORT_NATURAL)
                ->values()
                ->all();

            $listaCasas = [];

            foreach ($casasTotales as $c) {
                $listaCasas[] = [
                    'casa' => $c,

                    'estado' => isset(
                        $atrasadas[$c]
                    )
                        ? 'pendiente'
                        : 'al_corriente',

                    'pendientes' => $pendientesPorCasa[
                        $c
                    ] ?? [],
                ];
            }

            $numPendientes = count(
                $atrasadas
            );

            $totalCasas = count(
                $casasTotales
            );

            $casasAlCorriente = max(
                $totalCasas - $numPendientes,
                0
            );

            $cobranzaPct = $totalCasas > 0
                ? round(
                    (
                        $casasAlCorriente
                        / $totalCasas
                    ) * 100
                )
                : 0;

            /*
             * =====================================================
             * RESULTADO
             * =====================================================
             */

            return [
                'periodo' =>
                    Carbon::parse(
                        $range['inicio']
                    )->format('d/m/Y')
                    .' – '.
                    Carbon::parse(
                        $range['fin']
                    )->format('d/m/Y'),

                /*
                 * Fechas crudas para poder
                 * conservarlas en formularios.
                 */
                'rango' => [
                    'inicio' => $range['inicio'],
                    'fin' => $range['fin'],
                ],

                'finanzas' => [
                    /*
                     * Recaudado total:
                     * recibos + multas.
                     */
                    'recaudado' => round(
                        $recaudado,
                        2
                    ),

                    'recaudado_recibos' => round(
                        $recaudadoRecibos,
                        2
                    ),

                    'recaudado_multas' => round(
                        $recaudadoMultas,
                        2
                    ),

                    'esperado' => round(
                        $esperado,
                        2
                    ),

                    /*
                     * Porcentaje de cobranza (devengado contra devengado).
                     */
                    'recaudacion_pct' => $recaudacionPct,

                    /*
                     * Dinero cobrado por encima de la cuota, típicamente
                     * cuotas adelantadas. Hoy no tiene dónde registrarse
                     * como saldo a favor del vecino.
                     */
                    'excedentes' => round(
                        $excedentes,
                        2
                    ),

                    'gastado' => round(
                        $gastado,
                        2
                    ),

                    'saldo_fondo' => $saldoFondo,

                    /*
                     * Del dinero que hay en la cuenta, una parte son cuotas
                     * que algunos vecinos ya pagaron por adelantado. Está en
                     * el banco, pero no es del condominio para gastar: es un
                     * compromiso. Separarlo evita presupuestar un proyecto
                     * con dinero que ya tiene dueño.
                     */
                    'saldo_comprometido' => $saldoComprometido,

                    'saldo_disponible' => round($saldoFondo - $saldoComprometido, 2),
                ],

                'ingresos_egresos' =>
                    $ingresosEgresos,

                'gastos_categoria' =>
                    $gastosCategoria,

                /*
                 * Nueva lista paginada.
                 */
                'gastos_lista' => [
                    'items' => $gastosLista,

                    'total' => $gastosPaginator
                        ->total(),

                    'pagina_actual' => $gastosPaginator
                        ->currentPage(),

                    'ultima_pagina' => $gastosPaginator
                        ->lastPage(),

                    'por_pagina' => $gastosPaginator
                        ->perPage(),

                    'desde' => $gastosPaginator
                        ->firstItem(),

                    'hasta' => $gastosPaginator
                        ->lastItem(),

                    'url_anterior' => $gastosPaginator
                        ->previousPageUrl(),

                    'url_siguiente' => $gastosPaginator
                        ->nextPageUrl(),

                    'links' => collect(
                        $gastosPaginator->linkCollection()
                    )
                        ->map(function ($link) {
                            return [
                                'url' => $link['url'],

                                'label' => $link['label'],

                                'active' => $link['active'],
                            ];
                        })
                        ->values()
                        ->all(),
                ],

                'filtros_gastos' => [
                    'buscar' => $gastoBuscar,

                    'categoria' =>
                        $gastoCategoria,

                    'por_pagina' =>
                        $gastoPorPagina,

                    'categorias' =>
                        $categoriasDisponibles,
                ],

                'cobranza' => [
                    'al_corriente' =>
                        $casasAlCorriente,

                    'pendientes' =>
                        $numPendientes,

                    'total' =>
                        $totalCasas,

                    'pct' =>
                        $cobranzaPct,

                    'casas' =>
                        $listaCasas,
                ],
            ];
        } catch (\Throwable $e) {
            \Log::error(
                'reportes.transparencia: '
                .$e->getMessage()
            );

            return [
                'periodo' => '',

                'rango' => [
                    'inicio' => '',
                    'fin' => '',
                ],

                'finanzas' => [
                    'recaudado' => 0,
                    'recaudado_recibos' => 0,
                    'recaudado_multas' => 0,
                    'esperado' => 0,
                    'recaudacion_pct' => 0,
                    'excedentes' => 0,
                    'gastado' => 0,
                    'saldo_fondo' => 0,
                    'saldo_comprometido' => 0,
                    'saldo_disponible' => 0,
                ],

                'ingresos_egresos' => [],

                'gastos_categoria' => [],

                'gastos_lista' => [
                    'items' => [],
                    'total' => 0,
                    'pagina_actual' => 1,
                    'ultima_pagina' => 1,
                    'por_pagina' => 10,
                    'desde' => null,
                    'hasta' => null,
                    'url_anterior' => null,
                    'url_siguiente' => null,
                    'links' => [],
                ],

                'filtros_gastos' => [
                    'buscar' => '',
                    'categoria' => '',
                    'por_pagina' => 10,
                    'categorias' => [],
                ],

                'cobranza' => [
                    'al_corriente' => 0,
                    'pendientes' => 0,
                    'total' => 0,
                    'pct' => 0,
                    'casas' => [],
                ],
            ];
        }
    }
}