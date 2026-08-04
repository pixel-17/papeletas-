<?php

namespace App\Notifications;

use App\Models\Papeleta;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

/**
 * Cada notificación concreta define tipo()/titulo()/mensaje().
 * via() manda por dos canales propios (no los nativos de Laravel):
 * - "sistema": guarda fila en notificaciones_sistema (canal=SISTEMA) para la campana.
 * - "webpush": envía Web Push real (VAPID) y además dofja constancia en
 *   notificaciones_sistema (canal=PUSH) con su propio enviada_at.
 */
abstract class BasePapeletaNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Papeleta $papeleta)
    {
    }

    abstract public function tipo(): string;

    abstract public function titulo(): string;

    abstract public function mensaje(): string;

    public function via(object $notifiable): array
    {
        return ['sistema', 'webpush'];
    }
}
