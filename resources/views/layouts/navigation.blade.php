{{--
    Navegación del sistema · "Casa Alameda".

    Escritorio: barra superior con el menú por zonas (Mi casa, Comunidad,
    Servicios) y acceso directo a Reportes y, si aplica, a la Mesa directiva.
    Móvil: barra inferior; cada zona abre una hoja con sus módulos.

    Los módulos salen de config/navegacion.php (App\Support\Navegacion): el
    mismo mapa alimenta el menú, la barra móvil y la pantalla de inicio.
--}}
@php
    $usuario = auth()->user();
    $zonas = \App\Support\Navegacion::zonas($usuario);
    $esAdmin = $usuario && in_array($usuario->rol, ['administrador', 'super-administrador'], true);
    $zonaPorClave = collect($zonas)->keyBy('clave');
    $reportes = collect($zonaPorClave['comunidad']['items'] ?? [])->firstWhere('ruta', 'usuario.reportes.index');

    // Antes se cargaban TODAS las notificaciones en cada página. Se limitan las
    // leídas: nadie navega cientos de avisos viejos desde una campana.
    $sinLeer = $usuario ? $usuario->unreadNotifications()->latest()->limit(100)->get() : collect();
    $totalSinLeer = $usuario ? $usuario->unreadNotifications()->count() : 0;
    $leidas = $usuario ? $usuario->readNotifications()->latest()->limit(30)->get() : collect();
    $hayNotificaciones = $sinLeer->isNotEmpty() || $leidas->isNotEmpty();

    $enMas = request()->routeIs('usuario.perfil.*') || request()->routeIs('admin.*');
@endphp

<a href="#contenido-principal" class="skip-link">Saltar al contenido</a>

<nav class="navbar" aria-label="Principal">
    <div class="navbar-container">
        @unless(request()->routeIs('inicio'))
            <button type="button" class="navbar-back" onclick="volverAtras()" title="Atrás" aria-label="Atrás">
                <i class="arrow left icon"></i>
            </button>
        @endunless

        <a href="{{ route('inicio') }}" class="navbar-brand" aria-label="Alameda Condominio, ir al inicio">
            <img src="{{ URL::to('/') }}/img/logo.jpg" alt="" class="brand-logo" width="46" height="46">
            <span class="brand-text">Alameda<small>Condominio</small></span>
        </a>

        @auth
            <div class="navbar-menu desktop-menu">
                <a href="{{ route('inicio') }}" class="nav-link {{ request()->routeIs('inicio') ? 'active' : '' }}">
                    <i class="home icon"></i><span>Inicio</span>
                </a>

                @foreach($zonas as $zona)
                    @continue($zona['clave'] === 'mesa')
                    <div class="nav-zona {{ $zona['activa'] ? 'active' : '' }}" data-zona="{{ $zona['clave'] }}">
                        <button type="button" aria-haspopup="true" aria-expanded="false">
                            <i class="{{ $zona['icono'] }} icon"></i><span>{{ $zona['titulo'] }}</span><i class="chevron down icon caret"></i>
                        </button>
                        <div class="nav-pop" role="menu">
                            @foreach($zona['items'] as $item)
                                <a href="{{ $item['url'] }}" role="menuitem" class="{{ $item['actual'] ? 'active' : '' }}">
                                    <i class="{{ $item['icono'] }} icon"></i><span>{{ $item['titulo'] }}</span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endforeach

                @if($esAdmin && $zonaPorClave->has('mesa'))
                    <a href="{{ route('admin.inicio.index') }}" class="nav-link {{ request()->routeIs('admin.*') ? 'active' : '' }}">
                        <i class="shield alternate icon"></i><span>Mesa</span>
                    </a>
                @endif
            </div>

            <div class="navbar-actions">
                <div class="notification-wrapper">
                    <button id="notificationBell" class="notification-btn" title="Notificaciones" type="button" aria-label="Notificaciones">
                        <i class="bell outline icon"></i>
                        @if($totalSinLeer > 0)
                            <span class="notification-badge" id="notifBadge">{{ $totalSinLeer > 9 ? '9+' : $totalSinLeer }}</span>
                        @endif
                    </button>
                </div>

                <div class="user-menu-wrapper">
                    <button class="user-menu-btn" id="userMenuBtn" type="button" aria-haspopup="true" aria-expanded="false">
                        <span class="user-avatar">{{ \App\Support\Navegacion::iniciales($usuario->nombre) }}</span>
                        <span class="user-name">{{ \Illuminate\Support\Str::limit($usuario->nombre, 15) }}</span>
                        <i class="chevron down icon"></i>
                    </button>

                    <div class="user-dropdown" id="userDropdown">
                        <div class="dropdown-header">
                            <div class="user-info">
                                <div class="user-avatar-lg">{{ $usuario->casa !== null && $usuario->casa !== '' ? $usuario->casa : '·' }}</div>
                                <div class="user-details">
                                    <span class="user-name-full">{{ $usuario->nombre }}</span>
                                    <span class="user-role">Casa {{ $usuario->casa }} · {{ $usuario->tipo ?? 'Usuario' }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="dropdown-divider"></div>

                        <a href="{{ route('usuario.perfil.index') }}" class="dropdown-item">
                            <i class="user outline icon"></i><span>Mi perfil</span>
                        </a>

                        @if($esAdmin)
                            <a href="{{ route('admin.inicio.index') }}" class="dropdown-item">
                                <i class="shield alternate icon"></i><span>Mesa directiva</span>
                            </a>
                        @endif

                        <div class="dropdown-divider"></div>

                        <a href="#" class="dropdown-item logout-item" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="sign out alternate icon"></i><span>Cerrar sesión</span>
                        </a>
                    </div>
                </div>
            </div>
        @endauth
    </div>
</nav>

@auth
    {{-- Barra inferior (móvil). Cada zona abre una hoja con sus módulos. --}}
    <nav class="bottom-tabs" aria-label="Navegación móvil">
        <a href="{{ route('inicio') }}" class="bt-item {{ request()->routeIs('inicio') ? 'active' : '' }}">
            <i class="home icon"></i><span>Inicio</span>
        </a>
        @foreach($zonas as $zona)
            @continue($zona['clave'] === 'mesa')
            <button type="button" class="bt-item {{ $zona['activa'] ? 'active' : '' }}" data-abrir-zona="{{ $zona['clave'] }}" aria-haspopup="dialog">
                <i class="{{ $zona['icono'] }} icon"></i><span>{{ $zona['titulo'] }}</span>
            </button>
        @endforeach
        <button type="button" class="bt-item {{ $enMas ? 'active' : '' }}" data-abrir-zona="mas" aria-haspopup="dialog">
            <i class="ellipsis horizontal icon"></i><span>Más</span>
        </button>
    </nav>

    {{-- Hoja inferior de zona --}}
    <div class="sheet" id="sheet" role="dialog" aria-modal="true" aria-labelledby="sheetTitulo" aria-hidden="true">
        <div class="sheet-fondo" data-cerrar-sheet></div>
        <div class="sheet-panel">
            <div class="sheet-asa"></div>
            <div class="sheet-cab">
                <h3 id="sheetTitulo">Menú</h3>
                <button type="button" class="sheet-cerrar" data-cerrar-sheet aria-label="Cerrar"><i class="close icon" style="margin:0"></i></button>
            </div>

            @foreach($zonas as $zona)
                @continue($zona['clave'] === 'mesa')
                <div class="sheet-links" data-zona="{{ $zona['clave'] }}" data-titulo="{{ $zona['titulo'] }}" hidden>
                    @foreach($zona['items'] as $item)
                        <a href="{{ $item['url'] }}">
                            <span class="icono"><i class="{{ $item['icono'] }} icon"></i></span>
                            <span>{{ $item['titulo'] }}</span>
                            <i class="angle right icon flecha"></i>
                        </a>
                    @endforeach
                </div>
            @endforeach

            <div class="sheet-links" data-zona="mas" data-titulo="Más" hidden>
                <a href="{{ route('usuario.perfil.index') }}"><span class="icono"><i class="user outline icon"></i></span><span>Mi perfil</span><i class="angle right icon flecha"></i></a>
                @if($reportes)
                    <a href="{{ $reportes['url'] }}"><span class="icono"><i class="{{ $reportes['icono'] }} icon"></i></span><span>Reportes</span><i class="angle right icon flecha"></i></a>
                @endif
                @if($esAdmin)
                    <a href="{{ route('admin.inicio.index') }}"><span class="icono"><i class="shield alternate icon"></i></span><span>Mesa directiva</span><i class="angle right icon flecha"></i></a>
                @endif
                <button type="button" id="sheetNotificaciones"><span class="icono"><i class="bell outline icon"></i></span><span>Notificaciones @if($totalSinLeer > 0)<b class="ficha danger" style="margin-left:6px">{{ $totalSinLeer > 9 ? '9+' : $totalSinLeer }}</b>@endif</span><i class="angle right icon flecha"></i></button>
                <button type="button" class="peligro" onclick="document.getElementById('logout-form').submit();"><span class="icono"><i class="sign out alternate icon"></i></span><span>Cerrar sesión</span></button>
            </div>
        </div>
    </div>
@endauth

@auth
    <div class="notifications-backdrop" id="notificationsBackdrop"></div>

    <aside class="notifications-panel" id="notificationsPanel" aria-hidden="true" aria-label="Centro de notificaciones">
        <div class="notifications-header">
            <div class="notifications-title-wrap">
                <div class="notifications-title-icon"><i class="bell outline icon"></i></div>
                <div>
                    <h3>Notificaciones</h3>
                    <p id="notificationsCounterText">
                        {{ $totalSinLeer }} {{ $totalSinLeer === 1 ? 'nueva notificación' : 'nuevas notificaciones' }}
                    </p>
                </div>
            </div>

            <button class="notifications-close" id="notificationsClose" type="button" aria-label="Cerrar notificaciones">
                <i class="close icon"></i>
            </button>
        </div>

        <div class="notifications-list" id="notificationsList">
            <div id="notificationsContent">
                <div class="notification-section-title unread-section-title">Nuevas</div>

                <div id="unreadNotificationsList">
                    @forelse($sinLeer as $notification)
                        <a href="{{ url($notification->data['url'] ?? '#') }}" class="notification-item notification-link unread" data-id="{{ $notification->id }}">
                            <div class="notification-icon">
                                @if(!empty($notification->data['icono']))
                                    {!! \App\Support\HtmlSeguro::limpiar($notification->data['icono'] ?? '') !!}
                                @else
                                    <i class="bell outline icon"></i>
                                @endif
                            </div>
                            <div class="notification-content">
                                <strong>{{ $notification->data['titulo'] ?? 'Notificación' }}</strong>
                                <p>{!! nl2br(\App\Support\HtmlSeguro::limpiar($notification->data['mensaje'] ?? '')) !!}</p>
                                <small>{{ $notification->created_at->diffForHumans() }}</small>
                            </div>
                        </a>
                    @empty
                        <div class="notification-empty-inline" id="unreadEmptyState">No hay notificaciones nuevas.</div>
                    @endforelse
                </div>

                @if($leidas->isNotEmpty())
                    <div class="notification-section-title read-section-title">Leídas · las más recientes</div>
                @endif

                <div id="readNotificationsList">
                    @foreach($leidas as $notification)
                        <a href="{{ url($notification->data['url'] ?? '#') }}" class="notification-item notification-link" data-id="{{ $notification->id }}">
                            <div class="notification-icon read">
                                @if(!empty($notification->data['icono']))
                                    {!! \App\Support\HtmlSeguro::limpiar($notification->data['icono'] ?? '') !!}
                                @else
                                    <i class="bell outline icon"></i>
                                @endif
                            </div>
                            <div class="notification-content">
                                <strong>{{ $notification->data['titulo'] ?? 'Notificación' }}</strong>
                                <p>{!! nl2br(\App\Support\HtmlSeguro::limpiar($notification->data['mensaje'] ?? '')) !!}</p>
                                <small>{{ $notification->created_at->diffForHumans() }}</small>
                            </div>
                        </a>
                    @endforeach
                </div>

                @unless($hayNotificaciones)
                    <div class="notifications-empty" id="notificationsEmpty">
                        <i class="bell slash outline icon"></i>
                        <p>No tienes notificaciones.</p>
                    </div>
                @endunless
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

    document.addEventListener('DOMContentLoaded', function () {
        var BASE_URL = "{{ url('/') }}";
        var CSRF = '{{ csrf_token() }}';

        // ---- Menú de usuario ------------------------------------------------
        var userMenuBtn = document.getElementById('userMenuBtn');
        var userDropdown = document.getElementById('userDropdown');

        function cerrarUsuario() {
            if (!userMenuBtn || !userDropdown) return;
            userMenuBtn.classList.remove('active');
            userDropdown.classList.remove('show');
            userMenuBtn.setAttribute('aria-expanded', 'false');
        }

        if (userMenuBtn && userDropdown) {
            userMenuBtn.addEventListener('click', function (e) {
                e.stopPropagation();
                cerrarZonas();
                var abierto = userDropdown.classList.toggle('show');
                userMenuBtn.classList.toggle('active', abierto);
                userMenuBtn.setAttribute('aria-expanded', abierto ? 'true' : 'false');
            });
            document.addEventListener('click', function (e) {
                if (!userDropdown.contains(e.target) && !userMenuBtn.contains(e.target)) cerrarUsuario();
            });
        }

        // ---- Menús de zona (escritorio) -------------------------------------
        var zonasEscritorio = document.querySelectorAll('.nav-zona');

        function cerrarZonas(except) {
            zonasEscritorio.forEach(function (z) {
                if (z !== except) {
                    z.classList.remove('abierta');
                    var b = z.querySelector('button');
                    if (b) b.setAttribute('aria-expanded', 'false');
                }
            });
        }

        zonasEscritorio.forEach(function (zona) {
            var boton = zona.querySelector('button');
            boton.addEventListener('click', function (e) {
                e.stopPropagation();
                cerrarUsuario();
                cerrarZonas(zona);
                var abierta = zona.classList.toggle('abierta');
                boton.setAttribute('aria-expanded', abierta ? 'true' : 'false');
            });
            zona.addEventListener('mouseenter', function () {
                if (window.matchMedia('(hover: hover)').matches) { cerrarZonas(zona); zona.classList.add('abierta'); boton.setAttribute('aria-expanded', 'true'); }
            });
            zona.addEventListener('mouseleave', function () {
                if (window.matchMedia('(hover: hover)').matches) { zona.classList.remove('abierta'); boton.setAttribute('aria-expanded', 'false'); }
            });
        });
        document.addEventListener('click', function () { cerrarZonas(); });
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') { cerrarZonas(); cerrarUsuario(); cerrarSheet(); cerrarNotificaciones(); }
        });

        // ---- Hoja inferior (móvil) -------------------------------------------
        var sheet = document.getElementById('sheet');
        var sheetTitulo = document.getElementById('sheetTitulo');
        var ultimoBoton = null;

        function abrirSheet(clave, origen) {
            if (!sheet) return;
            var panel = sheet.querySelector('.sheet-links[data-zona="' + clave + '"]');
            if (!panel) return;
            sheet.querySelectorAll('.sheet-links').forEach(function (p) { p.hidden = p !== panel; });
            sheetTitulo.textContent = panel.dataset.titulo || 'Menú';
            sheet.classList.add('show');
            sheet.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';
            ultimoBoton = origen || null;
            var primero = panel.querySelector('a, button');
            if (primero) primero.focus();
        }

        function cerrarSheet() {
            if (!sheet || !sheet.classList.contains('show')) return;
            sheet.classList.remove('show');
            sheet.setAttribute('aria-hidden', 'true');
            document.body.style.overflow = '';
            if (ultimoBoton) ultimoBoton.focus();
        }

        document.querySelectorAll('[data-abrir-zona]').forEach(function (b) {
            b.addEventListener('click', function () { abrirSheet(b.dataset.abrirZona, b); });
        });
        document.querySelectorAll('[data-cerrar-sheet]').forEach(function (b) { b.addEventListener('click', cerrarSheet); });

        // ---- Centro de notificaciones ----------------------------------------
        var bell = document.getElementById('notificationBell');
        var notificationsPanel = document.getElementById('notificationsPanel');
        var notificationsBackdrop = document.getElementById('notificationsBackdrop');
        var notificationsClose = document.getElementById('notificationsClose');
        var deleteNotificationsBtn = document.getElementById('btn-eliminar-notificaciones');
        var notificationsCounterText = document.getElementById('notificationsCounterText');

        function abrirNotificaciones() {
            if (!notificationsPanel || !notificationsBackdrop) return;
            cerrarSheet(); cerrarUsuario(); cerrarZonas();
            notificationsPanel.classList.add('show');
            notificationsBackdrop.classList.add('show');
            notificationsPanel.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';
        }

        function cerrarNotificaciones() {
            if (!notificationsPanel || !notificationsBackdrop) return;
            notificationsPanel.classList.remove('show');
            notificationsBackdrop.classList.remove('show');
            notificationsPanel.setAttribute('aria-hidden', 'true');
            document.body.style.overflow = '';
        }

        function updateUnreadCounter() {
            var unreadCount = document.querySelectorAll('.notification-item.unread').length;
            var currentBadge = document.getElementById('notifBadge');

            if (currentBadge) {
                if (unreadCount > 0) { currentBadge.textContent = unreadCount > 9 ? '9+' : unreadCount; }
                else { currentBadge.remove(); }
            }

            if (notificationsCounterText) {
                notificationsCounterText.textContent = unreadCount === 1 ? '1 nueva notificación' : unreadCount + ' nuevas notificaciones';
            }

            var unreadEmptyState = document.getElementById('unreadEmptyState');
            var unreadList = document.getElementById('unreadNotificationsList');

            if (unreadList && unreadCount === 0 && !unreadEmptyState) {
                unreadList.innerHTML = '<div class="notification-empty-inline" id="unreadEmptyState">No hay notificaciones nuevas.</div>';
            }
        }

        if (bell) {
            bell.addEventListener('click', function (e) { e.preventDefault(); e.stopPropagation(); abrirNotificaciones(); });
        }
        var sheetNotif = document.getElementById('sheetNotificaciones');
        if (sheetNotif) sheetNotif.addEventListener('click', function () { cerrarSheet(); abrirNotificaciones(); });
        if (notificationsClose) notificationsClose.addEventListener('click', cerrarNotificaciones);
        if (notificationsBackdrop) notificationsBackdrop.addEventListener('click', cerrarNotificaciones);

        // Marcar como leída al abrir, y luego ir a su destino.
        document.querySelectorAll('.notification-link').forEach(function (link) {
            link.addEventListener('click', function (e) {
                e.preventDefault();

                var notificationId = this.dataset.id;
                var targetUrl = this.getAttribute('href');
                var clicked = this;

                function ir() { if (targetUrl && targetUrl !== '#') window.location.href = targetUrl; }

                if (!notificationId) { ir(); return; }

                fetch(BASE_URL + '/notificaciones/' + notificationId + '/marcar-leida', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF }
                })
                    .then(function (r) { return r.json(); })
                    .then(function () {
                        clicked.classList.remove('unread');
                        var icon = clicked.querySelector('.notification-icon');
                        if (icon) icon.classList.add('read');
                        updateUnreadCounter();
                        ir();
                    })
                    .catch(ir);
            });
        });

        // Eliminar todas.
        function eliminarTodasLasNotificaciones() {
            deleteNotificationsBtn.classList.add('loading');
            deleteNotificationsBtn.disabled = true;

            $.ajax({
                url: BASE_URL + '/notificaciones/eliminar-todas',
                type: 'DELETE',
                headers: { 'X-CSRF-TOKEN': CSRF },
                success: function (response) {
                    if (typeof alertify !== 'undefined') alertify.success(response.mensaje || 'Notificaciones eliminadas.');

                    var contenido = document.getElementById('notificationsContent');
                    if (contenido) {
                        contenido.innerHTML = '<div class="notifications-empty" id="notificationsEmpty"><i class="bell slash outline icon"></i><p>No tienes notificaciones.</p></div>';
                    }
                    var b = document.getElementById('notifBadge');
                    if (b) b.remove();
                    if (notificationsCounterText) notificationsCounterText.textContent = '0 nuevas notificaciones';
                    cerrarNotificaciones();
                },
                error: function () {
                    if (typeof alertify !== 'undefined') { alertify.error('Error al eliminar.'); } else { alert('Error al eliminar.'); }
                },
                complete: function () {
                    deleteNotificationsBtn.classList.remove('loading');
                    deleteNotificationsBtn.disabled = false;
                }
            });
        }

        if (deleteNotificationsBtn) {
            deleteNotificationsBtn.addEventListener('click', function () {
                if (typeof alertify !== 'undefined') {
                    alertify.confirm('Eliminar notificaciones', '¿Estás seguro de que quieres eliminar todas las notificaciones?',
                        eliminarTodasLasNotificaciones, function () { alertify.message('Operación cancelada'); }
                    ).set('labels', { ok: 'Sí, eliminar', cancel: 'Cancelar' });
                } else if (confirm('¿Estás seguro de que quieres eliminar todas las notificaciones?')) {
                    eliminarTodasLasNotificaciones();
                }
            });
        }

        // Desplazamiento suave en anclas internas.
        document.querySelectorAll('a[href^="#"]').forEach(function (anchor) {
            anchor.addEventListener('click', function (e) {
                var id = this.getAttribute('href');
                if (id.length < 2) return;
                var target = document.querySelector(id);
                if (target) { e.preventDefault(); target.scrollIntoView({ behavior: 'smooth' }); }
            });
        });
    });
</script>
