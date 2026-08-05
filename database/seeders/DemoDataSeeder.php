<?php

namespace Database\Seeders;

use App\Models\Area;
use App\Models\Cargo;
use App\Models\Motivo;
use App\Models\Sede;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoDataSeeder extends Seeder
{
    /**
     * Ejecutar DESPUÉS de EstadoSeeder:
     *   php artisan db:seed --class=EstadoSeeder
     *   php artisan db:seed --class=DemoDataSeeder
     */
    public function run(): void
    {
        $area = Area::firstOrCreate(['nombre' => 'Gerencia de Administración'], ['siglas' => 'GA']);
        $cargo = Cargo::firstOrCreate(['area_id' => $area->id, 'nombre' => 'Asistente Administrativo']);
        $sede = Sede::firstOrCreate(['nombre' => 'Sede Central'], [
            'direccion' => 'Plaza de Armas s/n',
            'latitud' => -12.0463731,
            'longitud' => -77.0427934,
            'radio_permitido' => 150,
        ]);

        Motivo::firstOrCreate(['nombre' => 'Trámite documentario'], [
            'requiere_documento' => false,
            'goce_haber' => true,
            'max_horas' => 4,
        ]);
        Motivo::firstOrCreate(['nombre' => 'Cita médica'], [
            'requiere_documento' => true,
            'goce_haber' => true,
            'max_horas' => 6,
        ]);

        $admin = User::updateOrCreate(
            ['email' => 'admin@demo.test'],
            [
                'name' => 'Ana Administradora',
                'password' => Hash::make('password'),
                'dni' => '10000001',
                'rol' => 'ADMINISTRADOR',
                'cargo_id' => $cargo->id,
                'sede_id' => $sede->id,
                'email_verified_at' => now(),
            ]
        );

        $jefe = User::updateOrCreate(
            ['email' => 'jefe@demo.test'],
            [
                'name' => 'Jorge Jefe de Área',
                'password' => Hash::make('password'),
                'dni' => '10000002',
                'rol' => 'JEFE',
                'cargo_id' => $cargo->id,
                'sede_id' => $sede->id,
                'email_verified_at' => now(),
            ]
        );

        $rrhh = User::updateOrCreate(
            ['email' => 'rrhh@demo.test'],
            [
                'name' => 'Rosa RRHH',
                'password' => Hash::make('password'),
                'dni' => '10000003',
                'rol' => 'RRHH',
                'cargo_id' => $cargo->id,
                'sede_id' => $sede->id,
                'email_verified_at' => now(),
            ]
        );

        User::updateOrCreate(
            ['email' => 'trabajador@demo.test'],
            [
                'name' => 'Tito Trabajador',
                'password' => Hash::make('password'),
                'dni' => '10000004',
                'rol' => 'TRABAJADOR',
                'cargo_id' => $cargo->id,
                'sede_id' => $sede->id,
                'jefe_id' => $jefe->id,
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('Usuarios demo (password para todos: "password"):');
        $this->command->table(
            ['Rol', 'Email'],
            [
                ['ADMINISTRADOR', 'admin@demo.test'],
                ['JEFE', 'jefe@demo.test'],
                ['RRHH', 'rrhh@demo.test'],
                ['TRABAJADOR', 'trabajador@demo.test'],
            ]
        );
    }
}
