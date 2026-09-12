<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Laravel Log Viewer</title>

    <style>
        :root {
            --bg: #f4f6f8;
            --card: #ffffff;
            --text: #111827;
            --muted: #6b7280;
            --border: #e5e7eb;
            --dark: #111827;
            --primary: #2563eb;
            --primary-hover: #1d4ed8;
            --danger: #dc2626;
            --danger-hover: #b91c1c;
            --success-bg: #d1fae5;
            --success-text: #065f46;
            --shadow: 0 8px 24px rgba(15, 23, 42, .08);
            --radius: 14px;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
            background: var(--bg);
            color: var(--text);
        }

        body.modal-open {
            overflow: hidden;
        }

        .page {
            width: 100%;
            max-width: 1500px;
            margin: 0 auto;
            padding: 24px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 16px;
            margin-bottom: 20px;
        }

        .header-title h1 {
            margin: 0;
            font-size: 28px;
            line-height: 1.2;
        }

        .header-title p {
            margin: 6px 0 0;
            color: var(--muted);
            font-size: 14px;
        }

        .file-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: var(--dark);
            color: white;
            padding: 10px 14px;
            border-radius: 999px;
            font-size: 13px;
            white-space: nowrap;
        }

        .alert-success {
            background: var(--success-bg);
            color: var(--success-text);
            padding: 14px 16px;
            border-radius: var(--radius);
            margin-bottom: 16px;
            border: 1px solid #a7f3d0;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 14px;
            margin-bottom: 16px;
        }

        .stat-card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 16px;
            box-shadow: var(--shadow);
        }

        .stat-label {
            color: var(--muted);
            font-size: 13px;
            margin-bottom: 8px;
        }

        .stat-value {
            font-size: 24px;
            font-weight: 700;
        }

        .stat-value.error {
            color: var(--danger);
        }

        .stat-value.info {
            color: var(--primary);
        }

        .filters-card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 16px;
            box-shadow: var(--shadow);
            margin-bottom: 16px;
        }

        .filters-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            margin-bottom: 14px;
        }

        .filters-header h2 {
            margin: 0;
            font-size: 16px;
        }

        .filters-header small {
            color: var(--muted);
        }

        .filters-layout {
            display: grid;
            grid-template-columns: 220px 1fr 180px auto auto;
            gap: 12px;
            align-items: end;
        }

        .field {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        label {
            font-size: 13px;
            color: #374151;
            font-weight: 600;
        }

        select,
        input,
        button,
        .button {
            width: 100%;
            padding: 11px 12px;
            border-radius: 10px;
            border: 1px solid #d1d5db;
            background: #fff;
            color: var(--text);
            text-decoration: none;
            font-size: 14px;
            line-height: 1.2;
        }

        input:focus,
        select:focus {
            outline: 2px solid rgba(37, 99, 235, .25);
            border-color: var(--primary);
        }

        button,
        .button {
            cursor: pointer;
            text-align: center;
            font-weight: 700;
            border: none;
        }

        .button-primary {
            background: var(--primary);
            color: #fff;
        }

        .button-primary:hover {
            background: var(--primary-hover);
        }

        .button-secondary {
            background: #f3f4f6;
            color: var(--text);
            border: 1px solid var(--border);
        }

        .button-danger {
            background: var(--danger);
            color: #fff;
        }

        .button-danger:hover {
            background: var(--danger-hover);
        }

        .danger-zone {
            margin-top: 14px;
            padding-top: 14px;
            border-top: 1px dashed var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 14px;
        }

        .danger-zone-text strong {
            display: block;
            font-size: 14px;
        }

        .danger-zone-text span {
            display: block;
            color: var(--muted);
            font-size: 13px;
            margin-top: 3px;
        }

        .danger-zone form {
            width: 190px;
            flex-shrink: 0;
        }

        .active-filters {
            margin-top: 12px;
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .filter-chip {
            background: #eff6ff;
            color: #1e40af;
            border: 1px solid #bfdbfe;
            padding: 7px 10px;
            border-radius: 999px;
            font-size: 13px;
        }

        .desktop-table {
            display: block;
        }

        .mobile-cards {
            display: none;
        }

        .table-card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            overflow: hidden;
            box-shadow: var(--shadow);
        }

        .table-scroll {
            overflow: auto;
            max-height: 72vh;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 1100px;
        }

        th {
            position: sticky;
            top: 0;
            z-index: 2;
            background: var(--dark);
            color: #fff;
            text-align: left;
            padding: 13px 14px;
            font-size: 13px;
            white-space: nowrap;
        }

        td {
            padding: 13px 14px;
            border-bottom: 1px solid var(--border);
            vertical-align: top;
            font-size: 13px;
        }

        tbody tr:hover {
            background: #f9fafb;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 74px;
            padding: 5px 9px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 800;
            letter-spacing: .02em;
        }

        .badge-ERROR,
        .badge-CRITICAL,
        .badge-ALERT,
        .badge-EMERGENCY {
            background: #fee2e2;
            color: #991b1b;
        }

        .badge-WARNING {
            background: #fef3c7;
            color: #92400e;
        }

        .badge-INFO {
            background: #dbeafe;
            color: #1e40af;
        }

        .badge-DEBUG {
            background: #e5e7eb;
            color: #374151;
        }

        .message-preview {
            max-width: 620px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            color: #1f2937;
        }

        details {
            margin-top: 8px;
        }

        details summary {
            cursor: pointer;
            color: var(--primary);
            font-weight: 700;
            user-select: none;
        }

        .message-detail {
            white-space: pre-wrap;
            word-break: break-word;
            font-family: Consolas, Monaco, monospace;
            background: #0f172a;
            color: #e5e7eb;
            padding: 14px;
            border-radius: 12px;
            margin-top: 10px;
            line-height: 1.5;
            font-size: 12px;
            max-height: 420px;
            overflow: auto;
        }

        .number {
            text-align: center;
            font-weight: 800;
        }

        .number.ok {
            color: var(--success-text);
        }

        .number.error {
            color: var(--danger);
        }

        .empty {
            background: var(--card);
            border: 1px solid var(--border);
            padding: 42px 20px;
            border-radius: var(--radius);
            text-align: center;
            color: var(--muted);
            box-shadow: var(--shadow);
        }

        .empty strong {
            display: block;
            color: var(--text);
            font-size: 18px;
            margin-bottom: 6px;
        }

        .log-card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 14px;
            box-shadow: var(--shadow);
            margin-bottom: 12px;
        }

        .log-card-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 10px;
            margin-bottom: 12px;
        }

        .log-card-date {
            font-weight: 800;
            font-size: 14px;
        }

        .log-card-origin {
            color: var(--muted);
            font-size: 12px;
            margin-top: 3px;
        }

        .log-card-message {
            color: #1f2937;
            font-size: 14px;
            line-height: 1.45;
            margin-bottom: 10px;
            display: -webkit-box;
            -webkit-line-clamp: 4;
            -webkit-box-orient: vertical;
            overflow: hidden;
            word-break: break-word;
        }

        .cron-metrics {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 8px;
            margin: 12px 0;
        }

        .metric {
            background: #f9fafb;
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 10px;
        }

        .metric span {
            display: block;
            color: var(--muted);
            font-size: 12px;
            margin-bottom: 4px;
        }

        .metric strong {
            font-size: 18px;
        }

        .mobile-detail-button {
            margin-top: 10px;
        }

        .log-modal {
            display: none;
            position: fixed;
            inset: 0;
            z-index: 9999;
        }

        .log-modal.is-open {
            display: block;
        }

        .log-modal-backdrop {
            position: absolute;
            inset: 0;
            background: rgba(15, 23, 42, .65);
        }

        .log-modal-panel {
            position: absolute;
            inset: 16px;
            background: #ffffff;
            border-radius: 18px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            box-shadow: 0 20px 60px rgba(0,0,0,.35);
        }

        .log-modal-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 12px;
            padding: 16px;
            border-bottom: 1px solid #e5e7eb;
        }

        .log-modal-header strong {
            display: block;
            font-size: 16px;
            color: #111827;
        }

        .log-modal-header span {
            display: block;
            margin-top: 3px;
            color: #6b7280;
            font-size: 12px;
        }

        .log-modal-close {
            width: 38px;
            height: 38px;
            border-radius: 999px;
            border: none;
            background: #f3f4f6;
            color: #111827;
            font-size: 26px;
            line-height: 1;
            cursor: pointer;
        }

        .log-modal-actions {
            padding: 12px 16px;
            border-bottom: 1px solid #e5e7eb;
            background: #f9fafb;
            display: flex;
            gap: 10px;
        }

        .log-modal-content {
            margin: 0;
            padding: 16px;
            flex: 1;
            overflow: auto;
            background: #0f172a;
            color: #e5e7eb;
            font-family: Consolas, Monaco, monospace;
            font-size: 12px;
            line-height: 1.5;
            white-space: pre-wrap;
            word-break: break-word;
        }

        @media (max-width: 1100px) {
            .filters-layout {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .stats-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 768px) {
            .page {
                padding: 14px;
            }

            .header {
                flex-direction: column;
            }

            .header-title h1 {
                font-size: 23px;
            }

            .file-pill {
                width: 100%;
                justify-content: center;
                border-radius: 12px;
            }

            .filters-layout {
                grid-template-columns: 1fr;
            }

            .filters-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .danger-zone {
                flex-direction: column;
                align-items: stretch;
            }

            .danger-zone form {
                width: 100%;
            }

            .desktop-table {
                display: none;
            }

            .mobile-cards {
                display: block;
            }

            .stats-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 10px;
            }

            .stat-card {
                padding: 13px;
            }

            .stat-value {
                font-size: 21px;
            }

            .log-modal-panel {
                inset: 0;
                border-radius: 0;
            }

            .log-modal-content {
                font-size: 11px;
            }
        }

        @media (max-width: 420px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }

            .cron-metrics {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>

@php
    $totalEntries = $entries->count();

    $errorCount = $entries->filter(function ($entry) {
        return in_array(strtoupper((string) $entry['level']), ['ERROR', 'CRITICAL', 'ALERT', 'EMERGENCY']);
    })->count();

    $infoCount = $entries->filter(function ($entry) {
        return strtoupper((string) $entry['level']) === 'INFO';
    })->count();

    $warningCount = $entries->filter(function ($entry) {
        return strtoupper((string) $entry['level']) === 'WARNING';
    })->count();
@endphp

<div class="page">

    <div class="header">
        <div class="header-title">
            <h1>Laravel Log Viewer</h1>
            <p>Consulta, filtra y administra los logs de tu aplicación.</p>
        </div>

        <div class="file-pill">
            Archivo actual: <strong>{{ $selected }}</strong>
        </div>
    </div>

    @if(session('success'))
        <div class="alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-label">Registros mostrados</div>
            <div class="stat-value">{{ $totalEntries }}</div>
        </div>

        <div class="stat-card">
            <div class="stat-label">Errores</div>
            <div class="stat-value error">{{ $errorCount }}</div>
        </div>

        <div class="stat-card">
            <div class="stat-label">Warnings</div>
            <div class="stat-value">{{ $warningCount }}</div>
        </div>

        <div class="stat-card">
            <div class="stat-label">Infos</div>
            <div class="stat-value info">{{ $infoCount }}</div>
        </div>
    </div>

    <div class="filters-card">
        <div class="filters-header">
            <div>
                <h2>Filtros</h2>
                <small>Busca por fecha, mensaje, nivel, JSON o resultados del cron.</small>
            </div>
        </div>

        <form method="GET" action="{{ route('admin.logs') }}">
            <div class="filters-layout">

                <div class="field">
                    <label for="file">Archivo</label>
                    <select name="file" id="file">
                        @foreach($logs as $name => $path)
                            <option value="{{ $name }}" @selected($selected === $name)>
                                {{ $name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="field">
                    <label for="search">Buscar texto</label>
                    <input
                        type="text"
                        name="search"
                        id="search"
                        value="{{ $search }}"
                        placeholder="Ej: error, 2026-05-06, reservaciones, pagos..."
                    >
                </div>

                <div class="field">
                    <label for="level">Nivel</label>
                    <select name="level" id="level">
                        <option value="">Todos</option>

                        @foreach(['ERROR', 'WARNING', 'INFO', 'DEBUG', 'CRITICAL', 'ALERT', 'EMERGENCY'] as $logLevel)
                            <option value="{{ $logLevel }}" @selected(strtoupper((string) $level) === $logLevel)>
                                {{ $logLevel }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="field">
                    <label>&nbsp;</label>
                    <button type="submit" class="button-primary">
                        Filtrar
                    </button>
                </div>

                <div class="field">
                    <label>&nbsp;</label>
                    <a href="{{ route('admin.logs', ['file' => $selected]) }}" class="button button-secondary">
                        Limpiar
                    </a>
                </div>

            </div>
        </form>

        @if($search || $level)
            <div class="active-filters">
                @if($search)
                    <div class="filter-chip">
                        Búsqueda: <strong>{{ $search }}</strong>
                    </div>
                @endif

                @if($level)
                    <div class="filter-chip">
                        Nivel: <strong>{{ $level }}</strong>
                    </div>
                @endif
            </div>
        @endif

        <div class="danger-zone">
            <div class="danger-zone-text">
                <strong>Eliminar contenido del log seleccionado</strong>
                <span>Esto vacía el archivo actual. La acción no se puede deshacer.</span>
            </div>

            <form
                method="POST"
                action="{{ route('admin.logs.clear') }}"
                onsubmit="return confirm('¿Seguro que quieres eliminar el contenido de {{ $selected }}? Esta acción no se puede deshacer.')"
            >
                @csrf
                @method('DELETE')

                <input type="hidden" name="file" value="{{ $selected }}">

                <button type="submit" class="button-danger">
                    Eliminar log
                </button>
            </form>
        </div>
    </div>

    @if($entries->count())

        {{-- Vista desktop / tablet horizontal --}}
        <div class="desktop-table">
            <div class="table-card">
                <div class="table-scroll">
                    <table>
                        <thead>
                            <tr>
                                <th style="width: 175px;">Fecha</th>
                                <th style="width: 110px;">Origen</th>
                                <th style="width: 110px;">Nivel</th>
                                <th>Mensaje</th>

                                @if($selected === 'cron-url.log')
                                    <th style="width: 125px;">Pagos enviados</th>
                                    <th style="width: 120px;">Pagos errores</th>
                                    <th style="width: 165px;">Reservaciones enviadas</th>
                                    <th style="width: 160px;">Reservaciones errores</th>
                                @endif
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($entries as $index => $entry)
                                <tr>
                                    <td>{{ $entry['date'] ?: 'Sin fecha' }}</td>

                                    <td>{{ $entry['environment'] ?: 'N/A' }}</td>

                                    <td>
                                        <span class="badge badge-{{ strtoupper($entry['level']) }}">
                                            {{ strtoupper($entry['level']) }}
                                        </span>
                                    </td>

                                    <td>
                                        <div class="message-preview">
                                            {{ $entry['message'] }}
                                        </div>

                                        <details>
                                            <summary>Ver detalle</summary>
                                            <div class="message-detail">{{ $entry['full'] }}</div>
                                        </details>
                                    </td>

                                    @if($selected === 'cron-url.log')
                                        <td class="number ok">{{ $entry['pagos_enviados'] }}</td>
                                        <td class="number error">{{ $entry['pagos_errores'] }}</td>
                                        <td class="number ok">{{ $entry['reservaciones_enviados'] }}</td>
                                        <td class="number error">{{ $entry['reservaciones_errores'] }}</td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Vista móvil / tablet vertical --}}
        <div class="mobile-cards">
            @foreach($entries as $index => $entry)
                <div class="log-card">
                    <div class="log-card-top">
                        <div>
                            <div class="log-card-date">
                                {{ $entry['date'] ?: 'Sin fecha' }}
                            </div>

                            <div class="log-card-origin">
                                Origen: {{ $entry['environment'] ?: 'N/A' }}
                            </div>
                        </div>

                        <span class="badge badge-{{ strtoupper($entry['level']) }}">
                            {{ strtoupper($entry['level']) }}
                        </span>
                    </div>

                    <div class="log-card-message">
                        {{ \Illuminate\Support\Str::limit($entry['message'], 160) }}
                    </div>

                    @if($selected === 'cron-url.log')
                        <div class="cron-metrics">
                            <div class="metric">
                                <span>Pagos enviados</span>
                                <strong class="number ok">{{ $entry['pagos_enviados'] }}</strong>
                            </div>

                            <div class="metric">
                                <span>Pagos errores</span>
                                <strong class="number error">{{ $entry['pagos_errores'] }}</strong>
                            </div>

                            <div class="metric">
                                <span>Reservaciones enviadas</span>
                                <strong class="number ok">{{ $entry['reservaciones_enviados'] }}</strong>
                            </div>

                            <div class="metric">
                                <span>Reservaciones errores</span>
                                <strong class="number error">{{ $entry['reservaciones_errores'] }}</strong>
                            </div>
                        </div>
                    @endif

                    <button
                        type="button"
                        class="button button-secondary mobile-detail-button"
                        onclick="openLogModal('log-detail-{{ $index }}')"
                    >
                        Ver detalle completo
                    </button>

                    <template id="log-detail-{{ $index }}">{{ $entry['full'] }}</template>
                </div>
            @endforeach
        </div>

    @else

        <div class="empty">
            <strong>No hay registros para mostrar</strong>
            Revisa si el archivo está vacío o si los filtros actuales no tienen coincidencias.
        </div>

    @endif

</div>

<div id="logModal" class="log-modal" aria-hidden="true">
    <div class="log-modal-backdrop" onclick="closeLogModal()"></div>

    <div class="log-modal-panel">
        <div class="log-modal-header">
            <div>
                <strong>Detalle del log</strong>
                <span>Contenido completo del registro seleccionado</span>
            </div>

            <button type="button" class="log-modal-close" onclick="closeLogModal()">
                ×
            </button>
        </div>

        <div class="log-modal-actions">
            <button type="button" class="button button-secondary" onclick="copyLogDetail()">
                Copiar detalle
            </button>

            <button type="button" class="button button-secondary" onclick="closeLogModal()">
                Cerrar
            </button>
        </div>

        <pre id="logModalContent" class="log-modal-content"></pre>
    </div>
</div>

<script>
    const fileSelect = document.getElementById('file');

    if (fileSelect) {
        fileSelect.addEventListener('change', function () {
            this.form.submit();
        });
    }

    function openLogModal(templateId) {
        const template = document.getElementById(templateId);
        const modal = document.getElementById('logModal');
        const content = document.getElementById('logModalContent');

        if (!template || !modal || !content) {
            return;
        }

        content.textContent = template.innerHTML.trim();

        modal.classList.add('is-open');
        modal.setAttribute('aria-hidden', 'false');
        document.body.classList.add('modal-open');
    }

    function closeLogModal() {
        const modal = document.getElementById('logModal');
        const content = document.getElementById('logModalContent');

        if (!modal || !content) {
            return;
        }

        modal.classList.remove('is-open');
        modal.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('modal-open');
        content.textContent = '';
    }

    function copyLogDetail() {
        const content = document.getElementById('logModalContent');

        if (!content) {
            return;
        }

        navigator.clipboard.writeText(content.textContent).then(function () {
            alert('Detalle copiado al portapapeles.');
        }).catch(function () {
            alert('No se pudo copiar el detalle.');
        });
    }

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            closeLogModal();
        }
    });
</script>

</body>
</html>