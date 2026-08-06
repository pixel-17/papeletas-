<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EstadoSeeder extends Seeder
{
    public function run(): void
    {
        $estados = [
            ['codigo' => 'SOLICITADO', 'nombre' => 'Solicitado', 'color' => 'gray', 'orden' => 1],
            ['codigo' => 'APROBADO_JEFE', 'nombre' => 'Aprobado por Jefe', 'color' => 'blue', 'orden' => 2],
            ['codigo' => 'APROBADO_RRHH', 'nombre' => 'Aprobado por RRHH', 'color' => 'indigo', 'orden' => 3],
            ['codigo' => 'EN_CURSO', 'nombre' => 'En Curso', 'color' => 'yellow', 'orden' => 4],
            ['codigo' => 'RETORNO_MARCADO', 'nombre' => 'Retorno Marcado', 'color' => 'cyan', 'orden' => 5],
            ['codigo' => 'FINALIZADO', 'nombre' => 'Finalizado', 'color' => 'green', 'orden' => 6],
            ['codigo' => 'RECHAZADO', 'nombre' => 'Rechazado', 'color' => 'red', 'orden' => 7],
            ['codigo' => 'OBSERVADO', 'nombre' => 'Observado', 'color' => 'orange', 'orden' => 8],
            ['codigo' => 'VENCIDA', 'nombre' => 'Vencida', 'color' => 'red', 'orden' => 9],
        ];

        foreach ($estados as $estado) {
            DB::table('estados')->updateOrInsert(
                ['codigo' => $estado['codigo']],
                array_merge($estado, ['created_at' => now(), 'updated_at' => now()])
            );
        }
    }
}
