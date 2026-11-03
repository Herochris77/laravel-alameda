@php
    $usuario = auth()->user();
    $esSuper = $usuario->rol === 'super-administrador';
    $puedePagos = $usuario->puedeGestionarPagos();

    $permitido = fn (?string $solo) => match ($solo) {
        'super' => $esSuper,
        'pagos' => $puedePagos,
        default => true,
    };

    // Solo las áreas y opciones que esta persona puede usar.
    $grupos = collect(\App\Support\Navegacion::mapa('panel-admin', 'grupos'))
        ->filter(fn ($g) => $permitido($g['solo'] ?? null))
        ->map(function ($g) use ($permitido) {
            $g['items'] = collect($g['items'])
                ->filter(function ($i) use ($permitido) {
                    if (! $permitido($i['solo'] ?? null)) {
                        return false;
                    }

                    return isset($i['tipo']) || (isset($i['ruta']) && \Illuminate\Support\Facades\Route::has($i['ruta']));
                })
                ->map(function ($i) {
                    $i['href'] = match ($i['tipo'] ?? null) {
                        'logs' => url($i['url']),
                        'cron' => route('cronjob.notificaciones', ['token' => (config('mantenimiento.cron') ?: env('CRON_TOKEN', 'admin123'))]),
                        default => route($i['ruta']),
                    };

                    return $i;
                })
                ->values()
                ->all();

            return $g;
        })
        ->filter(fn ($g) => $g['items'] !== [])
        ->values();

    $frecuentes = collect(\App\Support\Navegacion::mapa('panel-admin', 'frecuentes'))
        ->filter(fn ($f) => \Illuminate\Support\Facades\Route::has($f['ruta']))
        ->all();
    $nombrePila = trim(explode(' ', trim((string) $usuario->nombre))[0] ?? '') ?: 'equipo';
@endphp

<x-app-layout titulo="Panel de la mesa · Alameda">

    <section class="hero-seccion">
        <div>
            <div class="sobre">Mesa directiva</div>
            <h1>Hola, {{ $nombrePila }}</h1>
            <p>Elige qué quieres hacer. Lo más común está arriba; todo lo demás, ordenado por área.</p>
        </div>
    </section>

    {{--
        Comprobantes esperando validación.

        Es el único pendiente del sistema con alguien esperando del otro lado:
        el vecino ya pagó y ya subió su comprobante, pero su recibo sigue
        diciendo "pendiente" hasta que tesorería lo valide.
    --}}
    @if(($porRevisar['total'] ?? 0) > 0 && $puedePagos)
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
                    {{-- La liga lleva los filtros puestos: al abrir el concepto ya se ve solo lo que hay que revisar. --}}
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

    {{-- Lo más usado, en botones grandes --}}
    <section class="rapidos" aria-label="Lo más usado">
        @foreach($frecuentes as $f)
            <a class="rapido" href="{{ route($f['ruta']) }}">
                <span class="rapido-ic" aria-hidden="true"><i class="{{ $f['icono'] }} icon"></i></span>
                <b>{{ $f['titulo'] }}</b>
                <small>{{ $f['desc'] }}</small>
            </a>
        @endforeach
    </section>

    {{-- Buscador: con tantas opciones, escribir es más rápido que recorrer --}}
    <div class="buscador-modulos" role="search">
        <i class="search icon"></i>
        <input type="search" id="moduleSearch" placeholder="¿Qué buscas? Ej. pagos, usuarios, asamblea..." autocomplete="off" aria-label="Buscar una opción del panel">
        <button type="button" class="search-clear" id="clearSearch" aria-label="Limpiar búsqueda"><i class="times icon"></i></button>
    </div>

    <div class="titulo-sec">Todas las áreas</div>

    <div class="zonas">
        @foreach($grupos as $grupo)
            <article class="zona module-group" aria-labelledby="grupo-{{ $grupo['clave'] }}">
                <header>
                    <span class="zona-ic" aria-hidden="true"><i class="{{ $grupo['icono'] }} icon"></i></span>
                    <h3 id="grupo-{{ $grupo['clave'] }}">{{ $grupo['titulo'] }}</h3>
                </header>

                @foreach($grupo['items'] as $item)
                    @if(($item['tipo'] ?? null) === 'cron')
                        {{--
                            Este botón manda correos de verdad a los vecinos. Se pide
                            confirmación antes de dispararlo: el cron normalmente corre
                            solo, y pulsarlo por curiosidad les llega a la bandeja.
                        --}}
                        <a class="module-card" id="btn-cronjob" href="{{ $item['href'] }}" target="_blank" rel="noopener" data-search="{{ $item['buscar'] }}">
                            <span class="mc-ic" aria-hidden="true"><i class="{{ $item['icono'] }} icon"></i></span>
                            <span class="mc-text">
                                <b>{{ $item['titulo'] }}</b>
                                <small>
                                    @if($cron)
                                        Última ejecución: {{ $cron['cuando'] }}@if($cron['origen'] === 'manual') (manual)@endif
                                    @else
                                        {{ $item['desc'] }}
                                    @endif
                                </small>
                                @if($cron && $cron['atrasado'])
                                    <span class="cron-alerta">
                                        <i class="exclamation triangle icon"></i>
                                        Sin correr hace {{ $cron['horas'] }} h
                                    </span>
                                @endif
                            </span>
                            <span class="mc-go" aria-hidden="true"><i class="external alternate icon"></i></span>
                        </a>
                    @else
                        <a class="module-card" href="{{ $item['href'] }}" data-search="{{ $item['buscar'] ?? '' }}">
                            <span class="mc-ic" aria-hidden="true"><i class="{{ $item['icono'] }} icon"></i></span>
                            <span class="mc-text">
                                <b>{{ $item['titulo'] }}</b>
                                <small>{{ $item['desc'] }}</small>
                            </span>
                            <span class="mc-go" aria-hidden="true"><i class="angle right icon"></i></span>
                        </a>
                    @endif
                @endforeach
            </article>
        @endforeach
    </div>

    <div id="noResults" role="status">
        <i class="search icon"></i>
        No encontramos esa opción. Prueba con otra palabra.
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

            // Buscador de opciones del panel.
            var entrada = document.getElementById('moduleSearch');
            var limpiar = document.getElementById('clearSearch');
            var tarjetas = document.querySelectorAll('.module-card');
            var grupos = document.querySelectorAll('.module-group');
            var sinResultados = document.getElementById('noResults');

            function normalizar(t) {
                return t.toLowerCase().normalize('NFD').replace(/[̀-ͯ]/g, '').trim();
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
