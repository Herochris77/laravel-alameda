<?php

namespace App\Support;

use DOMDocument;
use DOMElement;
use DOMNode;

/**
 * Saneador de HTML con lista blanca.
 *
 * Las notificaciones y los correos del sistema llevan HTML a propósito
 * (<strong>Concepto:</strong>, saltos de línea, íconos). Ese HTML se arma
 * concatenando texto que escribió una persona —el nombre de un vecino, el
 * concepto de un recibo— y se muestra sin escapar. Sin una barrera, quien
 * escriba `<img src=x onerror=...>` en su nombre ejecuta código en el
 * navegador de quien lea la notificación, que puede ser un administrador.
 *
 * Esta clase es esa barrera. Es la ÚLTIMA defensa, no la primera: lo correcto
 * es escapar con e() al armar el mensaje, para que un nombre con "<" se vea
 * tal cual. Aquí se cubre lo que se escapó mal, y también lo que ya estaba
 * guardado antes de existir esta clase.
 *
 * Qué conserva: formato básico y enlaces seguros.
 * Qué elimina: todo lo demás, y de lo que conserva, todos los atributos
 * salvo `href` seguro y `class` en íconos.
 */
final class HtmlSeguro
{
    /** Etiquetas que se conservan. */
    private const PERMITIDAS = [
        'a', 'b', 'strong', 'i', 'em', 'u', 'br', 'p', 'ul', 'ol', 'li', 'span', 'small', 'div',
    ];

    /** Etiquetas que se eliminan CON su contenido (nunca se "desenvuelven"). */
    private const PELIGROSAS = [
        'script', 'style', 'iframe', 'frame', 'frameset', 'object', 'embed', 'applet', 'svg', 'math',
        'template', 'noscript', 'form', 'input', 'button', 'textarea', 'select', 'link', 'meta',
        'base', 'title', 'head', 'audio', 'video', 'source', 'track', 'canvas', 'xml',
    ];

    /**
     * Propiedades CSS que se conservan en `style`. Solo apariencia: nada que
     * saque un elemento de su sitio (position, z-index, float), cargue
     * recursos (url(), @import) ni ejecute (expression, behavior).
     */
    private const CSS_PERMITIDO = [
        'color', 'background', 'background-color', 'background-image',
        'border', 'border-top', 'border-right', 'border-bottom', 'border-left',
        'border-color', 'border-width', 'border-style', 'border-radius',
        'padding', 'padding-top', 'padding-right', 'padding-bottom', 'padding-left',
        'margin', 'margin-top', 'margin-right', 'margin-bottom', 'margin-left',
        'font-size', 'font-weight', 'font-style', 'line-height', 'letter-spacing',
        'text-align', 'text-decoration', 'text-transform', 'white-space', 'word-break',
        'display', 'width', 'max-width', 'min-width', 'height', 'vertical-align', 'opacity',
    ];

    /** Esquemas permitidos en enlaces. */
    private const ESQUEMAS = ['http', 'https', 'mailto', 'tel'];

    public static function limpiar(?string $html): string
    {
        $html = (string) $html;

        // Sin "<" no puede formarse ninguna etiqueta: es texto. Se devuelve tal
        // cual, que es lo más barato y lo más frecuente.
        if ($html === '' || ! str_contains($html, '<')) {
            return $html;
        }

        $anterior = libxml_use_internal_errors(true);

        try {
            $dom = new DOMDocument('1.0', 'UTF-8');
            // El prefijo XML fuerza UTF-8; sin él libxml lee Latin-1 y rompe los acentos.
            $cargado = $dom->loadHTML(
                '<?xml encoding="UTF-8"><div id="__raiz__">'.$html.'</div>',
                LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD | LIBXML_NONET
            );

            $raiz = $cargado ? $dom->getElementById('__raiz__') : null;

            if (! $raiz) {
                // Si no se pudo interpretar, lo seguro es tratarlo todo como texto.
                return e(strip_tags($html));
            }

            self::depurar($raiz);

            $salida = '';
            foreach (iterator_to_array($raiz->childNodes) as $hijo) {
                $salida .= $dom->saveHTML($hijo);
            }

            return $salida;
        } finally {
            libxml_clear_errors();
            libxml_use_internal_errors($anterior);
        }
    }

    /**
     * Texto plano: sin etiquetas y sin entidades pendientes. Para títulos y
     * lugares donde nunca debe haber formato.
     */
    public static function texto(?string $html): string
    {
        return trim(html_entity_decode(strip_tags((string) $html), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
    }

    private static function depurar(DOMNode $nodo): void
    {
        // Se itera sobre una copia: la lista cambia mientras se modifica el árbol.
        foreach (iterator_to_array($nodo->childNodes) as $hijo) {
            if (! $hijo instanceof DOMElement) {
                // Comentarios y demás nodos que no son texto se quitan.
                if ($hijo->nodeType !== XML_TEXT_NODE) {
                    $nodo->removeChild($hijo);
                }

                continue;
            }

            $etiqueta = strtolower($hijo->nodeName);

            if (in_array($etiqueta, self::PELIGROSAS, true)) {
                $nodo->removeChild($hijo);

                continue;
            }

            // Primero se limpia el interior, luego se decide qué hacer con el nodo.
            self::depurar($hijo);

            if (! in_array($etiqueta, self::PERMITIDAS, true)) {
                // Etiqueta desconocida e inofensiva en sí (img, font, etc.): se
                // desenvuelve y queda su contenido de texto.
                while ($hijo->firstChild) {
                    $nodo->insertBefore($hijo->firstChild, $hijo);
                }
                $nodo->removeChild($hijo);

                continue;
            }

            self::depurarAtributos($hijo, $etiqueta);
        }
    }

    private static function depurarAtributos(DOMElement $el, string $etiqueta): void
    {
        foreach (iterator_to_array($el->attributes) as $attr) {
            $nombre = strtolower($attr->nodeName);
            $valor = trim($attr->nodeValue ?? '');

            $conservar = false;

            if ($nombre === 'href' && $etiqueta === 'a') {
                $conservar = self::enlaceSeguro($valor);
            } elseif ($nombre === 'class' && in_array($etiqueta, ['i', 'span', 'div', 'p'], true)) {
                // Solo clases "de verdad": letras, números, guiones y espacios.
                $conservar = (bool) preg_match('/^[A-Za-z0-9 _\-]+$/', $valor);
            } elseif ($nombre === 'style') {
                $css = self::limpiarEstilo($valor);
                if ($css !== '') {
                    $el->setAttribute('style', $css);
                    $conservar = true;
                }
            }

            if (! $conservar) {
                $el->removeAttribute($attr->nodeName);
            }
        }

        if ($etiqueta === 'a' && $el->hasAttribute('href')) {
            // Evita que la página abierta controle a la que la abrió.
            $el->setAttribute('rel', 'noopener noreferrer');
            $el->setAttribute('target', '_blank');
        }
    }

    /**
     * Deja pasar solo declaraciones CSS de apariencia con valores inofensivos.
     */
    public static function limpiarEstilo(string $css): string
    {
        $css = html_entity_decode($css, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        // Un valor con estos fragmentos puede cargar recursos o ejecutar: se descarta entero.
        $prohibido = '/url\s*\(|expression|javascript|vbscript|behavior|binding|@import|\\|<|>|\/\*|\bdata:/i';

        $salida = [];

        foreach (self::declaraciones($css) as [$propiedad, $valor]) {
            $propiedad = strtolower(trim($propiedad));
            $valor = trim(preg_replace('/\s*!important\s*$/i', '', $valor));

            if ($valor === '' || strlen($valor) > 200 || ! in_array($propiedad, self::CSS_PERMITIDO, true)) {
                continue;
            }
            if (preg_match($prohibido, $valor)) {
                continue;
            }
            // background-image y background solo aceptan degradados; nunca url().
            if (in_array($propiedad, ['background', 'background-image'], true)
                && str_contains(strtolower($valor), '(')
                && ! preg_match('/^(repeating-)?(linear|radial)-gradient\(/i', $valor)
            ) {
                continue;
            }
            if ($propiedad === 'display' && ! preg_match('/^(block|inline|inline-block|none|flex|inline-flex)$/i', $valor)) {
                continue;
            }

            $salida[] = $propiedad.':'.$valor;
        }

        return implode(';', $salida);
    }

    /**
     * Separa "a:b;c:d" respetando los parentesis (un degradado lleva comas y ';' no, pero rgb() sí lleva comas).
     *
     * @return array<int, array{0:string,1:string}>
     */
    private static function declaraciones(string $css): array
    {
        $partes = [];
        $actual = '';
        $prof = 0;

        foreach (str_split($css) as $c) {
            if ($c === '(') {
                $prof++;
            } elseif ($c === ')') {
                $prof = max(0, $prof - 1);
            }

            if ($c === ';' && $prof === 0) {
                $partes[] = $actual;
                $actual = '';

                continue;
            }
            $actual .= $c;
        }
        $partes[] = $actual;

        $out = [];
        foreach ($partes as $p) {
            $pos = strpos($p, ':');
            if ($pos !== false) {
                $out[] = [substr($p, 0, $pos), substr($p, $pos + 1)];
            }
        }

        return $out;
    }

    private static function enlaceSeguro(string $url): bool
    {
        if ($url === '') {
            return false;
        }

        // Los navegadores ignoran espacios, tabuladores y saltos DENTRO del
        // esquema ("java\tscript:"), así que se quitan antes de juzgar.
        $limpia = preg_replace('/[\x00-\x20\x7F]+/', '', html_entity_decode($url, ENT_QUOTES | ENT_HTML5, 'UTF-8'));

        // Rutas relativas del propio sitio y anclas.
        if (str_starts_with($limpia, '/') && ! str_starts_with($limpia, '//')) {
            return true;
        }
        if (str_starts_with($limpia, '#')) {
            return true;
        }

        $esquema = parse_url($limpia, PHP_URL_SCHEME);

        return is_string($esquema) && in_array(strtolower($esquema), self::ESQUEMAS, true);
    }
}
