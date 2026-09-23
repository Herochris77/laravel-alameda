<script>
    window.addEventListener('load', function() {
        $('.menu-card').removeClass('loading');
    });
</script>

<x-app-layout>
    <style>
        :root {
            --text-main: #1e293b;
            --text-muted: #64748b;
            --surface: #ffffff;
            --surface-soft: #f8fafc;
            --border-soft: rgba(148, 163, 184, 0.22);
            --shadow-soft: 0 10px 30px rgba(15, 23, 42, 0.08);
            --shadow-hover: 0 18px 40px rgba(15, 23, 42, 0.14);

            --admin-start: #667eea;
            --admin-end: #764ba2;
        }

        .admin-dashboard-wrapper {
            max-width: 1180px;
            margin: 0 auto;
            padding: 8px 4px 32px;
        }

        .admin-hero {
            background:
                radial-gradient(circle at top right, rgba(255,255,255,0.22), transparent 34%),
                linear-gradient(135deg, var(--admin-start) 0%, var(--admin-end) 100%);
            border-radius: 24px;
            padding: 32px;
            color: white;
            margin-bottom: 28px;
            position: relative;
            overflow: hidden;
            box-shadow: var(--shadow-soft);
        }

        .admin-hero::before,
        .admin-hero::after {
            content: '';
            position: absolute;
            border-radius: 999px;
            background: rgba(255,255,255,0.11);
            pointer-events: none;
        }

        .admin-hero::before {
            width: 360px;
            height: 360px;
            right: -120px;
            top: -170px;
        }

        .admin-hero::after {
            width: 160px;
            height: 160px;
            right: 88px;
            bottom: -88px;
        }

        .admin-hero-content {
            display: flex;
            align-items: center;
            gap: 18px;
            position: relative;
            z-index: 2;
            max-width: 720px;
        }

        .admin-hero-icon {
            width: 68px;
            height: 68px;
            border-radius: 20px;
            background: rgba(255,255,255,0.16);
            border: 1px solid rgba(255,255,255,0.22);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            box-shadow: 0 12px 28px rgba(15, 23, 42, 0.14);
            backdrop-filter: blur(10px);
        }

        .admin-hero-icon i {
            margin: 0 !important;
            color: white;
            font-size: 2rem;
            line-height: 1 !important;
        }

        .admin-hero-title {
            font-size: clamp(1.55rem, 3vw, 2.2rem);
            font-weight: 800;
            margin: 0 0 8px;
            letter-spacing: -0.02em;
        }

        .admin-hero-subtitle {
            opacity: 0.92;
            font-size: 1.05rem;
            margin: 0;
        }

        .admin-hero-bg-icon {
            position: absolute;
            right: 34px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 5rem;
            opacity: 0.18;
            z-index: 1;
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            margin: 0 0 16px;
        }

        .section-title {
            font-size: 1.05rem;
            font-weight: 800;
            color: var(--text-main);
            margin: 0;
            letter-spacing: -0.01em;
        }

        .section-subtitle {
            color: var(--text-muted);
            font-size: 0.92rem;
            margin-top: 2px;
        }

        /* El cron falla en silencio; esto lo hace visible. */
        .cron-alerta {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            background: #fef2f2;
            color: #b91c1c;
            padding: 3px 9px;
            border-radius: 99px;
            font-size: .78rem;
            font-weight: 700;
        }

        .cron-alerta i { margin: 0; }

        /* =====================================================
           COMPROBANTES POR REVISAR
           ===================================================== */

        .revisar-alerta {
            background: var(--surface);
            border: 1px solid #bae6fd;
            border-left: 4px solid #0ea5e9;
            border-radius: 16px;
            padding: 16px 18px;
            margin-bottom: 22px;
            box-shadow: var(--shadow-soft);
        }

        /* A partir de 3 días esperando deja de ser un pendiente normal. */
        .revisar-alerta.urge {
            border-color: #fcd34d;
            border-left-color: #f59e0b;
            background: #fffdf7;
        }

        .revisar-cab {
            display: flex;
            align-items: flex-start;
            gap: 13px;
            margin-bottom: 13px;
        }

        .revisar-icono {
            width: 40px;
            height: 40px;
            border-radius: 11px;
            background: #e0f2fe;
            color: #0369a1;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .revisar-alerta.urge .revisar-icono { background: #fef3c7; color: #b45309; }

        .revisar-icono i { font-size: 1.15rem; margin: 0; }

        .revisar-texto strong {
            display: block;
            font-size: 1.02rem;
            color: var(--text-main);
        }

        .revisar-texto span {
            display: block;
            font-size: .85rem;
            color: var(--text-muted);
            line-height: 1.5;
            margin-top: 2px;
        }

        .revisar-lista { display: flex; flex-direction: column; gap: 8px; }

        .revisar-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 11px 14px;
            border: 1px solid var(--border-soft);
            border-radius: 12px;
            background: var(--surface-soft);
            text-decoration: none;
            color: inherit;
            transition: border-color .15s, background .15s, transform .15s;
        }

        .revisar-item:hover {
            border-color: #7dd3fc;
            background: #f0f9ff;
            transform: translateX(2px);
        }

        .revisar-item-info { min-width: 0; }

        .revisar-item-concepto {
            display: block;
            font-weight: 600;
            font-size: .92rem;
            color: var(--text-main);
        }

        .revisar-item-meta {
            display: block;
            font-size: .8rem;
            color: var(--text-muted);
            margin-top: 2px;
        }

        .revisar-espera { color: #b45309; font-weight: 600; }

        .revisar-item-boton {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            background: linear-gradient(135deg, var(--admin-start), var(--admin-end));
            color: #fff;
            padding: 7px 14px;
            border-radius: 9px;
            font-size: .84rem;
            font-weight: 600;
            white-space: nowrap;
        }

        .revisar-item-boton i { margin: 0; font-size: .8rem; }

        @media (max-width: 640px) {
            .revisar-item { flex-direction: column; align-items: stretch; }
            .revisar-item-boton { justify-content: center; }
        }

        .admin-menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(230px, 1fr));
            gap: 18px;
        }

        @media (min-width: 1024px) {
            .admin-menu-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        @media (max-width: 560px) {
            .admin-dashboard-wrapper {
                padding: 0 2px 24px;
            }

            .admin-hero {
                padding: 24px;
                border-radius: 20px;
            }

            .admin-hero-content {
                align-items: flex-start;
            }

            .admin-hero-icon {
                width: 58px;
                height: 58px;
                border-radius: 18px;
            }

            .admin-hero-icon i {
                font-size: 1.65rem;
            }

            .admin-hero-bg-icon {
                display: none;
            }

            .admin-menu-grid {
                grid-template-columns: 1fr;
                gap: 14px;
            }
        }

        .menu-card {
            background: rgba(255,255,255,0.96);
            border: 1px solid var(--border-soft);
            border-radius: 22px;
            text-decoration: none !important;
            color: inherit;
            box-shadow: 0 4px 18px rgba(15, 23, 42, 0.055);
            transition:
                transform 0.22s ease,
                box-shadow 0.22s ease,
                border-color 0.22s ease,
                background 0.22s ease;
            position: relative;
            overflow: hidden;
            outline: none;
            min-height: 220px;
            display: flex;
        }

        .menu-card::before {
            content: '';
            position: absolute;
            inset: 0 0 auto 0;
            height: 4px;
            background: var(--card-gradient);
            transform: scaleX(0);
            transform-origin: left;
            transition: transform 0.22s ease;
        }

        .menu-card::after {
            content: '';
            position: absolute;
            width: 120px;
            height: 120px;
            right: -56px;
            bottom: -60px;
            border-radius: 999px;
            background: var(--card-soft-bg);
            opacity: 0;
            transition: opacity 0.22s ease, transform 0.22s ease;
        }

        .menu-card:hover,
        .menu-card:focus-visible {
            transform: translateY(-6px);
            box-shadow: var(--shadow-hover);
            border-color: var(--card-border);
            background: #ffffff;
        }

        .menu-card:hover::before,
        .menu-card:focus-visible::before {
            transform: scaleX(1);
        }

        .menu-card:hover::after,
        .menu-card:focus-visible::after {
            opacity: 1;
            transform: scale(1.08);
        }

        .menu-card:active {
            transform: translateY(-2px);
        }

        .menu-card-body {
            width: 100%;
            padding: 30px 26px;
            text-align: center;
            position: relative;
            z-index: 2;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 14px;
        }

        .menu-card-icon {
            width: 76px;
            height: 76px;
            border-radius: 22px;
            background: var(--card-gradient);
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: var(--card-shadow);
            transition: transform 0.22s ease, box-shadow 0.22s ease;
            flex-shrink: 0;
        }

        .menu-card:hover .menu-card-icon,
        .menu-card:focus-visible .menu-card-icon {
            transform: scale(1.07) rotate(-2deg);
            box-shadow: var(--card-shadow-hover);
        }

        .menu-card-icon i {
            font-size: 2rem;
            color: white;
            margin: 0 !important;
            line-height: 1 !important;
        }

        .menu-card-title {
            margin: 2px 0 0;
            color: var(--text-main);
            font-size: 1.08rem;
            font-weight: 800;
            letter-spacing: -0.01em;
        }

        .menu-card-description {
            margin: 0;
            color: var(--text-muted);
            font-size: 0.92rem;
            line-height: 1.45;
            max-width: 240px;
        }

        .menu-card-action {
            margin-top: 4px;
            font-size: 0.82rem;
            font-weight: 800;
            color: var(--card-action);
            display: inline-flex;
            align-items: center;
            gap: 6px;
            opacity: 0;
            transform: translateY(4px);
            transition: opacity 0.22s ease, transform 0.22s ease;
        }

        .menu-card:hover .menu-card-action,
        .menu-card:focus-visible .menu-card-action {
            opacity: 1;
            transform: translateY(0);
        }

        .menu-card-action i {
            margin: 0 !important;
            font-size: 0.85rem;
        }

        .card-orange {
            --card-gradient: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            --card-border: rgba(245, 158, 11, 0.36);
            --card-soft-bg: linear-gradient(135deg, rgba(245,158,11,0.10), rgba(217,119,6,0.16));
            --card-shadow: 0 12px 24px rgba(245, 158, 11, 0.24);
            --card-shadow-hover: 0 16px 30px rgba(245, 158, 11, 0.30);
            --card-action: #d97706;
        }

        .card-green {
            --card-gradient: linear-gradient(135deg, #10b981 0%, #059669 100%);
            --card-border: rgba(16, 185, 129, 0.36);
            --card-soft-bg: linear-gradient(135deg, rgba(16,185,129,0.10), rgba(5,150,105,0.16));
            --card-shadow: 0 12px 24px rgba(16, 185, 129, 0.22);
            --card-shadow-hover: 0 16px 30px rgba(16, 185, 129, 0.28);
            --card-action: #059669;
        }

        .card-red {
            --card-gradient: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            --card-border: rgba(239, 68, 68, 0.36);
            --card-soft-bg: linear-gradient(135deg, rgba(239,68,68,0.10), rgba(220,38,38,0.16));
            --card-shadow: 0 12px 24px rgba(239, 68, 68, 0.22);
            --card-shadow-hover: 0 16px 30px rgba(239, 68, 68, 0.28);
            --card-action: #dc2626;
        }

        .card-blue {
            --card-gradient: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            --card-border: rgba(59, 130, 246, 0.36);
            --card-soft-bg: linear-gradient(135deg, rgba(59,130,246,0.10), rgba(37,99,235,0.16));
            --card-shadow: 0 12px 24px rgba(59, 130, 246, 0.22);
            --card-shadow-hover: 0 16px 30px rgba(59, 130, 246, 0.28);
            --card-action: #2563eb;
        }

        .card-purple {
            --card-gradient: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
            --card-border: rgba(139, 92, 246, 0.36);
            --card-soft-bg: linear-gradient(135deg, rgba(139,92,246,0.10), rgba(124,58,237,0.16));
            --card-shadow: 0 12px 24px rgba(139, 92, 246, 0.22);
            --card-shadow-hover: 0 16px 30px rgba(139, 92, 246, 0.28);
            --card-action: #7c3aed;
        }

        .card-pink {
            --card-gradient: linear-gradient(135deg, #ec4899 0%, #db2777 100%);
            --card-border: rgba(236, 72, 153, 0.36);
            --card-soft-bg: linear-gradient(135deg, rgba(236,72,153,0.10), rgba(219,39,119,0.16));
            --card-shadow: 0 12px 24px rgba(236, 72, 153, 0.22);
            --card-shadow-hover: 0 16px 30px rgba(236, 72, 153, 0.28);
            --card-action: #db2777;
        }
    </style>

    <div class="admin-dashboard-wrapper">
        <section class="admin-hero">
            <div class="admin-hero-content">
                <div class="admin-hero-icon">
                    <i class="cog icon"></i>
                </div>

                <div>
                    <h1 class="admin-hero-title">Panel de Administración</h1>
                    <p class="admin-hero-subtitle">Gestiona comunicados, usuarios, pagos, documentos y módulos del condominio.</p>
                </div>
            </div>

            <i class="ui cog icon admin-hero-bg-icon"></i>
        </section>

        {{--
            Comprobantes esperando validación.

            Es el único pendiente del sistema con alguien esperando del otro
            lado: el vecino ya pagó y ya subió su comprobante, pero su recibo
            sigue diciendo "pendiente" hasta que tesorería lo valide. Se avisa
            aquí porque antes había que acordarse de entrar concepto por
            concepto a buscarlos.
        --}}
        @if(($porRevisar['total'] ?? 0) > 0 && auth()->user()->puedeGestionarPagos())
            <div class="revisar-alerta {{ ($porRevisar['mas_antiguo'] ?? 0) >= 3 ? 'urge' : '' }}">

                <div class="revisar-cab">
                    <div class="revisar-icono">
                        <i class="inbox icon"></i>
                    </div>

                    <div class="revisar-texto">
                        <strong>
                            {{ $porRevisar['total'] }}
                            {{ $porRevisar['total'] === 1 ? 'comprobante' : 'comprobantes' }}
                            por revisar
                        </strong>
                        <span>
                            @if(($porRevisar['mas_antiguo'] ?? 0) >= 1)
                                El más antiguo lleva {{ $porRevisar['mas_antiguo'] }}
                                {{ $porRevisar['mas_antiguo'] === 1 ? 'día' : 'días' }} esperando.
                            @else
                                Subidos hoy.
                            @endif
                            Hasta que los valides, el vecino sigue viendo su recibo como pendiente.
                        </span>
                    </div>
                </div>

                <div class="revisar-lista">
                    @foreach($porRevisar['conceptos'] as $c)
                        {{--
                            La liga lleva los filtros puestos: al abrir el
                            concepto ya se ve solo lo que hay que revisar, sin
                            tener que seleccionarlos a mano cada vez.
                        --}}
                        <a href="{{ route('admin.pago.DetallePagos', ['id' => $c['pago_id']]) }}?estado=pendiente&comprobante=con"
                           class="revisar-item">
                            <span class="revisar-item-info">
                                <span class="revisar-item-concepto">{{ $c['concepto'] }}</span>
                                <span class="revisar-item-meta">
                                    {{ $c['cuantos'] }} {{ $c['cuantos'] === 1 ? 'comprobante' : 'comprobantes' }}
                                    · ${{ number_format($c['monto'], 2) }}
                                    @if($c['espera'] >= 1)
                                        · <span class="revisar-espera">{{ $c['espera'] }}
                                            {{ $c['espera'] === 1 ? 'día' : 'días' }}</span>
                                    @endif
                                </span>
                            </span>

                            <span class="revisar-item-boton">
                                Revisar
                                <i class="arrow right icon"></i>
                            </span>
                        </a>
                    @endforeach
                </div>

            </div>
        @endif

        <div class="section-header">
            <div>
                <h2 class="section-title">Acciones administrativas</h2>
                <div class="section-subtitle">Selecciona una opción para continuar</div>
            </div>
        </div>

        <div class="admin-menu-grid">
            <a href="{{ route('admin.comunicado.nuevoComunicado') }}" class="menu-card card-orange" aria-label="Crear nuevo comunicado">
                <div class="menu-card-body">
                    <div class="menu-card-icon">
                        <i class="bullhorn icon"></i>
                    </div>
                    <h3 class="menu-card-title">Nuevo Comunicado</h3>
                    <p class="menu-card-description">Publica avisos importantes para los vecinos.</p>
                    <span class="menu-card-action">
                        Abrir módulo
                        <i class="arrow right icon"></i>
                    </span>
                </div>
            </a>

            <a href="{{ route('admin.contacto.nuevoContacto') }}" class="menu-card card-green" aria-label="Crear nuevo contacto">
                <div class="menu-card-body">
                    <div class="menu-card-icon">
                        <i class="address book icon"></i>
                    </div>
                    <h3 class="menu-card-title">Nuevo Contacto</h3>
                    <p class="menu-card-description">Gestiona contactos de emergencia.</p>
                    <span class="menu-card-action">
                        Abrir módulo
                        <i class="arrow right icon"></i>
                    </span>
                </div>
            </a>

            <a href="{{ route('admin.sancion.nuevaSancion') }}" class="menu-card card-red" aria-label="Registrar nueva sanción">
                <div class="menu-card-body">
                    <div class="menu-card-icon">
                        <i class="gavel icon"></i>
                    </div>
                    <h3 class="menu-card-title">Nueva Sanción</h3>
                    <p class="menu-card-description">Registra multas e infracciones.</p>
                    <span class="menu-card-action">
                        Abrir módulo
                        <i class="arrow right icon"></i>
                    </span>
                </div>
            </a>

            <a href="{{ route('admin.pago.nuevoPago') }}" class="menu-card card-blue" aria-label="Crear nuevo pago">
                <div class="menu-card-body">
                    <div class="menu-card-icon">
                        <i class="money bill alternate icon"></i>
                    </div>
                    <h3 class="menu-card-title">Nuevo Pago</h3>
                    <p class="menu-card-description">Genera recibos y conceptos de pago.</p>
                    <span class="menu-card-action">
                        Abrir módulo
                        <i class="arrow right icon"></i>
                    </span>
                </div>
            </a>

            <a href="{{ route('admin.documento.nuevoDocumento') }}" class="menu-card card-purple" aria-label="Administrar documentos">
                <div class="menu-card-body">
                    <div class="menu-card-icon">
                        <i class="file alternate icon"></i>
                    </div>
                    <h3 class="menu-card-title">Documentos</h3>
                    <p class="menu-card-description">Sube documentos, reglamentos y archivos relevantes.</p>
                    <span class="menu-card-action">
                        Abrir módulo
                        <i class="arrow right icon"></i>
                    </span>
                </div>
            </a>

            <a href="{{ route('admin.usuarios.listaUsuarios') }}" class="menu-card card-pink" aria-label="Administrar usuarios">
                <div class="menu-card-body">
                    <div class="menu-card-icon">
                        <i class="users icon"></i>
                    </div>
                    <h3 class="menu-card-title">Usuarios</h3>
                    <p class="menu-card-description">Administra cuentas, perfiles y vecinos.</p>
                    <span class="menu-card-action">
                        Abrir módulo
                        <i class="arrow right icon"></i>
                    </span>
                </div>
            </a>

            <a href="{{ route('admin.encuesta.index') }}" class="menu-card card-purple" aria-label="Administrar encuestas">
                <div class="menu-card-body">
                    <div class="menu-card-icon">
                        <i class="chart pie icon"></i>
                    </div>
                    <h3 class="menu-card-title">Encuestas</h3>
                    <p class="menu-card-description">Crea encuestas para consultar a los vecinos.</p>
                    <span class="menu-card-action">
                        Abrir módulo
                        <i class="arrow right icon"></i>
                    </span>
                </div>
            </a>

            <a href="{{ route('admin.asamblea.index') }}" class="menu-card card-blue" aria-label="Administrar asambleas">
                <div class="menu-card-body">
                    <div class="menu-card-icon">
                        <i class="gavel icon"></i>
                    </div>
                    <h3 class="menu-card-title">Asambleas</h3>
                    <p class="menu-card-description">Convoca asambleas, registra asistencia y toma votaciones.</p>
                    <span class="menu-card-action">
                        Abrir módulo
                        <i class="arrow right icon"></i>
                    </span>
                </div>
            </a>

            <a href="{{ route('admin.dashboard') }}" class="menu-card card-blue" aria-label="Dashboard del comité">
                <div class="menu-card-body">
                    <div class="menu-card-icon">
                        <i class="chart bar icon"></i>
                    </div>
                    <h3 class="menu-card-title">Panel del comité</h3>
                    <p class="menu-card-description">Indicadores de cobranza, morosidad y participación.</p>
                    <span class="menu-card-action">
                        Ver panel
                        <i class="arrow right icon"></i>
                    </span>
                </div>
            </a>

            <a href="{{ route('admin.expediente.index') }}" class="menu-card card-blue" aria-label="Expediente por casa">
                <div class="menu-card-body">
                    <div class="menu-card-icon">
                        <i class="folder open icon"></i>
                    </div>
                    <h3 class="menu-card-title">Expediente por casa</h3>
                    <p class="menu-card-description">Toda la información de cada casa en un solo lugar.</p>
                    <span class="menu-card-action">
                        Abrir módulo
                        <i class="arrow right icon"></i>
                    </span>
                </div>
            </a>

            <a href="{{ route('admin.proyectos.index') }}" class="menu-card card-blue" aria-label="Proyectos">
                <div class="menu-card-body">
                    <div class="menu-card-icon">
                        <i class="tasks icon"></i>
                    </div>
                    <h3 class="menu-card-title">Proyectos</h3>
                    <p class="menu-card-description">Publica proyectos con costo y registra su avance.</p>
                    <span class="menu-card-action">
                        Abrir módulo
                        <i class="arrow right icon"></i>
                    </span>
                </div>
            </a>

            @if(auth()->user()->rol == 'super-administrador')
            <a href="https://alameda-condominio.com.mx/admin/logs" class="menu-card card-purple" aria-label="Logs">
                <div class="menu-card-body">
                    <div class="menu-card-icon">
                        <i class="bug icon"></i>
                    </div>
                    <h3 class="menu-card-title">Logs</h3>
                    <p class="menu-card-description">Revisa logs del sistema.</p>
                    <span class="menu-card-action">
                        Abrir módulo
                        <i class="arrow right icon"></i>
                    </span>
                </div>
            </a>
            <a href="{{ route('admin.correos.index') }}" class="menu-card card-purple" aria-label="Correos enviados">
                <div class="menu-card-body">
                    <div class="menu-card-icon">
                        <i class="mail icon"></i>
                    </div>
                    <h3 class="menu-card-title">Correos Enviados</h3>
                    <p class="menu-card-description">Historial de todos los correos enviados por el sistema.</p>
                    <span class="menu-card-action">
                        Ver historial
                        <i class="arrow right icon"></i>
                    </span>
                </div>
            </a>
            @endif

            <a href="{{ route('admin.reporteMensual.index') }}" class="menu-card card-green" aria-label="Reporte mensual">
                <div class="menu-card-body">
                    <div class="menu-card-icon">
                        <i class="file alternate icon"></i>
                    </div>
                    <h3 class="menu-card-title">Reporte Mensual</h3>
                    <p class="menu-card-description">Ingresos y egresos del mes en PDF, para entregar a la mesa directiva provisional.</p>
                    <span class="menu-card-action">
                        Generar reporte
                        <i class="arrow right icon"></i>
                    </span>
                </div>
            </a>

            @if(auth()->user()->puedeGestionarPagos())
            <a href="{{ route('admin.firma.index') }}" class="menu-card card-green" aria-label="Mi firma">
                <div class="menu-card-body">
                    <div class="menu-card-icon">
                        <i class="pen fancy icon"></i>
                    </div>
                    <h3 class="menu-card-title">Mi Firma</h3>
                    <p class="menu-card-description">Firma que llevan los recibos que descargan los vecinos.</p>
                    <span class="menu-card-action">
                        Administrar firma
                        <i class="arrow right icon"></i>
                    </span>
                </div>
            </a>
            @endif

            <a href="{{ route('admin.estadoCuenta.index') }}" class="menu-card card-blue" aria-label="Estado de cuenta por vivienda">
                <div class="menu-card-body">
                    <div class="menu-card-icon">
                        <i class="home icon"></i>
                    </div>
                    <h3 class="menu-card-title">Estado de Cuenta</h3>
                    <p class="menu-card-description">Quién debe por vivienda, con PDF y constancia de no adeudo.</p>
                    <span class="menu-card-action">
                        Ver viviendas
                        <i class="arrow right icon"></i>
                    </span>
                </div>
            </a>

            <a href="{{ route('admin.servicio.index') }}" class="menu-card card-purple" aria-label="Servicios y pagos recurrentes">
                <div class="menu-card-body">
                    <div class="menu-card-icon">
                        <i class="calendar alternate icon"></i>
                    </div>
                    <h3 class="menu-card-title">Servicios</h3>
                    <p class="menu-card-description">Pagos recurrentes con su referencia y aviso antes de vencer.</p>
                    <span class="menu-card-action">
                        Ver servicios
                        <i class="arrow right icon"></i>
                    </span>
                </div>
            </a>

            <a href="{{ route('admin.solicitudes.index') }}" class="menu-card card-blue" aria-label="Solicitudes de permiso">
                <div class="menu-card-body">
                    <div class="menu-card-icon">
                        <i class="file alternate outline icon"></i>
                    </div>
                    <h3 class="menu-card-title">Solicitudes</h3>
                    <p class="menu-card-description">Revisa solicitudes de permiso de inquilinos.</p>
                    <span class="menu-card-action">
                        Ver solicitudes
                        <i class="arrow right icon"></i>
                    </span>
                </div>
            </a>
            @if(auth()->user()->rol == 'super-administrador')
            {{--
                Este botón manda correos de verdad a los vecinos. Se pide
                confirmación antes de dispararlo: el cron normalmente corre
                solo, y pulsarlo por curiosidad les llega a la bandeja.
            --}}
            <a href="{{ route('cronjob.notificaciones', ['token' => env('CRON_TOKEN', 'admin123')]) }}" class="menu-card card-purple" id="btn-cronjob" aria-label="Ejecutar cronjob" target="_blank">
                <div class="menu-card-body">
                    <div class="menu-card-icon">
                        <i class="clock icon"></i>
                    </div>
                    <h3 class="menu-card-title">Cronjob</h3>
                    <p class="menu-card-description">
                        @if($cron)
                            Última ejecución: {{ $cron['cuando'] }}
                            @if($cron['origen'] === 'manual') (manual) @endif
                        @else
                            Ejecuta notificaciones diarias (pagos, reservas, estacionamientos).
                        @endif
                    </p>
                    <span class="menu-card-action">
                        @if($cron && $cron['atrasado'])
                            <span class="cron-alerta">
                                <i class="exclamation triangle icon"></i>
                                Sin correr hace {{ $cron['horas'] }} h
                            </span>
                        @else
                            Ejecutar
                            <i class="external alternate icon"></i>
                        @endif
                    </span>
                </div>
            </a>
            @endif
        </div>
    </div>

    <script>
        $(function () {
            /*
             * Confirmación antes de disparar el cron a mano.
             *
             * No es una acción de consulta: manda correos y notificaciones a
             * los vecinos. El cron corre solo todos los días, así que pulsar
             * esto es la excepción, no la rutina.
             */
            $('#btn-cronjob').on('click', function (e) {
                e.preventDefault();

                const destino = this.href;

                @if($cron)
                    const ultima = 'La última ejecución fue el {{ $cron['cuando'] }}.';
                @else
                    const ultima = 'No hay registro de una ejecución previa.';
                @endif

                alertify.confirm(
                    'Ejecutar las notificaciones diarias',
                    'Esto <strong>manda correos y notificaciones reales</strong> a los vecinos: ' +
                    'recordatorios de pago por vencer, reservaciones de mañana, estacionamientos ' +
                    'ocupados y servicios por vencer.<br><br>' +
                    ultima + '<br><br>' +
                    'Normalmente corre solo cada día. Un concepto no se avisa dos veces el mismo ' +
                    'día, así que si ya salió hoy no se repetirá.<br><br>' +
                    '¿Ejecutar ahora?',
                    function () {
                        window.open(destino, '_blank');
                    },
                    function () {}
                ).set({ labels: { ok: 'Sí, ejecutar', cancel: 'Cancelar' } });
            });
        });
    </script>
</x-app-layout>