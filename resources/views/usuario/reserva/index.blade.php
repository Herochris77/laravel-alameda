<x-app-layout>
    <div class="reserva-page">
        <div class="page-header reserva-header">
            <div class="page-header-left">
                <div class="page-icon reserva-page-icon">
                    <i class="calendar icon"></i>
                </div>
                <div>
                    <h1 class="page-title">Reservar Espacio</h1>
                    <p class="page-subtitle">Seleccione el espacio, fecha y horario para su reserva</p>
                </div>
            </div>
        </div>

        <div class="reserva-layout">
            <div class="reserva-form-card">
                <div class="reserva-card-header">
                    <div class="reserva-card-title-wrap">
                        <div class="reserva-card-icon">
                            <i class="calendar plus icon"></i>
                        </div>
                        <div>
                            <h3 class="reserva-card-title">Nueva Reserva</h3>
                            <p class="reserva-card-subtitle">Completa los datos para validar disponibilidad</p>
                        </div>
                    </div>
                </div>

                <div class="reserva-card-body">
                    <form id="reserva-form" method="POST" action="{{ route('usuario.reserva.guardar') }}">
                        @csrf

                        <div class="form-group">
                            <label class="form-label">Espacio a reservar</label>
                            <select name="espacio" id="espacio-select" class="ui fluid dropdown reserva-input" onchange="cargarReservas()">
                                <option value="Palapa">Palapa</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Fecha de reserva</label>
                            <div class="date-input-wrapper">
                                <i class="calendar icon"></i>
                                <input
                                    type="date"
                                    name="fecha_reserva"
                                    id="fecha-reserva"
                                    class="form-input date-input reserva-input"
                                    required
                                    min=""
                                >
                            </div>
                        </div>

                        <div class="time-grid">
                            <div class="form-group">
                                <label class="form-label">Hora de inicio</label>
                                <div class="time-input-wrapper">
                                    <i class="clock outline icon"></i>
                                    <input
                                        type="time"
                                        name="hora_inicio"
                                        id="hora-inicio"
                                        class="form-input time-input reserva-input"
                                        required
                                        onchange="validarHorario()"
                                    >
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Hora de fin</label>
                                <div class="time-input-wrapper">
                                    <i class="clock icon"></i>
                                    <input
                                        type="time"
                                        name="hora_fin"
                                        id="hora-fin"
                                        class="form-input time-input reserva-input"
                                        required
                                        onchange="validarHorario()"
                                    >
                                </div>
                            </div>
                        </div>

                        <div id="horario-mensaje" class="reserva-alert" style="display: none;"></div>

                        <div class="reserva-helper">
                            <i class="info circle icon"></i>
                            <span>El sistema revisará automáticamente si el horario seleccionado está disponible.</span>
                        </div>

                        <button class="btn btn-primary btn-lg reservar-btn" type="submit" id="btn-reservar">
                            <i class="calendar check icon"></i>
                            Reservar
                        </button>
                    </form>
                </div>
            </div>

            <div class="reservas-dia-card">
                <div class="reserva-card-header">
                    <div class="reserva-card-title-wrap">
                        <div class="reserva-card-icon secondary">
                            <i class="list calendar icon"></i>
                        </div>
                        <div>
                            <h3 class="reserva-card-title">Reservaciones del día</h3>
                            <p class="reserva-card-subtitle" id="reservas-dia-subtitle">Consulta los horarios ocupados</p>
                        </div>
                    </div>
                </div>

                <div class="reserva-card-body">
                    <div id="reservas-del-dia">
                        <div class="reserva-info-state">
                            <div class="reserva-state-icon info">
                                <i class="info circle icon"></i>
                            </div>
                            <h3>Seleccione una fecha</h3>
                            <p>Elige una fecha para ver las reservaciones existentes.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<style>
    :root {
        --primary: #667eea;
        --primary-dark: #4f46e5;
        --secondary: #764ba2;
        --success: #10b981;
        --success-dark: #059669;
        --danger: #ef4444;
        --danger-dark: #dc2626;
        --warning: #f59e0b;
        --text-main: #0f172a;
        --text-muted: #64748b;
        --border: #e2e8f0;
        --surface: #ffffff;
        --soft-bg: #f8fafc;
    }

    .reserva-page {
        width: 100%;
    }

    .reserva-header {
        margin-bottom: 22px;
    }

    .reserva-page-icon {
        background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
    }

    .reserva-layout {
        display: grid;
        grid-template-columns: minmax(0, 1fr) minmax(360px, 0.9fr);
        gap: 24px;
        align-items: start;
    }

    .reserva-form-card,
    .reservas-dia-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 22px;
        overflow: hidden;
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.06);
    }

    .reservas-dia-card {
        position: sticky;
        top: 18px;
    }

    .reserva-card-header {
        padding: 18px 20px;
        border-bottom: 1px solid #f1f5f9;
        background: linear-gradient(180deg, #ffffff 0%, #fbfdff 100%);
    }

    .reserva-card-title-wrap {
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .reserva-card-icon {
        width: 52px;
        height: 52px;
        border-radius: 16px;
        background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        box-shadow: 0 8px 18px rgba(102, 126, 234, 0.24);
    }

    .reserva-card-icon.secondary {
        background: linear-gradient(135deg, #0ea5e9 0%, #2563eb 100%);
    }

    .reserva-card-icon i {
        margin: 0;
        font-size: 1.25rem;
    }

    .reserva-card-title {
        margin: 0;
        font-size: 1.12rem;
        font-weight: 800;
        color: var(--text-main);
        line-height: 1.25;
    }

    .reserva-card-subtitle {
        margin: 4px 0 0 0;
        color: var(--text-muted);
        font-size: 0.9rem;
        line-height: 1.35;
    }

    .reserva-card-body {
        padding: 20px;
    }

    .reserva-input {
        min-height: 46px;
        border-radius: 14px !important;
    }

    .date-input-wrapper,
    .time-input-wrapper {
        position: relative;
        display: flex;
        align-items: center;
        width: 100%;
    }

    .date-input-wrapper .calendar.icon,
    .time-input-wrapper i {
        position: absolute;
        left: 14px;
        color: var(--primary);
        font-size: 1.05rem;
        pointer-events: none;
        z-index: 1;
        margin: 0;
    }

    .date-input,
    .time-input {
        padding-left: 42px !important;
        cursor: pointer;
    }

    input[type="date"],
    input[type="time"] {
        -webkit-appearance: none;
        appearance: none;
        font-size: 15px;
    }

    input[type="date"]::-webkit-calendar-picker-indicator,
    input[type="time"]::-webkit-calendar-picker-indicator {
        cursor: pointer;
        opacity: 0.65;
        padding: 4px;
        margin-right: 4px;
        border-radius: 6px;
    }

    input[type="date"]::-webkit-calendar-picker-indicator:hover,
    input[type="time"]::-webkit-calendar-picker-indicator:hover {
        background: #f0f4ff;
        opacity: 1;
    }

    .time-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 18px;
    }

    .reserva-alert {
        margin-top: 4px;
        margin-bottom: 14px;
        padding: 12px 14px;
        border-radius: 14px;
        display: flex;
        align-items: flex-start;
        gap: 10px;
        font-size: 0.92rem;
        line-height: 1.45;
        font-weight: 600;
    }

    .reserva-alert i {
        margin: 0;
        margin-top: 2px;
        flex-shrink: 0;
    }

    .reserva-alert.alert-success {
        background: #dcfce7;
        color: #166534;
        border: 1px solid #bbf7d0;
    }

    .reserva-alert.alert-warning {
        background: #fef3c7;
        color: #92400e;
        border: 1px solid #fde68a;
    }

    .reserva-alert.alert-error {
        background: #fee2e2;
        color: #991b1b;
        border: 1px solid #fecaca;
    }

    .reserva-helper {
        display: flex;
        align-items: flex-start;
        gap: 9px;
        background: var(--soft-bg);
        border: 1px solid #eef2f7;
        color: var(--text-muted);
        border-radius: 14px;
        padding: 12px 14px;
        font-size: 0.9rem;
        line-height: 1.45;
        margin-bottom: 16px;
    }

    .reserva-helper i {
        color: var(--primary);
        margin: 0;
        margin-top: 2px;
        flex-shrink: 0;
    }

    .reservar-btn {
        width: 100%;
        min-height: 48px;
        border-radius: 14px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        margin-top: 4px;
    }

    .reserva-info-state,
    .reserva-disponible {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        min-height: 240px;
        padding: 32px 20px;
        text-align: center;
    }

    .reserva-info-state h3,
    .reserva-disponible h3 {
        margin: 0 0 8px 0;
        color: var(--text-main);
        font-size: 1.1rem;
        font-weight: 800;
    }

    .reserva-info-state p,
    .reserva-disponible p {
        color: var(--text-muted);
        margin: 0;
        line-height: 1.5;
    }

    .reserva-state-icon,
    .reserva-disponible-icon {
        width: 78px;
        height: 78px;
        border-radius: 22px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 16px;
        box-shadow: 0 12px 24px rgba(15, 23, 42, 0.10);
    }

    .reserva-state-icon.info {
        background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
        color: #1d4ed8;
    }

    .reserva-state-icon.error {
        background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
        color: var(--danger-dark);
    }

    .reserva-disponible-icon {
        background: linear-gradient(135deg, var(--success) 0%, var(--success-dark) 100%);
        color: #ffffff;
    }

    .reserva-state-icon i,
    .reserva-disponible-icon i {
        margin: 0;
        font-size: 2rem;
    }

    .reservas-list {
        display: grid;
        gap: 12px;
    }

    .reservas-list-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 14px;
        padding: 12px 14px;
        background: var(--soft-bg);
        border: 1px solid #eef2f7;
        border-radius: 14px;
    }

    .reservas-list-title {
        display: flex;
        align-items: center;
        gap: 8px;
        color: var(--text-main);
        font-weight: 800;
        font-size: 0.95rem;
    }

    .reservas-list-title i {
        color: var(--primary);
        margin: 0;
    }

    .reservas-count {
        font-size: 0.78rem;
        font-weight: 800;
        color: #1d4ed8;
        background: #dbeafe;
        padding: 5px 10px;
        border-radius: 999px;
        white-space: nowrap;
    }

    .reserva-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 14px;
        border: 1px solid var(--border);
        border-radius: 16px;
        background: #ffffff;
        transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
    }

    .reserva-item:hover {
        transform: translateY(-2px);
        border-color: rgba(102, 126, 234, 0.35);
        box-shadow: 0 8px 18px rgba(102, 126, 234, 0.12);
    }

    .reserva-icon {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .reserva-icon.mio {
        background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        color: #ffffff;
        box-shadow: 0 8px 18px rgba(102, 126, 234, 0.22);
    }

    .reserva-icon.otro {
        background: var(--soft-bg);
        color: var(--text-muted);
        border: 1px solid #eef2f7;
    }

    .reserva-icon i {
        margin: 0;
        font-size: 1.15rem;
    }

    .reserva-info {
        flex: 1;
        min-width: 0;
    }

    .reserva-hora {
        font-weight: 800;
        color: var(--text-main);
        font-size: 0.96rem;
        line-height: 1.25;
    }

    .reserva-usuario {
        font-size: 0.86rem;
        color: var(--text-muted);
        margin-top: 4px;
        overflow-wrap: anywhere;
    }

    .reserva-badge-mine {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        margin-top: 8px;
        padding: 5px 9px;
        background: #dbeafe;
        color: #1d4ed8;
        border-radius: 999px;
        font-size: 0.72rem;
        font-weight: 800;
    }

    .reserva-badge-mine i {
        margin: 0;
    }

    @media (max-width: 1100px) {
        .reserva-layout {
            grid-template-columns: 1fr;
        }

        .reservas-dia-card {
            position: static;
        }
    }

    @media (max-width: 768px) {
        .reserva-header {
            align-items: flex-start;
        }

        .page-header-left {
            align-items: flex-start;
        }

        .page-title {
            font-size: 1.45rem;
            line-height: 1.2;
        }

        .page-subtitle {
            font-size: 0.9rem;
        }

        .reserva-layout {
            gap: 18px;
        }

        .reserva-form-card,
        .reservas-dia-card {
            border-radius: 18px;
        }

        .reserva-card-header {
            padding: 16px;
        }

        .reserva-card-body {
            padding: 16px;
        }

        .reserva-card-icon {
            width: 46px;
            height: 46px;
            border-radius: 14px;
        }

        .reserva-card-title {
            font-size: 1.04rem;
        }

        .reserva-card-subtitle {
            font-size: 0.85rem;
        }

        .time-grid {
            grid-template-columns: 1fr;
            gap: 12px;
        }

        input[type="date"],
        input[type="time"],
        .reserva-input {
            font-size: 16px;
        }

        .reserva-info-state,
        .reserva-disponible {
            min-height: 210px;
            padding: 28px 16px;
        }

        .reserva-state-icon,
        .reserva-disponible-icon {
            width: 68px;
            height: 68px;
            border-radius: 20px;
        }
    }

    @media (max-width: 480px) {
        .reserva-card-title-wrap {
            align-items: flex-start;
        }

        .reservas-list-header {
            align-items: flex-start;
            flex-direction: column;
        }

        .reserva-item {
            align-items: flex-start;
        }

        .reserva-icon {
            width: 44px;
            height: 44px;
            border-radius: 13px;
        }
    }

    @media (hover: none) {
        .reserva-item:hover {
            transform: none;
        }
    }
</style>

<script>
    var reservasDelDia = [];

    var today = new Date();
    var fechaInput = document.getElementById('fecha-reserva');

    var year = today.getFullYear();
    var month = String(today.getMonth() + 1).padStart(2, '0');
    var day = String(today.getDate()).padStart(2, '0');
    var todayStr = year + '-' + month + '-' + day;

    fechaInput.min = todayStr;
    fechaInput.value = todayStr;

    fechaInput.addEventListener('change', function() {
        cargarReservas();
    });

    function cargarReservas() {
        var espacio = $('#espacio-select').val();
        var fecha = $('#fecha-reserva').val();

        if (!fecha) return;

        $('#reservas-dia-subtitle').text('Cargando reservaciones...');
        $('#reservas-del-dia').html(`
            <div class="reserva-info-state">
                <div class="reserva-state-icon info">
                    <i class="spinner loading icon"></i>
                </div>
                <h3>Cargando reservaciones</h3>
                <p>Estamos consultando la disponibilidad del día seleccionado.</p>
            </div>
        `);

        $.ajax({
            url: '{{ route("usuario.reserva.obtenerReservas") }}',
            method: 'GET',
            data: {
                espacio: espacio,
                fecha: fecha
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    reservasDelDia = response.reservas || [];
                    mostrarReservas();
                    validarHorario();
                } else {
                    mostrarErrorReservas(response.message || 'No se pudieron cargar las reservaciones.');
                }
            },
            error: function() {
                mostrarErrorReservas('Error al cargar las reservaciones.');
                alertify.error('Error al cargar las reservaciones');
            }
        });
    }

    function mostrarReservas() {
        var container = $('#reservas-del-dia');
        var fechaSeleccionada = $('#fecha-reserva').val();

        $('#reservas-dia-subtitle').text(formatearFecha(fechaSeleccionada));

        if (reservasDelDia.length === 0) {
            container.html(`
                <div class="reserva-disponible">
                    <div class="reserva-disponible-icon">
                        <i class="check icon"></i>
                    </div>
                    <h3 style="color: #10b981;">¡Día disponible!</h3>
                    <p>No hay reservaciones registradas para esta fecha.</p>
                </div>
            `);
            return;
        }

        var html = `
            <div class="reservas-list-header">
                <div class="reservas-list-title">
                    <i class="calendar day icon"></i>
                    Horarios ocupados
                </div>
                <div class="reservas-count">
                    ${reservasDelDia.length} reservación${reservasDelDia.length === 1 ? '' : 'es'}
                </div>
            </div>

            <div class="reservas-list">
        `;

        reservasDelDia.forEach(function(reserva) {
            var isMine = reserva.es_propia;
            var usuarioTexto = isMine
                ? 'Tu reservación'
                : escapeHtml(reserva.nombre_usuario || 'Usuario') + ' - Casa ' + escapeHtml(reserva.casa_usuario || 'N/A');

            html += `
                <div class="reserva-item">
                    <div class="reserva-icon ${isMine ? 'mio' : 'otro'}">
                        <i class="${isMine ? 'user check' : 'user circle'} icon"></i>
                    </div>

                    <div class="reserva-info">
                        <div class="reserva-hora">
                            ${escapeHtml(reserva.hora_inicio || '')} - ${escapeHtml(reserva.hora_fin || '')} hrs
                        </div>
                        <div class="reserva-usuario">${usuarioTexto}</div>

                        ${isMine ? `
                            <span class="reserva-badge-mine">
                                <i class="check icon"></i>
                                Mi reservación
                            </span>
                        ` : ''}
                    </div>
                </div>
            `;
        });

        html += `</div>`;
        container.html(html);
    }

    function mostrarErrorReservas(mensaje) {
        $('#reservas-dia-subtitle').text('No se pudo cargar la información');

        $('#reservas-del-dia').html(`
            <div class="reserva-info-state">
                <div class="reserva-state-icon error">
                    <i class="warning sign icon"></i>
                </div>
                <h3>Error al cargar</h3>
                <p>${escapeHtml(mensaje)}</p>
            </div>
        `);
    }

    function validarHorario() {
        var horaInicio = $('#hora-inicio').val();
        var horaFin = $('#hora-fin').val();
        var mensajeDiv = $('#horario-mensaje');
        var btnReservar = $('#btn-reservar');

        mensajeDiv.css('display', 'none');

        if (!horaInicio || !horaFin) {
            btnReservar.removeClass('disabled');
            return;
        }

        if (horaInicio >= horaFin) {
            mensajeDiv.css('display', 'flex');
            mensajeDiv.removeClass('alert-success alert-error').addClass('alert-warning');
            mensajeDiv.html('<i class="warning icon"></i> <span>La hora de inicio debe ser anterior a la hora de fin.</span>');
            btnReservar.addClass('disabled');
            return;
        }

        var conflicto = false;
        var mensajeConflicto = '';

        for (var i = 0; i < reservasDelDia.length; i++) {
            var r = reservasDelDia[i];

            if (horaInicio < r.hora_fin && horaFin > r.hora_inicio) {
                conflicto = true;

                var tipoReserva = r.es_propia
                    ? 'tu reservación'
                    : 'reservación de ' + (r.nombre_usuario || 'Usuario') + ' - Casa ' + (r.casa_usuario || 'N/A');

                mensajeConflicto = 'El horario está ocupado con ' + tipoReserva + ' (' + r.hora_inicio + ' - ' + r.hora_fin + ') hrs';
                break;
            }
        }

        if (conflicto) {
            mensajeDiv.css('display', 'flex');
            mensajeDiv.removeClass('alert-success alert-warning').addClass('alert-error');
            mensajeDiv.html('<i class="ban icon"></i> <span>' + escapeHtml(mensajeConflicto) + '</span>');
            btnReservar.addClass('disabled');
        } else {
            mensajeDiv.css('display', 'flex');
            mensajeDiv.removeClass('alert-warning alert-error').addClass('alert-success');
            mensajeDiv.html('<i class="check circle icon"></i> <span>Horario disponible para reservar.</span>');
            btnReservar.removeClass('disabled');
        }
    }

    function formatearFecha(fecha) {
        if (!fecha) return 'Consulta los horarios ocupados';

        var partes = fecha.split('-');
        if (partes.length !== 3) return fecha;

        var date = new Date(partes[0], partes[1] - 1, partes[2]);

        return date.toLocaleDateString('es-MX', {
            weekday: 'long',
            day: 'numeric',
            month: 'long',
            year: 'numeric'
        });
    }

    function escapeHtml(text) {
        return String(text)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    $(document).ready(function() {
        $('#espacio-select').dropdown();
        cargarReservas();
    });

    $('#reserva-form').on('submit', function(e) {
        e.preventDefault();

        var form = $(this);
        var button = $('#btn-reservar');

        if (button.hasClass('disabled')) {
            alertify.error('Revisa el horario antes de continuar.');
            return;
        }

        button.addClass('loading disabled');

        var data = form.serialize();

        $.ajax({
            url: form.attr('action'),
            method: 'POST',
            data: data,
            dataType: 'json',
            beforeSend: function(xhr) {
                var token = $('meta[name="csrf-token"]').attr('content');

                if (token) {
                    xhr.setRequestHeader('X-CSRF-TOKEN', token);
                }
            },
            success: function(response) {
                if (response.success) {
                    alertify.success(response.message);

                    form[0].reset();

                    var resetToday = new Date();
                    var resetYear = resetToday.getFullYear();
                    var resetMonth = String(resetToday.getMonth() + 1).padStart(2, '0');
                    var resetDay = String(resetToday.getDate()).padStart(2, '0');
                    var resetDateStr = resetYear + '-' + resetMonth + '-' + resetDay;

                    $('#fecha-reserva').val(resetDateStr);
                    $('#espacio-select').dropdown('set selected', 'Palapa');
                    $('#horario-mensaje').css('display', 'none');

                    cargarReservas();
                } else {
                    alertify.error(response.message || 'No se pudo crear la reserva.');
                }
            },
            error: function(xhr) {
                var message = 'Error al procesar la solicitud.';

                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }

                alertify.error(message);
            },
            complete: function() {
                button.removeClass('loading disabled');
            }
        });
    });
</script>