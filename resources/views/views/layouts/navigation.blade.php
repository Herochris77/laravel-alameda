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