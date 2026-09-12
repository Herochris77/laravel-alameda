<?php

namespace App\Http\Controllers\Usuario;

use App\Http\Controllers\Controller;
use App\Models\Detallepago;
use App\Models\Documento;
use App\Models\Sancion;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

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
            ->whereBetween(
                'created_at',
                [$range['inicio'], $range['fin'].' 23:59:59']
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
                'created_at',
                [$range['inicio'], $range['fin'].' 23:59:59']
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

        $detallesPago = Detallepago::where('estado', 'pagado')
            ->whereBetween(
                'created_at',
                [$range['inicio'], $range['fin'].' 23:59:59']
            )
            ->with('pago')
            ->get();

        $recibosData = [];

        foreach ($detallesPago as $detalle) {
            $recibosData[] = [
                'id' => $detalle->id,

                'concepto' => $detalle->pago
                    ? $detalle->pago->concepto
                    : 'Sin concepto',

                'monto' => floatval(
                    $detalle->cantidad_pago
                    ?: ($detalle->pago ? $detalle->pago->cantidad : 0)
                ),

                'fecha_pago' => $detalle->created_at
                    ->format('Y-m-d'),

                'usuario' => $this->getUserName(
                    $detalle->user_id
                ),
            ];
        }

        $sancionesData = Sancion::where('estado', 'pagado')
            ->whereBetween(
                'created_at',
                [$range['inicio'], $range['fin'].' 23:59:59']
            )
            ->get()
            ->map(function ($sancion) {
                return [
                    'id' => $sancion->id,
                    'motivo' => $sancion->motivo,
                    'monto' => floatval($sancion->monto),

                    'fecha_pago' => $sancion->updated_at
                        ->format('Y-m-d'),

                    'usuario' => $this->getUserName(
                        $sancion->user_id
                    ),
                ];
            });

        return response()->json([
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

        $detallesPago = Detallepago::where('estado', 'pagado')
            ->whereBetween(
                'created_at',
                [$range['inicio'], $range['fin'].' 23:59:59']
            )
            ->with('pago')
            ->get();

        $sanciones = Sancion::where('estado', 'pagado')
            ->whereBetween(
                'created_at',
                [$range['inicio'], $range['fin'].' 23:59:59']
            )
            ->get();

        $meses = [];

        for ($i = 1; $i <= 12; $i++) {
            $meses[$i] = 0;
        }

        foreach ($detallesPago as $detalle) {
            $mes = $detalle->created_at->month;

            $cantidad = floatval(
                $detalle->cantidad_pago
                ?: ($detalle->pago ? $detalle->pago->cantidad : 0)
            );

            $meses[$mes] += $cantidad;
        }

        foreach ($sanciones as $sancion) {
            $mes = $sancion->created_at->month;

            $meses[$mes] += floatval(
                $sancion->monto
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

    public function gastosPorMes()
    {
        $range = $this->getDateRange();

        $documentos = Documento::where('tipo', 'referencia')
            ->whereBetween(
                'created_at',
                [$range['inicio'], $range['fin'].' 23:59:59']
            )
            ->whereNotNull('cantidad')
            ->selectRaw(
                'MONTH(created_at) as mes, SUM(cantidad) as total'
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

    public function gastosPorCategoria()
    {
        $range = $this->getDateRange();

        $documentos = Documento::where('tipo', 'referencia')
            ->whereBetween(
                'created_at',
                [$range['inicio'], $range['fin'].' 23:59:59']
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
            ->whereBetween(
                'created_at',
                [$range['inicio'], $range['fin'].' 23:59:59']
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

        $sanciones = Sancion::where('estado', 'pagado')
            ->whereBetween(
                'created_at',
                [$range['inicio'], $range['fin'].' 23:59:59']
            )
            ->get();

        $mesesData = [];
        $mesesMonto = [];

        for ($i = 1; $i <= 12; $i++) {
            $mesesData[$i] = 0;
            $mesesMonto[$i] = 0;
        }

        foreach ($sanciones as $sancion) {
            $mes = $sancion->created_at->month;

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
            ->whereBetween(
                'created_at',
                [$range['inicio'], $range['fin'].' 23:59:59']
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

            $recibosPagados = Detallepago::with('pago')
                ->where('estado', 'pagado')
                ->whereBetween(
                    'created_at',
                    [$ini, $fin]
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
                        'created_at',
                        [$ini, $fin]
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

            $todosLosDetalles = Detallepago::with('pago')
                ->whereBetween(
                    'created_at',
                    [$ini, $fin]
                )
                ->get();

            foreach ($todosLosDetalles as $d) {
                $esperado += floatval(
                    $d->pago
                        ? $d->pago->cantidad
                        : ($d->cantidad_pago ?: 0)
                );
            }

            /*
             * % financiero de recibos.
             *
             * NO incluye multas.
             */
            $recaudacionPct = $esperado > 0
                ? round(
                    ($recaudadoRecibos / $esperado) * 100
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
                $k = Carbon::parse(
                    $g->created_at
                )->format('Y-m');

                if (isset($meses[$k])) {
                    $meses[$k]['egresos'] += floatval(
                        $g->cantidad
                    );
                }
            }

            $ingresosEgresos = array_map(
                function ($m) {
                    return [
                        'label' => $m['label'],

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
                     * Porcentaje financiero.
                     */
                    'recaudacion_pct' => $recaudacionPct,

                    'gastado' => round(
                        $gastado,
                        2
                    ),

                    'saldo_fondo' => $saldoFondo,
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
                    'gastado' => 0,
                    'saldo_fondo' => 0,
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