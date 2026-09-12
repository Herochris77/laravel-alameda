<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asambleas', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->text('descripcion')->nullable();
            $table->date('fecha')->nullable();
            $table->time('hora')->nullable();
            $table->string('lugar')->nullable();
            $table->unsignedInteger('quorum_pct')->default(50);
            // Si es true, una casa solo puede votar si el administrador la
            // registró como presente (pase de lista). Evita que quien no
            // asiste vote de forma remota.
            $table->boolean('control_asistencia')->default(true);
            $table->string('estado')->default('convocada'); // convocada | en_curso | cerrada
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
        });

        Schema::create('asamblea_puntos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('asamblea_id');
            $table->unsignedInteger('orden')->default(0);
            $table->string('titulo');
            $table->text('descripcion')->nullable();
            $table->string('tipo')->default('si_no'); // si_no | opciones | ordenamiento
            $table->string('estado')->default('pendiente'); // pendiente | abierto | cerrado
            $table->timestamps();
            $table->index('asamblea_id');
        });

        Schema::create('asamblea_opciones', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('punto_id');
            $table->string('opcion');
            $table->unsignedInteger('orden')->default(0);
            $table->timestamps();
            $table->index('punto_id');
        });

        Schema::create('asamblea_poderes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('asamblea_id');
            $table->string('casa');                 // casa cuyo voto se delega
            $table->unsignedBigInteger('otorgante_id');   // dueño que delega
            $table->unsignedBigInteger('representante_id'); // quien votará
            $table->string('tipo');                 // inquilino | vecino
            $table->string('estado')->default('activo'); // activo | revocado
            $table->timestamps();
            $table->index(['asamblea_id', 'casa']);
        });

        Schema::create('asamblea_votos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('asamblea_id');
            $table->unsignedBigInteger('punto_id');
            $table->string('casa');                 // casa a la que pertenece el voto
            $table->string('valor')->nullable();    // si_no: a_favor | en_contra | abstencion
            $table->unsignedBigInteger('opcion_id')->nullable(); // opciones | ordenamiento
            $table->unsignedInteger('posicion')->nullable();     // ordenamiento
            $table->unsignedBigInteger('emitido_por'); // usuario que emitió
            $table->timestamps();
            $table->index(['punto_id', 'casa']);
        });

        Schema::create('asamblea_asistencias', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('asamblea_id');
            $table->string('casa');
            $table->unsignedBigInteger('user_id');       // quien está presente por esa casa
            $table->boolean('confirmada')->default(true); // presencia válida para votar
            $table->unsignedBigInteger('registrada_por')->nullable(); // admin del pase de lista
            $table->timestamps();
            $table->unique(['asamblea_id', 'casa']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asamblea_asistencias');
        Schema::dropIfExists('asamblea_votos');
        Schema::dropIfExists('asamblea_poderes');
        Schema::dropIfExists('asamblea_opciones');
        Schema::dropIfExists('asamblea_puntos');
        Schema::dropIfExists('asambleas');
    }
};
