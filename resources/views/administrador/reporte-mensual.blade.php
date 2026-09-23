<x-app-layout>

    <div class="page-header">
        <div class="page-header-left">
            <div class="page-icon" style="background: linear-gradient(135deg, #0f766e 0%, #115e59 100%);">
                <i class="file alternate icon"></i>
            </div>
            <div>
                <h1 class="page-title">Reporte mensual</h1>
                <p class="page-subtitle">Ingresos y egresos del mes, en el formato que se entrega a la mesa directiva provisional.</p>
            </div>
        </div>
    </div>

    <div class="card" style="margin-bottom: 18px;">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.reporteMensual.index') }}" class="rm-barra">

                <div class="rm-campo">
                    <label class="form-label">Periodo</label>
                    <select name="periodo" class="form-input" onchange="this.form.submit()">
                        @foreach($periodos as $p)
                            <option value="{{ $p }}" {{ $p === $periodo ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::parse($p.'-01')->translatedFormat('F Y') }}
                            </option>
                        @endforeach
                    </select>
                </div>

                @php
                    $compro = collect($datos['egresos'])->pluck('comprobante.estado');
                    $conImagen = $compro->filter(fn ($e) => $e === 'imagen')->count();
                    $sinImagen = $compro->count() - $conImagen;
                @endphp

                <a href="{{ route('admin.reporteMensual.pdf', ['periodo' => $periodo]) }}"
                   class="btn btn-primary rm-btn-pdf">
                    <i class="file pdf outline icon"></i>
                    Descargar PDF
                </a>

                <a href="{{ route('admin.reporteMensual.pdf', ['periodo' => $periodo, 'comprobantes' => 0]) }}"
                   class="btn btn-secondary rm-btn-pdf">
                    <i class="file alternate outline icon"></i>
                    Solo las cifras
                </a>

            </form>

            <div class="rm-comprobantes">
                @if($compro->isEmpty())
                    Este mes no tiene egresos capturados, así que el PDF sale sin anexo.
                @else
                    <strong>Anexo de comprobantes:</strong>
                    <strong>{{ $conImagen }} de {{ $compro->count() }}</strong>
                    egreso(s) del mes se anexan como imagen al final del PDF.
                    @if($sinImagen > 0)
                        Los otros <strong>{{ $sinImagen }}</strong> solo se nombran, para
                        consultarse en Documentos: son archivos PDF, están ausentes, o el
                        servidor no puede incrustarlos.
                    @endif
                    Con <em>Solo las cifras</em> obtienes el reporte sin el anexo.
                @endif
            </div>
        </div>
    </div>

    <div class="rm-columnas">

        {{-- CIFRAS DEL MES --}}
        <div class="card">
            <div class="card-body">

                <table class="rm-tabla">
                    <tr class="rm-saldo">
                        <td>Saldo inicial — efectivo y bancos</td>
                        <td class="rm-monto">${{ number_format($datos['saldo_inicial'], 2) }}</td>
                    </tr>

                    @if($datos['cierre_anterior'])
                        <tr>
                            <td colspan="2" class="rm-nota">
                                Arrastrado del cierre de
                                {{ \Carbon\Carbon::parse($datos['cierre_anterior']->periodo.'-01')->translatedFormat('F Y') }}
                            </td>
                        </tr>
                    @else
                        <tr>
                            <td colspan="2" class="rm-nota rm-aviso-nota">
                                No hay cierre de un mes anterior, por eso arranca en cero.
                                Captura abajo el cierre de este mes y los siguientes se encadenarán solos.
                            </td>
                        </tr>
                    @endif

                    <tr class="rm-seccion"><td colspan="2">Ingresos</td></tr>

                    @forelse($datos['ingresos'] as $l)
                        <tr>
                            <td>{{ $l['concepto'] }}</td>
                            <td class="rm-monto">
                                {{ $l['monto'] > 0 ? '$'.number_format($l['monto'], 2) : '—' }}
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="2" class="rm-nota">Sin ingresos en el periodo.</td></tr>
                    @endforelse

                    <tr class="rm-total">
                        <td>Total de ingresos</td>
                        <td class="rm-monto">${{ number_format($datos['total_ingresos'], 2) }}</td>
                    </tr>

                    <tr class="rm-seccion"><td colspan="2">Egresos</td></tr>

                    @forelse($datos['egresos'] as $l)
                        <tr>
                            <td>
                                {{ $l['concepto'] }}
                                <span class="rm-cat">· {{ $l['categoria'] }}</span>
                                @if(! ($l['fecha_real'] ?? true))
                                    <span class="rm-aprox" title="Se está usando la fecha en que se subió el comprobante, no la del movimiento bancario. Edita el gasto para capturarla.">
                                        fecha aproximada
                                    </span>
                                @endif
                            </td>
                            <td class="rm-monto">${{ number_format($l['monto'], 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="2" class="rm-nota">Sin egresos en el periodo.</td></tr>
                    @endforelse

                    <tr class="rm-total">
                        <td>Total de egresos</td>
                        <td class="rm-monto">${{ number_format($datos['total_egresos'], 2) }}</td>
                    </tr>

                    <tr class="rm-saldo rm-saldo-final">
                        <td>Saldo final — efectivo y bancos</td>
                        <td class="rm-monto">${{ number_format($datos['saldo_final'], 2) }}</td>
                    </tr>
                </table>

            </div>
        </div>

        {{-- CIERRE DE CAJA --}}
        <div class="card">
            <div class="card-body">

                <h3 class="rm-titulo">Cierre del mes</h3>
                <p class="rm-ayuda">
                    Captura lo que dice tu estado de cuenta al cerrar el mes. Es el único dato
                    que la plataforma no puede saber, y es el que arrastra el saldo inicial del
                    mes siguiente.
                </p>

                <form id="form-cierre">
                    @csrf
                    <input type="hidden" name="periodo" value="{{ $periodo }}">

                    <div class="form-group">
                        <label class="form-label">Saldo en cuenta bancaria *</label>
                        <div class="input-icon-wrapper">
                            <i class="university icon"></i>
                            <input type="number" step="0.01" min="0" required
                                   name="saldo_banco" id="cierre-banco"
                                   class="form-input input-with-icon"
                                   value="{{ $datos['cierre']->saldo_banco ?? '' }}"
                                   placeholder="0.00">
                        </div>
                        <p class="rm-hint">
                            Tal cual lo reporta tu estado de cuenta al último día del mes.
                        </p>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Efectivo en caja</label>
                        <div class="input-icon-wrapper">
                            <i class="money bill wave icon"></i>
                            <input type="number" step="0.01" min="0"
                                   name="saldo_efectivo" id="cierre-efectivo"
                                   class="form-input input-with-icon"
                                   value="{{ ($datos['cierre'] && $datos['cierre']->saldo_efectivo > 0) ? $datos['cierre']->saldo_efectivo : '' }}"
                                   placeholder="0.00">
                        </div>
                        <p class="rm-hint">
                            Billetes en tu poder sin depositar. Si todo se maneja por
                            transferencia, déjalo vacío.
                        </p>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Notas (opcional)</label>
                        <textarea name="notas" rows="2" maxlength="500"
                                  class="form-input"
                                  placeholder="Aclaraciones para la mesa directiva provisional">{{ $datos['cierre']->notas ?? '' }}</textarea>
                    </div>

                    <div id="rm-comparativo" class="rm-comparativo" style="display:none;"></div>

                    <button type="submit" class="btn btn-primary" style="width:100%;">
                        <i class="save icon"></i>
                        Guardar cierre
                    </button>
                </form>

                @if($datos['cierre'])
                    <div class="rm-guardado">
                        <i class="check circle icon"></i>
                        Cierre registrado: ${{ number_format($datos['cierre']->total(), 2) }}
                        @if(abs($datos['descuadre']) >= 0.01)
                            <div class="rm-descuadre">
                                Difiere en ${{ number_format(abs($datos['descuadre']), 2) }}
                                de lo que resulta de los movimientos registrados.
                            </div>
                        @endif
                    </div>
                @endif

            </div>
        </div>

    </div>

    <style>
        .rm-barra {
            display: flex;
            align-items: flex-end;
            gap: 12px;
            flex-wrap: wrap;
        }

        .rm-campo { flex: 1; min-width: 200px; }

        .rm-btn-pdf { white-space: nowrap; }
        .rm-comprobantes { margin-top: 12px; font-size: .86rem; color: #475569; line-height: 1.5; }

        .rm-columnas {
            display: grid;
            grid-template-columns: minmax(0, 1.7fr) minmax(0, 1fr);
            gap: 18px;
            align-items: start;
        }

        .rm-tabla { width: 100%; border-collapse: collapse; font-size: .9rem; }

        .rm-tabla td {
            padding: 7px 10px;
            border-bottom: 1px solid #f1f5f9;
        }

        .rm-monto {
            text-align: right;
            white-space: nowrap;
            font-variant-numeric: tabular-nums;
        }

        .rm-seccion td {
            background: #f8fafc;
            font-weight: 700;
            text-transform: uppercase;
            font-size: .76rem;
            letter-spacing: .04em;
            color: #475569;
        }

        .rm-total td {
            font-weight: 700;
            background: #f1f5f9;
            border-top: 2px solid #e2e8f0;
        }

        .rm-saldo td { font-weight: 700; }

        .rm-saldo-final td {
            background: #ecfdf5;
            color: #065f46;
            border-top: 2px solid #a7f3d0;
        }

        .rm-cat { color: #94a3b8; font-size: .78rem; }

        .rm-aprox {
            display: inline-block;
            margin-left: 6px;
            padding: 1px 7px;
            border-radius: 999px;
            background: #fef3c7;
            color: #92400e;
            font-size: .68rem;
            font-weight: 700;
            cursor: help;
        }

        .rm-nota { color: #64748b; font-size: .8rem; }

        .rm-aviso-nota { color: #92400e; background: #fffbeb; }

        .rm-titulo { margin: 0 0 6px; font-size: 1.02rem; }

        .rm-hint {
            margin: 5px 0 0;
            color: #94a3b8;
            font-size: .78rem;
            line-height: 1.45;
        }

        .rm-ayuda {
            color: #64748b;
            font-size: .85rem;
            line-height: 1.5;
            margin-bottom: 14px;
        }

        .rm-comparativo {
            margin-bottom: 12px;
            padding: 10px 12px;
            border-radius: 10px;
            font-size: .84rem;
            line-height: 1.5;
        }

        .rm-comparativo.cuadra {
            background: #ecfdf5;
            border: 1px solid #a7f3d0;
            color: #065f46;
        }

        .rm-comparativo.primero {
            background: #f0f9ff;
            border: 1px solid #bae6fd;
            color: #075985;
        }

        .rm-comparativo.difiere {
            background: #fffbeb;
            border: 1px solid #fcd34d;
            color: #92400e;
        }

        .rm-guardado {
            margin-top: 14px;
            padding: 10px 12px;
            background: #f0f9ff;
            border: 1px solid #bae6fd;
            border-radius: 10px;
            color: #075985;
            font-size: .85rem;
        }

        .rm-descuadre { margin-top: 4px; color: #92400e; }

        @media (max-width: 900px) {
            .rm-columnas { grid-template-columns: 1fr; }
        }
    </style>

    <script>
        $(function () {
            const BASE_URL = "{{ url('/') }}";

            // Lo que deberia haber segun los movimientos registrados.
            const esperado = {{ $datos['saldo_inicial'] + $datos['total_ingresos'] - $datos['total_egresos'] }};

            // Sin cierre previo el mes arranca en cero, asi que la comparacion
            // no significa nada: la "diferencia" es el acumulado historico.
            const hayCierreAnterior = {{ $datos['cierre_anterior'] ? 'true' : 'false' }};
            const flujoDelMes = {{ $datos['total_ingresos'] - $datos['total_egresos'] }};

            /*
             * Compara en vivo lo capturado contra lo que resulta del sistema.
             * Una diferencia no es un error: suele ser un cobro o un gasto que
             * todavia no se registra en la plataforma. Lo importante es verla
             * antes de firmar el reporte.
             */
            function compararCierre() {
                const banco = parseFloat($('#cierre-banco').val());
                const efectivo = parseFloat($('#cierre-efectivo').val());

                if (isNaN(banco) && isNaN(efectivo)) {
                    $('#rm-comparativo').hide();
                    return;
                }

                const capturado = (banco || 0) + (efectivo || 0);
                const dif = Math.round((capturado - esperado) * 100) / 100;
                const fmt = n => '$' + n.toLocaleString('es-MX', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

                if (!hayCierreAnterior) {
                    const inicialImplicito = Math.round((capturado - flujoDelMes) * 100) / 100;

                    $('#rm-comparativo')
                        .attr('class', 'rm-comparativo primero')
                        .html(
                            '<strong>Primer cierre: no hay con qué comparar.</strong><br>' +
                            'Guarda el saldo de tu estado de cuenta tal cual. Esto implica que al ' +
                            'iniciar el mes había ' + fmt(inicialImplicito) + ' acumulado de meses ' +
                            'anteriores. A partir del mes siguiente la comparación ya tiene sentido.'
                        )
                        .show();
                    return;
                }

                if (Math.abs(dif) < 0.01) {
                    $('#rm-comparativo')
                        .attr('class', 'rm-comparativo cuadra')
                        .html('<i class="check circle icon"></i> Cuadra con los movimientos registrados: ' + fmt(esperado) + '.')
                        .show();
                    return;
                }

                $('#rm-comparativo')
                    .attr('class', 'rm-comparativo difiere')
                    .html(
                        '<strong>Diferencia de ' + fmt(Math.abs(dif)) + '</strong><br>' +
                        'Según los movimientos registrados deberías tener ' + fmt(esperado) +
                        ', y capturaste ' + fmt(capturado) + '. ' +
                        (dif > 0
                            ? 'Hay más dinero del esperado: puede faltar registrar un cobro.'
                            : 'Falta dinero: puede faltar registrar un gasto.')
                    )
                    .show();
            }

            $('#cierre-banco, #cierre-efectivo').on('input', compararCierre);
            compararCierre();

            $('#form-cierre').on('submit', function (e) {
                e.preventDefault();

                $.ajax({
                    url: `${BASE_URL}/administrador/reporte-mensual/cierre`,
                    method: 'POST',
                    data: $(this).serialize(),
                    success: function (res) {
                        alertify.alert(res.header, res.message, function () {
                            location.reload();
                        });
                    },
                    error: function (xhr) {
                        const res = xhr.responseJSON;
                        alertify.alert(
                            (res && res.header) || 'Error',
                            (res && res.message) || 'No se pudo guardar el cierre.'
                        );
                    }
                });
            });
        });
    </script>

</x-app-layout>
