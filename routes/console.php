<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Sin esto, EN_CURSO nunca pasa a VENCIDA automáticamente: el comando
// existía pero no estaba programado en ningún lado.
Schedule::command('papeletas:marcar-vencidas')
    ->everyFiveMinutes()
    ->withoutOverlapping();
