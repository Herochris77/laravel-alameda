{{--
    Notificaciones push del navegador (Web Push).
    Bloque movido tal cual desde layouts/app.blade.php: no cambia su comportamiento.
--}}
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

