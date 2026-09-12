<x-app-layout>

    <style>

        .vecinos-page {
            --primary: #667eea;
            --primary-dark: #764ba2;

            --success: #22c55e;
            --success-dark: #16a34a;

            --info: #3b82f6;
            --info-dark: #2563eb;

            --danger: #ef4444;
            --danger-dark: #dc2626;

            --warning: #f59e0b;

            --text: #0f172a;
            --muted: #64748b;
            --soft: #94a3b8;

            --surface: #ffffff;
            --surface-soft: #f8fafc;
            --surface-muted: #f1f5f9;

            --border: rgba(148,163,184,.24);

            --shadow-sm:
                0 5px 18px rgba(15,23,42,.05);

            --shadow-md:
                0 12px 30px rgba(15,23,42,.09);

            width: 100%;

            padding-bottom: 30px;
        }


        * {
            box-sizing: border-box;
        }


        .vecinos-container {
            width: 100%;

            max-width: 1180px;

            margin: 0 auto;
        }


        /* =====================================================
           HERO
        ===================================================== */

        .vecinos-hero {
            position: relative;

            overflow: hidden;

            display: flex;

            align-items: center;

            justify-content: space-between;

            gap: 20px;

            padding: 25px 27px;

            margin-bottom: 17px;

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
                0 14px 34px rgba(102,126,234,.18);
        }


        .vecinos-hero::after {
            content: '';

            position: absolute;

            width: 230px;
            height: 230px;

            right: -90px;
            bottom: -145px;

            border-radius: 999px;

            background:
                rgba(255,255,255,.09);
        }


        .vecinos-hero-left {
            position: relative;

            z-index: 2;

            display: flex;

            align-items: center;

            gap: 14px;

            min-width: 0;
        }


        .vecinos-hero-icon {
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
                1px solid rgba(255,255,255,.17);
        }


        .vecinos-hero-icon i {
            margin: 0 !important;

            font-size: 1.45rem;
        }


        .vecinos-title {
            margin: 0;

            font-size:
                clamp(1.45rem,3vw,2rem);

            font-weight: 900;

            letter-spacing: -.035em;

            line-height: 1.1;
        }


        .vecinos-subtitle {
            max-width: 650px;

            margin: 4px 0 0;

            opacity: .9;

            font-size: .86rem;

            line-height: 1.45;
        }


        /* =====================================================
           FILTRO HERO
        ===================================================== */

        .hero-filter {
            position: relative;

            z-index: 2;

            width: 220px;

            flex-shrink: 0;
        }


        .hero-filter label {
            display: block;

            margin-bottom: 5px;

            color:
                rgba(255,255,255,.83);

            font-size: .63rem;

            font-weight: 900;

            text-transform: uppercase;

            letter-spacing: .06em;
        }


        .hero-filter-select-wrap {
            position: relative;
        }


        .hero-filter-select-wrap > i {
            position: absolute;

            left: 12px;
            top: 50%;

            z-index: 2;

            margin: 0 !important;

            transform:
                translateY(-50%);

            color:
                var(--primary);

            pointer-events: none;
        }


        .vecinos-filter {
            width: 100%;

            min-height: 42px;

            padding:
                0 32px 0 36px;

            border:
                1px solid rgba(255,255,255,.25);

            border-radius: 13px;

            outline: 0;

            color:
                var(--text);

            background: #fff;

            font-size: .77rem;

            font-weight: 800;

            cursor: pointer;

            box-shadow:
                0 8px 19px rgba(15,23,42,.10);
        }


        /* =====================================================
           RESUMEN
        ===================================================== */

        .vecinos-summary {
            display: grid;

            grid-template-columns:
                repeat(3,1fr);

            gap: 9px;

            margin-bottom: 17px;
        }


        .summary-card {
            display: flex;

            align-items: center;

            gap: 10px;

            min-height: 72px;

            padding: 11px 13px;

            border:
                1px solid var(--border);

            border-radius: 15px;

            background:
                var(--surface);

            box-shadow:
                var(--shadow-sm);
        }


        .summary-icon {
            width: 38px;
            height: 38px;

            flex-shrink: 0;

            display: flex;

            align-items: center;

            justify-content: center;

            border-radius: 11px;

            color: #fff;
        }


        .summary-icon i {
            margin: 0 !important;
        }


        .summary-icon.total {
            background:
                linear-gradient(
                    135deg,
                    var(--primary),
                    var(--primary-dark)
                );
        }


        .summary-icon.active {
            background:
                linear-gradient(
                    135deg,
                    var(--success),
                    var(--success-dark)
                );
        }


        .summary-icon.inactive {
            background:
                linear-gradient(
                    135deg,
                    var(--danger),
                    var(--danger-dark)
                );
        }


        .summary-number {
            color:
                var(--text);

            font-size: 1.15rem;

            font-weight: 950;

            line-height: 1;
        }


        .summary-label {
            margin-top: 4px;

            color:
                var(--muted);

            font-size: .65rem;

            font-weight: 850;

            text-transform: uppercase;

            letter-spacing: .035em;
        }


        /* =====================================================
           DIRECTORIO
        ===================================================== */

        .directory-card {
            overflow: hidden;

            border:
                1px solid var(--border);

            border-radius: 20px;

            background:
                var(--surface);

            box-shadow:
                var(--shadow-sm);
        }


        .directory-header {
            display: flex;

            align-items: center;

            justify-content: space-between;

            gap: 18px;

            padding: 17px 19px;

            border-bottom:
                1px solid #eef2f7;

            background:
                linear-gradient(
                    180deg,
                    #fff,
                    #fbfdff
                );
        }


        .directory-heading {
            min-width: 0;
        }


        .directory-eyebrow {
            margin-bottom: 2px;

            color:
                var(--primary);

            font-size: .62rem;

            font-weight: 900;

            text-transform: uppercase;

            letter-spacing: .07em;
        }


        .directory-title {
            margin: 0;

            color:
                var(--text);

            font-size: 1rem;

            font-weight: 900;
        }


        .directory-subtitle {
            margin-top: 3px;

            color:
                var(--muted);

            font-size: .7rem;

            line-height: 1.35;
        }


        /* =====================================================
           BUSCADOR
        ===================================================== */

        .directory-search {
            position: relative;

            width: 100%;

            max-width: 345px;

            flex-shrink: 0;
        }


        .directory-search > i {
            position: absolute;

            left: 13px;
            top: 50%;

            margin: 0 !important;

            transform:
                translateY(-50%);

            color:
                var(--soft);
        }


        .directory-search input {
            width: 100%;

            min-height: 42px;

            padding:
                9px 38px 9px 38px;

            border:
                1px solid #dbe2eb;

            border-radius: 13px;

            outline: 0;

            color:
                var(--text);

            background: #fff;

            font-size: .76rem;

            font-weight: 750;

            transition:
                border-color .18s ease,
                box-shadow .18s ease;
        }


        .directory-search input:focus {
            border-color:
                rgba(102,126,234,.55);

            box-shadow:
                0 0 0 3px rgba(102,126,234,.09);
        }


        .directory-search input::placeholder {
            color:
                var(--soft);
        }


        .search-clear {
            position: absolute;

            right: 7px;
            top: 50%;

            width: 29px;
            height: 29px;

            display: none;

            align-items: center;

            justify-content: center;

            padding: 0;

            border: 0;

            border-radius: 8px;

            transform:
                translateY(-50%);

            color:
                var(--muted);

            background:
                var(--surface-muted);

            cursor: pointer;
        }


        .search-clear i {
            margin: 0 !important;

            font-size: .72rem;
        }


        /* =====================================================
           DESKTOP
        ===================================================== */

        .desktop-table-view {
            display: block;
        }


        .mobile-card-view {
            display: none;
        }


        .vecinos-table-wrap {
            width: 100%;

            overflow-x: auto;

            padding: 4px;
        }


        #vecinos-table {
            width: 100% !important;

            margin: 0 !important;

            border: 0 !important;

            border-collapse:
                separate !important;

            border-spacing:
                0 !important;
        }


        #vecinos-table thead th {
            padding:
                11px 14px !important;

            border-bottom:
                1px solid #e9eef5 !important;

            color:
                var(--muted) !important;

            background:
                #f8fafc !important;

            font-size:
                .65rem !important;

            font-weight:
                900 !important;

            text-transform:
                uppercase;

            letter-spacing:
                .05em;

            white-space:
                nowrap;
        }


        #vecinos-table tbody td {
            padding:
                13px 14px !important;

            border-top:
                1px solid #f1f5f9 !important;

            vertical-align:
                middle !important;
        }


        #vecinos-table tbody tr {
            transition:
                background .16s ease;
        }


        #vecinos-table tbody tr:hover {
            background:
                #fafaff !important;
        }


        /* =====================================================
           PERFIL VECINO
        ===================================================== */

        .neighbor-profile {
            display: flex;

            align-items: center;

            gap: 11px;

            min-width: 205px;
        }


        .neighbor-photo-wrap {
            position: relative;

            width: 48px;
            height: 48px;

            flex-shrink: 0;
        }


        .neighbor-avatar,
        .neighbor-initial {
            width: 48px;
            height: 48px;

            border-radius: 999px;
        }


        .neighbor-avatar {
            display: block;

            border:
                3px solid #fff;

            object-fit: cover;

            box-shadow:
                0 6px 15px rgba(15,23,42,.14);

            cursor: zoom-in;

            transition:
                transform .16s ease;
        }


        .neighbor-avatar:hover {
            transform:
                scale(1.05);
        }


        /*
         * Indicador de ampliación.
         */
        .photo-zoom {
            position: absolute;

            right: -2px;
            bottom: -2px;

            width: 19px;
            height: 19px;

            display: flex;

            align-items: center;

            justify-content: center;

            border:
                2px solid #fff;

            border-radius: 999px;

            color: #fff;

            background:
                var(--primary);

            pointer-events: none;
        }


        .photo-zoom i {
            margin: 0 !important;

            font-size: .52rem;
        }


        .neighbor-initial {
            display: flex;

            align-items: center;

            justify-content: center;

            color: #fff;

            background:
                linear-gradient(
                    135deg,
                    var(--primary),
                    var(--primary-dark)
                );

            font-size: 1rem;

            font-weight: 900;

            text-transform: uppercase;

            box-shadow:
                0 6px 15px rgba(102,126,234,.20);
        }


        .neighbor-name-row {
            display: flex;

            align-items: center;

            flex-wrap: wrap;

            gap: 5px;
        }


        .neighbor-name {
            color:
                var(--text);

            font-size: .83rem;

            font-weight: 900;

            line-height: 1.25;
        }


        .neighbor-you {
            display: inline-flex;

            align-items: center;

            gap: 3px;

            padding: 3px 6px;

            border-radius: 999px;

            color: #2563eb;

            background: #eff6ff;

            font-size: .57rem;

            font-weight: 900;
        }


        .neighbor-you i {
            margin: 0 !important;
        }


        /* =====================================================
           CASA / ESTADOS
        ===================================================== */

        .neighbor-house,
        .status-pill,
        .type-pill {
            display: inline-flex;

            align-items: center;

            justify-content: center;

            gap: 5px;

            padding: 6px 8px;

            border-radius: 999px;

            font-size: .67rem;

            font-weight: 900;

            white-space: nowrap;
        }


        .neighbor-house {
            color:
                #475569;

            background:
                var(--surface-muted);
        }


        .neighbor-house i {
            margin: 0 !important;

            color:
                var(--primary);
        }


        .status-pill i,
        .type-pill i {
            margin: 0 !important;
        }


        .status-pill.active {
            color: #15803d;

            background: #dcfce7;
        }


        .status-pill.inactive {
            color: #b91c1c;

            background: #fee2e2;
        }


        .type-pill.owner {
            color: #6d28d9;

            background: #ede9fe;
        }


        .type-pill.tenant {
            color: #0f766e;

            background: #ccfbf1;
        }


        .neighbor-muted {
            color:
                var(--soft);

            font-size: .72rem;

            font-weight: 700;
        }


        /* =====================================================
           CONTACTO
        ===================================================== */

        .contact-actions {
            display: flex;

            flex-wrap: wrap;

            gap: 6px;

            min-width: 175px;
        }


        .contact-action {
            min-height: 35px;

            display: inline-flex;

            align-items: center;

            justify-content: center;

            gap: 5px;

            padding:
                7px 9px;

            border-radius: 10px;

            border: 0;

            color: #fff !important;

            font-size: .67rem;

            font-weight: 900;

            text-decoration: none !important;

            transition:
                transform .16s ease,
                opacity .16s ease;
        }


        .contact-action:hover {
            transform:
                translateY(-1px);

            opacity: .94;
        }


        .contact-action i {
            margin: 0 !important;
        }


        .contact-action.phone {
            background:
                linear-gradient(
                    135deg,
                    var(--info),
                    var(--info-dark)
                );
        }


        .contact-action.whatsapp {
            background:
                linear-gradient(
                    135deg,
                    var(--success),
                    var(--success-dark)
                );
        }


        /* =====================================================
           DATATABLE
        ===================================================== */

        .dataTables_wrapper {
            padding:
                13px 16px 16px;
        }


        .dataTables_wrapper
        .dataTables_filter {
            display: none;
        }


        .dataTables_wrapper
        .dataTables_length,
        .dataTables_wrapper
        .dataTables_info,
        .dataTables_wrapper
        .dataTables_paginate {
            color:
                var(--muted) !important;

            font-size: .72rem;

            font-weight: 750;
        }


        .dataTables_wrapper
        .dataTables_length select {
            margin: 0 5px !important;

            padding:
                6px 8px !important;

            border:
                1px solid #dbe2eb !important;

            border-radius:
                9px !important;

            color:
                var(--text) !important;

            background:
                #fff !important;
        }


        .dataTables_wrapper
        .dataTables_paginate
        .paginate_button {
            min-width:
                32px !important;

            border-radius:
                8px !important;

            border:
                0 !important;

            color:
                var(--muted) !important;

            font-weight:
                800 !important;
        }


        .dataTables_wrapper
        .dataTables_paginate
        .paginate_button.current {
            color:
                #fff !important;

            background:
                linear-gradient(
                    135deg,
                    var(--primary),
                    var(--primary-dark)
                ) !important;
        }


        /* =====================================================
           MOBILE
        ===================================================== */

        .mobile-neighbors-list {
            display: grid;

            grid-template-columns: 1fr;

            gap: 9px;

            padding: 11px;
        }


        .mobile-neighbor-card {
            overflow: hidden;

            padding: 13px;

            border:
                1px solid var(--border);

            border-radius: 16px;

            background: #fff;

            box-shadow:
                0 5px 16px rgba(15,23,42,.045);
        }


        .mobile-neighbor-header {
            display: flex;

            align-items: center;

            gap: 11px;

            padding-bottom: 11px;

            border-bottom:
                1px solid #f1f5f9;
        }


        .mobile-photo-wrap {
            position: relative;

            width: 54px;
            height: 54px;

            flex-shrink: 0;
        }


        .mobile-neighbor-avatar,
        .mobile-neighbor-initial {
            width: 54px;
            height: 54px;

            border-radius: 999px;
        }


        .mobile-neighbor-avatar {
            display: block;

            object-fit: cover;

            border:
                3px solid #fff;

            box-shadow:
                0 7px 16px rgba(15,23,42,.14);

            cursor: zoom-in;
        }


        .mobile-neighbor-initial {
            display: flex;

            align-items: center;

            justify-content: center;

            color: #fff;

            background:
                linear-gradient(
                    135deg,
                    var(--primary),
                    var(--primary-dark)
                );

            font-size: 1.05rem;

            font-weight: 900;
        }


        .mobile-neighbor-main {
            flex: 1;

            min-width: 0;
        }


        .mobile-neighbor-name {
            margin-bottom: 5px;

            color:
                var(--text);

            font-size: .87rem;

            font-weight: 900;

            line-height: 1.25;
        }


        .mobile-neighbor-badges {
            display: flex;

            flex-wrap: wrap;

            gap: 5px;
        }


        .mobile-info-grid {
            display: grid;

            grid-template-columns:
                repeat(2,minmax(0,1fr));

            gap: 7px;

            margin-top: 11px;
        }


        .mobile-info-box {
            min-width: 0;

            padding: 9px;

            border-radius: 11px;

            background:
                var(--surface-soft);
        }


        .mobile-info-label {
            display: flex;

            align-items: center;

            gap: 4px;

            margin-bottom: 5px;

            color:
                var(--soft);

            font-size: .57rem;

            font-weight: 900;

            text-transform: uppercase;
        }


        .mobile-info-label i {
            margin: 0 !important;

            color:
                var(--primary);
        }


        .mobile-contact-actions {
            display: grid;

            grid-template-columns:
                repeat(2,1fr);

            gap: 7px;

            margin-top: 10px;
        }


        .mobile-contact-actions
        .contact-action {
            width: 100%;

            min-height: 42px;

            border-radius: 11px;

            font-size: .72rem;
        }


        /* =====================================================
           EMPTY
        ===================================================== */

        .empty-state,
        .mobile-empty-search {
            padding: 35px 18px;

            text-align: center;
        }


        .mobile-empty-search {
            display: none;
        }


        .empty-icon {
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
                #eef2ff;
        }


        .empty-icon i {
            margin: 0 !important;

            font-size: 1.25rem;
        }


        .empty-state h3,
        .mobile-empty-search h3 {
            margin:
                0 0 4px;

            color:
                var(--text);

            font-size: .9rem;

            font-weight: 900;
        }


        .empty-state p,
        .mobile-empty-search p {
            margin: 0;

            color:
                var(--muted);

            font-size: .7rem;
        }


        /* =====================================================
           MODAL FOTO
        ===================================================== */

        .neighbor-lightbox {
            position: fixed;

            inset: 0;

            z-index: 99999;

            display: none;

            align-items: center;

            justify-content: center;

            padding: 20px;

            background:
                rgba(15,23,42,.94);

            opacity: 0;

            transition:
                opacity .18s ease;
        }


        .neighbor-lightbox.show {
            display: flex;

            opacity: 1;
        }


        .neighbor-lightbox-content {
            position: relative;

            max-width: 92vw;

            text-align: center;
        }


        .neighbor-lightbox-img {
            display: block;

            max-width: 90vw;
            max-height: 82vh;

            margin: 0 auto;

            border:
                4px solid rgba(255,255,255,.12);

            border-radius: 17px;

            object-fit: contain;

            background: #fff;

            box-shadow:
                0 22px 60px rgba(0,0,0,.4);
        }


        .neighbor-lightbox-info {
            margin-top: 11px;

            color: #fff;
        }


        .neighbor-lightbox-name {
            font-size: .9rem;

            font-weight: 900;
        }


        .neighbor-lightbox-house {
            margin-top: 2px;

            color:
                rgba(255,255,255,.66);

            font-size: .67rem;
        }


        .neighbor-lightbox-close {
            position: absolute;

            top: 15px;
            right: 15px;

            z-index: 2;

            width: 42px;
            height: 42px;

            display: flex;

            align-items: center;

            justify-content: center;

            padding: 0;

            border:
                1px solid rgba(255,255,255,.14);

            border-radius: 999px;

            color: #fff;

            background:
                rgba(255,255,255,.12);

            backdrop-filter:
                blur(8px);

            cursor: pointer;
        }


        .neighbor-lightbox-close i {
            margin: 0 !important;

            font-size: 1rem;
        }


        .neighbor-lightbox-hint {
            position: absolute;

            left: 50%;
            bottom: 14px;

            transform:
                translateX(-50%);

            color:
                rgba(255,255,255,.5);

            font-size: .6rem;

            white-space: nowrap;
        }


        /* =====================================================
           RESPONSIVE
        ===================================================== */

        @media (max-width: 768px) {

            .vecinos-page {
                padding-bottom: 22px;
            }


            .vecinos-container {
                width: 100%;
            }


            .vecinos-hero {
                align-items: stretch;

                flex-direction: column;

                gap: 15px;

                padding: 19px;

                margin:
                    0 0 12px;

                border-radius: 20px;
            }


            .vecinos-hero-left {
                align-items: flex-start;
            }


            .vecinos-hero-icon {
                width: 47px;
                height: 47px;

                border-radius: 14px;
            }


            .vecinos-title {
                font-size: 1.3rem;
            }


            .vecinos-subtitle {
                font-size: .74rem;
            }


            .hero-filter {
                width: 100%;
            }


            .vecinos-filter {
                min-height: 44px;

                font-size: .8rem;
            }


            /*
             * Resumen más compacto.
             */
            .vecinos-summary {
                grid-template-columns:
                    repeat(3,1fr);

                gap: 6px;

                margin:
                    0 0 12px;
            }


            .summary-card {
                display: block;

                min-height: 77px;

                padding: 9px 6px;

                border-radius: 13px;

                text-align: center;
            }


            .summary-icon {
                width: 30px;
                height: 30px;

                margin:
                    0 auto 6px;

                border-radius: 9px;
            }


            .summary-icon i {
                font-size: .75rem;
            }


            .summary-number {
                font-size: .95rem;
            }


            .summary-label {
                margin-top: 2px;

                font-size: .48rem;

                line-height: 1.25;
            }


            .directory-card {
                border-radius: 17px;
            }


            .directory-header {
                display: block;

                padding: 14px;
            }


            .directory-subtitle {
                margin-bottom: 10px;
            }


            .directory-search {
                max-width: 100%;
            }


            .directory-search input {
                min-height: 45px;

                /*
                 * Evita zoom automático de iOS.
                 */
                font-size: 16px;
            }


            .desktop-table-view {
                display: none !important;
            }


            .mobile-card-view {
                display: block !important;
            }


            .neighbor-lightbox {
                padding: 12px;
            }


            .neighbor-lightbox-img {
                max-width:
                    calc(100vw - 24px);

                max-height: 78vh;

                border-radius: 14px;
            }


            .neighbor-lightbox-close {
                top: 10px;
                right: 10px;

                width: 40px;
                height: 40px;
            }

        }


        @media (max-width: 380px) {

            .mobile-contact-actions {
                grid-template-columns: 1fr;
            }


            .mobile-info-grid {
                grid-template-columns: 1fr;
            }

        }


        @media (prefers-reduced-motion: reduce) {

            *,
            *::before,
            *::after {
                scroll-behavior: auto !important;

                transition: none !important;
            }

        }

    </style>


    @php

        $totalVecinos =
            $vecinos->count();

        $vecinosActivos =
            $vecinos
                ->where(
                    'estado',
                    1
                )
                ->count();

        $vecinosInactivos =
            $vecinos
                ->where(
                    'estado',
                    '!=',
                    1
                )
                ->count();

    @endphp


    <div class="vecinos-page">

        <div class="vecinos-container">


            {{-- =================================================
                 HERO
            ================================================== --}}

            <section class="vecinos-hero">


                <div class="vecinos-hero-left">

                    <div class="vecinos-hero-icon">

                        <i class="users icon"></i>

                    </div>


                    <div>

                        <h1 class="vecinos-title">
                            Vecinos
                        </h1>

                        <p class="vecinos-subtitle">
                            Encuentra a tus vecinos y comunícate
                            fácilmente por llamada o WhatsApp.
                        </p>

                    </div>

                </div>


                <div class="hero-filter">

                    <label for="filtro-vecinos">
                        Mostrar
                    </label>


                    <div class="hero-filter-select-wrap">

                        <i class="filter icon"></i>


                        <select
                            class="vecinos-filter"
                            id="filtro-vecinos"
                            onchange="aplicarFiltro()"
                        >

                            <option
                                value="todos"
                                {{
                                    request('filtro') == 'todos'
                                    ||
                                    !request('filtro')
                                        ? 'selected'
                                        : ''
                                }}
                            >
                                Todos los vecinos
                            </option>


                            <option
                                value="activos"
                                {{
                                    request('filtro') == 'activos'
                                        ? 'selected'
                                        : ''
                                }}
                            >
                                Solo activos
                            </option>


                            <option
                                value="inactivos"
                                {{
                                    request('filtro') == 'inactivos'
                                        ? 'selected'
                                        : ''
                                }}
                            >
                                Solo inactivos
                            </option>


                            <option
                                value="casa"
                                {{
                                    request('filtro') == 'casa'
                                        ? 'selected'
                                        : ''
                                }}
                            >
                                Ordenar por casa
                            </option>


                            <option
                                value="inquilinos"
                                {{
                                    request('filtro') == 'inquilinos'
                                        ? 'selected'
                                        : ''
                                }}
                            >
                                Inquilinos
                            </option>


                            <option
                                value="dueños"
                                {{
                                    request('filtro') == 'dueños'
                                        ? 'selected'
                                        : ''
                                }}
                            >
                                Dueños
                            </option>

                        </select>

                    </div>

                </div>

            </section>


            {{-- =================================================
                 RESUMEN
            ================================================== --}}

            <div class="vecinos-summary">


                <div class="summary-card">

                    <div class="summary-icon total">
                        <i class="users icon"></i>
                    </div>

                    <div>

                        <div class="summary-number">
                            {{ $totalVecinos }}
                        </div>

                        <div class="summary-label">
                            Registrados
                        </div>

                    </div>

                </div>


                <div class="summary-card">

                    <div class="summary-icon active">
                        <i class="check icon"></i>
                    </div>

                    <div>

                        <div class="summary-number">
                            {{ $vecinosActivos }}
                        </div>

                        <div class="summary-label">
                            Activos
                        </div>

                    </div>

                </div>


                <div class="summary-card">

                    <div class="summary-icon inactive">
                        <i class="times icon"></i>
                    </div>

                    <div>

                        <div class="summary-number">
                            {{ $vecinosInactivos }}
                        </div>

                        <div class="summary-label">
                            Inactivos
                        </div>

                    </div>

                </div>

            </div>


            {{-- =================================================
                 SIN VECINOS
            ================================================== --}}

            @if($vecinos->isEmpty())

                <div class="directory-card">

                    <div class="empty-state">

                        <div class="empty-icon">
                            <i class="users icon"></i>
                        </div>

                        <h3>
                            No se encontraron vecinos
                        </h3>

                        <p>
                            No hay vecinos registrados
                            en el condominio.
                        </p>

                    </div>

                </div>


            @else


                {{-- =================================================
                     DIRECTORIO
                ================================================== --}}

                <section class="directory-card">


                    <div class="directory-header">


                        <div class="directory-heading">

                            <div class="directory-eyebrow">
                                Directorio
                            </div>

                            <h2 class="directory-title">
                                Lista de vecinos
                            </h2>

                            <div class="directory-subtitle">
                                Busca por nombre, casa, celular,
                                estado o tipo de residente.
                            </div>

                        </div>


                        <div class="directory-search">

                            <i class="search icon"></i>


                            <input
                                type="search"
                                id="buscar-vecinos"
                                placeholder="Buscar vecino o casa..."
                                autocomplete="off"
                                aria-label="Buscar vecino"
                            >


                            <button
                                type="button"
                                class="search-clear"
                                id="limpiar-busqueda"
                                aria-label="Limpiar búsqueda"
                            >
                                <i class="times icon"></i>
                            </button>

                        </div>

                    </div>


                    {{-- =================================================
                         DESKTOP
                    ================================================== --}}

                    <div class="desktop-table-view">

                        <div class="vecinos-table-wrap">

                            <table
                                class="ui unstackable table"
                                id="vecinos-table"
                            >

                                <thead>

                                    <tr>

                                        <th>
                                            Vecino
                                        </th>

                                        <th>
                                            Casa
                                        </th>

                                        <th>
                                            Contacto
                                        </th>

                                        <th>
                                            Estado
                                        </th>

                                        <th>
                                            Tipo
                                        </th>

                                    </tr>

                                </thead>


                                <tbody>


                                    @foreach($vecinos as $vecino)

                                        @php

                                            $celularWhatsApp =
                                                preg_replace(
                                                    '/\D/',
                                                    '',
                                                    $vecino->celular ?? ''
                                                );


                                            if (
                                                strlen(
                                                    $celularWhatsApp
                                                ) === 10
                                            ) {

                                                $celularWhatsApp =
                                                    '52'
                                                    .$celularWhatsApp;

                                            }


                                            $mensajeWhatsApp =
                                                urlencode(
                                                    'Hola '
                                                    .$vecino->nombre
                                                    .', buen día, '
                                                );


                                            $tieneFoto =
                                                $vecino->foto
                                                &&
                                                file_exists(
                                                    storage_path(
                                                        'app/public/perfil/'
                                                        .$vecino->foto
                                                    )
                                                );


                                            $fotoUrl =
                                                $tieneFoto
                                                    ? asset(
                                                        'storage/perfil/'
                                                        .$vecino->foto
                                                    )
                                                    : null;


                                            $casaTexto =
                                                $vecino->casa
                                                    ? 'Casa '.$vecino->casa
                                                    : 'Sin casa registrada';

                                        @endphp


                                        <tr>


                                            {{-- VECINO --}}
                                            <td>

                                                <div class="neighbor-profile">


                                                    @if($tieneFoto)

                                                        <div class="neighbor-photo-wrap">

                                                            <img
                                                                src="{{ $fotoUrl }}"
                                                                class="
                                                                    neighbor-avatar
                                                                    js-neighbor-photo
                                                                "
                                                                alt="Foto de {{ $vecino->nombre }}"
                                                                data-image="{{ $fotoUrl }}"
                                                                data-name="{{ $vecino->nombre }}"
                                                                data-house="{{ $casaTexto }}"
                                                                tabindex="0"
                                                                role="button"
                                                                aria-label="Ampliar fotografía de {{ $vecino->nombre }}"
                                                            >


                                                            <span class="photo-zoom">
                                                                <i class="expand icon"></i>
                                                            </span>

                                                        </div>


                                                    @else

                                                        <div class="neighbor-initial">

                                                            {{
                                                                mb_substr(
                                                                    $vecino->nombre,
                                                                    0,
                                                                    1
                                                                )
                                                            }}

                                                        </div>

                                                    @endif


                                                    <div>

                                                        <div class="neighbor-name-row">

                                                            <span class="neighbor-name">

                                                                {{ $vecino->nombre }}

                                                            </span>


                                                            @if(
                                                                Auth::user()->id
                                                                ===
                                                                $vecino->id
                                                            )

                                                                <span class="neighbor-you">

                                                                    <i class="user icon"></i>

                                                                    Tú

                                                                </span>

                                                            @endif

                                                        </div>

                                                    </div>

                                                </div>

                                            </td>


                                            {{-- CASA --}}
                                            <td>

                                                @if($vecino->casa)

                                                    <span class="neighbor-house">

                                                        <i class="home icon"></i>

                                                        Casa {{ $vecino->casa }}

                                                    </span>

                                                @else

                                                    <span class="neighbor-muted">

                                                        No registrada

                                                    </span>

                                                @endif

                                            </td>


                                            {{-- CONTACTO --}}
                                            <td>

                                                @if($vecino->celular)

                                                    <div class="contact-actions">


                                                        <a
                                                            href="tel:{{ $vecino->celular }}"
                                                            class="contact-action phone"
                                                            aria-label="Llamar a {{ $vecino->nombre }}"
                                                        >

                                                            <i class="phone icon"></i>

                                                            Llamar

                                                        </a>


                                                        @if(!empty($celularWhatsApp))

                                                            <a
                                                                href="https://wa.me/{{ $celularWhatsApp }}?text={{ $mensajeWhatsApp }}"
                                                                class="contact-action whatsapp"
                                                                target="_blank"
                                                                rel="noopener noreferrer"
                                                                aria-label="Enviar WhatsApp a {{ $vecino->nombre }}"
                                                            >

                                                                <i class="whatsapp icon"></i>

                                                                WhatsApp

                                                            </a>

                                                        @endif

                                                    </div>

                                                @else

                                                    <span class="neighbor-muted">

                                                        Sin celular

                                                    </span>

                                                @endif

                                            </td>


                                            {{-- ESTADO --}}
                                            <td>

                                                @if($vecino->estado == 1)

                                                    <span class="status-pill active">

                                                        <i class="check circle icon"></i>

                                                        Activo

                                                    </span>

                                                @else

                                                    <span class="status-pill inactive">

                                                        <i class="times circle icon"></i>

                                                        Inactivo

                                                    </span>

                                                @endif

                                            </td>


                                            {{-- TIPO --}}
                                            <td>

                                                <span
                                                    class="
                                                        type-pill

                                                        {{
                                                            $vecino->tipo === 'Dueño'
                                                                ? 'owner'
                                                                : 'tenant'
                                                        }}
                                                    "
                                                >

                                                    <i
                                                        class="
                                                            {{
                                                                $vecino->tipo === 'Dueño'
                                                                    ? 'key'
                                                                    : 'user'
                                                            }}
                                                            icon
                                                        "
                                                    ></i>

                                                    {{ $vecino->tipo }}

                                                </span>

                                            </td>

                                        </tr>

                                    @endforeach

                                </tbody>

                            </table>

                        </div>

                    </div>


                    {{-- =================================================
                         MÓVIL
                    ================================================== --}}

                    <div class="mobile-card-view">


                        <div
                            class="mobile-neighbors-list"
                            id="mobile-neighbors-list"
                        >


                            @foreach($vecinos as $vecino)

                                @php

                                    $celularWhatsApp =
                                        preg_replace(
                                            '/\D/',
                                            '',
                                            $vecino->celular ?? ''
                                        );


                                    if (
                                        strlen(
                                            $celularWhatsApp
                                        ) === 10
                                    ) {

                                        $celularWhatsApp =
                                            '52'
                                            .$celularWhatsApp;

                                    }


                                    $mensajeWhatsApp =
                                        urlencode(
                                            'Hola '
                                            .$vecino->nombre
                                            .', me gustaría comunicarme contigo desde la plataforma.'
                                        );


                                    $estadoTexto =
                                        $vecino->estado == 1
                                            ? 'Activo'
                                            : 'Inactivo';


                                    $casaTexto =
                                        $vecino->casa
                                            ? 'Casa '.$vecino->casa
                                            : 'No registrada';


                                    $textoBusqueda =
                                        strtolower(
                                            $vecino->nombre
                                            .' '
                                            .$casaTexto
                                            .' '
                                            .($vecino->casa ?? '')
                                            .' '
                                            .($vecino->celular ?? '')
                                            .' '
                                            .$estadoTexto
                                            .' '
                                            .($vecino->tipo ?? '')
                                        );


                                    $tieneFoto =
                                        $vecino->foto
                                        &&
                                        file_exists(
                                            storage_path(
                                                'app/public/perfil/'
                                                .$vecino->foto
                                            )
                                        );


                                    $fotoUrl =
                                        $tieneFoto
                                            ? asset(
                                                'storage/perfil/'
                                                .$vecino->foto
                                            )
                                            : null;

                                @endphp


                                <article
                                    class="mobile-neighbor-card"
                                    data-search="{{ $textoBusqueda }}"
                                >


                                    <div class="mobile-neighbor-header">


                                        @if($tieneFoto)

                                            <div class="mobile-photo-wrap">

                                                <img
                                                    src="{{ $fotoUrl }}"
                                                    class="
                                                        mobile-neighbor-avatar
                                                        js-neighbor-photo
                                                    "
                                                    alt="Foto de {{ $vecino->nombre }}"
                                                    data-image="{{ $fotoUrl }}"
                                                    data-name="{{ $vecino->nombre }}"
                                                    data-house="{{ $casaTexto }}"
                                                    role="button"
                                                    tabindex="0"
                                                    aria-label="Ampliar fotografía de {{ $vecino->nombre }}"
                                                >


                                                <span class="photo-zoom">

                                                    <i class="expand icon"></i>

                                                </span>

                                            </div>


                                        @else

                                            <div class="mobile-neighbor-initial">

                                                {{
                                                    mb_substr(
                                                        $vecino->nombre,
                                                        0,
                                                        1
                                                    )
                                                }}

                                            </div>

                                        @endif


                                        <div class="mobile-neighbor-main">


                                            <div class="mobile-neighbor-name">

                                                {{ $vecino->nombre }}

                                            </div>


                                            <div class="mobile-neighbor-badges">


                                                @if(
                                                    Auth::user()->id
                                                    ===
                                                    $vecino->id
                                                )

                                                    <span class="neighbor-you">

                                                        <i class="user icon"></i>

                                                        Tú

                                                    </span>

                                                @endif


                                                @if($vecino->estado == 1)

                                                    <span class="status-pill active">

                                                        <i class="check circle icon"></i>

                                                        Activo

                                                    </span>

                                                @else

                                                    <span class="status-pill inactive">

                                                        <i class="times circle icon"></i>

                                                        Inactivo

                                                    </span>

                                                @endif

                                            </div>

                                        </div>

                                    </div>


                                    {{-- INFORMACIÓN --}}
                                    <div class="mobile-info-grid">


                                        <div class="mobile-info-box">

                                            <div class="mobile-info-label">

                                                <i class="home icon"></i>

                                                Casa

                                            </div>


                                            @if($vecino->casa)

                                                <span class="neighbor-house">

                                                    Casa {{ $vecino->casa }}

                                                </span>

                                            @else

                                                <span class="neighbor-muted">

                                                    No registrada

                                                </span>

                                            @endif

                                        </div>


                                        <div class="mobile-info-box">

                                            <div class="mobile-info-label">

                                                <i
                                                    class="
                                                        {{
                                                            $vecino->tipo === 'Dueño'
                                                                ? 'key'
                                                                : 'user'
                                                        }}
                                                        icon
                                                    "
                                                ></i>

                                                Tipo

                                            </div>


                                            <span
                                                class="
                                                    type-pill

                                                    {{
                                                        $vecino->tipo === 'Dueño'
                                                            ? 'owner'
                                                            : 'tenant'
                                                    }}
                                                "
                                            >

                                                {{ $vecino->tipo }}

                                            </span>

                                        </div>

                                    </div>


                                    {{-- CONTACTO --}}
                                    @if($vecino->celular)

                                        <div class="mobile-contact-actions">


                                            <a
                                                href="tel:{{ $vecino->celular }}"
                                                class="contact-action phone"
                                                aria-label="Llamar a {{ $vecino->nombre }}"
                                            >

                                                <i class="phone icon"></i>

                                                Llamar

                                            </a>


                                            @if(!empty($celularWhatsApp))

                                                <a
                                                    href="https://wa.me/{{ $celularWhatsApp }}?text={{ $mensajeWhatsApp }}"
                                                    class="contact-action whatsapp"
                                                    target="_blank"
                                                    rel="noopener noreferrer"
                                                    aria-label="Enviar WhatsApp a {{ $vecino->nombre }}"
                                                >

                                                    <i class="whatsapp icon"></i>

                                                    WhatsApp

                                                </a>

                                            @endif

                                        </div>


                                    @else

                                        <div
                                            style="
                                                margin-top:10px;
                                                text-align:center;
                                            "
                                        >

                                            <span class="neighbor-muted">
                                                Celular no registrado
                                            </span>

                                        </div>

                                    @endif

                                </article>

                            @endforeach

                        </div>


                        {{-- SIN RESULTADOS --}}
                        <div
                            class="mobile-empty-search"
                            id="mobile-empty-search"
                        >

                            <div class="empty-icon">

                                <i class="search icon"></i>

                            </div>

                            <h3>
                                No encontramos coincidencias
                            </h3>

                            <p>
                                Intenta buscar por nombre,
                                casa, estado o tipo.
                            </p>

                        </div>

                    </div>

                </section>

            @endif

        </div>

    </div>


    {{-- =========================================================
         MODAL / LIGHTBOX FOTOGRAFÍA
    ========================================================== --}}

    <div
        class="neighbor-lightbox"
        id="neighbor-lightbox"
        role="dialog"
        aria-modal="true"
        aria-hidden="true"
        aria-label="Fotografía del vecino"
    >


        <button
            type="button"
            class="neighbor-lightbox-close"
            id="neighbor-lightbox-close"
            aria-label="Cerrar fotografía"
        >

            <i class="times icon"></i>

        </button>


        <div class="neighbor-lightbox-content">


            <img
                src=""
                id="neighbor-lightbox-img"
                class="neighbor-lightbox-img"
                alt=""
            >


            <div class="neighbor-lightbox-info">

                <div
                    class="neighbor-lightbox-name"
                    id="neighbor-lightbox-name"
                ></div>


                <div
                    class="neighbor-lightbox-house"
                    id="neighbor-lightbox-house"
                ></div>

            </div>

        </div>


        <div class="neighbor-lightbox-hint">

            Toca fuera de la fotografía para cerrar

        </div>

    </div>


    <script>

        var vecinosDataTable =
            null;


        /*
         * ======================================================
         * FILTRO PRINCIPAL
         * ======================================================
         */

        function aplicarFiltro() {

            var filtro =
                document
                    .getElementById(
                        'filtro-vecinos'
                    )
                    .value;


            var url =
                '{{ route("usuario.vecino.index") }}';


            if (
                filtro === 'todos'
                ||
                filtro === ''
            ) {

                window.location.href =
                    url;

            } else {

                window.location.href =
                    url
                    + '?filtro='
                    + encodeURIComponent(
                        filtro
                    );

            }

        }


        /*
         * ======================================================
         * NORMALIZAR TEXTO
         * ======================================================
         */

        function normalizarTexto(
            texto
        ) {

            return (
                texto
                ||
                ''
            )
                .toString()
                .toLowerCase()
                .normalize(
                    'NFD'
                )
                .replace(
                    /[\u0300-\u036f]/g,
                    ''
                )
                .trim();

        }


        /*
         * ======================================================
         * BÚSQUEDA MOBILE
         * ======================================================
         */

        function filtrarVecinosMobile(
            valor
        ) {

            var busqueda =
                normalizarTexto(
                    valor
                );


            var cards =
                document.querySelectorAll(
                    '.mobile-neighbor-card'
                );


            var emptySearch =
                document.getElementById(
                    'mobile-empty-search'
                );


            var visibles =
                0;


            cards.forEach(
                function (card) {

                    var texto =
                        normalizarTexto(
                            card.getAttribute(
                                'data-search'
                            )
                        );


                    if (
                        texto.indexOf(
                            busqueda
                        )
                        !==
                        -1
                    ) {

                        card.style.display =
                            '';

                        visibles++;

                    } else {

                        card.style.display =
                            'none';

                    }

                }
            );


            if (emptySearch) {

                emptySearch.style.display =
                    visibles === 0
                        ? 'block'
                        : 'none';

            }

        }


        /*
         * ======================================================
         * BÚSQUEDA GENERAL
         * ======================================================
         */

        function filtrarVecinos(
            valor
        ) {

            if (
                vecinosDataTable
            ) {

                vecinosDataTable
                    .search(
                        valor
                    )
                    .draw();

            }


            filtrarVecinosMobile(
                valor
            );

        }


        /*
         * ======================================================
         * LIGHTBOX
         * ======================================================
         */

        function abrirFotoVecino(
            elemento
        ) {

            var imagen =
                elemento.getAttribute(
                    'data-image'
                );


            if (!imagen) {
                return;
            }


            var nombre =
                elemento.getAttribute(
                    'data-name'
                )
                ||
                'Vecino';


            var casa =
                elemento.getAttribute(
                    'data-house'
                )
                ||
                '';


            var modal =
                document.getElementById(
                    'neighbor-lightbox'
                );


            var modalImg =
                document.getElementById(
                    'neighbor-lightbox-img'
                );


            var modalName =
                document.getElementById(
                    'neighbor-lightbox-name'
                );


            var modalHouse =
                document.getElementById(
                    'neighbor-lightbox-house'
                );


            modalImg.src =
                imagen;


            modalImg.alt =
                'Fotografía de '
                + nombre;


            modalName.textContent =
                nombre;


            modalHouse.textContent =
                casa;


            modal.classList.add(
                'show'
            );


            modal.setAttribute(
                'aria-hidden',
                'false'
            );


            document.body.style.overflow =
                'hidden';


            var closeButton =
                document.getElementById(
                    'neighbor-lightbox-close'
                );


            if (closeButton) {

                setTimeout(
                    function () {

                        closeButton.focus();

                    },
                    50
                );

            }

        }


        function cerrarFotoVecino() {

            var modal =
                document.getElementById(
                    'neighbor-lightbox'
                );


            var modalImg =
                document.getElementById(
                    'neighbor-lightbox-img'
                );


            if (!modal) {
                return;
            }


            modal.classList.remove(
                'show'
            );


            modal.setAttribute(
                'aria-hidden',
                'true'
            );


            document.body.style.overflow =
                '';


            if (modalImg) {

                /*
                 * Se limpia para no dejar
                 * fotografías anteriores en memoria.
                 */
                setTimeout(
                    function () {

                        modalImg.src =
                            '';

                    },
                    180
                );

            }

        }


        /*
         * ======================================================
         * INIT
         * ======================================================
         */

        $(document).ready(
            function () {


                /*
                 * ==============================================
                 * DATATABLE
                 * ==============================================
                 */

                if (
                    $(window).width()
                    >
                    768
                    &&
                    $('#vecinos-table').length
                ) {

                    vecinosDataTable =
                        $('#vecinos-table')
                            .DataTable({

                                language: {

                                    url:
                                        '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'

                                },

                                order: [
                                    [0, 'asc']
                                ],

                                responsive:
                                    false,

                                pageLength:
                                    10,

                                lengthMenu: [
                                    10,
                                    25,
                                    50,
                                    100
                                ],

                                columnDefs: [
                                    {
                                        targets: [2],
                                        orderable: false
                                    }
                                ]

                            });

                }


                /*
                 * ==============================================
                 * BUSCADOR
                 * ==============================================
                 */

                var $search =
                    $('#buscar-vecinos');


                var $clear =
                    $('#limpiar-busqueda');


                function actualizarBotonLimpiar() {

                    $clear.css(
                        'display',
                        $search.val()
                            ? 'flex'
                            : 'none'
                    );

                }


                $search.on(
                    'keyup change input search',
                    function () {

                        filtrarVecinos(
                            this.value
                        );


                        actualizarBotonLimpiar();

                    }
                );


                $clear.on(
                    'click',
                    function () {

                        $search.val(
                            ''
                        );


                        filtrarVecinos(
                            ''
                        );


                        actualizarBotonLimpiar();


                        $search.trigger(
                            'focus'
                        );

                    }
                );


                /*
                 * ==============================================
                 * ABRIR FOTOGRAFÍA
                 * ==============================================
                 */

                $(document)
                    .on(
                        'click',
                        '.js-neighbor-photo',
                        function (event) {

                            event.preventDefault();

                            event.stopPropagation();


                            abrirFotoVecino(
                                this
                            );

                        }
                    );


                /*
                 * También permite Enter / espacio
                 * para usuarios con teclado.
                 */
                $(document)
                    .on(
                        'keydown',
                        '.js-neighbor-photo',
                        function (event) {

                            if (
                                event.key
                                ===
                                'Enter'
                                ||
                                event.key
                                ===
                                ' '
                            ) {

                                event.preventDefault();


                                abrirFotoVecino(
                                    this
                                );

                            }

                        }
                    );


                /*
                 * ==============================================
                 * CERRAR FOTOGRAFÍA
                 * ==============================================
                 */

                $('#neighbor-lightbox-close')
                    .on(
                        'click',
                        function (event) {

                            event.preventDefault();

                            event.stopPropagation();


                            cerrarFotoVecino();

                        }
                    );


                $('#neighbor-lightbox')
                    .on(
                        'click',
                        function (event) {

                            /*
                             * Solo cerramos si el usuario
                             * toca directamente el fondo.
                             *
                             * Tocar la fotografía no la cierra.
                             */
                            if (
                                event.target
                                ===
                                this
                            ) {

                                cerrarFotoVecino();

                            }

                        }
                    );


                /*
                 * ESC.
                 */
                $(document)
                    .on(
                        'keydown.vecinosLightbox',
                        function (event) {

                            if (
                                event.key
                                ===
                                'Escape'
                                &&
                                $('#neighbor-lightbox')
                                    .hasClass(
                                        'show'
                                    )
                            ) {

                                cerrarFotoVecino();

                            }

                        }
                    );

            }
        );

    </script>

</x-app-layout>