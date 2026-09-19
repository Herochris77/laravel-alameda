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

        $estadoConfig =
            $badge[$proyecto->estado]
            ??
            [
                'bg' => '#f1f5f9',
                'color' => '#475569',
                'icon' => 'info circle',
            ];

        $fill =
            $proyecto->estado === 'terminado'
                ? '#10b981'
                : '#0d9488';

        $avance =
            max(
                0,
                min(
                    100,
                    (int) $proyecto->avance
                )
            );

        $totalActualizaciones =
            $proyecto->avances->count();

        $ultimaActualizacion =
            $proyecto->avances->first();

    @endphp


    <style>

        .pj-page {
            --primary: #0d9488;
            --primary-dark: #0f766e;

            --success: #10b981;

            --text: #0f172a;
            --muted: #64748b;
            --soft: #94a3b8;

            --border: #e2e8f0;

            --surface: #ffffff;
            --bg: #f8fafc;

            padding-bottom: 30px;
        }


        .pj-container {
            max-width: 1100px;

            margin: 0 auto;
        }


        /* =====================================================
           BACK
        ===================================================== */

        .pj-back {
            display: inline-flex;

            align-items: center;

            gap: 5px;

            margin-bottom: 12px;

            color:
                var(--muted);

            font-size: .82rem;

            font-weight: 700;

            text-decoration:
                none !important;
        }


        .pj-back:hover {
            color:
                var(--primary);
        }


        .pj-back i {
            margin: 0 !important;
        }


        /* =====================================================
           HERO DEL PROYECTO
        ===================================================== */

        .pj-project {
            position: relative;

            overflow: hidden;

            padding: 22px;

            margin-bottom: 15px;

            border:
                1px solid var(--border);

            border-radius: 20px;

            background:
                var(--surface);

            box-shadow:
                0 8px 24px rgba(15,23,42,.055);
        }


        .pj-project::before {
            content: '';

            position: absolute;

            top: 0;
            left: 0;

            width: 5px;
            height: 100%;

            background:
                var(--primary);
        }


        .pj-project.finished::before {
            background:
                var(--success);
        }


        .pj-project-top {
            display: flex;

            align-items: flex-start;

            justify-content:
                space-between;

            gap: 15px;

            margin-bottom: 13px;
        }


        .pj-name {
            margin: 0;

            color:
                var(--text);

            font-size:
                clamp(1.3rem,3vw,1.75rem);

            font-weight: 900;

            letter-spacing: -.025em;

            line-height: 1.2;
        }


        .pj-badge {
            flex-shrink: 0;

            display: inline-flex;

            align-items: center;

            gap: 5px;

            padding: 5px 10px;

            border-radius: 999px;

            font-size: .7rem;

            font-weight: 800;

            white-space: nowrap;
        }


        .pj-badge i {
            margin: 0 !important;
        }


        .pj-desc {
            max-width: 850px;

            margin:
                0 0 18px;

            color:
                #475569;

            font-size: .88rem;

            line-height: 1.55;
        }


        /* =====================================================
           PROGRESO PRINCIPAL
        ===================================================== */

        .pj-progress-box {
            padding: 16px;

            border-radius: 16px;

            background:
                var(--bg);
        }


        .pj-progress-header {
            display: flex;

            align-items: flex-end;

            justify-content:
                space-between;

            gap: 12px;

            margin-bottom: 9px;
        }


        .pj-progress-title {
            color:
                var(--muted);

            font-size: .72rem;

            font-weight: 800;

            text-transform: uppercase;

            letter-spacing: .04em;
        }


        .pj-progress-number {
            color:
                var(--primary-dark);

            font-size: 1.8rem;

            font-weight: 900;

            line-height: 1;
        }


        .pj-bar {
            height: 13px;

            overflow: hidden;

            border-radius: 999px;

            background:
                #e2e8f0;
        }


        .pj-fill {
            height: 100%;

            border-radius: 999px;
        }


        /* =====================================================
           META
        ===================================================== */

        .pj-meta-grid {
            display: grid;

            grid-template-columns:
                repeat(3,1fr);

            gap: 10px;

            margin-top: 13px;
        }


        .pj-meta-card {
            display: flex;

            align-items: center;

            gap: 9px;

            padding: 11px 12px;

            border:
                1px solid var(--border);

            border-radius: 13px;

            background: #fff;
        }


        .pj-meta-icon {
            width: 34px;
            height: 34px;

            flex-shrink: 0;

            display: flex;

            align-items: center;

            justify-content: center;

            border-radius: 10px;

            color:
                var(--primary);

            background:
                rgba(13,148,136,.09);
        }


        .pj-meta-icon i {
            margin: 0 !important;
        }


        .pj-meta-label {
            color:
                var(--soft);

            font-size: .61rem;

            font-weight: 800;

            text-transform: uppercase;
        }


        .pj-meta-value {
            margin-top: 2px;

            color:
                var(--text);

            font-size: .8rem;

            font-weight: 850;
        }


        /* =====================================================
           CARD GENERAL
        ===================================================== */

        .pj-card {
            padding: 20px;

            margin-bottom: 15px;

            border:
                1px solid var(--border);

            border-radius: 18px;

            background:
                var(--surface);

            box-shadow:
                0 6px 18px rgba(15,23,42,.045);
        }


        .pj-card-head {
            display: flex;

            align-items: flex-start;

            justify-content:
                space-between;

            gap: 12px;

            margin-bottom: 16px;
        }


        .pj-card-title {
            margin: 0;

            display: flex;

            align-items: center;

            gap: 7px;

            color:
                var(--text);

            font-size: 1rem;

            font-weight: 900;
        }


        .pj-card-title i {
            margin: 0 !important;

            color:
                var(--primary);
        }


        .pj-card-subtitle {
            margin-top: 3px;

            color:
                var(--muted);

            font-size: .72rem;
        }


        .pj-count-badge {
            flex-shrink: 0;

            padding: 5px 8px;

            border-radius: 999px;

            color:
                var(--primary-dark);

            background:
                rgba(13,148,136,.08);

            font-size: .65rem;

            font-weight: 800;
        }


        /* =====================================================
           TIMELINE
        ===================================================== */

        .timeline {
            position: relative;
        }


        .timeline-item {
            position: relative;

            display: grid;

            grid-template-columns:
                38px minmax(0,1fr);

            gap: 10px;
        }


        .timeline-marker {
            position: relative;

            display: flex;

            flex-direction: column;

            align-items: center;
        }


        .timeline-dot {
            position: relative;

            z-index: 2;

            width: 14px;
            height: 14px;

            margin-top: 5px;

            border:
                3px solid #ccfbf1;

            border-radius: 999px;

            background:
                var(--primary);
        }


        .timeline-line {
            flex: 1;

            width: 2px;

            min-height: 30px;

            margin-top: 3px;

            background:
                var(--border);
        }


        .timeline-content {
            padding-bottom: 20px;
        }


        .timeline-head {
            display: flex;

            align-items: flex-start;

            justify-content:
                space-between;

            gap: 10px;

            margin-bottom: 5px;
        }


        .timeline-pct {
            color:
                var(--primary-dark);

            font-size: .82rem;

            font-weight: 900;
        }


        .timeline-date {
            color:
                var(--soft);

            font-size: .67rem;

            white-space: nowrap;
        }


        .timeline-comment {
            color:
                #334155;

            font-size: .86rem;

            line-height: 1.5;

            white-space: pre-line;
        }


        /* =====================================================
           EVIDENCIA
        ===================================================== */

        .timeline-photo {
            position: relative;

            display: inline-block;

            max-width: 360px;

            margin-top: 9px;

            cursor: zoom-in;
        }


        .timeline-photo img {
            display: block;

            width: 100%;

            max-height: 250px;

            border:
                1px solid var(--border);

            border-radius: 13px;

            object-fit: cover;
        }


        .timeline-photo-overlay {
            position: absolute;

            right: 7px;
            bottom: 7px;

            display: inline-flex;

            align-items: center;

            gap: 5px;

            padding: 5px 8px;

            border-radius: 8px;

            color: #fff;

            background:
                rgba(15,23,42,.72);

            font-size: .62rem;

            font-weight: 800;
        }


        .timeline-photo-overlay i {
            margin: 0 !important;
        }


        /* =====================================================
           EMPTY
        ===================================================== */

        .pj-empty {
            padding: 32px 15px;

            text-align: center;
        }


        .pj-empty-icon {
            width: 55px;
            height: 55px;

            display: flex;

            align-items: center;

            justify-content: center;

            margin:
                0 auto 10px;

            border-radius: 16px;

            background:
                #f1f5f9;

            color:
                var(--soft);
        }


        .pj-empty-icon i {
            margin: 0 !important;

            font-size: 1.25rem;
        }


        .pj-empty strong {
            display: block;

            color:
                var(--text);

            font-size: .87rem;

            margin-bottom: 4px;
        }


        .pj-empty span {
            color:
                var(--muted);

            font-size: .72rem;
        }


        /* =====================================================
           LIGHTBOX
        ===================================================== */

        .pj-lightbox {
            position: fixed;

            inset: 0;

            z-index: 9999;

            display: none;

            align-items: center;

            justify-content: center;

            padding: 20px;

            background:
                rgba(15,23,42,.94);
        }


        .pj-lightbox.show {
            display: flex;
        }


        .pj-lightbox img {
            display: block;

            max-width: 95%;

            max-height: 90vh;

            border-radius: 13px;

            box-shadow:
                0 18px 50px rgba(0,0,0,.45);
        }


        .pj-lightbox-close {
            position: absolute;

            top: 16px;
            right: 20px;

            width: 40px;
            height: 40px;

            display: flex;

            align-items: center;

            justify-content: center;

            border-radius: 999px;

            background:
                rgba(255,255,255,.12);

            color: #fff;

            cursor: pointer;

            font-size: 1.2rem;
        }


        .pj-lightbox-close i {
            margin: 0 !important;
        }


        .pj-lightbox-caption {
            position: absolute;

            left: 20px;
            right: 20px;
            bottom: 15px;

            text-align: center;

            color:
                rgba(255,255,255,.78);

            font-size: .72rem;
        }


        /* =====================================================
           RESPONSIVE
        ===================================================== */

        @media (max-width: 700px) {

            .pj-project {
                padding: 17px;

                border-radius: 17px;
            }


            .pj-project-top {
                align-items: flex-start;
            }


            .pj-name {
                font-size: 1.25rem;
            }


            .pj-meta-grid {
                grid-template-columns: 1fr;
            }


            .pj-card {
                padding: 15px;

                border-radius: 16px;
            }


            .timeline-item {
                grid-template-columns:
                    28px minmax(0,1fr);

                gap: 7px;
            }


            .timeline-head {
                display: block;
            }


            .timeline-date {
                margin-top: 2px;
            }


            .timeline-photo {
                max-width: 100%;
            }

        }

    </style>


    <div class="pj-page">

        <div class="pj-container">


            {{-- =================================================
                 VOLVER
            ================================================== --}}

            <a
                href="{{
                    route(
                        'usuario.proyectos.index'
                    )
                }}"
                class="pj-back"
            >

                <i class="arrow left icon"></i>

                Volver a proyectos

            </a>


            {{-- =================================================
                 RESUMEN DEL PROYECTO
            ================================================== --}}

            <section
                class="
                    pj-project
                    {{
                        $proyecto->estado
                        ===
                        'terminado'
                            ? 'finished'
                            : ''
                    }}
                "
            >

                <div class="pj-project-top">

                    <h1 class="pj-name">
                        {{ $proyecto->nombre }}
                    </h1>


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
                            $estados[
                                $proyecto->estado
                            ]
                            ??
                            $proyecto->estado
                        }}

                    </span>

                </div>


                @if($proyecto->descripcion)

                    <p class="pj-desc">
                        {{ $proyecto->descripcion }}
                    </p>

                @endif


                <div class="pj-progress-box">


                    <div class="pj-progress-header">

                        <div>

                            <div class="pj-progress-title">
                                Avance general
                            </div>

                        </div>


                        <div class="pj-progress-number">
                            {{ $avance }}%
                        </div>

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


                    <div class="pj-meta-grid">


                        {{-- COSTO --}}
                        <div class="pj-meta-card">

                            <div class="pj-meta-icon">
                                <i class="dollar sign icon"></i>
                            </div>

                            <div>

                                <div class="pj-meta-label">
                                    Inversión
                                </div>

                                <div class="pj-meta-value">

                                    @if(!is_null($proyecto->costo))

                                        ${{ number_format(
                                            $proyecto->costo,
                                            2
                                        ) }}

                                    @else

                                        Sin costo definido

                                    @endif

                                </div>

                            </div>

                        </div>


                        {{-- ESTADO --}}
                        <div class="pj-meta-card">

                            <div class="pj-meta-icon">

                                <i
                                    class="
                                        {{ $estadoConfig['icon'] }}
                                        icon
                                    "
                                ></i>

                            </div>

                            <div>

                                <div class="pj-meta-label">
                                    Estado
                                </div>

                                <div class="pj-meta-value">

                                    {{
                                        $estados[
                                            $proyecto->estado
                                        ]
                                        ??
                                        $proyecto->estado
                                    }}

                                </div>

                            </div>

                        </div>


                        {{-- ACTUALIZACIONES --}}
                        <div class="pj-meta-card">

                            <div class="pj-meta-icon">
                                <i class="history icon"></i>
                            </div>

                            <div>

                                <div class="pj-meta-label">
                                    Actualizaciones
                                </div>

                                <div class="pj-meta-value">
                                    {{ $totalActualizaciones }}
                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </section>


            {{-- =================================================
                 HISTORIAL
            ================================================== --}}

            <section class="pj-card">


                <div class="pj-card-head">

                    <div>

                        <h2 class="pj-card-title">

                            <i class="history icon"></i>

                            Actualizaciones del comité

                        </h2>

                        <div class="pj-card-subtitle">
                            Historial de avances, comentarios
                            y evidencia publicada.
                        </div>

                    </div>


                    @if($totalActualizaciones > 0)

                        <span class="pj-count-badge">

                            {{ $totalActualizaciones }}

                            {{
                                $totalActualizaciones === 1
                                    ? 'actualización'
                                    : 'actualizaciones'
                            }}

                        </span>

                    @endif

                </div>


                @forelse($proyecto->avances as $a)


                    <div class="timeline-item">


                        <div class="timeline-marker">

                            <span class="timeline-dot"></span>


                            @if(!$loop->last)

                                <span class="timeline-line"></span>

                            @endif

                        </div>


                        <div class="timeline-content">


                            <div class="timeline-head">

                                <div class="timeline-pct">

                                    {{ $a->porcentaje }}% de avance

                                </div>


                                <div class="timeline-date">

                                    <i class="calendar outline icon"></i>

                                    {{
                                        \Carbon\Carbon::parse(
                                            $a->created_at
                                        )->format('d/m/Y')
                                    }}

                                </div>

                            </div>


                            @if($a->comentario)

                                <div class="timeline-comment">
                                    {{ $a->comentario }}
                                </div>

                            @else

                                <div
                                    class="timeline-comment"
                                    style="color:var(--muted);"
                                >
                                    Se actualizó el porcentaje
                                    de avance del proyecto.
                                </div>

                            @endif


                            @if($a->foto)

                                @php
                                    $fotoUrl =
                                        asset(
                                            'storage/'
                                            .$a->foto
                                        );
                                @endphp


                                <div
                                    class="timeline-photo"
                                    onclick="abrirEvidencia(
                                        '{{ $fotoUrl }}',
                                        '{{ $a->porcentaje }}% · {{ \Carbon\Carbon::parse($a->created_at)->format('d/m/Y') }}'
                                    )"
                                >

                                    <img
                                        src="{{ $fotoUrl }}"
                                        alt="Evidencia del avance del proyecto"
                                        loading="lazy"
                                    >


                                    <span class="timeline-photo-overlay">

                                        <i class="expand icon"></i>

                                        Ver evidencia

                                    </span>

                                </div>

                            @endif

                        </div>

                    </div>


                @empty


                    <div class="pj-empty">

                        <div>

                            <div class="pj-empty-icon">

                                <i class="history icon"></i>

                            </div>


                            <strong>
                                Aún no hay actualizaciones
                            </strong>

                            <span>
                                Cuando el comité registre avances,
                                aparecerán aquí en orden cronológico.
                            </span>

                        </div>

                    </div>


                @endforelse

            </section>

        </div>

    </div>


    {{-- =========================================================
         LIGHTBOX
    ========================================================== --}}

    <div
        class="pj-lightbox"
        id="pj-lightbox"
        role="dialog"
        aria-modal="true"
        aria-label="Evidencia del proyecto"
    >

        <button
            type="button"
            class="pj-lightbox-close"
            id="pj-lightbox-close"
            aria-label="Cerrar imagen"
        >
            <i class="times icon"></i>
        </button>


        <img
            id="pj-lightbox-img"
            src=""
            alt="Evidencia del proyecto"
        >


        <div
            class="pj-lightbox-caption"
            id="pj-lightbox-caption"
        ></div>

    </div>


    <script>

        window.abrirEvidencia =
            function (
                src,
                caption
            ) {

                const lightbox =
                    document.getElementById(
                        'pj-lightbox'
                    );


                const image =
                    document.getElementById(
                        'pj-lightbox-img'
                    );


                const captionEl =
                    document.getElementById(
                        'pj-lightbox-caption'
                    );


                image.src =
                    src;


                captionEl.textContent =
                    caption || '';


                lightbox
                    .classList
                    .add(
                        'show'
                    );


                document.body.style.overflow =
                    'hidden';

            };


        window.cerrarEvidencia =
            function () {

                const lightbox =
                    document.getElementById(
                        'pj-lightbox'
                    );


                const image =
                    document.getElementById(
                        'pj-lightbox-img'
                    );


                lightbox
                    .classList
                    .remove(
                        'show'
                    );


                image.src =
                    '';


                document.body.style.overflow =
                    '';

            };


        (function () {

            const lightbox =
                document.getElementById(
                    'pj-lightbox'
                );


            const close =
                document.getElementById(
                    'pj-lightbox-close'
                );


            if (!lightbox) {
                return;
            }


            close.addEventListener(
                'click',
                function (event) {

                    event.stopPropagation();

                    cerrarEvidencia();

                }
            );


            lightbox.addEventListener(
                'click',
                function (event) {

                    /*
                     * Cierra solamente al tocar
                     * el fondo, no la fotografía.
                     */
                    if (
                        event.target
                        ===
                        lightbox
                    ) {

                        cerrarEvidencia();

                    }

                }
            );


            document.addEventListener(
                'keydown',
                function (event) {

                    if (
                        event.key
                        ===
                        'Escape'
                    ) {

                        cerrarEvidencia();

                    }

                }
            );

        })();

    </script>

</x-app-layout>