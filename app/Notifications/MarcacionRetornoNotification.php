<?php

namespace App\Notifications;

class MarcacionRetornoNotification extends BasePapeletaNotification
{
    public function tipo(): string
    {
        return 'CIERRE_FORMAL';
    }

    public function titulo(): string
    {
        return "{$this->papeleta->trabajador->name} marcó su retorno";
    }

    public function mensaje(): string
    {
        return "Papeleta {$this->papeleta->codigo}: finalizada correctamente.";
    }
}
