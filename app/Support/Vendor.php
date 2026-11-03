<?php

namespace App\Support;

use Illuminate\Support\Facades\Log;

/**
 * Librerías de terceros servidas desde el propio sitio.
 *
 * Antes cada pantalla bajaba ~20 archivos de cinco servidores ajenos, y cada
 * visita de un vecino les entregaba su IP. Ahora salen de /vendor, con la misma
 * versión exacta, comprobadas contra el registro de npm.
 *
 * RESPALDO: si un archivo local no está —por ejemplo porque en el servidor aún
 * no se subió la carpeta vendor— se usa el CDN que se usaba antes, en vez de
 * dejar la pantalla sin estilos. El sitio nunca queda peor que antes.
 *
 * La existencia se comprueba contra la carpeta que el servidor web realmente
 * sirve (DOCUMENT_ROOT), no contra public_path(): en cPanel el document root es
 * public_html, que está fuera del proyecto, y comprobar la carpeta equivocada
 * daría "existe" para un archivo que el navegador no puede pedir.
 */
final class Vendor
{
    /**
     * clave => [ruta local dentro de /vendor, URL de respaldo].
     * Las versiones son las mismas que ya estaban en producción.
     */
    private const CATALOGO = [
        'jquery' => ['jquery/3.6.0/jquery.min.js', 'https://code.jquery.com/jquery-3.6.0.min.js'],

        'fomantic.css' => ['fomantic-ui/2.9.2/semantic.min.css', 'https://cdn.jsdelivr.net/npm/fomantic-ui@2.9.2/dist/semantic.min.css'],
        'fomantic.js' => ['fomantic-ui/2.9.2/semantic.min.js', 'https://cdn.jsdelivr.net/npm/fomantic-ui@2.9.2/dist/semantic.min.js'],

        'alertify.css' => ['alertifyjs/1.13.1/alertify.min.css', 'https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/alertify.min.css'],
        'alertify-tema.css' => ['alertifyjs/1.13.1/default.min.css', 'https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/themes/default.min.css'],
        'alertify.js' => ['alertifyjs/1.13.1/alertify.min.js', 'https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/alertify.min.js'],

        'datatables.js' => ['datatables/1.13.6/jquery.dataTables.min.js', 'https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js'],
        'datatables-se.js' => ['datatables/1.13.6/dataTables.semanticui.min.js', 'https://cdn.datatables.net/1.13.6/js/dataTables.semanticui.min.js'],
        'datatables-se.css' => ['datatables/1.13.6/dataTables.semanticui.min.css', 'https://cdn.datatables.net/1.13.6/css/dataTables.semanticui.min.css'],
        'datatables-responsive.js' => ['datatables/responsive-2.5.0/dataTables.responsive.min.js', 'https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js'],
        'datatables-responsive-se.js' => ['datatables/responsive-2.5.0/responsive.semanticui.min.js', 'https://cdn.datatables.net/responsive/2.5.0/js/responsive.semanticui.min.js'],
        'datatables-responsive-se.css' => ['datatables/responsive-2.5.0/responsive.semanticui.min.css', 'https://cdn.datatables.net/responsive/2.5.0/css/responsive.semanticui.min.css'],

        'lottie.js' => ['lottie/5.12.2/lottie_light.min.js', 'https://cdnjs.cloudflare.com/ajax/libs/lottie-web/5.12.2/lottie_light.min.js'],
        'sortable.js' => ['sortablejs/1.15.2/Sortable.min.js', 'https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js'],
        'chart.js' => ['chartjs/4.4.0/chart.umd.js', 'https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.js'],

        'inter.woff2' => ['fonts/inter/inter-latin-wght-normal.woff2', null],
        'fraunces.woff2' => ['fonts/fraunces/fraunces-latin-wght-normal.woff2', null],
        'fraunces-italic.woff2' => ['fonts/fraunces/fraunces-latin-wght-italic.woff2', null],
    ];

    /** @var array<string, bool> Claves que cayeron al CDN en esta petición. */
    private static array $enRespaldo = [];

    /** @var array<string, string|null> Caché por petición de la ruta local resuelta. */
    private static array $resuelto = [];

    /** URL del archivo (local si existe, CDN si no). Null si no hay ninguno. */
    public static function url(string $clave): ?string
    {
        [$local, $cdn] = self::CATALOGO[$clave] ?? [null, null];

        if ($local === null) {
            return null;
        }

        $version = self::versionLocal($local);

        if ($version !== null) {
            return asset('vendor/'.$local).'?v='.$version;
        }

        if ($cdn === null) {
            return null;
        }

        if (! isset(self::$enRespaldo[$clave])) {
            self::$enRespaldo[$clave] = true;
            Log::warning("Vendor: falta public/vendor/{$local}; se usa el CDN de respaldo. Sube la carpeta vendor al servidor.");
        }

        return $cdn;
    }

    public static function js(string $clave, string $atributos = ''): string
    {
        $url = self::url($clave);

        return $url ? '<script src="'.e($url).'"'.($atributos ? ' '.$atributos : '').'></script>' : '';
    }

    public static function css(string $clave): string
    {
        $url = self::url($clave);

        return $url ? '<link rel="stylesheet" href="'.e($url).'">' : '';
    }

    /**
     * Hosts externos que esta petición terminó usando. La política de seguridad
     * (CSP) solo abre la puerta a estos: si todo es local, la lista queda vacía
     * y la página no puede cargar nada de afuera.
     *
     * Se calcula resolviendo todo el catálogo, no solo lo que la vista pidió,
     * porque las cabeceras se arman después de pintar y deben ser deterministas.
     */
    public static function hostsDeRespaldo(): array
    {
        $hosts = [];

        foreach (self::CATALOGO as $clave => [$local, $cdn]) {
            if ($cdn === null || self::versionLocal($local) !== null) {
                continue;
            }
            $hosts[parse_url($cdn, PHP_URL_HOST)] = true;
        }

        return array_keys($hosts);
    }

    /**
     * Hoja de estilos del sistema (resources/css/alameda.css).
     *
     * Se enlaza la copia de public/css cuando es IDÉNTICA a la fuente (el
     * navegador la guarda en caché); si falta, o está desactualizada porque se
     * subió el código pero no la carpeta pública, se incrusta la fuente. Así el
     * sitio nunca queda sin estilos ni con estilos viejos.
     */
    public static function estilosSistema(): string
    {
        static $html = null;

        if ($html !== null) {
            return $html;
        }

        $fuente = resource_path('css/alameda.css');

        if (! is_file($fuente)) {
            return $html = '';
        }

        $externo = self::raiz().DIRECTORY_SEPARATOR.'css'.DIRECTORY_SEPARATOR.'alameda.css';

        if (is_file($externo) && filesize($externo) === filesize($fuente) && md5_file($externo) === md5_file($fuente)) {
            return $html = '<link rel="stylesheet" href="'.e(asset('css/alameda.css').'?v='.filemtime($externo)).'">';
        }

        return $html = '<style>'."\n".file_get_contents($fuente).'</style>';
    }

    /** Carpeta que el servidor web sirve como raíz. */
    private static function raiz(): string
    {
        $raiz = $_SERVER['DOCUMENT_ROOT'] ?? '';

        if ($raiz === '' || ! is_dir($raiz)) {
            $raiz = public_path();
        }

        return rtrim($raiz, '/\\');
    }

    /** Marca de versión (mtime) del archivo local, o null si no existe. */
    private static function versionLocal(string $local): ?string
    {
        if (! array_key_exists($local, self::$resuelto)) {
            $ruta = self::raiz().DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR.str_replace('/', DIRECTORY_SEPARATOR, $local);
            $mtime = is_file($ruta) ? @filemtime($ruta) : false;
            self::$resuelto[$local] = $mtime ? (string) $mtime : null;
        }

        return self::$resuelto[$local];
    }
}
