<?php

namespace App\Actions;

use App\Enums\EstadoPapeleta;
use App\Enums\TipoMarcacion;
use App\Models\Estado;
use App\Models\HistorialPapeleta;
use App\Models\Marcacion;
use App\Models\Papeleta;
use App\Models\User;
use App\Notifications\MarcacionRetornoNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;

class MarcarRetornoAction
{
    public function execute(Papeleta $papeleta, User $trabajador, array $gps): Papeleta
    {
        Gate::forUser($trabajador)->authorize('marcar', $papeleta);

        if (! $papeleta->yaMarcoSalida()) {
            throw ValidationException::withMessages([
                'marcacion' => 'No puedes marcar retorno sin haber marcado salida primero.',
            ]);
        }

        if ($papeleta->yaMarcoRetorno()) {
            throw ValidationException::withMessages([
                'marcacion' => 'Esta papeleta ya tiene una marcación de retorno registrada.',
            ]);
        }

        $dentroDelRadio = $this->calcularDentroDelRadio($papeleta, $gps['latitud'], $gps['longitud']);

        DB::transaction(function () use ($papeleta, $trabajador, $gps, $dentroDelRadio) {
            Marcacion::create([
                'papeleta_id' => $papeleta->id,
                'tipo' => TipoMarcacion::RETORNO->value,
                'latitud' => $gps['latitud'],
                'longitud' => $gps['longitud'],
                'precision_gps' => $gps['precision_gps'] ?? null,
                'direccion' => $gps['direccion'] ?? null,
                'dentro_radio_permitido' => $dentroDelRadio,
                'ip_origen' => request()->ip(),
                'user_agent' => (string) request()->userAgent(),
            ]);

            $estadoAnterior = $papeleta->estado->codigo;
            $papeleta->update(['estado_id' => Estado::porCodigo(EstadoPapeleta::FINALIZADO)->id]);

            HistorialPapeleta::registrar(
                $papeleta, $trabajador, 'MARCO_RETORNO', $estadoAnterior, EstadoPapeleta::FINALIZADO->value
            );
        });

        $papeleta->refresh();
        $papeleta->jefe?->notify(new MarcacionRetornoNotification($papeleta));

        return $papeleta;
    }

    private function calcularDentroDelRadio(Papeleta $papeleta, float $lat, float $lng): ?bool
    {
        if (! $papeleta->sede) {
            return null;
        }

        $distancia = $papeleta->sede->distanciaHaciaMetros($lat, $lng);

        return $distancia <= $papeleta->sede->radio_permitido;
    }
}
