<script>
    window.addEventListener('load', function () {
        document.querySelectorAll('.module-card').forEach(card => {
            card.classList.remove('loading');
        });
    });
</script>

<x-app-layout>
    <style>
        :root {
            --primary-start: #667eea;
            --primary-end: #764ba2;

            --admin-start: #f59e0b;
            --admin-end: #d97706;

            --success-start: #22c55e;
            --success-end: #16a34a;

            --info-start: #3b82f6;
            --info-end: #2563eb;

            --text-main: #0f172a;
            --text-muted: #64748b;
            --text-soft: #94a3b8;

            --surface: #ffffff;
            --surface-soft: #f8fafc;
            --surface-muted: #f1f5f9;

            --border-soft: rgba(148, 163, 184, .24);

            --shadow-xs: 0 2px 8px rgba(15, 23, 42, .05);
            --shadow-soft: 0 10px 30px rgba(15, 23, 42, .08);
            --shadow-hover: 0 14px 34px rgba(15, 23, 42, .13);

            --radius-xl: 24px;
            --radius-lg: 18px;
            --radius-md: 14px;
        }

        * {
            box-sizing: border-box;
        }

        .dashboard {
            max-width: 1180px;
            margin: 0 auto;
            padding: 8px 4px 34px;
        }

        /* =========================================
           HERO
        ========================================= */

        .dashboard-hero {
            position: relative;
            overflow: hidden;

            background:
                radial-gradient(
                    circle at 92% 15%,
                    rgba(255, 255, 255, .22),
                    transparent 25%
                ),
                linear-gradient(
                    135deg,
                    var(--primary-start),
                    var(--primary-end)
                );

            color: white;
            border-radius: var(--radius-xl);
            padding: 28px 30px;
            margin-bottom: 18px;
            box-shadow: var(--shadow-soft);
        }

        .dashboard-hero::after {
            content: '';
            position: absolute;

            width: 220px;
            height: 220px;

            border-radius: 50%;

            right: -80px;
            bottom: -120px;

            background: rgba(255, 255, 255, .09);
        }

        .hero-content {
            position: relative;
            z-index: 2;
            max-width: 700px;
        }

        .hero-eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 7px;

            padding: 6px 10px;
            margin-bottom: 10px;

            border-radius: 999px;

            background: rgba(255, 255, 255, .14);
            border: 1px solid rgba(255, 255, 255, .18);

            font-size: .76rem;
            font-weight: 800;
        }

        .dashboard-hero h1 {
            margin: 0;

            font-size: clamp(1.45rem, 3vw, 2.1rem);
            font-weight: 900;
            line-height: 1.1;
            letter-spacing: -.035em;
        }

        .dashboard-hero p {
            margin: 8px 0 0;
            max-width: 600px;

            font-size: .96rem;
            line-height: 1.5;

            opacity: .92;
        }

        .hero-icon {
            position: absolute;

            right: 35px;
            top: 50%;

            transform: translateY(-50%);

            font-size: 5rem !important;
            opacity: .16;

            pointer-events: none;
        }

        /* =========================================
           BUSCADOR
        ========================================= */

        .module-search-wrapper {
            position: relative;
            z-index: 5;

            margin: -2px 0 24px;
        }

        .module-search {
            position: relative;
        }

        .module-search i {
            position: absolute;

            left: 17px;
            top: 50%;

            transform: translateY(-50%);

            color: var(--text-soft);

            margin: 0 !important;
            z-index: 2;
        }

        .module-search input {
            width: 100%;
            height: 52px;

            padding: 0 48px 0 48px;

            border: 1px solid var(--border-soft);
            border-radius: 16px;

            background: white;

            color: var(--text-main);
            font-size: .94rem;
            font-weight: 600;

            outline: none;

            box-shadow: var(--shadow-xs);

            transition:
                border-color .2s ease,
                box-shadow .2s ease;
        }

        .module-search input:focus {
            border-color: rgba(102, 126, 234, .58);

            box-shadow:
                0 0 0 4px rgba(102, 126, 234, .10),
                var(--shadow-xs);
        }

        .module-search input::placeholder {
            color: var(--text-soft);
        }

        .search-clear {
            position: absolute;

            right: 12px;
            top: 50%;

            transform: translateY(-50%);

            width: 32px;
            height: 32px;

            border: 0;
            border-radius: 9px;

            background: transparent;
            color: var(--text-muted);

            cursor: pointer;

            display: none;
            align-items: center;
            justify-content: center;
        }

        .search-clear:hover {
            background: var(--surface-muted);
        }

        /* =========================================
           HEADERS
        ========================================= */

        .section-heading {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;

            gap: 12px;

            margin-bottom: 12px;
        }

        .section-heading-main {
            min-width: 0;
        }

        .section-eyebrow {
            margin-bottom: 3px;

            color: var(--primary-start);

            font-size: .7rem;
            font-weight: 900;

            text-transform: uppercase;
            letter-spacing: .08em;
        }

        .section-title {
            margin: 0;

            color: var(--text-main);

            font-size: 1.05rem;
            font-weight: 900;

            letter-spacing: -.02em;
        }

        .section-description {
            margin-top: 3px;

            color: var(--text-muted);

            font-size: .84rem;
            line-height: 1.4;
        }

        /* =========================================
           ACCESOS FRECUENTES
        ========================================= */

        .quick-grid {
            display: grid;

            grid-template-columns: repeat(4, 1fr);

            gap: 12px;

            margin-bottom: 28px;
        }

        .quick-card {
            position: relative;

            min-height: 96px;

            display: flex;
            align-items: center;

            gap: 13px;

            padding: 15px;

            border: 1px solid var(--border-soft);
            border-radius: var(--radius-lg);

            background: white;

            box-shadow: var(--shadow-xs);

            text-decoration: none !important;

            overflow: hidden;

            transition:
                transform .2s ease,
                box-shadow .2s ease,
                border-color .2s ease;
        }

        .quick-card:hover,
        .quick-card:focus-visible {
            transform: translateY(-3px);

            border-color: rgba(102, 126, 234, .34);

            box-shadow: var(--shadow-hover);

            outline: none;
        }

        .quick-icon {
            width: 48px;
            height: 48px;

            border-radius: 14px;

            display: flex;
            align-items: center;
            justify-content: center;

            flex-shrink: 0;

            color: white;

            background:
                linear-gradient(
                    135deg,
                    var(--primary-start),
                    var(--primary-end)
                );

            box-shadow:
                0 8px 20px rgba(102, 126, 234, .20);
        }

        .quick-icon i {
            margin: 0 !important;
            font-size: 1.3rem !important;
        }

        .quick-content {
            min-width: 0;
        }

        .quick-title {
            color: var(--text-main);

            font-size: .91rem;
            font-weight: 900;

            line-height: 1.2;
        }

        .quick-text {
            margin-top: 3px;

            color: var(--text-muted);

            font-size: .73rem;

            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* =========================================
           GRUPOS
        ========================================= */

        .modules-container {
            display: flex;
            flex-direction: column;

            gap: 24px;
        }

        .module-group {
            min-width: 0;
        }

        .module-group-header {
            display: flex;
            align-items: center;

            gap: 9px;

            margin-bottom: 10px;
        }

        .group-icon {
            width: 30px;
            height: 30px;

            display: flex;
            align-items: center;
            justify-content: center;

            border-radius: 9px;

            background: rgba(102, 126, 234, .10);

            color: var(--primary-start);

            flex-shrink: 0;
        }

        .group-icon i {
            margin: 0 !important;
        }

        .group-title {
            color: var(--text-main);

            font-size: .9rem;
            font-weight: 900;
        }

        .module-grid {
            display: grid;

            grid-template-columns:
                repeat(auto-fill, minmax(185px, 1fr));

            gap: 10px;
        }

        /* =========================================
           MÓDULOS
        ========================================= */

        .module-card {
            min-width: 0;
            min-height: 74px;

            display: flex;
            align-items: center;

            gap: 12px;

            padding: 11px 13px;

            border: 1px solid var(--border-soft);
            border-radius: 15px;

            background: white;

            box-shadow: var(--shadow-xs);

            text-decoration: none !important;

            transition:
                transform .18s ease,
                box-shadow .18s ease,
                border-color .18s ease,
                background .18s ease;
        }

        .module-card:hover,
        .module-card:focus-visible {
            transform: translateY(-2px);

            border-color: rgba(102, 126, 234, .34);

            background: #fff;

            box-shadow: 0 9px 24px rgba(15, 23, 42, .10);

            outline: none;
        }

        .module-icon {
            width: 42px;
            height: 42px;

            flex-shrink: 0;

            display: flex;
            align-items: center;
            justify-content: center;

            border-radius: 12px;

            color: white;

            background:
                linear-gradient(
                    135deg,
                    var(--primary-start),
                    var(--primary-end)
                );

            box-shadow:
                0 6px 14px rgba(102, 126, 234, .18);
        }

        .module-icon i {
            margin: 0 !important;
            font-size: 1.05rem !important;
        }

        .module-content {
            flex: 1;
            min-width: 0;
        }

        .module-title {
            color: var(--text-main);

            font-size: .84rem;
            font-weight: 900;

            line-height: 1.2;
        }

        .module-arrow {
            color: #cbd5e1;

            margin-left: auto;

            transition:
                transform .18s ease,
                color .18s ease;
        }

        .module-arrow i {
            margin: 0 !important;
        }

        .module-card:hover .module-arrow {
            color: var(--primary-start);
            transform: translateX(2px);
        }

        /* Variantes */

        .module-card.admin-module .module-icon {
            background:
                linear-gradient(
                    135deg,
                    var(--admin-start),
                    var(--admin-end)
                );

            box-shadow:
                0 6px 14px rgba(245, 158, 11, .20);
        }

        .module-card.admin-module:hover {
            border-color: rgba(245, 158, 11, .38);
        }

        .module-icon.green {
            background:
                linear-gradient(
                    135deg,
                    #14b8a6,
                    #0f766e
                );
        }

        .module-icon.blue {
            background:
                linear-gradient(
                    135deg,
                    #3b82f6,
                    #2563eb
                );
        }

        .module-icon.orange {
            background:
                linear-gradient(
                    135deg,
                    #f59e0b,
                    #d97706
                );
        }

        .module-icon.purple {
            background:
                linear-gradient(
                    135deg,
                    #8b5cf6,
                    #7c3aed
                );
        }

        /* =========================================
           RESULTADO BÚSQUEDA
        ========================================= */

        .no-results {
            display: none;

            padding: 30px 18px;

            border: 1px dashed rgba(148, 163, 184, .45);
            border-radius: 18px;

            background: var(--surface-soft);

            text-align: center;

            color: var(--text-muted);

            margin-top: 12px;
        }

        .no-results i {
            display: block;

            margin: 0 0 10px !important;

            font-size: 2rem !important;

            color: var(--text-soft);
        }

        .no-results strong {
            display: block;

            color: var(--text-main);

            margin-bottom: 4px;
        }

        /* =========================================
           MESA DIRECTIVA
        ========================================= */

        .board-section {
            margin-top: 32px;

            padding: 21px;

            border: 1px solid var(--border-soft);
            border-radius: var(--radius-xl);

            background:
                linear-gradient(
                    180deg,
                    rgba(255,255,255,.98),
                    rgba(248,250,252,.92)
                );

            box-shadow: var(--shadow-xs);
        }

        .board-list {
            display: grid;

            grid-template-columns:
                repeat(auto-fill, minmax(285px, 1fr));

            gap: 11px;

            margin-top: 14px;
        }

        .board-member {
            display: flex;
            align-items: center;

            gap: 12px;

            padding: 13px;

            border: 1px solid var(--border-soft);
            border-radius: 16px;

            background: white;
        }

        .board-avatar,
        .board-initial {
            width: 45px !important;
            height: 45px !important;

            border-radius: 50%;

            flex-shrink: 0;
        }

        .board-avatar {
            object-fit: cover;
        }

        .board-initial {
            display: flex;
            align-items: center;
            justify-content: center;

            background:
                linear-gradient(
                    135deg,
                    var(--primary-start),
                    var(--primary-end)
                );

            color: white;

            font-weight: 900;
            font-size: 1rem;

            text-transform: uppercase;
        }

        .board-content {
            flex: 1;
            min-width: 0;
        }

        .board-name {
            color: var(--text-main);

            font-size: .88rem;
            font-weight: 900;

            line-height: 1.2;
        }

        .board-house {
            margin-top: 4px;

            color: var(--text-muted);

            font-size: .74rem;
            font-weight: 700;
        }

        .board-actions {
            display: flex;

            gap: 6px;

            margin-left: auto;
        }

        .board-action {
            width: 36px;
            height: 36px;

            display: flex;
            align-items: center;
            justify-content: center;

            border-radius: 11px;

            color: white !important;

            text-decoration: none !important;

            transition: transform .18s ease;
        }

        .board-action:hover {
            transform: translateY(-2px);
        }

        .board-action i {
            margin: 0 !important;
        }

        .board-action.phone {
            background:
                linear-gradient(
                    135deg,
                    var(--info-start),
                    var(--info-end)
                );
        }

        .board-action.whatsapp {
            background:
                linear-gradient(
                    135deg,
                    var(--success-start),
                    var(--success-end)
                );
        }

        .board-empty {
            padding: 22px;

            text-align: center;

            color: var(--text-muted);

            font-weight: 700;

            border: 1px dashed rgba(148, 163, 184, .4);
            border-radius: 16px;

            background: var(--surface-soft);
        }

        /* =========================================
           TABLET
        ========================================= */

        @media (max-width: 900px) {
            .quick-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .module-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        /* =========================================
           MÓVIL
        ========================================= */

        @media (max-width: 640px) {
            .dashboard {
                padding: 0 1px 24px;
            }

            .dashboard-hero {
                padding: 21px 20px;

                margin-bottom: 12px;

                border-radius: 20px;
            }

            .dashboard-hero p {
                font-size: .84rem;

                max-width: 95%;
            }

            .hero-eyebrow {
                font-size: .69rem;
            }

            .hero-icon {
                display: none;
            }

            .module-search-wrapper {
                margin-bottom: 19px;
            }

            .module-search input {
                height: 48px;
            }

            .quick-grid {
                grid-template-columns: repeat(2, 1fr);

                gap: 8px;

                margin-bottom: 24px;
            }

            .quick-card {
                min-height: 72px;

                gap: 9px;

                padding: 10px;

                border-radius: 14px;
            }

            .quick-icon {
                width: 39px;
                height: 39px;

                border-radius: 11px;
            }

            .quick-icon i {
                font-size: 1.05rem !important;
            }

            .quick-title {
                font-size: .8rem;
            }

            .quick-text {
                display: none;
            }

            .section-description {
                font-size: .78rem;
            }

            .modules-container {
                gap: 20px;
            }

            /*
             * La mejora más importante:
             * una sola columna, pero cada módulo mide
             * aproximadamente 58-60px, no 138px.
             */
            .module-grid {
                display: grid;
                grid-template-columns: 1fr;

                gap: 6px;
            }

            .module-card {
                min-height: 58px;

                padding: 8px 10px;

                border-radius: 13px;
            }

            .module-icon {
                width: 38px;
                height: 38px;

                border-radius: 10px;
            }

            .module-title {
                font-size: .82rem;
            }

            .module-group-header {
                margin-bottom: 7px;
            }

            .group-icon {
                width: 28px;
                height: 28px;
            }

            .board-section {
                margin-top: 26px;

                padding: 15px;

                border-radius: 19px;
            }

            .board-list {
                grid-template-columns: 1fr;

                gap: 8px;
            }

            .board-member {
                padding: 10px;
            }

            .board-avatar,
            .board-initial {
                width: 40px !important;
                height: 40px !important;
            }

            .board-action {
                width: 34px;
                height: 34px;
            }
        }
    </style>

    <div class="dashboard">

        {{-- =========================================
             BIENVENIDA
        ========================================== --}}
        <section class="dashboard-hero">
            <div class="hero-content">

                <div class="hero-eyebrow">
                    <i class="home icon"></i>
                    Mi condominio
                </div>

                <h1>
                    ¡Hola, {{ Auth::user()->nombre }}!
                </h1>

                <p>
                    Encuentra rápidamente servicios, pagos,
                    reservas, documentos y herramientas de tu comunidad.
                </p>

            </div>
        </section>


        {{-- =========================================
             BUSCADOR
        ========================================== --}}
        <div class="module-search-wrapper">

            <div class="module-search">

                <i class="search icon"></i>

                <input
                    type="search"
                    id="moduleSearch"
                    placeholder="¿Qué necesitas? Ej. pagos, reservas, mascota..."
                    autocomplete="off"
                    aria-label="Buscar módulo"
                >

                <button
                    type="button"
                    class="search-clear"
                    id="clearSearch"
                    aria-label="Limpiar búsqueda"
                >
                    <i class="times icon"></i>
                </button>

            </div>

        </div>


        {{-- =========================================
             ACCESOS FRECUENTES
        ========================================== --}}
        <div class="section-heading">
            <div class="section-heading-main">
                <div class="section-eyebrow">
                    Acceso inmediato
                </div>

                <h2 class="section-title">
                    Lo más utilizado
                </h2>
            </div>
        </div>


        <div class="quick-grid">

            <a
                href="{{ route('usuario.pago.index') }}"
                class="quick-card"
            >
                <div class="quick-icon">
                    <i class="money bill alternate outline icon"></i>
                </div>

                <div class="quick-content">
                    <div class="quick-title">
                        Pagos
                    </div>

                    <div class="quick-text">
                        Consulta y administra pagos
                    </div>
                </div>
            </a>


            <a
                href="{{ route('usuario.reserva.index') }}"
                class="quick-card"
            >
                <div class="quick-icon">
                    <i class="calendar plus outline icon"></i>
                </div>

                <div class="quick-content">
                    <div class="quick-title">
                        Reservar
                    </div>

                    <div class="quick-text">
                        Reserva áreas comunes
                    </div>
                </div>
            </a>


            <a
                href="{{ route('usuario.comunicados.index') }}"
                class="quick-card"
            >
                <div class="quick-icon">
                    <i class="bullhorn icon"></i>
                </div>

                <div class="quick-content">
                    <div class="quick-title">
                        Comunicados
                    </div>

                    <div class="quick-text">
                        Noticias de tu comunidad
                    </div>
                </div>
            </a>


            <a
                href="{{ route('usuario.reportes.index') }}"
                class="quick-card"
            >
                <div class="quick-icon">
                    <i class="clipboard list icon"></i>
                </div>

                <div class="quick-content">
                    <div class="quick-title">
                        Reportes
                    </div>

                    <div class="quick-text">
                        Reporta una situación
                    </div>
                </div>
            </a>

        </div>


        {{-- =========================================
             TODOS LOS MÓDULOS
        ========================================== --}}
        <div class="section-heading">

            <div class="section-heading-main">

                <div class="section-eyebrow">
                    Servicios
                </div>

                <h2 class="section-title">
                    Todos los módulos
                </h2>

                <div class="section-description">
                    Organizados por categoría para encontrarlos más rápido.
                </div>

            </div>

        </div>


        <div
            class="modules-container"
            id="modulesContainer"
        >

            {{-- =====================================
                 ADMINISTRACIÓN
            ====================================== --}}

            @if(
                Auth::user()->rol === 'administrador'
                || Auth::user()->rol === 'super-administrador'
            )

                <div class="module-group">

                    <div class="module-group-header">

                        <div class="group-icon">
                            <i class="shield alternate icon"></i>
                        </div>

                        <div class="group-title">
                            Administración
                        </div>

                    </div>

                    <div class="module-grid">

                        <a
                            href="{{ route('admin.inicio.index') }}"
                            class="module-card admin-module"
                            data-search="administracion administrador configuracion"
                        >
                            <div class="module-icon">
                                <i class="settings icon"></i>
                            </div>

                            <div class="module-content">
                                <div class="module-title">
                                    Administración
                                </div>
                            </div>

                            <div class="module-arrow">
                                <i class="angle right icon"></i>
                            </div>
                        </a>

                    </div>

                </div>

            @endif


            {{-- =====================================
                 MI CUENTA
            ====================================== --}}

            <div class="module-group">

                <div class="module-group-header">

                    <div class="group-icon">
                        <i class="user outline icon"></i>
                    </div>

                    <div class="group-title">
                        Mi cuenta
                    </div>

                </div>

                <div class="module-grid">

                    <a
                        href="{{ route('usuario.perfil.index') }}"
                        class="module-card"
                        data-search="perfil mi perfil cuenta usuario datos"
                    >
                        <div class="module-icon">
                            <i class="user outline icon"></i>
                        </div>

                        <div class="module-content">
                            <div class="module-title">
                                Mi perfil
                            </div>
                        </div>

                        <div class="module-arrow">
                            <i class="angle right icon"></i>
                        </div>
                    </a>


                    <a
                        href="{{ route('usuario.pago.index') }}"
                        class="module-card"
                        data-search="pagos pago cuotas mantenimiento dinero"
                    >
                        <div class="module-icon">
                            <i class="money bill alternate outline icon"></i>
                        </div>

                        <div class="module-content">
                            <div class="module-title">
                                Pagos
                            </div>
                        </div>

                        <div class="module-arrow">
                            <i class="angle right icon"></i>
                        </div>
                    </a>


                    <a
                        href="{{ route('usuario.documentos.index') }}"
                        class="module-card"
                        data-search="documentos archivos reglamento pdf"
                    >
                        <div class="module-icon">
                            <i class="folder open outline icon"></i>
                        </div>

                        <div class="module-content">
                            <div class="module-title">
                                Documentos
                            </div>
                        </div>

                        <div class="module-arrow">
                            <i class="angle right icon"></i>
                        </div>
                    </a>

                </div>

            </div>


            {{-- =====================================
                 RESERVAS
            ====================================== --}}

            <div class="module-group">

                <div class="module-group-header">

                    <div class="group-icon">
                        <i class="calendar alternate outline icon"></i>
                    </div>

                    <div class="group-title">
                        Reservas y espacios
                    </div>

                </div>

                <div class="module-grid">

                    <a
                        href="{{ route('usuario.reserva.index') }}"
                        class="module-card"
                        data-search="reservar reserva areas amenidades espacios"
                    >
                        <div class="module-icon">
                            <i class="calendar plus outline icon"></i>
                        </div>

                        <div class="module-content">
                            <div class="module-title">
                                Reservar
                            </div>
                        </div>

                        <div class="module-arrow">
                            <i class="angle right icon"></i>
                        </div>
                    </a>


                    <a
                        href="{{ route('usuario.reserva.misReservas') }}"
                        class="module-card"
                        data-search="reservaciones mis reservas historial"
                    >
                        <div class="module-icon">
                            <i class="calendar check outline icon"></i>
                        </div>

                        <div class="module-content">
                            <div class="module-title">
                                reservaciones
                            </div>
                        </div>

                        <div class="module-arrow">
                            <i class="angle right icon"></i>
                        </div>
                    </a>


                    <a
                        href="{{ route('usuario.estacionamiento.index') }}"
                        class="module-card"
                        data-search="estacionamiento escalera comunitaria espacios"
                    >
                        <div class="module-icon blue">
                            <i class="parking icon"></i>
                        </div>

                        <div class="module-content">
                            <div class="module-title">
                                Estacionamiento y escalera
                            </div>
                        </div>

                        <div class="module-arrow">
                            <i class="angle right icon"></i>
                        </div>
                    </a>

                </div>

            </div>


            {{-- =====================================
                 COMUNIDAD
            ====================================== --}}

            <div class="module-group">

                <div class="module-group-header">

                    <div class="group-icon">
                        <i class="users icon"></i>
                    </div>

                    <div class="group-title">
                        Comunidad
                    </div>

                </div>

                <div class="module-grid">

                    <a
                        href="{{ route('usuario.comunicados.index') }}"
                        class="module-card"
                        data-search="comunicados avisos noticias anuncios"
                    >
                        <div class="module-icon">
                            <i class="bullhorn icon"></i>
                        </div>

                        <div class="module-content">
                            <div class="module-title">
                                Comunicados
                            </div>
                        </div>

                        <div class="module-arrow">
                            <i class="angle right icon"></i>
                        </div>
                    </a>


                    <a
                        href="{{ route('usuario.contacto.index') }}"
                        class="module-card"
                        data-search="contactos telefonos ayuda contacto"
                    >
                        <div class="module-icon">
                            <i class="address book outline icon"></i>
                        </div>

                        <div class="module-content">
                            <div class="module-title">
                                Contactos
                            </div>
                        </div>

                        <div class="module-arrow">
                            <i class="angle right icon"></i>
                        </div>
                    </a>


                    <a
                        href="{{ route('usuario.vecino.index') }}"
                        class="module-card"
                        data-search="vecinos comunidad residentes casas"
                    >
                        <div class="module-icon">
                            <i class="users icon"></i>
                        </div>

                        <div class="module-content">
                            <div class="module-title">
                                Vecinos
                            </div>
                        </div>

                        <div class="module-arrow">
                            <i class="angle right icon"></i>
                        </div>
                    </a>


                    <a
                        href="{{ route('usuario.encuesta.index') }}"
                        class="module-card"
                        data-search="encuestas votaciones opinion resultados"
                    >
                        <div class="module-icon purple">
                            <i class="chart pie icon"></i>
                        </div>

                        <div class="module-content">
                            <div class="module-title">
                                Encuestas
                            </div>
                        </div>

                        <div class="module-arrow">
                            <i class="angle right icon"></i>
                        </div>
                    </a>


                    <a
                        href="{{ route('usuario.asamblea.index') }}"
                        class="module-card"
                        data-search="asambleas reunion reuniones votacion"
                    >
                        <div class="module-icon green">
                            <i class="gavel icon"></i>
                        </div>

                        <div class="module-content">
                            <div class="module-title">
                                Asambleas
                            </div>
                        </div>

                        <div class="module-arrow">
                            <i class="angle right icon"></i>
                        </div>
                    </a>

                </div>

            </div>


            {{-- =====================================
                 REGISTROS Y GESTIÓN
            ====================================== --}}

            <div class="module-group">

                <div class="module-group-header">

                    <div class="group-icon">
                        <i class="clipboard list icon"></i>
                    </div>

                    <div class="group-title">
                        Registros y gestión
                    </div>

                </div>

                <div class="module-grid">

                    <a
                        href="{{ route('usuario.mascota.index') }}"
                        class="module-card"
                        data-search="mascota mascotas perro gato registro"
                    >
                        <div class="module-icon">
                            <i class="paw icon"></i>
                        </div>

                        <div class="module-content">
                            <div class="module-title">
                                Mascotas
                            </div>
                        </div>

                        <div class="module-arrow">
                            <i class="angle right icon"></i>
                        </div>
                    </a>


                    {{-- Modulo de vehiculos retirado: guardaba placas y
                         fotografias visibles para todos los vecinos. --}}


                    <a
                        href="{{ route('usuario.sancion.index') }}"
                        class="module-card"
                        data-search="sanciones multas infracciones"
                    >
                        <div class="module-icon">
                            <i class="exclamation triangle icon"></i>
                        </div>

                        <div class="module-content">
                            <div class="module-title">
                                Sanciones
                            </div>
                        </div>

                        <div class="module-arrow">
                            <i class="angle right icon"></i>
                        </div>
                    </a>


                    <a
                        href="{{ route('usuario.reportes.index') }}"
                        class="module-card"
                        data-search="reportes reporte incidencia problema"
                    >
                        <div class="module-icon">
                            <i class="clipboard list icon"></i>
                        </div>

                        <div class="module-content">
                            <div class="module-title">
                                Reportes
                            </div>
                        </div>

                        <div class="module-arrow">
                            <i class="angle right icon"></i>
                        </div>
                    </a>


                    <a
                        href="{{ route('usuario.proyectos.index') }}"
                        class="module-card"
                        data-search="proyectos proyecto avances obras"
                    >
                        <div class="module-icon orange">
                            <i class="tasks icon"></i>
                        </div>

                        <div class="module-content">
                            <div class="module-title">
                                Proyectos
                            </div>
                        </div>

                        <div class="module-arrow">
                            <i class="angle right icon"></i>
                        </div>
                    </a>


                    @if(
                        Auth::user()->tipo == 'inquilino'
                        && Auth::user()->estado == 1
                    )

                        <a
                            href="{{ route('usuario.solicitudes.index') }}"
                            class="module-card"
                            data-search="solicitar permisos solicitudes permiso"
                        >
                            <div class="module-icon green">
                                <i class="file alternate outline icon"></i>
                            </div>

                            <div class="module-content">
                                <div class="module-title">
                                    Solicitar permisos
                                </div>
                            </div>

                            <div class="module-arrow">
                                <i class="angle right icon"></i>
                            </div>
                        </a>

                    @endif

                </div>

            </div>

        </div>


        {{-- Sin resultados --}}
        <div
            class="no-results"
            id="noResults"
        >
            <i class="search icon"></i>

            <strong>
                No encontramos ese módulo
            </strong>

            Prueba con otra palabra, por ejemplo:
            pagos, reservas, mascota o vehículos.
        </div>


        {{-- =========================================
             MESA DIRECTIVA
        ========================================== --}}

        <section class="board-section">

            <div class="section-heading">

                <div class="section-heading-main">

                    <div class="section-eyebrow">
                        Contacto directo
                    </div>

                    <h2 class="section-title">
                        Mesa directiva provisional
                    </h2>

                    <div class="section-description">
                        Comunícate con los integrantes disponibles.
                    </div>

                </div>

            </div>


            @if(
                isset($mesaDirectiva)
                && count($mesaDirectiva) > 0
            )

                <div class="board-list">

                    @foreach($mesaDirectiva as $usuario)

                        @php
                            $celularWhatsApp =
                                preg_replace(
                                    '/\D/',
                                    '',
                                    $usuario->celular ?? ''
                                );

                            if (
                                strlen($celularWhatsApp) === 10
                            ) {
                                $celularWhatsApp =
                                    '52' . $celularWhatsApp;
                            }

                            $mensajeWhatsApp =
                                urlencode(
                                    'Hola '
                                    . $usuario->nombre
                                    . ', buen día, '
                                );
                        @endphp


                        <div class="board-member">

                            @if($usuario->foto != null)

                                <img
                                    class="ui avatar image board-avatar"
                                    src="{{ asset('storage/perfil/'.$usuario->foto) }}"
                                    alt="{{ $usuario->nombre }}"
                                >

                            @else

                                <div class="board-initial">
                                    {{ substr($usuario->nombre, 0, 1) }}
                                </div>

                            @endif


                            <div class="board-content">

                                <div class="board-name">
                                    {{ $usuario->nombre }}
                                </div>

                                <div class="board-house">
                                    <i class="home icon"></i>
                                    Casa {{ $usuario->casa }}
                                </div>

                            </div>


                            <div class="board-actions">

                                @if(!empty($usuario->celular))

                                    <a
                                        href="tel:{{ $usuario->celular }}"
                                        class="board-action phone"
                                        aria-label="Llamar a {{ $usuario->nombre }}"
                                        title="Llamar"
                                    >
                                        <i class="phone alternate icon"></i>
                                    </a>

                                @endif


                                @if(!empty($celularWhatsApp))

                                    <a
                                        href="https://wa.me/{{ $celularWhatsApp }}?text={{ $mensajeWhatsApp }}"
                                        class="board-action whatsapp"
                                        target="_blank"
                                        rel="noopener"
                                        aria-label="Enviar WhatsApp a {{ $usuario->nombre }}"
                                        title="WhatsApp"
                                    >
                                        <i class="whatsapp icon"></i>
                                    </a>

                                @endif

                            </div>

                        </div>

                    @endforeach

                </div>

            @else

                <div class="board-empty">
                    <i class="users icon"></i>
                    No hay integrantes de mesa directiva provisional disponibles
                    por el momento.
                </div>

            @endif

        </section>

    </div>


    {{-- =========================================
         BUSCADOR DE MÓDULOS
    ========================================== --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            const searchInput =
                document.getElementById('moduleSearch');

            const clearButton =
                document.getElementById('clearSearch');

            const cards =
                document.querySelectorAll('.module-card');

            const groups =
                document.querySelectorAll('.module-group');

            const noResults =
                document.getElementById('noResults');

            function normalizeText(text) {
                return text
                    .toLowerCase()
                    .normalize('NFD')
                    .replace(/[\u0300-\u036f]/g, '')
                    .trim();
            }

            function filterModules() {

                const query =
                    normalizeText(searchInput.value);

                let totalVisible = 0;

                cards.forEach(card => {

                    const searchableText =
                        normalizeText(
                            card.innerText
                            + ' '
                            + (
                                card.dataset.search || ''
                            )
                        );

                    const visible =
                        query === ''
                        || searchableText.includes(query);

                    card.style.display =
                        visible ? '' : 'none';

                    if (visible) {
                        totalVisible++;
                    }

                });


                groups.forEach(group => {

                    const visibleCards =
                        group.querySelectorAll(
                            '.module-card:not([style*="display: none"])'
                        );

                    group.style.display =
                        visibleCards.length
                            ? ''
                            : 'none';

                });


                noResults.style.display =
                    totalVisible === 0
                        ? 'block'
                        : 'none';


                clearButton.style.display =
                    query !== ''
                        ? 'flex'
                        : 'none';
            }


            searchInput.addEventListener(
                'input',
                filterModules
            );


            clearButton.addEventListener(
                'click',
                function () {

                    searchInput.value = '';

                    filterModules();

                    searchInput.focus();
                }
            );

        });
    </script>

</x-app-layout>