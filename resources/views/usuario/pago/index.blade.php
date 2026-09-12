<x-app-layout>

    <style>

        :root {
            --pay-primary: #2563eb;
            --pay-primary-dark: #1d4ed8;
            --pay-secondary: #7c3aed;

            --pay-warning: #f59e0b;
            --pay-warning-dark: #d97706;

            --pay-danger: #ef4444;
            --pay-danger-dark: #dc2626;

            --pay-success: #10b981;
            --pay-success-dark: #059669;

            --pay-info: #0ea5e9;

            --pay-text: #0f172a;
            --pay-muted: #64748b;
            --pay-soft: #94a3b8;

            --pay-bg: #f5f8ff;
            --pay-surface: #ffffff;
            --pay-surface-soft: #f8fafc;

            --pay-border:
                rgba(148, 163, 184, 0.22);

            --pay-shadow-sm:
                0 6px 20px rgba(15, 23, 42, 0.06);

            --pay-shadow-md:
                0 16px 38px rgba(15, 23, 42, 0.11);
        }


        * {
            box-sizing: border-box;
        }


        .pagos-page {
            min-height:
                calc(100vh - 80px);

            padding:
                8px 0 36px;
        }


        .pagos-container {
            width: 100%;

            max-width: 1220px;

            margin: 0 auto;
        }


        /* =====================================================
           HEADER
        ===================================================== */

        .pagos-header {
            position: relative;

            overflow: hidden;

            display: flex;

            align-items: center;

            padding:
                26px 28px;

            margin-bottom:
                16px;

            border-radius:
                24px;

            color: #fff;

            background:
                radial-gradient(
                    circle at top right,
                    rgba(255,255,255,.20),
                    transparent 32%
                ),
                linear-gradient(
                    135deg,
                    var(--pay-primary),
                    var(--pay-secondary)
                );

            box-shadow:
                0 14px 34px rgba(37,99,235,.18);
        }


        .pagos-header::after {
            content: '';

            position: absolute;

            width: 230px;
            height: 230px;

            right: -100px;
            bottom: -140px;

            border-radius: 999px;

            background:
                rgba(255,255,255,.10);
        }


        .pagos-header-content {
            position: relative;

            z-index: 2;

            display: flex;

            align-items: center;

            gap: 15px;
        }


        .pagos-header-icon {
            width: 56px;
            height: 56px;

            flex-shrink: 0;

            display: flex;

            align-items: center;

            justify-content: center;

            border-radius: 17px;

            background:
                rgba(255,255,255,.16);

            border:
                1px solid rgba(255,255,255,.18);
        }


        .pagos-header-icon i {
            margin: 0 !important;

            font-size: 1.45rem;
        }


        .pagos-header h1 {
            margin: 0;

            font-size:
                clamp(1.45rem,3vw,2rem);

            font-weight: 900;

            line-height: 1.1;

            letter-spacing:
                -.035em;
        }


        .pagos-header p {
            max-width: 680px;

            margin:
                5px 0 0;

            opacity: .9;

            font-size: .87rem;

            line-height: 1.5;
        }


        /* =====================================================
           ALERTAS
        ===================================================== */

        .pay-alert {
            display: flex;

            align-items: flex-start;

            gap: 9px;

            padding:
                12px 14px;

            margin-bottom:
                14px;

            border-radius:
                13px;

            font-size: .83rem;

            font-weight: 700;
        }


        .pay-alert i {
            margin:
                2px 0 0 !important;
        }


        .pay-alert.success {
            color: #065f46;

            background: #ecfdf5;

            border:
                1px solid #a7f3d0;
        }


        .pay-alert.error {
            color: #991b1b;

            background: #fef2f2;

            border:
                1px solid #fecaca;
        }


        /* =====================================================
           AYUDA PARA COMPRIMIR
        ===================================================== */

        .compress-box {
            position: relative;

            overflow: hidden;

            display: grid;

            grid-template-columns:
                auto minmax(0,1fr) auto;

            align-items: center;

            gap: 12px;

            padding:
                13px 14px;

            margin-bottom:
                17px;

            border:
                1px solid #bfdbfe;

            border-radius:
                16px;

            background:
                linear-gradient(
                    135deg,
                    #eff6ff,
                    #f5f3ff
                );

            box-shadow:
                var(--pay-shadow-sm);
        }


        .compress-icon {
            width: 42px;
            height: 42px;

            display: flex;

            align-items: center;

            justify-content: center;

            flex-shrink: 0;

            border-radius:
                13px;

            color: #fff;

            background:
                linear-gradient(
                    135deg,
                    var(--pay-primary),
                    var(--pay-secondary)
                );
        }


        .compress-icon i {
            margin: 0 !important;
        }


        .compress-title {
            color:
                var(--pay-text);

            font-size: .82rem;

            font-weight: 900;

            margin-bottom: 2px;
        }


        .compress-text {
            color:
                var(--pay-muted);

            font-size: .74rem;

            line-height: 1.4;
        }


        .compress-btn {
            min-height: 38px;

            display: inline-flex;

            align-items: center;

            justify-content: center;

            gap: 6px;

            padding:
                8px 12px;

            border-radius:
                11px;

            color: #fff !important;

            background:
                linear-gradient(
                    135deg,
                    var(--pay-primary),
                    var(--pay-secondary)
                );

            box-shadow:
                0 8px 18px rgba(37,99,235,.18);

            font-size: .72rem;

            font-weight: 900;

            white-space: nowrap;

            text-decoration:
                none !important;
        }


        .compress-btn i {
            margin: 0 !important;
        }


        /* =====================================================
           STATS
        ===================================================== */

        .stats-grid {
            display: grid;

            grid-template-columns:
                repeat(4,1fr);

            gap: 10px;

            margin-bottom:
                16px;
        }


        .stat-card {
            display: flex;

            align-items: center;

            gap: 11px;

            min-height: 84px;

            padding: 13px;

            border:
                1px solid var(--pay-border);

            border-radius:
                16px;

            background:
                var(--pay-surface);

            box-shadow:
                var(--pay-shadow-sm);
        }


        .stat-icon {
            width: 41px;
            height: 41px;

            flex-shrink: 0;

            display: flex;

            align-items: center;

            justify-content: center;

            border-radius:
                12px;

            color: #fff;
        }


        .stat-icon i {
            margin: 0 !important;
        }


        .stat-card.pendiente .stat-icon {
            background:
                linear-gradient(
                    135deg,
                    var(--pay-warning),
                    var(--pay-warning-dark)
                );
        }


        .stat-card.vencido .stat-icon {
            background:
                linear-gradient(
                    135deg,
                    var(--pay-danger),
                    var(--pay-danger-dark)
                );
        }


        .stat-card.aprobado .stat-icon {
            background:
                linear-gradient(
                    135deg,
                    var(--pay-success),
                    var(--pay-success-dark)
                );
        }


        .stat-card.rechazado .stat-icon {
            background:
                linear-gradient(
                    135deg,
                    #64748b,
                    #475569
                );
        }


        .stat-value {
            color:
                var(--pay-text);

            font-size:
                1.4rem;

            font-weight: 950;

            line-height: 1;
        }


        .stat-label {
            margin-top: 4px;

            color:
                var(--pay-muted);

            font-size: .64rem;

            font-weight: 900;

            text-transform:
                uppercase;

            letter-spacing:
                .05em;
        }


        /* =====================================================
           TABS
        ===================================================== */

        .tabs-wrapper {
            position: sticky;

            top: 0;

            z-index: 20;

            padding: 7px 0;

            margin-bottom:
                14px;

            background:
                rgba(245,248,255,.94);

            backdrop-filter:
                blur(12px);

            -webkit-backdrop-filter:
                blur(12px);
        }


        .tabs-shell {
            position: relative;

            overflow: hidden;

            border:
                1px solid var(--pay-border);

            border-radius:
                15px;

            background:
                rgba(255,255,255,.96);

            box-shadow:
                var(--pay-shadow-sm);
        }


        .tabs-container {
            display: flex;

            gap: 6px;

            overflow-x: auto;

            padding: 6px;

            scrollbar-width: none;

            -webkit-overflow-scrolling:
                touch;

            scroll-snap-type:
                x proximity;
        }


        .tabs-container::-webkit-scrollbar {
            display: none;
        }


        .tab-btn {
            position: relative;

            z-index: 2;

            flex: 0 0 auto;

            min-height: 40px;

            display: inline-flex;

            align-items: center;

            justify-content: center;

            gap: 6px;

            padding:
                8px 13px;

            border-radius:
                11px;

            color:
                var(--pay-muted);

            font-size: .77rem;

            font-weight: 900;

            white-space: nowrap;

            scroll-snap-align:
                start;

            text-decoration:
                none !important;
        }


        .tab-btn i {
            margin: 0 !important;
        }


        .tab-btn:hover {
            color:
                var(--pay-text);

            background:
                var(--pay-surface-soft);
        }


        .tab-btn.active {
            color: #fff;

            background:
                linear-gradient(
                    135deg,
                    var(--pay-primary),
                    var(--pay-secondary)
                );

            box-shadow:
                0 8px 18px rgba(37,99,235,.18);
        }


        .tab-count {
            min-width: 21px;

            height: 21px;

            display: inline-flex;

            align-items: center;

            justify-content: center;

            padding: 0 6px;

            border-radius:
                999px;

            font-size: .64rem;

            background:
                rgba(255,255,255,.22);
        }


        .tab-btn:not(.active)
        .tab-count {
            color:
                var(--pay-primary);

            background:
                #eff6ff;
        }


        /*
         * Indicadores de scroll.
         *
         * Solo aparecen cuando realmente
         * existe contenido oculto.
         */
        .tabs-scroll-hint {
            position: absolute;

            top: 0;
            bottom: 0;

            width: 48px;

            z-index: 8;

            pointer-events: none;

            opacity: 0;

            transition:
                opacity .2s ease;
        }


        .tabs-scroll-hint.left {
            left: 0;

            background:
                linear-gradient(
                    to right,
                    #fff 42%,
                    rgba(255,255,255,0)
                );
        }


        .tabs-scroll-hint.right {
            right: 0;

            background:
                linear-gradient(
                    to left,
                    #fff 42%,
                    rgba(255,255,255,0)
                );
        }


        .tabs-scroll-hint i {
            position: absolute;

            top: 50%;

            transform:
                translateY(-50%);

            margin: 0 !important;

            color:
                var(--pay-primary);

            font-size:
                .95rem;

            animation:
                swipeHint 1.3s ease-in-out infinite;
        }


        .tabs-scroll-hint.left i {
            left: 8px;
        }


        .tabs-scroll-hint.right i {
            right: 8px;
        }


        @keyframes swipeHint {

            0%,
            100% {
                transform:
                    translateY(-50%)
                    translateX(0);
            }

            50% {
                transform:
                    translateY(-50%)
                    translateX(4px);
            }

        }


        .mobile-swipe-text {
            display: none;

            align-items: center;

            justify-content: center;

            gap: 5px;

            margin:
                -5px 0 10px;

            color:
                var(--pay-soft);

            font-size:
                .64rem;

            font-weight: 700;
        }


        .mobile-swipe-text i {
            margin: 0 !important;
        }


        /* =====================================================
           LISTA
        ===================================================== */

        .list-heading {
            display: flex;

            align-items: flex-start;

            justify-content:
                space-between;

            gap: 15px;

            margin-bottom:
                11px;
        }


        .list-title {
            margin: 0;

            color:
                var(--pay-text);

            font-size:
                1rem;

            font-weight: 900;
        }


        .list-subtitle {
            margin-top: 3px;

            color:
                var(--pay-muted);

            font-size: .74rem;

            line-height: 1.4;
        }


        .list-page-info {
            color:
                var(--pay-soft);

            font-size: .7rem;

            white-space: nowrap;
        }


        .pagos-list {
            display: grid;

            grid-template-columns:
                repeat(
                    3,
                    minmax(0,1fr)
                );

            gap: 12px;
        }


        .pago-card {
            position: relative;

            overflow: hidden;

            display: flex;

            flex-direction: column;

            padding: 15px;

            border:
                1px solid var(--pay-border);

            border-radius:
                17px;

            background:
                var(--pay-surface);

            box-shadow:
                var(--pay-shadow-sm);

            transition:
                transform .18s ease,
                box-shadow .18s ease;
        }


        .pago-card::before {
            content: '';

            position: absolute;

            top: 0;
            bottom: 0;
            left: 0;

            width: 4px;

            background:
                var(--pay-primary);
        }


        .pago-card.warning::before {
            background:
                var(--pay-warning);
        }


        .pago-card.review::before {
            background:
                var(--pay-info);
        }


        .pago-card.danger::before {
            background:
                var(--pay-danger);
        }


        .pago-card.success::before {
            background:
                var(--pay-success);
        }


        .pago-card:hover {
            transform:
                translateY(-2px);

            box-shadow:
                var(--pay-shadow-md);
        }


        .pago-top {
            display: flex;

            align-items: flex-start;

            justify-content:
                space-between;

            gap: 11px;

            margin-bottom: 11px;
        }


        .pago-main {
            min-width: 0;
        }


        .pago-concept {
            color:
                var(--pay-text);

            font-size: .88rem;

            font-weight: 900;

            line-height: 1.25;

            word-break:
                break-word;
        }


        .pago-date {
            display: flex;

            align-items: center;

            gap: 5px;

            margin-top: 5px;

            color:
                var(--pay-muted);

            font-size: .69rem;
        }


        .pago-date i {
            margin: 0 !important;
        }


        .pago-date.danger {
            color:
                var(--pay-danger);
        }


        .pago-amount {
            flex-shrink: 0;

            color:
                var(--pay-text);

            font-size: 1rem;

            font-weight: 950;

            white-space: nowrap;
        }


        .pago-amount.success {
            color:
                var(--pay-success);
        }


        /* =====================================================
           ESTADOS
        ===================================================== */

        .pago-state {
            padding:
                9px 0;

            margin-bottom:
                11px;

            border-top:
                1px solid #eef2f7;

            border-bottom:
                1px solid #eef2f7;
        }


        .status-badge {
            display: inline-flex;

            align-items: center;

            gap: 6px;

            padding:
                6px 9px;

            border-radius:
                999px;

            font-size: .67rem;

            font-weight: 900;
        }


        .status-badge i {
            margin: 0 !important;
        }


        .status-badge.pending {
            color: #b45309;

            background: #fef3c7;
        }


        .status-badge.review {
            color: #0369a1;

            background: #e0f2fe;
        }


        .status-badge.expired,
        .status-badge.rejected {
            color: #b91c1c;

            background: #fee2e2;
        }


        .status-badge.paid {
            color: #047857;

            background: #d1fae5;
        }


        .status-helper {
            margin-top: 6px;

            color:
                var(--pay-muted);

            font-size: .67rem;

            line-height: 1.4;
        }


        /* =====================================================
           BOTONES
        ===================================================== */

        .pago-actions {
            display: grid;

            grid-template-columns:
                repeat(
                    2,
                    minmax(0,1fr)
                );

            gap: 6px;

            margin-top: auto;
        }


        .pago-actions.one {
            grid-template-columns:
                1fr;
        }


        .pay-btn {
            min-height: 39px;

            display: inline-flex;

            align-items: center;

            justify-content: center;

            gap: 6px;

            padding:
                9px 9px;

            border: 0;

            border-radius:
                10px;

            cursor: pointer;

            font-size: .69rem;

            font-weight: 900;

            text-decoration:
                none !important;
        }


        .pay-btn i {
            margin: 0 !important;
        }


        .pay-btn.primary {
            color: #fff;

            background:
                linear-gradient(
                    135deg,
                    var(--pay-primary),
                    var(--pay-secondary)
                );
        }


        .pay-btn.warning {
            color: #fff;

            background:
                linear-gradient(
                    135deg,
                    var(--pay-warning),
                    var(--pay-warning-dark)
                );
        }


        .pay-btn.success {
            color: #fff;

            background:
                linear-gradient(
                    135deg,
                    var(--pay-success),
                    var(--pay-success-dark)
                );
        }


        .pay-btn.secondary {
            color: #475569;

            background: #f1f5f9;
        }


        .pay-btn.loading {
            opacity: .65;

            pointer-events: none;
        }


        /* =====================================================
           EMPTY
        ===================================================== */

        .empty-state {
            grid-column:
                1 / -1;

            min-height: 220px;

            display: flex;

            align-items: center;

            justify-content: center;

            padding:
                28px 18px;

            border:
                1px dashed #cbd5e1;

            border-radius:
                19px;

            background:
                rgba(255,255,255,.82);

            text-align: center;
        }


        .empty-icon {
            width: 56px;
            height: 56px;

            display: flex;

            align-items: center;

            justify-content: center;

            margin:
                0 auto 11px;

            border-radius:
                16px;

            color:
                var(--pay-soft);

            background:
                #f1f5f9;
        }


        .empty-icon i {
            margin: 0 !important;

            font-size: 1.3rem;
        }


        .empty-state h3 {
            margin:
                0 0 5px;

            color:
                var(--pay-text);

            font-size: .94rem;
        }


        .empty-state p {
            margin: 0;

            color:
                var(--pay-muted);

            font-size: .75rem;
        }


        /* =====================================================
           PAGINACIÓN
        ===================================================== */

        .pagos-pagination {
            grid-column:
                1 / -1;

            margin-top: 4px;

            padding:
                11px 13px;

            border:
                1px solid var(--pay-border);

            border-radius:
                14px;

            background: #fff;
        }


        .pagos-pagination svg {
            width: 18px;

            height: 18px;
        }


        /* =====================================================
           MODAL
        ===================================================== */

        #modal-subir-comprobante.ui.modal,
        #modal-ver-comprobante.ui.modal {
            border-radius:
                20px !important;

            overflow: hidden;
        }


        #modal-subir-comprobante .header,
        #modal-ver-comprobante .header {
            display: flex;

            align-items: center;

            gap: 8px;

            padding:
                17px 20px;

            color: #fff;

            background:
                linear-gradient(
                    135deg,
                    var(--pay-primary),
                    var(--pay-secondary)
                );

            font-weight: 900;
        }


        #modal-subir-comprobante
        .content {
            padding: 18px;
        }


        .modal-info {
            display: flex;

            gap: 9px;

            padding:
                10px 11px;

            margin-bottom:
                13px;

            border:
                1px solid #bfdbfe;

            border-radius:
                11px;

            color: #1e40af;

            background: #eff6ff;

            font-size: .74rem;

            line-height: 1.4;
        }


        .modal-info i {
            margin:
                2px 0 0 !important;
        }


        .modal-compress-link {
            color:
                #1d4ed8 !important;

            font-weight: 900;

            text-decoration:
                underline !important;
        }


        /* =====================================================
           UPLOAD
        ===================================================== */

        .upload-area {
            position: relative;

            min-height: 190px;

            display: flex;

            align-items: center;

            justify-content: center;

            padding: 10px;

            border:
                2px dashed #dbe4ef;

            border-radius:
                16px;

            background: #f8fafc;

            cursor: pointer;
        }


        .upload-area.dragover {
            border-color:
                var(--pay-primary);

            background: #eff6ff;
        }


        .upload-placeholder {
            padding:
                24px 15px;

            text-align: center;
        }


        .upload-icon {
            width: 54px;
            height: 54px;

            display: flex;

            align-items: center;

            justify-content: center;

            margin:
                0 auto 11px;

            border-radius:
                16px;

            color: #fff;

            background:
                linear-gradient(
                    135deg,
                    var(--pay-primary),
                    var(--pay-secondary)
                );
        }


        .upload-icon i {
            margin: 0 !important;
        }


        .upload-primary {
            display: block;

            color:
                var(--pay-text);

            font-size: .81rem;

            font-weight: 900;
        }


        .upload-secondary {
            display: block;

            margin-top: 4px;

            color:
                var(--pay-muted);

            font-size: .71rem;
        }


        .upload-formats {
            display: block;

            margin-top: 8px;

            color:
                var(--pay-soft);

            font-size: .65rem;
        }


        .upload-preview {
            position: relative;

            width: 100%;
        }


        .upload-preview img {
            display: block;

            max-width: 100%;

            max-height: 280px;

            margin: 0 auto;

            border-radius: 13px;

            object-fit: contain;
        }


        .remove-image {
            position: absolute;

            top: 7px;
            right: 7px;

            width: 32px;
            height: 32px;

            display: flex;

            align-items: center;

            justify-content: center;

            border: 0;

            border-radius: 999px;

            color: #fff;

            background:
                var(--pay-danger);

            cursor: pointer;
        }


        .remove-image i {
            margin: 0 !important;
        }


        #imagen-comprobante {
            display: block;

            max-width: 100%;

            max-height: 72vh;

            margin: 0 auto;

            object-fit: contain;
        }


        #modal-ver-comprobante
        .scrolling.content {
            background: #0f172a;
        }


        /* =====================================================
           RESPONSIVE
        ===================================================== */

        @media (max-width: 950px) {

            .pagos-list {
                grid-template-columns:
                    repeat(
                        2,
                        minmax(0,1fr)
                    );
            }


            .stats-grid {
                grid-template-columns:
                    repeat(2,1fr);
            }

        }


        @media (min-width: 768px) {

            /*
             * En escritorio no necesitamos
             * indicar desplazamiento.
             */
            .tabs-scroll-hint {
                display: none !important;
            }

        }


        @media (max-width: 767px) {

            .pagos-page {
                padding:
                    0 0 25px;
            }


            .pagos-header {
                padding: 18px;

                border-radius: 19px;

                margin-bottom: 12px;
            }


            .pagos-header-icon {
                width: 45px;

                height: 45px;

                border-radius: 13px;
            }


            .pagos-header h1 {
                font-size: 1.28rem;
            }


            .pagos-header p {
                font-size: .75rem;
            }


            /*
             * Botón de comprimir SE MANTIENE
             * visible también en móvil.
             */
            .compress-box {
                grid-template-columns:
                    auto minmax(0,1fr);

                padding: 12px;

                gap: 10px;
            }


            .compress-btn {
                grid-column:
                    1 / -1;

                width: 100%;
            }


            .stats-grid {
                gap: 7px;
            }


            .stat-card {
                min-height: 68px;

                padding: 9px;

                gap: 8px;

                border-radius: 13px;
            }


            .stat-icon {
                width: 34px;

                height: 34px;

                border-radius: 10px;
            }


            .stat-value {
                font-size: 1.08rem;
            }


            .stat-label {
                font-size: .56rem;
            }


            .tabs-wrapper {
                /*
                 * Permite que visualmente
                 * llegue casi hasta los bordes
                 * y se entienda que continúa.
                 */
                margin-left: -4px;

                margin-right: -4px;
            }


            .tabs-shell {
                border-radius: 13px;
            }


            .tabs-container {
                /*
                 * Dejamos espacio lateral para
                 * que los hints no tapen texto.
                 */
                padding-left: 8px;

                padding-right: 8px;
            }


            .tab-btn {
                min-height: 37px;

                padding:
                    7px 11px;

                font-size: .7rem;
            }


            /*
             * Texto explícito:
             * "Desliza para ver más"
             */
            .mobile-swipe-text {
                display: flex;
            }


            .list-heading {
                align-items: flex-end;
            }


            .pagos-list {
                grid-template-columns:
                    1fr;

                gap: 8px;
            }


            .pago-card {
                padding: 13px;

                border-radius: 15px;
            }


            .pago-top {
                margin-bottom: 8px;
            }


            #modal-subir-comprobante.ui.modal,
            #modal-ver-comprobante.ui.modal {
                width:
                    calc(100% - 20px) !important;

                margin-left:
                    10px !important;

                margin-right:
                    10px !important;
            }

        }


        @media (max-width: 380px) {

            .pago-actions {
                grid-template-columns: 1fr;
            }

        }


        @media (prefers-reduced-motion: reduce) {

            .tabs-scroll-hint i {
                animation: none !important;
            }

        }

    </style>


    @php

        /*
         * =====================================================
         * CONTADORES
         * =====================================================
         *
         * Se utilizan las colecciones entregadas
         * por tu controller original.
         */

        $pendientesCount =
            $pendientes->count();

        $vencidosCount =
            $vencidos->count();

        $aprobadosCount =
            $aprobados->count();

        $rechazadosCount =
            $rechazados->count();


        $titulos = [
            'pendientes' =>
                'Pagos pendientes',

            'vencidos' =>
                'Pagos vencidos',

            'aprobados' =>
                'Pagos realizados',

            'rechazados' =>
                'Pagos rechazados',
        ];


        $subtitulos = [

            'pendientes' =>
                'Pagos por realizar y comprobantes que están esperando revisión.',

            'vencidos' =>
                'Recibos cuya fecha límite ya pasó y aún no tienen comprobante.',

            'aprobados' =>
                'Pagos que ya fueron revisados y aprobados.',

            'rechazados' =>
                'Comprobantes que necesitan un nuevo envío.',
        ];

    @endphp


    <div class="pagos-page">

        <div class="pagos-container">


            {{-- =================================================
                 HEADER
            ================================================== --}}

            <section class="pagos-header">

                <div class="pagos-header-content">

                    <div class="pagos-header-icon">
                        <i class="dollar sign icon"></i>
                    </div>


                    <div>

                        <h1>
                            Mis pagos
                        </h1>

                        <p>
                            Consulta tus recibos, revisa fechas de vencimiento,
                            envía comprobantes y descarga tus recibos aprobados.
                        </p>

                    </div>

                </div>

            </section>


            {{-- =================================================
                 MENSAJES
            ================================================== --}}

            @if(session('success'))

                <div class="pay-alert success">

                    <i class="check circle icon"></i>

                    <span>
                        {{ session('success') }}
                    </span>

                </div>

            @endif


            @if(session('error'))

                <div class="pay-alert error">

                    <i class="exclamation triangle icon"></i>

                    <span>
                        {{ session('error') }}
                    </span>

                </div>

            @endif


            @if($errors->any())

                <div class="pay-alert error">

                    <i class="exclamation triangle icon"></i>

                    <div>

                        @foreach(
                            $errors->all()
                            as $error
                        )

                            <div>
                                {{ $error }}
                            </div>

                        @endforeach

                    </div>

                </div>

            @endif


            {{-- =================================================
                 COMPRIMIR IMAGEN
            ================================================== --}}

            <div class="compress-box">

                <div class="compress-icon">

                    <i class="compress arrows alternate icon"></i>

                </div>


                <div>

                    <div class="compress-title">
                        ¿Tu comprobante pesa más de 2 MB?
                    </div>

                    <div class="compress-text">
                        Puedes reducir el tamaño de la imagen
                        antes de subirla para evitar errores.
                    </div>

                </div>


                <a
                    href="https://www.iloveimg.com/es/comprimir-imagen"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="compress-btn"
                >

                    <i class="compress icon"></i>

                    Comprimir imagen

                    <i class="external alternate icon"></i>

                </a>

            </div>


            {{-- =================================================
                 RESUMEN
            ================================================== --}}

            <div class="stats-grid">


                <div class="stat-card pendiente">

                    <div class="stat-icon">

                        <i class="clock icon"></i>

                    </div>


                    <div>

                        <div class="stat-value">

                            {{ $pendientesCount }}

                        </div>

                        <div class="stat-label">

                            Pendientes

                        </div>

                    </div>

                </div>


                <div class="stat-card vencido">

                    <div class="stat-icon">

                        <i class="exclamation triangle icon"></i>

                    </div>


                    <div>

                        <div class="stat-value">

                            {{ $vencidosCount }}

                        </div>

                        <div class="stat-label">

                            Vencidos

                        </div>

                    </div>

                </div>


                <div class="stat-card aprobado">

                    <div class="stat-icon">

                        <i class="check icon"></i>

                    </div>


                    <div>

                        <div class="stat-value">

                            {{ $aprobadosCount }}

                        </div>

                        <div class="stat-label">

                            Pagados

                        </div>

                    </div>

                </div>


                <div class="stat-card rechazado">

                    <div class="stat-icon">

                        <i class="times icon"></i>

                    </div>


                    <div>

                        <div class="stat-value">

                            {{ $rechazadosCount }}

                        </div>

                        <div class="stat-label">

                            Rechazados

                        </div>

                    </div>

                </div>

            </div>


            {{-- =================================================
                 TABS
            ================================================== --}}

            <div class="tabs-wrapper">

                <div class="tabs-shell">


                    {{-- INDICADOR IZQUIERDO --}}
                    <div
                        class="tabs-scroll-hint left"
                        id="tabsHintLeft"
                    >

                        <i class="chevron left icon"></i>

                    </div>


                    {{-- INDICADOR DERECHO --}}
                    <div
                        class="tabs-scroll-hint right"
                        id="tabsHintRight"
                    >

                        <i class="chevron right icon"></i>

                    </div>


                    <nav
                        class="tabs-container"
                        id="tabsContainer"
                        aria-label="Estados de pago"
                    >


                        <a
                            href="{{
                                route(
                                    'usuario.pago.index',
                                    [
                                        'tab'
                                        =>
                                        'pendientes'
                                    ]
                                )
                            }}"
                            class="
                                tab-btn
                                {{
                                    $tabActivo
                                    ===
                                    'pendientes'
                                        ? 'active'
                                        : ''
                                }}
                            "
                        >

                            <i class="clock icon"></i>

                            Pendientes


                            @if(
                                $pendientesCount
                                > 0
                            )

                                <span class="tab-count">

                                    {{ $pendientesCount }}

                                </span>

                            @endif

                        </a>


                        <a
                            href="{{
                                route(
                                    'usuario.pago.index',
                                    [
                                        'tab'
                                        =>
                                        'vencidos'
                                    ]
                                )
                            }}"
                            class="
                                tab-btn
                                {{
                                    $tabActivo
                                    ===
                                    'vencidos'
                                        ? 'active'
                                        : ''
                                }}
                            "
                        >

                            <i class="exclamation triangle icon"></i>

                            Vencidos


                            @if(
                                $vencidosCount
                                > 0
                            )

                                <span class="tab-count">

                                    {{ $vencidosCount }}

                                </span>

                            @endif

                        </a>


                        <a
                            href="{{
                                route(
                                    'usuario.pago.index',
                                    [
                                        'tab'
                                        =>
                                        'aprobados'
                                    ]
                                )
                            }}"
                            class="
                                tab-btn
                                {{
                                    $tabActivo
                                    ===
                                    'aprobados'
                                        ? 'active'
                                        : ''
                                }}
                            "
                        >

                            <i class="check circle icon"></i>

                            Pagados


                            @if(
                                $aprobadosCount
                                > 0
                            )

                                <span class="tab-count">

                                    {{ $aprobadosCount }}

                                </span>

                            @endif

                        </a>


                        <a
                            href="{{
                                route(
                                    'usuario.pago.index',
                                    [
                                        'tab'
                                        =>
                                        'rechazados'
                                    ]
                                )
                            }}"
                            class="
                                tab-btn
                                {{
                                    $tabActivo
                                    ===
                                    'rechazados'
                                        ? 'active'
                                        : ''
                                }}
                            "
                        >

                            <i class="times circle icon"></i>

                            Rechazados


                            @if(
                                $rechazadosCount
                                > 0
                            )

                                <span class="tab-count">

                                    {{ $rechazadosCount }}

                                </span>

                            @endif

                        </a>

                    </nav>

                </div>

            </div>


            {{-- MENSAJE SOLO MÓVIL --}}
            <div
                class="mobile-swipe-text"
                id="mobileSwipeText"
            >

                <i class="hand point right outline icon"></i>

                Desliza para consultar otros estados

            </div>


            {{-- =================================================
                 HEADER LISTADO
            ================================================== --}}

            <div class="list-heading">

                <div>

                    <h2 class="list-title">

                        {{
                            $titulos[
                                $tabActivo
                            ]
                            ??
                            'Mis pagos'
                        }}

                    </h2>


                    <div class="list-subtitle">

                        {{
                            $subtitulos[
                                $tabActivo
                            ]
                            ??
                            ''
                        }}

                    </div>

                </div>


                @if(
                    $pagosPaginados
                        ->total()
                    > 0
                )

                    <div class="list-page-info">

                        {{
                            $pagosPaginados
                                ->firstItem()
                        }}

                        -

                        {{
                            $pagosPaginados
                                ->lastItem()
                        }}

                        de

                        {{
                            $pagosPaginados
                                ->total()
                        }}

                    </div>

                @endif

            </div>


            {{-- =================================================
                 PAGOS
            ================================================== --}}

            <div class="pagos-list">


                @if(
                    $pagosPaginados
                        ->isEmpty()
                )

                    <div class="empty-state">

                        <div>

                            <div class="empty-icon">

                                <i class="inbox icon"></i>

                            </div>


                            @if(
                                $tabActivo
                                ===
                                'vencidos'
                            )

                                <h3>
                                    No tienes pagos vencidos
                                </h3>

                                <p>
                                    No existen recibos vencidos
                                    pendientes de pago.
                                </p>


                            @elseif(
                                $tabActivo
                                ===
                                'aprobados'
                            )

                                <h3>
                                    Aún no hay pagos aprobados
                                </h3>

                                <p>
                                    Aquí aparecerán tus pagos
                                    una vez aprobados.
                                </p>


                            @elseif(
                                $tabActivo
                                ===
                                'rechazados'
                            )

                                <h3>
                                    No tienes pagos rechazados
                                </h3>

                                <p>
                                    No existen comprobantes
                                    que necesiten corrección.
                                </p>


                            @else

                                <h3>
                                    No tienes pagos pendientes
                                </h3>

                                <p>
                                    No hay pagos pendientes
                                    para mostrar.
                                </p>

                            @endif

                        </div>

                    </div>


                @else


                    @foreach(
                        $pagosPaginados
                        as $pago
                    )

                        @php

                            /*
                             * La clasificación YA FUE HECHA
                             * por el controller.
                             *
                             * Aquí solamente preparamos
                             * información visual.
                             */

                            $pagoPrincipal =
                                $pago->pago;


                            $concepto =
                                $pagoPrincipal
                                    ->concepto
                                ??
                                'Pago';


                            $montoOriginal =
                                floatval(
                                    $pagoPrincipal
                                        ->cantidad
                                    ??
                                    0
                                );


                            $montoPagado =
                                floatval(
                                    $pago
                                        ->cantidad_pago
                                    ?:
                                    $montoOriginal
                                );


                            $tieneComprobante =
                                !is_null(
                                    $pago
                                        ->path_pago
                                );


                            $vencimiento =
                                (
                                    $pagoPrincipal
                                    &&
                                    $pagoPrincipal
                                        ->vencimiento
                                )
                                ?
                                \Carbon\Carbon::parse(
                                    $pagoPrincipal
                                        ->vencimiento
                                )
                                :
                                null;

                        @endphp


                        {{-- =====================================
                             PENDIENTES
                        ====================================== --}}

                        @if(
                            $tabActivo
                            ===
                            'pendientes'
                        )

                            <article
                                class="
                                    pago-card
                                    {{
                                        $tieneComprobante
                                            ? 'review'
                                            : 'warning'
                                    }}
                                "
                            >

                                <div class="pago-top">


                                    <div class="pago-main">

                                        <div class="pago-concept">

                                            {{ $concepto }}

                                        </div>


                                        @if(
                                            $vencimiento
                                        )

                                            <div class="pago-date">

                                                <i class="calendar alternate outline icon"></i>

                                                Vence:

                                                {{
                                                    $vencimiento
                                                        ->translatedFormat(
                                                            'd M Y'
                                                        )
                                                }}

                                            </div>

                                        @endif

                                    </div>


                                    <div class="pago-amount">

                                        ${{
                                            number_format(
                                                $montoOriginal,
                                                2
                                            )
                                        }}

                                    </div>

                                </div>


                                <div class="pago-state">


                                    @if(
                                        $tieneComprobante
                                    )

                                        <span class="status-badge review">

                                            <i class="hourglass half icon"></i>

                                            En revisión

                                        </span>


                                        <div class="status-helper">

                                            Tu comprobante ya fue enviado
                                            y está pendiente de aprobación.

                                        </div>


                                    @else

                                        <span class="status-badge pending">

                                            <i class="clock icon"></i>

                                            Pendiente

                                        </span>


                                        <div class="status-helper">

                                            Puedes subir tu comprobante
                                            antes de la fecha de vencimiento.

                                        </div>

                                    @endif

                                </div>


                                <div
                                    class="
                                        pago-actions
                                        {{
                                            $tieneComprobante
                                                ? ''
                                                : 'one'
                                        }}
                                    "
                                >


                                    <button
                                        type="button"
                                        class="
                                            pay-btn
                                            primary
                                            btn-subir-comprobante
                                        "
                                        data-id="{{
                                            $pago->id
                                        }}"
                                        data-cantidad="{{
                                            $montoOriginal
                                        }}"
                                    >

                                        <i class="upload icon"></i>

                                        {{
                                            $tieneComprobante
                                                ? 'Cambiar'
                                                : 'Subir comprobante'
                                        }}

                                    </button>


                                    @if(
                                        $tieneComprobante
                                    )

                                        <button
                                            type="button"
                                            class="
                                                pay-btn
                                                secondary
                                                btn-ver-comprobante
                                            "
                                            data-imagen="{{
                                                asset(
                                                    'storage/'
                                                    .$pago
                                                        ->path_pago
                                                )
                                            }}"
                                        >

                                            <i class="eye icon"></i>

                                            Ver enviado

                                        </button>

                                    @endif

                                </div>

                            </article>


                        {{-- =====================================
                             VENCIDOS
                        ====================================== --}}

                        @elseif(
                            $tabActivo
                            ===
                            'vencidos'
                        )

                            <article class="pago-card danger">


                                <div class="pago-top">


                                    <div class="pago-main">

                                        <div class="pago-concept">

                                            {{ $concepto }}

                                        </div>


                                        @if(
                                            $vencimiento
                                        )

                                            <div class="pago-date danger">

                                                <i class="calendar times outline icon"></i>

                                                Venció:

                                                {{
                                                    $vencimiento
                                                        ->translatedFormat(
                                                            'd M Y'
                                                        )
                                                }}

                                            </div>

                                        @endif

                                    </div>


                                    <div class="pago-amount">

                                        ${{
                                            number_format(
                                                $montoOriginal,
                                                2
                                            )
                                        }}

                                    </div>

                                </div>


                                <div class="pago-state">

                                    <span class="status-badge expired">

                                        <i class="exclamation triangle icon"></i>

                                        Vencido

                                    </span>


                                    <div class="status-helper">

                                        Este recibo ya pasó
                                        su fecha límite.

                                    </div>

                                </div>


                                <div class="pago-actions one">

                                    <button
                                        type="button"
                                        class="
                                            pay-btn
                                            warning
                                            btn-subir-comprobante
                                        "
                                        data-id="{{
                                            $pago->id
                                        }}"
                                        data-cantidad="{{
                                            $montoOriginal
                                        }}"
                                    >

                                        <i class="upload icon"></i>

                                        Subir comprobante

                                    </button>

                                </div>

                            </article>


                        {{-- =====================================
                             APROBADOS
                        ====================================== --}}

                        @elseif(
                            $tabActivo
                            ===
                            'aprobados'
                        )

                            <article class="pago-card success">


                                <div class="pago-top">


                                    <div class="pago-main">

                                        <div class="pago-concept">

                                            {{ $concepto }}

                                        </div>


                                        <div class="pago-date">

                                            <i class="calendar check outline icon"></i>

                                            Pagado:

                                            {{
                                                \Carbon\Carbon::parse(
                                                    $pago
                                                        ->updated_at
                                                )
                                                    ->translatedFormat(
                                                        'd M Y'
                                                    )
                                            }}

                                        </div>

                                    </div>


                                    <div class="pago-amount success">

                                        ${{
                                            number_format(
                                                $montoPagado,
                                                2
                                            )
                                        }}

                                    </div>

                                </div>


                                <div class="pago-state">

                                    <span class="status-badge paid">

                                        <i class="check icon"></i>

                                        Pagado

                                    </span>


                                    <div class="status-helper">

                                        Este pago ya fue aprobado
                                        por la administración.

                                    </div>

                                </div>


                                <div
                                    class="
                                        pago-actions
                                        {{
                                            $tieneComprobante
                                                ? ''
                                                : 'one'
                                        }}
                                    "
                                >

                                    <button
                                        type="button"
                                        class="
                                            pay-btn
                                            success
                                            btn-descargar-recibo
                                        "
                                        data-id="{{
                                            $pago->id
                                        }}"
                                    >

                                        <i class="download icon"></i>

                                        Descargar recibo

                                    </button>


                                    @if(
                                        $tieneComprobante
                                    )

                                        <button
                                            type="button"
                                            class="
                                                pay-btn
                                                secondary
                                                btn-ver-comprobante
                                            "
                                            data-imagen="{{
                                                asset(
                                                    'storage/'
                                                    .$pago
                                                        ->path_pago
                                                )
                                            }}"
                                        >

                                            <i class="eye icon"></i>

                                            Ver comprobante

                                        </button>

                                    @endif

                                </div>

                            </article>


                        {{-- =====================================
                             RECHAZADOS
                        ====================================== --}}

                        @elseif(
                            $tabActivo
                            ===
                            'rechazados'
                        )

                            <article class="pago-card danger">


                                <div class="pago-top">


                                    <div class="pago-main">

                                        <div class="pago-concept">

                                            {{ $concepto }}

                                        </div>


                                        <div class="pago-date danger">

                                            <i class="times circle outline icon"></i>

                                            Rechazado:

                                            {{
                                                \Carbon\Carbon::parse(
                                                    $pago
                                                        ->updated_at
                                                )
                                                    ->translatedFormat(
                                                        'd M Y'
                                                    )
                                            }}

                                        </div>

                                    </div>


                                    <div class="pago-amount">

                                        ${{
                                            number_format(
                                                $montoPagado,
                                                2
                                            )
                                        }}

                                    </div>

                                </div>


                                <div class="pago-state">

                                    <span class="status-badge rejected">

                                        <i class="times icon"></i>

                                        Rechazado

                                    </span>


                                    <div class="status-helper">

                                        Puedes enviar un nuevo
                                        comprobante para revisión.

                                    </div>

                                </div>


                                <div
                                    class="
                                        pago-actions
                                        {{
                                            $tieneComprobante
                                                ? ''
                                                : 'one'
                                        }}
                                    "
                                >

                                    <button
                                        type="button"
                                        class="
                                            pay-btn
                                            primary
                                            btn-subir-comprobante
                                        "
                                        data-id="{{
                                            $pago->id
                                        }}"
                                        data-cantidad="{{
                                            $montoOriginal
                                        }}"
                                    >

                                        <i class="upload icon"></i>

                                        Enviar nuevamente

                                    </button>


                                    @if(
                                        $tieneComprobante
                                    )

                                        <button
                                            type="button"
                                            class="
                                                pay-btn
                                                secondary
                                                btn-ver-comprobante
                                            "
                                            data-imagen="{{
                                                asset(
                                                    'storage/'
                                                    .$pago
                                                        ->path_pago
                                                )
                                            }}"
                                        >

                                            <i class="eye icon"></i>

                                            Ver anterior

                                        </button>

                                    @endif

                                </div>

                            </article>

                        @endif

                    @endforeach

                @endif


                {{-- =================================================
                     PAGINACIÓN
                ================================================== --}}

                @if(
                    $pagosPaginados
                        ->hasPages()
                )

                    <div class="pagos-pagination">

                        {{
                            $pagosPaginados
                                ->appends([
                                    'tab'
                                    =>
                                    $tabActivo
                                ])
                                ->links()
                        }}

                    </div>

                @endif

            </div>

        </div>

    </div>


    {{-- =========================================================
         MODAL SUBIR COMPROBANTE
    ========================================================== --}}

    <div
        class="ui modal"
        id="modal-subir-comprobante"
    >

        <i class="close icon"></i>


        <div class="header">

            <i class="cloud upload alternate icon"></i>

            Enviar comprobante

        </div>


        <div class="content">


            <div class="modal-info">

                <i class="info circle icon"></i>

                <div>

                    El comprobante debe pesar
                    máximo 2 MB.

                    Si necesitas reducirlo,

                    <a
                        href="https://www.iloveimg.com/es/comprimir-imagen"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="modal-compress-link"
                    >
                        comprime tu imagen aquí
                    </a>.

                </div>

            </div>


            <form
                id="form-subir-comprobante"
                class="ui form"
                enctype="multipart/form-data"
            >

                @csrf


                <input
                    type="hidden"
                    id="pago-id"
                    value=""
                >


                <div
                    class="upload-area"
                    id="uploadArea"
                >

                    <input
                        type="file"
                        name="comprobante"
                        id="comprobante-input"
                        accept="image/*"
                        style="display:none;"
                        required
                    >


                    <div
                        class="upload-placeholder"
                        id="uploadPlaceholder"
                    >

                        <div class="upload-icon">

                            <i class="cloud upload alternate icon"></i>

                        </div>


                        <span class="upload-primary">

                            Selecciona tu comprobante

                        </span>


                        <span class="upload-secondary">

                            o arrastra una imagen aquí

                        </span>


                        <span class="upload-formats">

                            JPG · PNG · GIF · Máximo 2 MB

                        </span>

                    </div>


                    <div
                        class="upload-preview"
                        id="uploadPreview"
                        style="display:none;"
                    >

                        <img
                            id="previewImage"
                            src=""
                            alt="Vista previa"
                        >


                        <button
                            type="button"
                            class="remove-image"
                            id="removeImage"
                            aria-label="Quitar comprobante"
                        >

                            <i class="times icon"></i>

                        </button>

                    </div>

                </div>


                <div
                    class="field"
                    style="margin-top:16px;"
                >

                    <label>
                        Cantidad pagada
                    </label>


                    <div
                        class="ui left icon input"
                        style="width:100%;"
                    >

                        <i class="dollar sign icon"></i>


                        <input
                            type="number"
                            name="cantidad_pago"
                            id="cantidad-pago"
                            min="0"
                            step="0.01"
                            placeholder="0.00"
                            required
                        >

                    </div>

                </div>

            </form>

        </div>


        <div class="actions">

            <div class="ui black deny button">

                Cancelar

            </div>


            <button
                type="button"
                class="ui green button"
                id="btn-subir-comprobante"
            >

                <i class="upload icon"></i>

                Subir

            </button>

        </div>

    </div>


    {{-- =========================================================
         MODAL VER
    ========================================================== --}}

    <div
        class="ui modal"
        id="modal-ver-comprobante"
    >

        <i class="close icon"></i>


        <div class="header">

            <i class="image outline icon"></i>

            Comprobante

        </div>


        <div class="scrolling content">

            <img
                id="imagen-comprobante"
                src=""
                alt="Comprobante"
            >

        </div>


        <div class="actions">

            <div class="ui black deny button">

                Cerrar

            </div>

        </div>

    </div>


    <script>

        $(document).ready(
            function () {

                const BASE_URL =
                    "{{ url('/') }}";


                let isSubmitting =
                    false;


                /*
                 * ==================================================
                 * MODALES
                 * ==================================================
                 */

                $('#modal-subir-comprobante')
                    .modal({
                        closable: true,
                        autofocus: false
                    });


                $('#modal-ver-comprobante')
                    .modal({
                        closable: true,
                        autofocus: false
                    });


                /*
                 * ==================================================
                 * ABRIR SUBIDA
                 * ==================================================
                 */

                $(document).on(
                    'click',
                    '.btn-subir-comprobante',
                    function (e) {

                        e.preventDefault();


                        const id =
                            $(this)
                                .data('id');


                        const cantidad =
                            $(this)
                                .data('cantidad')
                            || '';


                        $('#pago-id')
                            .val(id);


                        $('#cantidad-pago')
                            .val(cantidad);


                        limpiarArchivo();


                        $('#modal-subir-comprobante')
                            .modal(
                                'show'
                            );

                    }
                );


                /*
                 * ==================================================
                 * VER COMPROBANTE
                 * ==================================================
                 */

                $(document).on(
                    'click',
                    '.btn-ver-comprobante',
                    function (e) {

                        e.preventDefault();


                        const imagen =
                            $(this)
                                .data(
                                    'imagen'
                                );


                        if (!imagen) {
                            return;
                        }


                        $('#imagen-comprobante')
                            .attr(
                                'src',
                                imagen
                            );


                        $('#modal-ver-comprobante')
                            .modal(
                                'show'
                            );

                    }
                );


                /*
                 * ==================================================
                 * ARCHIVO
                 * ==================================================
                 */

                const uploadArea =
                    $('#uploadArea');


                const fileInput =
                    $('#comprobante-input');


                const uploadPlaceholder =
                    $('#uploadPlaceholder');


                const uploadPreview =
                    $('#uploadPreview');


                const previewImage =
                    $('#previewImage');


                /*
                 * Click en área.
                 */
                uploadArea.on(
                    'click',
                    function (e) {

                        if (
                            $(e.target)
                                .closest(
                                    '#removeImage'
                                )
                                .length
                        ) {
                            return;
                        }


                        fileInput
                            .trigger(
                                'click'
                            );

                    }
                );


                /*
                 * Input.
                 */
                fileInput.on(
                    'change',
                    function () {

                        const file =
                            this.files[0];


                        if (file) {

                            processFile(
                                file
                            );

                        }

                    }
                );


                /*
                 * Drag.
                 */
                uploadArea.on(
                    'dragover',
                    function (e) {

                        e.preventDefault();


                        $(this)
                            .addClass(
                                'dragover'
                            );

                    }
                );


                uploadArea.on(
                    'dragleave',
                    function (e) {

                        e.preventDefault();


                        $(this)
                            .removeClass(
                                'dragover'
                            );

                    }
                );


                /*
                 * Drop.
                 */
                uploadArea.on(
                    'drop',
                    function (e) {

                        e.preventDefault();


                        $(this)
                            .removeClass(
                                'dragover'
                            );


                        const files =
                            e.originalEvent
                                .dataTransfer
                                .files;


                        if (
                            !files.length
                        ) {
                            return;
                        }


                        const file =
                            files[0];


                        /*
                         * Asignar el archivo al input.
                         */
                        try {

                            const dt =
                                new DataTransfer();


                            dt.items.add(
                                file
                            );


                            fileInput[0]
                                .files =
                                dt.files;

                        } catch (error) {

                            console.warn(
                                error
                            );

                        }


                        processFile(
                            file
                        );

                    }
                );


                /*
                 * ==================================================
                 * VALIDAR IMAGEN
                 * ==================================================
                 */

                function processFile(
                    file
                ) {

                    if (!file) {
                        return;
                    }


                    /*
                     * 2 MB
                     */
                    if (
                        file.size
                        >
                        2 * 1024 * 1024
                    ) {

                        limpiarArchivo();


                        alertify.error(
                            'La imagen debe pesar menos de 2 MB'
                        );


                        return;
                    }


                    if (
                        !file.type
                            .startsWith(
                                'image/'
                            )
                    ) {

                        limpiarArchivo();


                        alertify.error(
                            'Solo se permiten imágenes'
                        );


                        return;
                    }


                    const reader =
                        new FileReader();


                    reader.onload =
                        function (event) {

                            previewImage
                                .attr(
                                    'src',
                                    event.target.result
                                );


                            uploadPlaceholder
                                .hide();


                            uploadPreview
                                .show();

                        };


                    reader.onerror =
                        function () {

                            limpiarArchivo();


                            alertify.error(
                                'No fue posible leer la imagen'
                            );

                        };


                    reader.readAsDataURL(
                        file
                    );

                }


                /*
                 * ==================================================
                 * QUITAR IMAGEN
                 * ==================================================
                 */

                $('#removeImage').on(
                    'click',
                    function (e) {

                        e.preventDefault();

                        e.stopPropagation();


                        limpiarArchivo();

                    }
                );


                function limpiarArchivo() {

                    fileInput
                        .val('');


                    previewImage
                        .attr(
                            'src',
                            ''
                        );


                    uploadPreview
                        .hide();


                    uploadPlaceholder
                        .show();

                }


                /*
                 * ==================================================
                 * SUBIR
                 * ==================================================
                 */

                $('#btn-subir-comprobante')
                    .on(
                        'click',
                        function () {

                            if (
                                isSubmitting
                            ) {
                                return false;
                            }


                            const btn =
                                $(this);


                            const id =
                                $('#pago-id')
                                    .val();


                            if (!id) {

                                alertify.error(
                                    'No se encontró el pago'
                                );

                                return false;
                            }


                            if (
                                !fileInput[0]
                                    .files[0]
                            ) {

                                alertify.error(
                                    'Selecciona una imagen'
                                );

                                return false;
                            }


                            if (
                                !$('#cantidad-pago')
                                    .val()
                            ) {

                                alertify.error(
                                    'Ingresa la cantidad'
                                );

                                return false;
                            }


                            const formData =
                                new FormData(
                                    document
                                        .getElementById(
                                            'form-subir-comprobante'
                                        )
                                );


                            isSubmitting =
                                true;


                            btn
                                .addClass(
                                    'loading disabled'
                                )
                                .html(
                                    '<i class="spinner loading icon"></i> Subiendo...'
                                );


                            $.ajax({

                                /*
                                 * MISMA ruta de tu implementación.
                                 */
                                url:
                                    BASE_URL
                                    + '/usuario/pago/subir-comprobante/'
                                    + id,

                                method:
                                    'POST',

                                data:
                                    formData,

                                processData:
                                    false,

                                contentType:
                                    false,

                                headers: {

                                    'X-CSRF-TOKEN':
                                        $(
                                            'meta[name="csrf-token"]'
                                        )
                                            .attr(
                                                'content'
                                            )

                                },


                                success:
                                    function () {

                                        alertify.success(
                                            'Comprobante subido correctamente'
                                        );


                                        $('#modal-subir-comprobante')
                                            .modal(
                                                'hide'
                                            );


                                        /*
                                         * Regresamos a pendientes,
                                         * porque tu controller deja
                                         * el estado en "pendiente".
                                         */
                                        setTimeout(
                                            function () {

                                                window.location.href =
                                                    "{{
                                                        route(
                                                            'usuario.pago.index',
                                                            [
                                                                'tab'
                                                                =>
                                                                'pendientes'
                                                            ]
                                                        )
                                                    }}";

                                            },
                                            700
                                        );

                                    },


                                error:
                                    function (xhr) {

                                        let mensaje =
                                            xhr
                                                .responseJSON
                                                ?.message
                                            ||
                                            'Error al subir el comprobante';


                                        const errores =
                                            xhr
                                                .responseJSON
                                                ?.errors;


                                        if (errores) {

                                            const key =
                                                Object.keys(
                                                    errores
                                                )[0];


                                            if (
                                                key
                                                &&
                                                errores[key]
                                                ?.length
                                            ) {

                                                mensaje =
                                                    errores[
                                                        key
                                                    ][0];

                                            }

                                        }


                                        alertify.error(
                                            mensaje
                                        );

                                    },


                                complete:
                                    function () {

                                        isSubmitting =
                                            false;


                                        btn
                                            .removeClass(
                                                'loading disabled'
                                            )
                                            .html(
                                                '<i class="upload icon"></i> Subir'
                                            );

                                    }

                            });


                            return false;

                        }
                    );


                /*
                 * ==================================================
                 * DESCARGAR RECIBO
                 * ==================================================
                 */

                $(document).on(
                    'click',
                    '.btn-descargar-recibo',
                    function () {

                        const btn =
                            $(this);


                        if (
                            btn.hasClass(
                                'loading'
                            )
                        ) {
                            return false;
                        }


                        const id =
                            btn.data(
                                'id'
                            );


                        if (!id) {
                            return false;
                        }


                        const original =
                            btn.html();


                        btn
                            .addClass(
                                'loading'
                            )
                            .html(
                                '<i class="spinner loading icon"></i> Preparando...'
                            );


                        window.location.href =
                            BASE_URL
                            + '/usuario/pago/descargar/'
                            + id;


                        setTimeout(
                            function () {

                                btn
                                    .removeClass(
                                        'loading'
                                    )
                                    .html(
                                        original
                                    );

                            },
                            2000
                        );

                    }
                );


                /*
                 * ==================================================
                 * INDICADORES DE SCROLL DE TABS
                 * ==================================================
                 *
                 * Esto recupera el comportamiento
                 * que ya tenías anteriormente.
                 */

                function actualizarIndicadoresTabs() {

                    const container =
                        document.getElementById(
                            'tabsContainer'
                        );


                    const leftHint =
                        document.getElementById(
                            'tabsHintLeft'
                        );


                    const rightHint =
                        document.getElementById(
                            'tabsHintRight'
                        );


                    const swipeText =
                        document.getElementById(
                            'mobileSwipeText'
                        );


                    if (
                        !container
                        ||
                        !leftHint
                        ||
                        !rightHint
                    ) {
                        return;
                    }


                    /*
                     * Solo móvil.
                     */
                    if (
                        window.innerWidth
                        >= 768
                    ) {

                        leftHint.style.opacity =
                            '0';


                        rightHint.style.opacity =
                            '0';


                        if (swipeText) {
                            swipeText.style.display =
                                'none';
                        }


                        return;
                    }


                    const hasOverflow =
                        container.scrollWidth
                        >
                        container.clientWidth
                        + 2;


                    /*
                     * Si todas las pestañas caben,
                     * no mostramos ninguna sugerencia.
                     */
                    if (!hasOverflow) {

                        leftHint.style.opacity =
                            '0';


                        rightHint.style.opacity =
                            '0';


                        if (swipeText) {
                            swipeText.style.display =
                                'none';
                        }


                        return;
                    }


                    if (swipeText) {

                        swipeText.style.display =
                            'flex';

                    }


                    const scrollLeft =
                        container.scrollLeft;


                    const maxScroll =
                        container.scrollWidth
                        -
                        container.clientWidth;


                    /*
                     * Flecha izquierda solo si
                     * hay contenido atrás.
                     */
                    leftHint.style.opacity =
                        scrollLeft > 8
                            ? '1'
                            : '0';


                    /*
                     * Flecha derecha mientras
                     * haya pestañas ocultas.
                     */
                    rightHint.style.opacity =
                        scrollLeft
                        <
                        maxScroll - 8
                            ? '1'
                            : '0';

                }


                /*
                 * ==================================================
                 * LLEVAR TAB ACTIVO A LA VISTA
                 * ==================================================
                 *
                 * Muy útil si el usuario entra directamente
                 * a Rechazados en móvil.
                 */

                function centrarTabActivo() {

                    if (
                        window.innerWidth
                        >= 768
                    ) {
                        return;
                    }


                    const container =
                        document.getElementById(
                            'tabsContainer'
                        );


                    if (!container) {
                        return;
                    }


                    const activo =
                        container.querySelector(
                            '.tab-btn.active'
                        );


                    if (!activo) {
                        return;
                    }


                    /*
                     * scrollIntoView puede mover también
                     * verticalmente la página.
                     *
                     * Calculamos solamente el horizontal.
                     */
                    const objetivo =
                        activo.offsetLeft
                        -
                        (
                            container.clientWidth
                            -
                            activo.offsetWidth
                        )
                        / 2;


                    container.scrollTo({
                        left:
                            Math.max(
                                0,
                                objetivo
                            ),

                        behavior:
                            'auto'
                    });

                }


                /*
                 * Inicialización.
                 */
                setTimeout(
                    function () {

                        centrarTabActivo();

                        actualizarIndicadoresTabs();

                    },
                    100
                );


                /*
                 * Al deslizar.
                 */
                $('#tabsContainer')
                    .on(
                        'scroll',
                        actualizarIndicadoresTabs
                    );


                /*
                 * Al rotar / redimensionar.
                 */
                $(window)
                    .on(
                        'resize',
                        function () {

                            actualizarIndicadoresTabs();

                        }
                    );

            }
        );

    </script>

</x-app-layout>