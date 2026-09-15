<x-app-layout>

    <div class="page-header">
        <div class="page-header-left">
            <div class="page-icon" style="background: linear-gradient(135deg, #0369a1 0%, #075985 100%);">
                <i class="home icon"></i>
            </div>
            <div>
                <h1 class="page-title">Estado de cuenta por vivienda</h1>
                <p class="page-subtitle">
                    Quién debe, quién trae saldo a favor, y el PDF que se le entrega al vecino.
                </p>
            </div>
        </div>
    </div>

    <div class="ec-kpis">
        <div class="ec-kpi">
            <span class="ec-kpi-label">Viviendas</span>
            <span class="ec-kpi-valor">{{ $resumen['total'] }}</span>
        </div>
        <div class="ec-kpi ok">
            <span class="ec-kpi-label">Al corriente</span>
            <span class="ec-kpi-valor">{{ $resumen['al_corriente'] }}</span>
        </div>
        <div class="ec-kpi {{ $resumen['con_adeudo'] > 0 ? 'alerta' : '' }}">
            <span class="ec-kpi-label">Con adeudo</span>
            <span class="ec-kpi-valor">{{ $resumen['con_adeudo'] }}</span>
        </div>
        <div class="ec-kpi">
            <span class="ec-kpi-label">Por cobrar</span>
            <span class="ec-kpi-valor">${{ number_format($resumen['por_cobrar'], 2) }}</span>
        </div>
        <div class="ec-kpi">
            <span class="ec-kpi-label">Saldo a favor de vecinos</span>
            <span class="ec-kpi-valor">${{ number_format($resumen['saldo_favor'], 2) }}</span>
            <span class="ec-kpi-nota">Dinero del fondo ya comprometido</span>
        </div>
    </div>

    <div class="card">
        <div class="card-body">

            <div class="ec-toolbar">
                <div class="ui icon input ec-buscador">
                    <i class="search icon"></i>
                    <input type="text" id="ec-buscar" placeholder="Buscar por casa o nombre">
                </div>

                <select id="ec-filtro" class="form-input ec-filtro">
                    <option value="">Todas</option>
                    <option value="deben">Solo con adeudo</option>
                    <option value="favor">Solo con saldo a favor</option>
                </select>
            </div>

            <div class="ec-tabla-wrap">
                <table class="ec-tabla">
                    <thead>
                        <tr>
                            <th>Casa</th>
                            <th>Titular</th>
                            <th class="num">Pendiente</th>
                            <th class="num">Saldo a favor</th>
                            <th class="num">Neto</th>
                            <th>Situación</th>
                            <th class="acc">Documentos</th>
                        </tr>
                    </thead>
                    <tbody id="ec-cuerpo"></tbody>
                </table>
            </div>

        </div>
    </div>

    <style>
        .ec-kpis {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 12px;
            margin-bottom: 18px;
        }

        .ec-kpi {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 12px 14px;
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .ec-kpi.ok { border-color: #a7f3d0; background: #ecfdf5; }
        .ec-kpi.alerta { border-color: #fca5a5; background: #fef2f2; }

        .ec-kpi-label {
            font-size: .71rem;
            text-transform: uppercase;
            letter-spacing: .05em;
            color: #64748b;
            font-weight: 700;
        }

        .ec-kpi-valor {
            font-size: 1.4rem;
            font-weight: 700;
            color: #0f172a;
            font-variant-numeric: tabular-nums;
        }

        .ec-kpi-nota { font-size: .72rem; color: #94a3b8; line-height: 1.35; }

        .ec-toolbar { display: flex; gap: 10px; margin-bottom: 14px; flex-wrap: wrap; }
        .ec-buscador { flex: 1; min-width: 220px; }
        .ec-filtro { width: auto; min-width: 190px; }

        .ec-tabla-wrap { overflow-x: auto; }

        .ec-tabla { width: 100%; border-collapse: collapse; font-size: .87rem; }

        .ec-tabla th {
            text-align: left;
            padding: 9px 10px;
            background: #f8fafc;
            font-size: .72rem;
            text-transform: uppercase;
            letter-spacing: .04em;
            color: #475569;
            white-space: nowrap;
        }

        .ec-tabla td { padding: 9px 10px; border-bottom: 1px solid #f1f5f9; }

        .ec-tabla .num { text-align: right; font-variant-numeric: tabular-nums; white-space: nowrap; }
        .ec-tabla .acc { text-align: right; }

        .ec-casa {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 34px;
            height: 26px;
            padding: 0 7px;
            border-radius: 7px;
            background: #eff6ff;
            color: #1d4ed8;
            font-weight: 700;
            font-size: .82rem;
        }

        .ec-pill {
            display: inline-block;
            padding: 2px 9px;
            border-radius: 999px;
            font-size: .72rem;
            font-weight: 700;
            white-space: nowrap;
        }

        .ec-pill.ok { background: #dcfce7; color: #166534; }
        .ec-pill.debe { background: #fee2e2; color: #991b1b; }
        .ec-pill.favor { background: #dbeafe; color: #1e40af; }

        .ec-neto-debe { color: #b91c1c; font-weight: 700; }
        .ec-neto-favor { color: #1d4ed8; font-weight: 700; }

        .ec-tarde {
            display: block;
            font-size: .7rem;
            color: #b45309;
            margin-top: 2px;
        }

        .ec-vacio { text-align: center; padding: 30px; color: #94a3b8; }
    </style>

    <script>
        $(function () {
            const BASE_URL = "{{ url('/') }}";
            const VIVIENDAS = @json($viviendas);

            const fmt = n => '$' + Number(n).toLocaleString('es-MX', {
                minimumFractionDigits: 2, maximumFractionDigits: 2
            });

            const esc = t => $('<div>').text(t == null ? '' : t).html();

            function pintar() {
                const texto = $('#ec-buscar').val().toLowerCase().trim();
                const filtro = $('#ec-filtro').val();

                const filas = VIVIENDAS.filter(v => {
                    if (filtro === 'deben' && v.al_corriente) return false;
                    if (filtro === 'favor' && v.saldo_favor <= 0) return false;
                    if (!texto) return true;

                    return String(v.casa).toLowerCase().includes(texto)
                        || (v.nombre || '').toLowerCase().includes(texto);
                });

                if (!filas.length) {
                    $('#ec-cuerpo').html('<tr><td colspan="7" class="ec-vacio">Ninguna vivienda coincide.</td></tr>');
                    return;
                }

                $('#ec-cuerpo').html(filas.map(v => {
                    /*
                     * El neto positivo es deuda y el negativo es saldo a favor.
                     * Se muestran como conceptos distintos y no como un número
                     * con signo, que se presta a leerse al revés.
                     */
                    let situacion, neto;

                    if (v.neto > 0.009) {
                        situacion = '<span class="ec-pill debe">Debe</span>';
                        neto = `<span class="ec-neto-debe">${fmt(v.neto)}</span>`;
                    } else if (v.neto < -0.009) {
                        situacion = '<span class="ec-pill favor">A favor</span>';
                        neto = `<span class="ec-neto-favor">${fmt(Math.abs(v.neto))}</span>`;
                    } else {
                        situacion = '<span class="ec-pill ok">Al corriente</span>';
                        neto = '—';
                    }

                    const tarde = v.pagos_tardios > 0
                        ? `<span class="ec-tarde">${v.pagos_tardios} pago(s) tardío(s)</span>` : '';

                    // La constancia solo se ofrece a quien puede recibirla.
                    const constancia = v.al_corriente
                        ? `<a class="ui mini teal button" target="_blank"
                              href="${BASE_URL}/administrador/estado-cuenta/constancia/${v.id}">
                               <i class="certificate icon"></i> Constancia
                           </a>`
                        : '';

                    return `
                        <tr>
                            <td><span class="ec-casa">${esc(v.casa)}</span></td>
                            <td>${esc(v.nombre)}${tarde}</td>
                            <td class="num">${v.pendiente > 0 ? fmt(v.pendiente) : '—'}</td>
                            <td class="num">${v.saldo_favor > 0 ? fmt(v.saldo_favor) : '—'}</td>
                            <td class="num">${neto}</td>
                            <td>${situacion}</td>
                            <td class="acc">
                                <a class="ui mini button" target="_blank"
                                   href="${BASE_URL}/administrador/estado-cuenta/pdf/${v.id}">
                                    <i class="file pdf outline icon"></i> Estado de cuenta
                                </a>
                                ${constancia}
                            </td>
                        </tr>`;
                }).join(''));
            }

            $('#ec-buscar').on('input', pintar);
            $('#ec-filtro').on('change', pintar);

            pintar();
        });
    </script>

</x-app-layout>
