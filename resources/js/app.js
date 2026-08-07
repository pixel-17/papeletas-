

import Alpine from 'alpinejs';
import { registrarStoreNotificaciones } from './notificaciones';
import './progress-bar';

window.Alpine = Alpine;

document.addEventListener('alpine:init', () => {
    registrarStoreNotificaciones(Alpine);
});

Alpine.start();
