<?php

namespace Tests\Unit;

use App\Support\HtmlSeguro;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Prueba pura de lógica: no arranca la aplicación ni toca la base de datos.
 */
class HtmlSeguroTest extends TestCase
{
    /** Vectores que JAMÁS deben sobrevivir al saneado. */
    public static function ataques(): array
    {
        return [
            'img onerror' => ['<img src=x onerror=alert(1)>'],
            'img con comillas' => ['"\'><img src=x onerror="alert(1)">'],
            'script' => ['<script>alert(1)</script>'],
            'script mayusculas' => ['<SCRIPT SRC=//evil.test/x.js></SCRIPT>'],
            'svg onload' => ['<svg onload=alert(1)>'],
            'svg con script' => ['<svg><script>alert(1)</script></svg>'],
            'iframe' => ['<iframe src="javascript:alert(1)"></iframe>'],
            'a javascript' => ['<a href="javascript:alert(1)">clic</a>'],
            'a javascript con tab' => ["<a href=\"java\tscript:alert(1)\">clic</a>"],
            'a javascript con entidad' => ['<a href="&#106;avascript:alert(1)">clic</a>'],
            'a data' => ['<a href="data:text/html,<script>alert(1)</script>">clic</a>'],
            'a vbscript' => ['<a href="vbscript:msgbox(1)">clic</a>'],
            'protocolo relativo' => ['<a href="//evil.test/x">clic</a>'],
            'onclick en strong' => ['<strong onclick="alert(1)">hola</strong>'],
            'onmouseover en a' => ['<a href="/x" onmouseover="alert(1)">hola</a>'],
            'style con expression' => ['<span style="background:url(javascript:alert(1))">x</span>'],
            'body onload' => ['<body onload=alert(1)>'],
            'math' => ['<math><mtext><script>alert(1)</script></mtext></math>'],
            'form' => ['<form action="//evil.test"><input name=x></form>'],
            'meta refresh' => ['<meta http-equiv="refresh" content="0;url=//evil.test">'],
            'base' => ['<base href="//evil.test/">'],
            'comentario condicional' => ['<!--[if IE]><script>alert(1)</script><![endif]-->'],
            'etiqueta rota' => ['<scr<script>ipt>alert(1)</scr</script>ipt>'],
            'class malicioso' => ['<i class="x" onclick="alert(1)"></i>'],
            'object' => ['<object data="javascript:alert(1)"></object>'],
        ];
    }

    #[DataProvider('ataques')]
    public function test_los_ataques_no_sobreviven(string $ataque): void
    {
        $limpio = strtolower(HtmlSeguro::limpiar($ataque));

        foreach (['<script', 'onerror', 'onload', 'onclick', 'onmouseover', 'javascript:', 'vbscript:',
            'data:text', '<iframe', '<svg', '<img', '<object', '<form', '<meta', '<base', 'expression('] as $peligro) {
            $this->assertStringNotContainsString($peligro, $limpio, "Sobrevivió «{$peligro}» en: ".$limpio);
        }

        // Ningún atributo de evento, en ninguna forma.
        $this->assertDoesNotMatchRegularExpression('/\son[a-z]+\s*=/', $limpio);
        $this->assertStringNotContainsString('href="//', $limpio);
    }

    public function test_conserva_el_formato_legitimo(): void
    {
        $entrada = '<strong>Concepto:</strong> Cuota de mantenimiento<br>'
            .'<b>Monto:</b> $650.00<br><i class="check circle icon"></i> Pagado';

        $this->assertSame($entrada, HtmlSeguro::limpiar($entrada));
    }

    public function test_conserva_acentos_y_enie(): void
    {
        $entrada = '<strong>Reparación</strong> de la bardá perimetral — Año 2026 ñandú';

        $this->assertSame($entrada, HtmlSeguro::limpiar($entrada));
    }

    public function test_texto_sin_etiquetas_pasa_intacto(): void
    {
        $this->assertSame('Pagaste $650 y quedó a tu favor', HtmlSeguro::limpiar('Pagaste $650 y quedó a tu favor'));
        $this->assertSame('', HtmlSeguro::limpiar(null));
        $this->assertSame('', HtmlSeguro::limpiar(''));
    }

    public function test_enlaces_seguros_se_conservan_y_se_endurecen(): void
    {
        $this->assertStringContainsString('href="https://alameda-condominio.com.mx/inicio"',
            HtmlSeguro::limpiar('<a href="https://alameda-condominio.com.mx/inicio">ir</a>'));
        $this->assertStringContainsString('href="/usuario/pago"', HtmlSeguro::limpiar('<a href="/usuario/pago">ir</a>'));

        $salida = HtmlSeguro::limpiar('<a href="https://x.test">ir</a>');
        $this->assertStringContainsString('rel="noopener noreferrer"', $salida);
        $this->assertStringContainsString('target="_blank"', $salida);
    }

    public function test_un_nombre_con_menor_que_no_se_pierde_como_texto(): void
    {
        // Si el nombre se escapó con e() al armar el mensaje, "<" ya viene como &lt;
        // y debe seguir viéndose como texto, no desaparecer.
        $mensaje = '<strong>'.e('Ana <3 Luis').'</strong>';

        $this->assertStringContainsString('Ana &lt;3 Luis', HtmlSeguro::limpiar($mensaje));
    }

    public function test_el_boton_de_correo_conserva_su_estilo(): void
    {
        $boton = '<a href="https://alameda-condominio.com.mx/x" style="display:inline-block;padding:12px 28px;background:#667eea;color:#fff;text-decoration:none;border-radius:8px;">Ver</a>';
        $limpio = HtmlSeguro::limpiar($boton);

        foreach (['display:inline-block', 'padding:12px 28px', 'background:#667eea', 'color:#fff', 'border-radius:8px'] as $regla) {
            $this->assertStringContainsString($regla, $limpio);
        }
    }

    public function test_conserva_degradados_pero_no_url(): void
    {
        $this->assertStringContainsString('linear-gradient(135deg, #667eea, #764ba2)',
            HtmlSeguro::limpiar('<a href="/x" style="background: linear-gradient(135deg, #667eea, #764ba2); color:#fff">v</a>'));

        $this->assertStringNotContainsString('url(', HtmlSeguro::limpiar('<span style="background:url(https://evil.test/a.png)">x</span>'));
        $this->assertStringNotContainsString('url(', HtmlSeguro::limpiar('<span style="background-image:url(\'//evil.test/a.png\')">x</span>'));
    }

    public function test_el_estilo_no_puede_sacar_un_elemento_de_su_sitio(): void
    {
        $limpio = HtmlSeguro::limpiar('<div style="position:fixed;top:0;left:0;width:100%;height:100%;z-index:99999;background:red">cubre</div>');

        $this->assertStringNotContainsString('position', $limpio);
        $this->assertStringNotContainsString('z-index', $limpio);
        $this->assertStringContainsString('background:red', $limpio);
    }

    public function test_estilos_con_expresiones_o_codigo_se_descartan(): void
    {
        foreach (['width:expression(alert(1))', 'color:red;behavior:url(x.htc)', 'background:javascript:alert(1)',
            'color:red\;x', 'margin:0;@import "x"', 'color:red/*x*/'] as $css) {
            $limpio = strtolower(HtmlSeguro::limpiar('<span style="'.$css.'">x</span>'));
            $this->assertStringNotContainsString('expression', $limpio, $css);
            $this->assertStringNotContainsString('behavior', $limpio, $css);
            $this->assertStringNotContainsString('javascript', $limpio, $css);
            $this->assertStringNotContainsString('@import', $limpio, $css);
        }
    }

    public function test_estilo_vacio_o_invalido_no_deja_atributo(): void
    {
        $this->assertSame('<span>x</span>', HtmlSeguro::limpiar('<span style="position:fixed">x</span>'));
    }
    public function test_texto_plano(): void
    {
        $this->assertSame('Hola Mundo', HtmlSeguro::texto('<b>Hola</b> <img src=x onerror=1>Mundo'));
        $this->assertSame('a & b', HtmlSeguro::texto('a &amp; b'));
    }
}
