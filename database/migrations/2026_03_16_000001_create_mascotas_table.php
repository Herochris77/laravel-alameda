<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mascotas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('nombre');
            $table->string('tipo'); // perro, gato, otro
            $table->integer('edad'); // años
            $table->string('genero'); // macho, hembra
            $table->text('caracteristicas')->nullable();
            $table->boolean('vacunas')->default(false);
            $table->boolean('esterilizado')->default(false);
            $table->boolean('amistoso')->default(true);
            $table->string('foto')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mascotas');
    }
};
