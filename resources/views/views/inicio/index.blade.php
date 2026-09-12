@php $rol = Auth::user()->rol ?? 'usuario'; @endphp

<x-app-layout>
    <div class="home">

        <div class="home-hero">
            <div class="greet">
                <div class="hi">Bienvenido(a)</div>
                <h1>Hola, {{ Auth::user()->nombre }}</h1>
                <p>Tu condominio Alameda en una sola app. Busca abajo o toca un acceso.</p>
            </div>
        </div>

        <div class="home-search">
            <div class="box">
                <i class="search icon"></i>
                <input type="text" id="modSearch" placeholder="Buscar módulo… (pagos, reservar, documentos)" autocomplete="off" aria-label="Buscar módulo">
                <button type="button" class="clear" id="modClear" aria-label="Limpiar"><i class="times icon"></i></button>
            </div>
        </div>

        @if($rol === 'administrador' || $rol === 'super-administrador')
            <a href="{{ route('admin.inicio.index') }}" class="admin-card js-mod" data-name="administracion admin panel gestion">
                <div class="ico c-admin"><i class="shield alternate icon"></i></div>
                <div class="tx"><b>Administración</b><span>Panel de gestión del condominio</span></div>
                <div class="go"><i class="chevron right icon"></i></div>
            </a>
        @endif

        <div class="home-section">
            <div class="label"><span class="dot" style="background:#16a34a"></span> Finanzas y trámites</div>
            <div class="app-grid">
                <a href="{{ route('usuario.pago.index') }}" class="app-tile js-mod" data-name="pagos cuotas mantenimiento recibos">
                    <div class="ico c-fin"><i class="money bill wave icon"></i></div><div class="t">Pagos</div>
                </a>
                <a href="{{ route('usuario.sancion.index') }}" class="app-tile js-mod" data-name="sanciones multas infracciones">
                    <div class="ico c-fin"><i class="exclamation triangle icon"></i></div><div class="t">Sanciones</div>
                </a>
                @if(Auth::user()->tipo == 'inquilino' && Auth::user()->estado == 1)
                    <a href="{{ route('usuario.solicitudes.index') }}" class="app-tile js-mod" data-name="solicitar permisos solicitudes inquilino">
                        <div class="ico c-fin"><i class="file alternate outline icon"></i></div><div class="t">Solicitar permisos</div>
                    </a>
                @endif
            </div>
        </div>

        <div class="home-section">
            <div class="label"><span class="dot" style="background:#667eea"></span> Mi hogar</div>
            <div class="app-grid">
                <a href="{{ route('usuario.perfil.index') }}" class="app-tile js-mod" data-name="mi perfil cuenta datos">
                    <div class="ico c-home"><i class="user icon"></i></div><div class="t">Mi Perfil</div>
                </a>
                <a href="{{ route('usuario.mascota.index') }}" class="app-tile js-mod" data-name="mascota perro gato registro">
                    <div class="ico c-home"><i class="paw icon"></i></div><div class="t">Mascota</div>
                </a>
                <a href="{{ route('usuario.vehiculo.index') }}" class="app-tile js-mod" data-name="vehiculos auto carro placas">
                    <div class="ico c-home"><i class="car icon"></i></div><div class="t">Vehículos</div>
                </a>
                <a href="{{ route('usuario.documentos.index') }}" class="app-tile js-mod" data-name="documentos reglamento archivos">
                    <div class="ico c-home"><i class="file alternate icon"></i></div><div class="t">Documentos</div>
                </a>
            </div>
        </div>

        <div class="home-section">
            <div class="label"><span class="dot" style="background:#3b82f6"></span> Comunidad</div>
            <div class="app-grid">
                <a href="{{ route('usuario.comunicados.index') }}" class="app-tile js-mod" data-name="comunicados avisos noticias">
                    <div class="ico c-com"><i class="bullhorn icon"></i></div><div class="t">Comunicados</div>
                </a>
                <a href="{{ route('usuario.vecino.index') }}" class="app-tile js-mod" data-name="vecinos directorio residentes">
                    <div class="ico c-com"><i class="users icon"></i></div><div class="t">Vecinos</div>
                </a>
                <a href="{{ route('usuario.contacto.index') }}" class="app-tile js-mod" data-name="contactos telefonos emergencia">
                    <div class="ico c-com"><i class="address book icon"></i></div><div class="t">Contactos</div>
                </a>
                <a href="{{ route('usuario.reserva.index') }}" class="app-tile js-mod" data-name="reservar areas comunes salon">
                    <div class="ico c-com"><i class="calendar plus outline icon"></i></div><div class="t">Reservar</div>
                </a>
                <a href="{{ route('usuario.reserva.misReservas') }}" class="app-tile js-mod" data-name="reservaciones mis reservas">
                    <div class="ico c-com"><i class="calendar check outline icon"></i></div><div class="t">Reservaciones</div>
                </a>
            </div>
        </div>

        <div class="home-section">
            <div class="label"><span class="dot" style="background:#8b5cf6"></span> Gobierno y transparencia</div>
            <div class="app-grid">
                <a href="{{ route('usuario.asamblea.index') }}" class="app-tile js-mod" data-name="asambleas juntas votaciones">
                    <div class="ico c-gob"><i class="gavel icon"></i></div><div class="t">Asambleas</div>
                </a>
                <a href="{{ route('usuario.encuesta.index') }}" class="app-tile js-mod" data-name="encuestas votar opinion">
                    <div class="ico c-gob"><i class="chart pie icon"></i></div><div class="t">Encuestas</div>
                </a>
                <a href="{{ route('usuario.proyectos.index') }}" class="app-tile js-mod" data-name="proyectos obras avances">
                    <div class="ico c-gob"><i class="tasks icon"></i></div><div class="t">Proyectos</div>
                </a>
                <a href="{{ route('usuario.reportes.index') }}" class="app-tile js-mod" data-name="reportes transparencia finanzas">
                    <div class="ico c-gob"><i class="chart bar icon"></i></div><div class="t">Reportes</div>
                </a>
                <a href="{{ route('usuario.estacionamiento.index') }}" class="app-tile js-mod" data-name="estacionamiento escalera comunitaria">
                    <div class="ico c-gob"><i class="clipboard check icon"></i></div><div class="t">Estacionamiento y escalera</div>
                </a>
            </div>
        </div>

        <div class="home-empty" id="modEmpty">
            <i class="search minus icon"></i>
            <p>Ningún módulo coincide con tu búsqueda.</p>
        </div>

        <section class="board">
            <div class="board-head">
                <div class="ico"><i class="users icon"></i></div>
                <div>
                    <h2>Mesa directiva</h2>
                    <p>Comunícate con los integrantes disponibles.</p>
                </div>
            </div>

            @if(isset($mesaDirectiva) && count($mesaDirectiva) > 0)
                <div class="board-list">
                    @foreach($mesaDirectiva as $usuario)
                        @php
                            $celularWhatsApp = preg_replace('/\D/', '', $usuario->celular ?? '');
                            if (strlen($celularWhatsApp) === 10) { $celularWhatsApp = '52' . $celularWhatsApp; }
                            $mensajeWhatsApp = urlencode('Hola ' . $usuario->nombre . ', buen día, ');
                        @endphp
                        <div class="board-member">
                            @if($usuario->foto != null)
                                <img class="board-avatar" src="{{ asset('storage/perfil/'.$usuario->foto) }}" alt="{{ $usuario->nombre }}">
                            @else
                                <div class="board-initial">{{ substr($usuario->nombre, 0, 1) }}</div>
                            @endif
                            <div class="board-content">
                                <div class="board-name">{{ $usuario->nombre }}</div>
                                <div class="board-house"><i class="home icon"></i> Casa {{ $usuario->casa }}</div>
                                <div class="board-actions">
                                    @if(!empty($usuario->celular))
                                        <a href="tel:{{ $usuario->celular }}" class="board-action phone" aria-label="Llamar a {{ $usuario->nombre }}">
                                            <i class="phone alternate icon"></i> Llamar
                                        </a>
                                    @endif
                                    @if(!empty($celularWhatsApp))
                                        <a href="https://wa.me/{{ $celularWhatsApp }}?text={{ $mensajeWhatsApp }}" class="board-action whatsapp" target="_blank" rel="noopener" aria-label="WhatsApp a {{ $usuario->nombre }}">
                                            <i class="whatsapp icon"></i> WhatsApp
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="board-empty"><i class="users icon"></i> No hay integrantes de mesa directiva disponibles por el momento.</div>
            @endif
        </section>
    </div>

    <script>
        (function () {
            var input = document.getElementById('modSearch');
            var clear = document.getElementById('modClear');
            var empty = document.getElementById('modEmpty');
            var mods = Array.prototype.slice.call(document.querySelectorAll('.js-mod'));
            var sections = Array.prototype.slice.call(document.querySelectorAll('.home-section'));
            function norm(s) { return (s || '').toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, ''); }

            function filter() {
                var q = norm(input.value.trim());
                clear.classList.toggle('show', q.length > 0);
                var anyVisible = false;
                mods.forEach(function (m) {
                    var hit = q === '' || norm(m.getAttribute('data-name')).indexOf(q) !== -1 || norm(m.textContent).indexOf(q) !== -1;
                    m.style.display = hit ? '' : 'none';
                    if (hit) anyVisible = true;
                });
                // Ocultar secciones sin resultados
                sections.forEach(function (sec) {
                    var vis = Array.prototype.slice.call(sec.querySelectorAll('.js-mod'))
                        .some(function (m) { return m.style.display !== 'none'; });
                    sec.style.display = vis ? '' : 'none';
                });
                empty.classList.toggle('show', !anyVisible);
            }
            input.addEventListener('input', filter);
            clear.addEventListener('click', function () { input.value = ''; input.focus(); filter(); });
        })();
    </script>
</x-app-layout>
