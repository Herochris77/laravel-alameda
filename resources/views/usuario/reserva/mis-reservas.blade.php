<x-app-layout>
    <div class="reservas-page">
        <div class="page-header reservas-header">
            <div class="page-header-left">
                <div class="page-icon reservas-page-icon">
                    <i class="calendar icon"></i>
                </div>
                <div>
                    <h1 class="page-title">Reservaciones</h1>
                    <p class="page-subtitle">Consulta, filtra y administra tus reservaciones</p>
                </div>
            </div>

            <div class="page-header-actions reservas-actions">
                <div class="filter-wrapper">
                    <label class="filter-label">Filtrar por fecha</label>
                    <select class="ui fluid dropdown filtro-fecha-dropdown" id="filtro-fecha" onchange="aplicarFiltro()">
                        <option value="todos" {{ request('filtro') == 'todos' || !request('filtro') ? 'selected' : '' }}>Todas</option>
                        <option value="hoy" {{ request('filtro') == 'hoy' ? 'selected' : '' }}>Hoy</option>
                        <option value="proximos" {{ request('filtro') == 'proximos' ? 'selected' : '' }}>Próximos días</option>
                        <option value="pasados" {{ request('filtro') == 'pasados' ? 'selected' : '' }}>Pasados</option>
                    </select>
                </div>

                <a href="{{ route('usuario.reserva.index') }}" class="btn btn-primary nueva-reserva-btn">
                    <i class="plus icon"></i>
                    Nueva Reserva
                </a>
            </div>
        </div>

        <div class="reservas-summary-card">
            <div class="summary-item">
                <div class="summary-icon">
                    <i class="calendar check icon"></i>
                </div>
                <div>
                    <span class="summary-label">Reservaciones encontradas</span>
                    <strong class="summary-value">{{ $reservas->count() }}</strong>
                </div>
            </div>

            <div class="summary-helper">
                <i class="info circle icon"></i>
                <span>Solo puedes editar o eliminar tus reservas actuales o futuras.</span>
            </div>
        </div>

        @if($reservas->isEmpty())
            <div class="card empty-reservas-card">
                <div class="empty-state reservas-empty-state">
                    <div class="empty-state-icon">
                        <i class="calendar outline icon"></i>
                    </div>
                    <h3>No hay reservaciones</h3>
                    <p>No se encontraron reservaciones con el filtro seleccionado.</p>

                    <div class="empty-actions">
                        <a href="{{ route('usuario.reserva.index') }}" class="btn btn-primary">
                            <i class="plus icon"></i>
                            Crear primera reserva
                        </a>

                        @if(request('filtro'))
                            <a href="{{ route('usuario.reserva.misReservas') }}" class="btn btn-secondary">
                                <i class="undo icon"></i>
                                Ver todas
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        @else
            <div class="reservas-grid">
                @foreach($reservas as $reserva)
                    @php
                        $fechaReserva = \Carbon\Carbon::parse($reserva->fecha_reserva);
                        $esEditable = Auth::user()->id === $reserva->user_id && ($fechaReserva->isToday() || $fechaReserva->isFuture());
                        $esMia = Auth::user()->id === $reserva->user_id;

                        if ($fechaReserva->isToday()) {
                            $estadoClase = 'hoy';
                            $estadoTexto = 'Hoy';
                            $estadoIcono = 'clock';
                        } elseif ($fechaReserva->isFuture()) {
                            $estadoClase = 'proxima';
                            $estadoTexto = 'Próxima';
                            $estadoIcono = 'calendar plus';
                        } else {
                            $estadoClase = 'pasada';
                            $estadoTexto = 'Pasada';
                            $estadoIcono = 'history';
                        }
                    @endphp

                    <div class="reserva-card {{ $estadoClase }}">
                        <div class="reserva-card-header">
                            <div class="reserva-icon">
                                <i class="calendar icon"></i>
                            </div>

                            <div class="reserva-title-wrap">
                                <h3 class="reserva-title">{{ $reserva->espacio }}</h3>

                                <div class="reserva-badges">
                                    <span class="reserva-status {{ $estadoClase }}">
                                        <i class="{{ $estadoIcono }} icon"></i>
                                        {{ $estadoTexto }}
                                    </span>

                                    @if($esMia)
                                        <span class="reserva-owner">
                                            <i class="user check icon"></i>
                                            Mi reserva
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="reserva-card-body">
                            <div class="reserva-user-box">
                                <div class="reserva-detail">
                                    <i class="user icon"></i>
                                    <span>{{ $reserva->user->nombre ?? 'Usuario desconocido' }}</span>
                                </div>

                                <div class="reserva-detail">
                                    <i class="home icon"></i>
                                    <span>Casa {{ $reserva->user->casa ?? 'N/A' }}</span>
                                </div>
                            </div>

                            <div class="reserva-date-box">
                                <div class="date-main">
                                    <i class="calendar alternate outline icon"></i>
                                    <span>{{ $fechaReserva->translatedFormat('j \d\e F \d\e Y') }}</span>
                                </div>

                                <div class="time-main">
                                    <i class="clock outline icon"></i>
                                    <span>{{ $reserva->hora_inicio }} - {{ $reserva->hora_fin }} hrs</span>
                                </div>
                            </div>
                        </div>

                        <div class="reserva-card-footer">
                            @if($esEditable)
                                <a href="{{ route('usuario.reserva.editar', $reserva->id) }}" class="btn btn-secondary btn-sm reserva-action-btn">
                                    <i class="edit icon"></i>
                                    Editar
                                </a>

                                <button type="button" class="btn btn-danger btn-sm reserva-delete-btn" onclick="eliminarReserva({{ $reserva->id }})">
                                    <i class="trash icon"></i>
                                    <span>Eliminar</span>
                                </button>
                            @else
                                <div class="reserva-readonly-message">
                                    <i class="lock icon"></i>
                                    <span>
                                        @if($esMia)
                                            Esta reserva ya no se puede modificar.
                                        @else
                                            Reservación de otro usuario.
                                        @endif
                                    </span>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</x-app-layout>

<style>
    :root {
        --primary: #667eea;
        --primary-dark: #4f46e5;
        --secondary: #764ba2;
        --danger: #ef4444;
        --danger-dark: #dc2626;
        --success: #10b981;
        --warning: #f59e0b;
        --text-main: #0f172a;
        --text-muted: #64748b;
        --border: #e2e8f0;
        --surface: #ffffff;
        --soft-bg: #f8fafc;
    }

    .reservas-page {
        width: 100%;
    }

    .reservas-header {
        gap: 20px;
    }

    .reservas-page-icon {
        background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
    }

    .reservas-actions {
        display: flex;
        align-items: flex-end;
        gap: 12px;
        flex-wrap: wrap;
    }

    .filter-wrapper {
        min-width: 190px;
    }

    .filter-label {
        display: block;
        font-size: 0.78rem;
        font-weight: 700;
        color: var(--text-muted);
        margin-bottom: 6px;
    }

    .filtro-fecha-dropdown {
        min-height: 42px;
        border-radius: 12px !important;
    }

    .nueva-reserva-btn {
        min-height: 42px;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        white-space: nowrap;
    }

    .reservas-summary-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 20px;
        padding: 18px 20px;
        margin-bottom: 22px;
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.06);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
    }

    .summary-item {
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .summary-icon {
        width: 48px;
        height: 48px;
        border-radius: 16px;
        background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        box-shadow: 0 8px 18px rgba(102, 126, 234, 0.25);
    }

    .summary-icon i {
        margin: 0;
        font-size: 1.2rem;
    }

    .summary-label {
        display: block;
        color: var(--text-muted);
        font-size: 0.85rem;
        margin-bottom: 2px;
    }

    .summary-value {
        color: var(--text-main);
        font-size: 1.6rem;
        line-height: 1;
    }

    .summary-helper {
        display: flex;
        align-items: center;
        gap: 8px;
        color: var(--text-muted);
        font-size: 0.9rem;
        background: var(--soft-bg);
        border-radius: 999px;
        padding: 10px 14px;
    }

    .summary-helper i {
        color: var(--primary);
    }

    .reservas-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 20px;
    }

    .reserva-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 22px;
        overflow: hidden;
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.06);
        display: flex;
        flex-direction: column;
        min-height: 100%;
        transition: transform 0.25s ease, box-shadow 0.25s ease, border-color 0.25s ease;
    }

    .reserva-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 16px 34px rgba(15, 23, 42, 0.12);
        border-color: rgba(102, 126, 234, 0.35);
    }

    .reserva-card-header {
        padding: 18px;
        display: flex;
        align-items: flex-start;
        gap: 14px;
        border-bottom: 1px solid #f1f5f9;
    }

    .reserva-icon {
        width: 54px;
        height: 54px;
        border-radius: 16px;
        background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        box-shadow: 0 8px 18px rgba(102, 126, 234, 0.25);
    }

    .reserva-icon i {
        margin: 0;
        font-size: 1.25rem;
    }

    .reserva-title-wrap {
        min-width: 0;
        flex: 1;
    }

    .reserva-title {
        margin: 0;
        font-size: 1.12rem;
        color: var(--text-main);
        font-weight: 800;
        line-height: 1.25;
        overflow-wrap: anywhere;
    }

    .reserva-badges {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 8px;
        margin-top: 10px;
    }

    .reserva-status,
    .reserva-owner {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 5px 10px;
        border-radius: 999px;
        font-size: 0.72rem;
        font-weight: 800;
        line-height: 1;
    }

    .reserva-status i,
    .reserva-owner i {
        margin: 0;
    }

    .reserva-status.hoy {
        background: #fef3c7;
        color: #92400e;
    }

    .reserva-status.proxima {
        background: #dcfce7;
        color: #166534;
    }

    .reserva-status.pasada {
        background: #e2e8f0;
        color: #475569;
    }

    .reserva-owner {
        background: #dbeafe;
        color: #1d4ed8;
    }

    .reserva-card-body {
        padding: 18px;
        flex: 1;
    }

    .reserva-user-box {
        display: grid;
        gap: 8px;
        margin-bottom: 14px;
    }

    .reserva-detail {
        display: flex;
        align-items: center;
        gap: 9px;
        color: var(--text-muted);
        font-size: 0.92rem;
        min-width: 0;
    }

    .reserva-detail i {
        color: var(--primary);
        flex-shrink: 0;
        margin: 0;
    }

    .reserva-detail span {
        overflow-wrap: anywhere;
    }

    .reserva-date-box {
        background: var(--soft-bg);
        border: 1px solid #eef2f7;
        border-radius: 16px;
        padding: 14px;
        display: grid;
        gap: 10px;
    }

    .date-main,
    .time-main {
        display: flex;
        align-items: center;
        gap: 10px;
        color: var(--text-main);
        font-weight: 800;
        font-size: 0.95rem;
    }

    .date-main i,
    .time-main i {
        color: var(--primary);
        margin: 0;
        flex-shrink: 0;
    }

    .reserva-card-footer {
        padding: 16px 18px 18px;
        border-top: 1px solid #f1f5f9;
        display: grid;
        grid-template-columns: 1fr auto;
        gap: 10px;
    }

    .reserva-action-btn,
    .reserva-delete-btn {
        min-height: 42px;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        white-space: nowrap;
    }

    .reserva-delete-btn {
        padding-left: 14px;
        padding-right: 14px;
    }

    .reserva-readonly-message {
        grid-column: 1 / -1;
        min-height: 42px;
        border-radius: 12px;
        background: var(--soft-bg);
        color: var(--text-muted);
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 10px 12px;
        font-size: 0.9rem;
        text-align: center;
    }

    .reserva-readonly-message i {
        margin: 0;
    }

    .empty-reservas-card {
        border-radius: 22px;
        border: 1px solid var(--border);
    }

    .reservas-empty-state {
        padding: 48px 20px;
    }

    .empty-actions {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        flex-wrap: wrap;
        margin-top: 18px;
    }

    .empty-actions .btn {
        min-height: 42px;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    @media (max-width: 1200px) {
        .reservas-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 900px) {
        .reservas-header {
            align-items: flex-start;
        }

        .reservas-actions {
            width: 100%;
            display: grid;
            grid-template-columns: 1fr auto;
            align-items: end;
        }

        .filter-wrapper {
            min-width: 0;
        }

        .reservas-summary-card {
            align-items: flex-start;
            flex-direction: column;
        }

        .summary-helper {
            border-radius: 14px;
            width: 100%;
            align-items: flex-start;
        }
    }

    @media (max-width: 640px) {
        .page-header-left {
            align-items: flex-start;
        }

        .page-title {
            font-size: 1.4rem;
            line-height: 1.2;
        }

        .page-subtitle {
            font-size: 0.9rem;
        }

        .reservas-actions {
            grid-template-columns: 1fr;
        }

        .nueva-reserva-btn {
            width: 100%;
        }

        .reservas-summary-card {
            border-radius: 18px;
            padding: 16px;
            margin-bottom: 18px;
        }

        .summary-icon {
            width: 44px;
            height: 44px;
            border-radius: 14px;
        }

        .summary-value {
            font-size: 1.35rem;
        }

        .reservas-grid {
            grid-template-columns: 1fr;
            gap: 16px;
        }

        .reserva-card {
            border-radius: 18px;
        }

        .reserva-card-header {
            padding: 16px;
        }

        .reserva-icon {
            width: 48px;
            height: 48px;
            border-radius: 14px;
        }

        .reserva-title {
            font-size: 1.05rem;
        }

        .reserva-card-body {
            padding: 16px;
        }

        .reserva-card-footer {
            padding: 14px 16px 16px;
            grid-template-columns: 1fr;
        }

        .reserva-action-btn,
        .reserva-delete-btn {
            width: 100%;
        }

        .reserva-delete-btn span {
            display: inline;
        }

        .empty-actions {
            flex-direction: column;
        }

        .empty-actions .btn {
            width: 100%;
        }
    }

    @media (hover: none) {
        .reserva-card:hover {
            transform: none;
        }
    }
</style>

<script>
    function aplicarFiltro() {
        var filtro = document.getElementById('filtro-fecha').value;
        var url = '{{ route("usuario.reserva.misReservas") }}';

        if (filtro === 'todos') {
            window.location.href = url;
        } else {
            window.location.href = url + '?filtro=' + filtro;
        }
    }

    function eliminarReserva(id) {
        alertify.confirm(
            'Eliminar Reserva',
            '¿Está seguro de que desea eliminar esta reserva?',
            function() {
                mostrarLoaderPantalla();

                setTimeout(() => {
                    $.ajax({
                        url: "{{ route('usuario.reserva.eliminar', ['id' => ':id']) }}".replace(':id', id),
                        type: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            if (response.success) {
                                alertify.success(response.message);

                                setTimeout(function() {
                                    location.reload();
                                }, 1200);
                            } else {
                                alertify.error(response.message || 'No se pudo eliminar la reserva.');
                            }
                        },
                        error: function() {
                            alertify.error('Ocurrió un error al eliminar la reserva.');
                        },
                        complete: function() {
                            ocultarLoaderPantalla();
                        }
                    });
                }, 100);
            },
            function() {
                alertify.message('Eliminación cancelada');
            }
        ).set('labels', {
            ok: 'Sí, eliminar',
            cancel: 'Cancelar'
        });
    }

    $(document).ready(function() {
        $('#filtro-fecha').dropdown();
    });
</script>