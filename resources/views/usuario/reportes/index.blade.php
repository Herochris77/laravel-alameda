<x-app-layout>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>

        .tp-page {
            --primary: #0d9488;
            --primary-dark: #0f766e;
            --primary-light: #14b8a6;

            --success: #10b981;
            --danger: #ef4444;
            --warning: #f59e0b;

            --text: #0f172a;
            --muted: #64748b;
            --soft: #94a3b8;

            --border: #e2e8f0;

            --surface: #ffffff;
            --bg: #f8fafc;

            padding-bottom: 30px;
        }

        .tp-container {
            max-width: 1200px;
            margin: 0 auto;
        }


        /* =====================================================
           HERO
        ===================================================== */

        .tp-hero {
            position: relative;

            overflow: hidden;

            background:
                radial-gradient(
                    circle at top right,
                    rgba(255,255,255,.22),
                    transparent 34%
                ),
                linear-gradient(
                    135deg,
                    var(--primary) 0%,
                    var(--primary-light) 100%
                );

            border-radius: 24px;

            padding: 24px;

            color: #fff;

            margin-bottom: 20px;

            box-shadow:
                0 12px 30px rgba(13,148,136,.18);
        }

        .tp-hero-top {
            display: flex;

            align-items: center;

            gap: 14px;

            flex-wrap: wrap;

            position: relative;

            z-index: 2;
        }

        .tp-hero-icon {
            width: 58px;
            height: 58px;

            border-radius: 18px;

            background:
                rgba(255,255,255,.16);

            display: flex;

            align-items: center;

            justify-content: center;

            flex-shrink: 0;
        }

        .tp-hero-icon i {
            margin: 0 !important;

            font-size: 1.6rem;
        }

        .tp-title {
            margin: 0;

            font-size:
                clamp(1.4rem,3vw,2rem);

            font-weight: 900;

            letter-spacing: -.03em;
        }

        .tp-subtitle {
            margin: 3px 0 0;

            opacity: .92;

            font-size: .95rem;
        }

        .tp-period {
            margin-top: 7px;

            font-size: .78rem;

            opacity: .82;
        }


        /* =====================================================
           FILTRO PRINCIPAL
        ===================================================== */

        .tp-filter {
            display: flex;

            gap: 10px;

            align-items: flex-end;

            flex-wrap: wrap;

            margin-top: 18px;

            position: relative;

            z-index: 2;
        }

        .tp-filter .fg {
            display: flex;

            flex-direction: column;

            gap: 4px;
        }

        .tp-filter label {
            font-size: .75rem;

            font-weight: 700;

            opacity: .92;
        }

        .tp-filter input {
            border: none;

            border-radius: 10px;

            padding: 9px 10px;

            min-height: 39px;

            font-size: .88rem;

            outline: none;
        }

        .tp-filter button {
            min-height: 39px;

            border: none;

            border-radius: 10px;

            padding: 9px 14px;

            background: #fff;

            color: var(--primary-dark);

            font-size: .84rem;

            font-weight: 800;

            cursor: pointer;
        }

        .tp-filter button.ghost {
            background:
                rgba(255,255,255,.17);

            color: #fff;
        }


        /* =====================================================
           KPI
        ===================================================== */

        .tp-kpis {
            display: grid;

            grid-template-columns:
                repeat(4, 1fr);

            gap: 14px;

            margin-bottom: 18px;
        }

        .tp-kpi {
            background:
                var(--surface);

            border:
                1px solid var(--border);

            border-radius: 18px;

            padding: 17px;

            box-shadow:
                0 8px 22px rgba(15,23,42,.05);

            display: flex;

            align-items: center;

            gap: 13px;

            min-width: 0;
        }

        .tp-kpi .ic {
            width: 48px;
            height: 48px;

            flex-shrink: 0;

            border-radius: 14px;

            display: flex;

            align-items: center;

            justify-content: center;

            font-size: 1.25rem;
        }

        .tp-kpi .ic i {
            margin: 0 !important;
        }

        .ic-green {
            background:
                rgba(16,185,129,.12);

            color:
                var(--success);
        }

        .ic-red {
            background:
                rgba(239,68,68,.11);

            color:
                var(--danger);
        }

        .ic-teal {
            background:
                rgba(13,148,136,.11);

            color:
                var(--primary);
        }

        .tp-kpi-content {
            min-width: 0;
        }

        .tp-kpi .lbl {
            font-size: .77rem;

            color:
                var(--muted);

            margin-bottom: 2px;
        }

        .tp-kpi .val {
            font-size: 1.45rem;

            line-height: 1.05;

            font-weight: 900;

            color:
                var(--text);

            letter-spacing: -.025em;
        }

        .tp-kpi .sub {
            margin-top: 5px;

            color:
                var(--muted);

            font-size: .69rem;

            line-height: 1.3;
        }

        .tp-kpi .sub strong {
            color:
                var(--primary);
        }


        /* =====================================================
           EXPLICACIÓN
        ===================================================== */

        .tp-explain {
            display: flex;

            align-items: flex-start;

            gap: 12px;

            margin-bottom: 18px;

            padding: 13px 15px;

            border:
                1px solid #ccfbf1;

            border-radius: 15px;

            background:
                #f0fdfa;

            color:
                #475569;
        }

        .tp-explain-icon {
            width: 34px;
            height: 34px;

            border-radius: 10px;

            display: flex;

            align-items: center;

            justify-content: center;

            flex-shrink: 0;

            background:
                rgba(13,148,136,.12);

            color:
                var(--primary);
        }

        .tp-explain-icon i {
            margin: 0 !important;
        }

        .tp-explain-title {
            color:
                var(--text);

            font-weight: 900;

            font-size: .84rem;

            margin-bottom: 3px;
        }

        .tp-explain-text {
            font-size: .76rem;

            line-height: 1.5;
        }


        /* =====================================================
           CARDS
        ===================================================== */

        .tp-card {
            background:
                var(--surface);

            border:
                1px solid var(--border);

            border-radius: 18px;

            padding: 20px;

            box-shadow:
                0 8px 22px rgba(15,23,42,.05);

            margin-bottom: 18px;
        }

        .tp-grid2 {
            display: grid;

            grid-template-columns:
                1fr 1fr;

            gap: 16px;

            margin-bottom: 18px;
        }

        .tp-card h3 {
            margin:
                0 0 14px;

            font-size: 1rem;

            color:
                var(--text);

            display: flex;

            align-items: center;

            gap: 8px;
        }

        .tp-card h3 i {
            color:
                var(--primary);
        }

        .tp-card-subtitle {
            color:
                var(--muted);

            font-size: .75rem;

            margin:
                -8px 0 16px;

            line-height: 1.4;
        }


        /* =====================================================
           CATEGORÍAS
        ===================================================== */

        .cat-row {
            margin-bottom: 12px;
        }

        .cat-head {
            display: flex;

            justify-content:
                space-between;

            gap: 10px;

            margin-bottom: 5px;

            font-size: .82rem;

            color:
                var(--text);
        }

        .cat-head span:last-child {
            white-space: nowrap;
        }

        .cat-bar {
            height: 8px;

            overflow: hidden;

            border-radius: 20px;

            background:
                var(--bg);
        }

        .cat-fill {
            height: 100%;

            border-radius: 20px;

            background:
                var(--primary);
        }


        /* =====================================================
           COBRANZA CASAS
        ===================================================== */

        .cob-head {
            display: flex;

            justify-content:
                space-between;

            align-items: center;

            gap: 10px;

            margin-bottom: 8px;
        }

        .cob-text {
            font-size: .88rem;

            font-weight: 800;

            color:
                var(--text);
        }

        .cob-pct {
            font-size: 1rem;

            font-weight: 900;

            color:
                var(--success);
        }

        .cob-bar {
            height: 11px;

            overflow: hidden;

            border-radius: 20px;

            margin-bottom: 15px;

            background:
                var(--bg);
        }

        .cob-fill {
            height: 100%;

            background:
                var(--success);

            transition:
                width .35s ease;
        }

        .cob-description {
            margin-bottom: 9px;

            color:
                var(--muted);

            font-size: .75rem;
        }

        .casas-grid {
            display: grid;

            grid-template-columns:
                repeat(
                    auto-fill,
                    minmax(92px,1fr)
                );

            gap: 7px;
        }

        .casa-chip {
            display: flex;

            align-items: center;

            justify-content:
                space-between;

            gap: 6px;

            padding: 8px 9px;

            border-radius: 10px;

            font-size: .78rem;

            cursor: pointer;

            transition:
                transform .15s ease,
                box-shadow .15s ease;
        }

        .casa-chip:hover {
            transform:
                translateY(-1px);

            box-shadow:
                0 4px 12px rgba(15,23,42,.07);
        }

        .casa-chip.ok {
            border:
                1px solid #a7f3d0;

            background:
                #f0fdf4;
        }

        .casa-chip.pend {
            border:
                1px solid #fecaca;

            background:
                #fef2f2;
        }

        .casa-chip.ok i {
            color:
                var(--success);
        }

        .casa-chip.pend i {
            color:
                var(--danger);
        }

        .tp-legend {
            margin-top: 10px;

            display: flex;

            flex-wrap: wrap;

            gap: 14px;

            color:
                var(--soft);

            font-size: .73rem;
        }


        /* =====================================================
           LISTA DE GASTOS
        ===================================================== */

        .expenses-card-header {
            display: flex;

            align-items: flex-start;

            justify-content:
                space-between;

            gap: 15px;

            margin-bottom: 16px;
        }

        .expenses-card-header h3 {
            margin-bottom: 4px;
        }

        .expenses-description {
            color:
                var(--muted);

            font-size: .76rem;
        }

        .expenses-filters {
            display: grid;

            grid-template-columns:
                minmax(200px,1fr)
                minmax(150px,.6fr)
                110px
                auto;

            gap: 9px;

            padding: 12px;

            margin-bottom: 14px;

            border:
                1px solid var(--border);

            border-radius: 14px;

            background:
                var(--bg);
        }

        .expenses-field {
            min-width: 0;
        }

        .expenses-field label {
            display: block;

            margin-bottom: 4px;

            color:
                var(--muted);

            font-size: .69rem;

            font-weight: 800;
        }

        .expenses-input-wrap {
            position: relative;
        }

        .expenses-input-wrap i {
            position: absolute;

            left: 11px;

            top: 50%;

            transform:
                translateY(-50%);

            color:
                var(--soft);

            margin: 0 !important;
        }

        .expenses-field input,
        .expenses-field select {
            width: 100%;

            height: 38px;

            border:
                1px solid #dbe2ea;

            border-radius: 10px;

            background:
                #fff;

            color:
                var(--text);

            font-size: .78rem;

            outline: none;
        }

        .expenses-field input {
            padding:
                0 10px 0 33px;
        }

        .expenses-field select {
            padding:
                0 9px;
        }

        .expenses-field input:focus,
        .expenses-field select:focus {
            border-color:
                rgba(13,148,136,.5);

            box-shadow:
                0 0 0 3px rgba(13,148,136,.08);
        }

        .expenses-buttons {
            display: flex;

            align-items: flex-end;

            gap: 6px;
        }

        .expense-button {
            height: 38px;

            display: inline-flex;

            align-items: center;

            justify-content: center;

            gap: 6px;

            padding:
                0 12px;

            border: 0;

            border-radius: 10px;

            cursor: pointer;

            font-size: .76rem;

            font-weight: 800;

            text-decoration: none !important;
        }

        .expense-button i {
            margin: 0 !important;
        }

        .expense-button.primary {
            background:
                var(--primary);

            color:
                #fff;
        }

        .expense-button.secondary {
            background:
                #fff;

            color:
                var(--muted);

            border:
                1px solid var(--border);
        }

        .expenses-summary {
            display: flex;

            align-items: center;

            justify-content:
                space-between;

            gap: 10px;

            margin-bottom: 7px;

            color:
                var(--muted);

            font-size: .72rem;
        }

        .gasto-row {
            display: flex;

            align-items: center;

            justify-content:
                space-between;

            gap: 15px;

            min-height: 62px;

            padding: 10px 3px;

            border-bottom:
                1px solid var(--bg);
        }

        .gasto-row:last-child {
            border-bottom:
                none;
        }

        .gasto-content {
            min-width: 0;
        }

        .gasto-title {
            color:
                var(--text);

            font-size: .87rem;

            line-height: 1.3;
        }

        .gasto-meta {
            display: flex;

            align-items: center;

            flex-wrap: wrap;

            gap: 6px;

            margin-top: 4px;

            color:
                var(--muted);

            font-size: .7rem;
        }

        .gasto-category {
            display: inline-flex;

            align-items: center;

            padding:
                3px 7px;

            border-radius: 999px;

            background:
                rgba(13,148,136,.08);

            color:
                var(--primary-dark);

            font-weight: 700;
        }

        .gasto-monto {
            flex-shrink: 0;

            color:
                var(--text);

            font-size: .9rem;

            font-weight: 900;

            white-space: nowrap;
        }

        .expenses-empty {
            padding: 35px 15px;

            text-align: center;

            color:
                var(--muted);
        }

        .expenses-empty i {
            display: block;

            margin:
                0 0 9px !important;

            color:
                var(--soft);

            font-size: 1.8rem;
        }

        .expenses-empty strong {
            display: block;

            margin-bottom: 4px;

            color:
                var(--text);
        }


        /* =====================================================
           PAGINACIÓN
        ===================================================== */

        .pagination-wrapper {
            display: flex;

            align-items: center;

            justify-content:
                space-between;

            gap: 12px;

            padding-top: 14px;

            margin-top: 5px;

            border-top:
                1px solid var(--bg);
        }

        .pagination-info {
            color:
                var(--muted);

            font-size: .72rem;
        }

        .pagination-list {
            display: flex;

            align-items: center;

            flex-wrap: wrap;

            gap: 5px;
        }

        .pagination-item {
            min-width: 34px;
            height: 34px;

            display: inline-flex;

            align-items: center;

            justify-content: center;

            padding:
                0 9px;

            border:
                1px solid var(--border);

            border-radius: 9px;

            background:
                #fff;

            color:
                var(--muted);

            font-size: .73rem;

            font-weight: 800;

            text-decoration:
                none !important;
        }

        .pagination-item:hover {
            border-color:
                rgba(13,148,136,.4);

            color:
                var(--primary);
        }

        .pagination-item.active {
            border-color:
                var(--primary);

            background:
                var(--primary);

            color:
                #fff;
        }

        .pagination-item.disabled {
            pointer-events: none;

            opacity: .4;
        }


        /* =====================================================
           MODAL
        ===================================================== */

        .tp-modal {
            position: fixed;

            inset: 0;

            z-index: 9999;

            display: none;

            align-items: center;

            justify-content: center;

            padding: 16px;

            background:
                rgba(15,23,42,.48);
        }

        .tp-modal.show {
            display: flex;
        }

        .tp-modal-card {
            width: 100%;

            max-width: 430px;

            max-height: 80vh;

            overflow-y: auto;

            padding: 20px;

            background:
                #fff;

            border-radius: 18px;

            box-shadow:
                0 20px 50px rgba(0,0,0,.25);
        }

        .tp-modal-head {
            display: flex;

            align-items: center;

            justify-content:
                space-between;

            gap: 10px;

            margin-bottom: 12px;
        }

        .tp-modal-head h4 {
            margin: 0;

            color:
                var(--text);

            font-size: 1rem;
        }

        .tp-modal-close {
            cursor: pointer;

            color:
                var(--soft);
        }

        .tp-pend-item {
            display: flex;

            align-items: center;

            justify-content:
                space-between;

            gap: 12px;

            padding: 10px 0;

            border-bottom:
                1px solid var(--bg);

            font-size: .83rem;
        }

        .tp-pend-dias {
            flex-shrink: 0;

            color:
                var(--danger);

            font-size: .72rem;

            font-weight: 800;
        }


        /* =====================================================
           RESPONSIVE
        ===================================================== */

        @media (max-width: 900px) {

            .tp-kpis {
                grid-template-columns:
                    repeat(2,1fr);
            }

            .tp-grid2 {
                grid-template-columns:
                    1fr;
            }

            .expenses-filters {
                grid-template-columns:
                    1fr 1fr;
            }

            .expenses-buttons {
                align-items:
                    center;
            }

        }


        @media (max-width: 640px) {

            .tp-hero {
                padding: 18px;

                border-radius: 20px;
            }

            .tp-hero-icon {
                width: 48px;
                height: 48px;

                border-radius: 14px;
            }

            .tp-title {
                font-size: 1.3rem;
            }

            .tp-subtitle {
                font-size: .8rem;
            }

            .tp-filter {
                display: grid;

                grid-template-columns:
                    1fr 1fr;

                gap: 7px;
            }

            .tp-filter input,
            .tp-filter button {
                width: 100%;
            }

            .tp-kpis {
                gap: 8px;
            }

            .tp-kpi {
                padding: 12px;

                gap: 9px;

                border-radius: 15px;
            }

            .tp-kpi .ic {
                width: 40px;
                height: 40px;

                border-radius: 11px;

                font-size: 1rem;
            }

            .tp-kpi .lbl {
                font-size: .67rem;
            }

            .tp-kpi .val {
                font-size: 1.1rem;
            }

            .tp-kpi .sub {
                font-size: .6rem;
            }

            .tp-explain {
                padding: 11px 12px;
            }

            .tp-card {
                padding: 14px;

                border-radius: 16px;
            }

            .tp-card h3 {
                font-size: .9rem;
            }

            .casas-grid {
                grid-template-columns:
                    repeat(3,1fr);
            }

            .expenses-card-header {
                display: block;
            }

            .expenses-filters {
                grid-template-columns:
                    1fr;
            }

            .expenses-buttons {
                display: grid;

                grid-template-columns:
                    1fr 1fr;
            }

            .expense-button {
                width: 100%;
            }

            .gasto-row {
                min-height: 58px;

                align-items:
                    flex-start;
            }

            .gasto-title {
                font-size: .8rem;
            }

            .gasto-monto {
                font-size: .8rem;
            }

            .pagination-wrapper {
                flex-direction: column;

                align-items:
                    flex-start;
            }

        }


        @media (max-width: 400px) {

            .tp-kpis {
                grid-template-columns:
                    1fr;
            }

            .casas-grid {
                grid-template-columns:
                    repeat(2,1fr);
            }

        }

    </style>


    @php

        $finanzas =
            $data['finanzas'] ?? [];

        $cobranza =
            $data['cobranza'] ?? [];

        $lista =
            $data['gastos_lista'] ?? [
                'items' => [],
                'total' => 0,
                'pagina_actual' => 1,
                'ultima_pagina' => 1,
                'por_pagina' => 10,
                'desde' => null,
                'hasta' => null,
                'links' => [],
            ];

        $filtros =
            $data['filtros_gastos'] ?? [
                'buscar' => '',
                'categoria' => '',
                'por_pagina' => 10,
                'categorias' => [],
            ];

    @endphp


    <div class="tp-page">

        {{-- =====================================================
             HERO
        ====================================================== --}}

        <div class="tp-hero">

            <div class="tp-hero-top">

                <div class="tp-hero-icon">
                    <i class="chart pie icon"></i>
                </div>

                <div>

                    <h1 class="tp-title">
                        Transparencia del condominio
                    </h1>

                    <p class="tp-subtitle">
                        Consulta cuánto se recauda,
                        cuánto se gasta y el estado general
                        de la cobranza.
                    </p>

                    @if(!empty($data['periodo']))

                        <div class="tp-period">

                            <i class="calendar alternate outline icon"></i>

                            Información del
                            {{ $data['periodo'] }}

                        </div>

                    @endif

                </div>

            </div>


            {{-- FILTRO GENERAL --}}
            <div class="tp-filter">

                <div class="fg">

                    <label for="tp-desde">
                        Desde
                    </label>

                    <input
                        type="date"
                        id="tp-desde"
                        value="{{ request(
                            'fecha_inicio',
                            now()->startOfYear()->format('Y-m-d')
                        ) }}"
                    >

                </div>


                <div class="fg">

                    <label for="tp-hasta">
                        Hasta
                    </label>

                    <input
                        type="date"
                        id="tp-hasta"
                        value="{{ request(
                            'fecha_fin',
                            now()->format('Y-m-d')
                        ) }}"
                    >

                </div>


                <button
                    type="button"
                    id="tp-filtrar"
                >

                    <i class="filter icon"></i>

                    Consultar

                </button>


                <button
                    type="button"
                    id="tp-limpiar"
                    class="ghost"
                >
                    Limpiar
                </button>

            </div>

        </div>


        <div class="tp-container">


            {{-- =================================================
                 KPIs
            ================================================== --}}

            <div class="tp-kpis">


                {{-- RECAUDADO --}}
                <div class="tp-kpi">

                    <div class="ic ic-green">
                        <i class="arrow up icon"></i>
                    </div>

                    <div class="tp-kpi-content">

                        <div class="lbl">
                            Recaudado
                        </div>

                        <div
                            class="val"
                            id="k-recaudado"
                        >
                            $0
                        </div>

                        <div
                            class="sub"
                            id="k-recaudacion-info"
                        >
                            0% de los recibos esperados
                        </div>

                    </div>

                </div>


                {{-- GASTADO --}}
                <div class="tp-kpi">

                    <div class="ic ic-red">
                        <i class="arrow down icon"></i>
                    </div>

                    <div class="tp-kpi-content">

                        <div class="lbl">
                            Gastado
                        </div>

                        <div
                            class="val"
                            id="k-gastado"
                        >
                            $0
                        </div>

                        <div class="sub">
                            Egresos registrados en el período
                        </div>

                    </div>

                </div>


                {{-- SALDO --}}
                <div class="tp-kpi">

                    <div class="ic ic-teal">
                        <i class="money icon"></i>
                    </div>

                    <div class="tp-kpi-content">

                        <div class="lbl">
                            Saldo del fondo
                        </div>

                        <div
                            class="val"
                            id="k-saldo"
                        >
                            $0
                        </div>

                        <div class="sub">
                            Ingresos históricos menos egresos
                        </div>

                    </div>

                </div>


                {{-- CASAS AL CORRIENTE --}}
                <div class="tp-kpi">

                    <div class="ic ic-teal">
                        <i class="home icon"></i>
                    </div>

                    <div class="tp-kpi-content">

                        <div class="lbl">
                            Casas al corriente
                        </div>

                        <div
                            class="val"
                            id="k-cobranza"
                        >
                            0%
                        </div>

                        <div
                            class="sub"
                            id="k-cobranza-info"
                        >
                            0 de 0 casas
                        </div>

                    </div>

                </div>

            </div>


            {{-- =================================================
                 EXPLICACIÓN
            ================================================== --}}

            <div class="tp-explain">

                <div class="tp-explain-icon">
                    <i class="info icon"></i>
                </div>

                <div>

                    <div class="tp-explain-title">
                        ¿Cómo leer estos datos?
                    </div>

                    <div class="tp-explain-text">

                        <strong>Recaudado</strong>
                        indica el dinero recibido durante el período.

                        El porcentaje mostrado debajo compara
                        únicamente los recibos cobrados contra
                        los recibos esperados.

                        <strong>Casas al corriente</strong>
                        indica cuántas viviendas no tienen
                        recibos vencidos pendientes.

                    </div>

                </div>

            </div>


            {{-- =================================================
                 GRÁFICA
            ================================================== --}}

            <div class="tp-card">

                <h3>

                    <i class="exchange icon"></i>

                    Ingresos vs. egresos

                </h3>

                <div class="tp-card-subtitle">
                    Comportamiento financiero de los últimos 6 meses.
                </div>

                <div
                    style="
                        position:relative;
                        height:260px;
                    "
                >
                    <canvas id="chart-ie"></canvas>
                </div>

            </div>


            {{-- =================================================
                 CATEGORÍAS + COBRANZA
            ================================================== --}}

            <div class="tp-grid2">


                {{-- CATEGORÍAS --}}
                <div
                    class="tp-card"
                    style="margin-bottom:0;"
                >

                    <h3>

                        <i class="chart bar icon"></i>

                        ¿En qué se está gastando?

                    </h3>

                    <div class="tp-card-subtitle">
                        Distribución de los egresos
                        dentro del período seleccionado.
                    </div>

                    <div id="gastos-cat">

                        <p
                            style="
                                color:var(--soft);
                            "
                        >
                            Sin datos en el período.
                        </p>

                    </div>

                </div>


                {{-- COBRANZA --}}
                <div
                    class="tp-card"
                    style="margin-bottom:0;"
                >

                    <h3>

                        <i class="home icon"></i>

                        Estado de cobranza

                    </h3>

                    <div class="tp-card-subtitle">
                        Una casa aparece pendiente únicamente
                        cuando tiene al menos un recibo vencido.
                    </div>


                    <div class="cob-head">

                        <span
                            class="cob-text"
                            id="cob-texto"
                        >
                            0 de 0 casas al corriente
                        </span>

                        <span
                            class="cob-pct"
                            id="cob-pct"
                        >
                            0%
                        </span>

                    </div>


                    <div class="cob-bar">

                        <div
                            class="cob-fill"
                            id="cob-fill"
                            style="width:0%;"
                        ></div>

                    </div>


                    <div class="cob-description">
                        Toca una casa para consultar
                        si tiene recibos vencidos.
                    </div>


                    <div
                        class="casas-grid"
                        id="casas-grid"
                    ></div>


                    <div class="tp-legend">

                        <span>

                            <i
                                class="check circle icon"
                                style="
                                    color:var(--success);
                                "
                            ></i>

                            Sin recibos vencidos

                        </span>

                        <span>

                            <i
                                class="clock icon"
                                style="
                                    color:var(--danger);
                                "
                            ></i>

                            Con recibos vencidos

                        </span>

                    </div>

                </div>

            </div>


            {{-- =================================================
                 MOVIMIENTOS DE EGRESOS
            ================================================== --}}

            <div class="tp-card">


                <div class="expenses-card-header">

                    <div>

                        <h3>

                            <i class="list icon"></i>

                            Movimientos de egresos

                        </h3>

                        <div class="expenses-description">
                            Consulta y filtra los gastos
                            registrados durante el período seleccionado.
                        </div>

                    </div>

                </div>


                {{-- FILTROS DE LA LISTA --}}
                <form
                    method="GET"
                    action="{{ route('usuario.reportes.index') }}"
                    class="expenses-filters"
                    id="gastos-filter-form"
                >

                    {{-- Mantener fechas --}}
                    <input
                        type="hidden"
                        name="fecha_inicio"
                        value="{{ request(
                            'fecha_inicio',
                            now()->startOfYear()->format('Y-m-d')
                        ) }}"
                    >

                    <input
                        type="hidden"
                        name="fecha_fin"
                        value="{{ request(
                            'fecha_fin',
                            now()->format('Y-m-d')
                        ) }}"
                    >


                    {{-- BUSCADOR --}}
                    <div class="expenses-field">

                        <label for="gasto_buscar">
                            Buscar movimiento
                        </label>

                        <div class="expenses-input-wrap">

                            <i class="search icon"></i>

                            <input
                                type="search"
                                name="gasto_buscar"
                                id="gasto_buscar"
                                placeholder="Ej. internet, jardinería..."
                                value="{{ $filtros['buscar'] ?? '' }}"
                            >

                        </div>

                    </div>


                    {{-- CATEGORÍA --}}
                    <div class="expenses-field">

                        <label for="gasto_categoria">
                            Categoría
                        </label>

                        <select
                            name="gasto_categoria"
                            id="gasto_categoria"
                        >

                            <option value="">
                                Todas
                            </option>

                            @foreach(
                                ($filtros['categorias'] ?? [])
                                as $categoria
                            )

                                <option
                                    value="{{ $categoria }}"
                                    @selected(
                                        ($filtros['categoria'] ?? '')
                                        ===
                                        $categoria
                                    )
                                >
                                    {{ ucfirst($categoria) }}
                                </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- POR PÁGINA --}}
                    <div class="expenses-field">

                        <label for="gasto_por_pagina">
                            Mostrar
                        </label>

                        <select
                            name="gasto_por_pagina"
                            id="gasto_por_pagina"
                        >

                            @foreach([10,20,50] as $cantidad)

                                <option
                                    value="{{ $cantidad }}"
                                    @selected(
                                        intval(
                                            $filtros['por_pagina']
                                            ?? 10
                                        )
                                        ===
                                        $cantidad
                                    )
                                >
                                    {{ $cantidad }}
                                </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- BOTONES --}}
                    <div class="expenses-buttons">

                        <button
                            type="submit"
                            class="
                                expense-button
                                primary
                            "
                        >

                            <i class="filter icon"></i>

                            Filtrar

                        </button>


                        <a
                            href="{{
                                route(
                                    'usuario.reportes.index',
                                    [
                                        'fecha_inicio' =>
                                            request(
                                                'fecha_inicio',
                                                now()
                                                    ->startOfYear()
                                                    ->format('Y-m-d')
                                            ),

                                        'fecha_fin' =>
                                            request(
                                                'fecha_fin',
                                                now()
                                                    ->format('Y-m-d')
                                            ),
                                    ]
                                )
                            }}"
                            class="
                                expense-button
                                secondary
                            "
                        >

                            <i class="times icon"></i>

                            Limpiar

                        </a>

                    </div>

                </form>


                {{-- RESUMEN --}}
                <div class="expenses-summary">

                    <span>

                        @if(($lista['total'] ?? 0) > 0)

                            Mostrando
                            {{ $lista['desde'] }}
                            –
                            {{ $lista['hasta'] }}

                            de

                            {{ $lista['total'] }}

                            movimientos

                        @else

                            No hay movimientos para mostrar

                        @endif

                    </span>

                </div>


                {{-- LISTA --}}
                <div>

                    @forelse(
                        ($lista['items'] ?? [])
                        as $gasto
                    )

                        <div class="gasto-row">

                            <div class="gasto-content">

                                <div class="gasto-title">
                                    {{ $gasto['titulo'] }}
                                </div>


                                <div class="gasto-meta">

                                    <span class="gasto-category">
                                        {{ ucfirst(
                                            $gasto['categoria']
                                        ) }}
                                    </span>

                                    <span>
                                        <i class="calendar outline icon"></i>

                                        {{ $gasto['fecha'] }}
                                    </span>

                                </div>

                            </div>


                            <div class="gasto-monto">

                                ${{ number_format(
                                    $gasto['monto'],
                                    0,
                                    '.',
                                    ','
                                ) }}

                            </div>

                        </div>

                    @empty

                        <div class="expenses-empty">

                            <i class="search icon"></i>

                            <strong>
                                No encontramos movimientos
                            </strong>

                            Prueba cambiando la búsqueda,
                            la categoría o el período.

                        </div>

                    @endforelse

                </div>


                {{-- PAGINACIÓN --}}
                @if(
                    ($lista['ultima_pagina'] ?? 1)
                    > 1
                )

                    <div class="pagination-wrapper">


                        <div class="pagination-info">

                            Página
                            {{ $lista['pagina_actual'] }}

                            de

                            {{ $lista['ultima_pagina'] }}

                        </div>


                        <div class="pagination-list">

                            @foreach(
                                ($lista['links'] ?? [])
                                as $link
                            )

                                @php

                                    $label =
                                        html_entity_decode(
                                            strip_tags(
                                                $link['label']
                                            )
                                        );

                                    /*
                                     * Cambiamos los textos
                                     * por iconos más compactos.
                                     */
                                    if (
                                        str_contains(
                                            strtolower($label),
                                            'previous'
                                        )
                                    ) {
                                        $label = '‹';
                                    }

                                    if (
                                        str_contains(
                                            strtolower($label),
                                            'next'
                                        )
                                    ) {
                                        $label = '›';
                                    }

                                @endphp


                                @if(empty($link['url']))

                                    <span
                                        class="
                                            pagination-item
                                            disabled
                                        "
                                    >
                                        {{ $label }}
                                    </span>

                                @elseif($link['active'])

                                    <span
                                        class="
                                            pagination-item
                                            active
                                        "
                                    >
                                        {{ $label }}
                                    </span>

                                @else

                                    <a
                                        href="{{ $link['url'] }}#gastos-filter-form"
                                        class="pagination-item"
                                    >
                                        {{ $label }}
                                    </a>

                                @endif

                            @endforeach

                        </div>

                    </div>

                @endif

            </div>

        </div>

    </div>


    {{-- =========================================================
         MODAL CASA
    ========================================================== --}}

    <div
        class="tp-modal"
        id="tp-modal"
        onclick="
            if(event.target === this) {
                cerrarModal()
            }
        "
    >

        <div class="tp-modal-card">

            <div class="tp-modal-head">

                <h4 id="tp-modal-title">
                    Casa
                </h4>

                <span
                    class="tp-modal-close"
                    onclick="cerrarModal()"
                >
                    <i class="times icon"></i>
                </span>

            </div>


            <div id="tp-modal-body"></div>

        </div>

    </div>


    <script>

        const tpFmt =
            new Intl.NumberFormat(
                'es-MX',
                {
                    style: 'currency',
                    currency: 'MXN',
                    maximumFractionDigits: 0
                }
            );


        let chartIE = null;


        const DATA =
            @json($data);


        function esc(t) {

            return String(
                t == null ? '' : t
            )
                .replace(
                    /&/g,
                    '&amp;'
                )
                .replace(
                    /</g,
                    '&lt;'
                )
                .replace(
                    />/g,
                    '&gt;'
                )
                .replace(
                    /"/g,
                    '&quot;'
                );

        }


        function pintar(r) {

            const f =
                r.finanzas || {};

            const cob =
                r.cobranza || {};


            /*
             * ==================================================
             * KPIs
             * ==================================================
             */

            $('#k-recaudado').text(
                tpFmt.format(
                    f.recaudado || 0
                )
            );


            $('#k-gastado').text(
                tpFmt.format(
                    f.gastado || 0
                )
            );


            $('#k-saldo').text(
                tpFmt.format(
                    f.saldo_fondo || 0
                )
            );


            $('#k-recaudacion-info').html(
                '<strong>'
                + (
                    f.recaudacion_pct
                    || 0
                )
                + '%</strong> de los recibos esperados'
            );


            $('#k-cobranza').text(
                (
                    cob.pct
                    || 0
                )
                + '%'
            );


            $('#k-cobranza-info').text(
                (
                    cob.al_corriente
                    || 0
                )
                + ' de '
                + (
                    cob.total
                    || 0
                )
                + ' casas'
            );


            /*
             * ==================================================
             * GRÁFICA
             * ==================================================
             */

            const ie =
                r.ingresos_egresos
                || [];


            const labels =
                ie.map(
                    m => m.label
                );


            const ing =
                ie.map(
                    m => m.ingresos
                );


            const egr =
                ie.map(
                    m => m.egresos
                );


            const ctx =
                document.getElementById(
                    'chart-ie'
                );


            if (chartIE) {
                chartIE.destroy();
            }


            if (ctx) {

                chartIE =
                    new Chart(
                        ctx,
                        {
                            type: 'bar',

                            data: {

                                labels:
                                    labels,

                                datasets: [

                                    {
                                        label:
                                            'Ingresos',

                                        data:
                                            ing,

                                        backgroundColor:
                                            '#10b981',

                                        borderRadius:
                                            6
                                    },

                                    {
                                        label:
                                            'Egresos',

                                        data:
                                            egr,

                                        backgroundColor:
                                            '#ef4444',

                                        borderRadius:
                                            6
                                    }

                                ]

                            },

                            options: {

                                responsive:
                                    true,

                                maintainAspectRatio:
                                    false,

                                plugins: {

                                    legend: {
                                        position:
                                            'bottom'
                                    },

                                    tooltip: {

                                        callbacks: {

                                            label:
                                                c =>
                                                    c.dataset.label
                                                    + ': '
                                                    + tpFmt.format(
                                                        c.parsed.y
                                                    )

                                        }

                                    }

                                },

                                scales: {

                                    y: {

                                        beginAtZero:
                                            true,

                                        ticks: {

                                            callback:
                                                v =>
                                                    tpFmt.format(
                                                        v
                                                    )

                                        }

                                    }

                                }

                            }

                        }
                    );

            }


            /*
             * ==================================================
             * CATEGORÍAS
             * ==================================================
             */

            const cats =
                r.gastos_categoria
                || [];


            if (cats.length) {

                $('#gastos-cat').html(

                    cats.map(
                        c => {

                            const pct =
                                Math.max(
                                    0,
                                    Math.min(
                                        100,
                                        Number(
                                            c.pct
                                        )
                                        || 0
                                    )
                                );


                            return `
                                <div class="cat-row">

                                    <div class="cat-head">

                                        <span>
                                            ${esc(c.categoria)}
                                        </span>

                                        <span>

                                            ${tpFmt.format(c.monto)}

                                            ·

                                            ${c.pct}%

                                        </span>

                                    </div>


                                    <div class="cat-bar">

                                        <div
                                            class="cat-fill"
                                            style="
                                                width:${pct}%;
                                            "
                                        ></div>

                                    </div>

                                </div>
                            `;

                        }
                    )
                    .join('')

                );

            } else {

                $('#gastos-cat').html(
                    '<p style="color:var(--soft);">'
                    + 'Sin datos en el período.'
                    + '</p>'
                );

            }


            /*
             * ==================================================
             * COBRANZA
             * ==================================================
             */

            $('#cob-texto').text(
                (
                    cob.al_corriente
                    || 0
                )
                + ' de '
                + (
                    cob.total
                    || 0
                )
                + ' casas al corriente'
            );


            $('#cob-pct').text(
                (
                    cob.pct
                    || 0
                )
                + '%'
            );


            const cobPctVisual =
                Math.max(
                    0,
                    Math.min(
                        100,
                        Number(
                            cob.pct
                        )
                        || 0
                    )
                );


            $('#cob-fill').css(
                'width',
                cobPctVisual
                + '%'
            );


            window._cobCasas =
                cob.casas || [];


            $('#casas-grid').html(

                (
                    cob.casas
                    || []
                )
                    .map(
                        (c, i) => {

                            const ok =
                                c.estado
                                ===
                                'al_corriente';


                            return `
                                <div
                                    class="
                                        casa-chip
                                        ${
                                            ok
                                            ? 'ok'
                                            : 'pend'
                                        }
                                    "
                                    onclick="
                                        verCasa(${i})
                                    "
                                >

                                    <span>
                                        Casa ${esc(c.casa)}
                                    </span>

                                    <i
                                        class="
                                            ${
                                                ok
                                                ? 'check'
                                                : 'clock'
                                            }
                                            icon
                                        "
                                    ></i>

                                </div>
                            `;

                        }
                    )
                    .join('')

            );

        }


        /*
         * =====================================================
         * DETALLE CASA
         * =====================================================
         */

        window.verCasa =
            function (i) {

                const c =
                    (
                        window._cobCasas
                        || []
                    )[i];


                if (! c) {
                    return;
                }


                document.getElementById(
                    'tp-modal-title'
                ).textContent =
                    'Casa '
                    + c.casa;


                let body = '';


                if (
                    c.estado
                    ===
                    'al_corriente'
                ) {

                    body = `
                        <p
                            style="
                                color:var(--muted);
                                line-height:1.5;
                            "
                        >

                            <i
                                class="
                                    check circle icon
                                "
                                style="
                                    color:var(--success);
                                "
                            ></i>

                            Esta casa no tiene
                            recibos vencidos pendientes.

                        </p>
                    `;

                } else {

                    const items =
                        c.pendientes
                        || [];


                    body = `
                        <p
                            style="
                                margin:0 0 9px;
                                color:var(--muted);
                                font-size:.8rem;
                            "
                        >
                            Recibos vencidos:
                        </p>
                    `;


                    body +=
                        items.map(
                            p => `

                                <div
                                    class="tp-pend-item"
                                >

                                    <div>

                                        <div>
                                            ${esc(p.concepto)}
                                        </div>

                                        <div
                                            style="
                                                margin-top:2px;
                                                font-size:.7rem;
                                                color:var(--muted);
                                            "
                                        >
                                            Venció el
                                            ${esc(p.vence)}
                                        </div>

                                    </div>


                                    <span
                                        class="
                                            tp-pend-dias
                                        "
                                    >
                                        ${esc(p.dias)}
                                        día(s)
                                    </span>

                                </div>

                            `
                        )
                        .join('');


                    if (! items.length) {

                        body += `
                            <p
                                style="
                                    color:var(--muted);
                                "
                            >
                                Sin detalle disponible.
                            </p>
                        `;

                    }

                }


                document.getElementById(
                    'tp-modal-body'
                ).innerHTML =
                    body;


                document.getElementById(
                    'tp-modal'
                ).classList.add(
                    'show'
                );

            };


        window.cerrarModal =
            function () {

                document.getElementById(
                    'tp-modal'
                ).classList.remove(
                    'show'
                );

            };


        document.addEventListener(
            'keydown',
            function (event) {

                if (
                    event.key
                    ===
                    'Escape'
                ) {
                    cerrarModal();
                }

            }
        );


        /*
         * =====================================================
         * FILTRO PRINCIPAL
         * =====================================================
         */

        const TP_BASE =
            '{{ route("usuario.reportes.index") }}';


        $(function () {

            pintar(
                DATA
            );


            $('#tp-filtrar').on(
                'click',
                function () {

                    const desde =
                        document.getElementById(
                            'tp-desde'
                        ).value;


                    const hasta =
                        document.getElementById(
                            'tp-hasta'
                        ).value;


                    if (
                        ! desde
                        || ! hasta
                    ) {
                        return;
                    }


                    window.location =
                        TP_BASE
                        + '?fecha_inicio='
                        + encodeURIComponent(
                            desde
                        )
                        + '&fecha_fin='
                        + encodeURIComponent(
                            hasta
                        );

                }
            );


            $('#tp-limpiar').on(
                'click',
                function () {

                    window.location =
                        TP_BASE;

                }
            );


            /*
             * Cambiar cantidad por página
             * automáticamente.
             */
            $('#gasto_por_pagina').on(
                'change',
                function () {

                    document.getElementById(
                        'gastos-filter-form'
                    ).submit();

                }
            );


            /*
             * Cambiar categoría automáticamente.
             */
            $('#gasto_categoria').on(
                'change',
                function () {

                    document.getElementById(
                        'gastos-filter-form'
                    ).submit();

                }
            );

        });

    </script>

</x-app-layout>