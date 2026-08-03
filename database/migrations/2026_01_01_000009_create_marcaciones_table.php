<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('marcaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('papeleta_id')->constrained('papeletas')->cascadeOnDelete();
            $table->enum('tipo', ['SALIDA', 'RETORNO']);

            $table->decimal('latitud', 10, 8);
            $table->decimal('longitud', 11, 8);
            $table->decimal('precision_gps', 6, 2)->nullable()->comment('Precisión del GPS en metros');
            $table->string('direccion', 255)->nullable()->comment('Reverse geocoding, opcional');
            $table->boolean('dentro_radio_permitido')->nullable()
                ->comment('Calculado contra sedes.radio_permitido');

            $table->string('ip_origen', 45);
            $table->text('user_agent');

            $table->timestamps();

            $table->unique(['papeleta_id', 'tipo'], 'uq_marcacion_papeleta_tipo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('marcaciones');
    }
};
