<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Estado intermedio: el trabajador marca retorno (GPS) pero la papeleta ya
 * no pasa directo a FINALIZADO — queda RETORNO_MARCADO hasta que el jefe
 * confirma. Ver ConfirmarRetornoAction.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::table('estados')->updateOrInsert(
            ['codigo' => 'RETORNO_MARCADO'],
            [
                'nombre' => 'Retorno Marcado',
                'color' => 'cyan',
                'orden' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // Corre esto una vez, si tu tabla `estados` ya tenía FINALIZADO en
        // orden=5 y RECHAZADO/OBSERVADO/VENCIDA por encima: reordena el resto
        // para no romper el orden visual de las bandejas.
        DB::table('estados')->where('codigo', 'FINALIZADO')->update(['orden' => 6]);
        DB::table('estados')->where('codigo', 'RECHAZADO')->update(['orden' => 7]);
        DB::table('estados')->where('codigo', 'OBSERVADO')->update(['orden' => 8]);
        DB::table('estados')->where('codigo', 'VENCIDA')->update(['orden' => 9]);
    }

    public function down(): void
    {
        DB::table('papeletas')
            ->whereIn('estado_id', function ($q) {
                $q->select('id')->from('estados')->where('codigo', 'RETORNO_MARCADO');
            })
            ->update(['estado_id' => DB::table('estados')->where('codigo', 'EN_CURSO')->value('id')]);

        DB::table('estados')->where('codigo', 'RETORNO_MARCADO')->delete();

        DB::table('estados')->where('codigo', 'FINALIZADO')->update(['orden' => 5]);
        DB::table('estados')->where('codigo', 'RECHAZADO')->update(['orden' => 6]);
        DB::table('estados')->where('codigo', 'OBSERVADO')->update(['orden' => 7]);
        DB::table('estados')->where('codigo', 'VENCIDA')->update(['orden' => 8]);
    }
};
