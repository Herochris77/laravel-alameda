{{--
    Estado de cuenta de una vivienda.

    Mismo membrete y tipografía que el reporte mensual, para que los dos
    documentos se archiven juntos sin que desentonen.
--}}
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <style>
        @page { margin: 28px 34px; }

        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #111; }

        .encabezado { text-align: center; margin-bottom: 4px; }

        .condominio { font-size: 14px; font-weight: bold; letter-spacing: .04em; }

        .direccion { font-size: 9px; color: #444; line-height: 1.4; }

        .titulo {
            font-size: 11px;
            font-weight: bold;
            margin: 12px 0 10px;
            text-align: center;
            text-transform: uppercase;
        }

        table { width: 100%; border-collapse: collapse; }

        .ficha { margin-bottom: 12px; }
        .ficha td { padding: 3px 6px; font-size: 10px; }
        .ficha .etiqueta { color: #555; width: 22%; }
        .ficha .valor { font-weight: bold; }

        .detalle th {
            background: #f0f0f0;
            border: 1px solid #bbb;
            padding: 5px 6px;
            font-size: 9px;
            text-align: left;
            text-transform: uppercase;
        }

        .detalle td {
            border: 1px solid #ddd;
            padding: 4px 6px;
            font-size: 9.5px;
        }

        .num { text-align: right; }

        .seccion {
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            margin: 14px 0 5px;
            border-bottom: 1px solid #999;
            padding-bottom: 2px;
        }

        .marca { font-size: 8px; color: #666; }

        .resumen td {
            border: 1px solid #ddd;
            padding: 5px 7px;
            font-size: 10px;
        }

        .resumen .rotulo { width: 70%; }

        .resumen .fuerte { font-weight: bold; background: #f7f7f7; }

        .veredicto {
            margin-top: 12px;
            padding: 9px 11px;
            border: 1.5px solid #333;
            text-align: center;
            font-size: 11px;
            font-weight: bold;
        }

        .veredicto.debe { border-color: #a00; color: #a00; }
        .veredicto.ok { border-color: #060; color: #060; }

        .nota {
            margin-top: 12px;
            font-size: 8.5px;
            color: #555;
            line-height: 1.5;
        }

        .pie {
            margin-top: 16px;
            font-size: 8px;
            color: #666;
            text-align: center;
        }
    </style>
</head>
<body>

    <div class="encabezado">
        @include('pdf.logo', ['alto' => 46])
        <div class="condominio">CONDOMINIO ALAMEDA</div>
        <div class="direccion">
            Av. Los Arados No. 1, Fracc. Hacienda del Bosque<br>
            Los Ángeles, Qro. C.P. 76902
        </div>
    </div>

    <div class="titulo">Estado de cuenta por vivienda</div>

    <table class="ficha">
        <tr>
            <td class="etiqueta">Vivienda</td>
            <td class="valor">Casa {{ $datos['usuario']->casa }}</td>
            <td class="etiqueta">Emitido</td>
            <td class="valor">{{ $datos['generado']->translatedFormat('j \d\e F \d\e Y') }}</td>
        </tr>
        <tr>
            <td class="etiqueta">Titular</td>
            <td class="valor" colspan="3">{{ $datos['usuario']->nombre }}</td>
        </tr>
    </table>

    <div class="seccion">Recibos</div>

    <table class="detalle">
        <thead>
            <tr>
                <th>Concepto</th>
                <th>Vence</th>
                <th class="num">Cuota</th>
                <th class="num">Recargo</th>
                <th class="num">Total</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            @forelse($datos['cargos'] as $c)
                <tr>
                    <td>{{ $c['concepto'] }}</td>
                    <td>{{ optional($c['vencimiento'])->format('d/m/Y') ?: '—' }}</td>
                    <td class="num">$ {{ number_format($c['cuota'], 2) }}</td>
                    <td class="num">{{ $c['recargo'] > 0 ? '$ '.number_format($c['recargo'], 2) : '—' }}</td>
                    <td class="num">$ {{ number_format($c['esperado'], 2) }}</td>
                    <td>
                        @if($c['estado'] === 'pagado')
                            Pagado
                            @if($c['con_saldo'])
                                <div class="marca">con saldo a favor</div>
                            @elseif($c['fecha_pago'])
                                <div class="marca">
                                    {{ $c['fecha_pago']->format('d/m/Y') }}{{ $c['tarde'] ? ' · tarde' : '' }}
                                </div>
                            @endif
                        @elseif($c['estado'] === 'rechazado')
                            Rechazado
                        @else
                            Pendiente
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="6">Sin recibos registrados.</td></tr>
            @endforelse
        </tbody>
    </table>

    @if(count($datos['multas']))
        <div class="seccion">Multas</div>

        <table class="detalle">
            <thead>
                <tr>
                    <th>Motivo</th>
                    <th>Fecha</th>
                    <th class="num">Monto</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                @foreach($datos['multas'] as $m)
                    <tr>
                        <td>{{ $m['motivo'] }}</td>
                        <td>{{ optional($m['fecha'])->format('d/m/Y') ?: '—' }}</td>
                        <td class="num">$ {{ number_format($m['monto'], 2) }}</td>
                        <td>{{ $m['pagada'] ? 'Pagada' : 'Pendiente' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    @if(count($datos['movimientos_saldo']))
        <div class="seccion">Movimientos de saldo a favor</div>

        <table class="detalle">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Movimiento</th>
                    <th class="num">Monto</th>
                </tr>
            </thead>
            <tbody>
                @foreach($datos['movimientos_saldo'] as $m)
                    <tr>
                        <td>{{ $m['fecha']->format('d/m/Y') }}</td>
                        <td>{{ $m['descripcion'] ?: ucfirst($m['tipo']) }}</td>
                        <td class="num">{{ $m['suma'] ? '+' : '−' }} $ {{ number_format(abs($m['monto']), 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <div class="seccion">Resumen</div>

    <table class="resumen">
        <tr>
            <td class="rotulo">Total cargado (cuotas y recargos)</td>
            <td class="num">$ {{ number_format($datos['totales']['cargado'], 2) }}</td>
        </tr>
        <tr>
            <td class="rotulo">Pagado</td>
            <td class="num">$ {{ number_format($datos['totales']['pagado'], 2) }}</td>
        </tr>
        @if($datos['totales']['con_saldo'] > 0)
            <tr>
                <td class="rotulo" style="padding-left: 20px;">de eso, cubierto con saldo a favor</td>
                <td class="num">$ {{ number_format($datos['totales']['con_saldo'], 2) }}</td>
            </tr>
        @endif
        @if($datos['totales']['multas'] > 0)
            <tr>
                <td class="rotulo">Multas</td>
                <td class="num">$ {{ number_format($datos['totales']['multas'], 2) }}</td>
            </tr>
        @endif
        <tr class="fuerte">
            <td class="rotulo">Pendiente de pago</td>
            <td class="num">$ {{ number_format($datos['totales']['pendiente'], 2) }}</td>
        </tr>
        <tr class="fuerte">
            <td class="rotulo">Saldo a favor disponible</td>
            <td class="num">$ {{ number_format($datos['saldo_favor'], 2) }}</td>
        </tr>
    </table>

    @if($datos['neto'] > 0.009)
        <div class="veredicto debe">
            Adeudo de $ {{ number_format($datos['neto'], 2) }}
        </div>
    @elseif($datos['neto'] < -0.009)
        <div class="veredicto ok">
            Al corriente · saldo a favor de $ {{ number_format(abs($datos['neto']), 2) }}
        </div>
    @else
        <div class="veredicto ok">Al corriente. Sin adeudo.</div>
    @endif

    <div class="nota">
        Los recargos por pago tardío se calculan contra la fecha en que se realizó
        la transferencia, no contra la fecha en que se subió el comprobante.
        Un recibo marcado "con saldo a favor" se cubrió con dinero que la vivienda
        había adelantado previamente.
        @if($datos['totales']['pagos_tardios'] > 0)
            Esta vivienda registra {{ $datos['totales']['pagos_tardios'] }}
            pago(s) realizado(s) después del vencimiento.
        @endif
    </div>

    <div class="pie">
        Documento generado por la plataforma del Condominio Alameda el
        {{ $datos['generado']->translatedFormat('j \d\e F \d\e Y \a \l\a\s H:i') }}.
        Para aclaraciones, dirígete a la Tesorería de la mesa directiva.
    </div>

</body>
</html>
