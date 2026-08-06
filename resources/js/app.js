

import Alpine from 'alpinejs';
import { registrarStoreNotificaciones } from './notificaciones';

window.Alpine = Alpine;

document.addEventListener('alpine:init', () => {
    registrarStoreNotificaciones(Alpine);
});

Alpine.start();
