<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expediente_notas', function (Blueprint $table) {
            $table->id();
            $table->string('casa');
            $table->text('nota');
            $table->string('autor')->nullable();
            $table->timestamps();
            $table->index('casa');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expediente_notas');
    }
};
