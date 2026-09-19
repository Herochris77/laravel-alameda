<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

/**
 * Localiza el logo del condominio para incrustarlo en los PDF.
 *
 * dompdf lee la imagen del sistema de archivos: si se le pasa una URL, el
 * servidor tiene que pedirse la imagen a sí mismo, y eso es justo lo que
 * falla en hosting compartido detrás de un proxy o con HTTPS interno.
 *
 * La búsqueda recorre varias rutas porque en producción el document root
 * (public_html) está FUERA del proyecto, así que el archivo puede estar en
 * cualquiera de las dos. Si no aparece en ninguna, el PDF se genera sin logo
 * en vez de reventar: un reporte sin membrete sigue sirviendo.
 */
class LogoService
{
    /**
     * Archivos candidatos, en orden de preferencia.
     *
     * El JPG va primero por peso: 22 KB contra 192 KB del PNG. En un PDF que
     * se descarga desde el celular, esa diferencia sí se nota.
     */
    private const CANDIDATOS = [
        'img/logo.jpg',
        'img/logo-header.png',
    ];

    private static ?string $cache = null;

    private static bool $buscado = false;

    public function ruta(): ?string
    {
        // Se resuelve una sola vez por petición: un reporte con varias
        // páginas no tiene por qué tocar el disco en cada una.
        if (self::$buscado) {
            return self::$cache;
        }

        self::$buscado = true;

        foreach ($this->basesPosibles() as $base) {
            foreach (self::CANDIDATOS as $archivo) {
                $ruta = rtrim($base, '/\\').DIRECTORY_SEPARATOR.str_replace('/', DIRECTORY_SEPARATOR, $archivo);

                // Se descarta un PNG con transparencia si el servidor no tiene
                // GD: incrustarlo no dejaría el PDF sin logo, lo tumbaría
                // entero. Por eso el JPG va primero en la lista.
                if (is_file($ruta) && is_readable($ruta) && PdfService::puedeIncrustar($ruta)) {
                    return self::$cache = $ruta;
                }
            }
        }

        Log::warning('No se encontró el logo para los PDF. Se generan sin membrete.', [
            'buscado_en' => $this->basesPosibles(),
        ]);

        return self::$cache = null;
    }

    /**
     * Carpetas donde puede vivir la carpeta img.
     *
     * En local es public/ del propio proyecto. En cPanel el document root es
     * public_html, hermano de la carpeta del proyecto, y ahí es donde el
     * layout apunta cuando usa URL::to('/').
     */
    private function basesPosibles(): array
    {
        return array_values(array_unique(array_filter([
            public_path(),
            base_path('../public_html'),
            base_path('..').DIRECTORY_SEPARATOR.'public_html',
        ])));
    }

    /**
     * ¿Hay logo disponible? Sirve para decidir el diseño de la cabecera.
     */
    public function existe(): bool
    {
        return $this->ruta() !== null;
    }
}
