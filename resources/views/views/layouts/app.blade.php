
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="vapid-public-key" content="{{ config('webpush.vapid.public_key') }}">

    {{-- PWA: instalable y a pantalla completa (experiencia tipo app) --}}
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#0d9488">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="Alameda">
    <link rel="apple-touch-icon" href="{{ URL::to('/') }}/img/logo-header.png">
    <meta name="push-subscribe-url" content="{{ route('usuario.perfil.push.suscribir') }}">
    <meta name="push-unsubscribe-url" content="{{ route('usuario.perfil.push.desuscribir') }}">
    <link rel="icon" href="{{ URL::to('/') }}/img/logo-header.png">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Fomantic UI CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fomantic-ui@2.9.2/dist/semantic.min.css">

    <!-- AlertifyJS -->
    <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/alertify.min.css" />
    <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/themes/default.min.css" />
    <script src="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/alertify.min.js"></script>

    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.semanticui.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.semanticui.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.semanticui.min.css">
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.semanticui.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.semanticui.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.semanticui.min.js"></script>

    <!-- JSZip y PDFMake -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

    <!-- Fomantic UI JS -->
    <script src="https://cdn.jsdelivr.net/npm/fomantic-ui@2.9.2/dist/semantic.min.js"></script>

    <!-- Lottie Animation -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lottie-web/5.12.2/lottie.min.js"></script>

    <!-- Estilos centralizados (Alameda) -->
    <link rel="stylesheet" href="{{ asset('css/theme.css') }}?v={{ @filemtime(public_path('css/theme.css')) }}">
    <script>
        (function(){try{var t=localStorage.getItem('alameda-theme');if(t==='dark'||t==='light'){document.documentElement.setAttribute('data-theme',t);}}catch(e){}})();
    </script>

    {{-- Estilos por página (cada vista sube su hoja aquí) --}}
    @stack('styles')
</head>

<body>
    <!-- Loader -->
    <div id="fullscreen-loader" class="loader-overlay">
        <div id="lottie-loader" style="width: 200px; height: 200px;"></div>
    </div>

    <!-- Navigation -->
    @include('layouts.navigation')

    <!-- Main Content -->
    <main class="page-container">
        {{ $slot }}
    </main>
</body>

<script>
    // Alertify defaults
    alertify.defaults.transition = "zoom";
    alertify.defaults.theme.ok = "btn btn-primary";
    alertify.defaults.theme.cancel = "btn btn-secondary";
    alertify.defaults.glossary.ok = 'Aceptar';
    alertify.defaults.glossary.cancel = 'Cancelar';

    // Lottie Loader
    function getLottieAsset() {
        const today = new Date();
        const year = today.getFullYear();
        const dec1 = new Date(year, 11, 1);
        const jan10 = new Date(year + 1, 0, 10);
        const oct1 = new Date(year, 9, 1);
        const nov2 = new Date(year, 10, 2);

        if (today >= dec1 && today <= jan10) {
            return '{{ asset("lottie/santa.json") }}';
        } else if (today >= oct1 && today <= nov2) {
            return '{{ asset("lottie/fantasma.json") }}';
        }
        return '{{ asset("lottie/loader.json") }}';
    }

    var asset = getLottieAsset();
    if (typeof lottie !== 'undefined') {
        const loaderAnimation = lottie.loadAnimation({
            container: document.getElementById('lottie-loader'),
            renderer: 'svg',
            loop: true,
            autoplay: true,
            path: asset
        });
    }

    $('#fullscreen-loader').addClass('active');
    window.onload = function() {
        $('#fullscreen-loader').removeClass('active');
    };

    function mostrarLoaderPantalla() {
        $('#fullscreen-loader').addClass('active');
    }

    function ocultarLoaderPantalla() {
        $('#fullscreen-loader').removeClass('active');
    }
</script>

{{-- ==================== Web Push (notificaciones del navegador) ==================== --}}
<script>
    window.AppPush = (function () {
        const vapidPublicKey = document.querySelector('meta[name="vapid-public-key"]')?.content || '';
        const subscribeUrl = document.querySelector('meta[name="push-subscribe-url"]')?.content || '';
        const unsubscribeUrl = document.querySelector('meta[name="push-unsubscribe-url"]')?.content || '';
        const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';

        let swRegistration = null;

        function soportado() {
            return ('serviceWorker' in navigator) && ('PushManager' in window) && ('Notification' in window);
        }

        function urlBase64ToUint8Array(base64String) {
            const padding = '='.repeat((4 - (base64String.length % 4)) % 4);
            const base64 = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');
            const rawData = window.atob(base64);
            const outputArray = new Uint8Array(rawData.length);
            for (let i = 0; i < rawData.length; ++i) {
                outputArray[i] = rawData.charCodeAt(i);
            }
            return outputArray;
        }

        async function registrarSW() {
            if (!soportado()) return null;
            if (swRegistration) return swRegistration;
            swRegistration = await navigator.serviceWorker.register('/sw.js');
            await navigator.serviceWorker.ready;
            return swRegistration;
        }

        async function estaSuscrito() {
            if (!soportado()) return false;
            const reg = await registrarSW();
            if (!reg) return false;
            const sub = await reg.pushManager.getSubscription();
            return !!sub;
        }

        async function activar() {
            if (!soportado()) {
                throw new Error('Tu navegador no soporta notificaciones push.');
            }
            if (!vapidPublicKey) {
                throw new Error('Falta configurar la llave pública VAPID en el servidor.');
            }

            const permiso = await Notification.requestPermission();
            if (permiso !== 'granted') {
                throw new Error('Debes permitir las notificaciones en tu navegador.');
            }

            const reg = await registrarSW();
            let sub = await reg.pushManager.getSubscription();

            if (!sub) {
                sub = await reg.pushManager.subscribe({
                    userVisibleOnly: true,
                    applicationServerKey: urlBase64ToUint8Array(vapidPublicKey),
                });
            }

            const json = sub.toJSON();

            const resp = await fetch(subscribeUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
                body: JSON.stringify({
                    endpoint: sub.endpoint,
                    keys: { p256dh: json.keys.p256dh, auth: json.keys.auth },
                    contentEncoding: (PushManager.supportedContentEncodings || ['aesgcm'])[0],
                }),
            });

            if (!resp.ok) throw new Error('No se pudo guardar la suscripción en el servidor.');
            return true;
        }

        async function desactivar() {
            if (!soportado()) return true;
            const reg = await registrarSW();
            const sub = await reg.pushManager.getSubscription();
            if (!sub) return true;

            const endpoint = sub.endpoint;

            await fetch(unsubscribeUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
                body: JSON.stringify({ endpoint }),
            });

            await sub.unsubscribe();
            return true;
        }

        // Registra el SW de forma silenciosa en cada carga (para recibir push).
        if (soportado()) {
            registrarSW().catch(function () {});
        }

        return { soportado, estaSuscrito, activar, desactivar };
    })();
</script>

</html>