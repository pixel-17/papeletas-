<?php

namespace App\Enums;

/**
 * No es una columna enum de BD: espeja los códigos de la tabla catálogo
 * `estados` para evitar strings mágicos en Actions/Policies/Controllers.
 */
enum EstadoPapeleta: string
{
    case SOLICITADO = 'SOLICITADO';
    case APROBADO_JEFE = 'APROBADO_JEFE';
    case APROBADO_RRHH = 'APROBADO_RRHH';
    case EN_CURSO = 'EN_CURSO';
    case RETORNO_MARCADO = 'RETORNO_MARCADO';
    case FINALIZADO = 'FINALIZADO';
    case RECHAZADO = 'RECHAZADO';
    case OBSERVADO = 'OBSERVADO';
    case VENCIDA = 'VENCIDA';
}
