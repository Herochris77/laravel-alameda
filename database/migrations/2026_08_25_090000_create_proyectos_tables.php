<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('proyectos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->double('costo')->nullable(); // opcional
            $table->string('estado')->default('por_iniciar'); // por_iniciar | en_proceso | pausado | terminado
            $table->unsignedInteger('avance')->default(0);     // 0..100
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });

        Schema::create('proyecto_avances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('proyecto_id');
            $table->unsignedInteger('porcentaje')->default(0);
            $table->text('comentario')->nullable();
            $table->string('foto')->nullable(); // evidencia opcional
            $table->string('autor')->nullable();
            $table->timestamps();
            $table->index('proyecto_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('proyecto_avances');
        Schema::dropIfExists('proyectos');
    }
};
