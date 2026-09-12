<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RolMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // Verifica que el usuario esté autenticado
        if (! Auth::check()) {
            abort(403, 'Acceso no autorizado. Debes iniciar sesión.');
        }

        $userRol = Auth::user()->rol;

        switch (Auth::user()->estado) {
            case 2:
                abort(504);
                break;
            case 0:
                abort(506);
                break;
            case 3:
                abort(507);
                break;
        }

        // Si el rol del usuario está dentro de los roles permitidos
        if (in_array($userRol, $roles)) {
            return $next($request);
        }

        // Si no tiene permiso
        abort(503);
    }
}
