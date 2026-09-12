<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('detallepagos', function (Blueprint $table) {
            $table->id();
            $table->integer('pago_id');
            $table->integer('user_id');
            $table->text('path_pago')->nullable();
            $table->double('cantidad_pago')->nullable();
            $table->string('estado')->default('pendiente');
            $table->string('deleted_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detallepagos');
    }
};
