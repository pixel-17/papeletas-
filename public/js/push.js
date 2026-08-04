// resources/js/push.js
// Registra el sw.js y suscribe al usuario con la VAPID public key.
// Se llama desde un botón: <button onclick="activarNotificaciones()">

function urlBase64ToUint8Array(base64String) {
    const padding = '='.repeat((4 - (base64String.length % 4)) % 4);
    const base64 = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');
    const rawData = atob(base64);
    return Uint8Array.from([...rawData].map((c) => c.charCodeAt(0)));
}

async function activarNotificaciones() {
    if (!('serviceWorker' in navigator) || !('PushManager' in window)) {
        alert('Tu navegador no soporta notificaciones push.');
        return;
    }

    const permiso = await Notification.requestPermission();
    if (permiso !== 'granted') {
        alert('No se otorgó permiso para notificaciones.');
        return;
    }

    const registro = await navigator.serviceWorker.register('/sw.js');
    await navigator.serviceWorker.ready;

    let suscripcion = await registro.pushManager.getSubscription();

    if (!suscripcion) {
        suscripcion = await registro.pushManager.subscribe({
            userVisibleOnly: true,
            applicationServerKey: urlBase64ToUint8Array(window.VAPID_PUBLIC_KEY),
        });
    }

    await fetch('/push-subscriptions', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        },
        body: JSON.stringify(suscripcion.toJSON()),
    });
}

async function desactivarNotificaciones() {
    if (!('serviceWorker' in navigator)) return;

    const registro = await navigator.serviceWorker.getRegistration('/sw.js');
    const suscripcion = await registro?.pushManager.getSubscription();

    if (!suscripcion) return;

    await fetch('/push-subscriptions', {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        },
        body: JSON.stringify({ endpoint: suscripcion.endpoint }),
    });

    await suscripcion.unsubscribe();
}
