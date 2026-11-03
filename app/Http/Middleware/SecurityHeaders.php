<?php

namespace App\Http\Middleware;

use App\Support\Vendor;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Cabeceras de seguridad para todas las respuestas.
 *
 *  - CSP: el navegador solo carga scripts, estilos, fuentes e imágenes de este
 *    mismo sitio. Si alguien lograra inyectar <script src="https://malo.com">,
 *    el navegador se niega. (Los scripts en línea se siguen permitiendo porque
 *    las pantallas los usan; por eso no sustituye a escapar la salida.)
 *  - frame-ancestors: nadie puede incrustar el sistema en otra página
 *    (clickjacking).
 *  - base-uri y form-action: una inyección no puede cambiar a dónde van los
 *    formularios ni las rutas relativas.
 *  - HSTS solo bajo HTTPS y en producción.
 *
 * CSP_MODE en .env: enforce (por defecto) | report-only | off.
 * Si algo legítimo se bloqueara, report-only lo deja funcionando mientras se
 * corrige, sin tocar el código.
 */
class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $headers = $response->headers;

        $headers->set('X-Content-Type-Options', 'nosniff');
        $headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $headers->set('X-Frame-Options', 'SAMEORIGIN');
        $headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=(), payment=(), usb=()');
        $headers->set('Cross-Origin-Opener-Policy', 'same-origin');

        if ($request->isSecure() && app()->isProduction()) {
            $headers->set('Strict-Transport-Security', 'max-age=31536000');
        }

        $modo = config('seguridad.csp', 'enforce');

        if ($modo !== 'off' && ! $headers->has('Content-Security-Policy')) {
            $nombre = $modo === 'report-only'
                ? 'Content-Security-Policy-Report-Only'
                : 'Content-Security-Policy';

            $headers->set($nombre, $this->politica());
        }

        return $response;
    }

    private function politica(): string
    {
        // Solo si falta un archivo local y la pantalla cae al CDN de respaldo.
        $respaldo = implode(' ', array_map(fn ($h) => 'https://'.$h, Vendor::hostsDeRespaldo()));

        $directivas = [
            "default-src 'self'",
            "script-src 'self' 'unsafe-inline' {$respaldo}",
            "style-src 'self' 'unsafe-inline' {$respaldo}",
            "img-src 'self' data: blob:",
            "font-src 'self' data: {$respaldo}",
            "connect-src 'self'",
            "frame-src 'self'",
            "worker-src 'self'",
            "manifest-src 'self'",
            "media-src 'self'",
            "object-src 'none'",
            "base-uri 'self'",
            "form-action 'self'",
            "frame-ancestors 'self'",
        ];

        return implode('; ', array_map('trim', $directivas));
    }
}
