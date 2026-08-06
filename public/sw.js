// public/sw.js
// Debe servirse desde la RAÍZ del dominio (no /js/sw.js) para que su
// scope cubra toda la app. Requiere HTTPS (o localhost en desarrollo).

self.addEventListener('install', () => {
    self.skipWaiting();
});

self.addEventListener('activate', (event) => {
    event.waitUntil(self.clients.claim());
});

// Passthrough simple: no cachea nada (esto es un sistema de aprobaciones,
// mostrar datos viejos por error sería peor que no tener caché). Existe
// sobre todo porque algunos navegadores solo ofrecen "Instalar app" si el
// service worker tiene un listener de fetch activo.
self.addEventListener('fetch', (event) => {
    event.respondWith(fetch(event.request));
});

// Llega cuando el backend envía el push vía minishlink/web-push (WebPushChannel.php)
self.addEventListener('push', (event) => {
    if (!event.data) return;

    let payload;
    try {
        payload = event.data.json();
    } catch (e) {
        payload = { title: 'Papeletas', body: event.data.text() };
    }

    const opciones = {
        body: payload.body || '',
        icon: '/icons/icon-192.png',
        badge: '/icons/badge-72.png',
        data: { url: payload.url || '/papeletas' },
        vibrate: [100, 50, 100],
    };

    event.waitUntil(
        self.registration.showNotification(payload.title || 'Papeletas', opciones)
    );
});

// Click en la notificación: enfoca una pestaña existente o abre una nueva
self.addEventListener('notificationclick', (event) => {
    event.notification.close();
    const url = event.notification.data?.url || '/papeletas';

    event.waitUntil(
        self.clients.matchAll({ type: 'window', includeUncontrolled: true }).then((clientList) => {
            for (const client of clientList) {
                if (client.url.includes(url) && 'focus' in client) {
                    return client.focus();
                }
            }
            if (self.clients.openWindow) {
                return self.clients.openWindow(url);
            }
        })
    );
});
