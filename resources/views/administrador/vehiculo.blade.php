<x-app-layout>
    <div class="page-header">
        <div class="page-header-left">
            <div class="page-icon" style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);">
                <i class="car icon"></i>
            </div>
            <div>
                <h1 class="page-title">Gestión de Vehículos</h1>
                <p class="page-subtitle">Consulta todos los vehículos registrados en el condominio</p>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="list icon"></i> Vehículos Registrados</h3>
        </div>
        <div class="card-body">
            <table id="tabla-vehiculos" class="ui unstackable table" style="margin: 0;">
                <thead>
                    <tr>
                        <th>Usuario</th>
                        <th>Casa</th>
                        <th>Marca</th>
                        <th>Modelo</th>
                        <th>Año</th>
                        <th>Color</th>
                        <th>Placas</th>
                        <th>Tipo</th>
                        <th>Foto</th>
                        <th>Fecha</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</x-app-layout>

<div class="ui modal" id="modal-foto">
    <div class="header" style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); color: white;">
        <i class="car icon"></i> Foto del Vehículo
    </div>
    <div class="content" style="text-align: center; padding: 20px;">
        <img id="vehiculo-foto" src="" alt="Foto del vehículo" style="max-width: 100%; max-height: 70vh; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.15);">
    </div>
</div>

<script>
    const BASE_URL = "{{ url('/') }}";

    const tablaVehiculos = $('#tabla-vehiculos').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        ajax: "{{ route('admin.vehiculo.obtener') }}",
        columns: [
            { data: 'usuario', name: 'usuario' },
            { data: 'casa', name: 'casa' },
            { data: 'marca', name: 'marca' },
            { data: 'modelo', name: 'modelo' },
            { data: 'anio', name: 'anio' },
            { data: 'color', name: 'color' },
            { data: 'placas', name: 'placas' },
            { data: 'tipo_badge', name: 'tipo', orderable: false },
            { data: 'foto_preview', name: 'foto', orderable: false },
            { data: 'created_at', name: 'created_at' },
            { data: 'acciones', name: 'acciones', className: 'center aligned', orderable: false }
        ],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
        },
        order: [[9, 'desc']]
    });

    $(document).on('click', '.btn-ver-foto', function() {
        const img = $(this).data('img');
        $('#vehiculo-foto').attr('src', img);
        $('#modal-foto').modal('show');
    });

    $(document).on('click', '.btn-eliminar', function() {
        const id = $(this).data('id');

        alertify.confirm('¿Eliminar vehículo?', '¿Estás seguro de que deseas eliminar este vehículo?',
            function() {
                mostrarLoaderPantalla();
                setTimeout(() => {
                    $.ajax({
                        url: `${BASE_URL}/administrador/vehiculo/eliminar/${id}`,
                        method: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(res) {
                            alertify.alert(res.header, res.message, () => {
                                tablaVehiculos.ajax.reload();
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
