<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('flujo_aprobaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('papeleta_id')->constrained('papeletas')->cascadeOnDelete();
            $table->foreignId('usuario_id')->constrained('users');
            $table->string('rol', 50)->comment('JEFE, RRHH');
            $table->enum('accion', ['APROBADO', 'RECHAZADO', 'OBSERVADO']);
            $table->text('comentario')->nullable();

            $table->string('ip_origen', 45);
            $table->text('user_agent');

            $table->timestamps();

            $table->index('papeleta_id', 'idx_flujo_papeleta');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('flujo_aprobaciones');
    }
};
