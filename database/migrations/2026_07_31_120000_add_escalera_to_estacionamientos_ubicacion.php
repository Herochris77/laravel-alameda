<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Amplía el enum "ubicacion" de la tabla estacionamientos para permitir
     * la escalera comunitaria, además de los cajones de entrada y central.
     */
    public function up(): void
    {
        if (! Schema::hasTable('estacionamientos')) {
            return;
        }

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE estacionamientos MODIFY COLUMN ubicacion ENUM('entrada','central','escalera') NOT NULL DEFAULT 'entrada'");
        }
        // En otros motores (p. ej. sqlite) la columna no es enum estricto,
        // por lo que 'escalera' ya se acepta sin cambios.
    }

    public function down(): void
    {
        if (! Schema::hasTable('estacionamientos')) {
            return;
        }

        if (DB::getDriverName() === 'mysql') {
            // Regresamos las escaleras a un valor válido antes de revertir el enum.
            DB::table('estacionamientos')->where('ubicacion', 'escalera')->update(['ubicacion' => 'entrada']);
            DB::statement("ALTER TABLE estacionamientos MODIFY COLUMN ubicacion ENUM('entrada','central') NOT NULL DEFAULT 'entrada'");
        }
    }
};
