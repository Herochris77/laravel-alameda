<x-app-layout>

    @php
        $badge = [
            'por_iniciar' => [
                'bg' => '#eff6ff',
                'color' => '#1d4ed8',
                'icon' => 'clock outline',
            ],

            'en_proceso' => [
                'bg' => '#fffbeb',
                'color' => '#92400e',
                'icon' => 'spinner',
            ],

            'pausado' => [
                'bg' => '#f1f5f9',
                'color' => '#475569',
                'icon' => 'pause',
            ],

            'terminado' => [
                'bg' => '#f0fdf4',
                'color' => '#166534',
                'icon' => 'check circle',
            ],
        ];

        $totalProyectos = $proyectos->count();

        $terminados = $proyectos
            ->where('estado', 'terminado')
            ->count();

        $enProceso = $proyectos
            ->where('estado', 'en_proceso')
            ->count();

        $porIniciar = $proyectos
            ->where('estado', 'por_iniciar')
            ->count();
    @endphp


    <style>

        .pj-page {
            --primary: #0d9488;
            --primary-dark: #0f766e;
            --primary-light: #14b8a6;

            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;

            --text: #0f172a;
            --muted: #64748b;
            --soft: #94a3b8;

            --border: #e2e8f0;

            --surface: #ffffff;
            --bg: #f8fafc;

            padding-bottom: 30px;
        }


        .pj-container {
            max-width: 1200px;

            margin: 0 auto;
        }


        /* =====================================================
           HERO
        ===================================================== */

        .pj-hero {
            position: relative;

            overflow: hidden;

            padding: 24px;

            margin-bottom: 18px;

            border-radius: 24px;

            color: #fff;

            background:
                radial-gradient(
                    circle at top right,
                    rgba(255,255,255,.22),
                    transparent 34%
                ),
                linear-gradient(
                    135deg,
                    var(--primary),
                    var(--primary-light)
                );

            box-shadow:
                0 12px 30px rgba(13,148,136,.18);
        }


        .pj-hero::after {
            content: '';

            position: absolute;

            width: 220px;
            height: 220px;

            right: -90px;
            bottom: -130px;

            border-radius: 999px;

            background:
                rgba(255,255,255,.08);
        }


        .pj-hero-top {
            position: relative;

            z-index: 2;

            display: flex;

            align-items: center;

            gap: 14px;
        }


        .pj-hero-icon {
            width: 56px;
            height: 56px;

            flex-shrink: 0;

            display: flex;

            align-items: center;

            justify-content: center;

            border-radius: 16px;

            background:
                rgba(255,255,255,.16);

            border:
                1px solid rgba(255,255,255,.16);
        }


        .pj-hero-icon i {
            margin: 0 !important;

            font-size: 1.5rem;
        }


        .pj-title {
            margin: 0;

            font-size:
                clamp(1.4rem,3vw,2rem);

            font-weight: 900;

            letter-spacing: -.03em;
        }


        .pj-subtitle {
            margin: 3px 0 0;

            opacity: .92;

            font-size: .9rem;
        }


        /* =====================================================
           RESUMEN
        ===================================================== */

        .pj-summary {
            display: grid;

            grid-template-columns:
                repeat(4,1fr);

            gap: 10px;

            margin-bottom: 18px;
        }


        .pj-summary-card {
            display: flex;

            align-items: center;

            gap: 10px;

            min-height: 74px;

            padding: 12px 14px;

            border:
                1px solid var(--border);

            border-radius: 16px;

            background:
                var(--surface);

            box-shadow:
                0 6px 18px rgba(15,23,42,.045);
        }


        .pj-summary-icon {
            width: 38px;
            height: 38px;

            flex-shrink: 0;

            display: flex;

            align-items: center;

            justify-content: center;

            border-radius: 11px;

            background:
                rgba(13,148,136,.10);

            color:
                var(--primary);
        }


        .pj-summary-icon i {
            margin: 0 !important;
        }


        .pj-summary-value {
            color:
                var(--text);

            font-size: 1.2rem;

            font-weight: 900;

            line-height: 1;
        }


        .pj-summary-label {
            margin-top: 3px;

            color:
                var(--muted);

            font-size: .65rem;

            font-weight: 800;

            text-transform: uppercase;

            letter-spacing: .04em;
        }


        /* =====================================================
           ENCABEZADO DE LISTA
        ===================================================== */

        .pj-section-head {
            display: flex;

            align-items: flex-end;

            justify-content:
                space-between;

            gap: 12px;

            margin-bottom: 12px;
        }


        .pj-section-title {
            margin: 0;

            color:
                var(--text);

            font-size: 1rem;

            font-weight: 900;
        }


        .pj-section-subtitle {
            margin-top: 3px;

            color:
                var(--muted);

            font-size: .75rem;
        }


        .pj-count {
            color:
                var(--soft);

            font-size: .72rem;

            white-space: nowrap;
        }


        /* =====================================================
           GRID
        ===================================================== */

        .pj-grid {
            display: grid;

            grid-template-columns:
                repeat(
                    auto-fill,
                    minmax(300px,1fr)
                );

            gap: 14px;
        }


        .pj-card {
            position: relative;

            overflow: hidden;

            display: flex;

            flex-direction: column;

            min-height: 240px;

            padding: 18px;

            border:
                1px solid var(--border);

            border-radius: 18px;

            background:
                var(--surface);

            color: inherit;

            text-decoration: none !important;

            box-shadow:
                0 6px 18px rgba(15,23,42,.05);

            transition:
                transform .16s ease,
                box-shadow .16s ease,
                border-color .16s ease;
        }


        .pj-card::before {
            content: '';

            position: absolute;

            top: 0;
            left: 0;
            bottom: 0;

            width: 4px;

            background:
                var(--primary);
        }


        .pj-card.terminado::before {
            background:
                var(--success);
        }


        .pj-card.pausado::before {
            background:
                #64748b;
        }


        .pj-card:hover {
            transform:
                translateY(-2px);

            border-color:
                #99f6e4;

            box-shadow:
                0 12px 28px rgba(15,23,42,.09);
        }


        .pj-card-top {
            display: flex;

            align-items: flex-start;

            justify-content:
                space-between;

            gap: 10px;

            margin-bottom: 12px;
        }


        .pj-name {
            min-width: 0;

            color:
                var(--text);

            font-size: 1.02rem;

            font-weight: 900;

            line-height: 1.3;
        }


        .pj-badge {
            flex-shrink: 0;

            display: inline-flex;

            align-items: center;

            gap: 5px;

            padding: 4px 9px;

            border-radius: 999px;

            font-size: .68rem;

            font-weight: 800;

            white-space: nowrap;
        }


        .pj-badge i {
            margin: 0 !important;
        }


        .pj-cost-row {
            display: flex;

            align-items: center;

            gap: 8px;

            margin-bottom: 12px;

            color:
                var(--muted);

            font-size: .8rem;
        }


        .pj-cost-icon {
            width: 30px;
            height: 30px;

            display: flex;

            align-items: center;

            justify-content: center;

            flex-shrink: 0;

            border-radius: 9px;

            background:
                #f1f5f9;

            color:
                var(--primary);
        }


        .pj-cost-icon i {
            margin: 0 !important;
        }


        .pj-cost-label {
            font-size: .67rem;

            color:
                var(--soft);
        }


        .pj-cost-value {
            color:
                var(--text);

            font-weight: 800;
        }


        /* =====================================================
           PROGRESO
        ===================================================== */

        .pj-progress-head {
            display: flex;

            align-items: center;

            justify-content:
                space-between;

            gap: 10px;

            margin-bottom: 6px;
        }


        .pj-progress-label {
            color:
                var(--muted);

            font-size: .71rem;

            font-weight: 700;
        }


        .pj-progress-pct {
            color:
                var(--primary-dark);

            font-size: .82rem;

            font-weight: 900;
        }


        .pj-bar {
            height: 10px;

            overflow: hidden;

            border-radius: 999px;

            background:
                var(--bg);
        }


        .pj-fill {
            height: 100%;

            border-radius: 999px;

            transition:
                width .3s ease;
        }


        /* =====================================================
           ultima actualización
        ===================================================== */

        .pj-last-update {
            margin-top: 14px;

            padding: 10px 11px;

            border-radius: 12px;

            background:
                var(--bg);

            color:
                var(--muted);

            font-size: .75rem;

            line-height: 1.45;
        }


        .pj-last-update-head {
            display: flex;

            align-items: center;

            justify-content:
                space-between;

            gap: 10px;

            margin-bottom: 4px;
        }


        .pj-last-update-title {
            color:
                var(--text);

            font-size: .68rem;

            font-weight: 900;
        }


        .pj-last-update-date {
            color:
                var(--soft);

            font-size: .64rem;

            white-space: nowrap;
        }


        .pj-last-update-text {
            overflow: hidden;

            display:
                -webkit-box;

            -webkit-line-clamp: 2;

            -webkit-box-orient:
                vertical;
        }


        /* =====================================================
           FOOTER TARJETA
        ===================================================== */

        .pj-card-footer {
            display: flex;

            align-items: center;

            justify-content:
                space-between;

            gap: 10px;

            margin-top: auto;

            padding-top: 13px;
        }


        .pj-detail-text {
            color:
                var(--primary);

            font-size: .72rem;

            font-weight: 800;
        }


        .pj-detail-arrow {
            width: 28px;
            height: 28px;

            display: flex;

            align-items: center;

            justify-content: center;

            border-radius: 9px;

            color:
                var(--primary);

            background:
                rgba(13,148,136,.08);
        }


        .pj-detail-arrow i {
            margin: 0 !important;
        }


        /* =====================================================
           vacio
        ===================================================== */

        .pj-empty {
            grid-column:
                1 / -1;

            min-height: 220px;

            display: flex;

            align-items: center;

            justify-content: center;

            padding: 30px;

            border:
                1px dashed #cbd5e1;

            border-radius: 18px;

            background:
                rgba(255,255,255,.7);

            text-align: center;
        }


        .pj-empty-icon {
            width: 56px;
            height: 56px;

            display: flex;

            align-items: center;

            justify-content: center;

            margin:
                0 auto 10px;

            border-radius: 16px;

            color:
                var(--soft);

            background:
                #f1f5f9;
        }


        .pj-empty-icon i {
            margin: 0 !important;

            font-size: 1.3rem;
        }


        .pj-empty strong {
            display: block;

            color:
                var(--text);

            font-size: .9rem;

            margin-bottom: 4px;
        }


        .pj-empty span {
            color:
                var(--muted);

            font-size: .75rem;
        }


        /* =====================================================
           RESPONSIVE
        ===================================================== */

        @media (max-width: 800px) {

            .pj-summary {
                grid-template-columns:
                    repeat(2,1fr);
            }

        }


        @media (max-width: 640px) {

            .pj-hero {
                padding: 18px;

                border-radius: 20px;
            }


            .pj-hero-icon {
                width: 46px;
                height: 46px;

                border-radius: 13px;
            }


            .pj-title {
                font-size: 1.28rem;
            }


            .pj-subtitle {
                font-size: .77rem;
            }


            .pj-summary {
                gap: 7px;

                margin-bottom: 15px;
            }


            .pj-summary-card {
                min-height: 64px;

                padding: 9px 10px;

                gap: 8px;

                border-radius: 13px;
            }


            .pj-summary-icon {
                width: 33px;
                height: 33px;

                border-radius: 9px;
            }


            .pj-summary-value {
                font-size: 1rem;
            }


            .pj-summary-label {
                font-size: .55rem;
            }


            .pj-grid {
                grid-template-columns: 1fr;

                gap: 9px;
            }


            .pj-card {
                min-height: 0;

                padding: 14px;

                border-radius: 15px;
            }


            .pj-card-footer {
                padding-top: 10px;
            }

        }

    </style>


    <div class="pj-page">


        {{-- =====================================================
             HERO
        ====================================================== --}}

        <div class="pj-hero">

            <div class="pj-hero-top">

                <div class="pj-hero-icon">
                    <i class="tasks icon"></i>
                </div>

                <div>

                    <h1 class="pj-title">
                        Proyectos del condominio
                    </h1>

                    <p class="pj-subtitle">
                        Consulta el avance, inversión y últimas
                        actualizaciones de los proyectos de la comunidad.
                    </p>

                </div>

            </div>

        </div>


        <div class="pj-container">


            {{-- =================================================
                 RESUMEN
            ================================================== --}}

            <div class="pj-summary">


                <div class="pj-summary-card">

                    <div class="pj-summary-icon">
                        <i class="tasks icon"></i>
                    </div>

                    <div>

                        <div class="pj-summary-value">
                            {{ $totalProyectos }}
                        </div>

                        <div class="pj-summary-label">
                            Proyectos
                        </div>

                    </div>

                </div>


                <div class="pj-summary-card">

                    <div class="pj-summary-icon">
                        <i class="play icon"></i>
                    </div>

                    <div>

                        <div class="pj-summary-value">
                            {{ $enProceso }}
                        </div>

                        <div class="pj-summary-label">
                            En proceso
                        </div>

                    </div>

                </div>


                <div class="pj-summary-card">

                    <div class="pj-summary-icon">
                        <i class="clock outline icon"></i>
                    </div>

                    <div>

                        <div class="pj-summary-value">
                            {{ $porIniciar }}
                        </div>

                        <div class="pj-summary-label">
                            Por iniciar
                        </div>

                    </div>

                </div>


                <div class="pj-summary-card">

                    <div class="pj-summary-icon">
                        <i class="check circle icon"></i>
                    </div>

                    <div>

                        <div class="pj-summary-value">
                            {{ $terminados }}
                        </div>

                        <div class="pj-summary-label">
                            Terminados
                        </div>

                    </div>

                </div>

            </div>


            {{-- =================================================
                 LISTADO
            ================================================== --}}

            <div class="pj-section-head">

                <div>

                    <h2 class="pj-section-title">
                        Proyectos publicados
                    </h2>

                    <div class="pj-section-subtitle">
                        Selecciona un proyecto para consultar
                        su historial y evidencia.
                    </div>

                </div>


                @if($totalProyectos > 0)

                    <div class="pj-count">

                        {{ $totalProyectos }}

                        {{
                            $totalProyectos === 1
                                ? 'proyecto'
                                : 'proyectos'
                        }}

                    </div>

                @endif

            </div>


            <div class="pj-grid">


                @forelse($proyectos as $p)

                    @php

                        $estadoConfig =
                            $badge[$p->estado]
                            ??
                            [
                                'bg' => '#f1f5f9',
                                'color' => '#475569',
                                'icon' => 'info circle',
                            ];

                        $fill =
                            $p->estado === 'terminado'
                                ? 'var(--success)'
                                : 'var(--primary)';

                        $avance =
                            max(
                                0,
                                min(
                                    100,
                                    (int) $p->avance
                                )
                            );

                        /*
                         * Conservamos tu misma relación.
                         *
                         * Si la relación ya viene cargada,
                         * first() no genera consulta adicional.
                         */
                        $ultimo =
                            $p->avances()->first();

                    @endphp


                    <a
                        href="{{
                            route(
                                'usuario.proyectos.ver',
                                $p->id
                            )
                        }}"
                        class="
                            pj-card
                            {{ $p->estado }}
                        "
                    >


                        <div class="pj-card-top">

                            <div class="pj-name">
                                {{ $p->nombre }}
                            </div>


                            <span
                                class="pj-badge"
                                style="
                                    background:
                                    {{ $estadoConfig['bg'] }};

                                    color:
                                    {{ $estadoConfig['color'] }};
                                "
                            >

                                <i
                                    class="
                                        {{ $estadoConfig['icon'] }}
                                        icon
                                    "
                                ></i>

                                {{
                                    $estados[$p->estado]
                                    ??
                                    $p->estado
                                }}

                            </span>

                        </div>


                        {{-- COSTO --}}
                        <div class="pj-cost-row">

                            <div class="pj-cost-icon">
                                <i class="dollar sign icon"></i>
                            </div>

                            <div>

                                <div class="pj-cost-label">
                                    Inversión estimada
                                </div>

                                <div class="pj-cost-value">

                                    @if(!is_null($p->costo))

                                        ${{ number_format(
                                            $p->costo,
                                            2
                                        ) }}

                                    @else

                                        Sin costo definido

                                    @endif

                                </div>

                            </div>

                        </div>


                        {{-- PROGRESO --}}
                        <div class="pj-progress-head">

                            <span class="pj-progress-label">
                                Avance
                            </span>

                            <span class="pj-progress-pct">
                                {{ $avance }}%
                            </span>

                        </div>


                        <div class="pj-bar">

                            <div
                                class="pj-fill"
                                style="
                                    width:{{ $avance }}%;
                                    background:{{ $fill }};
                                "
                            ></div>

                        </div>


                        {{-- ÚLTIMA ACTUALIZACIÓN --}}
                        @if($ultimo)

                            <div class="pj-last-update">

                                <div class="pj-last-update-head">

                                    <span class="pj-last-update-title">

                                        <i class="history icon"></i>

                                        ùltima actualización

                                    </span>

                                    <span class="pj-last-update-date">

                                        {{
                                            \Carbon\Carbon::parse(
                                                $ultimo->created_at
                                            )->format('d/m/Y')
                                        }}

                                    </span>

                                </div>


                                <div class="pj-last-update-text">

                                    @if($ultimo->comentario)

                                        {{
                                            \Illuminate\Support\Str::limit(
                                                $ultimo->comentario,
                                                110
                                            )
                                        }}

                                    @else

                                        Avance actualizado al
                                        {{ $ultimo->porcentaje }}%.

                                    @endif

                                </div>

                            </div>

                        @endif


                        <div class="pj-card-footer">

                            <span class="pj-detail-text">
                                Ver detalle y actualizaciones
                            </span>

                            <span class="pj-detail-arrow">
                                <i class="arrow right icon"></i>
                            </span>

                        </div>

                    </a>


                @empty

                    <div class="pj-empty">

                        <div>

                            <div class="pj-empty-icon">
                                <i class="tasks icon"></i>
                            </div>

                            <strong>
                                Aún no hay proyectos publicados
                            </strong>

                            <span>
                                Cuando el comité publique un proyecto,
                                aparecerá en esta sección.
                            </span>

                        </div>

                    </div>

                @endforelse

            </div>

        </div>

    </div>

</x-app-layout>