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
