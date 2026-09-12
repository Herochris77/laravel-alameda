/*
 * Service Worker - Alameda Condominio
 * Maneja las notificaciones Web Push (funcionan aunque la app esté cerrada).
 * Debe estar servido desde la raíz del sitio: https://tudominio.com/sw.js
 */

self.addEventListener('install', function (event) {
    // Activa la nueva versión del SW de inmediato.
    self.skipWaiting();
});

self.addEventListener('activate', function (event) {
    event.waitUntil(self.clients.claim());
});

// Llega un mensaje push desde el servidor.
self.addEventListener('push', function (event) {
    let payload = {};

    try {
        payload = event.data ? event.data.json() : {};
    } catch (e) {
        payload = { title: 'Alameda', body: event.data ? event.data.text() : '' };
    }

    // El paquete de Laravel envía los datos dentro de "data" o en la raíz.
    const data = payload.data || payload;

    const title = payload.title || data.title || 'Alameda Condominio';
    const options = {
        body: payload.body || data.body || '',
        icon: payload.icon || data.icon || '/img/logo-header.png',
        badge: payload.badge || data.badge || '/img/logo-header.png',
        tag: payload.tag || data.tag || 'alameda-notif',
        renotify: true,
        requireInteraction: false,
        data: {
            url: (payload.data && payload.data.url) || data.url || '/inicio',
        },
    };

    event.waitUntil(self.registration.showNotification(title, options));
});

// El usuario hace clic en la notificación.
self.addEventListener('notificationclick', function (event) {
    event.notification.close();

    const destino = (event.notification.data && event.notification.data.url) || '/inicio';

    event.waitUntil(
        self.clients.matchAll({ type: 'window', includeUncontrolled: true }).then(function (clientList) {
            // Si ya hay una pestaña abierta, la enfocamos y navegamos.
            for (const client of clientList) {
                if ('focus' in client) {
                    client.focus();
                    if ('navigate' in client) {
                        try { client.navigate(destino); } catch (e) {}
                    }
                    return;
                }
            }
            // Si no hay pestañas, abrimos una nueva.
            if (self.clients.openWindow) {
                return self.clients.openWindow(destino);
            }
        })
    );
});
