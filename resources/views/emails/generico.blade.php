{{--
    Correo del sistema: único formato para todos los avisos (pagos, comunicados,
    recordatorios, recuperación de contraseña...). Recibe $titulo y $mensaje.

    Reglas de un correo (por eso no se parece al resto del código):
      - Tablas y estilos EN LÍNEA: Outlook y Gmail ignoran gran parte del CSS moderno.
      - Sin variables CSS ni fuentes web: casi ningún programa de correo las carga.
        Títulos en Georgia (serif, la voz de la marca) y texto en la fuente del sistema.
      - Colores fijos de la identidad de la casa (ciprés, oro y crema).
      - $mensaje ya viene saneado (HtmlSeguro): solo etiquetas permitidas.
--}}
@php
    $base = rtrim(\Illuminate\Support\Facades\URL::to('/'), '/');
    $tituloCorreo = $titulo ?? 'Correo de Alameda';
@endphp
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light">
    <meta name="supported-color-schemes" content="light">
    <title>{{ $tituloCorreo }}</title>
    <style>
        /* Solo mejoras para quien las soporta; el diseño no depende de esto. */
        .contenido p { margin: 0 0 14px; }
        .contenido ul, .contenido ol { margin: 0 0 14px; padding-left: 22px; }
        .contenido li { margin-bottom: 6px; }
        .contenido a { color: #0b6b45; font-weight: bold; }
        @media only screen and (max-width: 620px) {
            .tarjeta { width: 100% !important; border-radius: 0 !important; }
            .relleno { padding-left: 20px !important; padding-right: 20px !important; }
            .titulo { font-size: 24px !important; }
        }
    </style>
</head>
<body style="margin:0; padding:0; background-color:#f7f3e8;">

    {{-- Texto de vista previa en la bandeja de entrada (no se ve dentro del correo). --}}
    <div style="display:none; max-height:0; overflow:hidden; opacity:0; font-size:1px; line-height:1px; color:#f7f3e8;">
        {{ $tituloCorreo }} · Condominio Alameda
    </div>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" bgcolor="#f7f3e8" style="background-color:#f7f3e8;">
        <tr>
            <td align="center" style="padding:24px 12px;">

                <table role="presentation" class="tarjeta" width="600" cellpadding="0" cellspacing="0" border="0" style="width:600px; max-width:600px; background-color:#fffdf6; border:1px solid #e4dabd; border-radius:18px; overflow:hidden;">

                    {{-- Encabezado: verde ciprés con el logo --}}
                    <tr>
                        <td align="center" bgcolor="#0b6b45" style="background-color:#0b6b45; padding:26px 24px 22px;">
                            <table role="presentation" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td align="center" bgcolor="#ffffff" style="background-color:#ffffff; border-radius:16px; padding:8px;">
                                        <img src="{{ $base }}/img/logo-header.png" alt="Condominio Alameda" width="72" height="72" style="display:block; width:72px; height:72px; border:0;">
                                    </td>
                                </tr>
                            </table>
                            <div style="font-family:Georgia, 'Times New Roman', serif; font-size:22px; letter-spacing:.5px; color:#ffffff; margin-top:12px;">Alameda</div>
                            <div style="font-family:Arial, Helvetica, sans-serif; font-size:11px; letter-spacing:4px; color:#e8d9a4; margin-top:2px;">CONDOMINIO</div>
                        </td>
                    </tr>
                    {{-- Filete dorado --}}
                    <tr><td height="4" bgcolor="#c0a45a" style="background-color:#c0a45a; font-size:0; line-height:0;">&nbsp;</td></tr>

                    {{-- Título y mensaje --}}
                    <tr>
                        <td class="relleno" style="padding:30px 36px 8px;">
                            <h1 class="titulo" style="margin:0 0 18px; font-family:Georgia, 'Times New Roman', serif; font-size:27px; line-height:1.2; font-weight:bold; color:#0a4a31; text-align:left;">{{ $tituloCorreo }}</h1>
                        </td>
                    </tr>
                    <tr>
                        <td class="relleno" style="padding:0 36px 8px;">
                            <div class="contenido" style="font-family:Arial, Helvetica, sans-serif; font-size:16px; line-height:1.65; color:#1d2a23; background-color:#f7f3e8; border-left:4px solid #c0a45a; border-radius:8px; padding:18px 20px;">
                                {!! \App\Support\HtmlSeguro::limpiar($mensaje) !!}
                            </div>
                        </td>
                    </tr>

                    {{-- Botón: llevar a la persona directo a la plataforma --}}
                    <tr>
                        <td class="relleno" align="center" style="padding:24px 36px 8px;">
                            <table role="presentation" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td align="center" bgcolor="#0b6b45" style="background-color:#0b6b45; border-radius:12px;">
                                        <a href="{{ $base }}/inicio" target="_blank" style="display:inline-block; padding:14px 30px; font-family:Arial, Helvetica, sans-serif; font-size:16px; font-weight:bold; color:#ffffff; text-decoration:none;">Entrar a la plataforma</a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- Pie --}}
                    <tr>
                        <td class="relleno" align="center" style="padding:26px 36px 28px; font-family:Arial, Helvetica, sans-serif; font-size:12.5px; line-height:1.6; color:#5d695f;">
                            Este correo fue enviado desde el sistema del Condominio Alameda.<br>
                            <a href="{{ $base }}/aviso-de-privacidad" style="color:#5d695f;">Aviso de Privacidad</a>
                            &nbsp;·&nbsp;
                            <a href="{{ $base }}/terminos-de-uso" style="color:#5d695f;">Términos de Uso</a>
                            <br>
                            &copy; {{ date('Y') }} Condominio Alameda. Todos los derechos reservados.
                        </td>
                    </tr>
                </table>

            </td>
        </tr>
    </table>
</body>
</html>
