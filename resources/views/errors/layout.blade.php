{{--
    Plantilla de las pantallas de error. Pensada para quien no sabe qué es un
    "error 419": dice qué pasó en una frase y ofrece UN botón claro para seguir.

    Variables: $titulo, $mensaje, $animacion (opcional: acceso|bloqueo),
    $boton (texto), $destino (url) y $codigo (solo se muestra en letra chica,
    para que la mesa directiva pueda reportarlo).

    No toca la base de datos ni la sesión: una pantalla de error nunca debe
    fallar por la misma causa que la provocó.
--}}
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#0b6b45">
    <meta name="robots" content="noindex">
    <title>{{ $titulo }} · Alameda</title>
    <link rel="icon" href="{{ URL::to('/') }}/img/logo-header.png">
    @include('layouts.partials.fuentes')
    {!! \App\Support\Vendor::css('fomantic.css') !!}
    {!! \App\Support\Vendor::estilosSistema() !!}
    @if (!empty($animacion))
        {!! \App\Support\Vendor::js('lottie.js') !!}
    @endif
</head>

<body class="acceso">
    <main class="auth-wrapper" style="text-align:center">
        <div class="auth-card">
            <div class="auth-header">
                @if (!empty($animacion))
                    <div id="animacion-error" role="img" aria-label="" style="width:220px;height:220px;margin:0 auto"></div>
                    <script>
                        if (typeof lottie !== 'undefined') {
                            lottie.loadAnimation({
                                container: document.getElementById('animacion-error'),
                                renderer: 'svg', loop: true, autoplay: true,
                                path: '{{ asset("lottie/".$animacion.".json") }}'
                            });
                        }
                    </script>
                @else
                    <div class="auth-icon" aria-hidden="true"><i class="{{ $icono ?? 'home' }} icon"></i></div>
                @endif
                <h1 class="auth-title">{{ $titulo }}</h1>
                <p class="auth-subtitle">{{ $mensaje }}</p>
            </div>

            <div class="auth-body">
                <a href="{{ $destino ?? url('/') }}" class="btn-auth" style="text-decoration:none">
                    <i class="home icon"></i> {{ $boton ?? 'Ir al inicio' }}
                </a>
            </div>

            <div class="auth-footer">
                @isset($codigo)
                    <p class="auth-legal">Código {{ $codigo }}</p>
                @endisset
            </div>
        </div>
    </main>
</body>

</html>
