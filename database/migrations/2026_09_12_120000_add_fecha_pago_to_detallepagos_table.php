<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Agrega la fecha real en que se realizó el pago, en recibos y en multas.
 *
 * Antes el sistema deducía esa fecha de `updated_at`, que en realidad es la
 * fecha del último cambio de la fila. Eso provocaba que un pago puntual se
 * marcara como "Tarde" si tesorería lo validaba después del vencimiento, y
 * que los reportes por mes atribuyeran el ingreso al mes equivocado.
 *
 * Los registros anteriores se quedan en NULL a propósito: ni `created_at`
 * (fecha en que se creó el cargo, idéntica para todos los vecinos del mismo
 * concepto) ni `updated_at` son la fecha real del pago, y rellenar con
 * cualquiera de ellas inventaría el dato. La interfaz los muestra como
 * "Sin registrar" y tesorería puede capturarlos a mano.
 *
 * La migración es ADITIVA e IDEMPOTENTE: no altera ni elimina datos existentes.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('detallepagos') && ! Schema::hasColumn('detallepagos', 'fecha_pago')) {
            Schema::table('detallepagos', function (Blueprint $table) {
                $table->date('fecha_pago')->nullable()->after('cantidad_pago');
            });
        }

        if (Schema::hasTable('sanciones') && ! Schema::hasColumn('sanciones', 'fecha_pago')) {
            Schema::table('sanciones', function (Blueprint $table) {
                $table->date('fecha_pago')->nullable()->after('pago_path');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('detallepagos') && Schema::hasColumn('detallepagos', 'fecha_pago')) {
            Schema::table('detallepagos', function (Blueprint $table) {
                $table->dropColumn('fecha_pago');
            });
        }

        if (Schema::hasTable('sanciones') && Schema::hasColumn('sanciones', 'fecha_pago')) {
            Schema::table('sanciones', function (Blueprint $table) {
                $table->dropColumn('fecha_pago');
            });
        }
    }
};
