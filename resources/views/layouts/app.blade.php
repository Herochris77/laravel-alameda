
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
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

    <!-- Estilos personalizados -->
    @include('layouts.css')

    <style>
        @media (max-width: 768px) {
          .dataTables_wrapper .dataTables_paginate {
            width: 100%;
            overflow-x: auto;
            overflow-y: hidden;
            text-align: left;
            -webkit-overflow-scrolling: touch;
          }
        
          .dataTables_wrapper .dataTables_paginate .ui.pagination.menu {
            display: flex !important;
            flex-direction: row !important;
            flex-wrap: nowrap !important;
            width: max-content !important;
            min-width: 0 !important;
          }
        
          .dataTables_wrapper .dataTables_paginate .ui.pagination.menu .item {
            flex: 0 0 auto !important;
            width: auto !important;
            white-space: nowrap;
          }
        }
        
        * {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }

        body {
            background: #f5f7fa;
            min-height: 100vh;
            overflow-x: hidden;
        }

        html {
            overflow-x: hidden;
        }

        /* Prevenir scroll horizontal en móvil */
        .page-container,
        main {
            overflow-x: hidden;
            max-width: 100vw;
        }

        /* Grid de 2 columnas con tamaño fijo */
        .grid-sidebar {
            display: grid;
            grid-template-columns: 400px 1fr;
            gap: 24px;
            align-items: start;
        }

        .grid-sidebar-sm {
            display: grid;
            grid-template-columns: 350px 1fr;
            gap: 24px;
            align-items: start;
        }

        @media (max-width: 900px) {

            .grid-sidebar,
            .grid-sidebar-sm {
                grid-template-columns: 1fr;
            }
        }

        /* DataTables responsive */
        .dataTables_scroll,
        .dataTables_wrapper {
            max-width: 100%;
        }

        /* Forzar scroll horizontal en DataTables */
        table.dataTable {
            max-width: none !important;
        }

        /* Page Container */
        .page-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 88px 20px 24px;
        }

        @media (max-width: 768px) {
            .page-container {
                padding: 72px 12px 16px;
            }
        }

        /* Page Header */
        .page-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 24px;
            flex-wrap: wrap;
            gap: 16px;
        }

        .page-header-left {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .page-icon {
            width: 56px;
            height: 56px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
        }

        .page-icon i {
            margin: 0 !important;
            line-height: 1 !important;
        }

        .menu-card-icon i {
            margin: 0 !important;
            line-height: 1 !important;
        }

        /* Icon centering for all flex containers */
        .card-body>i[class*="icon"],
        .stat-card i,
        .menu-card i,
        .opcion-item>i,
        .resultado-info>i,
        [class*="icon-container"] i,
        .icon-centered i,
        div[style*="align-items: center"][style*="justify-content: center"] i {
            margin: 0 !important;
            line-height: 1 !important;
        }

        /* Specific for circular icon containers */
        .circular-icon,
        [style*="border-radius: 50%"] i {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
        }

        .circular-icon i,
        [style*="border-radius: 50%"] i {
            margin: 0 !important;
            line-height: 1 !important;
        }

        .page-title {
            font-size: 1.75rem;
            font-weight: 700;
            color: #1e293b;
            margin: 0;
        }

        .page-subtitle {
            font-size: 0.9rem;
            color: #64748b;
            margin: 4px 0 0 0;
        }

        .page-header-actions {
            display: flex;
            gap: 12px;
        }

        /* Cards */
        .card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
            overflow: hidden;
        }

        .card-header {
            padding: 20px 24px;
            border-bottom: 1px solid #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .card-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: #1e293b;
            margin: 0;
        }

        .card-body {
            padding: 24px;
        }

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 10px 20px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 0.9rem;
            border: none;
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
        }

        .btn:active {
            transform: scale(0.98);
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-primary:hover {
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
            transform: translateY(-2px);
        }

        .btn-secondary {
            background: #f1f5f9;
            color: #475569;
        }

        .btn-secondary:hover {
            background: #e2e8f0;
        }

        .btn-success {
            background: #10b981;
            color: white;
        }

        .btn-success:hover {
            background: #059669;
        }

        .btn-danger {
            background: #ef4444;
            color: white;
        }

        .btn-danger:hover {
            background: #dc2626;
        }

        .btn-warning {
            background: #f59e0b;
            color: white;
        }

        .btn-sm {
            padding: 8px 14px;
            font-size: 0.85rem;
        }

        .btn-lg {
            padding: 14px 28px;
            font-size: 1rem;
        }

        /* Form Elements */
        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            font-weight: 600;
            color: #374151;
            margin-bottom: 8px;
            font-size: 0.9rem;
        }

        .form-input {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e5e7eb;
            border-radius: 10px;
            font-size: 0.95rem;
            transition: all 0.2s ease;
            background: white;
        }

        .form-input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .form-input.error {
            border-color: #ef4444;
        }

        /* Upload Area */
        .upload-area {
            border: 2px dashed #e2e8f0;
            border-radius: 16px;
            padding: 8px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            background: #f8fafc;
            min-height: 180px;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        .upload-area:hover,
        .upload-area.dragover {
            border-color: #667eea;
            background: #f0f4ff;
        }

        .upload-area label {
            cursor: pointer;
            width: 100%;
        }

        .upload-placeholder {
            padding: 30px 20px;
        }

        .upload-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 16px;
            transition: transform 0.3s ease;
        }

        .upload-area:hover .upload-icon {
            transform: scale(1.1) translateY(-5px);
        }

        .upload-icon i {
            font-size: 24px;
            color: white;
        }

        .upload-text .primary {
            display: block;
            font-size: 1rem;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 4px;
        }

        .upload-text .secondary {
            display: block;
            font-size: 0.85rem;
            color: #64748b;
        }

        .upload-formats {
            font-size: 0.75rem;
            color: #94a3b8;
            margin-top: 8px;
        }

        /* Tables */
        .dataTables_wrapper {
            padding: 0 !important;
        }

        .dataTables_wrapper table {
            width: 100% !important;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
        }

        .empty-state-icon {
            width: 80px;
            height: 80px;
            background: #f1f5f9;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 2rem;
            color: #94a3b8;
        }

        .empty-state h3 {
            color: #475569;
            margin-bottom: 8px;
        }

        .empty-state p {
            color: #94a3b8;
        }

        /* Alerts */
        .alert {
            padding: 14px 16px;
            border-radius: 12px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .alert-success {
            background: #d1fae5;
            color: #065f46;
        }

        .alert-error {
            background: #fee2e2;
            color: #991b1b;
        }

        .alert-warning {
            background: #fef3c7;
            color: #92400e;
        }

        .alert-info {
            background: #dbeafe;
            color: #1e40af;
        }

        /* Grid */
        .grid {
            display: grid;
            gap: 20px;
        }

        .grid-2 {
            grid-template-columns: repeat(2, 1fr);
        }

        .grid-3 {
            grid-template-columns: repeat(3, 1fr);
        }

        .grid-4 {
            grid-template-columns: repeat(4, 1fr);
        }

        @media (max-width: 1024px) {
            .grid-4 {
                grid-template-columns: repeat(2, 1fr);
            }

            .grid-3 {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {

            .grid-4,
            .grid-3,
            .grid-2 {
                grid-template-columns: 1fr;
            }
        }

        /* Icon centering */
        .page-icon {
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
        }

        /* All icons in headers */
        .card-header i,
        .card-header .icon {
            display: inline-flex !important;
            align-items: center;
            justify-content: center;
            margin: 0 !important;
            line-height: 1 !important;
        }

        /* Stat Cards */
        .stat-card {
            background: white;
            border-radius: 16px;
            padding: 20px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
        }

        .stat-card .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 12px;
        }

        .stat-card .stat-value {
            font-size: 1.75rem;
            font-weight: 700;
            color: #1e293b;
        }

        .stat-card .stat-label {
            font-size: 0.85rem;
            color: #64748b;
        }

        /* Loader */
        .loader-overlay {
            position: fixed;
            z-index: 9999;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.9);
            display: none;
            align-items: center;
            justify-content: center;
        }

        .loader-overlay.active {
            display: flex;
        }
    </style>
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

    <style>
        .pie-legal {
            padding: 18px 20px 26px;
            text-align: center;
            font-size: .8rem;
            color: #94a3b8;
        }

        .pie-legal a {
            color: #64748b;
            text-decoration: none;
        }

        .pie-legal a:hover { color: #667eea; text-decoration: underline; }

        .pie-legal span { color: #cbd5e1; margin: 0 5px; }

        .pie-legal-marca { color: #94a3b8 !important; margin-left: 5px !important; }

        @media print { .pie-legal { display: none; } }
    </style>
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