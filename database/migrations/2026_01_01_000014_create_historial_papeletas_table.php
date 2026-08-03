<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('historial_papeletas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('papeleta_id')->constrained('papeletas')->cascadeOnDelete();
            $table->foreignId('usuario_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('accion', 150);
            $table->string('estado_anterior', 100)->nullable();
            $table->string('estado_nuevo', 100)->nullable();
            $table->text('descripcion')->nullable();
            $table->string('ip', 45)->nullable();
            $table->timestamps();

            $table->index('papeleta_id', 'idx_historial_papeleta');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('historial_papeletas');
    }
};
