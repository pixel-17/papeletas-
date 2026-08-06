<?php

namespace App\Actions;

use App\Enums\EstadoPapeleta;
use App\Models\Estado;
use App\Models\HistorialPapeleta;
use App\Models\Papeleta;
use App\Models\User;
use App\Notifications\PapeletaFinalizadaNotification;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class ConfirmarRetornoAction
{
    /**
     * @throws AuthorizationException
     */
    public function execute(Papeleta $papeleta, User $jefe, ?string $comentario = null): Papeleta
    {
        Gate::forUser($jefe)->authorize('confirmarRetorno', $papeleta);

        $estadoAnterior = $papeleta->estado->codigo;

        DB::transaction(function () use ($papeleta, $jefe, $comentario, $estadoAnterior) {
            $papeleta->update(['estado_id' => Estado::porCodigo(EstadoPapeleta::FINALIZADO)->id]);

            HistorialPapeleta::registrar(
                $papeleta, $jefe, 'CONFIRMO_RETORNO', $estadoAnterior, EstadoPapeleta::FINALIZADO->value, $comentario
            );
        });

        $papeleta->refresh();

        // Único correo de todo el proceso: se envía aquí, una sola vez, en el
        // punto donde el jefe confirma el retorno y la papeleta queda
        // FINALIZADO. Lleva el detalle completo como constancia.
        $papeleta->trabajador->notify(new PapeletaFinalizadaNotification($papeleta));

        return $papeleta;
    }
}
