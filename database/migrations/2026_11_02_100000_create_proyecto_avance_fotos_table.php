<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Varias fotos de evidencia por avance de proyecto.
 *
 * Aditiva: no toca proyecto_avances. La columna `foto` de siempre se conserva
 * (los avances ya publicados siguen mostrando su foto); las fotos nuevas viven
 * aquí, una fila por imagen.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('proyecto_avance_fotos')) {
            return;
        }

        Schema::create('proyecto_avance_fotos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('proyecto_avance_id');
            $table->string('ruta');
            $table->timestamps();

            $table->index('proyecto_avance_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('proyecto_avance_fotos');
    }
};
