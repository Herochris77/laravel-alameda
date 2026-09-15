<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Servicios y pagos recurrentes de la tesorería.
 *
 * Producción no tiene terminal, así que el esquema real se aplica desde la
 * ruta /migrar. Esta migración existe para que el entorno local se levante
 * igual desde cero, y por eso repite las mismas guardas de existencia.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('servicios_recurrentes')) {
            Schema::create('servicios_recurrentes', function (Blueprint $t) {
                $t->id();
                $t->string('nombre', 120);
                $t->string('proveedor', 120)->nullable();
                $t->string('referencia', 120)->nullable();
                $t->string('categoria', 30)->default('otro');
                $t->double('monto_estimado')->default(0);
                $t->string('periodicidad', 20)->default('mensual');
                $t->date('proximo_vencimiento')->nullable();
                $t->unsignedSmallInteger('dias_aviso')->default(1);
                $t->string('forma_pago', 20)->default('transferencia');
                $t->string('contacto', 150)->nullable();
                $t->text('notas')->nullable();
                $t->boolean('activo')->default(true);
                $t->date('ultimo_aviso')->nullable();
                $t->unsignedBigInteger('created_by')->nullable();
                $t->softDeletes();
                $t->timestamps();
                $t->index('proximo_vencimiento');
                $t->index('activo');
            });
        }

        if (! Schema::hasTable('servicio_pagos')) {
            Schema::create('servicio_pagos', function (Blueprint $t) {
                $t->id();
                $t->unsignedBigInteger('servicio_id');
                $t->string('periodo', 7);
                $t->date('fecha_pago');
                $t->double('monto_pagado');
                $t->string('folio', 100)->nullable();
                $t->text('notas')->nullable();
                $t->unsignedBigInteger('created_by')->nullable();
                $t->timestamps();
                $t->index('servicio_id');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('servicio_pagos');
        Schema::dropIfExists('servicios_recurrentes');
    }
};
