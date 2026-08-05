<?php

namespace App\Notifications;

use App\Models\Papeleta;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Única notificación por correo de todo el ciclo de vida de la papeleta.
 * Se dispara una sola vez, cuando el proceso queda FINALIZADO (retorno marcado).
 * Todo el resto del flujo (solicitud, aprobaciones, marcaciones) solo notifica
 * por los canales internos (campana/webpush), definidos en BasePapeletaNotification.
 */
class PapeletaFinalizadaNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Papeleta $papeleta)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Papeleta {$this->papeleta->codigo} finalizada")
            ->greeting("Hola {$notifiable->name},")
            ->line("Tu papeleta {$this->papeleta->codigo} completó todo el proceso y quedó finalizada.")
            ->line("Destino: {$this->papeleta->destino}")
            ->line('Fecha de salida: '.$this->papeleta->fecha_salida->format('d/m/Y'))
            ->action('Ver papeleta', route('papeletas.show', $this->papeleta))
            ->line('Este es el único correo que recibirás sobre esta papeleta; el resto de novedades quedan en tus notificaciones dentro del sistema.');
    }
}
