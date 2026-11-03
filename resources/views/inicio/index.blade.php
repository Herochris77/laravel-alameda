@php
    $usuario = Auth::user();
    $nombrePila = trim(explode(' ', trim((string) $usuario->nombre))[0] ?? '') ?: 'vecino';
    $hora = (int) now()->format('G');
    $saludo = $hora < 12 ? 'Buenos días' : ($hora < 19 ? 'Buenas tardes' : 'Buenas noches');
    $hoy = now()->locale('es')->translatedFormat('l j \d\e F');
    $rolCasa = $usuario->tipo === 'inquilino' ? 'Inquilino' : 'Propietario';
    $dinero = fn ($v) => '$'.number_format((float) $v, 2);
    $zonas = \App\Support\Navegacion::zonas($usuario);
    $proximo = $casa['proximo'];
    $avance = 6;
    if ($proximo && ! $proximo['vencido']) {
        $avance = max(6, min(100, (int) round(100 - ($proximo['dias'] / 30) * 100)));
    } elseif ($proximo) {
        $avance = 100;
    }
@endphp

<x-app-layout>

    {{-- Portada: saludo, placa de la casa y horizonte del condominio --}}
    <section class="hero-casa" aria-labelledby="saludo-inicio">
        <div class="sobre">{{ $saludo }} · {{ $hoy }}</div>
        <h1 id="saludo-inicio">Hola, <em>{{ $nombrePila }}</em></h1>
        <p>Aquí está todo lo de tu casa y de tu comunidad, en un solo lugar.</p>

        <div class="placa-casa" role="img" aria-label="Casa {{ $usuario->casa }}, {{ $rolCasa }}">
            <small>Casa</small>
            <strong>{{ $usuario->casa }}</strong>
            <span>{{ $rolCasa }}</span>
        </div>

        <svg class="horizonte" viewBox="0 0 1200 130" preserveAspectRatio="xMidYMax slice" aria-hidden="true">
            <g fill="none" stroke="#d8bf6e" stroke-width="2" stroke-linejoin="round" stroke-linecap="round" opacity=".6">
                <path d="M0 112c130-26 270-30 430-12 120 14 240 14 340 0 170-22 300-16 430 12"/>
                <path d="M548 112V66l52-38 52 38v46"/><path d="M536 70l64-50 64 50"/><path d="M575 74h12v12h-12zM613 74h12v12h-12z"/>
                <path d="M474 112V84l36-26 36 26v28"/><path d="M464 88l46-36 46 36"/><path d="M498 90h10v10h-10z"/>
                <path d="M654 112V84l36-26 36 26v28"/><path d="M644 88l46-36 46 36"/><path d="M682 90h10v10h-10z"/>
            </g>
            <g fill="#0a4a31" stroke="#d8bf6e" stroke-width="1.5" opacity=".75">
                <path d="M380 112c-13-12-19-34-19-52 0-18 7-32 19-42 12 10 19 24 19 42 0 18-6 40-19 52z"/>
                <path d="M840 112c-13-12-19-34-19-52 0-18 7-32 19-42 12 10 19 24 19 42 0 18-6 40-19 52z"/>
            </g>
        </svg>
    </section>

    {{-- Estado de la casa: la respuesta a "¿estoy al corriente?" --}}
    <section class="estado-casa {{ $casa['situacion'] }}" aria-labelledby="estado-titulo">
        <div class="techo" aria-hidden="true">
            <svg viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="M5 22 24 6l19 16"/><path d="M10 19v22h28V19"/><path d="M19 41V29h10v12"/><path d="M33 12v6" stroke-width="3.2"/></svg>
        </div>

        <div>
            <div class="etq">Estado de tu casa</div>
            <h2 id="estado-titulo">{{ $casa['titulo'] }}</h2>
            <p>
                @if($proximo)
                    <b>{{ $proximo['concepto'] }}</b> · {{ $dinero($proximo['monto']) }} ·
                    {{ $proximo['vencido'] ? 'venció el' : 'vence el' }} {{ $proximo['vence'] }}
                @else
                    {{ $casa['detalle'] }}
                @endif
            </p>
        </div>

        <div class="ctas">
            @if($casa['aplica'])
                <a class="btn btn-primary" href="{{ route('usuario.pago.index') }}">
                    <i class="money bill alternate outline icon"></i>
                    {{ in_array($casa['situacion'], ['alcorriente', 'revision'], true) ? 'Mis pagos' : 'Pagar ahora' }}
                </a>
            @endif
            <a class="btn btn-secondary" href="{{ route('usuario.perfil.index') }}">
                <i class="user outline icon"></i>
                Mi perfil
            </a>
        </div>

        @if($proximo)
            <div class="barra" role="img"
                 aria-label="{{ $proximo['vencido'] ? 'Pago vencido' : 'Faltan '.$proximo['dias'].' días para el vencimiento' }}">
                <i style="width: {{ $avance }}%"></i>
            </div>
            <div class="barra-etq">
                <span>Hoy</span>
                <span>
                    @if($proximo['vencido'])
                        Vencido hace {{ abs($proximo['dias']) }} {{ abs($proximo['dias']) === 1 ? 'día' : 'días' }}
                    @elseif($proximo['dias'] === 0)
                        Vence hoy
                    @else
                        Faltan {{ $proximo['dias'] }} {{ $proximo['dias'] === 1 ? 'día' : 'días' }}
                    @endif
                </span>
                <span>{{ $proximo['vencido'] ? 'Venció' : 'Vence' }} {{ $proximo['vence'] }}</span>
            </div>
        @endif
    </section>

    @if($casa['rechazados'] > 0 && $casa['situacion'] !== 'rechazado')
        <div class="alerta-casa" role="alert">
            <i class="exclamation triangle icon"></i>
            {{ $casa['rechazados'] === 1 ? 'Un comprobante fue rechazado' : $casa['rechazados'].' comprobantes fueron rechazados' }}.
            <a href="{{ route('usuario.pago.index') }}">Revisar</a>
        </div>
    @endif

    @if($casa['aplica'])
        <div class="cifras">
            <div class="cifra"><small>Pagado este año</small><b>{{ $dinero($casa['pagadoAnio']) }}</b></div>
            <div class="cifra"><small>Saldo a favor</small><b>{{ $dinero($casa['saldo']) }}</b></div>
            <div class="cifra"><small>Avisos nuevos</small><b>{{ $sinLeer }}</b></div>
        </div>
    @endif

    {{-- Lo más común, en botones grandes y con palabras sencillas --}}
    <section class="rapidos" aria-label="Lo que más se usa">
        <a class="rapido" href="{{ route('usuario.pago.index') }}">
            <span class="rapido-ic" aria-hidden="true"><i class="money bill alternate outline icon"></i></span>
            <b>Pagar mi cuota</b>
            <small>Sube tu comprobante</small>
        </a>
        <a class="rapido" href="{{ route('usuario.comunicados.index') }}">
            <span class="rapido-ic" aria-hidden="true"><i class="bullhorn icon"></i></span>
            <b>Ver los avisos</b>
            <small>Lo que informa la mesa</small>
        </a>
        <a class="rapido" href="{{ route('usuario.reserva.index') }}">
            <span class="rapido-ic" aria-hidden="true"><i class="calendar plus outline icon"></i></span>
            <b>Reservar un espacio</b>
            <small>Salón y áreas comunes</small>
        </a>
        <a class="rapido" href="{{ route('usuario.perfil.index') }}">
            <span class="rapido-ic" aria-hidden="true"><i class="user outline icon"></i></span>
            <b>Mis datos</b>
            <small>Teléfono, foto y contraseña</small>
        </a>
    </section>

    {{-- Buscador de módulos --}}
    <div class="buscador-modulos" role="search">
        <i class="search icon"></i>
        <input type="search" id="moduleSearch" placeholder="¿Qué necesitas? Ej. pagos, reservas, mascota..." autocomplete="off" aria-label="Buscar módulo">
        <button type="button" class="search-clear" id="clearSearch" aria-label="Limpiar búsqueda"><i class="times icon"></i></button>
    </div>

    <div class="rejilla-inicio" style="margin-top: 4px">
        <div>
            <div class="titulo-sec">Todo tu condominio</div>

            <div class="zonas">
                @foreach($zonas as $zona)
                    <article class="zona module-group" aria-labelledby="zona-{{ $zona['clave'] }}">
                        <header>
                            <span class="zona-ic" aria-hidden="true"><i class="{{ $zona['icono'] }} icon"></i></span>
                            <h3 id="zona-{{ $zona['clave'] }}">{{ $zona['titulo'] }}</h3>
                        </header>

                        @foreach($zona['items'] as $item)
                            <a class="module-card"
                               href="{{ $item['url'] }}"
                               data-search="{{ $item['buscar'] ?? '' }}">
                                <span class="mc-ic" aria-hidden="true"><i class="{{ $item['icono'] }} icon"></i></span>
                                <span class="mc-text">
                                    <b>{{ $item['titulo'] }}</b>
                                    <small>{{ $item['desc'] ?? '' }}</small>
                                </span>
                                <span class="mc-go" aria-hidden="true"><i class="angle right icon"></i></span>
                            </a>
                        @endforeach
                    </article>
                @endforeach
            </div>

            <div id="noResults" role="status">
                <i class="search icon"></i>
                No encontramos ese módulo. Prueba con otra palabra.
            </div>
        </div>

        <aside>
            <div class="titulo-sec">De la mesa directiva</div>

            {{-- Avisos nuevos: lo mismo que cuenta el número de arriba y la campana. --}}
            @if($avisos->isNotEmpty())
                <div class="panel-casa">
                    <h3>Avisos nuevos <button type="button" class="enlace-boton" id="abrirAvisos">Ver todos</button></h3>

                    @foreach($avisos as $aviso)
                        @php $datos = $aviso->data; @endphp
                        <a class="aviso-casa" href="{{ url($datos['url'] ?? '/inicio') }}">
                            <div class="fecha">
                                <b>{{ $aviso->created_at->format('d') }}</b>{{ $aviso->created_at->locale('es')->translatedFormat('M') }}
                            </div>
                            <div>
                                <p>{{ \App\Support\HtmlSeguro::texto($datos['titulo'] ?? 'Aviso') }}</p>
                                <span>{{ \Illuminate\Support\Str::limit(\App\Support\HtmlSeguro::texto($datos['mensaje'] ?? ''), 70) }}</span>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif

            {{-- Comunicados vigentes de la mesa directiva (solo si hay). --}}
            @if($comunicados->isNotEmpty())
                <div class="panel-casa">
                    <h3>Comunicados <a href="{{ route('usuario.comunicados.index') }}">Ver todos</a></h3>

                    @foreach($comunicados as $comunicado)
                        <a class="aviso-casa" href="{{ route('usuario.comunicados.index') }}">
                            <div class="fecha">
                                <b>{{ $comunicado->updated_at->format('d') }}</b>{{ $comunicado->updated_at->locale('es')->translatedFormat('M') }}
                            </div>
                            <div>
                                <p>{{ $comunicado->titulo }}</p>
                                <span>Mesa directiva provisional</span>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif

            {{-- Solo aparece cuando hay una encuesta abierta que esta persona aún no contesta. --}}
            @if($encuestaPendiente)
                <div class="panel-casa invita-casa">
                    <h3>Tu opinión cuenta</h3>
                    <p>{{ $encuestaPendiente->titulo }}</p>
                    <a class="btn btn-primary" href="{{ route('usuario.encuesta.index') }}">
                        <i class="poll icon"></i>
                        Responder encuesta
                    </a>
                </div>
            @endif
            <div class="panel-casa">
                <h3>Mesa directiva provisional</h3>

                @forelse($mesaDirectiva as $miembro)
                    @php
                        $celularWhatsApp = preg_replace('/\D/', '', $miembro->celular ?? '');
                        if (strlen($celularWhatsApp) === 10) {
                            $celularWhatsApp = '52'.$celularWhatsApp;
                        }
                        $mensajeWhatsApp = rawurlencode('Hola '.$miembro->nombre.', buen día, ');
                    @endphp

                    <div class="mesa-miembro">
                        @if($miembro->foto != null)
                            <img class="foto" src="{{ asset('storage/perfil/'.$miembro->foto) }}" alt="" loading="lazy" decoding="async">
                        @else
                            <div class="inicial" aria-hidden="true">{{ mb_substr($miembro->nombre, 0, 1) }}</div>
                        @endif

                        <div class="quien">
                            <b>{{ $miembro->nombre }}</b>
                            <small><i class="home icon"></i> Casa {{ $miembro->casa }}</small>
                        </div>

                        <div class="acciones">
                            @if(!empty($miembro->celular))
                                <a href="tel:{{ $miembro->celular }}" aria-label="Llamar a {{ $miembro->nombre }}" title="Llamar">
                                    <i class="phone alternate icon"></i>
                                </a>
                            @endif
                            @if(!empty($celularWhatsApp))
                                <a class="whatsapp" href="https://wa.me/{{ $celularWhatsApp }}?text={{ $mensajeWhatsApp }}" target="_blank" rel="noopener noreferrer" aria-label="Enviar WhatsApp a {{ $miembro->nombre }}" title="WhatsApp">
                                    <i class="whatsapp icon"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="vacio-casa">No hay integrantes de mesa directiva provisional disponibles por el momento.</p>
                @endforelse
            </div>
        </aside>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var verAvisos = document.getElementById('abrirAvisos');
            if (verAvisos) {
                verAvisos.addEventListener('click', function () {
                    var campana = document.getElementById('notificationBell');
                    if (campana) { campana.click(); }
                });
            }

            var entrada = document.getElementById('moduleSearch');
            var limpiar = document.getElementById('clearSearch');
            var tarjetas = document.querySelectorAll('.module-card');
            var grupos = document.querySelectorAll('.module-group');
            var sinResultados = document.getElementById('noResults');

            function normalizar(texto) {
                return texto.toLowerCase().normalize('NFD').replace(/[̀-ͯ]/g, '').trim();
            }

            function filtrar() {
                var consulta = normalizar(entrada.value);
                var visibles = 0;

                tarjetas.forEach(function (tarjeta) {
                    var coincide = consulta === '' || normalizar(tarjeta.innerText + ' ' + (tarjeta.dataset.search || '')).includes(consulta);
                    tarjeta.style.display = coincide ? '' : 'none';
                    if (coincide) { visibles++; }
                });

                grupos.forEach(function (grupo) {
                    grupo.style.display = grupo.querySelector('.module-card:not([style*="display: none"])') ? '' : 'none';
                });

                sinResultados.style.display = visibles === 0 ? 'block' : 'none';
                limpiar.style.display = consulta !== '' ? 'flex' : 'none';
            }

            entrada.addEventListener('input', filtrar);
            limpiar.addEventListener('click', function () {
                entrada.value = '';
                filtrar();
                entrada.focus();
            });
        });
    </script>

</x-app-layout>
