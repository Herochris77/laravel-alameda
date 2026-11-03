<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Asambleas flexibles (todo aditivo; lo existente conserva su comportamiento):
 *
 *  - asambleas.modalidad          plataforma (como hasta ahora) | presencial | mixta
 *  - asamblea_puntos.resolucion   acuerdo escrito de un punto informativo (sin votación)
 *  - asamblea_votos.origen        plataforma | mesa (voto registrado por la mesa en sala)
 *  - asamblea_asistencias.modo / nota   cómo se registró la presencia y una observación
 */
return new class extends Migration
{
    public function up(): void
    {
        $agregar = [
            'asambleas' => ['modalidad' => fn (Blueprint $t) => $t->string('modalidad', 20)->default('plataforma')],
            'asamblea_puntos' => ['resolucion' => fn (Blueprint $t) => $t->text('resolucion')->nullable()],
            'asamblea_votos' => ['origen' => fn (Blueprint $t) => $t->string('origen', 20)->default('plataforma')],
            'asamblea_asistencias' => [
                'modo' => fn (Blueprint $t) => $t->string('modo', 20)->default('mesa'),
                'nota' => fn (Blueprint $t) => $t->string('nota', 255)->nullable(),
            ],
        ];

        foreach ($agregar as $tabla => $columnas) {
            foreach ($columnas as $columna => $definir) {
                if (Schema::hasTable($tabla) && ! Schema::hasColumn($tabla, $columna)) {
                    Schema::table($tabla, function (Blueprint $t) use ($definir) {
                        $definir($t);
                    });
                }
            }
        }
    }

    public function down(): void
    {
        foreach ([
            'asambleas' => ['modalidad'],
            'asamblea_puntos' => ['resolucion'],
            'asamblea_votos' => ['origen'],
            'asamblea_asistencias' => ['modo', 'nota'],
        ] as $tabla => $columnas) {
            foreach ($columnas as $columna) {
                if (Schema::hasColumn($tabla, $columna)) {
                    Schema::table($tabla, fn (Blueprint $t) => $t->dropColumn($columna));
                }
            }
        }
    }
};