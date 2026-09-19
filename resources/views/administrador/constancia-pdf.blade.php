{{--
    Constancia de no adeudo.

    Documento formal, de una sola hoja, que se entrega cuando una vivienda se
    vende o se renta. El controlador solo llega hasta aquí si la vivienda está
    realmente al corriente.
--}}
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <style>
        @page { margin: 42px 48px; }

        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #111; }

        .encabezado { text-align: center; margin-bottom: 26px; }

        .condominio { font-size: 15px; font-weight: bold; letter-spacing: .05em; }

        .direccion { font-size: 9px; color: #444; line-height: 1.4; margin-top: 3px; }

        .titulo {
            font-size: 13px;
            font-weight: bold;
            text-align: center;
            text-transform: uppercase;
            letter-spacing: .08em;
            margin: 30px 0 26px;
        }

        .cuerpo { font-size: 11.5px; line-height: 1.85; text-align: justify; }

        .cuerpo strong { font-weight: bold; }

        .recuadro {
            margin: 22px 0;
            border: 1px solid #999;
            padding: 12px 14px;
        }

        .recuadro table { width: 100%; border-collapse: collapse; }
        .recuadro td { padding: 4px 6px; font-size: 10.5px; }
        .recuadro .etiqueta { color: #555; width: 38%; }
        .recuadro .valor { font-weight: bold; }

        .firmas { margin-top: 62px; width: 100%; border-collapse: collapse; }

        .firmas td {
            width: 50%;
            text-align: center;
            font-size: 10px;
            padding: 0 14px;
            vertical-align: bottom;
        }

        .linea {
            border-top: 1px solid #333;
            margin-bottom: 5px;
            padding-top: 4px;
        }

        .cargo { color: #555; font-size: 9px; }

        .pie {
            position: fixed;
            bottom: 12px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 7.5px;
            color: #777;
        }
    </style>
</head>
<body>

    <div class="encabezado">
        @include('pdf.logo', ['alto' => 58])
        <div class="condominio">CONDOMINIO ALAMEDA</div>
        <div class="direccion">
            Av. Los Arados No. 1, Fracc. Hacienda del Bosque<br>
            Los Ángeles, Qro. C.P. 76902
        </div>
    </div>

    <div class="titulo">Constancia de no adeudo</div>

    <div class="cuerpo">
        <p>A quien corresponda:</p>

        <p>
            Por medio del presente se hace constar que la vivienda identificada como
            <strong>Casa {{ $datos['usuario']->casa }}</strong> de este condominio,
            cuyo titular registrado es
            <strong>{{ $datos['usuario']->nombre }}</strong>,
            se encuentra <strong>al corriente</strong> en el pago de sus cuotas de
            mantenimiento, derramas y cualquier otra obligación económica con la
            administración, a la fecha de expedición de este documento.
        </p>
    </div>

    <div class="recuadro">
        <table>
            <tr>
                <td class="etiqueta">Total cargado a la vivienda</td>
                <td class="valor">$ {{ number_format($datos['totales']['cargado'] + $datos['totales']['multas'], 2) }}</td>
            </tr>
            <tr>
                <td class="etiqueta">Adeudo pendiente</td>
                <td class="valor">$ 0.00</td>
            </tr>
            @if($datos['saldo_favor'] > 0.009)
                <tr>
                    <td class="etiqueta">Saldo a favor de la vivienda</td>
                    <td class="valor">$ {{ number_format($datos['saldo_favor'], 2) }}</td>
                </tr>
            @endif
            <tr>
                <td class="etiqueta">Fecha de expedición</td>
                <td class="valor">{{ $datos['generado']->translatedFormat('j \d\e F \d\e Y') }}</td>
            </tr>
        </table>
    </div>

    <div class="cuerpo">
        <p>
            Se extiende la presente constancia a petición del interesado, para los
            fines legales y administrativos que le convengan. Su validez se limita
            a la situación de la vivienda a la fecha señalada; cualquier obligación
            generada con posterioridad no queda amparada por este documento.
        </p>
    </div>

    <table class="firmas">
        <tr>
            <td>
                <div class="linea">{{ $datos['firmas']['tesorero'] ?? '' }}</div>
                <div>Tesorería</div>
                <div class="cargo">Mesa Directiva</div>
            </td>
            <td>
                <div class="linea">{{ $datos['firmas']['presidente'] ?? '' }}</div>
                <div>Presidencia</div>
                <div class="cargo">Mesa Directiva</div>
            </td>
        </tr>
    </table>

    <div class="pie">
        Documento generado por la plataforma del Condominio Alameda el
        {{ $datos['generado']->translatedFormat('j \d\e F \d\e Y \a \l\a\s H:i') }}.
    </div>

</body>
</html>
