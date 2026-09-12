<nav class="navbar">
    <div class="navbar-container">
        @unless(request()->routeIs('inicio'))
            <button type="button" class="navbar-back" onclick="volverAtras()" title="Atrás" aria-label="Atrás">
                <i class="arrow left icon"></i>
            </button>
        @endunless

        <!-- Logo -->
        <a href="{{ route('inicio') }}" class="navbar-brand">
            <img src="{{ URL::to('/') }}/img/logo.jpg" alt="Logo" class="brand-logo">
            <span class="brand-text">Alameda</span>
        </a>

        <!-- Desktop Navigation -->
        <div class="navbar-menu desktop-menu">
            <a href="{{ route('inicio') }}" class="nav-link {{ request()->routeIs('inicio') ? 'active' : '' }}">
                <i class="home icon"></i>
                <span>Inicio</span>
            </a>

            <a href="{{ route('usuario.pago.index') }}" class="nav-link {{ request()->routeIs('usuario.pago.*') ? 'active' : '' }}">
                <i class="money bill alternate icon"></i>
                <span>Pagos</span>
            </a>

            <a href="{{ route('usuario.reportes.index') }}" class="nav-link {{ request()->routeIs('usuario.reportes.*') ? 'active' : '' }}">
                <i class="chart bar icon"></i>
                <span>Reportes</span>
            </a>

            <a href="{{ route('usuario.documentos.index') }}" class="nav-link {{ request()->routeIs('usuario.documentos.*') ? 'active' : '' }}">
                <i class="file alternate icon"></i>
                <span>Documentos</span>
            </a>
        </div>

        <!-- Right side actions -->
        <div class="navbar-actions">
            @auth
                <!-- Notifications -->
                <div class="notification-wrapper">
                    <button id="notificationBell" class="notification-btn" title="Notificaciones" type="button">
                        <i class="bell outline icon"></i>

                        @if(auth()->user()->unreadNotifications->count() > 0)
                            <span class="notification-badge" id="notifBadge">
                                {{ auth()->user()->unreadNotifications->count() > 9 ? '9+' : auth()->user()->unreadNotifications->count() }}
                            </span>
                        @endif
                    </button>
                </div>

                <!-- User Menu -->
                <div class="user-menu-wrapper">
                    <button class="user-menu-btn" id="userMenuBtn" type="button">
                        <div class="user-avatar">
                            <i class="user circle icon"></i>
                        </div>

                        <span class="user-name">
                            {{ \Illuminate\Support\Str::limit(Auth::user()->nombre, 15) }}
                        </span>

                        <i class="chevron down icon"></i>
                    </button>

                    <div class="user-dropdown" id="userDropdown">
                        <div class="dropdown-header">
                            <div class="user-info">
                                <div class="user-avatar-lg">
                                    <i class="user circle icon"></i>
                                </div>

                                <div class="user-details">
                                    <span class="user-name-full">{{ Auth::user()->nombre }}</span>
                                    <span class="user-role">{{ Auth::user()->tipo ?? 'Usuario' }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="dropdown-divider"></div>

                        <a href="#" class="dropdown-item logout-item" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="sign out alternate icon"></i>
                            <span>Cerrar sesión</span>
                        </a>
                    </div>
                </div>

                <!-- Mobile Menu Toggle -->
                <button class="mobile-menu-toggle" id="mobileMenuToggle" type="button" aria-label="Abrir menú">
                    <span class="hamburger-line"></span>
                    <span class="hamburger-line"></span>
                    <span class="hamburger-line"></span>
                </button>
            @endauth
        </div>
    </div>

    <!-- Mobile Menu -->
    <div class="mobile-menu" id="mobileMenu">
        <div class="mobile-user-section">
            <div class="mobile-user-info">
                <div class="user-avatar-sm">
                    <i class="user circle icon"></i>
                </div>

                <div>
                    <div class="user-name-full">{{ Auth::user()->nombre ?? 'Usuario' }}</div>
                    <div class="user-email">{{ Auth::user()->correo ?? '' }}</div>
                </div>
            </div>
        </div>

        <a href="#" class="mobile-nav-link logout-link" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            <i class="sign out alternate icon"></i>
            <span>Cerrar sesión</span>
        </a>
    </div>
</nav>

@auth
    {{-- Barra inferior de pestañas (experiencia tipo app, solo móvil) --}}
    <nav class="bottom-tabs">
        <a href="{{ route('inicio') }}" class="bt-item {{ request()->routeIs('inicio') ? 'active' : '' }}">
            <i class="home icon"></i><span>Inicio</span>
        </a>
        <a href="{{ route('usuario.pago.index') }}" class="bt-item {{ request()->routeIs('usuario.pago.*') ? 'active' : '' }}">
            <i class="money bill alternate icon"></i><span>Pagos</span>
        </a>
        <a href="{{ route('usuario.reportes.index') }}" class="bt-item {{ request()->routeIs('usuario.reportes.*') ? 'active' : '' }}">
            <i class="chart bar icon"></i><span>Reportes</span>
        </a>
        <a href="{{ route('usuario.asamblea.index') }}" class="bt-item {{ request()->routeIs('usuario.asamblea.*') ? 'active' : '' }}">
            <i class="gavel icon"></i><span>Asambleas</span>
        </a>
        <a href="{{ route('usuario.perfil.index') }}" class="bt-item {{ request()->routeIs('usuario.perfil.*') ? 'active' : '' }}">
            <i class="user icon"></i><span>Perfil</span>
        </a>
    </nav>
@endauth

@auth
    <!-- Notifications Backdrop -->
    <div class="notifications-backdrop" id="notificationsBackdrop"></div>

    <!-- Notifications Panel -->
    <aside class="notifications-panel" id="notificationsPanel" aria-hidden="true">
        <div class="notifications-header">
            <div class="notifications-title-wrap">
                <div class="notifications-title-icon">
                    <i class="bell outline icon"></i>
                </div>

                <div>
                    <h3>Centro de notificaciones</h3>
                    <p id="notificationsCounterText">
                        {{ auth()->user()->unreadNotifications->count() }}
                        {{ auth()->user()->unreadNotifications->count() === 1 ? 'nueva notificación' : 'nuevas notificaciones' }}
                    </p>
                </div>
            </div>

            <button class="notifications-close" id="notificationsClose" type="button" aria-label="Cerrar notificaciones">
                <i class="close icon"></i>
            </button>
        </div>

        <div class="notifications-list" id="notificationsList">
            <div id="notificationsContent">
                <div class="notification-section-title unread-section-title">
                    Nuevas notificaciones
                </div>

                <div id="unreadNotificationsList">
                    @forelse(auth()->user()->unreadNotifications as $notification)
                        <a href="{{ url($notification->data['url'] ?? '#') }}"
                           class="notification-item notification-link unread"
                           data-id="{{ $notification->id }}">
                            <div class="notification-icon">
                                @if(!empty($notification->data['icono']))
                                    {!! $notification->data['icono'] !!}
                                @else
                                    <i class="bell outline icon"></i>
                                @endif
                            </div>

                            <div class="notification-content">
                                <strong>{{ $notification->data['titulo'] ?? 'Notificación' }}</strong>

                                <p>
                                    {!! nl2br($notification->data['mensaje'] ?? '') !!}
                                </p>

                                <small>{{ $notification->created_at->diffForHumans() }}</small>
                            </div>
                        </a>
                    @empty
                        <div class="notification-empty-inline" id="unreadEmptyState">
                            No hay notificaciones nuevas.
                        </div>
                    @endforelse
                </div>

                @if(auth()->user()->readNotifications->count() > 0)
                    <div class="notification-section-title read-section-title">
                        Leídas
                    </div>
                @endif

                <div id="readNotificationsList">
                    @foreach(auth()->user()->readNotifications as $notification)
                        <a href="{{ url($notification->data['url'] ?? '#') }}"
                           class="notification-item notification-link"
                           data-id="{{ $notification->id }}">
                            <div class="notification-icon read">
                                @if(!empty($notification->data['icono']))
                                    {!! $notification->data['icono'] !!}
                                @else
                                    <i class="bell outline icon"></i>
                                @endif
                            </div>

                            <div class="notification-content">
                                <strong>{{ $notification->data['titulo'] ?? 'Notificación' }}</strong>

                                <p>
                                    {!! nl2br($notification->data['mensaje'] ?? '') !!}
                                </p>

                                <small>{{ $notification->created_at->diffForHumans() }}</small>
                            </div>
                        </a>
                    @endforeach
                </div>

                @if(auth()->user()->notifications->count() === 0)
                    <div class="notifications-empty" id="notificationsEmpty">
                        <i class="bell slash outline icon"></i>
                        <p>No tienes notificaciones.</p>
                    </div>
                @endif
            </div>
        </div>

        <div class="notifications-footer">
            <button id="btn-eliminar-notificaciones" class="ui red fluid button" type="button">
                <i class="trash alternate outline icon"></i>
                Eliminar todas
            </button>
        </div>
    </aside>
@endauth

<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
    @csrf
</form>

<style>
    /* ===== Barra inferior de pestañas (app-like) — solo móvil ===== */
    .bottom-tabs { display: none; }

    @media (max-width: 900px) {
        .bottom-tabs {
            display: flex;
            position: fixed;
            left: 0; right: 0; bottom: 0;
            z-index: 900;
            background: #ffffff;
            border-top: 1px solid #e5e7eb;
            box-shadow: 0 -4px 16px rgba(15, 23, 42, 0.06);
            padding: 6px 4px calc(6px + env(safe-area-inset-bottom, 0px));
            justify-content: space-around;
        }
        .bottom-tabs .bt-item {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 2px;
            text-decoration: none;
            color: #94a3b8;
            font-size: 10.5px;
            font-weight: 600;
            padding: 4px 0;
            border-radius: 10px;
        }
        .bottom-tabs .bt-item i { margin: 0 !important; font-size: 1.25rem; }
        .bottom-tabs .bt-item.active { color: #0d9488; }
        .bottom-tabs .bt-item:active { background: #f1f5f9; }

        /* Espacio para que el contenido no quede tapado por la barra */
        .page-container { padding-bottom: 84px !important; }
    }
</style>

<style>
    /* Navbar Base */
    .navbar {
        background: white;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        width: 100%;
        z-index: 1000;
    }

    .navbar-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 0 20px;
        height: 64px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    /* Botón atrás (app-like) */
    .navbar-back {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        margin-right: 8px;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        background: #f8fafc;
        color: #0f766e;
        cursor: pointer;
        flex-shrink: 0;
        transition: background 0.15s ease, transform 0.1s ease;
    }

    .navbar-back:hover { background: #f0fdfa; }
    .navbar-back:active { transform: scale(0.94); }
    .navbar-back i { margin: 0 !important; font-size: 1.05rem; }

    /* Brand */
    .navbar-brand {
        display: flex;
        align-items: center;
        gap: 10px;
        text-decoration: none;
        color: #1a1a2e;
    }

    .brand-logo {
        height: 40px;
        width: auto;
        border-radius: 8px;
    }

    .brand-text {
        font-size: 1.3rem;
        font-weight: 700;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    /* Desktop Menu */
    .desktop-menu {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .nav-link {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 10px 18px;
        border-radius: 10px;
        text-decoration: none;
        color: #64748b;
        font-weight: 500;
        font-size: 0.95rem;
        transition: all 0.2s ease;
    }

    .nav-link:hover {
        background: #f1f5f9;
        color: #1e293b;
    }

    .nav-link.active {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    .nav-link i.icon {
        font-size: 1.1rem;
    }

    /* Actions */
    .navbar-actions {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    /* Notification Button */
    .notification-wrapper {
        position: relative;
    }

    .notification-btn {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        border: none;
        background: #f1f5f9;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        transition: all 0.2s ease;
        margin: 0 auto;
    }

    .notification-btn:hover {
        background: #e2e8f0;
        transform: scale(1.05);
    }

    .notification-btn i {
        font-size: 1.3rem;
        color: #64748b;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto;
    }

    .notification-badge {
        position: absolute;
        top: -4px;
        right: -4px;
        min-width: 20px;
        height: 20px;
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        color: white;
        font-size: 0.7rem;
        font-weight: 700;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0 6px;
        box-shadow: 0 2px 8px rgba(239, 68, 68, 0.4);
        animation: pulse-badge 2s infinite;
    }

    @keyframes pulse-badge {
        0%,
        100% {
            transform: scale(1);
        }

        50% {
            transform: scale(1.1);
        }
    }

    /* User Menu */
    .user-menu-wrapper {
        position: relative;
    }

    .user-menu-btn {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 6px 14px 6px 6px;
        border-radius: 12px;
        border: 2px solid #e2e8f0;
        background: white;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .user-menu-btn:hover {
        border-color: #667eea;
        box-shadow: 0 2px 12px rgba(102, 126, 234, 0.15);
    }

    .user-menu-btn.active {
        border-color: #667eea;
        background: #f8faff;
    }

    .user-avatar {
        width: 32px;
        height: 32px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        flex-shrink: 0;
    }

    .user-avatar i {
        font-size: 1.1rem;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .user-name {
        font-weight: 600;
        color: #1e293b;
        font-size: 0.9rem;
    }

    .user-avatar i,
    .user-avatar-lg i,
    .user-avatar-sm i {
        margin: 0 !important;
        line-height: 1 !important;
    }

    .user-menu-btn i.chevron {
        font-size: 0.8rem;
        color: #94a3b8;
        transition: transform 0.2s ease;
    }

    .user-menu-btn.active i.chevron {
        transform: rotate(180deg);
    }

    /* User Dropdown */
    .user-dropdown {
        position: absolute;
        top: calc(100% + 8px);
        right: 0;
        width: 280px;
        background: white;
        border-radius: 16px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
        opacity: 0;
        visibility: hidden;
        transform: translateY(-10px);
        transition: all 0.3s ease;
        overflow: hidden;
        z-index: 1020;
    }

    .user-dropdown.show {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }

    .dropdown-header {
        padding: 20px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    .user-info {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .user-avatar-lg {
        width: 48px;
        height: 48px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .user-avatar-lg i {
        font-size: 1.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .user-details {
        display: flex;
        flex-direction: column;
    }

    .user-name-full {
        font-weight: 600;
        font-size: 1rem;
    }

    .user-role {
        font-size: 0.8rem;
        opacity: 0.8;
        text-transform: capitalize;
    }

    .dropdown-divider {
        height: 1px;
        background: #e2e8f0;
    }

    .dropdown-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 14px 20px;
        text-decoration: none;
        color: #64748b;
        transition: all 0.2s ease;
    }

    .dropdown-item:hover {
        background: #f8fafc;
        color: #1e293b;
    }

    .dropdown-item i {
        font-size: 1.1rem;
    }

    .logout-item:hover {
        background: #fef2f2;
        color: #ef4444;
    }

    /* Mobile Menu Toggle */
    .mobile-menu-toggle {
        display: none;
        flex-direction: column;
        gap: 5px;
        padding: 10px;
        border: none;
        background: none;
        cursor: pointer;
    }

    .hamburger-line {
        width: 24px;
        height: 2px;
        background: #64748b;
        border-radius: 2px;
        transition: all 0.3s ease;
    }

    .mobile-menu-toggle.active .hamburger-line:nth-child(1) {
        transform: rotate(45deg) translate(5px, 5px);
    }

    .mobile-menu-toggle.active .hamburger-line:nth-child(2) {
        opacity: 0;
    }

    .mobile-menu-toggle.active .hamburger-line:nth-child(3) {
        transform: rotate(-45deg) translate(5px, -5px);
    }

    /* Mobile Menu */
    .mobile-menu {
        display: none;
        padding: 0 20px 20px;
        background: white;
        border-top: 1px solid #e2e8f0;
    }

    .mobile-menu.show {
        display: block;
    }

    .mobile-nav-link {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 14px 0;
        text-decoration: none;
        color: #64748b;
        font-weight: 500;
        border-bottom: 1px solid #f1f5f9;
    }

    .mobile-nav-link:hover,
    .mobile-nav-link.active {
        color: #667eea;
    }

    .mobile-nav-link i {
        font-size: 1.2rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 24px;
        text-align: center;
    }

    .mobile-nav-divider {
        height: 1px;
        background: #e2e8f0;
        margin: 10px 0;
    }

    .mobile-user-section {
        padding: 16px 0;
    }

    .mobile-user-info {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .user-avatar-sm {
        width: 40px;
        height: 40px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        flex-shrink: 0;
    }

    .user-avatar-sm i {
        font-size: 1.2rem;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .user-email {
        font-size: 0.8rem;
        color: #94a3b8;
    }

    .logout-link {
        color: #ef4444 !important;
    }

    /* Notifications Panel */
    .notifications-backdrop {
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, 0.45);
        opacity: 0;
        visibility: hidden;
        transition: all 0.25s ease;
        z-index: 1090;
    }

    .notifications-backdrop.show {
        opacity: 1;
        visibility: visible;
    }

    .notifications-panel {
        position: fixed;
        top: 76px;
        right: 20px;
        width: 420px;
        max-height: calc(100dvh - 96px);
        background: white;
        border-radius: 18px;
        box-shadow: 0 20px 60px rgba(15, 23, 42, 0.22);
        display: flex;
        flex-direction: column;
        overflow: hidden;
        opacity: 0;
        visibility: hidden;
        transform: translateY(-8px);
        transition: all 0.25s ease;
        z-index: 1100;
    }

    .notifications-panel.show {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }

    .notifications-header {
        padding: 18px 20px;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        flex-shrink: 0;
    }

    .notifications-title-wrap {
        display: flex;
        align-items: center;
        gap: 12px;
        min-width: 0;
    }

    .notifications-title-icon {
        width: 42px;
        height: 42px;
        border-radius: 14px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .notifications-title-icon i {
        margin: 0 !important;
        line-height: 1 !important;
    }

    .notifications-header h3 {
        margin: 0;
        color: #1e293b;
        font-size: 1.1rem;
        font-weight: 700;
    }

    .notifications-header p {
        margin: 4px 0 0;
        color: #94a3b8;
        font-size: 0.85rem;
    }

    .notifications-close {
        width: 36px;
        height: 36px;
        border: none;
        border-radius: 10px;
        background: #f1f5f9;
        color: #64748b;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .notifications-close:hover {
        background: #e2e8f0;
        color: #1e293b;
    }

    .notifications-close i {
        margin: 0 !important;
    }

    .notifications-list {
        overflow-y: auto;
        padding: 8px;
        flex: 1;
    }

    .notification-section-title {
        font-size: 0.72rem;
        font-weight: 800;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: #94a3b8;
        padding: 14px 12px 8px;
    }

    .notification-item {
        display: flex;
        gap: 12px;
        padding: 14px;
        border-radius: 14px;
        transition: background 0.2s ease, transform 0.2s ease;
        text-decoration: none;
        color: inherit;
    }

    .notification-item:hover {
        background: #f8fafc;
        transform: translateX(2px);
    }

    .notification-item.unread {
        background: #f8faff;
    }

    .notification-icon {
        width: 38px;
        height: 38px;
        border-radius: 12px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        overflow: hidden;
    }

    .notification-icon.read {
        background: #f1f5f9;
        color: #64748b;
    }

    .notification-icon i,
    .notification-icon .icon {
        margin: 0 !important;
        line-height: 1 !important;
    }

    .notification-content {
        min-width: 0;
        flex: 1;
    }

    .notification-content strong {
        display: block;
        color: #1e293b;
        font-size: 0.92rem;
        margin-bottom: 3px;
    }

    .notification-content p {
        margin: 0;
        color: #64748b;
        font-size: 0.86rem;
        line-height: 1.35;
        word-break: break-word;
    }

    .notification-content small {
        display: block;
        margin-top: 6px;
        color: #94a3b8;
        font-size: 0.75rem;
    }

    .notification-empty-inline {
        margin: 8px 12px 12px;
        padding: 14px;
        border-radius: 12px;
        background: #f8fafc;
        color: #94a3b8;
        font-size: 0.88rem;
        text-align: center;
    }

    .notifications-empty {
        min-height: 220px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 10px;
        color: #94a3b8;
        text-align: center;
    }

    .notifications-empty i {
        font-size: 2rem;
        margin: 0 !important;
    }

    .notifications-empty p {
        margin: 0;
    }

    .notifications-footer {
        padding: 12px;
        border-top: 1px solid #e2e8f0;
        background: rgba(255, 255, 255, 0.96);
        flex-shrink: 0;
    }

    .notifications-footer .button {
        border-radius: 12px !important;
        font-weight: 700 !important;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .desktop-menu {
            display: none;
        }

        .user-menu-wrapper {
            display: none;
        }

        .mobile-menu-toggle {
            display: flex;
        }

        .brand-text {
            display: none;
        }

        .notification-btn {
            width: 40px;
            height: 40px;
        }

        .mobile-menu {
            position: fixed;
            top: 64px;
            left: 0;
            right: 0;
            z-index: 999;
            max-height: calc(100dvh - 64px);
            overflow-y: auto;
            box-shadow: 0 12px 28px rgba(15, 23, 42, 0.12);
        }

        .notifications-panel {
            top: 64px;
            right: 0;
            bottom: 0;
            left: 0;
            width: 100%;
            max-height: calc(100dvh - 64px);
            border-radius: 0;
            transform: translateY(100%);
        }

        .notifications-panel.show {
            transform: translateY(0);
        }

        .notifications-header {
            padding: 18px 18px 14px;
        }

        .notifications-list {
            padding: 8px 10px 12px;
        }

        .notifications-footer {
            position: sticky;
            bottom: 0;
            padding: 12px 14px calc(12px + env(safe-area-inset-bottom));
            box-shadow: 0 -8px 24px rgba(15, 23, 42, 0.08);
        }

        .notifications-footer .button {
            min-height: 48px;
            border-radius: 12px !important;
            font-weight: 700 !important;
        }
    }

    @media (max-width: 480px) {
        .navbar-container {
            padding: 0 12px;
            height: 56px;
        }

        .brand-logo {
            height: 36px;
        }

        .navbar-actions {
            gap: 8px;
        }

        .mobile-menu {
            top: 56px;
            max-height: calc(100dvh - 56px);
        }

        .notifications-panel {
            top: 56px;
            max-height: calc(100dvh - 56px);
        }
    }
</style>

<script>
    // Botón "atrás" propio (app-like). Regresa dentro de la app; si no hay
    // historial interno, va a Inicio en vez de salir del sitio.
    window.volverAtras = function () {
        var ref = document.referrer || '';
        if (ref.indexOf(window.location.origin) === 0 && window.history.length > 1) {
            window.history.back();
        } else {
            window.location.href = '{{ route('inicio') }}';
        }
    };

    document.addEventListener('DOMContentLoaded', function() {
        // User dropdown toggle
        const userMenuBtn = document.getElementById('userMenuBtn');
        const userDropdown = document.getElementById('userDropdown');

        if (userMenuBtn && userDropdown) {
            userMenuBtn.addEventListener('click', function(e) {
                e.stopPropagation();

                userMenuBtn.classList.toggle('active');
                userDropdown.classList.toggle('show');
            });

            document.addEventListener('click', function(e) {
                if (!userDropdown.contains(e.target) && !userMenuBtn.contains(e.target)) {
                    userMenuBtn.classList.remove('active');
                    userDropdown.classList.remove('show');
                }
            });
        }

        // Mobile menu toggle
        const mobileMenuToggle = document.getElementById('mobileMenuToggle');
        const mobileMenu = document.getElementById('mobileMenu');

        if (mobileMenuToggle && mobileMenu) {
            mobileMenuToggle.addEventListener('click', function() {
                mobileMenuToggle.classList.toggle('active');
                mobileMenu.classList.toggle('show');
            });
        }

        // Notifications panel
        const bell = document.getElementById('notificationBell');
        const notifBadge = document.getElementById('notifBadge');
        const notificationsPanel = document.getElementById('notificationsPanel');
        const notificationsBackdrop = document.getElementById('notificationsBackdrop');
        const notificationsClose = document.getElementById('notificationsClose');
        const deleteNotificationsBtn = document.getElementById('btn-eliminar-notificaciones');
        const notificationsCounterText = document.getElementById('notificationsCounterText');

        function openNotifications() {
            if (!notificationsPanel || !notificationsBackdrop) return;

            notificationsPanel.classList.add('show');
            notificationsBackdrop.classList.add('show');
            notificationsPanel.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';

            if (mobileMenu && mobileMenu.classList.contains('show')) {
                mobileMenu.classList.remove('show');
            }

            if (mobileMenuToggle && mobileMenuToggle.classList.contains('active')) {
                mobileMenuToggle.classList.remove('active');
            }

            if (userDropdown && userDropdown.classList.contains('show')) {
                userDropdown.classList.remove('show');
            }

            if (userMenuBtn && userMenuBtn.classList.contains('active')) {
                userMenuBtn.classList.remove('active');
            }
        }

        function closeNotifications() {
            if (!notificationsPanel || !notificationsBackdrop) return;

            notificationsPanel.classList.remove('show');
            notificationsBackdrop.classList.remove('show');
            notificationsPanel.setAttribute('aria-hidden', 'true');
            document.body.style.overflow = '';
        }

        function updateUnreadCounter() {
            const unreadItems = document.querySelectorAll('.notification-item.unread');
            const unreadCount = unreadItems.length;

            const currentBadge = document.getElementById('notifBadge');

            if (currentBadge) {
                if (unreadCount > 0) {
                    currentBadge.textContent = unreadCount > 9 ? '9+' : unreadCount;
                } else {
                    currentBadge.remove();
                }
            }

            if (notificationsCounterText) {
                notificationsCounterText.textContent = unreadCount === 1
                    ? '1 nueva notificación'
                    : `${unreadCount} nuevas notificaciones`;
            }

            const unreadEmptyState = document.getElementById('unreadEmptyState');
            const unreadList = document.getElementById('unreadNotificationsList');

            if (unreadList && unreadCount === 0 && !unreadEmptyState) {
                unreadList.innerHTML = `
                    <div class="notification-empty-inline" id="unreadEmptyState">
                        No hay notificaciones nuevas.
                    </div>
                `;
            }
        }

        if (bell) {
            bell.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();

                openNotifications();
            }, true);
        }

        if (notificationsClose) {
            notificationsClose.addEventListener('click', closeNotifications);
        }

        if (notificationsBackdrop) {
            notificationsBackdrop.addEventListener('click', closeNotifications);
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeNotifications();
            }
        });

        // Mark notification as read when clicked, then redirect
        document.querySelectorAll('.notification-link').forEach(function(link) {
            const BASE_URL = "{{ url('/') }}";
            link.addEventListener('click', function(e) {
                e.preventDefault();

                const notificationId = this.dataset.id;
                const targetUrl = this.getAttribute('href');
                const clickedNotification = this;

                if (!notificationId) {
                    if (targetUrl && targetUrl !== '#') {
                        window.location.href = targetUrl;
                    }

                    return;
                }

                fetch(`${BASE_URL}/notificaciones/${notificationId}/marcar-leida`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    clickedNotification.classList.remove('unread');

                    const icon = clickedNotification.querySelector('.notification-icon');
                    if (icon) {
                        icon.classList.add('read');
                    }

                    updateUnreadCounter();

                    if (targetUrl && targetUrl !== '#') {
                        window.location.href = targetUrl;
                    }
                })
                .catch(error => {
                    if (targetUrl && targetUrl !== '#') {
                        window.location.href = targetUrl;
                    }
                });
            });
        });

        // Delete all notifications - same function as old flyout
        if (deleteNotificationsBtn) {
            deleteNotificationsBtn.addEventListener('click', function() {
                if (typeof alertify !== 'undefined') {
                    alertify.confirm(
                        'Eliminar notificaciones',
                        '¿Estás seguro de que quieres eliminar todas las notificaciones?',
                        function() {
                            eliminarTodasLasNotificaciones('{{ url('/') }}');
                        },
                        function() {
                            alertify.message('Operación cancelada');
                        }
                    ).set('labels', {
                        ok: 'Sí, eliminar',
                        cancel: 'Cancelar'
                    });
                } else {
                    if (confirm('¿Estás seguro de que quieres eliminar todas las notificaciones?')) {
                        eliminarTodasLasNotificaciones('{{ url('/') }}');
                    }
                }
            });
        }

        function eliminarTodasLasNotificaciones(BASE_URL) {
            deleteNotificationsBtn.classList.add('loading');
            deleteNotificationsBtn.disabled = true;

            $.ajax({
                url: `{{ url('/') }}/notificaciones/eliminar-todas`,
                type: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (typeof alertify !== 'undefined') {
                        alertify.success(response.mensaje || 'Notificaciones eliminadas.');
                    }

                    const notificationsContent = document.getElementById('notificationsContent');

                    if (notificationsContent) {
                        notificationsContent.innerHTML = `
                            <div class="notifications-empty" id="notificationsEmpty">
                                <i class="bell slash outline icon"></i>
                                <p>No tienes notificaciones.</p>
                            </div>
                        `;
                    }

                    const currentBadge = document.getElementById('notifBadge');

                    if (currentBadge) {
                        currentBadge.remove();
                    }

                    if (notificationsCounterText) {
                        notificationsCounterText.textContent = '0 nuevas notificaciones';
                    }

                    closeNotifications();
                },
                error: function() {
                    if (typeof alertify !== 'undefined') {
                        alertify.error('Error al eliminar.');
                    } else {
                        alert('Error al eliminar.');
                    }
                },
                complete: function() {
                    deleteNotificationsBtn.classList.remove('loading');
                    deleteNotificationsBtn.disabled = false;
                }
            });
        }

        // Smooth scroll for any anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                const target = document.querySelector(this.getAttribute('href'));

                if (target) {
                    e.preventDefault();

                    target.scrollIntoView({
                        behavior: 'smooth'
                    });
                }
            });
        });
    });
</script>