<x-app-layout>

    @php
        $usuarioFoto = auth()->user()->foto
            ? asset('storage/perfil/' . auth()->user()->foto)
            : 'https://cdn-icons-png.flaticon.com/512/847/847969.png';

        $tipoUsuario = ucfirst($user->tipo ?? 'residente');
        $casaUsuario = $user->casa ?: 'Sin asignar';
    @endphp

    <style>
        .perfil-page {
            --primary: #667eea;
            --primary-dark: #764ba2;
            --primary-soft: #eef2ff;

            --success: #10b981;
            --success-dark: #059669;

            --danger: #ef4444;
            --danger-dark: #dc2626;

            --warning: #f59e0b;
            --warning-dark: #d97706;

            --info: #3b82f6;

            --text: #0f172a;
            --muted: #64748b;
            --soft: #94a3b8;

            --border: #e2e8f0;
            --surface: #ffffff;
            --background: #f8fafc;

            width: 100%;
            padding-bottom: 32px;
        }

        .perfil-container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
        }

        * {
            box-sizing: border-box;
        }


        /* =====================================================
           HERO
        ===================================================== */

        .perfil-hero {
            position: relative;
            overflow: hidden;

            display: flex;
            align-items: center;
            justify-content: space-between;

            gap: 18px;

            padding: 23px 25px;
            margin-bottom: 18px;

            border-radius: 24px;

            color: #fff;

            background:
                radial-gradient(
                    circle at top right,
                    rgba(255,255,255,.20),
                    transparent 34%
                ),
                linear-gradient(
                    135deg,
                    var(--primary),
                    var(--primary-dark)
                );

            box-shadow:
                0 14px 34px rgba(102,126,234,.19);
        }

        .perfil-hero::after {
            content: '';

            position: absolute;

            width: 220px;
            height: 220px;

            right: -90px;
            bottom: -140px;

            border-radius: 999px;

            background:
                rgba(255,255,255,.08);
        }

        .perfil-hero-left {
            position: relative;
            z-index: 2;

            display: flex;
            align-items: center;

            gap: 14px;
        }

        .perfil-hero-icon {
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
                1px solid rgba(255,255,255,.16);
        }

        .perfil-hero-icon i {
            margin: 0 !important;
            font-size: 1.45rem;
        }

        .perfil-title {
            margin: 0;

            font-size:
                clamp(1.4rem, 3vw, 1.95rem);

            line-height: 1.1;

            font-weight: 900;

            letter-spacing: -.03em;
        }

        .perfil-subtitle {
            margin: 4px 0 0;

            font-size: .87rem;

            opacity: .9;
        }


        /* =====================================================
           RESUMEN DE CUENTA
        ===================================================== */

        .account-summary {
            display: grid;

            grid-template-columns:
                repeat(3, minmax(0, 1fr));

            gap: 10px;

            margin-bottom: 18px;
        }

        .account-summary-item {
            display: flex;
            align-items: center;

            gap: 10px;

            padding: 12px 14px;

            border:
                1px solid var(--border);

            border-radius: 15px;

            background:
                var(--surface);

            box-shadow:
                0 5px 18px rgba(15,23,42,.045);
        }

        .account-summary-icon {
            width: 37px;
            height: 37px;

            flex-shrink: 0;

            display: flex;
            align-items: center;
            justify-content: center;

            border-radius: 11px;

            color:
                var(--primary);

            background:
                var(--primary-soft);
        }

        .account-summary-icon i {
            margin: 0 !important;
        }

        .account-summary-label {
            color:
                var(--soft);

            font-size: .61rem;

            line-height: 1;

            font-weight: 900;

            text-transform: uppercase;

            letter-spacing: .04em;
        }

        .account-summary-value {
            margin-top: 4px;

            overflow: hidden;

            color:
                var(--text);

            font-size: .8rem;

            font-weight: 850;

            white-space: nowrap;

            text-overflow: ellipsis;
        }


        /* =====================================================
           LAYOUT
        ===================================================== */

        .perfil-layout {
            display: grid;

            grid-template-columns:
                300px minmax(0,1fr);

            gap: 16px;

            align-items: start;
        }

        .perfil-card {
            overflow: hidden;

            border:
                1px solid var(--border);

            border-radius: 19px;

            background:
                var(--surface);

            box-shadow:
                0 7px 22px rgba(15,23,42,.05);
        }

        .perfil-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;

            gap: 12px;

            padding: 16px 18px;

            border-bottom:
                1px solid #f1f5f9;
        }

        .perfil-card-title-wrap {
            display: flex;
            align-items: center;

            gap: 11px;

            min-width: 0;
        }

        .perfil-card-icon {
            width: 40px;
            height: 40px;

            flex-shrink: 0;

            display: flex;
            align-items: center;
            justify-content: center;

            border-radius: 12px;

            color:
                var(--primary);

            background:
                var(--primary-soft);
        }

        .perfil-card-icon.info {
            color: #2563eb;
            background: #eff6ff;
        }

        .perfil-card-icon.tenant {
            color: #0f766e;
            background: #ccfbf1;
        }

        .perfil-card-icon i {
            margin: 0 !important;
        }

        .perfil-card-title {
            margin: 0;

            color:
                var(--text);

            font-size: .95rem;

            font-weight: 900;
        }

        .perfil-card-subtitle {
            margin: 2px 0 0;

            color:
                var(--muted);

            font-size: .71rem;

            line-height: 1.35;
        }

        .perfil-card-body {
            padding: 18px;
        }


        /* =====================================================
           FOTO
        ===================================================== */

        .perfil-photo-body {
            text-align: center;
        }

        .avatar-wrap {
            position: relative;

            display: inline-block;

            margin:
                5px 0 16px;
        }

        .avatar-preview {
            display: block;

            width: 132px;
            height: 132px;

            border:
                4px solid #fff;

            border-radius: 999px;

            object-fit: cover;

            background:
                var(--background);

            box-shadow:
                0 11px 28px rgba(15,23,42,.15);
        }

        .avatar-badge {
            position: absolute;

            right: 3px;
            bottom: 4px;

            width: 36px;
            height: 36px;

            display: flex;
            align-items: center;
            justify-content: center;

            border:
                3px solid #fff;

            border-radius: 999px;

            color: #fff;

            background:
                linear-gradient(
                    135deg,
                    var(--primary),
                    var(--primary-dark)
                );

            box-shadow:
                0 6px 15px rgba(102,126,234,.25);
        }

        .avatar-badge i {
            margin: 0 !important;
            font-size: .85rem;
        }

        .upload-area {
            overflow: hidden;

            margin-bottom: 10px;

            border:
                2px dashed #dbe1eb;

            border-radius: 14px;

            background:
                var(--background);

            transition:
                .18s ease;
        }

        .upload-area:hover,
        .upload-area.dragover {
            border-color:
                var(--primary);

            background:
                #f5f3ff;
        }

        .upload-label {
            display: block;

            padding: 15px;

            cursor: pointer;
        }

        .upload-placeholder {
            display: flex;
            align-items: center;

            gap: 10px;

            text-align: left;
        }

        .upload-icon {
            width: 37px;
            height: 37px;

            flex-shrink: 0;

            display: flex;
            align-items: center;
            justify-content: center;

            border-radius: 11px;

            color:
                var(--primary);

            background:
                var(--primary-soft);
        }

        .upload-icon i {
            margin: 0 !important;
        }

        .upload-text {
            min-width: 0;
        }

        .upload-text .primary {
            display: block;

            color:
                var(--text);

            font-size: .76rem;

            font-weight: 900;
        }

        .upload-text .secondary {
            display: block;

            margin-top: 2px;

            color:
                var(--muted);

            font-size: .65rem;
        }

        .photo-help {
            margin-bottom: 13px;

            color:
                var(--soft);

            font-size: .64rem;

            line-height: 1.4;
        }


        /* =====================================================
           FORMS
        ===================================================== */

        .section-label {
            display: flex;
            align-items: center;

            gap: 7px;

            margin:
                4px 0 13px;

            color:
                var(--text);

            font-size: .78rem;

            font-weight: 900;
        }

        .section-label i {
            margin: 0 !important;

            color:
                var(--primary);
        }

        .form-grid-2 {
            display: grid;

            grid-template-columns:
                repeat(2,minmax(0,1fr));

            gap: 13px;
        }

        .form-group {
            margin-bottom: 14px;
        }

        .form-label {
            display: block;

            margin-bottom: 5px;

            color:
                #475569;

            font-size: .69rem;

            font-weight: 850;
        }

        .input-icon-wrapper {
            position: relative;

            display: flex;
            align-items: center;
        }

        .input-icon-wrapper > i {
            position: absolute;

            left: 12px;

            z-index: 2;

            margin: 0 !important;

            color:
                var(--soft);

            pointer-events: none;
        }

        .perfil-input {
            width: 100%;

            min-height: 43px;

            padding:
                9px 11px;

            border:
                1px solid #dbe2ea !important;

            border-radius:
                12px !important;

            outline: none !important;

            background: #fff !important;

            color:
                var(--text) !important;

            font-size: .8rem !important;

            transition:
                border-color .18s ease,
                box-shadow .18s ease;
        }

        .perfil-input:focus {
            border-color:
                rgba(102,126,234,.6) !important;

            box-shadow:
                0 0 0 3px rgba(102,126,234,.08) !important;
        }

        .input-with-icon {
            padding-left:
                38px !important;
        }


        /* =====================================================
           INFORMACIÓN NO EDITABLE
        ===================================================== */

        .account-fields {
            display: grid;

            grid-template-columns:
                repeat(2,minmax(0,1fr));

            gap: 10px;

            margin-bottom: 17px;
        }

        .readonly-card {
            display: flex;
            align-items: center;

            gap: 10px;

            padding: 11px 12px;

            border:
                1px solid var(--border);

            border-radius: 13px;

            background:
                var(--background);
        }

        .readonly-icon {
            width: 33px;
            height: 33px;

            flex-shrink: 0;

            display: flex;
            align-items: center;
            justify-content: center;

            border-radius: 10px;

            color:
                var(--primary);

            background:
                #fff;
        }

        .readonly-icon i {
            margin: 0 !important;
        }

        .readonly-label {
            color:
                var(--soft);

            font-size: .6rem;

            font-weight: 900;

            text-transform: uppercase;
        }

        .readonly-value {
            margin-top: 2px;

            color:
                var(--text);

            font-size: .76rem;

            font-weight: 850;
        }


        /* =====================================================
           PREFERENCIAS / SWITCH
        ===================================================== */

        .preferences {
            display: grid;

            gap: 8px;
        }

        .setting-card {
            position: relative;

            display: block;

            cursor: pointer;
        }

        .setting-card input {
            position: absolute;

            opacity: 0;

            pointer-events: none;
        }

        .setting-content {
            display: grid;

            grid-template-columns:
                auto minmax(0,1fr) auto;

            align-items: center;

            gap: 11px;

            padding: 12px 13px;

            border:
                1px solid var(--border);

            border-radius: 14px;

            background: #fff;

            transition:
                .18s ease;
        }

        .setting-card.checked .setting-content,
        .setting-card input:checked + .setting-content {
            border-color:
                rgba(102,126,234,.45);

            background:
                #f7f7ff;
        }

        .setting-icon {
            width: 36px;
            height: 36px;

            display: flex;
            align-items: center;
            justify-content: center;

            flex-shrink: 0;

            border-radius: 11px;

            color:
                var(--soft);

            background:
                var(--background);
        }

        .setting-card.checked .setting-icon,
        .setting-card input:checked + .setting-content .setting-icon {
            color: #fff;

            background:
                linear-gradient(
                    135deg,
                    var(--primary),
                    var(--primary-dark)
                );
        }

        .setting-icon i {
            margin: 0 !important;
        }

        .setting-title {
            display: block;

            color:
                var(--text);

            font-size: .76rem;

            font-weight: 900;
        }

        .setting-description {
            display: block;

            margin-top: 2px;

            color:
                var(--muted);

            font-size: .66rem;

            line-height: 1.35;
        }

        .switch {
            position: relative;

            width: 38px;
            height: 22px;

            flex-shrink: 0;

            border-radius: 999px;

            background: #cbd5e1;

            transition:
                .18s ease;
        }

        .switch::after {
            content: '';

            position: absolute;

            top: 3px;
            left: 3px;

            width: 16px;
            height: 16px;

            border-radius: 999px;

            background: #fff;

            box-shadow:
                0 2px 5px rgba(15,23,42,.15);

            transition:
                transform .18s ease;
        }

        .setting-card.checked .switch,
        .setting-card input:checked + .setting-content .switch {
            background:
                var(--success);
        }

        .setting-card.checked .switch::after,
        .setting-card input:checked + .setting-content .switch::after {
            transform:
                translateX(16px);
        }

        .push-test {
            display: none;

            justify-content: flex-end;

            margin-top: 7px;
        }


        /* =====================================================
           BOTONES
        ===================================================== */

        .perfil-actions {
            display: flex;
            align-items: center;
            justify-content: flex-end;

            gap: 8px;

            margin-top: 18px;

            padding-top: 16px;

            border-top:
                1px solid #f1f5f9;
        }

        .perfil-btn {
            min-height: 41px;

            display: inline-flex;
            align-items: center;
            justify-content: center;

            gap: 6px;

            padding:
                9px 14px;

            border: 0;

            border-radius: 11px;

            cursor: pointer;

            font-size: .72rem;

            font-weight: 900;

            text-decoration: none !important;
        }

        .perfil-btn i {
            margin: 0 !important;
        }

        .perfil-btn.primary {
            color: #fff;

            background:
                linear-gradient(
                    135deg,
                    var(--primary),
                    var(--primary-dark)
                );

            box-shadow:
                0 7px 17px rgba(102,126,234,.18);
        }

        .perfil-btn.secondary {
            color: #475569;

            background:
                #f1f5f9;
        }

        .perfil-btn.success {
            color: #fff;

            background:
                var(--success);
        }

        .perfil-btn.info {
            color: #1d4ed8;

            background:
                #eff6ff;
        }

        .perfil-btn.danger {
            color: #b91c1c;

            background:
                #fef2f2;
        }

        .perfil-btn.loading,
        .perfil-btn.disabled {
            opacity: .65;

            pointer-events: none;
        }

        .photo-submit {
            width: 100%;
        }


        /* =====================================================
           INQUILINO
        ===================================================== */

        .tenant-section {
            margin-top: 17px;
        }

        .tenant-card-body {
            display: grid;

            grid-template-columns:
                minmax(0,1.25fr)
                minmax(280px,.75fr);

            gap: 14px;
        }

        .tenant-profile {
            overflow: hidden;

            border:
                1px solid var(--border);

            border-radius: 16px;

            background:
                var(--background);
        }

        .tenant-main-row {
            display: flex;

            align-items: center;

            gap: 13px;

            padding: 15px;

            background: #fff;

            border-bottom:
                1px solid #f1f5f9;
        }

        .tenant-avatar {
            width: 60px;
            height: 60px;

            flex-shrink: 0;

            border:
                3px solid #fff;

            border-radius: 16px;

            object-fit: cover;

            box-shadow:
                0 7px 16px rgba(15,23,42,.10);
        }

        .tenant-name {
            margin:
                0 0 7px;

            color:
                var(--text);

            font-size: .95rem;

            font-weight: 900;
        }

        .tenant-badges {
            display: flex;
            flex-wrap: wrap;

            gap: 5px;
        }

        .tenant-badge {
            display: inline-flex;

            align-items: center;

            gap: 4px;

            padding: 4px 7px;

            border-radius: 999px;

            font-size: .61rem;

            font-weight: 900;
        }

        .tenant-badge i {
            margin: 0 !important;
        }

        .tenant-badge.type {
            color: #0f766e;

            background: #ccfbf1;
        }

        .tenant-badge.active {
            color: #166534;

            background: #dcfce7;
        }

        .tenant-badge.pending {
            color: #92400e;

            background: #fef3c7;
        }

        .tenant-badge.inactive {
            color: #991b1b;

            background: #fee2e2;
        }

        .tenant-details {
            display: grid;

            grid-template-columns:
                repeat(2,minmax(0,1fr));

            gap: 7px;

            padding: 12px;
        }

        .tenant-detail {
            display: flex;

            align-items: center;

            gap: 7px;

            min-width: 0;

            padding: 9px;

            border-radius: 10px;

            background: #fff;

            color:
                var(--muted);

            font-size: .69rem;
        }

        .tenant-detail i {
            flex-shrink: 0;

            margin: 0 !important;

            color:
                var(--primary);
        }

        .tenant-detail span {
            overflow-wrap: anywhere;
        }

        .tenant-actions {
            display: flex;
            flex-wrap: wrap;

            gap: 7px;

            padding:
                0 12px 12px;
        }


        /* =====================================================
           PAGO INQUILINO
        ===================================================== */

        .tenant-permissions {
            display: flex;
            flex-direction: column;

            gap: 10px;
        }

        .permission-info {
            padding: 14px;

            border:
                1px solid #fde68a;

            border-radius: 14px;

            background: #fffbeb;
        }

        .permission-info-head {
            display: flex;

            align-items: center;

            gap: 8px;

            margin-bottom: 6px;

            color: #92400e;

            font-size: .75rem;

            font-weight: 900;
        }

        .permission-info-head i {
            margin: 0 !important;
        }

        .permission-info p {
            margin: 0;

            color: #92400e;

            font-size: .68rem;

            line-height: 1.48;
        }

        .permission-note {
            margin-top: 7px;

            font-weight: 800;
        }


        /* =====================================================
           MODAL PASSWORD
        ===================================================== */

        #modal-pass.ui.modal {
            border-radius:
                18px !important;

            overflow: hidden;
        }

        .perfil-modal-header {
            display: flex !important;

            align-items: center;

            gap: 7px;

            color:
                #fff !important;

            background:
                linear-gradient(
                    135deg,
                    var(--primary),
                    var(--primary-dark)
                ) !important;
        }

        .perfil-modal-header i {
            margin: 0 !important;
        }

        .password-helper {
            margin-bottom: 14px;

            padding: 10px 11px;

            border:
                1px solid #dbeafe;

            border-radius: 11px;

            background:
                #eff6ff;

            color:
                #1e40af;

            font-size: .7rem;

            line-height: 1.4;
        }


        /* =====================================================
           RESPONSIVE
        ===================================================== */

        @media (max-width: 950px) {

            .perfil-layout {
                grid-template-columns: 1fr;
            }

            .perfil-photo-card {
                display: grid;

                grid-template-columns:
                    220px minmax(0,1fr);
            }

            .perfil-photo-card .perfil-card-header {
                display: none;
            }

            .perfil-photo-body {
                grid-column:
                    1 / -1;

                display: grid;

                grid-template-columns:
                    190px minmax(0,1fr);

                gap: 20px;

                align-items: center;

                text-align: left;
            }

            .avatar-wrap {
                margin: 0 auto;
            }

            .tenant-card-body {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 700px) {

            .perfil-hero {
                padding: 18px;

                border-radius: 20px;

                margin-bottom: 12px;
            }

            .perfil-hero-icon {
                width: 46px;
                height: 46px;

                border-radius: 13px;
            }

            .perfil-title {
                font-size: 1.28rem;
            }

            .perfil-subtitle {
                font-size: .75rem;
            }

            .account-summary {
                grid-template-columns:
                    repeat(2,minmax(0,1fr));

                gap: 7px;
            }

            .account-summary-item:first-child {
                grid-column:
                    1 / -1;
            }

            .perfil-card {
                border-radius: 16px;
            }

            .perfil-card-header,
            .perfil-card-body {
                padding: 14px;
            }

            .perfil-photo-card {
                display: block;
            }

            .perfil-photo-card .perfil-card-header {
                display: flex;
            }

            .perfil-photo-body {
                display: block;

                text-align: center;
            }

            .avatar-wrap {
                margin:
                    3px 0 15px;
            }

            .form-grid-2,
            .account-fields {
                grid-template-columns: 1fr;
            }

            .setting-content {
                grid-template-columns:
                    auto minmax(0,1fr) auto;
            }

            .perfil-actions {
                display: grid;

                grid-template-columns: 1fr;
            }

            .perfil-actions .perfil-btn {
                width: 100%;
            }

            .tenant-details {
                grid-template-columns: 1fr;
            }

            .tenant-actions {
                display: grid;

                grid-template-columns: 1fr;
            }

            .tenant-actions .perfil-btn {
                width: 100%;
            }

            #modal-pass.ui.modal {
                width:
                    calc(100% - 20px) !important;

                margin-left:
                    10px !important;

                margin-right:
                    10px !important;
            }
        }

        @media (max-width: 420px) {

            .account-summary-value {
                font-size: .72rem;
            }

            .avatar-preview {
                width: 118px;
                height: 118px;
            }

            .tenant-main-row {
                align-items: flex-start;
            }

            .tenant-avatar {
                width: 54px;
                height: 54px;
            }

            .switch {
                width: 36px;
            }

            .switch::after {
                width: 16px;
                height: 16px;
            }
        }

    </style>


    <div class="perfil-page">

        <div class="perfil-container">


            {{-- =================================================
                 HERO
            ================================================== --}}

            <section class="perfil-hero">

                <div class="perfil-hero-left">

                    <div class="perfil-hero-icon">
                        <i class="user circle icon"></i>
                    </div>

                    <div>

                        <h1 class="perfil-title">
                            Mi perfil
                        </h1>

                        <p class="perfil-subtitle">
                            Administra tus datos, preferencias
                            y configuración de tu cuenta.
                        </p>

                    </div>

                </div>

            </section>


            {{-- =================================================
                 RESUMEN
            ================================================== --}}

            <div class="account-summary">

                <div class="account-summary-item">

                    <div class="account-summary-icon">
                        <i class="envelope outline icon"></i>
                    </div>

                    <div style="min-width:0;">

                        <div class="account-summary-label">
                            Correo
                        </div>

                        <div class="account-summary-value">
                            {{ $user->correo }}
                        </div>

                    </div>

                </div>


                <div class="account-summary-item">

                    <div class="account-summary-icon">
                        <i class="home icon"></i>
                    </div>

                    <div>

                        <div class="account-summary-label">
                            Casa
                        </div>

                        <div class="account-summary-value">
                            {{ $casaUsuario }}
                        </div>

                    </div>

                </div>


                <div class="account-summary-item">

                    <div class="account-summary-icon">
                        <i class="id badge outline icon"></i>
                    </div>

                    <div>

                        <div class="account-summary-label">
                            Perfil
                        </div>

                        <div class="account-summary-value">
                            {{ $tipoUsuario }}
                        </div>

                    </div>

                </div>

            </div>


            {{-- =================================================
                 CONTENIDO PRINCIPAL
            ================================================== --}}

            <div class="perfil-layout">


                {{-- =============================================
                     FOTOGRAFÍA
                ============================================== --}}

                <section class="perfil-card perfil-photo-card">

                    <div class="perfil-card-header">

                        <div class="perfil-card-title-wrap">

                            <div class="perfil-card-icon">
                                <i class="camera icon"></i>
                            </div>

                            <div>

                                <h2 class="perfil-card-title">
                                    Fotografía
                                </h2>

                                <p class="perfil-card-subtitle">
                                    Personaliza tu perfil.
                                </p>

                            </div>

                        </div>

                    </div>


                    <div class="perfil-card-body perfil-photo-body">


                        <div>

                            <div class="avatar-wrap">

                                <img
                                    id="preview-foto"
                                    class="avatar-preview"
                                    src="{{ $usuarioFoto }}"
                                    alt="Foto de perfil"
                                >

                                <span class="avatar-badge">

                                    <i class="camera icon"></i>

                                </span>

                            </div>

                        </div>


                        <form
                            id="form-foto"
                            enctype="multipart/form-data"
                        >

                            @csrf


                            <div
                                class="upload-area"
                                id="upload-area-foto"
                            >

                                <input
                                    type="file"
                                    accept="image/jpeg,image/png,image/webp,image/*"
                                    name="foto"
                                    id="foto-input"
                                    hidden
                                >


                                <label
                                    for="foto-input"
                                    class="upload-label"
                                >

                                    <div
                                        class="upload-placeholder"
                                        id="upload-placeholder"
                                    >

                                        <div class="upload-icon">

                                            <i class="image outline icon"></i>

                                        </div>


                                        <div class="upload-text">

                                            <span class="primary">
                                                Seleccionar fotografía
                                            </span>

                                            <span class="secondary">
                                                JPG, PNG o WEBP
                                            </span>

                                        </div>

                                    </div>

                                </label>

                            </div>


                            <div class="photo-help">
                                Recomendamos una imagen cuadrada.
                                Tamaño máximo: 5 MB.
                            </div>


                            <button
                                type="submit"
                                class="perfil-btn primary photo-submit"
                                id="btn-guardar-foto"
                            >

                                <i class="upload icon"></i>

                                Actualizar fotografía

                            </button>

                        </form>

                    </div>

                </section>


                {{-- =============================================
                     INFORMACIÓN
                ============================================== --}}

                <section class="perfil-card">

                    <div class="perfil-card-header">

                        <div class="perfil-card-title-wrap">

                            <div class="perfil-card-icon info">

                                <i class="user outline icon"></i>

                            </div>

                            <div>

                                <h2 class="perfil-card-title">
                                    Información personal
                                </h2>

                                <p class="perfil-card-subtitle">
                                    Actualiza tus datos de contacto
                                    y preferencias.
                                </p>

                            </div>

                        </div>

                    </div>


                    <div class="perfil-card-body">

                        <form id="form-perfil">

                            @csrf


                            {{-- DATOS EDITABLES --}}
                            <div class="section-label">

                                <i class="edit outline icon"></i>

                                Datos de contacto

                            </div>


                            <div class="form-grid-2">

                                <div class="form-group">

                                    <label
                                        class="form-label"
                                        for="perfil-nombre"
                                    >
                                        Nombre completo *
                                    </label>


                                    <div class="input-icon-wrapper">

                                        <i class="user icon"></i>

                                        <input
                                            id="perfil-nombre"
                                            type="text"
                                            name="name"
                                            class="perfil-input input-with-icon"
                                            value="{{ $user->nombre }}"
                                            required
                                            autocomplete="name"
                                        >

                                    </div>

                                </div>


                                <div class="form-group">

                                    <label
                                        class="form-label"
                                        for="perfil-email"
                                    >
                                        Correo electrónico *
                                    </label>


                                    <div class="input-icon-wrapper">

                                        <i class="envelope icon"></i>

                                        <input
                                            id="perfil-email"
                                            type="email"
                                            name="email"
                                            class="perfil-input input-with-icon"
                                            value="{{ $user->correo }}"
                                            required
                                            autocomplete="email"
                                        >

                                    </div>

                                </div>


                                <div class="form-group">

                                    <label
                                        class="form-label"
                                        for="perfil-telefono"
                                    >
                                        Celular
                                    </label>


                                    <div class="input-icon-wrapper">

                                        <i class="phone icon"></i>

                                        <input
                                            id="perfil-telefono"
                                            type="tel"
                                            inputmode="numeric"
                                            name="telefono"
                                            class="perfil-input input-with-icon"
                                            maxlength="10"
                                            value="{{ $user->celular }}"
                                            autocomplete="tel"
                                        >

                                    </div>

                                </div>

                            </div>


                            {{-- INFORMACIÓN DE CUENTA --}}
                            <div class="section-label">

                                <i class="address card outline icon"></i>

                                Información de la cuenta

                            </div>


                            <div class="account-fields">

                                <div class="readonly-card">

                                    <div class="readonly-icon">
                                        <i class="home icon"></i>
                                    </div>

                                    <div>

                                        <div class="readonly-label">
                                            Casa
                                        </div>

                                        <div class="readonly-value">
                                            {{ $casaUsuario }}
                                        </div>

                                    </div>

                                </div>


                                <div class="readonly-card">

                                    <div class="readonly-icon">
                                        <i class="id badge outline icon"></i>
                                    </div>

                                    <div>

                                        <div class="readonly-label">
                                            Tipo de usuario
                                        </div>

                                        <div class="readonly-value">
                                            {{ $tipoUsuario }}
                                        </div>

                                    </div>

                                </div>

                            </div>


                            {{-- PREFERENCIAS --}}
                            <div class="section-label">

                                <i class="bell outline icon"></i>

                                Preferencias de notificación

                            </div>


                            <div class="preferences">


                                {{-- EMAIL --}}
                                <label
                                    class="
                                        setting-card
                                        perfil-checkbox-card
                                        {{ $user->emails ? 'checked' : '' }}
                                    "
                                >

                                    <input
                                        type="checkbox"
                                        name="emails"
                                        {{ $user->emails ? 'checked' : '' }}
                                    >


                                    <div class="setting-content">

                                        <div class="setting-icon">
                                            <i class="envelope outline icon"></i>
                                        </div>


                                        <div>

                                            <span class="setting-title">
                                                Notificaciones por correo
                                            </span>

                                            <span class="setting-description">
                                                Recibe comunicados y avisos
                                                importantes en tu correo.
                                            </span>

                                        </div>


                                        <span
                                            class="switch"
                                            aria-hidden="true"
                                        ></span>

                                    </div>

                                </label>


                                {{-- PUSH --}}
                                <label
                                    class="
                                        setting-card
                                        perfil-checkbox-card
                                    "
                                    id="push-card"
                                >

                                    <input
                                        type="checkbox"
                                        id="push-toggle"
                                    >


                                    <div class="setting-content">

                                        <div class="setting-icon">
                                            <i class="bell outline icon"></i>
                                        </div>


                                        <div>

                                            <span class="setting-title">
                                                Notificaciones en este dispositivo
                                            </span>

                                            <span
                                                class="setting-description"
                                                id="push-estado-texto"
                                            >
                                                Recibe avisos aunque
                                                la aplicación esté cerrada.
                                            </span>

                                        </div>


                                        <span
                                            class="switch"
                                            aria-hidden="true"
                                        ></span>

                                    </div>

                                </label>


                                <div
                                    class="push-test"
                                    id="push-prueba-wrap"
                                >

                                    <button
                                        type="button"
                                        id="btn-push-prueba"
                                        class="perfil-btn secondary"
                                    >

                                        <i class="paper plane outline icon"></i>

                                        Enviar notificación de prueba

                                    </button>

                                </div>

                            </div>


                            {{-- ACCIONES --}}
                            <div class="perfil-actions">


                                <button
                                    type="button"
                                    class="perfil-btn secondary"
                                    id="btn-cambiar-pass"
                                >

                                    <i class="lock icon"></i>

                                    Cambiar contraseña

                                </button>


                                <button
                                    type="submit"
                                    id="btn-guardar-perfil"
                                    class="perfil-btn primary"
                                >

                                    <i class="save outline icon"></i>

                                    Guardar cambios

                                </button>

                            </div>

                        </form>

                    </div>

                </section>

            </div>


            {{-- =================================================
                 INQUILINO
            ================================================== --}}

            @if($inquilino != null)

                <section class="perfil-card tenant-section">


                    <div class="perfil-card-header">

                        <div class="perfil-card-title-wrap">

                            <div class="perfil-card-icon tenant">

                                <i class="users icon"></i>

                            </div>


                            <div>

                                <h2 class="perfil-card-title">
                                    Inquilino asociado
                                </h2>

                                <p class="perfil-card-subtitle">
                                    Consulta su estado y administra
                                    sus permisos.
                                </p>

                            </div>

                        </div>


                        @if(
                            isset($pendientesCount)
                            &&
                            $pendientesCount > 0
                        )

                            <span
                                class="ui red circular mini label"
                            >
                                {{ $pendientesCount }}
                            </span>

                        @endif

                    </div>


                    <div class="perfil-card-body">


                        <div class="tenant-card-body">


                            {{-- INFORMACIÓN --}}
                            <div class="tenant-profile">


                                <div class="tenant-main-row">

                                    <img
                                        src="{{
                                            $inquilino->foto
                                                ? asset(
                                                    'storage/perfil/'
                                                    .$inquilino->foto
                                                )
                                                : 'https://cdn-icons-png.flaticon.com/512/847/847969.png'
                                        }}"
                                        class="tenant-avatar"
                                        alt="Foto de {{ $inquilino->nombre }}"
                                    >


                                    <div style="min-width:0;">

                                        <h3 class="tenant-name">
                                            {{ $inquilino->nombre }}
                                        </h3>


                                        <div class="tenant-badges">

                                            <span class="tenant-badge type">

                                                <i class="user icon"></i>

                                                {{ $inquilino->tipo }}

                                            </span>


                                            @if($inquilino->estado == 1)

                                                <span class="tenant-badge active">

                                                    <i class="check circle icon"></i>

                                                    Activo

                                                </span>


                                            @elseif($inquilino->estado == 3)

                                                <span class="tenant-badge pending">

                                                    <i class="clock outline icon"></i>

                                                    Pendiente de aprobación

                                                </span>


                                            @else

                                                <span class="tenant-badge inactive">

                                                    <i class="times circle icon"></i>

                                                    Inactivo

                                                </span>

                                            @endif

                                        </div>

                                    </div>

                                </div>


                                <div class="tenant-details">


                                    <div class="tenant-detail">

                                        <i class="envelope outline icon"></i>

                                        <span>
                                            {{ $inquilino->correo ?: 'Sin correo' }}
                                        </span>

                                    </div>


                                    <div class="tenant-detail">

                                        <i class="phone icon"></i>

                                        <span>
                                            {{ $inquilino->celular ?: 'Sin celular' }}
                                        </span>

                                    </div>

                                </div>


                                <div class="tenant-actions">


                                    @if($inquilino->estado == 3)

                                        <button
                                            type="button"
                                            class="
                                                perfil-btn
                                                success
                                                btn-aprobar-inquilino
                                            "
                                            data-id="{{ $inquilino->id }}"
                                        >

                                            <i class="check icon"></i>

                                            Aprobar inquilino

                                        </button>

                                    @endif


                                    @if($inquilino->estado == 1)

                                        <a
                                            href="{{
                                                route(
                                                    'usuario.perfil.solicitudes'
                                                )
                                            }}"
                                            class="perfil-btn info"
                                        >

                                            <i class="file alternate outline icon"></i>

                                            Solicitudes


                                            @if($pendientesCount > 0)

                                                <span
                                                    class="ui red circular mini label"
                                                >
                                                    {{ $pendientesCount }}
                                                </span>

                                            @endif

                                        </a>

                                    @endif


                                    <button
                                        type="button"
                                        class="
                                            perfil-btn
                                            danger
                                            btn-eliminar-inquilino
                                        "
                                        data-id="{{ $inquilino->id }}"
                                    >

                                        <i class="trash alternate outline icon"></i>

                                        Eliminar

                                    </button>

                                </div>

                            </div>


                            {{-- PERMISOS --}}
                            <div class="tenant-permissions">


                                @if($inquilino->estado == 1)

                                    <div>

                                        <div class="section-label">
                                            <i class="key icon"></i>
                                            Permisos
                                        </div>


                                        <label
                                            class="
                                                setting-card
                                                perfil-checkbox-card
                                                compact
                                                {{ $inquilino->pago ? 'checked' : '' }}
                                            "
                                        >

                                            <input
                                                type="checkbox"
                                                class="chk-pago"
                                                data-id="{{ $inquilino->id }}"
                                                {{ $inquilino->pago ? 'checked' : '' }}
                                            >


                                            <div class="setting-content">

                                                <div class="setting-icon">

                                                    <i class="credit card outline icon"></i>

                                                </div>


                                                <div>

                                                    <span class="setting-title">
                                                        Permitir realizar pagos
                                                    </span>

                                                    <span class="setting-description">
                                                        Autoriza al inquilino
                                                        a subir comprobantes de pago.
                                                    </span>

                                                </div>


                                                <span
                                                    class="switch"
                                                    aria-hidden="true"
                                                ></span>

                                            </div>

                                        </label>

                                    </div>


                                    <div class="permission-info">

                                        <div class="permission-info-head">

                                            <i class="info circle icon"></i>

                                            ¿Cómo funciona este permiso?

                                        </div>


                                        <p>

                                            Solo una de las dos cuentas puede
                                            encargarse de subir los pagos de la casa.

                                        </p>


                                        <p class="permission-note">

                                            @if($inquilino->pago)

                                                Actualmente el inquilino
                                                tiene permiso para subir pagos.

                                            @else

                                                Actualmente tú conservas
                                                el control para subir pagos.

                                            @endif

                                        </p>

                                    </div>

                                @else

                                    <div class="permission-info">

                                        <div class="permission-info-head">

                                            <i class="clock outline icon"></i>

                                            Pendiente de activación

                                        </div>

                                        <p>
                                            Los permisos estarán disponibles
                                            cuando el inquilino esté activo.
                                        </p>

                                    </div>

                                @endif

                            </div>

                        </div>

                    </div>

                </section>

            @endif

        </div>

    </div>


    {{-- =========================================================
         MODAL CONTRASEÑA
    ========================================================== --}}

    <div
        class="ui modal"
        id="modal-pass"
    >

        <div class="header perfil-modal-header">

            <i class="lock icon"></i>

            Cambiar contraseña

        </div>


        <div class="content">


            <div class="password-helper">

                <i class="info circle icon"></i>

                Por seguridad, escribe tu contraseña actual
                antes de establecer una nueva.

            </div>


            <form id="form-pass">

                @csrf


                <div class="form-group">

                    <label class="form-label">
                        Contraseña actual *
                    </label>

                    <div class="input-icon-wrapper">

                        <i class="lock icon"></i>

                        <input
                            type="password"
                            name="actual"
                            class="perfil-input input-with-icon"
                            required
                            autocomplete="current-password"
                        >

                    </div>

                </div>


                <div class="form-group">

                    <label class="form-label">
                        Nueva contraseña *
                    </label>

                    <div class="input-icon-wrapper">

                        <i class="key icon"></i>

                        <input
                            type="password"
                            name="nueva"
                            class="perfil-input input-with-icon"
                            required
                            autocomplete="new-password"
                        >

                    </div>

                </div>


                <div class="form-group">

                    <label class="form-label">
                        Confirmar nueva contraseña *
                    </label>

                    <div class="input-icon-wrapper">

                        <i class="check icon"></i>

                        <input
                            type="password"
                            name="confirmacion"
                            class="perfil-input input-with-icon"
                            required
                            autocomplete="new-password"
                        >

                    </div>

                </div>

            </form>

        </div>


        <div class="actions">

            <button
                class="ui deny button"
                type="button"
            >
                Cancelar
            </button>


            <button
                class="ui primary button"
                id="btn-guardar-pass"
                form="form-pass"
                type="submit"
            >

                <i class="check icon"></i>

                Actualizar contraseña

            </button>

        </div>

    </div>


    <script>

        $(document).ready(function () {

            /*
             * ==================================================
             * CHECKBOX VISUAL
             * ==================================================
             */

            function pintarCheckbox($input) {

                const $card =
                    $input.closest(
                        '.perfil-checkbox-card'
                    );


                $card.toggleClass(
                    'checked',
                    $input.is(':checked')
                );

            }


            $('.perfil-checkbox-card input')
                .each(function () {

                    pintarCheckbox(
                        $(this)
                    );

                });


            $('.perfil-checkbox-card input')
                .on(
                    'change.perfilVisual',
                    function () {

                        pintarCheckbox(
                            $(this)
                        );

                    }
                );


            /*
             * ==================================================
             * FOTO
             * ==================================================
             */

            const fileInput =
                document.getElementById(
                    'foto-input'
                );


            const uploadArea =
                document.getElementById(
                    'upload-area-foto'
                );


            if (
                uploadArea
                &&
                fileInput
            ) {

                [
                    'dragenter',
                    'dragover',
                    'dragleave',
                    'drop'
                ]
                    .forEach(
                        function (eventName) {

                            uploadArea
                                .addEventListener(
                                    eventName,
                                    preventDefaults,
                                    false
                                );

                        }
                    );


                function preventDefaults(e) {

                    e.preventDefault();

                    e.stopPropagation();

                }


                [
                    'dragenter',
                    'dragover'
                ]
                    .forEach(
                        function (eventName) {

                            uploadArea
                                .addEventListener(
                                    eventName,
                                    function () {

                                        uploadArea
                                            .classList
                                            .add(
                                                'dragover'
                                            );

                                    },
                                    false
                                );

                        }
                    );


                [
                    'dragleave',
                    'drop'
                ]
                    .forEach(
                        function (eventName) {

                            uploadArea
                                .addEventListener(
                                    eventName,
                                    function () {

                                        uploadArea
                                            .classList
                                            .remove(
                                                'dragover'
                                            );

                                    },
                                    false
                                );

                        }
                    );


                uploadArea
                    .addEventListener(
                        'drop',
                        function (e) {

                            const files =
                                e.dataTransfer.files;


                            if (
                                !files.length
                            ) {
                                return;
                            }


                            /*
                             * Asignación segura del archivo.
                             */
                            try {

                                const dt =
                                    new DataTransfer();


                                dt.items.add(
                                    files[0]
                                );


                                fileInput.files =
                                    dt.files;

                            } catch (error) {

                                /*
                                 * Algunos navegadores móviles
                                 * pueden impedir la asignación.
                                 */
                                console.warn(
                                    error
                                );

                            }


                            previewSelectedPhoto(
                                files[0]
                            );

                        }
                    );


                fileInput
                    .addEventListener(
                        'change',
                        function () {

                            if (
                                this.files.length
                            ) {

                                previewSelectedPhoto(
                                    this.files[0]
                                );

                            }

                        }
                    );

            }


            function previewSelectedPhoto(
                file
            ) {

                if (!file) {
                    return;
                }


                if (
                    !file.type
                        .startsWith(
                            'image/'
                        )
                ) {

                    alertify.error(
                        'Selecciona una imagen válida.'
                    );


                    if (fileInput) {
                        fileInput.value = '';
                    }


                    return;
                }


                if (
                    file.size
                    >
                    5 * 1024 * 1024
                ) {

                    alertify.error(
                        'La imagen no puede superar los 5 MB.'
                    );


                    if (fileInput) {
                        fileInput.value = '';
                    }


                    return;
                }


                const reader =
                    new FileReader();


                reader.onload =
                    function (e) {

                        $('#preview-foto')
                            .attr(
                                'src',
                                e.target.result
                            );

                    };


                reader.readAsDataURL(
                    file
                );

            }


            /*
             * ==================================================
             * ACTUALIZAR FOTO
             * ==================================================
             */

            $('#form-foto')
                .submit(
                    function (e) {

                        e.preventDefault();


                        const btn =
                            $('#btn-guardar-foto');


                        if (
                            !fileInput
                            ||
                            !fileInput.files.length
                        ) {

                            alertify.error(
                                'Selecciona una fotografía.'
                            );

                            return;
                        }


                        btn.addClass(
                            'loading disabled'
                        );


                        const formData =
                            new FormData(
                                this
                            );


                        $.ajax({

                            url:
                                "{{ route('usuario.perfil.actualizarFoto') }}",

                            method:
                                'POST',

                            data:
                                formData,

                            contentType:
                                false,

                            processData:
                                false,


                            success:
                                function (res) {

                                    alertify.success(
                                        res.message
                                    );

                                },


                            error:
                                function (xhr) {

                                    if (
                                        xhr.responseJSON
                                        &&
                                        xhr.responseJSON.errors
                                    ) {

                                        Object.values(
                                            xhr.responseJSON.errors
                                        )
                                            .forEach(
                                                function (err) {

                                                    alertify.error(
                                                        err[0]
                                                    );

                                                }
                                            );

                                    } else {

                                        alertify.error(
                                            xhr.responseJSON?.message
                                            ??
                                            'No fue posible actualizar la fotografía.'
                                        );

                                    }

                                },


                            complete:
                                function () {

                                    btn.removeClass(
                                        'loading disabled'
                                    );

                                }

                        });

                    }
                );


            /*
             * ==================================================
             * GUARDAR PERFIL
             * ==================================================
             */

            $('#form-perfil')
                .submit(
                    function (e) {

                        e.preventDefault();


                        const btn =
                            $('#btn-guardar-perfil');


                        btn.addClass(
                            'loading disabled'
                        );


                        $.ajax({

                            url:
                                "{{ route('usuario.perfil.actualizarDatos') }}",

                            method:
                                'POST',

                            data:
                                $(this).serialize(),


                            success:
                                function (res) {

                                    alertify.alert(
                                        res.header
                                        ?? 'Perfil actualizado',

                                        res.message
                                        ?? 'Los cambios se guardaron correctamente.'
                                    );

                                },


                            error:
                                function (xhr) {

                                    if (
                                        xhr.responseJSON
                                        ?.errors
                                    ) {

                                        Object.values(
                                            xhr.responseJSON.errors
                                        )
                                            .forEach(
                                                function (err) {

                                                    alertify.error(
                                                        err[0]
                                                    );

                                                }
                                            );

                                    } else {

                                        alertify.error(
                                            xhr.responseJSON?.message
                                            ??
                                            'Error al guardar los cambios.'
                                        );

                                    }

                                },


                            complete:
                                function () {

                                    btn.removeClass(
                                        'loading disabled'
                                    );

                                }

                        });

                    }
                );


            /*
             * ==================================================
             * PASSWORD
             * ==================================================
             */

            $('#btn-cambiar-pass')
                .on(
                    'click',
                    function () {

                        $('#modal-pass')
                            .modal({
                                closable: true,
                                autofocus: false
                            })
                            .modal(
                                'show'
                            );

                    }
                );


            $('#form-pass')
                .submit(
                    function (e) {

                        e.preventDefault();


                        const btn =
                            $('#btn-guardar-pass');


                        btn.addClass(
                            'loading disabled'
                        );


                        $.ajax({

                            url:
                                "{{ route('usuario.perfil.actualizarPassword') }}",

                            method:
                                'POST',

                            data:
                                $(this).serialize(),


                            success:
                                function (res) {

                                    alertify.success(
                                        res.message
                                    );


                                    $('#modal-pass')
                                        .modal(
                                            'hide'
                                        );


                                    document
                                        .getElementById(
                                            'form-pass'
                                        )
                                        .reset();

                                },


                            error:
                                function (xhr) {

                                    if (
                                        !xhr.responseJSON
                                    ) {

                                        alertify.error(
                                            'Ocurrió un error inesperado.'
                                        );

                                    } else if (
                                        xhr.responseJSON.errors
                                    ) {

                                        Object.values(
                                            xhr.responseJSON.errors
                                        )
                                            .forEach(
                                                function (err) {

                                                    alertify.error(
                                                        err[0]
                                                    );

                                                }
                                            );

                                    } else {

                                        alertify.error(
                                            xhr.responseJSON.message
                                            ??
                                            'No fue posible actualizar la contraseña.'
                                        );

                                    }

                                },


                            complete:
                                function () {

                                    btn.removeClass(
                                        'loading disabled'
                                    );

                                }

                        });

                    }
                );


            /*
             * ==================================================
             * PERMISO DE PAGOS DEL INQUILINO
             * ==================================================
             */

            let isSubmittingPago =
                false;


            $('.chk-pago')
                .on(
                    'change.perfilPago',
                    function () {

                        const $input =
                            $(this);


                        if (
                            isSubmittingPago
                        ) {
                            return;
                        }


                        const nuevoValor =
                            $input.is(':checked');


                        const valorAnterior =
                            !nuevoValor;


                        const inquilinoId =
                            $input.data(
                                'id'
                            );


                        isSubmittingPago =
                            true;


                        $.ajax({

                            url:
                                "{{ route('usuario.perfil.actualizarPago') }}",

                            method:
                                'POST',

                            data: {

                                _token:
                                    "{{ csrf_token() }}",

                                pago:
                                    nuevoValor
                                        ? 1
                                        : 0,

                                inquilino_id:
                                    inquilinoId

                            },


                            success:
                                function (res) {

                                    alertify.success(
                                        res.message
                                    );

                                },


                            error:
                                function (xhr) {

                                    /*
                                     * Revertimos SIN disparar
                                     * nuevamente la petición AJAX.
                                     */
                                    $input.prop(
                                        'checked',
                                        valorAnterior
                                    );


                                    pintarCheckbox(
                                        $input
                                    );


                                    alertify.error(
                                        xhr.responseJSON?.message
                                        ??
                                        'No fue posible actualizar el permiso.'
                                    );

                                },


                            complete:
                                function () {

                                    isSubmittingPago =
                                        false;

                                }

                        });

                    }
                );


            /*
             * ==================================================
             * APROBAR INQUILINO
             * ==================================================
             */

            $(document)
                .on(
                    'click',
                    '.btn-aprobar-inquilino',
                    function () {

                        const btn =
                            $(this);


                        const id =
                            btn.data(
                                'id'
                            );


                        alertify.confirm(

                            'Aprobar inquilino',

                            '¿Deseas aprobar a este inquilino y activar su acceso?',

                            function () {

                                btn.addClass(
                                    'loading disabled'
                                );


                                $.ajax({

                                    url:
                                        "{{
                                            route(
                                                'usuario.perfil.aprobarInquilino',
                                                ':id'
                                            )
                                        }}"
                                        .replace(
                                            ':id',
                                            id
                                        ),

                                    method:
                                        'POST',

                                    data: {
                                        _token:
                                            "{{ csrf_token() }}"
                                    },


                                    success:
                                        function (res) {

                                            alertify.alert(

                                                'Inquilino aprobado',

                                                res.message,

                                                function () {

                                                    location.reload();

                                                }

                                            );

                                        },


                                    error:
                                        function (xhr) {

                                            alertify.error(
                                                xhr.responseJSON?.message
                                                ??
                                                'Error al aprobar al inquilino.'
                                            );

                                        },


                                    complete:
                                        function () {

                                            btn.removeClass(
                                                'loading disabled'
                                            );

                                        }

                                });

                            },

                            function () {}

                        );

                    }
                );


            /*
             * ==================================================
             * ELIMINAR INQUILINO
             * ==================================================
             */

            $(document)
                .on(
                    'click',
                    '.btn-eliminar-inquilino',
                    function () {

                        const btn =
                            $(this);


                        const id =
                            btn.data(
                                'id'
                            );


                        alertify.confirm(

                            'Eliminar inquilino',

                            'Esta acción no se puede deshacer. ¿Deseas eliminar al inquilino asociado?',

                            function () {

                                btn.addClass(
                                    'loading disabled'
                                );


                                $.ajax({

                                    url:
                                        "{{
                                            route(
                                                'usuario.perfil.eliminarInquilino',
                                                ':id'
                                            )
                                        }}"
                                        .replace(
                                            ':id',
                                            id
                                        ),

                                    method:
                                        'DELETE',

                                    data: {
                                        _token:
                                            "{{ csrf_token() }}"
                                    },


                                    success:
                                        function (res) {

                                            alertify.alert(

                                                'Inquilino eliminado',

                                                res.message,

                                                function () {

                                                    location.reload();

                                                }

                                            );

                                        },


                                    error:
                                        function (xhr) {

                                            alertify.error(
                                                xhr.responseJSON?.message
                                                ??
                                                'Error al eliminar al inquilino.'
                                            );

                                        },


                                    complete:
                                        function () {

                                            btn.removeClass(
                                                'loading disabled'
                                            );

                                        }

                                });

                            },

                            function () {}

                        );

                    }
                );


            /*
             * ==================================================
             * WEB PUSH
             * ==================================================
             */

            (function () {

                const $toggle =
                    $('#push-toggle');


                const $card =
                    $('#push-card');


                const $texto =
                    $('#push-estado-texto');


                const $prueba =
                    $('#push-prueba-wrap');


                if (
                    !$toggle.length
                ) {
                    return;
                }


                function pintar(
                    activo
                ) {

                    $toggle.prop(
                        'checked',
                        activo
                    );


                    $card.toggleClass(
                        'checked',
                        activo
                    );


                    $prueba.toggle(
                        !!activo
                    );

                }


                function bloquear(
                    valor
                ) {

                    $toggle.prop(
                        'disabled',
                        valor
                    );


                    $card.css(
                        'opacity',
                        valor
                            ? .62
                            : 1
                    );

                }


                /*
                 * Sin AppPush.
                 */
                if (
                    typeof window.AppPush
                    ===
                    'undefined'
                ) {

                    pintar(
                        false
                    );


                    bloquear(
                        true
                    );


                    $texto.text(
                        'Las notificaciones no están disponibles en este dispositivo.'
                    );


                    return;
                }


                /*
                 * Botón prueba.
                 */
                $('#btn-push-prueba')
                    .on(
                        'click',
                        function () {

                            const $btn =
                                $(this);


                            $btn.addClass(
                                'loading disabled'
                            );


                            $.ajax({

                                url:
                                    "{{ route('usuario.perfil.push.probar') }}",

                                method:
                                    'POST',

                                data: {
                                    _token:
                                        "{{ csrf_token() }}"
                                },


                                success:
                                    function (res) {

                                        if (
                                            res.success
                                        ) {

                                            alertify.success(
                                                res.message
                                            );

                                        } else {

                                            alertify.error(
                                                res.message
                                                ??
                                                'No se pudo enviar la prueba.'
                                            );

                                        }

                                    },


                                error:
                                    function () {

                                        alertify.error(
                                            'No se pudo enviar la notificación de prueba.'
                                        );

                                    },


                                complete:
                                    function () {

                                        $btn.removeClass(
                                            'loading disabled'
                                        );

                                    }

                            });

                        }
                    );


                /*
                 * Estado inicial.
                 */
                (async function initPush() {

                    if (
                        !window.AppPush.soportado()
                    ) {

                        pintar(
                            false
                        );


                        bloquear(
                            true
                        );


                        $texto.text(
                            'Este navegador no soporta notificaciones.'
                        );


                        return;
                    }


                    if (
                        Notification.permission
                        ===
                        'denied'
                    ) {

                        pintar(
                            false
                        );


                        bloquear(
                            true
                        );


                        $texto.text(
                            'Las notificaciones están bloqueadas en los ajustes del navegador.'
                        );


                        return;
                    }


                    try {

                        const activo =
                            await window
                                .AppPush
                                .estaSuscrito();


                        pintar(
                            activo
                        );


                        $texto.text(
                            activo
                                ? 'Activadas en este dispositivo.'
                                : 'Recibe avisos aunque la aplicación esté cerrada.'
                        );

                    } catch (error) {

                        pintar(
                            false
                        );

                    }

                })();


                /*
                 * Activar / desactivar.
                 */
                $toggle
                    .on(
                        'change.push',
                        async function () {

                            const quiereActivar =
                                $(this)
                                    .is(
                                        ':checked'
                                    );


                            bloquear(
                                true
                            );


                            try {

                                if (
                                    quiereActivar
                                ) {

                                    await window
                                        .AppPush
                                        .activar();


                                    pintar(
                                        true
                                    );


                                    $texto.text(
                                        'Activadas en este dispositivo.'
                                    );


                                    alertify.success(
                                        'Notificaciones activadas.'
                                    );

                                } else {

                                    await window
                                        .AppPush
                                        .desactivar();


                                    pintar(
                                        false
                                    );


                                    $texto.text(
                                        'Recibe avisos aunque la aplicación esté cerrada.'
                                    );


                                    alertify.success(
                                        'Notificaciones desactivadas.'
                                    );

                                }

                            } catch (error) {

                                /*
                                 * Regresar al estado anterior.
                                 */
                                pintar(
                                    !quiereActivar
                                );


                                alertify.error(
                                    error.message
                                    ??
                                    'No se pudo cambiar la configuración.'
                                );

                            } finally {

                                bloquear(
                                    false
                                );

                            }

                        }
                    );

            })();

        });

    </script>

</x-app-layout>