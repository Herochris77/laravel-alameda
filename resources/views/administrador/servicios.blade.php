<x-app-layout>

    <div class="page-header">
        <div class="page-header-left">
            <div class="page-icon" style="background: linear-gradient(135deg, #7c3aed 0%, #5b21b6 100%);">
                <i class="calendar alternate icon"></i>
            </div>
            <div>
                <h1 class="page-title">Servicios y pagos recurrentes</h1>
                <p class="page-subtitle">
                    Las obligaciones fijas del condominio: con qué referencia se pagan,
                    cuánto cuestan y cuándo vencen.
                </p>
            </div>
        </div>

        <div class="page-header-right">
            <a href="{{ route('admin.servicio.exportar') }}" class="btn btn-secondary">
                <i class="file excel outline icon"></i>
                Descargar catálogo
            </a>
            @if($puedeEditar)
                <button class="btn btn-primary" id="btn-nuevo">
                    <i class="plus icon"></i>
                    Nuevo servicio
                </button>
            @endif
        </div>
    </div>

    @unless($puedeEditar)
        <div class="sv-aviso-rol">
            <i class="lock icon"></i>
            Estás viendo el catálogo en modo consulta. Registrar servicios y sus pagos
            es del Tesorero.
        </div>
    @endunless

    {{-- RESUMEN --}}
    <div class="sv-kpis">
        <div class="sv-kpi">
            <span class="sv-kpi-label">Carga fija mensual</span>
            <span class="sv-kpi-valor">${{ number_format($resumen['carga_mensual'], 2) }}</span>
            <span class="sv-kpi-nota">Prorrateada: un pago anual cuenta 1/12 cada mes</span>
        </div>

        <div class="sv-kpi">
            <span class="sv-kpi-label">Comprometido a 30 días</span>
            <span class="sv-kpi-valor">${{ number_format($resumen['proximos_30'], 2) }}</span>
            <span class="sv-kpi-nota">Lo que debe estar disponible en la cuenta</span>
        </div>

        <div class="sv-kpi {{ $resumen['por_vencer'] > 0 ? 'alerta' : '' }}">
            <span class="sv-kpi-label">Por vencer</span>
            <span class="sv-kpi-valor">{{ $resumen['por_vencer'] }}</span>
            <span class="sv-kpi-nota">Dentro de su ventana de aviso</span>
        </div>

        <div class="sv-kpi {{ $resumen['vencidos'] > 0 ? 'peligro' : '' }}">
            <span class="sv-kpi-label">Vencidos</span>
            <span class="sv-kpi-valor">{{ $resumen['vencidos'] }}</span>
            <span class="sv-kpi-nota">
                @if($resumen['vencidos'] > 0)
                    ${{ number_format($resumen['monto_vencido'], 2) }} sin pagar
                @else
                    Nada pendiente
                @endif
            </span>
        </div>
    </div>

    {{-- LISTA --}}
    <div class="card">
        <div class="card-body">

            <div class="sv-toolbar">
                <div class="ui icon input sv-buscador">
                    <i class="search icon"></i>
                    <input type="text" id="sv-buscar" placeholder="Buscar servicio, proveedor o referencia">
                </div>

                <select id="sv-filtro" class="form-input sv-filtro">
                    <option value="">Todos los estados</option>
                    <option value="vencido">Vencidos</option>
                    <option value="hoy">Vencen hoy</option>
                    <option value="por_vencer">Por vencer</option>
                    <option value="al_corriente">Al corriente</option>
                    <option value="inactivo">Inactivos</option>
                </select>
            </div>

            <div id="sv-lista"></div>

        </div>
    </div>

    {{-- MODAL: alta y edición --}}
    <div class="ui modal" id="modal-servicio">
        <div class="header" id="modal-servicio-titulo">Nuevo servicio</div>
        <div class="content">
            <form id="form-servicio">
                @csrf
                <input type="hidden" name="id" id="sv-id">

                <div class="sv-grid">
                    <div class="form-group sv-col-2">
                        <label class="form-label">Servicio *</label>
                        <input type="text" name="nombre" id="sv-nombre" class="form-input"
                               placeholder="Ej. Agua CEA" maxlength="120" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Proveedor</label>
                        <input type="text" name="proveedor" id="sv-proveedor" class="form-input"
                               placeholder="Ej. Comisión Estatal de Aguas" maxlength="120">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Referencia de pago</label>
                        <input type="text" name="referencia" id="sv-referencia" class="form-input"
                               placeholder="Número de cuenta o referencia" maxlength="120">
                        <p class="sv-hint">El dato con el que se paga en ventanilla o en banca en línea.</p>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Categoría *</label>
                        <select name="categoria" id="sv-categoria" class="form-input" required>
                            @foreach($categorias as $valor => $texto)
                                <option value="{{ $valor }}">{{ $texto }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Monto estimado *</label>
                        <input type="number" step="0.01" min="0" name="monto_estimado" id="sv-monto"
                               class="form-input" placeholder="0.00" required>
                        <p class="sv-hint">Aproximado. El monto real se captura al registrar cada pago.</p>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Periodicidad *</label>
                        <select name="periodicidad" id="sv-periodicidad" class="form-input" required>
                            @foreach($periodicidades as $valor => $config)
                                <option value="{{ $valor }}">{{ $config['etiqueta'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Próximo vencimiento *</label>
                        <input type="date" name="proximo_vencimiento" id="sv-vencimiento"
                               class="form-input" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Avisarme con *</label>
                        <div class="sv-dias">
                            <input type="number" min="1" max="30" name="dias_aviso" id="sv-dias"
                                   class="form-input" value="1" required>
                            <span>días de anticipación</span>
                        </div>
                        <p class="sv-hint">Llega por correo, notificación y al celular.</p>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Forma de pago *</label>
                        <select name="forma_pago" id="sv-forma" class="form-input" required>
                            @foreach($formasPago as $valor => $texto)
                                <option value="{{ $valor }}">{{ $texto }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Contacto del proveedor</label>
                        <input type="text" name="contacto" id="sv-contacto" class="form-input"
                               placeholder="Teléfono o correo" maxlength="150">
                    </div>

                    <div class="form-group sv-col-2">
                        <label class="form-label">Notas para quien reciba la tesorería</label>
                        <textarea name="notas" id="sv-notas" rows="3" maxlength="1000" class="form-input"
                                  placeholder="Dónde se paga, qué pedir, con quién hablar si hay problema..."></textarea>
                    </div>

                    <div class="form-group sv-col-2">
                        <label class="sv-check">
                            <input type="checkbox" name="activo" id="sv-activo" value="1" checked>
                            <span>Servicio activo (recibe avisos de vencimiento)</span>
                        </label>
                    </div>
                </div>
            </form>
        </div>
        <div class="actions">
            <button class="ui button" onclick="$('#modal-servicio').modal('hide')">Cancelar</button>
            <button class="ui primary button" id="btn-guardar-servicio">
                <i class="save icon"></i> Guardar
            </button>
        </div>
    </div>

    {{-- MODAL: registrar pago --}}
    <div class="ui small modal" id="modal-pago">
        <div class="header">Registrar pago</div>
        <div class="content">
            <div class="sv-pago-encabezado" id="pago-encabezado"></div>

            <form id="form-pago">
                @csrf
                <input type="hidden" id="pago-servicio-id">

                <div class="form-group">
                    <label class="form-label">Monto pagado *</label>
                    <input type="number" step="0.01" min="0" id="pago-monto" class="form-input"
                           placeholder="0.00" required>
                    <p class="sv-hint">El monto real del recibo, aunque difiera del estimado.</p>
                </div>

                <div class="form-group">
                    <label class="form-label">Fecha de pago *</label>
                    <input type="date" id="pago-fecha" class="form-input" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Folio o referencia del comprobante</label>
                    <input type="text" id="pago-folio" class="form-input" maxlength="100"
                           placeholder="Para rastrearlo en el estado de cuenta">
                </div>

                <div class="form-group">
                    <label class="form-label">Notas</label>
                    <textarea id="pago-notas" rows="2" maxlength="500" class="form-input"></textarea>
                </div>
            </form>
        </div>
        <div class="actions">
            <button class="ui button" onclick="$('#modal-pago').modal('hide')">Cancelar</button>
            <button class="ui green button" id="btn-guardar-pago">
                <i class="check icon"></i> Registrar pago
            </button>
        </div>
    </div>

    {{-- MODAL: historial --}}
    <div class="ui modal" id="modal-historial">
        <div class="header" id="historial-titulo">Historial de pagos</div>
        <div class="content" id="historial-contenido"></div>
        <div class="actions">
            <button class="ui button" onclick="$('#modal-historial').modal('hide')">Cerrar</button>
        </div>
    </div>

    <style>
        .page-header-right { display: flex; gap: 10px; flex-wrap: wrap; }

        .sv-aviso-rol {
            background: #f0f9ff;
            border: 1px solid #bae6fd;
            color: #075985;
            border-radius: 10px;
            padding: 10px 14px;
            margin-bottom: 16px;
            font-size: .86rem;
        }

        .sv-kpis {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(190px, 1fr));
            gap: 12px;
            margin-bottom: 18px;
        }

        .sv-kpi {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 12px 14px;
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .sv-kpi.alerta { border-color: #fcd34d; background: #fffbeb; }
        .sv-kpi.peligro { border-color: #fca5a5; background: #fef2f2; }

        .sv-kpi-label {
            font-size: .72rem;
            text-transform: uppercase;
            letter-spacing: .05em;
            color: #64748b;
            font-weight: 700;
        }

        .sv-kpi-valor {
            font-size: 1.45rem;
            font-weight: 700;
            color: #0f172a;
            font-variant-numeric: tabular-nums;
        }

        .sv-kpi-nota { font-size: .74rem; color: #94a3b8; line-height: 1.35; }

        .sv-toolbar {
            display: flex;
            gap: 10px;
            margin-bottom: 14px;
            flex-wrap: wrap;
        }

        .sv-buscador { flex: 1; min-width: 220px; }
        .sv-filtro { width: auto; min-width: 170px; }

        .sv-item {
            border: 1px solid #e2e8f0;
            border-left: 4px solid #cbd5e1;
            border-radius: 10px;
            padding: 12px 14px;
            margin-bottom: 10px;
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto;
            gap: 12px;
            align-items: center;
        }

        .sv-item.vencido { border-left-color: #dc2626; background: #fef2f2; }
        .sv-item.hoy { border-left-color: #ea580c; background: #fff7ed; }
        .sv-item.por_vencer { border-left-color: #d97706; background: #fffbeb; }
        .sv-item.al_corriente { border-left-color: #16a34a; }
        .sv-item.inactivo { border-left-color: #cbd5e1; opacity: .62; }
        .sv-item.sin_fecha { border-left-color: #94a3b8; }

        .sv-nombre { font-weight: 700; color: #0f172a; }

        .sv-meta {
            font-size: .8rem;
            color: #64748b;
            margin-top: 3px;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .sv-ref {
            font-family: ui-monospace, "Cascadia Code", Menlo, monospace;
            background: #f1f5f9;
            padding: 1px 6px;
            border-radius: 5px;
            font-size: .76rem;
        }

        .sv-badge {
            display: inline-block;
            padding: 2px 9px;
            border-radius: 999px;
            font-size: .72rem;
            font-weight: 700;
        }

        .sv-badge.vencido { background: #fee2e2; color: #991b1b; }
        .sv-badge.hoy { background: #ffedd5; color: #9a3412; }
        .sv-badge.por_vencer { background: #fef3c7; color: #92400e; }
        .sv-badge.al_corriente { background: #dcfce7; color: #166534; }
        .sv-badge.inactivo { background: #f1f5f9; color: #475569; }
        .sv-badge.sin_fecha { background: #f1f5f9; color: #475569; }

        .sv-derecha { text-align: right; display: flex; align-items: center; gap: 14px; }

        .sv-monto {
            font-weight: 700;
            font-size: 1.05rem;
            white-space: nowrap;
            font-variant-numeric: tabular-nums;
        }

        .sv-periodicidad { font-size: .74rem; color: #94a3b8; }

        .sv-vacio {
            text-align: center;
            padding: 36px 16px;
            color: #94a3b8;
        }

        .sv-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
        }

        .sv-col-2 { grid-column: 1 / -1; }

        .sv-hint { margin: 5px 0 0; color: #94a3b8; font-size: .76rem; line-height: 1.4; }

        .sv-dias { display: flex; align-items: center; gap: 8px; }
        .sv-dias input { width: 90px; }
        .sv-dias span { color: #64748b; font-size: .85rem; }

        .sv-check { display: flex; align-items: center; gap: 8px; cursor: pointer; font-size: .88rem; }

        .sv-pago-encabezado {
            background: #f8fafc;
            border-radius: 10px;
            padding: 10px 12px;
            margin-bottom: 14px;
            font-size: .85rem;
            color: #475569;
            line-height: 1.55;
        }

        .sv-tabla-hist { width: 100%; border-collapse: collapse; font-size: .85rem; }
        .sv-tabla-hist th {
            text-align: left;
            padding: 7px 9px;
            background: #f8fafc;
            font-size: .74rem;
            text-transform: uppercase;
            color: #475569;
            letter-spacing: .04em;
        }
        .sv-tabla-hist td { padding: 7px 9px; border-bottom: 1px solid #f1f5f9; }
        .sv-tabla-hist .num { text-align: right; font-variant-numeric: tabular-nums; }

        @media (max-width: 720px) {
            .sv-grid { grid-template-columns: 1fr; }
            .sv-item { grid-template-columns: 1fr; }
            .sv-derecha { justify-content: space-between; }
        }
    </style>

    <script>
        $(function () {
            const BASE_URL = "{{ url('/') }}";
            const PUEDE_EDITAR = @json($puedeEditar);

            let servicios = [];

            const fmt = n => '$' + Number(n).toLocaleString('es-MX', {
                minimumFractionDigits: 2, maximumFractionDigits: 2
            });

            const esc = t => $('<div>').text(t == null ? '' : t).html();

            /*
             * Texto del semáforo. Se arma aquí y no en el backend porque la
             * lista se vuelve a pintar al filtrar sin volver a consultar.
             */
            function etiqueta(s) {
                const d = s.dias_restantes;

                switch (s.estatus) {
                    case 'vencido':
                        return d === -1 ? 'Venció ayer' : `Venció hace ${Math.abs(d)} días`;
                    case 'hoy':
                        return 'Vence hoy';
                    case 'por_vencer':
                        return d === 1 ? 'Vence mañana' : `Vence en ${d} días`;
                    case 'al_corriente':
                        return `Vence en ${d} días`;
                    case 'inactivo':
                        return 'Inactivo';
                    default:
                        return 'Sin fecha';
                }
            }

            function pintar() {
                const texto = $('#sv-buscar').val().toLowerCase().trim();
                const filtro = $('#sv-filtro').val();

                const visibles = servicios.filter(s => {
                    if (filtro && s.estatus !== filtro) return false;
                    if (!texto) return true;

                    return [s.nombre, s.proveedor, s.referencia, s.categoria_texto]
                        .filter(Boolean)
                        .some(v => v.toLowerCase().includes(texto));
                });

                if (!visibles.length) {
                    $('#sv-lista').html(`
                        <div class="sv-vacio">
                            <i class="inbox icon" style="font-size:2rem;"></i>
                            <p>${servicios.length ? 'Ningún servicio coincide con el filtro.' : 'Todavía no hay servicios registrados.'}</p>
                        </div>`);
                    return;
                }

                $('#sv-lista').html(visibles.map(s => {
                    const ref = s.referencia
                        ? `<span class="sv-ref">${esc(s.referencia)}</span>` : '';

                    const prov = s.proveedor ? `<span>${esc(s.proveedor)}</span>` : '';

                    const hist = s.pagos_count
                        ? `<span>${s.pagos_count} pago${s.pagos_count === 1 ? '' : 's'} registrado${s.pagos_count === 1 ? '' : 's'}</span>`
                        : '<span>Sin pagos registrados</span>';

                    const acciones = PUEDE_EDITAR ? `
                        <div class="ui small icon buttons">
                            <button class="ui green button btn-pagar" data-id="${s.id}"
                                    title="Registrar pago"><i class="check icon"></i></button>
                            <button class="ui button btn-hist" data-id="${s.id}"
                                    title="Historial"><i class="history icon"></i></button>
                            <button class="ui blue button btn-editar" data-id="${s.id}"
                                    title="Editar"><i class="edit icon"></i></button>
                            <button class="ui red button btn-borrar" data-id="${s.id}"
                                    title="Eliminar"><i class="trash icon"></i></button>
                        </div>` : `
                        <div class="ui small icon buttons">
                            <button class="ui button btn-hist" data-id="${s.id}"
                                    title="Historial"><i class="history icon"></i></button>
                        </div>`;

                    return `
                        <div class="sv-item ${s.estatus}">
                            <div>
                                <div class="sv-nombre">${esc(s.nombre)}</div>
                                <div class="sv-meta">
                                    <span class="sv-badge ${s.estatus}">${etiqueta(s)}</span>
                                    <span>${esc(s.vencimiento_texto)}</span>
                                    ${prov}
                                    ${ref}
                                    <span>${esc(s.categoria_texto)}</span>
                                    ${hist}
                                </div>
                            </div>
                            <div class="sv-derecha">
                                <div>
                                    <div class="sv-monto">${fmt(s.monto_estimado)}</div>
                                    <div class="sv-periodicidad">${esc(s.periodicidad_texto)}</div>
                                </div>
                                ${acciones}
                            </div>
                        </div>`;
                }).join(''));
            }

            function cargar() {
                $.get(`${BASE_URL}/administrador/servicio/obtener-servicios`, function (res) {
                    servicios = res.data || [];
                    pintar();
                });
            }

            $('#sv-buscar').on('input', pintar);
            $('#sv-filtro').on('change', pintar);

            // --- Alta y edición ---------------------------------------------

            $('#btn-nuevo').on('click', function () {
                $('#form-servicio')[0].reset();
                $('#sv-id').val('');
                $('#sv-dias').val(1);
                $('#sv-activo').prop('checked', true);
                $('#modal-servicio-titulo').text('Nuevo servicio');
                $('#modal-servicio').modal('show');
            });

            $(document).on('click', '.btn-editar', function () {
                const s = servicios.find(x => x.id == $(this).data('id'));
                if (!s) return;

                $('#sv-id').val(s.id);
                $('#sv-nombre').val(s.nombre);
                $('#sv-proveedor').val(s.proveedor);
                $('#sv-referencia').val(s.referencia);
                $('#sv-categoria').val(s.categoria);
                $('#sv-monto').val(s.monto_estimado);
                $('#sv-periodicidad').val(s.periodicidad);
                $('#sv-vencimiento').val(s.proximo_vencimiento);
                $('#sv-dias').val(s.dias_aviso);
                $('#sv-forma').val(s.forma_pago);
                $('#sv-contacto').val(s.contacto);
                $('#sv-notas').val(s.notas);
                $('#sv-activo').prop('checked', !!s.activo);

                $('#modal-servicio-titulo').text('Editar servicio');
                $('#modal-servicio').modal('show');
            });

            $('#btn-guardar-servicio').on('click', function () {
                const form = $('#form-servicio')[0];

                if (!form.reportValidity()) return;

                const id = $('#sv-id').val();
                const url = id
                    ? `${BASE_URL}/administrador/servicio/actualizar-servicio/${id}`
                    : `${BASE_URL}/administrador/servicio/crear-servicio`;

                const datos = $('#form-servicio').serializeArray();

                // Un checkbox sin marcar no se serializa, y el backend
                // necesita el 0 explícito para poder desactivar el servicio.
                if (!$('#sv-activo').is(':checked')) {
                    datos.push({ name: 'activo', value: '0' });
                }

                $.ajax({
                    url: url,
                    method: 'POST',
                    data: $.param(datos),
                    success: function (res) {
                        $('#modal-servicio').modal('hide');
                        alertify.alert(res.header, res.message, function () {
                            location.reload();
                        });
                    },
                    error: function (xhr) {
                        const r = xhr.responseJSON || {};
                        alertify.alert(r.header || 'Error', r.message || 'No se pudo guardar.');
                    }
                });
            });

            // --- Registrar pago ---------------------------------------------

            $(document).on('click', '.btn-pagar', function () {
                const s = servicios.find(x => x.id == $(this).data('id'));
                if (!s) return;

                $('#form-pago')[0].reset();
                $('#pago-servicio-id').val(s.id);
                $('#pago-monto').val(s.monto_estimado);
                $('#pago-fecha').val(new Date().toISOString().slice(0, 10));

                const promedio = s.promedio !== null
                    ? `<br>Promedio de lo que se ha pagado: <strong>${fmt(s.promedio)}</strong>`
                    : '';

                $('#pago-encabezado').html(
                    `<strong>${esc(s.nombre)}</strong><br>` +
                    `Vencimiento que se cubre: ${esc(s.vencimiento_texto)}<br>` +
                    `Estimado: <strong>${fmt(s.monto_estimado)}</strong>${promedio}`
                );

                $('#modal-pago').modal('show');
            });

            $('#btn-guardar-pago').on('click', function () {
                if (!$('#form-pago')[0].reportValidity()) return;

                const id = $('#pago-servicio-id').val();

                $.ajax({
                    url: `${BASE_URL}/administrador/servicio/registrar-pago/${id}`,
                    method: 'POST',
                    data: {
                        _token: $('input[name=_token]').first().val(),
                        monto_pagado: $('#pago-monto').val(),
                        fecha_pago: $('#pago-fecha').val(),
                        folio: $('#pago-folio').val(),
                        notas: $('#pago-notas').val()
                    },
                    success: function (res) {
                        $('#modal-pago').modal('hide');
                        alertify.alert(res.header, res.message, function () {
                            location.reload();
                        });
                    },
                    error: function (xhr) {
                        const r = xhr.responseJSON || {};
                        alertify.alert(r.header || 'Error', r.message || 'No se pudo registrar el pago.');
                    }
                });
            });

            // --- Historial ---------------------------------------------------

            $(document).on('click', '.btn-hist', function () {
                const id = $(this).data('id');

                $.get(`${BASE_URL}/administrador/servicio/historial/${id}`, function (res) {
                    $('#historial-titulo').text('Historial: ' + res.servicio.nombre);

                    if (!res.pagos.length) {
                        $('#historial-contenido').html(
                            '<div class="sv-vacio">Todavía no se registra ningún pago de este servicio.</div>'
                        );
                    } else {
                        const filas = res.pagos.map(p => `
                            <tr>
                                <td>${esc(p.periodo_texto)}</td>
                                <td>${esc(p.fecha_pago || '—')}</td>
                                <td class="num">${fmt(p.monto_pagado)}</td>
                                <td>${esc(p.folio || '—')}</td>
                                <td>${esc(p.autor)}</td>
                            </tr>`).join('');

                        const comparativo = res.servicio.promedio !== null
                            ? `<p class="sv-hint" style="margin-bottom:10px;">
                                   Estimado registrado: <strong>${fmt(res.servicio.monto_estimado)}</strong> ·
                                   Promedio real pagado: <strong>${fmt(res.servicio.promedio)}</strong>
                               </p>` : '';

                        $('#historial-contenido').html(comparativo + `
                            <table class="sv-tabla-hist">
                                <thead>
                                    <tr>
                                        <th>Periodo</th><th>Fecha de pago</th>
                                        <th class="num">Monto</th><th>Folio</th><th>Registró</th>
                                    </tr>
                                </thead>
                                <tbody>${filas}</tbody>
                            </table>`);
                    }

                    $('#modal-historial').modal('show');
                });
            });

            // --- Eliminar -----------------------------------------------------

            $(document).on('click', '.btn-borrar', function () {
                const id = $(this).data('id');
                const s = servicios.find(x => x.id == id);

                alertify.confirm(
                    'Eliminar servicio',
                    `¿Quitar <strong>${esc(s ? s.nombre : '')}</strong> del catálogo? ` +
                    'Su historial de pagos se conserva.',
                    function () {
                        $.ajax({
                            url: `${BASE_URL}/administrador/servicio/eliminar-servicio/${id}`,
                            method: 'DELETE',
                            data: { _token: $('input[name=_token]').first().val() },
                            success: function (res) {
                                alertify.alert(res.header, res.message, function () {
                                    location.reload();
                                });
                            },
                            error: function (xhr) {
                                const r = xhr.responseJSON || {};
                                alertify.alert(r.header || 'Error', r.message || 'No se pudo eliminar.');
                            }
                        });
                    },
                    function () {}
                );
            });

            cargar();
        });
    </script>

</x-app-layout>
