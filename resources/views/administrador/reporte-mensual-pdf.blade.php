{{--
    Reporte mensual de ingresos y egresos.

    Sigue el formato que la mesa directiva provisional venía entregando en papel, para que
    pueda archivarse junto a los de meses anteriores sin que se note el cambio
    de herramienta.
--}}
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <style>
        @page { margin: 28px 34px; }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
            color: #111;
        }

        .encabezado { text-align: center; margin-bottom: 4px; }

        .condominio {
            font-size: 14px;
            font-weight: bold;
            letter-spacing: .04em;
        }

        .direccion { font-size: 9px; color: #444; line-height: 1.4; }

        .titulo {
            font-size: 11px;
            font-weight: bold;
            margin: 12px 0 10px;
            text-align: center;
            text-transform: uppercase;
        }

        table { width: 100%; border-collapse: collapse; }

        .tabla th {
            background: #e8eef5;
            border: 1px solid #9bb0c4;
            padding: 5px 7px;
            font-size: 9.5px;
            text-align: left;
        }

        .tabla td {
            border: 1px solid #c3d0dc;
            padding: 4px 7px;
        }

        .monto { text-align: right; white-space: nowrap; }

        .seccion td {
            background: #f2f6fa;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 9.5px;
        }

        .total td {
            background: #dce7f1;
            font-weight: bold;
            border-top: 2px solid #9bb0c4;
        }

        .saldo td {
            font-weight: bold;
            background: #fff;
        }

        .cat { color: #667; font-size: 8.5px; }

        .aviso {
            margin-top: 10px;
            padding: 6px 8px;
            border: 1px solid #f0c36d;
            background: #fdf6e3;
            font-size: 8.5px;
        }

        .anexos { margin-top: 14px; font-size: 9px; }
        .anexos strong { display: block; margin-bottom: 3px; }
        .anexos ol { margin: 0; padding-left: 18px; }

        .firmas { width: 100%; margin-top: 34px; }
        .firmas td { text-align: center; font-size: 9px; padding: 0 8px; vertical-align: bottom; }
        .linea-firma { border-top: 1px solid #333; padding-top: 4px; margin-top: 36px; }
        .firma-nombre { font-weight: bold; }
        .firma-cargo { color: #555; }
        .firma-rol { font-size: 8.5px; color: #666; margin-bottom: 2px; }

        .pie { margin-top: 16px; font-size: 8px; color: #888; text-align: center; }

        /* --- Anexo de comprobantes --- */
        .anexo-hoja { page-break-before: always; }

        .anexo-titulo {
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
            text-align: center;
            margin-bottom: 10px;
        }

        .anexo-ficha {
            border: 1px solid #9bb0c4;
            background: #f2f6fa;
            padding: 6px 8px;
            margin-bottom: 8px;
            font-size: 9px;
        }

        .anexo-ficha .concepto { font-weight: bold; font-size: 10px; }
        .anexo-ficha .datos { color: #445; margin-top: 2px; }

        .anexo-imagen { text-align: center; }

        .anexo-ausente {
            border: 1px dashed #b9a06a;
            background: #fdf8ec;
            padding: 10px 12px;
            font-size: 9px;
            color: #6b5a30;
        }

        .anexo-ausente .archivo { font-family: DejaVu Sans Mono, monospace; font-size: 8px; color: #7a6a44; }
    </style>
</head>
<body>

    <div class="encabezado">
        @include('pdf.logo', ['alto' => 46])
        <div class="condominio">CONDOMINIO LA ALAMEDA</div>
        <div class="direccion">
            Av. Los Arados No. 1 · Fracc. Hacienda del Bosque<br>
            Los Ángeles, Qro. C.P. 76902
        </div>
    </div>

    <div class="titulo">
        Reporte de ingresos y egresos del mes de {{ $datos['periodo_texto'] }}
    </div>

    <table class="tabla">
        <thead>
            <tr>
                <th style="width: 72%;">Concepto</th>
                <th style="width: 28%; text-align: right;">Total</th>
            </tr>
        </thead>
        <tbody>

            <tr class="saldo">
                <td>Saldo Inicial — Efectivo y Bancos</td>
                <td class="monto">$ {{ number_format($datos['saldo_inicial'], 2) }}</td>
            </tr>

            <tr class="seccion">
                <td colspan="2">Ingresos</td>
            </tr>

            @forelse($datos['ingresos'] as $linea)
                <tr>
                    <td>{{ $linea['concepto'] }}</td>
                    <td class="monto">
                        @if($linea['monto'] > 0)
                            $ {{ number_format($linea['monto'], 2) }}
                        @else
                            $ —
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="2">Sin ingresos registrados en el periodo.</td>
                </tr>
            @endforelse

            <tr class="total">
                <td>Total de ingresos</td>
                <td class="monto">$ {{ number_format($datos['total_ingresos'], 2) }}</td>
            </tr>

            <tr class="seccion">
                <td colspan="2">Egresos</td>
            </tr>

            @forelse($datos['egresos'] as $linea)
                <tr>
                    <td>
                        {{ $linea['concepto'] }}
                        <span class="cat">· {{ $linea['categoria'] }}</span>
                    </td>
                    <td class="monto">$ {{ number_format($linea['monto'], 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="2">Sin egresos registrados en el periodo.</td>
                </tr>
            @endforelse

            <tr class="total">
                <td>Total de egresos</td>
                <td class="monto">$ {{ number_format($datos['total_egresos'], 2) }}</td>
            </tr>

            <tr class="saldo">
                <td>Saldo Final — Efectivo y Bancos</td>
                <td class="monto">$ {{ number_format($datos['saldo_final'], 2) }}</td>
            </tr>

            {{--
                El desglose solo aporta cuando hay efectivo. Si todo está en el
                banco, repetir la misma cifra dos veces confunde más que aclara.
            --}}
            @if($datos['cierre'] && $datos['cierre']->saldo_efectivo > 0)
                <tr>
                    <td style="padding-left: 18px;">Saldo en cuenta</td>
                    <td class="monto">$ {{ number_format($datos['cierre']->saldo_banco, 2) }}</td>
                </tr>
                <tr>
                    <td style="padding-left: 18px;">Efectivo</td>
                    <td class="monto">$ {{ number_format($datos['cierre']->saldo_efectivo, 2) }}</td>
                </tr>
            @endif

        </tbody>
    </table>

    @if($datos['cierre'] && abs($datos['descuadre']) >= 0.01)
        <div class="aviso">
            <strong>Diferencia de ${{ number_format(abs($datos['descuadre']), 2) }}</strong>
            entre el saldo del estado de cuenta y el que resulta de los movimientos
            registrados. Suele deberse a cobros o gastos que aún no se capturan en la
            plataforma.
        </div>
    @endif

    @if($datos['cierre'] && $datos['cierre']->notas)
        <div class="anexos">
            <strong>Notas</strong>
            {{ $datos['cierre']->notas }}
        </div>
    @endif

    @php
        $conComprobante = collect($datos['egresos'])->filter(fn ($e) => ($e['comprobante']['estado'] ?? '') === 'imagen');
        $sinComprobante = collect($datos['egresos'])->filter(fn ($e) => ($e['comprobante']['estado'] ?? '') !== 'imagen');
    @endphp

    <div class="anexos">
        <strong>Se anexan documentos de respaldo:</strong>
        @if(($incluirComprobantes ?? true) && $conComprobante->count() > 0)
            <ol>
                <li>Carátula de estado de cuenta</li>
                <li>{{ $conComprobante->count() }} comprobante(s) de egreso, al final de este documento</li>
                @if($sinComprobante->count() > 0)
                    <li>{{ $sinComprobante->count() }} comprobante(s) que se consultan en el módulo de Documentos</li>
                @endif
            </ol>
        @else
            <ol>
                <li>Carátula de estado de cuenta</li>
                <li>Recibos, remisiones, facturas</li>
                <li>Comprobantes de pago</li>
            </ol>
        @endif
    </div>

    <table class="firmas">
        <tr>
            @foreach($datos['firmas'] as $firma)
                <td style="width: 33%;">
                    <div class="firma-rol">{{ $firma['rol'] }}</div>
                    <div class="linea-firma">
                        <div class="firma-nombre">{{ $firma['nombre'] ?: '&nbsp;' }}</div>
                        <div class="firma-cargo">{{ $firma['cargo'] }}</div>
                    </div>
                </td>
            @endforeach
        </tr>
    </table>

    @include('pdf.pie')

    {{--
        Anexo: los comprobantes de cada egreso, uno por hoja.

        Va después de las firmas a propósito: el reporte que se firma son las
        cifras, y los comprobantes son el respaldo que se engrapa detrás, igual
        que en el documento de papel.

        Cada egreso aparece SIEMPRE, traiga imagen o no. Un gasto que se salta
        en silencio parecería un gasto sin comprobar.
    --}}
    @if(($incluirComprobantes ?? true) && count($datos['egresos']) > 0)

        @foreach($datos['egresos'] as $egreso)
            @php $c = $egreso['comprobante']; @endphp

            <div class="anexo-hoja">
                <div class="anexo-titulo">
                    Anexo {{ $loop->iteration }} de {{ $loop->count }} · Comprobante de egreso
                </div>

                <div class="anexo-ficha">
                    <div class="concepto">{{ $egreso['concepto'] }}</div>
                    <div class="datos">
                        $ {{ number_format($egreso['monto'], 2) }}
                        · {{ $egreso['fecha'] ?? 'Sin fecha' }}@unless($egreso['fecha_real']) (aproximada)@endunless
                        · {{ ucfirst($egreso['categoria']) }}
                        @if($egreso['proveedor']) · {{ $egreso['proveedor'] }} @endif
                    </div>
                </div>

                @if($c['estado'] === 'imagen')
                    <div class="anexo-imagen">
                        <img src="{{ $c['ruta'] }}"
                             width="{{ $c['ancho'] }}"
                             @if($c['alto']) height="{{ $c['alto'] }}" @endif
                             alt="Comprobante">
                    </div>
                @else
                    <div class="anexo-ausente">
                        {{ $c['nota'] }}
                        @if($c['nombre'])
                            <div class="archivo">Archivo: {{ $c['nombre'] }}</div>
                        @endif
                    </div>
                @endif
            </div>
        @endforeach

    @endif

</body>
</html>
