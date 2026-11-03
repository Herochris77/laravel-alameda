<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Support\Facades\Route;

/**
 * Zonas de navegación visibles para una persona.
 *
 * Lee config/navegacion.php y deja solo lo que ese usuario puede usar: un
 * administrador ve "Mesa directiva", un inquilino ve "Solicitar permisos", y
 * nadie ve un enlace a una ruta que no existe.
 */
final class Navegacion
{
    /**
     * @return array<int, array{clave:string,titulo:string,icono:string,activa:bool,items:array}>
     */
    public static function zonas(?User $usuario): array
    {
        if (! $usuario) {
            return [];
        }

        $esAdmin = in_array($usuario->rol, ['administrador', 'super-administrador'], true);
        $esInquilino = $usuario->tipo === 'inquilino' && (string) $usuario->estado === '1';

        $permitido = function (?string $solo) use ($esAdmin, $esInquilino): bool {
            return match ($solo) {
                'admin' => $esAdmin,
                'inquilino' => $esInquilino,
                default => true,
            };
        };

        $zonas = [];

        foreach (self::mapa('navegacion', 'zonas') as $zona) {
            if (! $permitido($zona['solo'] ?? null)) {
                continue;
            }

            $items = [];
            $activa = false;

            foreach ($zona['items'] as $item) {
                if (! $permitido($item['solo'] ?? null) || ! Route::has($item['ruta'])) {
                    continue;
                }

                $esActivo = request()->routeIs($item['activa'] ?? $item['ruta']);
                $activa = $activa || $esActivo;

                $items[] = $item + ['url' => route($item['ruta']), 'actual' => $esActivo];
            }

            if ($items === []) {
                continue;
            }

            $zonas[] = [
                'clave' => $zona['clave'],
                'titulo' => $zona['titulo'],
                'icono' => $zona['icono'],
                'activa' => $activa,
                'items' => $items,
            ];
        }

        return $zonas;
    }

    /** Título de la pestaña según la pantalla en la que se está. */
    public static function tituloActual(): string
    {
        if (request()->routeIs('inicio')) {
            return 'Inicio · Alameda';
        }

        foreach (self::mapa('navegacion', 'zonas') as $zona) {
            foreach ($zona['items'] as $item) {
                if (Route::has($item['ruta']) && request()->routeIs($item['activa'] ?? $item['ruta'])) {
                    return $item['titulo'].' · Alameda';
                }
            }
        }

        return 'Alameda Condominio';
    }

    /** Dos letras para el avatar: inicial del nombre y del primer apellido. */
    public static function iniciales(?string $nombre): string
    {
        $partes = preg_split('/\s+/u', trim((string) $nombre)) ?: [];
        $a = mb_substr($partes[0] ?? '', 0, 1);
        $b = mb_substr($partes[1] ?? '', 0, 1);

        return mb_strtoupper($a.$b) ?: '·';
    }

    /**
     * Lee un mapa de config/. Si el servidor tiene la configuración en caché de
     * antes de subir este código, el archivo nuevo todavía no está en ella: se
     * lee directo del archivo para que el menú nunca desaparezca.
     */
    public static function mapa(string $archivo, string $clave): array
    {
        $valor = config("{$archivo}.{$clave}");

        if ($valor === null) {
            $ruta = config_path("{$archivo}.php");
            $valor = is_file($ruta) ? (require $ruta)[$clave] ?? [] : [];
        }

        return $valor;
    }
}
