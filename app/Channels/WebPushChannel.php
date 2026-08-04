<?php

namespace App\Channels;

use App\Enums\CanalNotificacion;
use App\Models\NotificacionSistema;
use App\Notifications\BasePapeletaNotification;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;

class WebPushChannel
{
    public function send(object $notifiable, Notification $notification): void
    {
        if (! $notification instanceof BasePapeletaNotification) {
            return;
        }

        $subscripciones = $notifiable->pushSubscriptions()->activas()->get();

        if ($subscripciones->isEmpty()) {
            return;
        }

        $webPush = new WebPush([
            'VAPID' => [
                'subject' => config('webpush.vapid.subject'),
                'publicKey' => config('webpush.vapid.public_key'),
                'privateKey' => config('webpush.vapid.private_key'),
            ],
        ]);

        $payload = json_encode([
            'title' => $notification->titulo(),
            'body' => $notification->mensaje(),
            'url' => url("/papeletas/{$notification->papeleta->id}"),
        ]);

        $enviadoAlMenosUno = false;

        foreach ($subscripciones as $sub) {
            $webPush->queueNotification(
                Subscription::create($sub->toWebPushSubscription()),
                $payload
            );
        }

        foreach ($webPush->flush() as $reporte) {
            $endpoint = $reporte->getRequest()->getUri()->__toString();
            $subscripcion = $subscripciones->firstWhere('endpoint', $endpoint);

            if ($reporte->isSuccess()) {
                $enviadoAlMenosUno = true;

                continue;
            }

            // 404/410 = suscripción expirada o revocada por el navegador: desactivar sin borrar.
            if ($subscripcion && in_array($reporte->getResponse()?->getStatusCode(), [404, 410], true)) {
                $subscripcion->update(['activo' => false]);
            }

            Log::warning('Web Push fallido', [
                'endpoint' => $endpoint,
                'reason' => $reporte->getReason(),
            ]);
        }

        NotificacionSistema::create([
            'user_id' => $notifiable->id,
            'papeleta_id' => $notification->papeleta->id,
            'tipo' => $notification->tipo(),
            'canal' => CanalNotificacion::PUSH->value,
            'titulo' => $notification->titulo(),
            'mensaje' => $notification->mensaje(),
            'enviada_at' => $enviadoAlMenosUno ? now() : null,
        ]);
    }
}
