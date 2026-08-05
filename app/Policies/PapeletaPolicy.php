<?php

namespace App\Policies;

use App\Enums\EstadoPapeleta;
use App\Enums\RolUsuario;
use App\Models\Papeleta;
use App\Models\User;

class PapeletaPolicy
{
    public function ver(User $user, Papeleta $papeleta): bool
    {
        return $user->id === $papeleta->trabajador_id
            || $user->id === $papeleta->jefe_id
            || $user->hasRole(RolUsuario::RRHH)
            || $user->hasRole(RolUsuario::ADMINISTRADOR);
    }

    public function crear(User $user): bool
    {
        return $user->hasRole(RolUsuario::TRABAJADOR) || $user->hasRole(RolUsuario::JEFE);
    }

    /**
     * Aprobar/rechazar/observar: quién puede actuar depende del estado actual.
     * SOLICITADO -> decide el jefe asignado.
     * APROBADO_JEFE -> decide RRHH.
     */
    public function decidir(User $user, Papeleta $papeleta): bool
    {
        return match ($papeleta->estado->codigo) {
            EstadoPapeleta::SOLICITADO->value => $user->id === $papeleta->jefe_id,
            EstadoPapeleta::APROBADO_JEFE->value => $user->hasRole(RolUsuario::RRHH),
            default => false,
        };
    }

    /**
     * Solo el propio trabajador marca su salida/retorno.
     */
    public function marcar(User $user, Papeleta $papeleta): bool
    {
        return $user->id === $papeleta->trabajador_id
            && in_array($papeleta->estado->codigo, [
                EstadoPapeleta::APROBADO_RRHH->value,
                EstadoPapeleta::EN_CURSO->value,
            ], true);
    }

    /**
     * Adjuntar documento: solo el propio trabajador, solo si el motivo de
     * la papeleta lo exige, y solo mientras no tenga ya un adjunto (se
     * permite un único archivo por papeleta — ver AdjuntoController::store).
     */
    public function adjuntar(User $user, Papeleta $papeleta): bool
    {
        return $user->id === $papeleta->trabajador_id
            && $papeleta->motivo->requiere_documento
            && $papeleta->adjuntos->isEmpty();
    }

    public function anular(User $user, Papeleta $papeleta): bool
    {
        return $user->hasRole(RolUsuario::ADMINISTRADOR)
            || ($user->id === $papeleta->trabajador_id && $papeleta->estado->codigo === EstadoPapeleta::SOLICITADO->value);
    }
}