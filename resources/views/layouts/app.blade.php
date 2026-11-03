<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    {{-- Sin maximum-scale ni user-scalable=no: bloquearlos impide hacer zoom con los
         dedos, y quien ve poco depende de eso (WCAG 1.4.4). Los campos de formulario
         usan 16px en móvil para que iOS no haga zoom solo al enfocarlos. --}}
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="vapid-public-key" content="{{ config('webpush.vapid.public_key') }}">

    {{-- PWA: instalable y a pantalla completa (experiencia tipo app) --}}
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#0b6b45">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="Alameda">
    <link rel="apple-touch-icon" href="{{ URL::to('/') }}/img/logo-header.png">
    <meta name="push-subscribe-url" content="{{ route('usuario.perfil.push.suscribir') }}">
    <meta name="push-unsubscribe-url" content="{{ route('usuario.perfil.push.desuscribir') }}">
    <link rel="icon" href="{{ URL::to('/') }}/img/logo-header.png">

    {{-- Cada pantalla con su propio título (WCAG 2.4.2). Se puede fijar con <x-app-layout titulo="..."> --}}
    <title>{{ $titulo ?? \App\Support\Navegacion::tituloActual() }}</title>

    {{-- Fuentes, jQuery, Fomantic y Alertify: locales, con respaldo a CDN. --}}
    @include('layouts.partials.vendor-core')

    {{-- Sistema de diseño "Casa Alameda": colores, tipografía y componentes. --}}
    {!! \App\Support\Vendor::estilosSistema() !!}
</head>

<body>
    {{-- Loader: solo la animación Lottie de temporada. --}}
    @include('layouts.partials.loader')

    @include('layouts.navigation')

    <main id="contenido-principal" class="page-container" tabindex="-1">
        {{ $slot }}
    </main>

    {{--
        Ventana de aceptación del aviso. Va en el layout para que aparezca en
        CUALQUIER pantalla: muchos vecinos tienen la sesión guardada y nunca
        vuelven a pasar por el login.
    --}}
    @include('legal.modal-aceptacion')

    {{--
        Pie legal en todas las pantallas del sistema. Va aquí y no solo en el
        inicio para que el vecino pueda consultar el aviso desde donde esté,
        sin tener que salir a buscarlo.
    --}}
    <footer class="pie-legal">
        <a href="{{ route('legal.privacidad') }}">Aviso de Privacidad</a>
        <span>·</span>
        <a href="{{ route('legal.terminos') }}">Términos de Uso</a>
        <span>·</span>
        <span class="pie-legal-marca">Condominio Alameda</span>
    </footer>

    <script>
        // Alertify
        alertify.defaults.transition = "zoom";
        alertify.defaults.theme.ok = "btn btn-primary";
        alertify.defaults.theme.cancel = "btn btn-secondary";
        alertify.defaults.glossary.ok = 'Aceptar';
        alertify.defaults.glossary.cancel = 'Cancelar';

        // Toda petición AJAX lleva el token CSRF (antes dependía de que cada pantalla lo recordara).
        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
    </script>

    {{-- ==================== Web Push (notificaciones del navegador) ==================== --}}
    @include('layouts.partials.webpush')
    @include('layouts.partials.comprimir-imagen')
    @include('layouts.partials.validacion-formularios')
</body>

</html>
