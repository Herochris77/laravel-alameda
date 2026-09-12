<x-app-layout>
    <div class="solicitudes-admin-page">
        <div class="solicitudes-hero">
            <div class="solicitudes-hero-content">
                <div class="solicitudes-hero-left">
                    <div class="solicitudes-hero-icon">
                        <i class="file alternate outline icon"></i>
                    </div>

                    <div>
                        <h1 class="solicitudes-title">Solicitudes de Permiso</h1>
                        <p class="solicitudes-subtitle">Consulta y administra las solicitudes realizadas por inquilinos.</p>
                    </div>
                </div>

                <div class="solicitudes-hero-pill">
                    <i class="clipboard list icon"></i>
                    Control de solicitudes
                </div>
            </div>
        </div>

        <div class="solicitudes-list-card">
            <div class="solicitud-card-header list-header">
                <div class="solicitud-card-title-wrap">
                    <div class="solicitud-card-icon secondary">
                        <i class="list icon"></i>
                    </div>

                    <div>
                        <h3 class="solicitud-card-title">Listado general</h3>
                        <p class="solicitud-card-subtitle">Busca, filtra y revisa el detalle de cada solicitud.</p>
                    </div>
                </div>
            </div>

            <div class="solicitudes-toolbar">
                <div class="filter-field">
                    <label class="form-label">Buscar solicitud</label>
                    <div class="ui icon input solicitudes-search-input">
                        <i class="search icon"></i>
                        <input
                            type="text"
                            id="buscar-solicitud"
                            placeholder="Inquilino, dueño, título, estado o fecha..."
                            autocomplete="off"
                        >
                    </div>
                </div>

                <div class="filter-field">
                    <label class="form-label">Estado</label>
                    <select id="filtro-estado-solicitud" class="ui fluid dropdown solicitud-input">
                        <option value="todos">Todos</option>
                        <option value="pendiente">Pendiente</option>
                        <option value="aprobado">Aprobado</option>
                        <option value="rechazado">Rechazado</option>
                        <option value="eliminada">Eliminada</option>
                    </select>
                </div>

                <div class="filter-field">
                    <label class="form-label">Orden</label>
                    <select id="orden-solicitud" class="ui fluid dropdown solicitud-input">
                        <option value="recientes">Más recientes</option>
                        <option value="antiguas">Más antiguas</option>
                        <option value="inquilino">Inquilino A-Z</option>
                        <option value="dueno">Dueño A-Z</option>
                    </select>
                </div>

                <button type="button" class="btn btn-secondary btn-clear-filters" onclick="limpiarFiltrosSolicitudes()">
                    <i class="times icon"></i>
                    Limpiar
                </button>
            </div>

            <div class="solicitudes-summary">
                <div class="summary-item">
                    <div class="summary-icon">
                        <i class="file alternate outline icon"></i>
                    </div>

                    <div>
                        <span>Solicitudes encontradas</span>
                        <strong id="total-solicitudes">0</strong>
                    </div>
                </div>

                <div class="summary-item status-pendiente">
                    <div class="summary-icon warning">
                        <i class="clock outline icon"></i>
                    </div>

                    <div>
                        <span>Pendientes</span>
                        <strong id="total-pendientes">0</strong>
                    </div>
                </div>

                <div class="summary-item status-aprobado">
                    <div class="summary-icon success">
                        <i class="check icon"></i>
                    </div>

                    <div>
                        <span>Aprobadas</span>
                        <strong id="total-aprobadas">0</strong>
                    </div>
                </div>

                <div class="summary-item status-rechazado">
                    <div class="summary-icon danger">
                        <i class="times icon"></i>
                    </div>

                    <div>
                        <span>Rechazadas</span>
                        <strong id="total-rechazadas">0</strong>
                    </div>
                </div>
            </div>

            <div id="solicitudes-loader" class="solicitudes-loader">
                <div class="ui active centered inline text loader large">Cargando solicitudes...</div>
            </div>

            <div id="solicitudes-container" class="solicitudes-grid"></div>

            <div id="solicitudes-pagination" class="solicitudes-pagination" style="display: none;"></div>

            <div id="sin-solicitudes" class="empty-solicitudes-card" style="display: none;">
                <div class="empty-solicitudes-icon">
                    <i class="file alternate outline icon"></i>
                </div>

                <h3>No hay solicitudes registradas</h3>
                <p>Aún no se han generado solicitudes de permiso.</p>
            </div>

            <div id="sin-resultados-solicitudes" class="empty-solicitudes-card" style="display: none;">
                <div class="empty-solicitudes-icon">
                    <i class="search icon"></i>
                </div>

                <h3>No se encontraron resultados</h3>
                <p>No hay solicitudes que coincidan con tu búsqueda o filtro.</p>

                <button type="button" class="btn btn-secondary btn-sm" onclick="limpiarFiltrosSolicitudes()">
                    <i class="undo icon"></i>
                    Limpiar filtros
                </button>
            </div>

            <div id="solicitudes-error" class="empty-solicitudes-card error" style="display: none;">
                <div class="empty-solicitudes-icon error">
                    <i class="warning sign icon"></i>
                </div>

                <h3>Error al cargar solicitudes</h3>
                <p>No se pudieron cargar las solicitudes. Intenta nuevamente.</p>

                <button type="button" class="btn btn-primary btn-sm" onclick="cargarSolicitudes()">
                    <i class="refresh icon"></i>
                    Reintentar
                </button>
            </div>
        </div>
    </div>

    <div class="ui modal solicitud-detail-modal" id="modal-ver-solicitud">
        <div class="header solicitud-modal-header">
            <i class="file alternate outline icon"></i>
            Detalle de solicitud
        </div>

        <div class="content">
            <div class="solicitud-modal-grid">
                <div class="solicitud-modal-item">
                    <span class="label">Inquilino</span>
                    <span class="value" id="det-inquilino"></span>
                </div>

                <div class="solicitud-modal-item">
                    <span class="label">Dueño</span>
                    <span class="value" id="det-dueno"></span>
                </div>

                <div class="solicitud-modal-item">
                    <span class="label">Título</span>
                    <span class="value" id="det-titulo"></span>
                </div>

                <div class="solicitud-modal-item">
                    <span class="label">Estado</span>
                    <span class="value" id="det-estado"></span>
                </div>

                <div class="solicitud-modal-item">
                    <span class="label">Enviada</span>
                    <span class="value" id="det-creada"></span>
                </div>

                <div class="solicitud-modal-item">
                    <span class="label">Respuesta</span>
                    <span class="value" id="det-respuesta"></span>
                </div>
            </div>

            <span class="solicitud-modal-mensaje-label">Mensaje</span>
            <div class="solicitud-modal-mensaje" id="det-mensaje"></div>
        </div>

        <div class="actions solicitud-modal-actions">
            <button class="btn btn-secondary" onclick="$('#modal-ver-solicitud').modal('hide')">
                <i class="times icon"></i>
                Cerrar
            </button>
        </div>
    </div>

    <style>
        :root {
            --soli-primary: #3b82f6;
            --soli-primary-dark: #2563eb;
            --soli-secondary: #667eea;
            --soli-success: #10b981;
            --soli-success-dark: #059669;
            --soli-danger: #ef4444;
            --soli-danger-dark: #dc2626;
            --soli-warning: #f59e0b;
            --soli-info: #3b82f6;
            --soli-text-main: #0f172a;
            --soli-text-muted: #64748b;
            --soli-text-soft: #94a3b8;
            --soli-border: #e2e8f0;
            --soli-surface: #ffffff;
            --soli-soft-bg: #f8fafc;
        }

        .solicitudes-admin-page {
            width: 100%;
            padding-bottom: 24px;
        }

        .solicitudes-hero {
            background:
                radial-gradient(circle at top right, rgba(255,255,255,0.24), transparent 34%),
                radial-gradient(circle at bottom left, rgba(255,255,255,0.14), transparent 30%),
                linear-gradient(135deg, var(--soli-primary) 0%, var(--soli-primary-dark) 100%);
            border-radius: 26px;
            padding: 26px;
            color: white;
            margin-bottom: 22px;
            box-shadow: 0 12px 30px rgba(37, 99, 235, 0.18);
            overflow: hidden;
        }

        .solicitudes-hero-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 18px;
            flex-wrap: wrap;
        }

        .solicitudes-hero-left {
            display: flex;
            align-items: center;
            gap: 16px;
            min-width: 0;
        }

        .solicitudes-hero-icon {
            width: 64px;
            height: 64px;
            border-radius: 20px;
            background: rgba(255, 255, 255, 0.16);
            border: 1px solid rgba(255, 255, 255, 0.22);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            backdrop-filter: blur(8px);
        }

        .solicitudes-hero-icon i {
            color: white;
            font-size: 1.8rem;
            margin: 0 !important;
        }

        .solicitudes-title {
            margin: 0 0 6px 0;
            font-size: clamp(1.55rem, 3vw, 2.15rem);
            font-weight: 900;
            letter-spacing: -0.035em;
            line-height: 1.08;
        }

        .solicitudes-subtitle {
            margin: 0;
            opacity: 0.92;
            font-size: 1rem;
            line-height: 1.45;
        }

        .solicitudes-hero-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 14px;
            border-radius: 999px;
            background: rgba(255,255,255,0.16);
            border: 1px solid rgba(255,255,255,0.22);
            font-size: 0.86rem;
            font-weight: 800;
            backdrop-filter: blur(8px);
            white-space: nowrap;
        }

        .solicitudes-hero-pill i {
            margin: 0 !important;
        }

        .solicitudes-list-card,
        .empty-solicitudes-card {
            background: var(--soli-surface);
            border: 1px solid var(--soli-border);
            border-radius: 22px;
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.06);
            overflow: hidden;
        }

        .solicitud-card-header {
            padding: 18px 20px;
            border-bottom: 1px solid #f1f5f9;
            background: linear-gradient(180deg, #ffffff 0%, #fbfdff 100%);
        }

        .solicitud-card-title-wrap {
            display: flex;
            align-items: center;
            gap: 14px;
            min-width: 0;
        }

        .solicitud-card-icon {
            width: 52px;
            height: 52px;
            border-radius: 16px;
            background: linear-gradient(135deg, var(--soli-primary) 0%, var(--soli-primary-dark) 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            box-shadow: 0 8px 18px rgba(59, 130, 246, 0.24);
        }

        .solicitud-card-icon.secondary {
            background: linear-gradient(135deg, var(--soli-secondary) 0%, var(--soli-primary-dark) 100%);
        }

        .solicitud-card-icon i {
            margin: 0 !important;
            font-size: 1.25rem;
        }

        .solicitud-card-title {
            margin: 0;
            color: var(--soli-text-main);
            font-size: 1.12rem;
            font-weight: 900;
            line-height: 1.25;
        }

        .solicitud-card-subtitle {
            margin: 4px 0 0 0;
            color: var(--soli-text-muted);
            font-size: 0.9rem;
            line-height: 1.35;
        }

        .solicitudes-toolbar {
            padding: 18px 20px;
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(170px, 210px) minmax(170px, 210px) auto;
            gap: 14px;
            align-items: end;
            border-bottom: 1px solid #f1f5f9;
        }

        .filter-field {
            min-width: 0;
        }

        .form-label {
            display: block;
            color: var(--soli-text-main);
            font-weight: 800;
            margin-bottom: 8px;
            font-size: 0.9rem;
        }

        .solicitud-input {
            min-height: 46px;
            border-radius: 14px !important;
            width: 100%;
            max-width: 100%;
            box-sizing: border-box;
            border: 1px solid var(--soli-border) !important;
            transition: border-color 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
        }

        .solicitud-input:focus {
            border-color: rgba(59, 130, 246, 0.65) !important;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.12) !important;
            outline: none;
            background: white;
        }

        .solicitudes-search-input {
            width: 100%;
        }

        .solicitudes-search-input input {
            min-height: 46px;
            border-radius: 14px !important;
        }

        .btn-clear-filters {
            min-height: 46px;
            border-radius: 14px;
            display: inline-flex !important;
            align-items: center;
            justify-content: center;
            gap: 8px;
            white-space: nowrap;
        }

        .btn-clear-filters i {
            margin: 0 !important;
        }

        .solicitudes-summary {
            padding: 16px 20px 0;
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        .summary-item {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            background: var(--soli-soft-bg);
            border: 1px solid #eef2f7;
            border-radius: 16px;
            padding: 12px 14px;
            min-width: 165px;
        }

        .summary-icon {
            width: 42px;
            height: 42px;
            border-radius: 14px;
            background: #dbeafe;
            color: var(--soli-primary-dark);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .summary-icon.warning {
            background: #fef3c7;
            color: #b45309;
        }

        .summary-icon.success {
            background: #d1fae5;
            color: var(--soli-success-dark);
        }

        .summary-icon.danger {
            background: #fee2e2;
            color: var(--soli-danger-dark);
        }

        .summary-icon i {
            margin: 0 !important;
        }

        .summary-item span {
            display: block;
            color: var(--soli-text-muted);
            font-size: 0.78rem;
            font-weight: 800;
            margin-bottom: 2px;
        }

        .summary-item strong {
            display: block;
            color: var(--soli-text-main);
            font-size: 1.2rem;
            line-height: 1;
        }

        .solicitudes-loader {
            min-height: 220px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
        }

        .solicitudes-grid {
            padding: 20px;
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 16px;
        }

        .solicitud-item-card {
            border: 1px solid var(--soli-border);
            border-radius: 18px;
            background: white;
            box-shadow: 0 6px 18px rgba(15, 23, 42, 0.05);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
        }

        .solicitud-item-card:hover {
            transform: translateY(-2px);
            border-color: rgba(59, 130, 246, 0.35);
            box-shadow: 0 10px 24px rgba(15, 23, 42, 0.08);
        }

        .solicitud-item-header {
            padding: 16px;
            border-bottom: 1px solid #f1f5f9;
            display: flex;
            align-items: flex-start;
            gap: 12px;
        }

        .solicitud-item-icon {
            width: 46px;
            height: 46px;
            border-radius: 14px;
            background: #dbeafe;
            color: var(--soli-primary-dark);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .solicitud-item-icon.pendiente {
            background: #fef3c7;
            color: #b45309;
        }

        .solicitud-item-icon.aprobado {
            background: #d1fae5;
            color: var(--soli-success-dark);
        }

        .solicitud-item-icon.rechazado {
            background: #fee2e2;
            color: var(--soli-danger-dark);
        }

        .solicitud-item-icon.eliminada {
            background: #e5e7eb;
            color: #4b5563;
        }

        .solicitud-item-icon i {
            margin: 0 !important;
            font-size: 1.15rem;
        }

        .solicitud-item-title {
            margin: 0;
            color: var(--soli-text-main);
            font-size: 1rem;
            font-weight: 900;
            line-height: 1.3;
            overflow-wrap: anywhere;
        }

        .solicitud-item-subtitle {
            margin: 6px 0 0;
            color: var(--soli-text-muted);
            font-size: 0.86rem;
            line-height: 1.4;
            overflow-wrap: anywhere;
        }

        .solicitud-item-badges {
            display: flex;
            flex-wrap: wrap;
            gap: 7px;
            margin-top: 9px;
        }

        .solicitud-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 6px 9px;
            border-radius: 999px;
            font-size: 0.72rem;
            font-weight: 900;
            line-height: 1;
        }

        .solicitud-badge i {
            margin: 0 !important;
        }

        .solicitud-badge.pendiente {
            background: #fef3c7;
            color: #92400e;
        }

        .solicitud-badge.aprobado {
            background: #d1fae5;
            color: #065f46;
        }

        .solicitud-badge.rechazado {
            background: #fee2e2;
            color: #991b1b;
        }

        .solicitud-badge.eliminada {
            background: #e5e7eb;
            color: #374151;
        }

        .solicitud-item-body {
            padding: 16px;
            flex: 1;
            display: grid;
            gap: 10px;
        }

        .solicitud-meta-item {
            display: flex;
            align-items: flex-start;
            gap: 9px;
            color: var(--soli-text-muted);
            font-size: 0.88rem;
            line-height: 1.4;
            overflow-wrap: anywhere;
        }

        .solicitud-meta-item i {
            color: var(--soli-primary-dark);
            margin: 2px 0 0 0 !important;
            flex-shrink: 0;
        }

        .solicitud-meta-item strong {
            color: var(--soli-text-main);
            font-weight: 900;
        }

        .solicitud-item-footer {
            padding: 14px 16px 16px;
            border-top: 1px solid #f1f5f9;
            background: #fbfdff;
        }

        .solicitud-actions {
            width: 100%;
            display: grid;
            grid-template-columns: 1fr;
            gap: 10px;
        }

        .solicitud-actions .btn,
        .solicitud-actions button,
        .solicitud-actions a {
            width: 100% !important;
            max-width: 100% !important;
            min-width: 0 !important;
            min-height: 44px;
            margin: 0 !important;
            padding: 10px 12px !important;
            border-radius: 12px !important;
            box-sizing: border-box;
            display: inline-flex !important;
            align-items: center;
            justify-content: center;
            gap: 7px;
            font-size: 0.88rem;
            font-weight: 900;
            line-height: 1.1;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .solicitud-actions i {
            margin: 0 !important;
            flex-shrink: 0;
        }

        .solicitud-actions .btn-ver-solicitud,
        .solicitud-actions .btn-ver-detalle {
            background: #eff6ff !important;
            color: #1d4ed8 !important;
            border: 1px solid #bfdbfe !important;
        }

        .solicitud-actions .btn-ver-solicitud:hover,
        .solicitud-actions .btn-ver-detalle:hover {
            background: #dbeafe !important;
        }

        .solicitudes-pagination {
            padding: 0 20px 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .pagination-btn {
            min-width: 40px;
            min-height: 40px;
            border: 1px solid var(--soli-border);
            background: #ffffff;
            color: var(--soli-text-muted);
            border-radius: 12px;
            font-weight: 900;
            cursor: pointer;
            transition: background 0.2s ease, color 0.2s ease, border-color 0.2s ease;
        }

        .pagination-btn:hover {
            background: #eff6ff;
            color: var(--soli-primary-dark);
            border-color: rgba(59, 130, 246, 0.35);
        }

        .pagination-btn.active {
            background: linear-gradient(135deg, var(--soli-primary) 0%, var(--soli-primary-dark) 100%);
            color: #ffffff;
            border-color: transparent;
            box-shadow: 0 8px 18px rgba(59, 130, 246, 0.22);
        }

        .pagination-btn:disabled {
            opacity: 0.45;
            cursor: not-allowed;
            background: var(--soli-soft-bg);
            color: var(--soli-text-soft);
        }

        .pagination-info {
            color: var(--soli-text-muted);
            font-size: 0.86rem;
            font-weight: 800;
            margin: 0 8px;
        }

        .empty-solicitudes-card {
            margin: 20px;
            padding: 44px 20px;
            text-align: center;
        }

        .empty-solicitudes-icon {
            width: 78px;
            height: 78px;
            border-radius: 24px;
            margin: 0 auto 18px;
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.12), rgba(37, 99, 235, 0.16));
            color: var(--soli-primary-dark);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .empty-solicitudes-icon.error {
            background: #fee2e2;
            color: var(--soli-danger);
        }

        .empty-solicitudes-icon i {
            font-size: 2rem;
            margin: 0 !important;
        }

        .empty-solicitudes-card h3 {
            margin: 0 0 8px 0;
            color: #475569;
            font-weight: 900;
        }

        .empty-solicitudes-card p {
            margin: 0 0 18px 0;
            color: var(--soli-text-soft);
            line-height: 1.5;
        }

        .solicitud-detail-modal {
            border-radius: 20px !important;
            overflow: hidden;
        }

        .solicitud-modal-header {
            background: linear-gradient(135deg, var(--soli-secondary) 0%, var(--soli-primary-dark) 100%) !important;
            color: white !important;
            font-weight: 900 !important;
        }

        .solicitud-modal-header i {
            margin-right: 8px !important;
        }

        .solicitud-detail-modal > .content {
            padding: 20px !important;
        }

        .solicitud-modal-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
        }

        .solicitud-modal-item {
            padding: 12px 14px;
            background: var(--soli-soft-bg);
            border: 1px solid #eef2f7;
            border-radius: 14px;
            overflow-wrap: anywhere;
        }

        .solicitud-modal-item .label {
            font-size: 0.72rem;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: var(--soli-text-soft);
            display: block;
            margin-bottom: 4px;
        }

        .solicitud-modal-item .value {
            font-size: 0.92rem;
            font-weight: 800;
            color: var(--soli-text-main);
            line-height: 1.4;
        }

        .solicitud-modal-mensaje-label {
            font-size: 0.72rem;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: var(--soli-text-soft);
            display: block;
            margin: 16px 0 6px;
        }

        .solicitud-modal-mensaje {
            background: var(--soli-soft-bg);
            border: 1px solid #eef2f7;
            padding: 18px;
            border-radius: 14px;
            line-height: 1.6;
            font-size: 0.95rem;
            color: var(--soli-text-main);
            white-space: pre-wrap;
            overflow-wrap: anywhere;
        }

        .solicitud-modal-actions {
            display: flex !important;
            justify-content: flex-end;
            gap: 10px;
            flex-wrap: wrap;
            padding: 16px 20px !important;
            background: var(--soli-soft-bg) !important;
            border-top: 1px solid #f1f5f9 !important;
        }

        .solicitud-modal-actions .btn {
            min-height: 42px;
            border-radius: 12px !important;
            display: inline-flex !important;
            align-items: center;
            justify-content: center;
            gap: 7px;
        }

        @media (max-width: 1200px) {
            .solicitudes-toolbar {
                grid-template-columns: minmax(0, 1fr) minmax(170px, 210px) minmax(170px, 210px);
            }

            .btn-clear-filters {
                grid-column: 1 / -1;
                width: 100%;
            }
        }

        @media (max-width: 900px) {
            .solicitudes-grid {
                grid-template-columns: 1fr;
            }

            .solicitudes-toolbar {
                grid-template-columns: 1fr;
            }

            .btn-clear-filters {
                width: 100%;
            }

            .solicitudes-summary {
                display: grid;
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .summary-item {
                width: 100%;
                min-width: 0;
            }
        }

        @media (max-width: 768px) {
            .solicitudes-hero {
                border-radius: 0 0 24px 24px;
                margin: -8px -4px 18px;
                padding: 22px;
            }

            .solicitudes-hero-content {
                align-items: stretch;
            }

            .solicitudes-hero-left {
                align-items: flex-start;
            }

            .solicitudes-hero-icon {
                width: 54px;
                height: 54px;
                border-radius: 18px;
            }

            .solicitudes-title {
                font-size: 1.55rem;
            }

            .solicitudes-subtitle {
                font-size: 0.92rem;
            }

            .solicitudes-hero-pill {
                width: 100%;
                justify-content: center;
                border-radius: 16px;
            }

            .solicitudes-list-card,
            .empty-solicitudes-card {
                border-radius: 18px;
            }

            .solicitud-card-header,
            .solicitudes-toolbar {
                padding: 16px;
            }

            .solicitud-card-icon {
                width: 46px;
                height: 46px;
                border-radius: 14px;
            }

            .solicitud-card-title {
                font-size: 1.04rem;
            }

            .solicitud-card-subtitle {
                font-size: 0.85rem;
            }

            .solicitudes-summary {
                padding: 14px 16px 0;
            }

            .solicitudes-grid {
                padding: 16px;
            }

            .solicitud-item-card:hover {
                transform: none;
            }

            .solicitud-item-footer {
                padding: 12px;
                position: sticky;
                bottom: 0;
                background: #ffffff;
                box-shadow: 0 -8px 18px rgba(15, 23, 42, 0.04);
            }

            .solicitud-actions .btn,
            .solicitud-actions button,
            .solicitud-actions a {
                min-height: 46px;
                font-size: 0.86rem;
            }

            .solicitudes-pagination {
                padding: 0 16px 16px;
                gap: 6px;
            }

            .pagination-btn {
                min-width: 38px;
                min-height: 38px;
                border-radius: 11px;
            }

            .pagination-info {
                width: 100%;
                text-align: center;
                margin: 4px 0;
            }

            .solicitud-modal-grid {
                grid-template-columns: 1fr;
            }

            input[type="text"],
            select {
                font-size: 16px !important;
            }

            .solicitud-modal-actions {
                padding: 14px !important;
            }

            .solicitud-modal-actions .btn {
                flex: 1;
            }
        }

        @media (max-width: 520px) {
            .solicitudes-summary {
                grid-template-columns: 1fr;
            }

            .status-pendiente,
            .status-aprobado,
            .status-rechazado {
                display: none;
            }
        }

        @media (max-width: 480px) {
            .solicitudes-grid {
                padding: 12px;
                gap: 14px;
            }

            .solicitud-item-header {
                padding: 14px;
            }

            .solicitud-item-body {
                padding: 14px;
                gap: 9px;
            }

            .solicitud-meta-item {
                font-size: 0.86rem;
            }

            .solicitud-actions .btn,
            .solicitud-actions button,
            .solicitud-actions a,
            .solicitud-item-footer .btn,
            .solicitud-item-footer button,
            .solicitud-item-footer a {
                min-height: 48px;
                width: 100% !important;
                justify-content: center !important;
                font-size: 0.9rem;
            }
        }

        @media (max-width: 420px) {
            .solicitudes-hero-left {
                gap: 12px;
            }

            .solicitud-card-title-wrap {
                align-items: flex-start;
            }

            .solicitud-item-header {
                align-items: flex-start;
            }

            .solicitud-item-icon {
                width: 42px;
                height: 42px;
                border-radius: 13px;
            }

            .solicitud-item-title {
                font-size: 0.98rem;
            }

            .solicitud-badge {
                max-width: 100%;
                white-space: normal;
                line-height: 1.2;
            }

            .solicitud-modal-actions {
                flex-direction: column-reverse;
            }

            .solicitud-modal-actions .btn {
                width: 100%;
            }
        }
    </style>

    <script>
        const BASE_URL_SOLICITUDES = "{{ url('/') }}";

        let solicitudesOriginales = [];
        let solicitudesFiltradas = [];
        let paginaActualSolicitudes = 1;
        let solicitudesPorPagina = 6;

        $(document).ready(function() {
            $('#modal-ver-solicitud').modal();

            $('#filtro-estado-solicitud').dropdown();
            $('#orden-solicitud').dropdown();

            cargarSolicitudes();

            $('#buscar-solicitud').on('keyup input change', function() {
                filtrarSolicitudes();
            });

            $('#filtro-estado-solicitud').on('change', function() {
                filtrarSolicitudes();
            });

            $('#orden-solicitud').on('change', function() {
                filtrarSolicitudes();
            });
        });

        function cargarSolicitudes() {
            $('#solicitudes-loader').show();
            $('#solicitudes-container').empty();
            $('#solicitudes-pagination').hide().empty();
            $('#sin-solicitudes').hide();
            $('#sin-resultados-solicitudes').hide();
            $('#solicitudes-error').hide();

            $.ajax({
                url: '{{ route("admin.solicitudes.listar") }}',
                method: 'GET',
                dataType: 'json',
                data: {
                    start: 0,
                    length: 1000
                },
                success: function(response) {
                    $('#solicitudes-loader').hide();

                    solicitudesOriginales = obtenerDataSolicitudes(response);
                    solicitudesOriginales = ordenarSolicitudes(solicitudesOriginales);

                    actualizarResumenSolicitudes(solicitudesOriginales);
                    renderizarSolicitudes(solicitudesOriginales);
                },
                error: function() {
                    $('#solicitudes-loader').hide();
                    $('#solicitudes-error').show();
                    actualizarResumenSolicitudes([]);
                }
            });
        }

        function obtenerDataSolicitudes(response) {
            if (Array.isArray(response)) {
                return response;
            }

            if (response && Array.isArray(response.data)) {
                return response.data;
            }

            return [];
        }

        function ordenarSolicitudes(solicitudes) {
            const orden = $('#orden-solicitud').val() || 'recientes';

            return [...solicitudes].sort(function(a, b) {
                if (orden === 'antiguas') {
                    return obtenerTimestamp(a.created_at) - obtenerTimestamp(b.created_at);
                }

                if (orden === 'inquilino') {
                    const inquilinoA = normalizarTextoSolicitud(extraerTextoSolicitud(a.inquilino || ''));
                    const inquilinoB = normalizarTextoSolicitud(extraerTextoSolicitud(b.inquilino || ''));

                    return inquilinoA.localeCompare(inquilinoB);
                }

                if (orden === 'dueno') {
                    const duenoA = normalizarTextoSolicitud(extraerTextoSolicitud(a.dueno || ''));
                    const duenoB = normalizarTextoSolicitud(extraerTextoSolicitud(b.dueno || ''));

                    return duenoA.localeCompare(duenoB);
                }

                return obtenerTimestamp(b.created_at) - obtenerTimestamp(a.created_at);
            });
        }

        function renderizarSolicitudes(solicitudes) {
            solicitudesFiltradas = solicitudes;
            paginaActualSolicitudes = 1;
            renderizarPaginaSolicitudes();
        }

        function renderizarPaginaSolicitudes() {
            const container = $('#solicitudes-container');
            const pagination = $('#solicitudes-pagination');

            container.empty();
            pagination.empty();

            $('#total-solicitudes').text(solicitudesFiltradas.length);

            $('#sin-solicitudes').hide();
            $('#sin-resultados-solicitudes').hide();
            $('#solicitudes-error').hide();

            if (solicitudesOriginales.length === 0) {
                pagination.hide();
                $('#sin-solicitudes').show();
                return;
            }

            if (solicitudesFiltradas.length === 0) {
                pagination.hide();
                $('#sin-resultados-solicitudes').show();
                return;
            }

            const totalPaginas = Math.ceil(solicitudesFiltradas.length / solicitudesPorPagina);
            const inicio = (paginaActualSolicitudes - 1) * solicitudesPorPagina;
            const fin = inicio + solicitudesPorPagina;
            const solicitudesPagina = solicitudesFiltradas.slice(inicio, fin);

            solicitudesPagina.forEach(function(solicitud) {
                const id = extraerTextoSolicitud(solicitud.id || solicitud.solicitud_id || '');
                const inquilino = extraerTextoSolicitud(solicitud.inquilino || 'Inquilino no disponible');
                const dueno = extraerTextoSolicitud(solicitud.dueno || 'Dueño no disponible');
                const titulo = extraerTextoSolicitud(solicitud.titulo || 'Solicitud sin título');
                const createdAt = extraerTextoSolicitud(solicitud.created_at || 'Sin fecha');
                const fechaRespuesta = extraerTextoSolicitud(solicitud.fecha_respuesta || 'Sin respuesta');
                const estadoInfo = obtenerEstadoSolicitud(solicitud);
                const casa = extraerTextoSolicitud(solicitud.casa || '');

                const casaHtml = casa
                    ? `<span>Casa <strong>${escapeHtmlSolicitud(casa)}</strong></span>`
                    : `<span>Casa no disponible</span>`;

                const accionesHtml = obtenerAccionesSolicitud(solicitud, id);

                const card = `
                    <div class="solicitud-item-card">
                        <div class="solicitud-item-header">
                            <div class="solicitud-item-icon ${estadoInfo.clase}">
                                <i class="${estadoInfo.icono} icon"></i>
                            </div>

                            <div style="min-width: 0; flex: 1;">
                                <h3 class="solicitud-item-title">${escapeHtmlSolicitud(titulo)}</h3>
                                <p class="solicitud-item-subtitle">${escapeHtmlSolicitud(inquilino)}</p>

                                <div class="solicitud-item-badges">
                                    <span class="solicitud-badge ${estadoInfo.clase}">
                                        <i class="${estadoInfo.icono} icon"></i>
                                        ${escapeHtmlSolicitud(estadoInfo.texto)}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="solicitud-item-body">
                            <div class="solicitud-meta-item">
                                <i class="user icon"></i>
                                <span><strong>Inquilino:</strong> ${escapeHtmlSolicitud(inquilino)}</span>
                            </div>

                            <div class="solicitud-meta-item">
                                <i class="home icon"></i>
                                ${casaHtml}
                            </div>

                            <div class="solicitud-meta-item">
                                <i class="user tie icon"></i>
                                <span><strong>Dueño:</strong> ${escapeHtmlSolicitud(dueno)}</span>
                            </div>

                            <div class="solicitud-meta-item">
                                <i class="calendar alternate outline icon"></i>
                                <span><strong>Enviada:</strong> ${escapeHtmlSolicitud(createdAt)}</span>
                            </div>

                            <div class="solicitud-meta-item">
                                <i class="reply icon"></i>
                                <span><strong>Respuesta:</strong> ${escapeHtmlSolicitud(fechaRespuesta)}</span>
                            </div>
                        </div>

                        <div class="solicitud-item-footer">
                            <div class="solicitud-actions">
                                ${accionesHtml}
                            </div>
                        </div>
                    </div>
                `;

                container.append(card);
            });

            renderizarPaginacionSolicitudes(totalPaginas, solicitudesFiltradas.length);
        }

        function obtenerAccionesSolicitud(solicitud, id) {
            if (id) {
                return `
                    <button
                        type="button"
                        class="btn btn-secondary btn-sm btn-ver-solicitud"
                        data-id="${escapeHtmlSolicitud(id)}"
                    >
                        <i class="eye icon"></i>
                        Ver detalle
                    </button>
                `;
            }

            if (solicitud.acciones) {
                return solicitud.acciones;
            }

            return `
                <button type="button" class="btn btn-secondary btn-sm disabled">
                    <i class="eye slash icon"></i>
                    Sin acciones
                </button>
            `;
        }

        function renderizarPaginacionSolicitudes(totalPaginas, totalSolicitudes) {
            const pagination = $('#solicitudes-pagination');

            pagination.empty();

            if (totalPaginas <= 1) {
                pagination.hide();
                return;
            }

            const inicio = ((paginaActualSolicitudes - 1) * solicitudesPorPagina) + 1;
            const fin = Math.min(paginaActualSolicitudes * solicitudesPorPagina, totalSolicitudes);

            pagination.show();

            pagination.append(`
                <button
                    type="button"
                    class="pagination-btn"
                    onclick="cambiarPaginaSolicitudes(${paginaActualSolicitudes - 1})"
                    ${paginaActualSolicitudes === 1 ? 'disabled' : ''}
                    aria-label="Página anterior"
                >
                    <i class="chevron left icon"></i>
                </button>
            `);

            const paginas = obtenerPaginasVisiblesSolicitudes(totalPaginas);

            paginas.forEach(function(pagina) {
                if (pagina === '...') {
                    pagination.append(`<span class="pagination-info">...</span>`);
                    return;
                }

                pagination.append(`
                    <button
                        type="button"
                        class="pagination-btn ${pagina === paginaActualSolicitudes ? 'active' : ''}"
                        onclick="cambiarPaginaSolicitudes(${pagina})"
                    >
                        ${pagina}
                    </button>
                `);
            });

            pagination.append(`
                <button
                    type="button"
                    class="pagination-btn"
                    onclick="cambiarPaginaSolicitudes(${paginaActualSolicitudes + 1})"
                    ${paginaActualSolicitudes === totalPaginas ? 'disabled' : ''}
                    aria-label="Página siguiente"
                >
                    <i class="chevron right icon"></i>
                </button>
            `);

            pagination.append(`
                <div class="pagination-info">
                    Mostrando ${inicio}-${fin} de ${totalSolicitudes}
                </div>
            `);
        }

        function cambiarPaginaSolicitudes(pagina) {
            const totalPaginas = Math.ceil(solicitudesFiltradas.length / solicitudesPorPagina);

            if (pagina < 1 || pagina > totalPaginas) {
                return;
            }

            paginaActualSolicitudes = pagina;
            renderizarPaginaSolicitudes();

            document.querySelector('.solicitudes-list-card')?.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }

        function obtenerPaginasVisiblesSolicitudes(totalPaginas) {
            const paginas = [];

            if (totalPaginas <= 5) {
                for (let i = 1; i <= totalPaginas; i++) {
                    paginas.push(i);
                }

                return paginas;
            }

            paginas.push(1);

            if (paginaActualSolicitudes > 3) {
                paginas.push('...');
            }

            const inicio = Math.max(2, paginaActualSolicitudes - 1);
            const fin = Math.min(totalPaginas - 1, paginaActualSolicitudes + 1);

            for (let i = inicio; i <= fin; i++) {
                paginas.push(i);
            }

            if (paginaActualSolicitudes < totalPaginas - 2) {
                paginas.push('...');
            }

            paginas.push(totalPaginas);

            return paginas;
        }

        function filtrarSolicitudes() {
            const busqueda = normalizarTextoSolicitud($('#buscar-solicitud').val());
            const estadoFiltro = $('#filtro-estado-solicitud').val();

            let filtradas = solicitudesOriginales.filter(function(solicitud) {
                const inquilino = extraerTextoSolicitud(solicitud.inquilino || '');
                const dueno = extraerTextoSolicitud(solicitud.dueno || '');
                const titulo = extraerTextoSolicitud(solicitud.titulo || '');
                const estado = obtenerEstadoSolicitud(solicitud).clase;
                const createdAt = extraerTextoSolicitud(solicitud.created_at || '');
                const fechaRespuesta = extraerTextoSolicitud(solicitud.fecha_respuesta || '');
                const casa = extraerTextoSolicitud(solicitud.casa || '');

                const textoCompleto = normalizarTextoSolicitud(
                    inquilino + ' ' +
                    dueno + ' ' +
                    titulo + ' ' +
                    estado + ' ' +
                    createdAt + ' ' +
                    fechaRespuesta + ' ' +
                    casa
                );

                const coincideBusqueda = textoCompleto.includes(busqueda);
                let coincideEstado = true;

                if (estadoFiltro !== 'todos') {
                    coincideEstado = estado === estadoFiltro;
                }

                return coincideBusqueda && coincideEstado;
            });

            filtradas = ordenarSolicitudes(filtradas);
            renderizarSolicitudes(filtradas);
        }

        function limpiarFiltrosSolicitudes() {
            $('#buscar-solicitud').val('');
            $('#filtro-estado-solicitud').dropdown('set selected', 'todos');
            $('#orden-solicitud').dropdown('set selected', 'recientes');

            const ordenadas = ordenarSolicitudes(solicitudesOriginales);
            renderizarSolicitudes(ordenadas);
        }

        function actualizarResumenSolicitudes(solicitudes) {
            const pendientes = solicitudes.filter(function(solicitud) {
                return obtenerEstadoSolicitud(solicitud).clase === 'pendiente';
            }).length;

            const aprobadas = solicitudes.filter(function(solicitud) {
                return obtenerEstadoSolicitud(solicitud).clase === 'aprobado';
            }).length;

            const rechazadas = solicitudes.filter(function(solicitud) {
                return obtenerEstadoSolicitud(solicitud).clase === 'rechazado';
            }).length;

            $('#total-solicitudes').text(solicitudes.length);
            $('#total-pendientes').text(pendientes);
            $('#total-aprobadas').text(aprobadas);
            $('#total-rechazadas').text(rechazadas);
        }

        function obtenerEstadoSolicitud(solicitud) {
            const trashed = solicitud.trashed === true || solicitud.trashed === 1 || solicitud.trashed === '1';

            if (trashed) {
                return {
                    clase: 'eliminada',
                    texto: 'Eliminada',
                    icono: 'trash'
                };
            }

            const estadoPlano = normalizarTextoSolicitud(extraerTextoSolicitud(solicitud.estado || ''));

            if (estadoPlano.includes('pendiente')) {
                return {
                    clase: 'pendiente',
                    texto: 'Pendiente',
                    icono: 'clock outline'
                };
            }

            if (estadoPlano.includes('aprobado') || estadoPlano.includes('aprobada')) {
                return {
                    clase: 'aprobado',
                    texto: 'Aprobado',
                    icono: 'check'
                };
            }

            if (estadoPlano.includes('rechazado') || estadoPlano.includes('rechazada')) {
                return {
                    clase: 'rechazado',
                    texto: 'Rechazado',
                    icono: 'times'
                };
            }

            return {
                clase: 'pendiente',
                texto: extraerTextoSolicitud(solicitud.estado || 'Pendiente'),
                icono: 'clock outline'
            };
        }

        $(document).on('click', '.btn-ver-solicitud', function() {
            const id = $(this).data('id');

            if (!id) {
                alertify.error('No se encontró el identificador de la solicitud');
                return;
            }

            $.get('{{ url("administrador/solicitudes/ver") }}/' + id, function(data) {
                const casa = data.casa ? ' (casa ' + data.casa + ')' : '';

                $('#det-inquilino').text((data.inquilino || 'No disponible') + casa);
                $('#det-dueno').text(data.dueno || 'No disponible');
                $('#det-titulo').text(data.titulo || 'Sin título');
                $('#det-mensaje').text(data.mensaje || 'Sin mensaje');
                $('#det-creada').text(data.created_at || 'Sin fecha');

                let estadoLabel = '';

                if (data.trashed) {
                    estadoLabel = '<span class="solicitud-badge eliminada"><i class="trash icon"></i> Eliminada</span>';
                } else if (data.estado === 'pendiente') {
                    estadoLabel = '<span class="solicitud-badge pendiente"><i class="clock outline icon"></i> Pendiente</span>';
                } else if (data.estado === 'aprobado') {
                    estadoLabel = '<span class="solicitud-badge aprobado"><i class="check icon"></i> Aprobado</span>';
                } else {
                    estadoLabel = '<span class="solicitud-badge rechazado"><i class="times icon"></i> Rechazado</span>';
                }

                $('#det-estado').html(estadoLabel);
                $('#det-respuesta').text(data.trashed ? ('Eliminada: ' + (data.deleted_at || 'Sin fecha')) : (data.fecha_respuesta || 'Sin respuesta'));

                $('#modal-ver-solicitud').modal('show');
            }).fail(function() {
                alertify.error('No se pudo cargar el detalle de la solicitud');
            });
        });

        function obtenerTimestamp(fecha) {
            const texto = extraerTextoSolicitud(fecha || '');

            if (!texto) {
                return 0;
            }

            const fechaParseada = new Date(texto);

            if (!isNaN(fechaParseada.getTime())) {
                return fechaParseada.getTime();
            }

            return 0;
        }

        function normalizarTextoSolicitud(texto) {
            return String(texto || '')
                .toLowerCase()
                .normalize('NFD')
                .replace(/[\u0300-\u036f]/g, '')
                .trim();
        }

        function extraerTextoSolicitud(valor) {
            const temporal = document.createElement('div');
            temporal.innerHTML = valor || '';
            return temporal.textContent || temporal.innerText || '';
        }

        function escapeHtmlSolicitud(text) {
            return String(text || '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }
    </script>
</x-app-layout>