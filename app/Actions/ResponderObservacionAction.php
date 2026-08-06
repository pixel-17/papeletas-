<?php

namespace App\Actions;

use App\Enums\EstadoPapeleta;
use App\Enums\TipoObservacion;
use App\Models\Estado;
use App\Models\HistorialPapeleta;
use App\Models\Papeleta;
use App\Models\User;
use App\Notifications\ObservacionRespondidaNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;

class ResponderObservacionAction
{
    public function execute(Papeleta $papeleta, User $trabajador, string $respuesta): Papeleta
    {
        Gate::forUser($trabajador)->authorize('responderObservacion', $papeleta);

        $observacion = $papeleta->observaciones()->where('atendida', false)->latest()->first();

        if (! $observacion) {
            throw ValidationException::withMessages([
                'respuesta' => 'No hay una observación pendiente por responder.',
            ]);
        }

        // Semántica definida en la migración de "observaciones": ADMINISTRATIVA
        // corrige un dato y salta directo a RRHH; JUSTIFICACION exige sustento
        // y vuelve a pasar por el Jefe. No depende de quién la haya levantado.
        $estadoDestino = $observacion->tipo === TipoObservacion::ADMINISTRATIVA
            ? EstadoPapeleta::APROBADO_JEFE
            : EstadoPapeleta::SOLICITADO;

        DB::transaction(function () use ($papeleta, $trabajador, $respuesta, $observacion, $estadoDestino) {
            $observacion->update(['atendida' => true]);

            $papeleta->update(['estado_id' => Estado::porCodigo($estadoDestino)->id]);

            HistorialPapeleta::registrar(
                $papeleta, $trabajador, 'RESPONDIO_OBSERVACION',
                EstadoPapeleta::OBSERVADO->value, $estadoDestino->value, $respuesta
            );
        });

        $papeleta->refresh();

        // Si vuelve a SOLICITADO, quien decide es el jefe asignado — se le
        // notifica directo. Si vuelve a APROBADO_JEFE (RRHH), no hay un
        // usuario puntual a quien avisar: reaparece en su bandeja de
        // pendientes igual que cualquier otra papeleta nueva.
        if ($estadoDestino === EstadoPapeleta::SOLICITADO) {
            $papeleta->jefe?->notify(new ObservacionRespondidaNotification($papeleta, $respuesta));
        }

        return $papeleta;
    }
}
