<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehiculos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('marca', 50);
            $table->string('modelo', 50);
            $table->string('anio', 10)->nullable();
            $table->string('color', 30)->nullable();
            $table->string('placas', 15)->unique();
            $table->string('tipo', 30)->default('automovil');
            $table->string('foto')->nullable();
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehiculos');
    }
};
