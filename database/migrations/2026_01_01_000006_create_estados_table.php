<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('estados', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 50)->unique()
                ->comment('SOLICITADO, APROBADO_JEFE, APROBADO_RRHH, EN_CURSO, FINALIZADO, RECHAZADO, OBSERVADO, VENCIDA');
            $table->string('nombre', 100);
            $table->string('color', 30)->nullable()->comment('Clase CSS o #HEX para badges');
            $table->integer('orden')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('estados');
    }
};
