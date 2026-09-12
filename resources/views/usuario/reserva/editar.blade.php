<x-app-layout>
    <div class="editar-reserva-page">
        <div class="page-header editar-reserva-header">
            <div class="page-header-left">
                <div class="page-icon editar-reserva-page-icon">
                    <i class="calendar icon"></i>
                </div>
                <div>
                    <h1 class="page-title">Editar Reserva</h1>
                    <p class="page-subtitle">Modifique los detalles de su reserva</p>
                </div>
            </div>

            <div class="page-header-actions editar-reserva-actions">
                <a href="{{ route('usuario.reserva.misReservas') }}" class="btn btn-secondary volver-btn">
                    <i class="arrow left icon"></i>
                    Volver
                </a>
            </div>
        </div>

        <div class="editar-reserva-layout">
            <div class="editar-reserva-card">
                <div class="editar-reserva-card-header">
                    <div class="editar-reserva-card-title-wrap">
                        <div class="editar-reserva-card-icon">
                            <i class="edit icon"></i>
                        </div>
                        <div>
                            <h3 class="editar-reserva-card-title">Datos de la reserva</h3>
                            <p class="editar-reserva-card-subtitle">
                                Actualiza el espacio, fecha y horario de tu reservación.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="editar-reserva-card-body">
                    <form id="editar-reserva-form" method="POST" action="{{ route('usuario.reserva.actualizar', $reserva->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label class="form-label">Espacio a reservar</label>
                            <div class="input-icon-wrapper">
                                <i class="map marker alternate icon"></i>
                                <input
                                    type="text"
                                    name="espacio"
                                    class="form-input reserva-input input-with-icon"
                                    value="{{ old('espacio', $reserva->espacio) }}"
                                    required
                                    autocomplete="off"
                                >
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Fecha de reserva</label>
                            <div class="input-icon-wrapper">
                                <i class="calendar icon"></i>
                                <input
                                    type="date"
                                    name="fecha_reserva"
                                    id="fecha-reserva"
                                    class="form-input reserva-input input-with-icon"
                                    value="{{ old('fecha_reserva', \Carbon\Carbon::parse($reserva->fecha_reserva)->format('Y-m-d')) }}"
                                    required
                                >
                            </div>
                        </div>

                        <div class="time-grid">
                            <div class="form-group">
                                <label class="form-label">Hora de inicio</label>
                                <div class="input-icon-wrapper">
                                    <i class="clock outline icon"></i>
                                    <input
                                        type="time"
                                        name="hora_inicio"
                                        id="hora-inicio"
                                        class="form-input reserva-input input-with-icon"
                                        value="{{ old('hora_inicio', $reserva->hora_inicio) }}"
                                        required
                                        onchange="validarHorarioEdicion()"
                                    >
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Hora de fin</label>
                                <div class="input-icon-wrapper">
                                    <i class="clock icon"></i>
                                    <input
                                        type="time"
                                        name="hora_fin"
                                        id="hora-fin"
                                        class="form-input reserva-input input-with-icon"
                                        value="{{ old('hora_fin', $reserva->hora_fin) }}"
                                        required
                                        onchange="validarHorarioEdicion()"
                                    >
                                </div>
                            </div>
                        </div>

                        <div id="horario-mensaje" class="editar-reserva-alert" style="display: none;"></div>

                        <div class="editar-reserva-helper">
                            <i class="info circle icon"></i>
                            <span>
                                La fecha no puede ser anterior a hoy y la hora de inicio debe ser menor que la hora de fin.
                            </span>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary btn-lg guardar-btn" id="btn-guardar">
                                <i class="calendar check icon"></i>
                                Actualizar Reserva
                            </button>

                            <a href="{{ route('usuario.reserva.misReservas') }}" class="btn btn-secondary btn-lg cancelar-btn">
                                <i class="times icon"></i>
                                Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="reserva-preview-card">
                <div class="reserva-preview-icon">
                    <i class="calendar alternate outline icon"></i>
                </div>

                <h3>Resumen de edición</h3>

                <div class="preview-list">
                    <div class="preview-item">
                        <span>Espacio actual</span>
                        <strong>{{ $reserva->espacio }}</strong>
                    </div>

                    <div class="preview-item">
                        <span>Fecha actual</span>
                        <strong>{{ \Carbon\Carbon::parse($reserva->fecha_reserva)->translatedFormat('j \d\e F \d\e Y') }}</strong>
                    </div>

                    <div class="preview-item">
                        <span>Horario actual</span>
                        <strong>{{ $reserva->hora_inicio }} - {{ $reserva->hora_fin }} hrs</strong>
                    </div>
                </div>

                <div class="preview-note">
                    <i class="shield alternate icon"></i>
                    <span>Revisa bien la información antes de guardar los cambios.</span>
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
        --danger: #ef4444;
        --warning: #f59e0b;
        --text-main: #0f172a;
        --text-muted: #64748b;
        --border: #e2e8f0;
        --surface: #ffffff;
        --soft-bg: #f8fafc;
    }

    .editar-reserva-page {
        width: 100%;
    }

    .editar-reserva-header {
        gap: 20px;
        margin-bottom: 22px;
    }

    .editar-reserva-page-icon {
        background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
    }

    .editar-reserva-actions {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .volver-btn {
        min-height: 42px;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        white-space: nowrap;
    }

    .editar-reserva-layout {
        display: grid;
        grid-template-columns: minmax(0, 620px) minmax(280px, 380px);
        gap: 24px;
        align-items: start;
    }

    .editar-reserva-card,
    .reserva-preview-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 22px;
        overflow: hidden;
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.06);
    }

    .editar-reserva-card {
        width: 100%;
    }

    .editar-reserva-card-header {
        padding: 18px 20px;
        border-bottom: 1px solid #f1f5f9;
        background: linear-gradient(180deg, #ffffff 0%, #fbfdff 100%);
    }

    .editar-reserva-card-title-wrap {
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .editar-reserva-card-icon {
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

    .editar-reserva-card-icon i {
        margin: 0;
        font-size: 1.25rem;
    }

    .editar-reserva-card-title {
        margin: 0;
        font-size: 1.12rem;
        font-weight: 800;
        color: var(--text-main);
        line-height: 1.25;
    }

    .editar-reserva-card-subtitle {
        margin: 4px 0 0 0;
        color: var(--text-muted);
        font-size: 0.9rem;
        line-height: 1.35;
    }

    .editar-reserva-card-body {
        padding: 20px;
    }

    .reserva-input {
        min-height: 46px;
        border-radius: 14px !important;
    }

    .input-icon-wrapper {
        position: relative;
        display: flex;
        align-items: center;
        width: 100%;
    }

    .input-icon-wrapper > i {
        position: absolute;
        left: 14px;
        color: var(--primary);
        font-size: 1.05rem;
        pointer-events: none;
        z-index: 1;
        margin: 0;
    }

    .input-with-icon {
        padding-left: 42px !important;
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

    .editar-reserva-alert {
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

    .editar-reserva-alert i {
        margin: 0;
        margin-top: 2px;
        flex-shrink: 0;
    }

    .editar-reserva-alert.alert-success {
        background: #dcfce7;
        color: #166534;
        border: 1px solid #bbf7d0;
    }

    .editar-reserva-alert.alert-warning {
        background: #fef3c7;
        color: #92400e;
        border: 1px solid #fde68a;
    }

    .editar-reserva-alert.alert-error {
        background: #fee2e2;
        color: #991b1b;
        border: 1px solid #fecaca;
    }

    .editar-reserva-helper {
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
        margin-bottom: 18px;
    }

    .editar-reserva-helper i {
        color: var(--primary);
        margin: 0;
        margin-top: 2px;
        flex-shrink: 0;
    }

    .form-actions {
        display: grid;
        grid-template-columns: 1fr auto;
        gap: 12px;
        margin-top: 24px;
    }

    .guardar-btn,
    .cancelar-btn {
        min-height: 48px;
        border-radius: 14px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        white-space: nowrap;
    }

    .reserva-preview-card {
        padding: 22px;
        position: sticky;
        top: 18px;
    }

    .reserva-preview-icon {
        width: 64px;
        height: 64px;
        border-radius: 18px;
        background: linear-gradient(135deg, #0ea5e9 0%, #2563eb 100%);
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 8px 18px rgba(37, 99, 235, 0.22);
        margin-bottom: 16px;
    }

    .reserva-preview-icon i {
        margin: 0;
        font-size: 1.6rem;
    }

    .reserva-preview-card h3 {
        margin: 0 0 16px 0;
        color: var(--text-main);
        font-weight: 800;
        font-size: 1.1rem;
    }

    .preview-list {
        display: grid;
        gap: 10px;
        margin-bottom: 16px;
    }

    .preview-item {
        background: var(--soft-bg);
        border: 1px solid #eef2f7;
        border-radius: 14px;
        padding: 12px 14px;
    }

    .preview-item span {
        display: block;
        color: var(--text-muted);
        font-size: 0.78rem;
        font-weight: 700;
        margin-bottom: 4px;
    }

    .preview-item strong {
        display: block;
        color: var(--text-main);
        font-size: 0.95rem;
        overflow-wrap: anywhere;
    }

    .preview-note {
        display: flex;
        align-items: flex-start;
        gap: 9px;
        color: var(--text-muted);
        font-size: 0.88rem;
        line-height: 1.45;
        background: #fef3c7;
        border: 1px solid #fde68a;
        border-radius: 14px;
        padding: 12px 14px;
    }

    .preview-note i {
        color: #92400e;
        margin: 0;
        margin-top: 2px;
        flex-shrink: 0;
    }

    @media (max-width: 1100px) {
        .editar-reserva-layout {
            grid-template-columns: 1fr;
        }

        .reserva-preview-card {
            position: static;
        }
    }

    @media (max-width: 768px) {
        .editar-reserva-header {
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

        .editar-reserva-actions {
            width: 100%;
        }

        .volver-btn {
            width: 100%;
        }

        .editar-reserva-card,
        .reserva-preview-card {
            border-radius: 18px;
        }

        .editar-reserva-card-header,
        .editar-reserva-card-body {
            padding: 16px;
        }

        .editar-reserva-card-icon {
            width: 46px;
            height: 46px;
            border-radius: 14px;
        }

        .editar-reserva-card-title {
            font-size: 1.04rem;
        }

        .editar-reserva-card-subtitle {
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

        .form-actions {
            grid-template-columns: 1fr;
        }

        .guardar-btn,
        .cancelar-btn {
            width: 100%;
        }

        .reserva-preview-card {
            padding: 18px;
        }
    }

    @media (max-width: 480px) {
        .editar-reserva-card-title-wrap {
            align-items: flex-start;
        }

        .reserva-preview-icon {
            width: 56px;
            height: 56px;
            border-radius: 16px;
        }
    }
</style>

<script>
    var today = new Date();
    var year = today.getFullYear();
    var month = String(today.getMonth() + 1).padStart(2, '0');
    var day = String(today.getDate()).padStart(2, '0');
    var todayStr = year + '-' + month + '-' + day;

    var fechaInput = document.getElementById('fecha-reserva');

    if (fechaInput) {
        fechaInput.min = todayStr;
    }

    function validarHorarioEdicion() {
        var horaInicio = $('#hora-inicio').val();
        var horaFin = $('#hora-fin').val();
        var mensajeDiv = $('#horario-mensaje');
        var button = $('#btn-guardar');

        mensajeDiv.css('display', 'none');

        if (!horaInicio || !horaFin) {
            button.removeClass('disabled');
            return true;
        }

        if (horaInicio >= horaFin) {
            mensajeDiv.css('display', 'flex');
            mensajeDiv.removeClass('alert-success alert-error').addClass('alert-warning');
            mensajeDiv.html('<i class="warning icon"></i> <span>La hora de inicio debe ser anterior a la hora de fin.</span>');
            button.addClass('disabled');
            return false;
        }

        mensajeDiv.css('display', 'flex');
        mensajeDiv.removeClass('alert-warning alert-error').addClass('alert-success');
        mensajeDiv.html('<i class="check circle icon"></i> <span>Horario válido para actualizar.</span>');
        button.removeClass('disabled');

        return true;
    }

    $('#editar-reserva-form').on('submit', function(e) {
        e.preventDefault();

        var form = $(this);
        var button = $('#btn-guardar');

        if (!validarHorarioEdicion()) {
            alertify.warning('La hora de inicio debe ser anterior a la hora de fin.');
            return false;
        }

        button.addClass('loading disabled');

        var data = form.serialize();

        $.ajax({
            url: form.attr('action'),
            method: 'PUT',
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

                    setTimeout(function() {
                        window.location.href = "{{ route('usuario.reserva.misReservas') }}";
                    }, 1200);
                } else {
                    alertify.error(response.message || 'No se pudo actualizar la reserva.');
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

    $(document).ready(function() {
        validarHorarioEdicion();
    });
</script>