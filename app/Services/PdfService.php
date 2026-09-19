<?php

namespace App\Services;

use Dompdf\Dompdf;
use Dompdf\Options;

/**
 * Constructor único de documentos PDF.
 *
 * Existe por un detalle que costó encontrar: dompdf trae un `chroot` que por
 * omisión apunta SOLO a su propia carpeta dentro de vendor. Cualquier imagen
 * local fuera de ahí —el logo del condominio, la firma del tesorero— se
 * descarta en silencio: el PDF se genera igual, sin la imagen y sin error.
 *
 * Por eso todos los PDF del sistema se arman desde aquí. Si mañana se agrega
 * otro documento y se instancia Dompdf a mano, volverá a salir sin logo.
 */
class PdfService
{
    /**
     * Devuelve un dompdf listo para cargar HTML.
     *
     * @param  string  $tamano  LETTER, A4, etc.
     */
    public static function crear(string $tamano = 'LETTER', string $orientacion = 'portrait'): Dompdf
    {
        $options = new Options;

        $options->set('isRemoteEnabled', true);
        $options->set('isHtml5ParserEnabled', true);

        // Carpetas desde las que se permite leer imágenes del disco.
        $options->set('chroot', self::carpetasPermitidas());

        $dompdf = new Dompdf($options);
        $dompdf->setPaper($tamano, $orientacion);

        return $dompdf;
    }

    /**
     * ¿Este servidor puede incrustar esta imagen en un PDF?
     *
     * dompdf necesita la extensión GD para procesar PNG con canal alfa. Si no
     * está y se cuela una imagen así, NO falla la imagen: truena el documento
     * completo. Un recibo que no abre es peor que un recibo sin firma, así que
     * conviene detectarlo antes de guardar nada.
     */
    public static function puedeIncrustar(string $ruta): bool
    {
        if (! is_file($ruta)) {
            return false;
        }

        $info = @getimagesize($ruta);

        if ($info === false) {
            return false;
        }

        if ($info[2] !== IMAGETYPE_PNG) {
            return true;   // JPG y GIF no dependen de GD
        }

        // Sin GD dompdf rechaza CUALQUIER PNG, tenga transparencia o no:
        // addPngFromFile() lanza excepción antes siquiera de leer el archivo.
        return function_exists('imagecreatefrompng');
    }

    /**
     * Prueba real: intenta generar un PDF con esa imagen dentro.
     *
     * Es la comprobación que no se equivoca, porque ejecuta el mismo camino
     * que correrá después.
     *
     * OJO: la ruta tiene que estar DENTRO de carpetasPermitidas(). Si no,
     * dompdf descarta la imagen en silencio —no falla— y la prueba pasaría
     * sin haber probado nada. Por eso quien la usa guarda el archivo en su
     * ubicación definitiva antes de llamar aquí.
     */
    public static function seIncrustaSinError(string $ruta): bool
    {
        if (! self::estaEnCarpetaPermitida($ruta)) {
            return false;
        }

        try {
            $dompdf = self::crear('LETTER', 'portrait');
            $dompdf->loadHtml('<img src="'.htmlspecialchars($ruta, ENT_QUOTES).'" style="height:40px;">');
            $dompdf->render();
            $dompdf->output();

            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * ¿La ruta cae dentro de alguna carpeta permitida?
     */
    private static function estaEnCarpetaPermitida(string $ruta): bool
    {
        $real = realpath($ruta);

        if ($real === false) {
            return false;
        }

        foreach (self::carpetasPermitidas() as $permitida) {
            $base = realpath($permitida);

            if ($base !== false && str_starts_with($real, $base)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Rutas de las que los PDF pueden tomar imágenes.
     *
     * Se listan explícitamente en vez de abrir la raíz del proyecto: así una
     * plantilla no puede terminar incrustando un archivo de configuración por
     * un descuido en la ruta.
     */
    private static function carpetasPermitidas(): array
    {
        $rutas = [
            public_path(),                       // logo e imágenes del sitio
            storage_path('app/public'),          // firmas, comprobantes, fotos
            base_path('../public_html'),         // document root en cPanel
        ];

        return array_values(array_filter(array_unique($rutas), 'is_dir'));
    }
}
