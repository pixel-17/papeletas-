/**
 * Campana de notificaciones.
 *
 * Comportamiento pedido:
 *  - Se queda "en espera" (sin hacer nada visible) mientras no hay novedades.
 *  - Cuando el backend reporta notificaciones nuevas, se actualiza la lista,
 *    sube el contador y suena un aviso corto (como una notificación de chat).
 *  - No se generan sonidos ni se molesta al usuario si nada cambió.
 *
 * No usamos WebSockets/Echo aquí: el proyecto no tiene un servidor de
 * broadcasting configurado, así que esto funciona por polling ligero
 * (cada 12s, y solo mientras la pestaña está visible). Junto con el Web
 * Push que ya existe para cuando la pestaña está cerrada, cubre el caso de
 * "me entero casi al instante" sin añadir infraestructura nueva.
 */

const INTERVALO_MS = 12000;

/**
 * Beep sintetizado con Web Audio API (dos tonos cortos, tipo "ding").
 * Se genera en el navegador: no depende de ningún archivo de audio externo.
 */
function reproducirSonidoAviso() {
    try {
        const Ctx = window.AudioContext || window.webkitAudioContext;
        const ctx = new Ctx();
        const ahora = ctx.currentTime;

        [880, 1320].forEach((frecuencia, i) => {
            const osc = ctx.createOscillator();
            const gain = ctx.createGain();

            osc.type = 'sine';
            osc.frequency.value = frecuencia;

            const inicio = ahora + i * 0.11;
            gain.gain.setValueAtTime(0, inicio);
            gain.gain.linearRampToValueAtTime(0.15, inicio + 0.01);
            gain.gain.exponentialRampToValueAtTime(0.001, inicio + 0.18);

            osc.connect(gain).connect(ctx.destination);
            osc.start(inicio);
            osc.stop(inicio + 0.2);
        });

        setTimeout(() => ctx.close(), 500);
    } catch (e) {
        // Navegadores que bloquean audio sin interacción previa: se ignora,
        // la campana/contador ya se actualizó visualmente igual.
    }
}

function formatoRelativo(fechaIso) {
    const diffMs = Date.now() - new Date(fechaIso).getTime();
    const minutos = Math.round(diffMs / 60000);

    if (minutos < 1) return 'ahora';
    if (minutos < 60) return `hace ${minutos} min`;

    const horas = Math.round(minutos / 60);
    if (horas < 24) return `hace ${horas} h`;

    const dias = Math.round(horas / 24);
    return `hace ${dias} d`;
}

export default function notificacionesCampana() {
    return {
        abierto: false,
        cargando: true,
        primeraCarga: true,
        notificaciones: [],
        noLeidas: 0,

        init() {
            this.consultar();

            this.temporizador = setInterval(() => {
                if (!document.hidden) this.consultar();
            }, INTERVALO_MS);

            document.addEventListener('visibilitychange', () => {
                if (!document.hidden) this.consultar();
            });
        },

        async consultar() {
            try {
                const res = await fetch('/notificaciones', {
                    headers: { Accept: 'application/json' },
                });
                if (!res.ok) return;

                const data = await res.json();
                const huboNuevas = !this.primeraCarga && data.no_leidas > this.noLeidas;

                this.notificaciones = data.notificaciones;
                this.noLeidas = data.no_leidas;
                this.cargando = false;
                this.primeraCarga = false;

                if (huboNuevas) reproducirSonidoAviso();
            } catch (e) {
                // Sin conexión momentánea: se queda "en espera" y reintenta
                // en el siguiente ciclo, sin romper la interfaz.
            }
        },

        formatoFecha(fecha) {
            return formatoRelativo(fecha);
        },

        async marcarLeida(notificacion) {
            if (notificacion.leida_at) return;

            notificacion.leida_at = new Date().toISOString();
            this.noLeidas = Math.max(0, this.noLeidas - 1);

            await fetch(`/notificaciones/${notificacion.id}/leida`, {
                method: 'POST',
                headers: {
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
            });
        },

        async marcarTodas() {
            this.notificaciones.forEach((n) => (n.leida_at = n.leida_at || new Date().toISOString()));
            this.noLeidas = 0;

            await fetch('/notificaciones/leidas', {
                method: 'POST',
                headers: {
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
            });
        },
    };
}
