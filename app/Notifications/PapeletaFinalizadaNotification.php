<?php

namespace App\Notifications;

use App\Enums\TipoMarcacion;
use App\Models\Papeleta;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Única notificación por correo de todo el ciclo de vida de la papeleta.
 * Se dispara una sola vez, cuando el jefe confirma el retorno y el proceso
 * queda FINALIZADO (ver ConfirmarRetornoAction). Todo el resto del flujo
 * (solicitud, aprobaciones, marcación de salida) solo notifica por los
 * canales internos (campana/webpush), definidos en BasePapeletaNotification.
 *
 * Sirve como constancia: incluye horas de marcación GPS, si cayeron dentro
 * del radio permitido de la sede, y quién aprobó cada paso.
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
        $this->papeleta->loadMissing(['marcaciones', 'flujoAprobaciones.usuario', 'jefe', 'motivo']);

        $salida = $this->papeleta->marcaciones->firstWhere('tipo', TipoMarcacion::SALIDA->value);
        $retorno = $this->papeleta->marcaciones->firstWhere('tipo', TipoMarcacion::RETORNO->value);

        $mail = (new MailMessage)
            ->subject("Papeleta {$this->papeleta->codigo} finalizada")
            ->greeting("Hola {$notifiable->name},")
            ->line("Tu papeleta {$this->papeleta->codigo} completó todo el proceso y quedó finalizada.")
            ->line('Este correo sirve como constancia de tu salida.')
            ->line('**Datos de la papeleta**')
            ->line("Motivo: {$this->papeleta->motivo->nombre}")
            ->line("Destino: {$this->papeleta->destino}")
            ->line('Fecha de salida: '.$this->papeleta->fecha_salida->format('d/m/Y'));

        if ($salida) {
            $mail->line('**Marcación de salida**')
                ->line('Hora: '.$salida->created_at->format('d/m/Y H:i'))
                ->line($this->descripcionRadio($salida->dentro_radio_permitido));
        }

        if ($retorno) {
            $mail->line('**Marcación de retorno**')
                ->line('Hora: '.$retorno->created_at->format('d/m/Y H:i'))
                ->line($this->descripcionRadio($retorno->dentro_radio_permitido));
        }

        foreach ($this->papeleta->flujoAprobaciones as $flujo) {
            $mail->line("Aprobado por {$flujo->rol} — {$flujo->usuario?->name} ({$flujo->created_at->format('d/m/Y H:i')})");
        }

        if ($this->papeleta->jefe) {
            $mail->line("Retorno confirmado por: {$this->papeleta->jefe->name}");
        }

        return $mail
            ->action('Ver papeleta', route('papeletas.show', $this->papeleta))
            ->line('Este es el único correo que recibirás sobre esta papeleta; el resto de novedades quedan en tus notificaciones dentro del sistema.');
    }

    private function descripcionRadio(?bool $dentroDelRadio): string
    {
        return match ($dentroDelRadio) {
            true => '✓ Dentro del radio permitido de la sede.',
            false => '⚠ Fuera del radio permitido de la sede.',
            null => 'Sede sin coordenadas configuradas para validar radio.',
        };
    }
}
