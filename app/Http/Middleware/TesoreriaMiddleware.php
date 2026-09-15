<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Protege las acciones que mueven dinero.
 *
 * Se aplica a crear recibos, validar pagos, eliminarlos y asignar usuarios a
 * un concepto. Consultar la cobranza y descargar el reporte siguen abiertos a
 * toda la mesa directiva: el objetivo es separar quién decide sobre el dinero,
 * no esconderle la información al resto.
 *
 * La regla vive en User::puedeGestionarPagos(), que además respeta el periodo
 * de transición: mientras no haya un tesorero nombrado, nadie pierde acceso.
 */
class TesoreriaMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if (! $user) {
            abort(403, 'Acceso no autorizado. Debes iniciar sesión.');
        }

        if ($user->puedeGestionarPagos()) {
            return $next($request);
        }

        $mensaje = 'Esta acción es exclusiva del Tesorero de la mesa directiva. '
            .'Puedes consultar la cobranza y descargar el reporte, pero el registro '
            .'y la validación de pagos los realiza quien tiene ese cargo.';

        if ($request->expectsJson()) {
            return response()->json([
                'header' => '🔒 Acción reservada al Tesorero',
                'success' => false,
                'message' => $mensaje,
            ], 403);
        }

        abort(403, $mensaje);
    }
}
