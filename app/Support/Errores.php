<?php

namespace App\Support;

use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Mensajes de error para el navegador.
 *
 * Devolver `$e->getMessage()` tal cual le muestra a cualquiera el texto de la
 * excepcion: cuando falla una consulta eso incluye nombres de tablas y
 * columnas, y a veces fragmentos del SQL. Esa informacion le sirve a quien
 * busca una forma de entrar y a nadie mas.
 *
 * El detalle completo va al log, donde si hace falta; al navegador llega un
 * mensaje util pero sin entranas. En local (APP_DEBUG=true) se conserva el
 * detalle para no estorbar al desarrollar.
 */
final class Errores
{
    public static function mensaje(Throwable $e, string $contexto = 'No se pudo completar la operación'): string
    {
        Log::error($contexto.': '.$e->getMessage(), ['archivo' => $e->getFile().':'.$e->getLine()]);

        if (config('app.debug')) {
            return $contexto.': '.$e->getMessage();
        }

        return $contexto.'. Inténtalo de nuevo; si el problema continúa, avisa al administrador del sistema.';
    }
}
