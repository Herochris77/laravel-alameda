<x-app-layout>

    <style>
        .encuestas-page {
            --primary: #8b5cf6;
            --primary-dark: #7c3aed;
            --primary-deep: #6d28d9;
            --primary-soft: #f5f3ff;

            --success: #10b981;
            --success-dark: #059669;

            --danger: #ef4444;
            --warning: #f59e0b;

            --text: #0f172a;
            --text-secondary: #334155;
            --muted: #64748b;
            --soft: #94a3b8;

            --surface: #ffffff;
            --surface-soft: #f8fafc;
            --surface-muted: #f1f5f9;

            --border: #e2e8f0;

            --shadow-sm:
                0 5px 18px rgba(15, 23, 42, .05);

            --shadow-md:
                0 12px 30px rgba(15, 23, 42, .09);

            width: 100%;
            padding-bottom: 32px;
        }

        * {
            box-sizing: border-box;
        }

        .encuestas-container {
            width: 100%;
            max-width: 1180px;
            margin: 0 auto;
        }


        /* =====================================================
           HERO
        ===================================================== */

        .encuestas-hero {
            position: relative;
            overflow: hidden;

            display: flex;
            align-items: center;
            justify-content: space-between;

            gap: 18px;

            padding: 24px 26px;
            margin-bottom: 16px;

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
                    var(--primary-dark)
                );

            box-shadow:
                0 14px 34px rgba(124,58,237,.18);
        }

        .encuestas-hero::after {
            content: '';

            position: absolute;

            width: 225px;
            height: 225px;

            right: -85px;
            bottom: -145px;

            border-radius: 999px;

            background:
                rgba(255,255,255,.08);
        }

        .encuestas-hero-left {
            position: relative;
            z-index: 2;

            display: flex;
            align-items: center;

            gap: 14px;

            min-width: 0;
        }

        .encuestas-hero-icon {
            width: 55px;
            height: 55px;

            flex-shrink: 0;

            display: flex;
            align-items: center;
            justify-content: center;

            border-radius: 16px;

            background:
                rgba(255,255,255,.16);

            border:
                1px solid rgba(255,255,255,.17);
        }

        .encuestas-hero-icon i {
            margin: 0 !important;
            font-size: 1.45rem;
        }

        .encuestas-title {
            margin: 0;

            font-size:
                clamp(1.4rem,3vw,2rem);

            font-weight: 900;

            letter-spacing: -.035em;

            line-height: 1.1;
        }

        .encuestas-subtitle {
            max-width: 650px;

            margin: 4px 0 0;

            opacity: .91;

            font-size: .86rem;

            line-height: 1.45;
        }

        .encuestas-hero-status {
            position: relative;
            z-index: 2;

            display: inline-flex;
            align-items: center;

            gap: 7px;

            padding: 9px 12px;

            border:
                1px solid rgba(255,255,255,.18);

            border-radius: 999px;

            background:
                rgba(255,255,255,.14);

            font-size: .7rem;

            font-weight: 850;

            white-space: nowrap;
        }

        .encuestas-hero-status i {
            margin: 0 !important;
        }


        /* =====================================================
           TABS
        ===================================================== */

        .encuestas-tabs {
            display: grid;

            grid-template-columns:
                repeat(2,minmax(0,1fr));

            gap: 6px;

            padding: 6px;

            margin-bottom: 16px;

            border:
                1px solid var(--border);

            border-radius: 16px;

            background:
                var(--surface);

            box-shadow:
                var(--shadow-sm);
        }

        .encuesta-tab {
            min-height: 43px;

            display: flex;
            align-items: center;
            justify-content: center;

            gap: 7px;

            padding: 8px 12px;

            border: 0;

            border-radius: 11px;

            color:
                var(--muted);

            background:
                transparent;

            cursor: pointer;

            font-size: .76rem;

            font-weight: 900;

            transition:
                background .16s ease,
                color .16s ease,
                box-shadow .16s ease;
        }

        .encuesta-tab i {
            margin: 0 !important;
        }

        .encuesta-tab:hover {
            color:
                var(--primary);

            background:
                var(--primary-soft);
        }

        .encuesta-tab.tab-activo {
            color: #fff;

            background:
                linear-gradient(
                    135deg,
                    var(--primary),
                    var(--primary-dark)
                );

            box-shadow:
                0 7px 17px rgba(139,92,246,.20);
        }

        .tab-count {
            min-width: 21px;
            height: 21px;

            display: inline-flex;
            align-items: center;
            justify-content: center;

            padding: 0 6px;

            border-radius: 999px;

            background:
                rgba(255,255,255,.20);

            font-size: .61rem;
        }

        .encuesta-tab:not(.tab-activo)
        .tab-count {
            color:
                var(--primary);

            background:
                var(--primary-soft);
        }


        /* =====================================================
           SECTION HEADING
        ===================================================== */

        .survey-section-header {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;

            gap: 12px;

            margin-bottom: 11px;
        }

        .survey-section-title {
            margin: 0;

            color:
                var(--text);

            font-size: 1rem;

            font-weight: 900;
        }

        .survey-section-description {
            margin-top: 2px;

            color:
                var(--muted);

            font-size: .7rem;
        }

        .survey-section-count {
            color:
                var(--soft);

            font-size: .68rem;

            white-space: nowrap;
        }


        /* =====================================================
           LOADING
        ===================================================== */

        .survey-loading {
            display: none;

            padding: 35px 18px;

            border:
                1px solid var(--border);

            border-radius: 18px;

            background:
                var(--surface);

            text-align: center;

            box-shadow:
                var(--shadow-sm);
        }

        .survey-loading.show {
            display: block;
        }

        .survey-loader {
            width: 30px;
            height: 30px;

            margin:
                0 auto 10px;

            border:
                3px solid #ede9fe;

            border-top-color:
                var(--primary);

            border-radius: 999px;

            animation:
                surveySpin .7s linear infinite;
        }

        .survey-loading-title {
            color:
                var(--text);

            font-size: .78rem;

            font-weight: 900;
        }

        .survey-loading-text {
            margin-top: 2px;

            color:
                var(--muted);

            font-size: .66rem;
        }

        @keyframes surveySpin {
            to {
                transform:
                    rotate(360deg);
            }
        }


        /* =====================================================
           GRID
        ===================================================== */

        .encuestas-grid {
            display: grid;

            grid-template-columns:
                repeat(2,minmax(0,1fr));

            gap: 14px;
        }


        /* =====================================================
           CARD
        ===================================================== */

        .encuesta-card {
            overflow: hidden;

            display: flex;
            flex-direction: column;

            min-width: 0;

            border:
                1px solid var(--border);

            border-radius: 18px;

            background: #fff;

            box-shadow:
                var(--shadow-sm);

            transition:
                transform .18s ease,
                box-shadow .18s ease,
                border-color .18s ease;
        }

        @media (hover:hover) and (pointer:fine) {

            .encuesta-card:hover {
                transform:
                    translateY(-2px);

                border-color:
                    rgba(139,92,246,.28);

                box-shadow:
                    var(--shadow-md);
            }

        }


        /* =====================================================
           CARD HEADER
        ===================================================== */

        .encuesta-header {
            position: relative;

            overflow: hidden;

            padding: 16px 17px;

            color: #fff;

            background:
                linear-gradient(
                    135deg,
                    var(--primary),
                    var(--primary-dark)
                );
        }

        .encuesta-header::after {
            content: '';

            position: absolute;

            width: 100px;
            height: 100px;

            right: -38px;
            bottom: -60px;

            border-radius: 999px;

            background:
                rgba(255,255,255,.08);
        }

        .encuesta-header-row {
            position: relative;
            z-index: 2;

            display: flex;
            align-items: flex-start;
            justify-content: space-between;

            gap: 10px;
        }

        .encuesta-header-main {
            min-width: 0;
        }

        .encuesta-header h3 {
            margin: 0;

            color: #fff;

            font-size: .97rem;

            line-height: 1.35;

            font-weight: 900;

            overflow-wrap: anywhere;
        }

        .encuesta-type-description {
            margin-top: 5px;

            color:
                rgba(255,255,255,.76);

            font-size: .64rem;

            line-height: 1.35;
        }

        .tipo-badge,
        .estado-badge {
            flex-shrink: 0;

            display: inline-flex;
            align-items: center;

            gap: 4px;

            padding: 5px 8px;

            border-radius: 999px;

            color: #fff;

            background:
                rgba(255,255,255,.18);

            font-size: .6rem;

            font-weight: 900;

            white-space: nowrap;

            text-transform: uppercase;
        }

        .tipo-badge i,
        .estado-badge i {
            margin: 0 !important;
        }

        .estado-badge.cerrada {
            background:
                rgba(239,68,68,.22);
        }

        .estado-badge.vencida {
            background:
                rgba(245,158,11,.25);
        }


        /* =====================================================
           BODY
        ===================================================== */

        .encuesta-body {
            flex: 1;

            padding: 17px;
        }

        .descripcion-texto {
            margin:
                0 0 13px;

            color:
                var(--muted);

            font-size: .78rem;

            line-height: 1.5;

            overflow-wrap: anywhere;
        }

        .fecha-info {
            display: flex;
            align-items: flex-start;

            gap: 7px;

            margin-bottom: 13px;

            padding: 9px 10px;

            border:
                1px solid #ede9fe;

            border-radius: 11px;

            color:
                var(--muted);

            background:
                #faf9ff;

            font-size: .68rem;

            line-height: 1.4;

            font-weight: 750;
        }

        .fecha-info i {
            margin:
                2px 0 0 !important;

            flex-shrink: 0;

            color:
                var(--primary);
        }


        /* =====================================================
           INSTRUCCIÓN
        ===================================================== */

        .vote-instruction {
            display: flex;
            align-items: flex-start;

            gap: 7px;

            margin-bottom: 10px;

            color:
                var(--muted);

            font-size: .67rem;

            line-height: 1.4;
        }

        .vote-instruction i {
            margin:
                1px 0 0 !important;

            color:
                var(--primary);
        }


        /* =====================================================
           OPCIONES
        ===================================================== */

        .opciones-list {
            display: flex;

            flex-direction: column;

            gap: 8px;
        }

        .opcion-item {
            min-height: 48px;

            display: flex;
            align-items: center;

            gap: 10px;

            padding: 11px 12px;

            border:
                1.5px solid var(--border);

            border-radius: 13px;

            color:
                var(--text-secondary);

            background: #fff;

            cursor: pointer;

            transition:
                border-color .16s ease,
                background .16s ease,
                box-shadow .16s ease;
        }

        .opcion-item:hover {
            border-color:
                rgba(139,92,246,.45);

            background:
                #fcfaff;
        }

        .opcion-item.seleccionada {
            border-color:
                var(--primary);

            background:
                var(--primary-soft);

            box-shadow:
                0 0 0 2px rgba(139,92,246,.05);
        }

        .opcion-item.votada {
            display: block;

            min-height: 0;

            cursor: default;

            border-color:
                #e8edf3;

            background: #fff;
        }

        .opcion-item.votada:hover {
            border-color:
                #e8edf3;

            background: #fff;
        }

        .radio-circle,
        .checkbox-square {
            width: 21px;
            height: 21px;

            flex-shrink: 0;

            display: flex;
            align-items: center;
            justify-content: center;

            border:
                2px solid #cbd5e1;

            background: #fff;

            transition:
                .16s ease;
        }

        .radio-circle {
            border-radius: 999px;
        }

        .checkbox-square {
            border-radius: 6px;
        }

        .opcion-item.seleccionada
        .radio-circle,
        .opcion-item.seleccionada
        .checkbox-square {
            border-color:
                var(--primary);

            background:
                var(--primary);
        }

        .opcion-item.seleccionada
        .radio-circle::after,
        .opcion-item.seleccionada
        .checkbox-square::after {
            content: '✓';

            color: #fff;

            font-size: 12px;

            font-weight: 900;
        }

        .opcion-text {
            flex: 1;

            min-width: 0;

            color:
                inherit;

            font-size: .75rem;

            line-height: 1.4;

            font-weight: 800;

            overflow-wrap: anywhere;
        }


        /* =====================================================
           RESULTADOS
        ===================================================== */

        .resultado-info {
            display: flex;
            align-items: center;
            justify-content: space-between;

            gap: 10px;
        }

        .resultado-text {
            flex: 1;

            min-width: 0;

            color:
                var(--text-secondary);

            font-size: .74rem;

            line-height: 1.4;

            font-weight: 800;

            overflow-wrap: anywhere;
        }

        .resultado-text.my-choice {
            color:
                var(--success-dark);

            font-weight: 900;
        }

        .resultado-text i {
            margin:
                0 4px 0 0 !important;
        }

        .resultado-porcentaje {
            flex-shrink: 0;

            color:
                var(--primary-dark);

            font-size: .72rem;

            font-weight: 900;

            white-space: nowrap;
        }

        .resultado-bar {
            height: 7px;

            overflow: hidden;

            margin-top: 8px;

            border-radius: 999px;

            background:
                var(--surface-muted);
        }

        .resultado-bar-fill {
            height: 100%;

            border-radius: 999px;

            background:
                linear-gradient(
                    90deg,
                    var(--primary),
                    var(--primary-dark)
                );
        }


        /* =====================================================
           PARTICIPANTES
        ===================================================== */

        .participantes-section {
            margin-top: 10px;

            padding: 9px;

            border:
                1px solid #edf0f4;

            border-radius: 10px;

            background:
                var(--surface-soft);
        }

        .participantes-title {
            display: flex;
            align-items: center;

            gap: 5px;

            margin-bottom: 6px;

            color:
                var(--muted);

            font-size: .62rem;

            font-weight: 900;
        }

        .participantes-title i {
            margin: 0 !important;

            color:
                var(--primary);
        }

        .participantes-list {
            display: flex;
            flex-wrap: wrap;

            gap: 5px;
        }

        .participante-chip {
            display: inline-flex;
            align-items: center;

            gap: 4px;

            padding: 5px 7px;

            border:
                1px solid var(--border);

            border-radius: 999px;

            color:
                var(--muted);

            background: #fff;

            font-size: .59rem;

            font-weight: 800;
        }

        .participante-chip i {
            margin: 0 !important;

            color:
                var(--primary);
        }


        /* =====================================================
           FOOTER
        ===================================================== */

        .encuesta-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;

            gap: 10px;

            padding: 12px 17px;

            border-top:
                1px solid #eef2f7;

            background:
                var(--surface-soft);
        }

        .votos-info {
            display: flex;
            align-items: center;

            gap: 6px;

            color:
                var(--muted);

            font-size: .68rem;

            font-weight: 850;
        }

        .votos-info i {
            margin: 0 !important;

            color:
                var(--primary);
        }

        .btn-votar {
            min-height: 39px;

            display: inline-flex;
            align-items: center;
            justify-content: center;

            gap: 6px;

            padding:
                8px 14px;

            border: 0;

            border-radius: 10px;

            color: #fff;

            background:
                linear-gradient(
                    135deg,
                    var(--primary),
                    var(--primary-dark)
                );

            cursor: pointer;

            font-size: .68rem;

            font-weight: 900;

            white-space: nowrap;

            transition:
                opacity .16s ease,
                transform .16s ease;
        }

        .btn-votar i {
            margin: 0 !important;
        }

        .btn-votar:hover {
            opacity: .94;
        }

        .btn-votar:active {
            transform:
                scale(.98);
        }

        .btn-votar:disabled {
            opacity: .62;

            cursor: not-allowed;
        }

        .ya-votado-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;

            gap: 5px;

            padding: 8px 10px;

            border-radius: 10px;

            color: #166534;

            background: #dcfce7;

            font-size: .65rem;

            font-weight: 900;

            white-space: nowrap;
        }

        .ya-votado-badge i {
            margin: 0 !important;
        }


        /* =====================================================
           HISTORIAL - PARTICIPACIÓN
        ===================================================== */

        .participacion-card {
            display: grid;

            grid-template-columns:
                minmax(0,1fr) auto;

            align-items: center;

            gap: 13px;

            padding: 12px;

            margin-bottom: 12px;

            border:
                1px solid var(--border);

            border-radius: 13px;

            background:
                linear-gradient(
                    135deg,
                    #fafaff,
                    #f8fafc
                );
        }

        .participacion-num {
            color:
                var(--primary-dark);

            font-size: 1.65rem;

            font-weight: 950;

            line-height: 1;
        }

        .participacion-label {
            margin-top: 3px;

            color:
                var(--muted);

            font-size: .65rem;

            font-weight: 750;
        }

        .chart-container {
            width: 72px;
            height: 72px;
        }


        /* =====================================================
           ORDENAMIENTO
        ===================================================== */

        .orden-hint {
            display: flex;
            align-items: flex-start;

            gap: 7px;

            margin-bottom: 9px;

            padding: 8px 9px;

            border:
                1px solid #ede9fe;

            border-radius: 10px;

            color:
                var(--muted);

            background:
                #faf9ff;

            font-size: .65rem;

            line-height: 1.4;
        }

        .orden-hint i {
            margin:
                1px 0 0 !important;

            color:
                var(--primary);
        }

        .orden-list {
            display: flex;

            flex-direction: column;

            gap: 7px;
        }

        .orden-item {
            display: grid;

            grid-template-columns:
                auto minmax(0,1fr) auto;

            align-items: center;

            gap: 9px;

            min-height: 48px;

            padding: 9px 10px;

            border:
                1px solid var(--border);

            border-radius: 11px;

            background: #fff;

            cursor: grab;

            transition:
                border-color .16s ease,
                background .16s ease;
        }

        .orden-item:active {
            cursor: grabbing;
        }

        .orden-item.sortable-chosen {
            border-color:
                var(--primary);

            background:
                var(--primary-soft);
        }

        .orden-item.sortable-ghost {
            opacity: .4;
        }

        .orden-pos {
            width: 29px;
            height: 29px;

            flex-shrink: 0;

            display: flex;
            align-items: center;
            justify-content: center;

            border-radius: 9px;

            color:
                var(--primary-dark);

            background:
                var(--primary-soft);

            font-size: .68rem;

            font-weight: 950;
        }

        .orden-nombre {
            min-width: 0;

            color:
                var(--text);

            font-size: .73rem;

            line-height: 1.35;

            font-weight: 800;
        }

        .orden-actions {
            display: flex;

            gap: 3px;

            align-items: center;
        }

        .orden-move-btn {
            width: 30px;
            height: 30px;

            display: inline-flex;
            align-items: center;
            justify-content: center;

            padding: 0;

            border:
                1px solid var(--border);

            border-radius: 8px;

            color:
                var(--muted);

            background: #fff;

            cursor: pointer;
        }

        .orden-move-btn i {
            margin: 0 !important;

            font-size: .65rem;
        }

        .orden-move-btn:disabled {
            opacity: .3;

            cursor: not-allowed;
        }

        .orden-handle {
            margin:
                0 0 0 4px !important;

            color:
                var(--soft);

            cursor: grab;
        }


        /* =====================================================
           RANKING
        ===================================================== */

        .ranking-summary {
            margin-top: 3px;
        }

        .ranking-title {
            display: flex;
            align-items: center;

            gap: 6px;

            margin-bottom: 2px;

            color:
                var(--text);

            font-size: .75rem;

            font-weight: 900;
        }

        .ranking-title i {
            margin: 0 !important;

            color:
                var(--primary);
        }

        .ranking-subtitle {
            margin:
                0 0 12px;

            color:
                var(--muted);

            font-size: .64rem;
        }

        .rank-fila {
            margin-bottom: 11px;
        }

        .rank-head {
            display: grid;

            grid-template-columns:
                auto minmax(0,1fr) auto;

            align-items: center;

            gap: 8px;

            margin-bottom: 5px;
        }

        .rank-pos {
            width: 25px;
            height: 25px;

            display: inline-flex;
            align-items: center;
            justify-content: center;

            border-radius: 8px;

            color:
                var(--text);

            background:
                var(--surface-muted);

            font-size: .64rem;

            font-weight: 900;
        }

        .rank-pos.first {
            color: #fff;

            background:
                var(--primary);
        }

        .rank-name {
            min-width: 0;

            color:
                var(--text);

            font-size: .7rem;

            line-height: 1.3;

            font-weight: 850;
        }

        .rank-points {
            color:
                var(--muted);

            font-size: .63rem;

            font-weight: 900;

            white-space: nowrap;
        }

        .rank-bar {
            height: 6px;

            overflow: hidden;

            border-radius: 999px;

            background:
                var(--surface-muted);
        }

        .rank-bar-fill {
            height: 100%;

            border-radius: 999px;

            background:
                linear-gradient(
                    90deg,
                    var(--primary),
                    var(--primary-dark)
                );
        }


        /* =====================================================
           MI ORDEN
        ===================================================== */

        .mi-orden-box {
            margin-top: 13px;

            padding: 10px;

            border:
                1px dashed #c4b5fd;

            border-radius: 11px;

            background:
                #faf9ff;
        }

        .mi-orden-title {
            display: flex;
            align-items: center;

            gap: 5px;

            margin-bottom: 7px;

            color:
                var(--primary-dark);

            font-size: .67rem;

            font-weight: 900;
        }

        .mi-orden-title i {
            margin: 0 !important;
        }

        .mi-orden-items {
            display: flex;
            flex-wrap: wrap;

            gap: 5px;
        }

        .mi-orden-chip {
            display: inline-flex;
            align-items: center;

            gap: 5px;

            padding: 5px 7px;

            border-radius: 8px;

            color:
                var(--muted);

            background: #fff;

            font-size: .61rem;
        }

        .mi-orden-chip strong {
            color:
                var(--primary-dark);
        }


        /* =====================================================
           MATRIZ
        ===================================================== */

        .matrix-section {
            margin-top: 17px;
        }

        .matrix-scroll {
            width: 100%;

            overflow-x: auto;

            padding-bottom: 3px;

            -webkit-overflow-scrolling: touch;
        }

        .rank-matriz {
            width: 100%;

            min-width: 390px;

            border-collapse: separate;

            border-spacing: 4px;

            font-size: .62rem;
        }

        .rank-matriz th {
            padding: 4px;

            color:
                var(--muted);

            font-weight: 900;
        }

        .rank-matriz td {
            font-weight: 800;
        }


        /* =====================================================
           EMPTY
        ===================================================== */

        .empty-encuestas-card {
            padding: 38px 18px;

            border:
                1px dashed #d8dee8;

            border-radius: 18px;

            background:
                rgba(255,255,255,.8);

            text-align: center;
        }

        .empty-encuestas-icon {
            width: 55px;
            height: 55px;

            display: flex;
            align-items: center;
            justify-content: center;

            margin:
                0 auto 10px;

            border-radius: 16px;

            color:
                var(--primary);

            background:
                var(--primary-soft);
        }

        .empty-encuestas-icon i {
            margin: 0 !important;

            font-size: 1.25rem;
        }

        .empty-encuestas-card h3 {
            margin:
                0 0 4px;

            color:
                var(--text);

            font-size: .9rem;

            font-weight: 900;
        }

        .empty-encuestas-card p {
            margin: 0;

            color:
                var(--muted);

            font-size: .7rem;
        }


        /* =====================================================
           BUTTON LOADER
        ===================================================== */

        .loader-btn {
            width: 14px;
            height: 14px;

            display: inline-block;

            border:
                2px solid rgba(255,255,255,.35);

            border-top-color: #fff;

            border-radius: 999px;

            animation:
                surveySpin .7s linear infinite;
        }


        /* =====================================================
           RESPONSIVE
        ===================================================== */

        @media (max-width: 900px) {

            .encuestas-grid {
                grid-template-columns: 1fr;
            }

        }


        @media (max-width: 700px) {

            .encuestas-page {
                padding-bottom: 22px;
            }

            .encuestas-hero {
                flex-direction: column;

                align-items: stretch;

                gap: 13px;

                padding: 18px;

                margin-bottom: 11px;

                border-radius: 20px;
            }

            .encuestas-hero-icon {
                width: 46px;
                height: 46px;

                border-radius: 13px;
            }

            .encuestas-title {
                font-size: 1.28rem;
            }

            .encuestas-subtitle {
                font-size: .74rem;
            }

            .encuestas-hero-status {
                width: 100%;

                justify-content: center;

                border-radius: 11px;
            }

            .encuestas-tabs {
                margin-bottom: 13px;

                border-radius: 14px;
            }

            .encuesta-tab {
                min-height: 42px;

                padding: 7px 9px;

                font-size: .7rem;
            }

            .survey-section-header {
                margin-bottom: 9px;
            }

            .encuestas-grid {
                gap: 9px;
            }

            .encuesta-card {
                border-radius: 15px;
            }

            .encuesta-header {
                padding: 14px;
            }

            .encuesta-header-row {
                display: block;
            }

            .encuesta-header h3 {
                font-size: .9rem;
            }

            .tipo-badge,
            .estado-badge {
                margin-top: 8px;
            }

            .encuesta-body {
                padding: 14px;
            }

            .descripcion-texto {
                font-size: .74rem;
            }

            .opcion-item {
                /*
                 * Buen touch target.
                 */
                min-height: 48px;

                padding: 11px;
            }

            .opcion-text {
                font-size: .74rem;
            }

            .encuesta-footer {
                display: grid;

                grid-template-columns: 1fr;

                padding: 12px 14px;
            }

            .votos-info {
                justify-content: center;
            }

            .btn-votar,
            .ya-votado-badge {
                width: 100%;

                min-height: 42px;
            }

            .participacion-card {
                grid-template-columns:
                    minmax(0,1fr) 62px;
            }

            .chart-container {
                width: 62px;
                height: 62px;
            }

            .participacion-num {
                font-size: 1.35rem;
            }

            /*
             * Ordenamiento:
             * se mantienen flechas táctiles.
             */
            .orden-item {
                grid-template-columns:
                    auto minmax(0,1fr) auto;
            }

            /*
             * La matriz puede deslizarse horizontalmente.
             */
            .matrix-scroll {
                position: relative;
            }

        }


        @media (max-width: 390px) {

            .encuesta-tabs {
                gap: 4px;
            }

            .encuesta-tab {
                font-size: .65rem;
            }

            .resultado-info {
                align-items: flex-start;
            }

            .orden-actions {
                flex-direction: column;
            }

        }


        @media (prefers-reduced-motion: reduce) {

            *,
            *::before,
            *::after {
                animation-duration:
                    .01ms !important;

                animation-iteration-count:
                    1 !important;

                transition-duration:
                    .01ms !important;
            }

        }

    </style>


    <div class="encuestas-page">

        <div class="encuestas-container">


            {{-- =================================================
                 HERO
            ================================================== --}}

            <section class="encuestas-hero">


                <div class="encuestas-hero-left">

                    <div class="encuestas-hero-icon">

                        <i class="chart pie icon"></i>

                    </div>


                    <div>

                        <h1 class="encuestas-title">
                            Encuestas
                        </h1>

                        <p class="encuestas-subtitle">
                            Participa en las decisiones del condominio
                            y consulta los resultados de la comunidad.
                        </p>

                    </div>

                </div>


                <div class="encuestas-hero-status">

                    <i class="users icon"></i>

                    Participación comunitaria

                </div>

            </section>


            {{-- =================================================
                 TABS
            ================================================== --}}

            <div
                class="encuestas-tabs"
                role="tablist"
                aria-label="Encuestas"
            >

                <button
                    class="encuesta-tab tab-activo"
                    data-tab="activas"
                    type="button"
                    role="tab"
                    aria-selected="true"
                >

                    <i class="poll horizontal icon"></i>

                    Activas

                    <span
                        class="tab-count"
                        id="count-activas"
                        style="display:none;"
                    >
                        0
                    </span>

                </button>


                <button
                    class="encuesta-tab"
                    data-tab="historial"
                    type="button"
                    role="tab"
                    aria-selected="false"
                >

                    <i class="history icon"></i>

                    Historial

                    <span
                        class="tab-count"
                        id="count-historial"
                        style="display:none;"
                    >
                        0
                    </span>

                </button>

            </div>


            {{-- =================================================
                 ACTIVAS
            ================================================== --}}

            <section id="tab-activas">


                <div class="survey-section-header">

                    <div>

                        <h2 class="survey-section-title">
                            Participa ahora
                        </h2>

                        <div class="survey-section-description">
                            Selecciona tu respuesta y registra tu voto.
                        </div>

                    </div>


                    <div
                        class="survey-section-count"
                        id="label-count-activas"
                    ></div>

                </div>


                <div
                    class="survey-loading"
                    id="loading-activas"
                >

                    <div class="survey-loader"></div>

                    <div class="survey-loading-title">
                        Cargando encuestas
                    </div>

                    <div class="survey-loading-text">
                        Estamos consultando las encuestas disponibles.
                    </div>

                </div>


                <div
                    class="empty-encuestas-card"
                    id="alerta-activas"
                    style="display:none;"
                >

                    <div class="empty-encuestas-icon">

                        <i class="poll horizontal icon"></i>

                    </div>

                    <h3>
                        Sin encuestas activas
                    </h3>

                    <p>
                        No hay encuestas disponibles
                        para responder en este momento.
                    </p>

                </div>


                <div
                    id="encuestas-activas"
                    class="encuestas-grid"
                ></div>

            </section>


            {{-- =================================================
                 HISTORIAL
            ================================================== --}}

            <section
                id="tab-historial"
                style="display:none;"
            >


                <div class="survey-section-header">

                    <div>

                        <h2 class="survey-section-title">
                            Resultados anteriores
                        </h2>

                        <div class="survey-section-description">
                            Consulta cómo participó la comunidad
                            y los resultados finales.
                        </div>

                    </div>


                    <div
                        class="survey-section-count"
                        id="label-count-historial"
                    ></div>

                </div>


                <div
                    class="survey-loading"
                    id="loading-historial"
                >

                    <div class="survey-loader"></div>

                    <div class="survey-loading-title">
                        Cargando historial
                    </div>

                    <div class="survey-loading-text">
                        Consultando resultados anteriores.
                    </div>

                </div>


                <div
                    class="empty-encuestas-card"
                    id="alerta-historico"
                    style="display:none;"
                >

                    <div class="empty-encuestas-icon">

                        <i class="history icon"></i>

                    </div>

                    <h3>
                        Sin historial
                    </h3>

                    <p>
                        Todavía no hay encuestas
                        cerradas para mostrar.
                    </p>

                </div>


                <div
                    id="encuestas-historicas"
                    class="encuestas-grid"
                ></div>

            </section>

        </div>

    </div>


    {{-- =========================================================
         LIBRERÍAS EXISTENTES
    ========================================================== --}}

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>


    <script>

        /*
         * ======================================================
         * ESTADO
         * ======================================================
         */

        let chartInstances =
            {};


        let encuestaActivaCargada =
            false;


        let historialCargado =
            false;


        let peticionVotoActiva =
            {};


        /*
         * ======================================================
         * INIT
         * ======================================================
         */

        $(document).ready(
            function () {


                /*
                 * ==============================================
                 * TABS
                 * ==============================================
                 */

                $('.encuesta-tab')
                    .on(
                        'click',
                        function () {

                            const tab =
                                $(this)
                                    .data(
                                        'tab'
                                    );


                            $('.encuesta-tab')
                                .removeClass(
                                    'tab-activo'
                                )
                                .attr(
                                    'aria-selected',
                                    'false'
                                );


                            $(this)
                                .addClass(
                                    'tab-activo'
                                )
                                .attr(
                                    'aria-selected',
                                    'true'
                                );


                            $('#tab-activas, #tab-historial')
                                .hide();


                            $('#tab-' + tab)
                                .show();


                            if (
                                tab === 'activas'
                            ) {

                                /*
                                 * Recargamos porque una encuesta
                                 * puede haber cambiado desde que
                                 * el usuario abrió la página.
                                 */
                                cargarEncuestasActivas();

                            } else {

                                cargarHistorial();

                            }

                        }
                    );


                cargarEncuestasActivas();

            }
        );


        /*
         * ======================================================
         * CARGAR ACTIVAS
         * ======================================================
         */

        function cargarEncuestasActivas() {

            const $loading =
                $('#loading-activas');


            const $container =
                $('#encuestas-activas');


            const $empty =
                $('#alerta-activas');


            $loading.addClass(
                'show'
            );


            $empty.hide();


            $.get(
                '{{ route("usuario.encuesta.obtener") }}',

                function (response) {

                    if (
                        !response.success
                    ) {

                        alertify.error(
                            response.message
                            ||
                            'No se pudieron cargar las encuestas.'
                        );

                        return;
                    }


                    const encuestas =
                        response.encuestas
                        ||
                        [];


                    actualizarContador(
                        'activas',
                        encuestas.length
                    );


                    if (
                        encuestas.length
                        ===
                        0
                    ) {

                        $container.html(
                            ''
                        );


                        $empty.show();

                    } else {

                        $empty.hide();


                        renderizarEncuestas(
                            encuestas,
                            '#encuestas-activas',
                            true
                        );

                    }


                    encuestaActivaCargada =
                        true;

                }
            )
            .fail(
                function () {

                    alertify.error(
                        'No se pudieron cargar las encuestas activas.'
                    );

                }
            )
            .always(
                function () {

                    $loading.removeClass(
                        'show'
                    );

                }
            );

        }


        /*
         * ======================================================
         * CARGAR HISTORIAL
         * ======================================================
         */

        function cargarHistorial() {

            const $loading =
                $('#loading-historial');


            const $container =
                $('#encuestas-historicas');


            const $empty =
                $('#alerta-historico');


            $loading.addClass(
                'show'
            );


            $empty.hide();


            $.get(
                '{{ route("usuario.encuesta.historial") }}',

                function (response) {

                    if (
                        !response.success
                    ) {

                        alertify.error(
                            response.message
                            ||
                            'No se pudo cargar el historial.'
                        );

                        return;
                    }


                    const encuestas =
                        response.encuestas
                        ||
                        [];


                    actualizarContador(
                        'historial',
                        encuestas.length
                    );


                    if (
                        encuestas.length
                        ===
                        0
                    ) {

                        $container.html(
                            ''
                        );


                        $empty.show();

                    } else {

                        $empty.hide();


                        renderizarHistorial(
                            encuestas,
                            '#encuestas-historicas'
                        );

                    }


                    historialCargado =
                        true;

                }
            )
            .fail(
                function () {

                    alertify.error(
                        'No se pudo cargar el historial.'
                    );

                }
            )
            .always(
                function () {

                    $loading.removeClass(
                        'show'
                    );

                }
            );

        }


        /*
         * ======================================================
         * CONTADORES
         * ======================================================
         */

        function actualizarContador(
            tipo,
            cantidad
        ) {

            const $badge =
                $('#count-' + tipo);


            const $label =
                $('#label-count-' + tipo);


            $badge
                .text(
                    cantidad
                )
                .toggle(
                    cantidad > 0
                );


            if (
                cantidad === 0
            ) {

                $label.text(
                    ''
                );

                return;
            }


            $label.text(
                cantidad
                +
                ' '
                +
                (
                    cantidad === 1
                        ? 'encuesta'
                        : 'encuestas'
                )
            );

        }


        /*
         * ======================================================
         * RENDER ACTIVAS
         * ======================================================
         */

        function renderizarEncuestas(
            encuestas,
            container,
            puedeVotar
        ) {

            let html =
                '';


            encuestas.forEach(
                function (encuesta) {


                    const tipoTexto =
                        encuesta.tipo_usuario
                        ===
                        'dueño'
                            ? 'Dueños'
                            :
                            encuesta.tipo_usuario
                            ===
                            'inquilino'
                                ? 'Inquilinos'
                                : 'Todos';


                    if (
                        encuesta.tipo
                        ===
                        'ordenamiento'
                    ) {

                        html +=
                            cardOrdenamientoActiva(
                                encuesta,
                                tipoTexto,
                                puedeVotar
                            );


                        return;

                    }


                    const instruccion =
                        encuesta.multiple
                            ?
                            `
                                <div class="vote-instruction">
                                    <i class="check square outline icon"></i>

                                    <span>
                                        Puedes seleccionar más de una opción.
                                    </span>
                                </div>
                            `
                            :
                            `
                                <div class="vote-instruction">
                                    <i class="dot circle outline icon"></i>

                                    <span>
                                        Selecciona una opción para registrar tu voto.
                                    </span>
                                </div>
                            `;


                    html += `
                        <article class="encuesta-card">

                            <header class="encuesta-header">

                                <div class="encuesta-header-row">

                                    <div class="encuesta-header-main">

                                        <h3>
                                            ${escapeHtml(encuesta.titulo)}
                                        </h3>

                                        <div class="encuesta-type-description">
                                            ${encuesta.multiple
                                                ? 'Selección múltiple'
                                                : 'Una sola respuesta'}
                                        </div>

                                    </div>


                                    <span class="tipo-badge">

                                        <i class="users icon"></i>

                                        ${escapeHtml(tipoTexto)}

                                    </span>

                                </div>

                            </header>


                            <div class="encuesta-body">

                                ${
                                    encuesta.descripcion
                                        ?
                                        `
                                            <p class="descripcion-texto">
                                                ${escapeHtml(encuesta.descripcion)}
                                            </p>
                                        `
                                        :
                                        ''
                                }


                                ${
                                    encuesta.fecha_cierre
                                        ?
                                        `
                                            <div class="fecha-info">

                                                <i class="clock outline icon"></i>

                                                <span>
                                                    Disponible hasta
                                                    <strong>
                                                        ${escapeHtml(encuesta.fecha_cierre)}
                                                    </strong>
                                                </span>

                                            </div>
                                        `
                                        :
                                        ''
                                }


                                ${
                                    puedeVotar
                                    &&
                                    !encuesta.ya_voto
                                        ?
                                        instruccion
                                        :
                                        ''
                                }


                                <div
                                    class="opciones-list"
                                    data-encuesta-id="${encuesta.id}"
                                    data-multiple="${encuesta.multiple}"
                                >

                                    ${
                                        encuesta.opciones
                                            .map(
                                                function (opcion) {

                                                    const misRespuestas =
                                                        encuesta.mis_respuestas
                                                        ||
                                                        [];


                                                    const isSelected =
                                                        misRespuestas.includes(
                                                            opcion.id
                                                        );


                                                    const inputType =
                                                        encuesta.multiple
                                                            ? 'checkbox'
                                                            : 'radio';


                                                    const checkboxClass =
                                                        inputType
                                                        ===
                                                        'checkbox'
                                                            ? 'checkbox-square'
                                                            : 'radio-circle';


                                                    /*
                                                     * ENCUESTA AÚN VOTABLE
                                                     */
                                                    if (
                                                        puedeVotar
                                                        &&
                                                        !encuesta.ya_voto
                                                    ) {

                                                        return `
                                                            <button
                                                                type="button"
                                                                class="opcion-item"
                                                                data-opcion="${opcion.id}"
                                                                onclick="toggleOpcion(
                                                                    ${encuesta.id},
                                                                    ${opcion.id},
                                                                    ${encuesta.multiple},
                                                                    this
                                                                )"
                                                                style="width:100%; text-align:left;"
                                                            >

                                                                <span class="${checkboxClass}"></span>

                                                                <span class="opcion-text">
                                                                    ${escapeHtml(opcion.opcion)}
                                                                </span>

                                                            </button>
                                                        `;

                                                    }


                                                    /*
                                                     * RESULTADOS DESPUÉS DE VOTAR.
                                                     */
                                                    const porVotos =
                                                        encuesta.total_votos
                                                        >
                                                        0
                                                            ?
                                                            Math.round(
                                                                (
                                                                    opcion.votos
                                                                    /
                                                                    encuesta.total_votos
                                                                )
                                                                *
                                                                100
                                                            )
                                                            :
                                                            0;


                                                    return `
                                                        <div class="opcion-item votada">

                                                            <div class="resultado-info">

                                                                <span
                                                                    class="
                                                                        resultado-text
                                                                        ${isSelected ? 'my-choice' : ''}
                                                                    "
                                                                >

                                                                    ${
                                                                        isSelected
                                                                            ?
                                                                            '<i class="check circle icon"></i>'
                                                                            :
                                                                            ''
                                                                    }

                                                                    ${escapeHtml(opcion.opcion)}

                                                                </span>


                                                                <span class="resultado-porcentaje">
                                                                    ${porVotos}%
                                                                </span>

                                                            </div>


                                                            <div class="resultado-bar">

                                                                <div
                                                                    class="resultado-bar-fill"
                                                                    style="width:${clampPercent(porVotos)}%;"
                                                                ></div>

                                                            </div>

                                                        </div>
                                                    `;

                                                }
                                            )
                                            .join('')
                                    }

                                </div>

                            </div>


                            <footer class="encuesta-footer">

                                <div class="votos-info">

                                    <i class="users icon"></i>

                                    <span>
                                        ${encuesta.total_votos}
                                        voto${encuesta.total_votos !== 1 ? 's' : ''}
                                    </span>

                                </div>


                                ${
                                    encuesta.ya_voto
                                        ?
                                        `
                                            <div class="ya-votado-badge">

                                                <i class="check circle icon"></i>

                                                Ya participaste

                                            </div>
                                        `
                                        :
                                        puedeVotar
                                            ?
                                            `
                                                <button
                                                    class="btn-votar"
                                                    id="btn-votar-${encuesta.id}"
                                                    onclick="votar(${encuesta.id})"
                                                    type="button"
                                                >

                                                    <i class="send icon"></i>

                                                    Registrar voto

                                                </button>
                                            `
                                            :
                                            ''
                                }

                            </footer>

                        </article>
                    `;

                }
            );


            $(container)
                .html(
                    html
                );


            initSortables();

        }


        /*
         * ======================================================
         * ORDENAMIENTO ACTIVO
         * ======================================================
         */

        function cardOrdenamientoActiva(
            encuesta,
            tipoTexto,
            puedeVotar
        ) {

            const mis =
                encuesta.mis_respuestas
                ||
                [];


            const opcionesPorId =
                {};


            encuesta.opciones
                .forEach(
                    function (opcion) {

                        opcionesPorId[
                            opcion.id
                        ] =
                            opcion;

                    }
                );


            let lista =
                encuesta.opciones
                    .slice();


            if (
                mis.length
            ) {

                lista =
                    mis.map(
                        function (id) {

                            return opcionesPorId[
                                id
                            ];

                        }
                    )
                    .filter(
                        Boolean
                    );

            }


            const bloqueado =
                !puedeVotar;


            const items =
                lista.map(
                    function (
                        opcion,
                        index
                    ) {

                        return `
                            <div
                                class="orden-item"
                                data-opcion="${opcion.id}"
                            >

                                <span class="orden-pos">
                                    ${index + 1}
                                </span>


                                <span class="orden-nombre">
                                    ${escapeHtml(opcion.opcion)}
                                </span>


                                ${
                                    bloqueado
                                        ?
                                        ''
                                        :
                                        `
                                            <div class="orden-actions">

                                                <button
                                                    type="button"
                                                    class="orden-move-btn"
                                                    onclick="moverOrden(
                                                        ${encuesta.id},
                                                        this,
                                                        -1
                                                    )"
                                                    aria-label="Subir prioridad"
                                                >
                                                    <i class="chevron up icon"></i>
                                                </button>


                                                <button
                                                    type="button"
                                                    class="orden-move-btn"
                                                    onclick="moverOrden(
                                                        ${encuesta.id},
                                                        this,
                                                        1
                                                    )"
                                                    aria-label="Bajar prioridad"
                                                >
                                                    <i class="chevron down icon"></i>
                                                </button>


                                                <i
                                                    class="
                                                        grip lines
                                                        icon
                                                        orden-handle
                                                    "
                                                    title="Arrastrar"
                                                ></i>

                                            </div>
                                        `
                                }

                            </div>
                        `;

                    }
                )
                .join('');


            return `
                <article class="encuesta-card">

                    <header class="encuesta-header">

                        <div class="encuesta-header-row">

                            <div class="encuesta-header-main">

                                <h3>
                                    ${escapeHtml(encuesta.titulo)}
                                </h3>

                                <div class="encuesta-type-description">
                                    Ordena tus prioridades
                                </div>

                            </div>


                            <span class="tipo-badge">

                                <i class="sort amount down icon"></i>

                                Ordenamiento

                            </span>

                        </div>

                    </header>


                    <div class="encuesta-body">

                        ${
                            encuesta.descripcion
                                ?
                                `
                                    <p class="descripcion-texto">
                                        ${escapeHtml(encuesta.descripcion)}
                                    </p>
                                `
                                :
                                ''
                        }


                        ${
                            encuesta.fecha_cierre
                                ?
                                `
                                    <div class="fecha-info">

                                        <i class="clock outline icon"></i>

                                        <span>
                                            Disponible hasta
                                            <strong>
                                                ${escapeHtml(encuesta.fecha_cierre)}
                                            </strong>
                                        </span>

                                    </div>
                                `
                                :
                                ''
                        }


                        <div class="orden-hint">

                            <i class="hand pointer outline icon"></i>

                            <span>

                                ${
                                    bloqueado
                                        ?
                                        'Este es el orden que registraste.'
                                        :
                                        'Coloca primero lo más importante. Puedes arrastrar o utilizar las flechas.'
                                }

                            </span>

                        </div>


                        <div
                            class="orden-list"
                            id="orden-list-${encuesta.id}"
                            data-encuesta-id="${encuesta.id}"
                            data-bloqueado="${bloqueado ? 1 : 0}"
                        >

                            ${items}

                        </div>

                    </div>


                    <footer class="encuesta-footer">

                        <div class="votos-info">

                            <i class="users icon"></i>

                            <span>
                                ${encuesta.total_votos}
                                votante${encuesta.total_votos !== 1 ? 's' : ''}
                            </span>

                        </div>


                        ${
                            encuesta.ya_voto
                                ?
                                `
                                    <button
                                        class="btn-votar"
                                        id="btn-orden-${encuesta.id}"
                                        onclick="guardarOrden(${encuesta.id})"
                                        type="button"
                                    >

                                        <i class="redo icon"></i>

                                        Actualizar orden

                                    </button>
                                `
                                :
                                puedeVotar
                                    ?
                                    `
                                        <button
                                            class="btn-votar"
                                            id="btn-orden-${encuesta.id}"
                                            onclick="guardarOrden(${encuesta.id})"
                                            type="button"
                                        >

                                            <i class="save outline icon"></i>

                                            Guardar mi orden

                                        </button>
                                    `
                                    :
                                    ''
                        }

                    </footer>

                </article>
            `;

        }


        /*
         * ======================================================
         * SORTABLE
         * ======================================================
         */

        function initSortables() {

            document
                .querySelectorAll(
                    '.orden-list'
                )
                .forEach(
                    function (element) {

                        if (
                            element.dataset.bloqueado
                            ===
                            '1'
                            ||
                            element._sortable
                        ) {
                            return;
                        }


                        element._sortable =
                            Sortable.create(
                                element,
                                {

                                    animation:
                                        150,

                                    handle:
                                        '.orden-handle',

                                    onEnd:
                                        function () {

                                            renumerarOrden(
                                                element
                                            );


                                            actualizarBotonesOrden(
                                                element
                                            );

                                        }

                                }
                            );


                        actualizarBotonesOrden(
                            element
                        );

                    }
                );

        }


        /*
         * ======================================================
         * BOTONES ↑ ↓
         * ======================================================
         */

        window.moverOrden =
            function (
                encuestaId,
                boton,
                direccion
            ) {

                const item =
                    boton.closest(
                        '.orden-item'
                    );


                const lista =
                    document.getElementById(
                        'orden-list-'
                        +
                        encuestaId
                    );


                if (
                    !item
                    ||
                    !lista
                ) {
                    return;
                }


                if (
                    direccion
                    <
                    0
                ) {

                    const anterior =
                        item.previousElementSibling;


                    if (anterior) {

                        lista.insertBefore(
                            item,
                            anterior
                        );

                    }

                } else {

                    const siguiente =
                        item.nextElementSibling;


                    if (siguiente) {

                        lista.insertBefore(
                            siguiente,
                            item
                        );

                    }

                }


                renumerarOrden(
                    lista
                );


                actualizarBotonesOrden(
                    lista
                );

            };


        function renumerarOrden(
            element
        ) {

            element
                .querySelectorAll(
                    '.orden-item'
                )
                .forEach(
                    function (
                        item,
                        index
                    ) {

                        const pos =
                            item.querySelector(
                                '.orden-pos'
                            );


                        if (pos) {

                            pos.textContent =
                                index + 1;

                        }

                    }
                );

        }


        function actualizarBotonesOrden(
            lista
        ) {

            const items =
                Array.from(
                    lista.querySelectorAll(
                        '.orden-item'
                    )
                );


            items.forEach(
                function (
                    item,
                    index
                ) {

                    const buttons =
                        item.querySelectorAll(
                            '.orden-move-btn'
                        );


                    if (
                        buttons.length
                        <
                        2
                    ) {
                        return;
                    }


                    buttons[0].disabled =
                        index === 0;


                    buttons[1].disabled =
                        index
                        ===
                        items.length - 1;

                }
            );

        }


        /*
         * ======================================================
         * GUARDAR ORDEN
         * ======================================================
         */

        window.guardarOrden =
            function (
                encuestaId
            ) {

                if (
                    peticionVotoActiva[
                        encuestaId
                    ]
                ) {
                    return;
                }


                const element =
                    document.getElementById(
                        'orden-list-'
                        +
                        encuestaId
                    );


                if (!element) {
                    return;
                }


                const orden =
                    Array.from(
                        element.querySelectorAll(
                            '.orden-item'
                        )
                    )
                    .map(
                        function (item) {

                            return parseInt(
                                item.getAttribute(
                                    'data-opcion'
                                ),
                                10
                            );

                        }
                    )
                    .filter(
                        Number.isFinite
                    );


                if (
                    orden.length
                    ===
                    0
                ) {

                    alertify.error(
                        'No hay opciones para guardar.'
                    );

                    return;
                }


                const btn =
                    $(
                        '#btn-orden-'
                        +
                        encuestaId
                    );


                const original =
                    btn.html();


                peticionVotoActiva[
                    encuestaId
                ] =
                    true;


                btn
                    .html(
                        '<span class="loader-btn"></span> Guardando...'
                    )
                    .prop(
                        'disabled',
                        true
                    );


                $.ajax({

                    url:
                        '{{ route("usuario.encuesta.votar") }}',

                    type:
                        'POST',

                    data: {

                        _token:
                            '{{ csrf_token() }}',

                        encuesta_id:
                            encuestaId,

                        orden:
                            orden

                    },


                    success:
                        function (response) {

                            if (
                                response.success
                            ) {

                                alertify.success(
                                    response.message
                                    ||
                                    'Tu orden fue guardado.'
                                );


                                cargarEncuestasActivas();

                            } else {

                                alertify.error(
                                    response.message
                                    ||
                                    'No se pudo guardar tu orden.'
                                );


                                btn
                                    .html(
                                        original
                                    )
                                    .prop(
                                        'disabled',
                                        false
                                    );

                            }

                        },


                    error:
                        function (xhr) {

                            const mensaje =
                                xhr.responseJSON
                                    ?.message
                                ||
                                'Error al guardar tu orden';


                            alertify.error(
                                mensaje
                            );


                            btn
                                .html(
                                    original
                                )
                                .prop(
                                    'disabled',
                                    false
                                );


                            if (
                                xhr.status
                                ===
                                422
                            ) {

                                cargarEncuestasActivas();

                            }

                        },


                    complete:
                        function () {

                            peticionVotoActiva[
                                encuestaId
                            ] =
                                false;

                        }

                });

            };


        /*
         * ======================================================
         * HISTORIAL
         * ======================================================
         */

        function renderizarHistorial(
            encuestas,
            container
        ) {

            let html =
                '';


            /*
             * Destruir gráficos anteriores.
             */
            Object.keys(
                chartInstances
            )
                .forEach(
                    function (key) {

                        if (
                            chartInstances[
                                key
                            ]
                        ) {

                            chartInstances[
                                key
                            ]
                                .destroy();

                        }

                    }
                );


            chartInstances =
                {};


            encuestas.forEach(
                function (encuesta) {


                    const estaVencida =
                        encuesta.estado
                        ===
                        'vencida';


                    const estadoClass =
                        estaVencida
                            ? 'vencida'
                            : 'cerrada';


                    const estadoTexto =
                        estaVencida
                            ? 'Vencida'
                            : 'Cerrada';


                    const estadoIcon =
                        estaVencida
                            ? 'clock outline'
                            : 'lock';


                    if (
                        encuesta.tipo
                        ===
                        'ordenamiento'
                    ) {

                        html +=
                            cardOrdenamientoHistorial(
                                encuesta,
                                estadoClass,
                                estadoTexto,
                                estadoIcon
                            );


                        return;

                    }


                    html += `
                        <article class="encuesta-card">

                            <header class="encuesta-header">

                                <div class="encuesta-header-row">

                                    <div class="encuesta-header-main">

                                        <h3>
                                            ${escapeHtml(encuesta.titulo)}
                                        </h3>

                                        <div class="encuesta-type-description">
                                            Resultados de la comunidad
                                        </div>

                                    </div>


                                    <span class="estado-badge ${estadoClass}">

                                        <i class="${estadoIcon} icon"></i>

                                        ${estadoTexto}

                                    </span>

                                </div>

                            </header>


                            <div class="encuesta-body">


                                ${
                                    encuesta.descripcion
                                        ?
                                        `
                                            <p class="descripcion-texto">
                                                ${escapeHtml(encuesta.descripcion)}
                                            </p>
                                        `
                                        :
                                        ''
                                }


                                ${
                                    encuesta.fecha_cierre
                                        ?
                                        `
                                            <div class="fecha-info">

                                                <i class="calendar outline icon"></i>

                                                <span>
                                                    Fecha límite:
                                                    <strong>
                                                        ${escapeHtml(encuesta.fecha_cierre)}
                                                    </strong>
                                                </span>

                                            </div>
                                        `
                                        :
                                        ''
                                }


                                <div class="participacion-card">

                                    <div>

                                        <div class="participacion-num">
                                            ${encuesta.total_votos}
                                        </div>

                                        <div class="participacion-label">

                                            voto${encuesta.total_votos !== 1 ? 's' : ''}
                                            registrado${encuesta.total_votos !== 1 ? 's' : ''}

                                        </div>

                                    </div>


                                    <div class="chart-container">

                                        <canvas
                                            id="chart-${encuesta.id}"
                                        ></canvas>

                                    </div>

                                </div>


                                <div class="opciones-list">

                                    ${
                                        encuesta.opciones
                                            .map(
                                                function (opcion) {

                                                    const misRespuestas =
                                                        encuesta.mis_respuestas
                                                        ||
                                                        [];


                                                    const porcentaje =
                                                        encuesta.total_votos
                                                        >
                                                        0
                                                            ?
                                                            Math.round(
                                                                (
                                                                    opcion.votos
                                                                    /
                                                                    encuesta.total_votos
                                                                )
                                                                *
                                                                100
                                                            )
                                                            :
                                                            0;


                                                    const isSelected =
                                                        misRespuestas.includes(
                                                            opcion.id
                                                        );


                                                    let participantesHtml =
                                                        '';


                                                    if (
                                                        opcion.participantes
                                                        &&
                                                        opcion.participantes.length
                                                        >
                                                        0
                                                    ) {

                                                        participantesHtml = `
                                                            <div class="participantes-section">

                                                                <div class="participantes-title">

                                                                    <i class="users icon"></i>

                                                                    Participantes
                                                                    (${opcion.participantes.length})

                                                                </div>


                                                                <div class="participantes-list">

                                                                    ${
                                                                        opcion.participantes
                                                                            .map(
                                                                                function (persona) {

                                                                                    return `
                                                                                        <span class="participante-chip">

                                                                                            <i class="user icon"></i>

                                                                                            ${escapeHtml(persona.nombre)}
                                                                                            #${escapeHtml(persona.casa)}

                                                                                        </span>
                                                                                    `;

                                                                                }
                                                                            )
                                                                            .join('')
                                                                    }

                                                                </div>

                                                            </div>
                                                        `;

                                                    }


                                                    return `
                                                        <div class="opcion-item votada">

                                                            <div class="resultado-info">

                                                                <span
                                                                    class="
                                                                        resultado-text
                                                                        ${isSelected ? 'my-choice' : ''}
                                                                    "
                                                                >

                                                                    ${
                                                                        isSelected
                                                                            ?
                                                                            '<i class="check circle icon"></i>'
                                                                            :
                                                                            ''
                                                                    }

                                                                    ${escapeHtml(opcion.opcion)}

                                                                </span>


                                                                <span class="resultado-porcentaje">

                                                                    ${opcion.votos}
                                                                    ·
                                                                    ${porcentaje}%

                                                                </span>

                                                            </div>


                                                            <div class="resultado-bar">

                                                                <div
                                                                    class="resultado-bar-fill"
                                                                    style="
                                                                        width:
                                                                        ${clampPercent(porcentaje)}%;
                                                                    "
                                                                ></div>

                                                            </div>


                                                            ${participantesHtml}

                                                        </div>
                                                    `;

                                                }
                                            )
                                            .join('')
                                    }

                                </div>

                            </div>

                        </article>
                    `;

                }
            );


            $(container)
                .html(
                    html
                );


            /*
             * Crear Charts después de insertar HTML.
             */
            setTimeout(
                function () {

                    encuestas.forEach(
                        function (encuesta) {

                            /*
                             * Ordenamiento no usa doughnut.
                             */
                            if (
                                encuesta.tipo
                                ===
                                'ordenamiento'
                            ) {
                                return;
                            }


                            const ctx =
                                document.getElementById(
                                    'chart-'
                                    +
                                    encuesta.id
                                );


                            if (
                                !ctx
                                ||
                                chartInstances[
                                    'chart-'
                                    +
                                    encuesta.id
                                ]
                            ) {
                                return;
                            }


                            const data =
                                encuesta.opciones
                                    .map(
                                        function (opcion) {

                                            return Number(
                                                opcion.votos
                                                ||
                                                0
                                            );

                                        }
                                    );


                            const colors = [
                                'rgba(139,92,246,.95)',
                                'rgba(124,58,237,.82)',
                                'rgba(109,40,217,.72)',
                                'rgba(167,139,250,.82)',
                                'rgba(196,181,253,.85)',
                                'rgba(221,214,254,.90)'
                            ];


                            chartInstances[
                                'chart-'
                                +
                                encuesta.id
                            ] =
                                new Chart(
                                    ctx,
                                    {

                                        type:
                                            'doughnut',

                                        data: {

                                            labels:
                                                encuesta.opciones
                                                    .map(
                                                        function (opcion) {

                                                            return opcion.opcion;

                                                        }
                                                    ),

                                            datasets: [
                                                {

                                                    data:
                                                        data,

                                                    backgroundColor:
                                                        data.map(
                                                            function (
                                                                item,
                                                                index
                                                            ) {

                                                                return colors[
                                                                    index
                                                                    %
                                                                    colors.length
                                                                ];

                                                            }
                                                        ),

                                                    borderWidth:
                                                        0

                                                }
                                            ]

                                        },


                                        options: {

                                            responsive:
                                                true,

                                            maintainAspectRatio:
                                                true,

                                            cutout:
                                                '68%',

                                            plugins: {

                                                legend: {
                                                    display: false
                                                },

                                                tooltip: {

                                                    callbacks: {

                                                        label:
                                                            function (context) {

                                                                const value =
                                                                    context.parsed
                                                                    ||
                                                                    0;


                                                                return (
                                                                    context.label
                                                                    +
                                                                    ': '
                                                                    +
                                                                    value
                                                                    +
                                                                    ' voto'
                                                                    +
                                                                    (
                                                                        value
                                                                        !==
                                                                        1
                                                                            ? 's'
                                                                            : ''
                                                                    )
                                                                );

                                                            }

                                                    }

                                                }

                                            }

                                        }

                                    }
                                );

                        }
                    );

                },
                50
            );

        }


        /*
         * ======================================================
         * HISTORIAL ORDENAMIENTO
         * ======================================================
         */

        function cardOrdenamientoHistorial(
            encuesta,
            estadoClass,
            estadoTexto,
            estadoIcon
        ) {

            const resultado =
                encuesta.resultado_orden
                ||
                {
                    ranking: [],
                    num_opciones: 0,
                    total_votos: 0
                };


            const opcionesPorId =
                {};


            (
                encuesta.opciones
                ||
                []
            )
                .forEach(
                    function (opcion) {

                        opcionesPorId[
                            opcion.id
                        ] =
                            opcion.opcion;

                    }
                );


            const miOrden =
                (
                    encuesta.mis_respuestas
                    ||
                    []
                )
                    .map(
                        function (id) {

                            return opcionesPorId[
                                id
                            ];

                        }
                    )
                    .filter(
                        Boolean
                    );


            let miOrdenHtml =
                '';


            if (
                miOrden.length
            ) {

                miOrdenHtml = `
                    <div class="mi-orden-box">

                        <div class="mi-orden-title">

                            <i class="user outline icon"></i>

                            Tu orden registrado

                        </div>


                        <div class="mi-orden-items">

                            ${
                                miOrden
                                    .map(
                                        function (
                                            opcion,
                                            index
                                        ) {

                                            return `
                                                <span class="mi-orden-chip">

                                                    <strong>
                                                        ${index + 1}.
                                                    </strong>

                                                    ${escapeHtml(opcion)}

                                                </span>
                                            `;

                                        }
                                    )
                                    .join('')
                            }

                        </div>

                    </div>
                `;

            }


            return `
                <article class="encuesta-card">

                    <header class="encuesta-header">

                        <div class="encuesta-header-row">

                            <div class="encuesta-header-main">

                                <h3>
                                    ${escapeHtml(encuesta.titulo)}
                                </h3>

                                <div class="encuesta-type-description">
                                    Resultado de prioridades
                                </div>

                            </div>


                            <span class="estado-badge ${estadoClass}">

                                <i class="${estadoIcon} icon"></i>

                                ${estadoTexto}

                            </span>

                        </div>

                    </header>


                    <div class="encuesta-body">

                        ${
                            encuesta.descripcion
                                ?
                                `
                                    <p class="descripcion-texto">
                                        ${escapeHtml(encuesta.descripcion)}
                                    </p>
                                `
                                :
                                ''
                        }


                        ${
                            encuesta.fecha_cierre
                                ?
                                `
                                    <div class="fecha-info">

                                        <i class="calendar outline icon"></i>

                                        <span>
                                            Fecha límite:
                                            <strong>
                                                ${escapeHtml(encuesta.fecha_cierre)}
                                            </strong>
                                        </span>

                                    </div>
                                `
                                :
                                ''
                        }


                        ${renderRankingUsuario(resultado)}

                        ${miOrdenHtml}

                    </div>

                </article>
            `;

        }


        /*
         * ======================================================
         * RANKING + MATRIZ
         * ======================================================
         */

        function renderRankingUsuario(
            resultado
        ) {

            const votantes =
                Number(
                    resultado.total_votos
                    ||
                    0
                );


            const cantidadOpciones =
                Number(
                    resultado.num_opciones
                    ||
                    (
                        resultado.ranking
                            ?
                            resultado.ranking.length
                            :
                            0
                    )
                );


            const ranking =
                resultado.ranking
                ||
                [];


            let html = `
                <div class="ranking-summary">

                    <div class="ranking-title">

                        <i class="trophy icon"></i>

                        Orden ganador

                    </div>

                    <p class="ranking-subtitle">

                        ${votantes}
                        votante${votantes !== 1 ? 's' : ''}
                        · resultado calculado por puntos de prioridad

                    </p>
            `;


            if (
                ranking.length
                ===
                0
            ) {

                html += `
                    <div class="fecha-info">

                        <i class="info circle icon"></i>

                        <span>
                            No hay resultados suficientes para generar
                            un ranking.
                        </span>

                    </div>
                `;

            }


            ranking.forEach(
                function (
                    fila,
                    index
                ) {

                    const porcentaje =
                        clampPercent(
                            Number(
                                fila.pct
                                ||
                                0
                            )
                        );


                    html += `
                        <div class="rank-fila">

                            <div class="rank-head">

                                <span
                                    class="
                                        rank-pos
                                        ${index === 0 ? 'first' : ''}
                                    "
                                >
                                    ${index + 1}
                                </span>


                                <span class="rank-name">
                                    ${escapeHtml(fila.opcion)}
                                </span>


                                <span class="rank-points">
                                    ${escapeHtml(fila.puntos)} pts
                                </span>

                            </div>


                            <div class="rank-bar">

                                <div
                                    class="rank-bar-fill"
                                    style="width:${porcentaje}%;"
                                ></div>

                            </div>

                        </div>
                    `;

                }
            );


            if (
                ranking.length
                >
                0
                &&
                cantidadOpciones
                >
                0
            ) {

                html += `
                    <div class="matrix-section">

                        <div class="ranking-title">

                            <i class="th icon"></i>

                            Distribución de posiciones

                        </div>


                        <p class="ranking-subtitle">

                            Muestra cuántos vecinos colocaron
                            cada opción en cada posición.

                            En móvil puedes deslizar la tabla.

                        </p>


                        <div class="matrix-scroll">

                            <table class="rank-matriz">

                                <thead>

                                    <tr>

                                        <th style="text-align:left;">
                                            Opción
                                        </th>
                `;


                for (
                    let position = 1;
                    position <= cantidadOpciones;
                    position++
                ) {

                    html += `
                        <th>
                            #${position}
                        </th>
                    `;

                }


                html += `
                                    </tr>

                                </thead>

                                <tbody>
                `;


                ranking.forEach(
                    function (fila) {

                        html += `
                            <tr>

                                <td
                                    style="
                                        text-align:left;
                                        padding-right:7px;
                                    "
                                >
                                    ${escapeHtml(fila.opcion)}
                                </td>
                        `;


                        for (
                            let position = 1;
                            position <= cantidadOpciones;
                            position++
                        ) {

                            const cantidad =
                                Number(
                                    (
                                        fila.conteo_pos
                                        &&
                                        fila.conteo_pos[
                                            position
                                        ]
                                    )
                                    ||
                                    0
                                );


                            const alpha =
                                votantes
                                >
                                0
                                    ?
                                    cantidad
                                    /
                                    votantes
                                    :
                                    0;


                            const intensidad =
                                Math.min(
                                    .88,
                                    Math.max(
                                        .05,
                                        alpha
                                        *
                                        .85
                                    )
                                );


                            const background =
                                'rgba(139,92,246,'
                                +
                                intensidad.toFixed(
                                    2
                                )
                                +
                                ')';


                            const color =
                                alpha
                                >
                                .45
                                    ?
                                    '#ffffff'
                                    :
                                    '#0f172a';


                            html += `
                                <td
                                    style="
                                        text-align:center;
                                        padding:7px 4px;
                                        border-radius:6px;
                                        background:${background};
                                        color:${color};
                                    "
                                >
                                    ${cantidad}
                                </td>
                            `;

                        }


                        html += `
                            </tr>
                        `;

                    }
                );


                html += `
                                </tbody>

                            </table>

                        </div>

                    </div>
                `;

            }


            html += `
                </div>
            `;


            return html;

        }


        /*
         * ======================================================
         * SELECCIÓN DE OPCIÓN
         * ======================================================
         */

        window.toggleOpcion =
            function (
                encuestaId,
                opcionId,
                esMultiple,
                element
            ) {

                const container =
                    $(
                        '.opciones-list[data-encuesta-id="'
                        +
                        encuestaId
                        +
                        '"]'
                    );


                const multiple =
                    esMultiple
                    ===
                    true
                    ||
                    esMultiple
                    ===
                    'true'
                    ||
                    esMultiple
                    ===
                    1
                    ||
                    esMultiple
                    ===
                    '1';


                if (
                    multiple
                ) {

                    $(element)
                        .toggleClass(
                            'seleccionada'
                        );

                } else {

                    container
                        .find(
                            '.opcion-item'
                        )
                        .removeClass(
                            'seleccionada'
                        );


                    $(element)
                        .addClass(
                            'seleccionada'
                        );

                }

            };


        /*
         * ======================================================
         * VOTAR
         * ======================================================
         */

        window.votar =
            function (
                encuestaId
            ) {

                /*
                 * Evita doble clic/petición.
                 */
                if (
                    peticionVotoActiva[
                        encuestaId
                    ]
                ) {
                    return;
                }


                const container =
                    $(
                        '.opciones-list[data-encuesta-id="'
                        +
                        encuestaId
                        +
                        '"]'
                    );


                const multipleRaw =
                    container.data(
                        'multiple'
                    );


                const esMultiple =
                    multipleRaw === true
                    ||
                    multipleRaw === 'true'
                    ||
                    multipleRaw === 1
                    ||
                    multipleRaw === '1';


                let opcionesSeleccionadas =
                    [];


                if (
                    esMultiple
                ) {

                    opcionesSeleccionadas =
                        container
                            .find(
                                '.opcion-item.seleccionada'
                            )
                            .map(
                                function () {

                                    return parseInt(
                                        $(this)
                                            .data(
                                                'opcion'
                                            ),
                                        10
                                    );

                                }
                            )
                            .get();

                } else {

                    const selected =
                        container
                            .find(
                                '.opcion-item.seleccionada'
                            );


                    if (
                        selected.length
                    ) {

                        opcionesSeleccionadas =
                            [
                                parseInt(
                                    selected
                                        .data(
                                            'opcion'
                                        ),
                                    10
                                )
                            ];

                    }

                }


                opcionesSeleccionadas =
                    opcionesSeleccionadas
                        .filter(
                            Number.isFinite
                        );


                if (
                    opcionesSeleccionadas.length
                    ===
                    0
                ) {

                    alertify.error(
                        esMultiple
                            ?
                            'Selecciona al menos una opción.'
                            :
                            'Selecciona una opción antes de votar.'
                    );


                    return;

                }


                const btn =
                    $(
                        '#btn-votar-'
                        +
                        encuestaId
                    );


                const original =
                    btn.html();


                peticionVotoActiva[
                    encuestaId
                ] =
                    true;


                btn
                    .html(
                        '<span class="loader-btn"></span> Enviando...'
                    )
                    .prop(
                        'disabled',
                        true
                    );


                $.ajax({

                    url:
                        '{{ route("usuario.encuesta.votar") }}',

                    type:
                        'POST',

                    data: {

                        _token:
                            '{{ csrf_token() }}',

                        encuesta_id:
                            encuestaId,

                        /*
                         * Conservamos ambos valores porque
                         * así trabaja tu endpoint actual.
                         */
                        opciones:
                            opcionesSeleccionadas,

                        opcion_id:
                            opcionesSeleccionadas[
                                0
                            ]

                    },


                    success:
                        function (response) {

                            if (
                                response.success
                            ) {

                                alertify.success(
                                    response.message
                                    ||
                                    'Tu voto fue registrado.'
                                );


                                cargarEncuestasActivas();

                            } else {

                                alertify.error(
                                    response.message
                                    ||
                                    'No se pudo registrar el voto.'
                                );


                                btn
                                    .html(
                                        original
                                    )
                                    .prop(
                                        'disabled',
                                        false
                                    );

                            }

                        },


                    error:
                        function (xhr) {

                            const mensaje =
                                xhr.responseJSON
                                    ?.message
                                ||
                                'Error al registrar tu voto';


                            alertify.error(
                                mensaje
                            );


                            btn
                                .html(
                                    original
                                )
                                .prop(
                                    'disabled',
                                    false
                                );


                            if (
                                xhr.status
                                ===
                                422
                            ) {

                                /*
                                 * El estado del backend puede
                                 * haber cambiado.
                                 */
                                cargarEncuestasActivas();

                            }

                        },


                    complete:
                        function () {

                            peticionVotoActiva[
                                encuestaId
                            ] =
                                false;

                        }

                });

            };


        /*
         * ======================================================
         * HELPERS
         * ======================================================
         */

        function clampPercent(
            value
        ) {

            const number =
                Number(
                    value
                );


            if (
                !Number.isFinite(
                    number
                )
            ) {
                return 0;
            }


            return Math.max(
                0,
                Math.min(
                    100,
                    number
                )
            );

        }


        function escapeHtml(
            text
        ) {

            return String(
                text
                ??
                ''
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
                )
                .replace(
                    /'/g,
                    '&#039;'
                );

        }

    </script>

</x-app-layout>