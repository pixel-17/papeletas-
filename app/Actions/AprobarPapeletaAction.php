<?php

namespace App\Actions;

use App\Enums\AccionFlujo;
use App\Enums\EstadoPapeleta;
use App\Enums\RolUsuario;
use App\Models\Estado;
use App\Models\FlujoAprobacion;
use App\Models\HistorialPapeleta;
use App\Models\Papeleta;
use App\Models\User;
use App\Notifications\PapeletaAprobadaJefeNotification;
use App\Notifications\PapeletaAprobadaRrhhNotification;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Notification;

class AprobarPapeletaAction
{
    /**
     * @throws AuthorizationException
     */
    public function execute(Papeleta $papeleta, User $aprobador, ?string $comentario = null): Papeleta
    {
        Gate::forUser($aprobador)->authorize('decidir', $papeleta);

        $estadoAnteriorCodigo = $papeleta->estado->codigo;
        $esJefeDecidiendo = $estadoAnteriorCodigo === EstadoPapeleta::SOLICITADO->value;
        $nuevoCodigo = $esJefeDecidiendo ? EstadoPapeleta::APROBADO_JEFE : EstadoPapeleta::APROBADO_RRHH;

        DB::transaction(function () use ($papeleta, $aprobador, $comentario, $esJefeDecidiendo, $nuevoCodigo, $estadoAnteriorCodigo) {
            $papeleta->update(['estado_id' => Estado::porCodigo($nuevoCodigo)->id]);

            FlujoAprobacion::create([
                'papeleta_id' => $papeleta->id,
                'usuario_id' => $aprobador->id,
                'rol' => $esJefeDecidiendo ? RolUsuario::JEFE->value : RolUsuario::RRHH->value,
                'accion' => AccionFlujo::APROBADO->value,
                'comentario' => $comentario,
                'ip_origen' => request()->ip(),
                'user_agent' => (string) request()->userAgent(),
            ]);

            HistorialPapeleta::registrar(
                $papeleta, $aprobador, 'APROBADA', $estadoAnteriorCodigo, $nuevoCodigo->value, $comentario
            );
        });

        $papeleta->refresh();

        if ($esJefeDecidiendo) {
            $papeleta->trabajador->notify(new PapeletaAprobadaJefeNotification($papeleta));
            Notification::send(User::where('rol', RolUsuario::RRHH)->get(), new PapeletaAprobadaJefeNotification($papeleta));
        } else {
            $papeleta->trabajador->notify(new PapeletaAprobadaRrhhNotification($papeleta));
            $papeleta->jefe?->notify(new PapeletaAprobadaRrhhNotification($papeleta));
        }

        return $papeleta;
    }
}
