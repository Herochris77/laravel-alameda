<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Exoneración del recargo por mora, recibo por recibo.
 *
 * Aditiva y sin riesgo para lo existente: todo recibo conserva el valor 0
 * ("no exonerado"), así que ninguna cifra histórica cambia.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('detallepagos', function (Blueprint $table) {
            if (! Schema::hasColumn('detallepagos', 'recargo_exonerado')) {
                $table->boolean('recargo_exonerado')->default(false);
            }
            if (! Schema::hasColumn('detallepagos', 'recargo_exonerado_motivo')) {
                $table->string('recargo_exonerado_motivo', 255)->nullable();
            }
            if (! Schema::hasColumn('detallepagos', 'recargo_exonerado_por')) {
                $table->unsignedBigInteger('recargo_exonerado_por')->nullable();
            }
            if (! Schema::hasColumn('detallepagos', 'recargo_exonerado_en')) {
                $table->timestamp('recargo_exonerado_en')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('detallepagos', function (Blueprint $table) {
            foreach (['recargo_exonerado', 'recargo_exonerado_motivo', 'recargo_exonerado_por', 'recargo_exonerado_en'] as $col) {
                if (Schema::hasColumn('detallepagos', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
