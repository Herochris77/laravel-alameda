<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Impide operar sin haber aceptado el aviso de privacidad.
 *
 * La ventana emergente es la barrera visible, pero vive en el navegador y se
 * puede quitar desde las herramientas de desarrollo. Esto es la barrera real:
 * sin aceptación no se ejecuta ninguna acción que cree o modifique datos.
 *
 * Se bloquea SOLO lo que escribe (POST, PUT, PATCH, DELETE). Navegar y
 * consultar sigue permitido a propósito: alguien que aún no acepta tiene que
 * poder leer el aviso, ver sus recibos y decidir con información. Cerrar
 * sesión también, que es su otra salida.
 */
class AvisoAceptadoMiddleware
{
    /**
     * Rutas que siguen abiertas aunque no haya aceptado.
     *
     * Sin estas el usuario queda atrapado: no podría aceptar, ni salirse, ni
     * cambiar su contraseña si entró por un enlace de recuperación.
     */
    private const EXCEPCIONES = [
        'aceptar-aviso',
        'logout',
        'cerrar-sesion',
        'aviso-de-privacidad',
        'terminos-de-uso',
        'notifications/mark-all-read',
    ];

    public function handle(Request $request, Closure $next)
    {
        $usuario = Auth::user();

        if (! $usuario || $usuario->haAceptadoAviso()) {
            return $next($request);
        }

        if ($request->isMethodSafe()) {
            return $next($request);
        }

        foreach (self::EXCEPCIONES as $ruta) {
            if ($request->is($ruta) || $request->is($ruta.'/*')) {
                return $next($request);
            }
        }

        $mensaje = 'Para usar la plataforma necesitas aceptar el Aviso de Privacidad. '
            .'Recarga la página y acepta en la ventana que aparece.';

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => false,
                'header' => '📄 Falta aceptar el aviso',
                'message' => $mensaje,
                'requiere_aviso' => true,
            ], 403);
        }

        return redirect()->back()->with('error', $mensaje);
    }
}
