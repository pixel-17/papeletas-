<?php

namespace App\Policies;

use App\Enums\EstadoPapeleta;
use App\Enums\RolUsuario;
use App\Enums\TipoObservacion;
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
     * Adjuntar documento: solo el propio trabajador. Dos casos habilitan la
     * subida:
     *  - El motivo de la papeleta exige documento y todavía no tiene uno.
     *  - Le observaron pidiendo sustento (JUSTIFICACION) y esa observación
     *    sigue sin atender — aquí SÍ se permite sumar otro archivo aunque ya
     *    tenga uno, porque es justo lo que la observación está pidiendo.
     */
    public function adjuntar(User $user, Papeleta $papeleta): bool
    {
        if ($user->id !== $papeleta->trabajador_id) {
            return false;
        }

        $pideSustentoPorObservacion = $papeleta->observaciones
            ->where('atendida', false)
            ->contains(fn ($o) => $o->tipo === TipoObservacion::JUSTIFICACION);

        if ($pideSustentoPorObservacion) {
            return true;
        }

        return $papeleta->motivo->requiere_documento && $papeleta->adjuntos->isEmpty();
    }

    /**
     * Responder una observación: solo el propio trabajador, y solo mientras
     * la papeleta sigue OBSERVADO (ver ResponderObservacionAction — al
     * responder, la papeleta vuelve a quien la observó para que decida).
     */
    public function responderObservacion(User $user, Papeleta $papeleta): bool
    {
        return $user->id === $papeleta->trabajador_id
            && $papeleta->estado->codigo === EstadoPapeleta::OBSERVADO->value;
    }

    /**
     * Confirmar retorno: solo el jefe asignado, y solo mientras la papeleta
     * está RETORNO_MARCADO (el trabajador ya marcó GPS, falta el visto
     * bueno del jefe para que quede FINALIZADO).
     */
    public function confirmarRetorno(User $user, Papeleta $papeleta): bool
    {
        return $user->id === $papeleta->jefe_id
            && $papeleta->estado->codigo === EstadoPapeleta::RETORNO_MARCADO->value;
    }

    public function anular(User $user, Papeleta $papeleta): bool
    {
        return $user->hasRole(RolUsuario::ADMINISTRADOR)
            || ($user->id === $papeleta->trabajador_id && $papeleta->estado->codigo === EstadoPapeleta::SOLICITADO->value);
    }
}