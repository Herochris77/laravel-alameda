<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Saldo a favor de los vecinos.
 *
 * En el condominio es común pagar varias cuotas de golpe. Antes ese dinero
 * quedaba como un `cantidad_pago` mayor que la cuota, sin rastro de a qué
 * meses correspondía, y el recibo del mes siguiente seguía apareciendo
 * pendiente aunque ya estuviera cubierto.
 *
 * El saldo no se guarda como número en `users`: se calcula sumando estos
 * movimientos, para que siempre quede el rastro de cada peso.
 *
 * Tabla NUEVA: no altera ninguna existente.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('saldo_movimientos')) {
            return;
        }

        Schema::create('saldo_movimientos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('tipo', 20); // abono | aplicacion | ajuste
            $table->double('monto');
            $table->unsignedBigInteger('detallepago_id')->nullable();
            $table->string('descripcion')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index('user_id');
            $table->index('detallepago_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('saldo_movimientos');
    }
};
