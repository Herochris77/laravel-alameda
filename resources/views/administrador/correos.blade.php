<x-app-layout>
    <div class="page-header">
        <div class="page-header-left">
            <div class="page-icon" style="background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);">
                <i class="mail icon"></i>
            </div>
            <div>
                <h1 class="page-title">Correos Enviados</h1>
                <p class="page-subtitle">Historial de todos los correos enviados por el sistema</p>
            </div>
        </div>
    </div>

    {{--
        Consumo de la cuota del proveedor de correo. Lo que se cuenta son
        MENSAJES, no destinatarios: un aviso con 44 copias ocultas es un solo
        mensaje para el proveedor, y esa es la diferencia entre caber en el
        plan básico y quedarse sin correo a media quincena.
    --}}
    <div class="cq-kpis">
        <div class="cq-kpi">
            <span class="cq-label">Mensajes hoy</span>
            <span class="cq-valor">{{ $consumo['hoy'] }}</span>
            <span class="cq-nota">
                @if($consumo['destinatarios_hoy'] > 0)
                    llegaron a {{ $consumo['destinatarios_hoy'] }} destinatario(s)
                @else
                    Sin envíos todavía
                @endif
            </span>
        </div>

        <div class="cq-kpi">
            <span class="cq-label">Este mes</span>
            <span class="cq-valor">{{ $consumo['mes'] }}</span>
            <span class="cq-nota">Mensajes salidos desde el día 1</span>
        </div>

        <div class="cq-kpi">
            <span class="cq-label">Día más alto (14 días)</span>
            <span class="cq-valor">{{ $consumo['pico'] }}</span>
            <span class="cq-nota">Tu peor día reciente</span>
        </div>

        <div class="cq-kpi {{ $consumo['errores_hoy'] > 0 ? 'mal' : '' }}">
            <span class="cq-label">Fallidos hoy</span>
            <span class="cq-valor">{{ $consumo['errores_hoy'] }}</span>
            <span class="cq-nota">
                @if($consumo['errores_hoy'] > 0)
                    Puede ser la cuota del proveedor
                @else
                    Todo salió bien
                @endif
            </span>
        </div>
    </div>

    @if(count($consumo['serie']) > 1)
        <div class="card cq-barras-card">
            <div class="card-body">
                <div class="cq-barras-titulo">Mensajes por día (últimos 14 días)</div>
                <div class="cq-barras">
                    @foreach($consumo['serie'] as $d)
                        <div class="cq-col" title="{{ $d['dia'] }}: {{ $d['total'] }} mensaje(s)">
                            <div class="cq-barra" style="height: {{ max(4, $consumo['pico'] ? round($d['total'] / $consumo['pico'] * 100) : 4) }}%;"></div>
                            <span class="cq-dia">{{ $d['dia'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <style>
        .cq-kpis {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 12px;
            margin-bottom: 16px;
        }

        .cq-kpi {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 12px 14px;
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .cq-kpi.mal { border-color: #fca5a5; background: #fef2f2; }

        .cq-label {
            font-size: .71rem;
            text-transform: uppercase;
            letter-spacing: .05em;
            color: #64748b;
            font-weight: 700;
        }

        .cq-valor { font-size: 1.5rem; font-weight: 700; color: #0f172a; }

        .cq-nota { font-size: .73rem; color: #94a3b8; line-height: 1.35; }

        .cq-barras-card { margin-bottom: 16px; }

        .cq-barras-titulo {
            font-size: .78rem;
            text-transform: uppercase;
            letter-spacing: .04em;
            color: #475569;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .cq-barras {
            display: flex;
            align-items: flex-end;
            gap: 6px;
            height: 90px;
        }

        .cq-col {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-end;
            height: 100%;
            gap: 4px;
        }

        .cq-barra {
            width: 100%;
            max-width: 26px;
            background: linear-gradient(180deg, #8b5cf6, #7c3aed);
            border-radius: 5px 5px 0 0;
            min-height: 4px;
        }

        .cq-dia { font-size: .64rem; color: #94a3b8; white-space: nowrap; }
    </style>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="history icon"></i> Historial de Correos</h3>
            <div class="card-actions">
                <button class="ui red button btn-eliminar-seleccionados" disabled>
                    <i class="trash icon"></i> Eliminar seleccionados
                </button>
            </div>
        </div>
        <div class="card-body">
            <table id="tabla-correos" class="ui unstackable table" style="margin: 0;">
                <thead>
                    <tr>
                        <th class="center aligned">
                            <div class="ui checkbox">
                                <input type="checkbox" id="checkbox-todos">
                                <label></label>
                            </div>
                        </th>
                        <th>Destinatarios</th>
                        <th>Asunto</th>
                        <th>Origen</th>
                        <th>Estado</th>
                        <th>Fecha</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</x-app-layout>

<div class="ui modal" id="modal-ver-correo">
    <div class="header" style="background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%); color: white;">
        <i class="mail icon"></i> Contenido del Correo
    </div>
    <div class="content" style="padding: 0;">
        <div class="ui segment" style="border: none; box-shadow: none; margin: 0;">
            <table class="ui definition table" style="margin-bottom: 16px;">
                <tr>
                    <td style="font-weight: 600; width: 140px;">Destinatarios</td>
                    <td id="modal-destinatarios"></td>
                </tr>
                <tr>
                    <td style="font-weight: 600;">Asunto</td>
                    <td id="modal-asunto"></td>
                </tr>
                <tr>
                    <td style="font-weight: 600;">Origen</td>
                    <td id="modal-origen"></td>
                </tr>
                <tr>
                    <td style="font-weight: 600;">Estado</td>
                    <td id="modal-estado"></td>
                </tr>
                <tr>
                    <td style="font-weight: 600;">Enviado</td>
                    <td id="modal-fecha"></td>
                </tr>
            </table>
            <div style="border-top: 1px solid rgba(148,163,184,0.22); padding-top: 16px;">
                <div id="modal-mensaje"></div>
            </div>
        </div>
    </div>
    <div class="actions">
        <button class="ui button" onclick="$('#modal-ver-correo').modal('hide');">Cerrar</button>
    </div>
</div>

<script>
    const BASE_URL = "{{ url('/') }}";

    const tablaCorreos = $('#tabla-correos').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        ajax: "{{ route('admin.correos.listar') }}",
        columns: [
            { data: 'checkbox', name: 'checkbox', className: 'center aligned', orderable: false },
            { data: 'destinatarios', name: 'destinatarios' },
            { data: 'asunto', name: 'asunto' },
            { data: 'origen', name: 'origen' },
            { data: 'estado', name: 'estado', orderable: false },
            { data: 'created_at', name: 'created_at' },
            { data: 'acciones', name: 'acciones', className: 'center aligned', orderable: false }
        ],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
        },
        order: [[5, 'desc']],
        drawCallback: function() {
            $('.ui.checkbox').checkbox();
        }
    });

    $(document).on('click', '.btn-ver', function() {
        const id = $(this).data('id');

        mostrarLoaderPantalla();
        $.ajax({
            url: `${BASE_URL}/administrador/correos/ver/${id}`,
            method: 'GET',
            success: function(res) {
                if (res.success) {
                    $('#modal-destinatarios').text(res.destinatarios);
                    $('#modal-asunto').text(res.asunto);
                    $('#modal-origen').text(res.origen || '—');
                    $('#modal-estado').html(res.estado == 'enviado'
                        ? '<div class="ui green horizontal label">Enviado</div>'
                        : '<div class="ui red horizontal label">Error</div>');
                    $('#modal-fecha').text(res.created_at);
                    $('#modal-mensaje').html(res.mensaje);
                    $('#modal-ver-correo').modal('show');
                } else {
                    alertify.error(res.message);
                }
            },
            error: () => alertify.error('Error al obtener el correo'),
            complete: () => ocultarLoaderPantalla()
        });
    });

    $(document).on('click', '.btn-eliminar', function() {
        const id = $(this).data('id');

        alertify.confirm('¿Eliminar registro?', '¿Estás seguro de que deseas eliminar este registro de correo?',
            function() {
                mostrarLoaderPantalla();
                setTimeout(() => {
                    $.ajax({
                        url: `${BASE_URL}/administrador/correos/eliminar/${id}`,
                        method: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(res) {
                            alertify.alert(res.header, res.message, () => {
                                tablaCorreos.ajax.reload();
                            });
                        },
                        error: () => alertify.error('Error al eliminar'),
                        complete: () => ocultarLoaderPantalla()
                    });
                }, 100);
            },
            function() {
                alertify.message('Cancelado');
            }
        );
    });

    $('#checkbox-todos').on('change', function() {
        const checked = $(this).is(':checked');
        $('.checkbox-correo').prop('checked', checked);
        actualizarBtnEliminarSeleccionados();
    });

    $(document).on('change', '.checkbox-correo', function() {
        const total = $('.checkbox-correo').length;
        const seleccionados = $('.checkbox-correo:checked').length;
        $('#checkbox-todos').prop('checked', total > 0 && total === seleccionados);
        actualizarBtnEliminarSeleccionados();
    });

    function actualizarBtnEliminarSeleccionados() {
        const seleccionados = $('.checkbox-correo:checked').length;
        const btn = $('.btn-eliminar-seleccionados');
        btn.prop('disabled', seleccionados === 0);
        if (seleccionados > 0) {
            btn.html(`<i class="trash icon"></i> Eliminar (${seleccionados})`);
        } else {
            btn.html(`<i class="trash icon"></i> Eliminar seleccionados`);
        }
    }

    $('.btn-eliminar-seleccionados').on('click', function() {
        const ids = $('.checkbox-correo:checked').map(function() {
            return $(this).val();
        }).get();

        if (ids.length === 0) return;

        alertify.confirm(
            'Eliminar seleccionados',
            `¿Estás seguro de eliminar ${ids.length} registro(s) de correo?`,
            function() {
                mostrarLoaderPantalla();
                setTimeout(() => {
                    $.ajax({
                        url: `${BASE_URL}/administrador/correos/eliminar-seleccionados`,
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            ids: ids
                        },
                        success: function(res) {
                            alertify.alert(res.header, res.message, () => {
                                tablaCorreos.ajax.reload();
                            });
                        },
                        error: () => alertify.error('Error al eliminar'),
                        complete: () => ocultarLoaderPantalla()
                    });
                }, 100);
            },
            function() {
                alertify.message('Cancelado');
            }
        );
    });
</script>
