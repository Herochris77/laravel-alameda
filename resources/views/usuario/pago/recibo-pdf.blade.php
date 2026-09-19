<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">

    <title>
        Recibo de pago #{{ $pago->id }}
    </title>

    <style>

        @page {
            margin: 28px 32px;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;

            padding: 0;

            font-family:
                DejaVu Sans,
                Arial,
                sans-serif;

            font-size: 11px;

            line-height: 1.45;

            color: #334155;

            background: #ffffff;
        }


        /* =====================================================
           CONTENEDOR
        ===================================================== */

        .receipt {
            width: 100%;
        }


        /* =====================================================
           HEADER
        ===================================================== */

        .header-table {
            width: 100%;

            border-collapse: collapse;

            margin-bottom: 18px;
        }

        .header-table td {
            vertical-align: middle;
        }

        .brand-cell {
            width: 68%;
        }

        .folio-cell {
            width: 32%;

            text-align: right;
        }

        .brand-table {
            border-collapse: collapse;
        }

        .brand-table td {
            vertical-align: middle;
        }

        /* Misma caja que el ícono al que sustituye, para no mover el resto
           de la cabecera. */
        .brand-logo {
            height: 48px;
            width: auto;
            vertical-align: middle;
        }

        .brand-icon {
            width: 48px;
            height: 48px;

            text-align: center;

            vertical-align: middle;

            border-radius: 12px;

            background: #2563eb;

            color: #ffffff;

            font-size: 24px;

            font-weight: bold;
        }

        .brand-copy {
            padding-left: 12px;
        }

        .brand-title {
            margin: 0;

            color: #0f172a;

            font-size: 19px;

            font-weight: bold;

            line-height: 1.1;
        }

        .brand-subtitle {
            margin-top: 4px;

            color: #64748b;

            font-size: 9px;
        }

        .folio-label {
            color: #94a3b8;

            font-size: 8px;

            font-weight: bold;

            text-transform: uppercase;

            letter-spacing: .8px;
        }

        .folio-number {
            margin-top: 3px;

            color: #0f172a;

            font-size: 15px;

            font-weight: bold;
        }

        .folio-date {
            margin-top: 3px;

            color: #64748b;

            font-size: 8.5px;
        }


        /* =====================================================
           LINEA SUPERIOR
        ===================================================== */

        .top-line {
            width: 100%;

            height: 4px;

            margin-bottom: 20px;

            background: #2563eb;
        }


        /* =====================================================
           STATUS
        ===================================================== */

        .status-wrapper {
            width: 100%;

            margin-bottom: 17px;

            text-align: center;
        }

        .status-badge {
            display: inline-block;

            padding: 7px 15px;

            border: 1px solid #a7f3d0;

            border-radius: 14px;

            background: #ecfdf5;

            color: #047857;

            font-size: 10px;

            font-weight: bold;
        }


        /* =====================================================
           MONTO
        ===================================================== */

        .amount-box {
            width: 100%;

            margin-bottom: 19px;

            padding: 17px 20px;

            border: 1px solid #dbeafe;

            border-radius: 12px;

            background: #eff6ff;

            text-align: center;
        }

        .amount-label {
            margin-bottom: 5px;

            color: #64748b;

            font-size: 9px;

            font-weight: bold;

            text-transform: uppercase;

            letter-spacing: 1px;
        }

        .amount-value {
            color: #1d4ed8;

            font-size: 31px;

            font-weight: bold;

            line-height: 1.1;
        }

        .amount-currency {
            margin-top: 5px;

            color: #64748b;

            font-size: 9px;
        }


        /* =====================================================
           TITULOS DE SECCION
        ===================================================== */

        .section-title {
            margin:
                0 0 8px;

            color: #0f172a;

            font-size: 10px;

            font-weight: bold;

            text-transform: uppercase;

            letter-spacing: .6px;
        }


        /* =====================================================
           DATOS
        ===================================================== */

        .data-card {
            width: 100%;

            margin-bottom: 16px;

            border:
                1px solid #e2e8f0;

            border-radius: 10px;

            border-collapse: separate;

            border-spacing: 0;

            overflow: hidden;
        }

        .data-card td {
            padding:
                10px 12px;

            vertical-align: top;

            border-bottom:
                1px solid #f1f5f9;
        }

        .data-card tr:last-child td {
            border-bottom: none;
        }

        .data-card .label {
            width: 30%;

            color: #64748b;

            font-size: 8.5px;

            font-weight: bold;

            text-transform: uppercase;
        }

        .data-card .value {
            width: 70%;

            color: #0f172a;

            font-size: 10.5px;

            font-weight: bold;
        }


        /* =====================================================
           DOS COLUMNAS
        ===================================================== */

        .two-columns {
            width: 100%;

            margin-bottom: 16px;

            border-collapse: collapse;
        }

        .two-columns td {
            width: 50%;

            vertical-align: top;
        }

        .two-columns .left {
            padding-right: 5px;
        }

        .two-columns .right {
            padding-left: 5px;
        }

        .mini-card {
            width: 100%;

            min-height: 65px;

            padding: 11px 12px;

            border:
                1px solid #e2e8f0;

            border-radius: 10px;

            background: #f8fafc;
        }

        .mini-label {
            margin-bottom: 5px;

            color: #94a3b8;

            font-size: 8px;

            font-weight: bold;

            text-transform: uppercase;

            letter-spacing: .5px;
        }

        .mini-value {
            color: #0f172a;

            font-size: 10px;

            font-weight: bold;

            line-height: 1.35;
        }


        /* =====================================================
           CONCEPTO
        ===================================================== */

        .concept-box {
            margin-bottom: 17px;

            padding: 13px 14px;

            border-left:
                4px solid #2563eb;

            background: #f8fafc;
        }

        .concept-label {
            margin-bottom: 4px;

            color: #64748b;

            font-size: 8px;

            font-weight: bold;

            text-transform: uppercase;
        }

        .concept-value {
            color: #0f172a;

            font-size: 12px;

            font-weight: bold;
        }


        /* =====================================================
           VALIDACION
        ===================================================== */

        .validation-box {
            margin-top: 4px;

            padding: 11px 13px;

            border:
                1px solid #d1fae5;

            border-radius: 9px;

            background: #f0fdf4;
        }

        .validation-table {
            width: 100%;

            border-collapse: collapse;
        }

        .validation-icon-cell {
            width: 34px;

            vertical-align: middle;
        }

        .validation-icon {
            width: 26px;
            height: 26px;

            border-radius: 13px;

            background: #10b981;

            border-collapse: collapse;
        }

        .validation-icon td {
            width: 26px;
            height: 26px;

            padding: 0;
            border: 0;

            text-align: center;
            vertical-align: middle;

            color: #ffffff;

            font-size: 14px;
            line-height: 1;
            font-weight: bold;
        }

        .validation-content {
            vertical-align: middle;
        }

        .validation-title {
            color: #047857;

            font-size: 9.5px;

            font-weight: bold;
        }

        .validation-text {
            margin-top: 2px;

            color: #64748b;

            font-size: 8px;

            line-height: 1.4;
        }


        /* =====================================================
           FOOTER
        ===================================================== */

        /* =====================================================
           FORMA DE PAGO Y FIRMA
           ===================================================== */

        .bloque-firma {
            margin-top: 26px;
        }

        .tabla-firma {
            width: 100%;
            border-collapse: collapse;
        }

        .firma-datos {
            width: 55%;
            vertical-align: bottom;
            padding-right: 18px;
        }

        .firma-dato {
            margin-bottom: 9px;
        }

        .firma-dato span {
            display: block;
            font-size: 9px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .firma-dato strong {
            font-size: 12px;
            color: #0f172a;
        }

        .firma-efectivo {
            margin-top: 10px;
            padding: 8px 10px;
            background: #fffbeb;
            border: 1px solid #fcd34d;
            border-radius: 6px;
            font-size: 9.5px;
            color: #92400e;
            line-height: 1.45;
        }

        .firma-espacio {
            width: 45%;
            text-align: center;
            vertical-align: bottom;
        }

        /* Altura fija tanto con firma como sin ella, para que el recibo
           impreso y el descargado midan lo mismo. */
        .firma-imagen {
            max-height: 62px;
            max-width: 210px;
            margin-bottom: 2px;
        }

        .firma-hueco {
            height: 62px;
        }

        .firma-linea {
            border-top: 1px solid #334155;
            margin: 0 auto;
        }

        .firma-nombre {
            margin-top: 5px;
            font-size: 11px;
            font-weight: bold;
            color: #0f172a;
        }

        .firma-cargo {
            font-size: 9px;
            color: #64748b;
        }

        .footer {
            margin-top: 25px;

            padding-top: 11px;

            border-top:
                1px solid #e2e8f0;

            text-align: center;

            color: #94a3b8;

            font-size: 7.5px;

            line-height: 1.5;
        }

        .footer strong {
            color: #64748b;
        }


        /* =====================================================
           ID INTERNO
        ===================================================== */

        .internal-id {
            margin-top: 9px;

            color: #cbd5e1;

            font-size: 7px;

            text-align: center;
        }

    </style>
</head>

<body>

    <div class="receipt">


        {{-- =====================================================
             ENCABEZADO
        ====================================================== --}}

        <table class="header-table">

            <tr>

                <td class="brand-cell">

                    <table class="brand-table">

                        <tr>

                            <td>

                                {{--
                                    Si hay logo, ocupa el lugar del ícono: es
                                    la identidad del condominio, no un adorno
                                    genérico. Sin logo se conserva el símbolo
                                    de siempre para que no quede un hueco.
                                --}}
                                @php($logoRecibo = app(\App\Services\LogoService::class)->ruta())

                                @if($logoRecibo)
                                    <img src="{{ $logoRecibo }}" class="brand-logo" alt="">
                                @else
                                    <div class="brand-icon">
                                        $
                                    </div>
                                @endif

                            </td>

                            <td class="brand-copy">

                                <div class="brand-title">
                                    Recibo de pago
                                </div>

                                <div class="brand-subtitle">
                                    Comprobante emitido por la administración del condominio
                                </div>

                            </td>

                        </tr>

                    </table>

                </td>


                <td class="folio-cell">

                    <div class="folio-label">
                        Folio
                    </div>

                    <div class="folio-number">
                        #{{ str_pad($pago->id, 6, '0', STR_PAD_LEFT) }}
                    </div>

                    <div class="folio-date">
                        Emitido: {{ $fechaActual }}
                    </div>

                </td>

            </tr>

        </table>


        <div class="top-line"></div>


        {{-- =====================================================
             ESTADO
        ====================================================== --}}

        <div class="status-wrapper">

            <span class="status-badge">
                ✓ PAGO APROBADO
            </span>

        </div>


        {{-- =====================================================
             MONTO
        ====================================================== --}}

        <div class="amount-box">

            <div class="amount-label">
                Total pagado
            </div>

            <div class="amount-value">
                ${{ $cantidad }}
            </div>

            <div class="amount-currency">
                Pesos mexicanos (MXN)
            </div>

        </div>


        {{-- =====================================================
             RESIDENTE
        ====================================================== --}}

        <div class="section-title">
            Datos del residente
        </div>


        <table class="data-card">

            <tr>

                <td class="label">
                    Nombre
                </td>

                <td class="value">
                    {{ $nombre }}
                </td>

            </tr>


            <tr>

                <td class="label">
                    Casa
                </td>

                <td class="value">
                    {{ $casa }}
                </td>

            </tr>


            <tr>

                <td class="label">
                    Tipo de residente
                </td>

                <td class="value">
                    {{ ucfirst($tipo) }}
                </td>

            </tr>

        </table>


        {{-- =====================================================
             CONCEPTO
        ====================================================== --}}

        <div class="section-title">
            Detalle del pago
        </div>


        <div class="concept-box">

            <div class="concept-label">
                Concepto
            </div>

            <div class="concept-value">
                {{ $concepto }}
            </div>

        </div>


        {{-- =====================================================
             FECHAS
        ====================================================== --}}

        <table class="two-columns">

            <tr>

                <td class="left">

                    <div class="mini-card">

                        <div class="mini-label">
                            Fecha de vencimiento
                        </div>

                        <div class="mini-value">
                            {{ $vencimiento }}
                        </div>

                    </div>

                </td>


                <td class="right">

                    <div class="mini-card">

                        <div class="mini-label">
                            @if($fechaPago === 'No registrada')
                                Fecha de validación
                            @else
                                Fecha de pago
                            @endif
                        </div>

                        <div class="mini-value">
                            {{ $fechaPago === 'No registrada' ? $fechaValidacion : $fechaPago }}
                        </div>

                    </div>

                </td>

            </tr>

        </table>


        {{-- =====================================================
             VALIDACION
        ====================================================== --}}

        <div class="validation-box">

            <table class="validation-table">

                <tr>

                    <td class="validation-icon-cell">

                        {{--
                            El círculo va como tabla de una celda, no como div.
                            dompdf no centra de forma fiable con line-height, y
                            la paloma quedaba escurrida fuera del círculo.
                            El vertical-align de una celda sí lo respeta.
                        --}}
                        <table class="validation-icon">
                            <tr>
                                <td>✓</td>
                            </tr>
                        </table>

                    </td>

                    <td class="validation-content">

                        <div class="validation-title">
                            Pago registrado correctamente
                        </div>

                        <div class="validation-text">
                            Este recibo corresponde a un pago
                            que fue revisado y marcado como pagado
                            en el sistema de administración.
                        </div>

                    </td>

                </tr>

            </table>

        </div>


        {{-- =====================================================
             FORMA DE PAGO Y FIRMA

             El efectivo se destaca porque no tiene comprobante bancario que
             lo respalde: este papel firmado ES el comprobante del vecino.
        ====================================================== --}}

        <div class="bloque-firma">

            <table class="tabla-firma">
                <tr>
                    <td class="firma-datos">

                        <div class="firma-dato">
                            <span>Forma de pago</span>
                            <strong>{{ $formaPago ?? 'Transferencia' }}</strong>
                        </div>

                        @if(!empty($folioRecibo))
                            <div class="firma-dato">
                                <span>Folio del recibo</span>
                                <strong>{{ $folioRecibo }}</strong>
                            </div>
                        @endif

                        @if(!empty($esEfectivo))
                            <div class="firma-efectivo">
                                Pago recibido en efectivo por la Tesorería.
                                Este recibo firmado es tu comprobante.
                            </div>
                        @endif

                    </td>

                    <td class="firma-espacio">

                        @php($altoFirma = $firmaAlto ?? 62)

                        @if(!empty($firmaImagen))
                            {{-- Solo alto: el ancho libre evita deformarla. --}}
                            <img src="{{ $firmaImagen }}" alt=""
                                 style="height: {{ $altoFirma }}px; width: auto; margin-bottom: 2px;">
                        @else
                            <div style="height: {{ $altoFirma }}px;"></div>
                        @endif

                        <div class="firma-linea"></div>

                        <div class="firma-nombre">
                            {{ $firmanteNombre ?? '' }}
                        </div>

                        <div class="firma-cargo">
                            {{ $firmanteCargo ?? 'Tesorería · Mesa Directiva' }}
                        </div>

                    </td>
                </tr>
            </table>

        </div>


        {{-- =====================================================
             FOOTER
        ====================================================== --}}

        <div class="footer">

            <strong>
                Este documento es un comprobante informativo de pago.
            </strong>

            <br>

            Fue generado automáticamente por el sistema
            con la información registrada por la administración.

            <br>

            Conserva este recibo para futuras aclaraciones.

        </div>


        <div class="internal-id">

            Pago ID:
            {{ $pago->id }}

            ·

            Generado:
            {{ $fechaActual }}

        </div>

    </div>

</body>
</html>