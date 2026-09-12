<x-app-layout>
    <div class="encuestas-admin-page">
        <div class="encuestas-hero">
            <div class="encuestas-hero-content">
                <div class="encuestas-hero-left">
                    <div class="encuestas-hero-icon">
                        <i class="chart pie icon"></i>
                    </div>

                    <div>
                        <h1 class="encuestas-title">Encuestas</h1>
                        <p class="encuestas-subtitle">Crea y administra encuestas para los vecinos</p>
                    </div>
                </div>

                <div class="encuestas-hero-pill">
                    <i class="poll icon"></i>
                    Participación vecinal
                </div>
            </div>
        </div>

        <div class="encuestas-layout">
            <div class="encuesta-form-card">
                <div class="encuesta-card-header">
                    <div class="encuesta-card-title-wrap">
                        <div class="encuesta-card-icon">
                            <i class="plus circle icon"></i>
                        </div>

                        <div>
                            <h3 class="encuesta-card-title">Nueva Encuesta</h3>
                            <p class="encuesta-card-subtitle">Publica una pregunta y define opciones de respuesta.</p>
                        </div>
                    </div>
                </div>

                <div class="encuesta-card-body">
                    <form id="form-encuesta">
                        @csrf

                        <div class="form-group">
                            <label class="form-label">Título de la encuesta *</label>
                            <div class="input-icon-wrapper">
                                <i class="heading icon"></i>
                                <input
                                    type="text"
                                    name="titulo"
                                    class="form-input encuesta-input input-with-icon"
                                    placeholder="Ej. ¿Qué color preferimos para la fachada?"
                                    autocomplete="off"
                                    required
                                >
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Descripción</label>
                            <textarea
                                name="descripcion"
                                class="form-input encuesta-input encuesta-textarea"
                                rows="3"
                                placeholder="Describe el contexto de la encuesta..."
                            ></textarea>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Disponible para *</label>
                            <select name="tipo_usuario" class="form-input encuesta-input" required>
                                <option value="todos">Todos los usuarios</option>
                                <option value="dueño">Solo propietarios</option>
                                <option value="inquilino">Solo inquilinos</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Tipo de encuesta *</label>
                            <select name="tipo" id="tipo-encuesta" class="form-input encuesta-input" required>
                                <option value="opcion">Opción (elegir respuesta)</option>
                                <option value="ordenamiento">Ordenamiento (priorizar por orden)</option>
                            </select>
                            <small class="form-help-text" id="tipo-help">
                                En "Ordenamiento" los vecinos arrastran las opciones para ordenarlas por prioridad (#1 = mayor).
                            </small>
                        </div>

                        <div class="form-group" id="grupo-multiple">
                            <div class="encuesta-check-card">
                                <div class="ui checkbox">
                                    <input type="checkbox" name="multiple" id="check-multiple">
                                    <label>Permitir múltiples respuestas por usuario</label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Fecha y hora de cierre</label>
                            <div class="encuesta-cierre-grid">
                                <div class="input-icon-wrapper">
                                    <i class="calendar alternate outline icon"></i>
                                    <input
                                        type="date"
                                        name="fecha_fin"
                                        id="fecha-fin"
                                        class="form-input encuesta-input input-with-icon date-input"
                                    >
                                </div>

                                <div class="input-icon-wrapper">
                                    <i class="clock outline icon"></i>
                                    <input
                                        type="time"
                                        name="hora_fin"
                                        id="hora-fin"
                                        class="form-input encuesta-input input-with-icon time-input"
                                        step="60"
                                        disabled
                                    >
                                </div>
                            </div>

                            <small class="form-help-text">
                                Opcional. Si no seleccionas fecha y hora, la encuesta permanecerá abierta hasta que la cierres manualmente.
                            </small>
                        </div>

                        <div class="form-group">
                            <label class="form-label" id="label-opciones">Opciones de respuesta *</label>

                            <div id="opciones-container" class="opciones-container">
                                <div class="opcion-row">
                                    <div class="input-icon-wrapper">
                                        <i class="circle outline icon"></i>
                                        <input type="text" class="form-input encuesta-input input-with-icon opcion-input" placeholder="Opción 1" required>
                                    </div>

                                    <button type="button" class="btn btn-danger btn-remove-opcion" style="display: none;">
                                        <i class="times icon"></i>
                                    </button>
                                </div>

                                <div class="opcion-row">
                                    <div class="input-icon-wrapper">
                                        <i class="circle outline icon"></i>
                                        <input type="text" class="form-input encuesta-input input-with-icon opcion-input" placeholder="Opción 2" required>
                                    </div>

                                    <button type="button" class="btn btn-danger btn-remove-opcion" style="display: none;">
                                        <i class="times icon"></i>
                                    </button>
                                </div>
                            </div>

                            <button type="button" id="btn-add-opcion" class="btn btn-secondary btn-add-opcion">
                                <i class="plus icon"></i>
                                Agregar opción
                            </button>
                        </div>

                        <button id="btn-crear" class="btn btn-primary btn-lg btn-submit-encuesta" type="submit">
                            <i class="paper plane icon"></i>
                            Publicar encuesta
                        </button>
                    </form>
                </div>
            </div>

            <div class="encuestas-list-card">
                <div class="encuesta-card-header list-header">
                    <div class="encuesta-card-title-wrap">
                        <div class="encuesta-card-icon secondary">
                            <i class="list icon"></i>
                        </div>

                        <div>
                            <h3 class="encuesta-card-title">Encuestas Creadas</h3>
                            <p class="encuesta-card-subtitle">Consulta resultados, estados y administra encuestas publicadas.</p>
                        </div>
                    </div>
                </div>

                <div class="encuestas-toolbar">
                    <div class="filter-field">
                        <label class="form-label">Buscar encuesta</label>
                        <div class="ui icon input encuestas-search-input">
                            <i class="search icon"></i>
                            <input
                                type="text"
                                id="buscar-encuesta"
                                placeholder="Título, tipo, estado o respuestas..."
                                autocomplete="off"
                            >
                        </div>
                    </div>

                    <div class="filter-field">
                        <label class="form-label">Estado</label>
                        <select id="filtro-estado-encuesta" class="ui fluid dropdown encuesta-input">
                            <option value="todos">Todos</option>
                            <option value="abierta">Abierta</option>
                            <option value="cerrada">Cerrada</option>
                        </select>
                    </div>

                    <div class="filter-field">
                        <label class="form-label">Disponible para</label>
                        <select id="filtro-tipo-encuesta" class="ui fluid dropdown encuesta-input">
                            <option value="todos">Todos</option>
                            <option value="dueño">Propietarios</option>
                            <option value="inquilino">Inquilinos</option>
                            <option value="usuarios">Todos los usuarios</option>
                        </select>
                    </div>

                    <button type="button" class="btn btn-secondary btn-clear-filters" onclick="limpiarFiltrosEncuestas()">
                        <i class="times icon"></i>
                        Limpiar
                    </button>
                </div>

                <div class="encuestas-summary">
                    <div class="summary-item">
                        <div class="summary-icon">
                            <i class="chart pie icon"></i>
                        </div>

                        <div>
                            <span>Encuestas encontradas</span>
                            <strong id="total-encuestas">0</strong>
                        </div>
                    </div>
                </div>

                <div id="encuestas-loader" class="encuestas-loader">
                    <div class="ui active centered inline text loader large">Cargando encuestas...</div>
                </div>

                <div id="encuestas-container" class="encuestas-grid"></div>

                <div id="encuestas-pagination" class="encuestas-pagination" style="display: none;"></div>

                <div id="sin-encuestas" class="empty-encuestas-card" style="display: none;">
                    <div class="empty-encuestas-icon">
                        <i class="chart pie icon"></i>
                    </div>

                    <h3>No hay encuestas creadas</h3>
                    <p>Crea tu primera encuesta usando el formulario.</p>
                </div>

                <div id="sin-resultados" class="empty-encuestas-card" style="display: none;">
                    <div class="empty-encuestas-icon">
                        <i class="search icon"></i>
                    </div>

                    <h3>No se encontraron resultados</h3>
                    <p>No hay encuestas que coincidan con tu búsqueda o filtros.</p>

                    <button type="button" class="btn btn-secondary btn-sm" onclick="limpiarFiltrosEncuestas()">
                        <i class="undo icon"></i>
                        Limpiar filtros
                    </button>
                </div>

                <div id="encuestas-error" class="empty-encuestas-card error" style="display: none;">
                    <div class="empty-encuestas-icon error">
                        <i class="warning sign icon"></i>
                    </div>

                    <h3>Error al cargar encuestas</h3>
                    <p>No se pudieron cargar las encuestas. Intenta nuevamente.</p>

                    <button type="button" class="btn btn-primary btn-sm" onclick="cargarEncuestas()">
                        <i class="refresh icon"></i>
                        Reintentar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="ui modal encuesta-resultados-modal" id="modal-resultados">
        <div class="header">
            <i class="chart pie icon"></i>
            Resultados de la Encuesta
        </div>

        <div class="content" id="resultados-content"></div>

        <div class="actions encuesta-modal-actions">
            <div class="ui cancel button btn-modal-cancel">Cerrar</div>
        </div>
    </div>

    <div class="ui modal encuesta-resultados-modal" id="modal-votantes">
        <div class="header" id="votantes-titulo">
            <i class="users icon"></i>
            Votantes
        </div>

        <div class="content" id="votantes-content"></div>

        <div class="actions encuesta-modal-actions">
            <div class="ui cancel button btn-modal-cancel">Cerrar</div>
        </div>
    </div>

    <style>
        :root {
            --encuesta-primary: #8b5cf6;
            --encuesta-primary-dark: #7c3aed;
            --encuesta-secondary: #667eea;
            --encuesta-success: #10b981;
            --encuesta-danger: #ef4444;
            --encuesta-warning: #f59e0b;
            --encuesta-text-main: #0f172a;
            --encuesta-text-muted: #64748b;
            --encuesta-text-soft: #94a3b8;
            --encuesta-border: #e2e8f0;
            --encuesta-surface: #ffffff;
            --encuesta-soft-bg: #f8fafc;
        }

        .encuestas-admin-page {
            width: 100%;
            padding-bottom: 24px;
        }

        .encuestas-hero {
            background:
                radial-gradient(circle at top right, rgba(255,255,255,0.24), transparent 34%),
                radial-gradient(circle at bottom left, rgba(255,255,255,0.14), transparent 30%),
                linear-gradient(135deg, var(--encuesta-primary) 0%, var(--encuesta-primary-dark) 100%);
            border-radius: 26px;
            padding: 26px;
            color: white;
            margin-bottom: 22px;
            box-shadow: 0 12px 30px rgba(124, 58, 237, 0.18);
            overflow: hidden;
        }

        .encuestas-hero-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 18px;
            flex-wrap: wrap;
        }

        .encuestas-hero-left {
            display: flex;
            align-items: center;
            gap: 16px;
            min-width: 0;
        }

        .encuestas-hero-icon {
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

        .encuestas-hero-icon i {
            color: white;
            font-size: 1.8rem;
            margin: 0 !important;
        }

        .encuestas-title {
            margin: 0 0 6px 0;
            font-size: clamp(1.55rem, 3vw, 2.15rem);
            font-weight: 900;
            letter-spacing: -0.035em;
            line-height: 1.08;
        }

        .encuestas-subtitle {
            margin: 0;
            opacity: 0.92;
            font-size: 1rem;
            line-height: 1.45;
        }

        .encuestas-hero-pill {
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

        .encuestas-hero-pill i {
            margin: 0 !important;
        }

        .encuestas-layout {
            display: grid;
            grid-template-columns: minmax(300px, 430px) minmax(0, 1fr);
            gap: 24px;
            align-items: start;
        }

        .encuesta-form-card,
        .encuestas-list-card,
        .empty-encuestas-card {
            background: var(--encuesta-surface);
            border: 1px solid var(--encuesta-border);
            border-radius: 22px;
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.06);
            overflow: hidden;
        }

        .encuesta-form-card {
            position: sticky;
            top: 18px;
        }

        .encuesta-card-header {
            padding: 18px 20px;
            border-bottom: 1px solid #f1f5f9;
            background: linear-gradient(180deg, #ffffff 0%, #fbfdff 100%);
        }

        .encuesta-card-title-wrap {
            display: flex;
            align-items: center;
            gap: 14px;
            min-width: 0;
        }

        .encuesta-card-icon {
            width: 52px;
            height: 52px;
            border-radius: 16px;
            background: linear-gradient(135deg, var(--encuesta-primary) 0%, var(--encuesta-primary-dark) 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            box-shadow: 0 8px 18px rgba(139, 92, 246, 0.24);
        }

        .encuesta-card-icon.secondary {
            background: linear-gradient(135deg, var(--encuesta-secondary) 0%, var(--encuesta-primary-dark) 100%);
        }

        .encuesta-card-icon i {
            margin: 0 !important;
            font-size: 1.25rem;
        }

        .encuesta-card-title {
            margin: 0;
            color: var(--encuesta-text-main);
            font-size: 1.12rem;
            font-weight: 900;
            line-height: 1.25;
        }

        .encuesta-card-subtitle {
            margin: 4px 0 0 0;
            color: var(--encuesta-text-muted);
            font-size: 0.9rem;
            line-height: 1.35;
        }

        .encuesta-card-body {
            padding: 20px;
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-label {
            display: block;
            color: var(--encuesta-text-main);
            font-weight: 800;
            margin-bottom: 8px;
            font-size: 0.9rem;
        }

        .encuesta-input {
            min-height: 46px;
            border-radius: 14px !important;
            width: 100%;
            max-width: 100%;
            box-sizing: border-box;
            border: 1px solid var(--encuesta-border) !important;
            transition: border-color 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
        }

        .encuesta-input:focus {
            border-color: rgba(139, 92, 246, 0.65) !important;
            box-shadow: 0 0 0 4px rgba(139, 92, 246, 0.12) !important;
            outline: none;
            background: white;
        }

        .encuesta-textarea {
            resize: vertical;
            min-height: 98px;
            line-height: 1.5;
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
            color: var(--encuesta-primary-dark);
            pointer-events: none;
            z-index: 1;
            margin: 0 !important;
        }

        .input-with-icon {
            padding-left: 42px !important;
        }

        .form-help-text {
            display: block;
            color: var(--encuesta-text-soft);
            font-size: 0.8rem;
            margin-top: 6px;
            line-height: 1.35;
        }

        .encuesta-cierre-grid {
            display: grid;
            grid-template-columns: 1fr minmax(140px, 180px);
            gap: 10px;
        }

        .encuesta-cierre-grid .input-icon-wrapper {
            margin-bottom: 0;
        }

        .time-input:disabled {
            background-color: #f5f5f5;
            color: #aaa;
            cursor: not-allowed;
        }

        @media (max-width: 520px) {
            .encuesta-cierre-grid {
                grid-template-columns: 1fr;
            }
        }

        .encuesta-check-card {
            background: var(--encuesta-soft-bg);
            border: 1px solid #eef2f7;
            border-radius: 14px;
            padding: 14px;
        }

        .opciones-container {
            display: grid;
            gap: 10px;
        }

        .opcion-row {
            display: grid;
            grid-template-columns: minmax(0, 1fr) 46px;
            gap: 8px;
            align-items: center;
        }

        .btn-remove-opcion {
            width: 46px;
            min-width: 46px;
            height: 46px;
            border-radius: 14px !important;
            display: inline-flex !important;
            align-items: center;
            justify-content: center;
            padding: 0 !important;
        }

        .btn-remove-opcion i {
            margin: 0 !important;
        }

        .btn-add-opcion {
            margin-top: 10px;
            min-height: 44px;
            width: 100%;
            border-radius: 14px;
            display: inline-flex !important;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-weight: 900;
        }

        .btn-add-opcion i {
            margin: 0 !important;
        }

        .btn-submit-encuesta {
            width: 100%;
            min-height: 48px;
            border-radius: 14px;
            margin-top: 12px;
            display: inline-flex !important;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-weight: 900;
            box-shadow: 0 8px 18px rgba(139, 92, 246, 0.22);
        }

        .btn-submit-encuesta i {
            margin: 0 !important;
        }

        .encuestas-toolbar {
            padding: 18px 20px;
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(160px, 200px) minmax(180px, 220px) auto;
            gap: 14px;
            align-items: end;
            border-bottom: 1px solid #f1f5f9;
        }

        .filter-field {
            min-width: 0;
        }

        .encuestas-search-input {
            width: 100%;
        }

        .encuestas-search-input input {
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

        .encuestas-summary {
            padding: 16px 20px 0;
        }

        .summary-item {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            background: var(--encuesta-soft-bg);
            border: 1px solid #eef2f7;
            border-radius: 16px;
            padding: 12px 14px;
        }

        .summary-icon {
            width: 42px;
            height: 42px;
            border-radius: 14px;
            background: #ede9fe;
            color: var(--encuesta-primary-dark);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .summary-icon i {
            margin: 0 !important;
        }

        .summary-item span {
            display: block;
            color: var(--encuesta-text-muted);
            font-size: 0.78rem;
            font-weight: 800;
            margin-bottom: 2px;
        }

        .summary-item strong {
            display: block;
            color: var(--encuesta-text-main);
            font-size: 1.2rem;
            line-height: 1;
        }

        .encuestas-loader {
            min-height: 220px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
        }

        .encuestas-grid {
            padding: 20px;
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 16px;
        }

        .encuesta-item-card {
            border: 1px solid var(--encuesta-border);
            border-radius: 18px;
            background: white;
            box-shadow: 0 6px 18px rgba(15, 23, 42, 0.05);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
        }

        .encuesta-item-card:hover {
            transform: translateY(-2px);
            border-color: rgba(139, 92, 246, 0.35);
            box-shadow: 0 10px 24px rgba(15, 23, 42, 0.08);
        }

        .encuesta-item-header {
            padding: 16px;
            border-bottom: 1px solid #f1f5f9;
            display: flex;
            align-items: flex-start;
            gap: 12px;
        }

        .encuesta-item-icon {
            width: 46px;
            height: 46px;
            border-radius: 14px;
            background: #ede9fe;
            color: var(--encuesta-primary-dark);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .encuesta-item-icon i {
            margin: 0 !important;
            font-size: 1.15rem;
        }

        .encuesta-item-title {
            margin: 0;
            color: var(--encuesta-text-main);
            font-size: 1rem;
            font-weight: 900;
            line-height: 1.3;
            overflow-wrap: anywhere;
        }

        .encuesta-item-subtitle {
            margin-top: 4px;
            color: var(--encuesta-text-muted);
            font-size: 0.84rem;
            line-height: 1.35;
            overflow-wrap: anywhere;
        }

        .encuesta-badges {
            display: flex;
            flex-wrap: wrap;
            gap: 7px;
            margin-top: 9px;
        }

        .encuesta-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 6px 9px;
            border-radius: 999px;
            font-size: 0.72rem;
            font-weight: 900;
            line-height: 1;
        }

        .encuesta-badge i {
            margin: 0 !important;
        }

        .encuesta-badge.abierta {
            background: #dcfce7;
            color: #166534;
        }

        .encuesta-badge.cerrada {
            background: #fee2e2;
            color: #991b1b;
        }

        .encuesta-badge.tipo {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .encuesta-badge.votos {
            background: #fef3c7;
            color: #92400e;
        }

        .encuesta-item-body {
            padding: 16px;
            flex: 1;
            display: grid;
            gap: 10px;
        }

        .encuesta-meta-item {
            display: flex;
            align-items: flex-start;
            gap: 9px;
            color: var(--encuesta-text-muted);
            font-size: 0.88rem;
            line-height: 1.4;
            overflow-wrap: anywhere;
        }

        .encuesta-meta-item i {
            color: var(--encuesta-primary-dark);
            margin: 2px 0 0 0 !important;
            flex-shrink: 0;
        }

        .encuesta-item-footer {
            padding: 14px 16px 16px;
            border-top: 1px solid #f1f5f9;
            background: #fbfdff;
        }

        .encuesta-actions {
            width: 100%;
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px;
        }

        .encuesta-actions .ui.buttons,
        .encuesta-actions .ui.tiny.buttons {
            display: contents !important;
        }

        .encuesta-actions .btn,
        .encuesta-actions button,
        .encuesta-actions a,
        .encuesta-actions .ui.button {
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
            font-size: 0.82rem;
            font-weight: 900;
            line-height: 1.1;
            white-space: normal;
            overflow-wrap: anywhere;
            text-align: center;
        }

        .encuesta-actions i {
            margin: 0 !important;
            flex-shrink: 0;
        }

        .encuesta-actions .btn-ver {
            background: #eff6ff !important;
            color: #1d4ed8 !important;
            border: 1px solid #bfdbfe !important;
        }

        .encuesta-actions .btn-cerrar {
            background: #fef3c7 !important;
            color: #92400e !important;
            border: 1px solid #fde68a !important;
        }

        .encuesta-actions .btn-abrir {
            background: #dcfce7 !important;
            color: #166534 !important;
            border: 1px solid #bbf7d0 !important;
        }

        .encuesta-actions .btn-eliminar {
            background: #fef2f2 !important;
            color: #b91c1c !important;
            border: 1px solid #fecaca !important;
        }

        .encuestas-pagination {
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
            border: 1px solid var(--encuesta-border);
            background: #ffffff;
            color: var(--encuesta-text-muted);
            border-radius: 12px;
            font-weight: 900;
            cursor: pointer;
        }

        .pagination-btn:hover {
            background: #f5f3ff;
            color: var(--encuesta-primary-dark);
            border-color: rgba(139, 92, 246, 0.35);
        }

        .pagination-btn.active {
            background: linear-gradient(135deg, var(--encuesta-primary) 0%, var(--encuesta-primary-dark) 100%);
            color: #ffffff;
            border-color: transparent;
            box-shadow: 0 8px 18px rgba(139, 92, 246, 0.22);
        }

        .pagination-btn:disabled {
            opacity: 0.45;
            cursor: not-allowed;
            background: var(--encuesta-soft-bg);
            color: var(--encuesta-text-soft);
        }

        .pagination-info {
            color: var(--encuesta-text-muted);
            font-size: 0.86rem;
            font-weight: 800;
            margin: 0 8px;
        }

        .empty-encuestas-card {
            margin: 20px;
            padding: 44px 20px;
            text-align: center;
        }

        .empty-encuestas-icon {
            width: 78px;
            height: 78px;
            border-radius: 24px;
            margin: 0 auto 18px;
            background: linear-gradient(135deg, rgba(139, 92, 246, 0.12), rgba(124, 58, 237, 0.16));
            color: var(--encuesta-primary-dark);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .empty-encuestas-icon.error {
            background: #fee2e2;
            color: var(--encuesta-danger);
        }

        .empty-encuestas-icon i {
            font-size: 2rem;
            margin: 0 !important;
        }

        .empty-encuestas-card h3 {
            margin: 0 0 8px 0;
            color: #475569;
            font-weight: 900;
        }

        .empty-encuestas-card p {
            margin: 0 0 18px 0;
            color: var(--encuesta-text-soft);
            line-height: 1.5;
        }

        .encuesta-resultados-modal {
            border-radius: 20px !important;
            overflow: hidden;
        }

        .encuesta-resultados-modal > .header {
            background: linear-gradient(135deg, var(--encuesta-primary) 0%, var(--encuesta-primary-dark) 100%) !important;
            color: white !important;
            font-weight: 900 !important;
        }

        .encuesta-resultados-modal > .header i {
            margin-right: 8px !important;
        }

        .encuesta-modal-actions {
            display: flex !important;
            justify-content: flex-end;
            gap: 10px;
            flex-wrap: wrap;
            padding: 16px 20px !important;
            background: var(--encuesta-soft-bg) !important;
            border-top: 1px solid #f1f5f9 !important;
        }

        .resultado-title {
            margin: 0 0 8px 0;
            color: var(--encuesta-text-main);
            font-weight: 900;
            overflow-wrap: anywhere;
        }

        .resultado-description {
            margin: 0 0 14px 0;
            color: var(--encuesta-text-muted);
            line-height: 1.5;
            overflow-wrap: anywhere;
        }

        .resultado-summary {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 18px;
        }

        .resultado-option {
            padding: 12px 0;
            border-bottom: 1px solid #eef2f7;
        }

        .resultado-option-header {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 8px;
            color: var(--encuesta-text-main);
            font-size: 0.9rem;
        }

        .resultado-progress {
            height: 12px;
            background: #f1f5f9;
            border-radius: 999px;
            overflow: hidden;
        }

        .resultado-progress-bar {
            height: 100%;
            background: linear-gradient(90deg, var(--encuesta-primary), var(--encuesta-primary-dark));
            border-radius: 999px;
        }

        @media (max-width: 1200px) {
            .encuestas-layout {
                grid-template-columns: 1fr;
            }

            .encuesta-form-card {
                position: static;
            }

            .encuestas-toolbar {
                grid-template-columns: minmax(0, 1fr) minmax(160px, 1fr);
            }

            .btn-clear-filters {
                grid-column: 1 / -1;
                width: 100%;
            }
        }

        @media (max-width: 900px) {
            .encuestas-grid {
                grid-template-columns: 1fr;
            }

            .encuestas-toolbar {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .encuestas-hero {
                border-radius: 0 0 24px 24px;
                margin: -8px -4px 18px;
                padding: 22px;
            }

            .encuestas-hero-content {
                align-items: stretch;
            }

            .encuestas-hero-left {
                align-items: flex-start;
            }

            .encuestas-hero-icon {
                width: 54px;
                height: 54px;
                border-radius: 18px;
            }

            .encuestas-title {
                font-size: 1.55rem;
            }

            .encuestas-subtitle {
                font-size: 0.92rem;
            }

            .encuestas-hero-pill {
                width: 100%;
                justify-content: center;
                border-radius: 16px;
            }

            .encuesta-form-card,
            .encuestas-list-card,
            .empty-encuestas-card {
                border-radius: 18px;
            }

            .encuesta-card-header,
            .encuesta-card-body,
            .encuestas-toolbar {
                padding: 16px;
            }

            .encuesta-card-icon {
                width: 46px;
                height: 46px;
                border-radius: 14px;
            }

            .encuesta-card-title {
                font-size: 1.04rem;
            }

            .encuesta-card-subtitle {
                font-size: 0.85rem;
            }

            .opcion-row {
                grid-template-columns: minmax(0, 1fr) 46px;
            }

            .encuestas-summary {
                padding: 14px 16px 0;
            }

            .encuestas-grid {
                padding: 16px;
            }

            .encuesta-item-card:hover {
                transform: none;
            }

            .encuesta-item-footer {
                padding: 12px;
                background: #ffffff;
            }

            .encuesta-actions {
                grid-template-columns: 1fr;
                gap: 8px;
            }

            .encuesta-actions .btn,
            .encuesta-actions button,
            .encuesta-actions a,
            .encuesta-actions .ui.button {
                min-height: 48px;
                font-size: 0.88rem;
            }

            .encuestas-pagination {
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

            input[type="text"],
            input[type="date"],
            textarea,
            select {
                font-size: 16px !important;
            }

            .resultado-option-header {
                flex-direction: column;
                gap: 4px;
            }
        }

        @media (max-width: 420px) {
            .encuestas-hero-left {
                gap: 12px;
            }

            .encuesta-card-title-wrap,
            .encuesta-item-header {
                align-items: flex-start;
            }

            .encuesta-item-icon {
                width: 42px;
                height: 42px;
                border-radius: 13px;
            }

            .encuesta-item-title {
                font-size: 0.98rem;
            }

            .encuesta-badge {
                max-width: 100%;
                white-space: normal;
                line-height: 1.2;
            }

            .opcion-row {
                grid-template-columns: 1fr;
            }

            .btn-remove-opcion {
                width: 100%;
            }
        }
    </style>

    <script>
        const BASE_URL = "{{ url('/') }}";

        let encuestasOriginales = [];
        let encuestasFiltradas = [];
        let paginaActualEncuestas = 1;
        let encuestasPorPagina = 6;
        let isSubmittingEncuesta = false;

        $(document).ready(function() {
            $('.ui.checkbox').checkbox();
            $('#filtro-estado-encuesta').dropdown();
            $('#filtro-tipo-encuesta').dropdown();
            $('#modal-resultados').modal();
            $('#modal-votantes').modal();

            configurarFechaHoraCierre();
            cargarEncuestas();

            $('#buscar-encuesta').on('keyup input change', debounce(function() {
                filtrarEncuestas();
            }, 250));

            $('#filtro-estado-encuesta, #filtro-tipo-encuesta').on('change', function() {
                filtrarEncuestas();
            });

            $('#btn-add-opcion').click(function(e) {
                e.preventDefault();
                agregarOpcionEncuesta();
            });

            // Adaptar el formulario según el tipo de encuesta.
            $('#tipo-encuesta').on('change', function() {
                const esOrden = $(this).val() === 'ordenamiento';
                $('#grupo-multiple').toggle(!esOrden);
                if (esOrden) {
                    $('#check-multiple').prop('checked', false);
                    $('#label-opciones').text('Proyectos a ordenar *');
                    $('#btn-add-opcion').html('<i class="plus icon"></i> Agregar proyecto');
                    $('#tipo-help').text('Los vecinos arrastrarán estos proyectos para ordenarlos por prioridad (#1 = mayor).');
                } else {
                    $('#label-opciones').text('Opciones de respuesta *');
                    $('#btn-add-opcion').html('<i class="plus icon"></i> Agregar opción');
                    $('#tipo-help').text('En "Ordenamiento" los vecinos arrastran las opciones para ordenarlas por prioridad (#1 = mayor).');
                }
            }).trigger('change');

            $(document).on('click', '.btn-remove-opcion', function() {
                $(this).closest('.opcion-row').remove();
                actualizarBotonesRemoverOpciones();
            });

            $('#form-encuesta').on('submit', function(e) {
                e.preventDefault();

                if (isSubmittingEncuesta) {
                    return false;
                }

                const btn = $('#btn-crear');
                const btnOriginalText = btn.html();

                btn.html('<i class="notched circle loading icon"></i> Creando...');
                btn.prop('disabled', true);
                isSubmittingEncuesta = true;

                const opciones = [];

                $('.opcion-input').each(function() {
                    const val = $(this).val().trim();

                    if (val) {
                        opciones.push(val);
                    }
                });

                if (opciones.length < 2) {
                    alertify.error('Agrega al menos 2 opciones');
                    btn.html(btnOriginalText);
                    btn.prop('disabled', false);
                    isSubmittingEncuesta = false;
                    return;
                }

                if (!validarFechaHoraCierre()) {
                    alertify.error('La fecha y hora de cierre deben ser posteriores al momento actual.');
                    btn.html(btnOriginalText);
                    btn.prop('disabled', false);
                    isSubmittingEncuesta = false;
                    return;
                }

                $.ajax({
                    url: '{{ route("admin.encuesta.crear") }}',
                    type: 'POST',
                    data: {
                        _token: $('input[name="_token"]').val(),
                        titulo: $('input[name="titulo"]').val(),
                        descripcion: $('textarea[name="descripcion"]').val(),
                        tipo_usuario: $('select[name="tipo_usuario"]').val(),
                        tipo: $('#tipo-encuesta').val(),
                        multiple: $('#check-multiple').is(':checked') ? 1 : 0,
                        fecha_fin: $('input[name="fecha_fin"]').val(),
                        hora_fin: $('input[name="hora_fin"]').val(),
                        opciones: opciones
                    },
                    success: function(response) {
                        if (response.success) {
                            alertify.success(response.message);
                            resetFormularioEncuesta();
                            cargarEncuestas();
                        } else {
                            alertify.error(response.message);
                        }
                    },
                    error: function() {
                        alertify.error('Error al crear la encuesta');
                    },
                    complete: function() {
                        btn.html(btnOriginalText);
                        btn.prop('disabled', false);
                        isSubmittingEncuesta = false;
                    }
                });
            });
        });

        function establecerFechaMinimaEncuesta() {
            const today = new Date();
            const year = today.getFullYear();
            const month = String(today.getMonth() + 1).padStart(2, '0');
            const day = String(today.getDate()).padStart(2, '0');
            const todayStr = year + '-' + month + '-' + day;

            const fechaInput = document.getElementById('fecha-fin');

            if (fechaInput) {
                fechaInput.min = todayStr;
            }
        }

        function configurarFechaHoraCierre() {
            const fechaInput = document.getElementById('fecha-fin');
            const horaInput = document.getElementById('hora-fin');

            if (!fechaInput || !horaInput) {
                return;
            }

            establecerFechaMinimaEncuesta();

            horaInput.disabled = true;
            horaInput.required = false;

            fechaInput.addEventListener('change', function() {
                actualizarHoraMinimaEncuesta();
            });

            horaInput.addEventListener('change', function() {
                validarFechaHoraCierre();
            });
        }

        function actualizarHoraMinimaEncuesta() {
            const fechaInput = document.getElementById('fecha-fin');
            const horaInput = document.getElementById('hora-fin');

            if (!fechaInput || !horaInput) {
                return;
            }

            const fechaSeleccionada = fechaInput.value;
            const fechaHoy = obtenerFechaLocal(new Date());

            horaInput.value = '';

            if (!fechaSeleccionada) {
                horaInput.disabled = true;
                horaInput.required = false;
                horaInput.removeAttribute('min');
                return;
            }

            horaInput.disabled = false;
            horaInput.required = true;

            if (fechaSeleccionada === fechaHoy) {
                const ahora = new Date();
                const minutos = ahora.getMinutes() + 1;
                const horas = ahora.getHours();
                const minutosStr = String(minutos).padStart(2, '0');
                const horasStr = String(horas).padStart(2, '0');
                horaInput.min = horasStr + ':' + minutosStr;
            } else {
                horaInput.removeAttribute('min');
            }
        }

        function validarFechaHoraCierre() {
            const fechaInput = document.getElementById('fecha-fin');
            const horaInput = document.getElementById('hora-fin');

            if (!fechaInput || !horaInput) {
                return false;
            }

            const fechaSeleccionada = fechaInput.value;
            const horaSeleccionada = horaInput.value;

            if (!fechaSeleccionada && !horaSeleccionada) {
                return true;
            }

            if (!fechaSeleccionada || !horaSeleccionada) {
                alertify.warning('Si defines una fecha y hora de cierre, ambas son obligatorias.');
                return false;
            }

            const fechaHora = new Date(fechaSeleccionada + 'T' + horaSeleccionada);
            const ahora = new Date();

            if (fechaHora <= ahora) {
                alertify.warning('La fecha y hora de cierre deben ser posteriores al momento actual.');
                horaInput.value = '';
                return false;
            }

            return true;
        }

        function obtenerFechaLocal(fecha) {
            if (!(fecha instanceof Date) || isNaN(fecha)) {
                return '';
            }

            const year = fecha.getFullYear();
            const month = String(fecha.getMonth() + 1).padStart(2, '0');
            const day = String(fecha.getDate()).padStart(2, '0');

            return year + '-' + month + '-' + day;
        }

        function agregarOpcionEncuesta() {
            const count = $('#opciones-container .opcion-row').length + 1;

            const row = `
                <div class="opcion-row">
                    <div class="input-icon-wrapper">
                        <i class="circle outline icon"></i>
                        <input type="text" class="form-input encuesta-input input-with-icon opcion-input" placeholder="Opción ${count}" required>
                    </div>

                    <button type="button" class="btn btn-danger btn-remove-opcion">
                        <i class="times icon"></i>
                    </button>
                </div>
            `;

            $('#opciones-container').append(row);
            actualizarBotonesRemoverOpciones();
        }

        function actualizarBotonesRemoverOpciones() {
            const count = $('#opciones-container .opcion-row').length;

            $('.btn-remove-opcion').hide();

            if (count > 2) {
                $('.btn-remove-opcion').show();
            }
        }

        function resetFormularioEncuesta() {
            $('#form-encuesta')[0].reset();
            $('.ui.checkbox').checkbox('uncheck');

            $('#hora-fin')
                .prop('disabled', true)
                .prop('required', false)
                .val('')
                .removeAttr('min');

            $('#opciones-container').html(`
                <div class="opcion-row">
                    <div class="input-icon-wrapper">
                        <i class="circle outline icon"></i>
                        <input type="text" class="form-input encuesta-input input-with-icon opcion-input" placeholder="Opción 1" required>
                    </div>

                    <button type="button" class="btn btn-danger btn-remove-opcion" style="display: none;">
                        <i class="times icon"></i>
                    </button>
                </div>

                <div class="opcion-row">
                    <div class="input-icon-wrapper">
                        <i class="circle outline icon"></i>
                        <input type="text" class="form-input encuesta-input input-with-icon opcion-input" placeholder="Opción 2" required>
                    </div>

                    <button type="button" class="btn btn-danger btn-remove-opcion" style="display: none;">
                        <i class="times icon"></i>
                    </button>
                </div>
            `);

            establecerFechaMinimaEncuesta();
        }

        function cargarEncuestas() {
            $('#encuestas-loader').show();
            $('#encuestas-container').empty();
            $('#encuestas-pagination').hide().empty();
            $('#sin-encuestas').hide();
            $('#sin-resultados').hide();
            $('#encuestas-error').hide();

            $.ajax({
                url: '{{ route("admin.encuesta.obtener") }}',
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    $('#encuestas-loader').hide();

                    encuestasOriginales = ordenarEncuestas(response.data || []);
                    renderizarEncuestas(encuestasOriginales);
                },
                error: function() {
                    $('#encuestas-loader').hide();
                    $('#encuestas-error').show();
                }
            });
        }

        function ordenarEncuestas(encuestas) {
            return [...encuestas].sort(function(a, b) {
                const idA = Number(extraerTexto(a.id || obtenerIdEncuesta(a.acciones || '') || 0));
                const idB = Number(extraerTexto(b.id || obtenerIdEncuesta(b.acciones || '') || 0));

                return idB - idA;
            });
        }

        function renderizarEncuestas(encuestas) {
            encuestasFiltradas = encuestas;
            paginaActualEncuestas = 1;
            renderizarPaginaEncuestas();
        }

        function renderizarPaginaEncuestas() {
            const container = $('#encuestas-container');
            const pagination = $('#encuestas-pagination');

            container.empty();
            pagination.empty();

            $('#total-encuestas').text(encuestasFiltradas.length);

            $('#sin-encuestas').hide();
            $('#sin-resultados').hide();
            $('#encuestas-error').hide();

            if (encuestasOriginales.length === 0) {
                pagination.hide();
                $('#sin-encuestas').show();
                return;
            }

            if (encuestasFiltradas.length === 0) {
                pagination.hide();
                $('#sin-resultados').show();
                return;
            }

            const totalPaginas = Math.ceil(encuestasFiltradas.length / encuestasPorPagina);
            const inicio = (paginaActualEncuestas - 1) * encuestasPorPagina;
            const fin = inicio + encuestasPorPagina;
            const encuestasPagina = encuestasFiltradas.slice(inicio, fin);

            encuestasPagina.forEach(function(encuesta) {
                const id = extraerTexto(encuesta.id || obtenerIdEncuesta(encuesta.acciones || ''));
                const titulo = extraerTexto(encuesta.titulo || 'Encuesta sin título');
                const tipoHtml = encuesta.tipo_html || '';
                const tipoTexto = extraerTexto(tipoHtml || 'Sin tipo');
                const votos = extraerTexto(encuesta.votos || '0');
                const estadoHtml = encuesta.estado_html || '';
                const estadoTexto = extraerTexto(estadoHtml || 'Abierta');
                const estado = obtenerEstadoEncuesta(estadoTexto);
                const fechaCierre = extraerTexto(encuesta.fecha_cierre || 'Sin fecha límite');
                const accionesFallback = construirAccionesEncuesta(id, estado.valor);

                const card = `
                    <div class="encuesta-item-card">
                        <div class="encuesta-item-header">
                            <div class="encuesta-item-icon">
                                <i class="chart pie icon"></i>
                            </div>

                            <div style="min-width: 0; flex: 1;">
                                <h3 class="encuesta-item-title">${escapeHtml(titulo)}</h3>

                                <div class="encuesta-badges">
                                    <span class="encuesta-badge ${estado.clase}">
                                        <i class="${estado.icono} icon"></i>
                                        ${escapeHtml(estado.texto)}
                                    </span>

                                    <span class="encuesta-badge tipo">
                                        <i class="users icon"></i>
                                        ${escapeHtml(tipoTexto)}
                                    </span>

                                    <span class="encuesta-badge votos">
                                        <i class="chart bar outline icon"></i>
                                        ${escapeHtml(votos)} respuestas
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="encuesta-item-body">
                            <div class="encuesta-meta-item">
                                <i class="calendar alternate outline icon"></i>
                                <span><strong>Fecha y hora de cierre:</strong> ${escapeHtml(fechaCierre)} hrs.</span>
                            </div>
                        </div>

                        <div class="encuesta-item-footer">
                            <div class="encuesta-actions">
                                ${accionesFallback}
                            </div>
                        </div>
                    </div>
                `;

                container.append(card);
            });

            renderizarPaginacionEncuestas(totalPaginas, encuestasFiltradas.length);
        }

        function construirAccionesEncuesta(id, estado) {
            if (!id) {
                return `
                    <button type="button" class="btn btn-secondary btn-sm disabled" disabled>
                        <i class="info circle icon"></i>
                        Sin acciones
                    </button>
                `;
            }

            const toggleButton = estado === 'abierta'
                ? `
                    <button type="button" class="btn btn-secondary btn-sm btn-cerrar" data-id="${escapeHtml(id)}">
                        <i class="lock icon"></i>
                        Cerrar
                    </button>
                `
                : `
                    <button type="button" class="btn btn-secondary btn-sm btn-abrir" data-id="${escapeHtml(id)}">
                        <i class="unlock icon"></i>
                        Abrir
                    </button>
                `;

            return `
                <button type="button" class="btn btn-secondary btn-sm btn-ver" data-id="${escapeHtml(id)}">
                    <i class="chart pie icon"></i>
                    Resultados
                </button>

                ${toggleButton}

                <button type="button" class="btn btn-danger btn-sm btn-eliminar" data-id="${escapeHtml(id)}">
                    <i class="trash icon"></i>
                    Eliminar
                </button>
            `;
        }

        function obtenerIdEncuesta(html) {
            if (!html) {
                return '';
            }

            const temp = document.createElement('div');
            temp.innerHTML = html;

            const element = temp.querySelector('[data-id]');

            return element ? element.getAttribute('data-id') : '';
        }

        function obtenerEstadoEncuesta(valor) {
            const texto = normalizarTexto(extraerTexto(valor || ''));

            if (texto.includes('cerrada') || texto.includes('cerrado')) {
                return {
                    valor: 'cerrada',
                    texto: 'Cerrada',
                    clase: 'cerrada',
                    icono: 'lock'
                };
            }

            return {
                valor: 'abierta',
                texto: 'Abierta',
                clase: 'abierta',
                icono: 'unlock'
            };
        }

        function renderizarPaginacionEncuestas(totalPaginas, totalEncuestas) {
            const pagination = $('#encuestas-pagination');

            pagination.empty();

            if (totalPaginas <= 1) {
                pagination.hide();
                return;
            }

            const inicio = ((paginaActualEncuestas - 1) * encuestasPorPagina) + 1;
            const fin = Math.min(paginaActualEncuestas * encuestasPorPagina, totalEncuestas);

            pagination.show();

            pagination.append(`
                <button
                    type="button"
                    class="pagination-btn"
                    onclick="cambiarPaginaEncuestas(${paginaActualEncuestas - 1})"
                    ${paginaActualEncuestas === 1 ? 'disabled' : ''}
                >
                    <i class="chevron left icon"></i>
                </button>
            `);

            const paginas = obtenerPaginasVisiblesEncuestas(totalPaginas);

            paginas.forEach(function(pagina) {
                if (pagina === '...') {
                    pagination.append(`<span class="pagination-info">...</span>`);
                    return;
                }

                pagination.append(`
                    <button
                        type="button"
                        class="pagination-btn ${pagina === paginaActualEncuestas ? 'active' : ''}"
                        onclick="cambiarPaginaEncuestas(${pagina})"
                    >
                        ${pagina}
                    </button>
                `);
            });

            pagination.append(`
                <button
                    type="button"
                    class="pagination-btn"
                    onclick="cambiarPaginaEncuestas(${paginaActualEncuestas + 1})"
                    ${paginaActualEncuestas === totalPaginas ? 'disabled' : ''}
                >
                    <i class="chevron right icon"></i>
                </button>
            `);

            pagination.append(`
                <div class="pagination-info">
                    Mostrando ${inicio}-${fin} de ${totalEncuestas}
                </div>
            `);
        }

        function cambiarPaginaEncuestas(pagina) {
            const totalPaginas = Math.ceil(encuestasFiltradas.length / encuestasPorPagina);

            if (pagina < 1 || pagina > totalPaginas) {
                return;
            }

            paginaActualEncuestas = pagina;
            renderizarPaginaEncuestas();

            document.querySelector('.encuestas-list-card')?.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }

        function obtenerPaginasVisiblesEncuestas(totalPaginas) {
            const paginas = [];

            if (totalPaginas <= 5) {
                for (let i = 1; i <= totalPaginas; i++) {
                    paginas.push(i);
                }

                return paginas;
            }

            paginas.push(1);

            if (paginaActualEncuestas > 3) {
                paginas.push('...');
            }

            const inicio = Math.max(2, paginaActualEncuestas - 1);
            const fin = Math.min(totalPaginas - 1, paginaActualEncuestas + 1);

            for (let i = inicio; i <= fin; i++) {
                paginas.push(i);
            }

            if (paginaActualEncuestas < totalPaginas - 2) {
                paginas.push('...');
            }

            paginas.push(totalPaginas);

            return paginas;
        }

        function filtrarEncuestas() {
            const busqueda = normalizarTexto($('#buscar-encuesta').val());
            const estadoFiltro = $('#filtro-estado-encuesta').val();
            const tipoFiltro = $('#filtro-tipo-encuesta').val();

            const filtradas = encuestasOriginales.filter(function(encuesta) {
                const titulo = extraerTexto(encuesta.titulo || '');
                const tipoTexto = extraerTexto(encuesta.tipo_html || '');
                const votos = extraerTexto(encuesta.votos || '');
                const estadoTexto = extraerTexto(encuesta.estado_html || '');
                const estado = obtenerEstadoEncuesta(estadoTexto);

                const textoCompleto = normalizarTexto(
                    titulo + ' ' + tipoTexto + ' ' + votos + ' ' + estadoTexto
                );

                const tipoNormalizado = normalizarTexto(tipoTexto);

                const coincideBusqueda = textoCompleto.includes(busqueda);
                const coincideEstado = estadoFiltro === 'todos' || estado.valor === estadoFiltro;

                let coincideTipo = true;

                if (tipoFiltro === 'dueño') {
                    coincideTipo = tipoNormalizado.includes('dueno') || tipoNormalizado.includes('dueño') || tipoNormalizado.includes('propietario');
                }

                if (tipoFiltro === 'inquilino') {
                    coincideTipo = tipoNormalizado.includes('inquilino');
                }

                if (tipoFiltro === 'usuarios') {
                    coincideTipo = tipoNormalizado.includes('todos') || tipoNormalizado.includes('usuarios');
                }

                return coincideBusqueda && coincideEstado && coincideTipo;
            });

            renderizarEncuestas(ordenarEncuestas(filtradas));
        }

        function limpiarFiltrosEncuestas() {
            $('#buscar-encuesta').val('');
            $('#filtro-estado-encuesta').dropdown('set selected', 'todos');
            $('#filtro-tipo-encuesta').dropdown('set selected', 'todos');

            renderizarEncuestas(encuestasOriginales);
        }

        $(document).on('click', '.btn-ver', function() {
            const id = $(this).data('id');

            $.get('{{ url("administrador/encuesta/resultados") }}/' + id, function(response) {
                if (response.success) {
                    const data = response.encuesta;
                    const estado = data.estado === 'abierta'
                        ? '<span class="encuesta-badge abierta"><i class="unlock icon"></i> Abierta</span>'
                        : '<span class="encuesta-badge cerrada"><i class="lock icon"></i> Cerrada</span>';

                    let html = `
                        <h3 class="resultado-title">${escapeHtml(data.titulo || 'Encuesta')}</h3>
                        <p class="resultado-description">${escapeHtml(data.descripcion || 'Sin descripción')}</p>

                        <div class="resultado-summary">
                            ${estado}
                            <span class="encuesta-badge votos">
                                <i class="chart bar outline icon"></i>
                                ${escapeHtml(data.total_votos || 0)} ${data.tipo === 'ordenamiento' ? 'votantes' : 'votos'}
                            </span>
                        </div>
                    `;

                    if (data.tipo === 'ordenamiento') {
                        html += renderResultadosOrdenamiento(data, true);
                    } else {
                        window.encuestaResultados = data;
                        html += '<div class="resultado-options">';
                        data.opciones.forEach(function(opcion, idx) {
                            const porcentaje = Number(opcion.porcentaje || 0);
                            const votantesBtn = opcion.votos > 0
                                ? `<button type="button" class="btn btn-secondary btn-sm btn-ver-votantes" data-index="${idx}" style="margin-top:10px;">
                                    <i class="users icon"></i> Ver quién votó (${escapeHtml(opcion.respondieron ? opcion.respondieron.length : 0)})
                                   </button>`
                                : '';
                            html += `
                                <div class="resultado-option">
                                    <div class="resultado-option-header">
                                        <strong>${escapeHtml(opcion.opcion)}</strong>
                                        <span>${escapeHtml(opcion.votos)} votos (${escapeHtml(opcion.porcentaje)}%)</span>
                                    </div>
                                    <div class="resultado-progress">
                                        <div class="resultado-progress-bar" style="width: ${porcentaje}%;"></div>
                                    </div>
                                    ${votantesBtn}
                                </div>
                            `;
                        });
                        html += '</div>';
                    }

                    $('#resultados-content').html(html);
                    $('#modal-resultados').modal('show');
                }
            });
        });

        window.renderResultadosOrdenamiento = function(data, incluirVotantes) {
            const votantes = Number(data.total_votos || 0);
            const n = Number(data.num_opciones || (data.ranking ? data.ranking.length : 0));
            const ranking = data.ranking || [];

            let html = '<div style="margin-top:18px;">';
            html += '<h4 style="margin:0 0 4px; display:flex; align-items:center; gap:8px;"><i class="trophy icon" style="color:#0d9488;"></i> Orden ganador</h4>';
            html += `<p style="color:#64748b; font-size:13px; margin:0 0 14px;">${votantes} votante(s) · método de puntos (posición #1 = ${n} pts … #${n} = 1 pt)</p>`;

            ranking.forEach(function(fila, i) {
                const pct = Number(fila.pct || 0);
                html += `
                    <div style="margin-bottom:14px;">
                        <div style="display:flex; align-items:center; gap:10px; margin-bottom:6px;">
                            <span style="width:24px; height:24px; border-radius:50%; background:${i === 0 ? '#0d9488' : '#e2e8f0'}; color:${i === 0 ? '#fff' : '#0f172a'}; display:inline-flex; align-items:center; justify-content:center; font-weight:600; font-size:13px;">${i + 1}</span>
                            <strong style="flex:1;">${escapeHtml(fila.opcion)}</strong>
                            <span style="font-weight:600; font-size:13px;">${escapeHtml(fila.puntos)} pts</span>
                        </div>
                        <div style="height:8px; background:#f1f5f9; border-radius:99px; overflow:hidden;">
                            <div style="width:${pct}%; height:100%; background:#0d9488;"></div>
                        </div>
                        <p style="margin:4px 0 0; font-size:12px; color:#94a3b8;">${escapeHtml(fila.primeros || 0)} vecino(s) lo pusieron en #1</p>
                    </div>
                `;
            });

            // Matriz: cuántos vecinos pusieron cada opción en cada posición.
            html += '<h4 style="margin:22px 0 4px; display:flex; align-items:center; gap:8px;"><i class="th icon" style="color:#0d9488;"></i> Cómo votaron los vecinos</h4>';
            html += '<p style="color:#64748b; font-size:13px; margin:0 0 12px;">Cuántos vecinos colocaron cada proyecto en cada posición.</p>';
            html += '<div style="overflow-x:auto;"><table style="width:100%; border-collapse:separate; border-spacing:4px; font-size:13px;"><thead><tr><th style="text-align:left; color:#64748b; font-weight:600;"></th>';
            for (let p = 1; p <= n; p++) {
                html += `<th style="color:#64748b; font-weight:600; min-width:38px;">#${p}</th>`;
            }
            html += '</tr></thead><tbody>';

            ranking.forEach(function(fila) {
                html += `<tr><td style="text-align:left; padding-right:8px;">${escapeHtml(fila.opcion)}</td>`;
                for (let p = 1; p <= n; p++) {
                    const c = Number((fila.conteo_pos && fila.conteo_pos[p]) || 0);
                    const alpha = votantes > 0 ? (c / votantes) : 0;
                    const bg = `rgba(13,148,136,${(alpha * 0.85).toFixed(2)})`;
                    const color = alpha > 0.45 ? '#fff' : '#0f172a';
                    html += `<td style="text-align:center; padding:8px 0; border-radius:6px; background:${bg}; color:${color};">${c}</td>`;
                }
                html += '</tr>';
            });
            html += '</tbody></table></div>';

            // Detalle voto por voto (solo admin).
            if (incluirVotantes && Array.isArray(data.votantes) && data.votantes.length) {
                html += '<details style="margin-top:18px;"><summary style="cursor:pointer; font-weight:600; color:#0d9488;">Ver el orden que eligió cada vecino</summary>';
                html += '<div style="margin-top:10px; display:flex; flex-direction:column; gap:10px;">';
                data.votantes.forEach(function(v) {
                    const lista = (v.orden || []).map(function(op, idx) {
                        return `<span style="display:inline-block; margin:2px 4px 2px 0; font-size:12px;"><strong>${idx + 1}.</strong> ${escapeHtml(op)}</span>`;
                    }).join('');
                    html += `
                        <div style="border:1px solid #e2e8f0; border-radius:8px; padding:8px 12px;">
                            <div style="font-size:13px; font-weight:600; margin-bottom:4px;">${escapeHtml(v.nombre)} · Casa ${escapeHtml(v.casa)}</div>
                            <div>${lista}</div>
                        </div>
                    `;
                });
                html += '</div></details>';
            }

            html += '</div>';
            return html;
        };

        $(document).on('click', '.btn-ver-votantes', function() {
            const idx = $(this).data('index');
            const data = window.encuestaResultados;
            if (!data || !Array.isArray(data.opciones)) return;
            const opcion = data.opciones[idx];
            if (!opcion) return;

            const votantes = Array.isArray(opcion.respondieron) ? opcion.respondieron : [];

            let lista = '';
            if (!votantes.length) {
                lista = '<p style="color:#64748b;">Aún no hay votos registrados en esta opción.</p>';
            } else {
                lista = '<div style="display:flex; flex-direction:column; gap:8px;">';
                votantes.forEach(function(v) {
                    lista += `
                        <div style="border:1px solid #e2e8f0; border-radius:8px; padding:10px 12px; display:flex; align-items:center; justify-content:space-between; gap:10px;">
                            <span style="font-weight:600; font-size:14px; color:var(--encuesta-text-main);">${escapeHtml(v.nombre)}</span>
                            <span class="encuesta-badge votos"><i class="home icon"></i> Casa ${escapeHtml(v.casa)}</span>
                        </div>
                    `;
                });
                lista += '</div>';
            }

            $('#votantes-titulo').html(`<i class="users icon"></i> Votantes · ${escapeHtml(opcion.opcion)}`);
            $('#votantes-content').html(lista);
            $('#modal-votantes').modal('show');
        });

        $(document).on('click', '.btn-cerrar', function() {
            const id = $(this).data('id');

            alertify.confirm(
                'Cerrar encuesta',
                '¿Estás seguro de cerrar esta encuesta? Los usuarios ya no podrán cambiar su voto.',
                function() {
                    mostrarLoaderPantalla();

                    setTimeout(function() {
                        $.post('{{ url("administrador/encuesta/cerrar") }}/' + id, {
                            _token: '{{ csrf_token() }}'
                        }, function(response) {
                            if (response.success) {
                                alertify.success(response.message);
                                cargarEncuestas();
                            } else {
                                alertify.error(response.message);
                            }
                        }).always(function() {
                            ocultarLoaderPantalla();
                        });
                    }, 100);
                },
                function() {}
            );
        });

        $(document).on('click', '.btn-abrir', function() {
            const id = $(this).data('id');

            alertify.confirm(
                'Abrir encuesta',
                '¿Estás seguro de abrir esta encuesta nuevamente?',
                function() {
                    mostrarLoaderPantalla();

                    setTimeout(function() {
                        $.post('{{ url("administrador/encuesta/abrir") }}/' + id, {
                            _token: '{{ csrf_token() }}'
                        }, function(response) {
                            if (response.success) {
                                alertify.success(response.message);
                                cargarEncuestas();
                            } else {
                                alertify.error(response.message);
                            }
                        }).always(function() {
                            ocultarLoaderPantalla();
                        });
                    }, 100);
                },
                function() {}
            );
        });

        $(document).on('click', '.btn-eliminar', function() {
            const id = $(this).data('id');

            alertify.confirm(
                'Eliminar encuesta',
                '¿Estás seguro de eliminar esta encuesta? Esta acción no se puede deshacer.',
                function() {
                    mostrarLoaderPantalla();

                    setTimeout(function() {
                        $.ajax({
                            url: '{{ url("administrador/encuesta/eliminar") }}/' + id,
                            type: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                if (response.success) {
                                    alertify.success(response.message);
                                    cargarEncuestas();
                                } else {
                                    alertify.error(response.message);
                                }
                            },
                            complete: function() {
                                ocultarLoaderPantalla();
                            }
                        });
                    }, 100);
                },
                function() {}
            );
        });

        function debounce(callback, delay = 350) {
            let timer;

            return function() {
                clearTimeout(timer);

                const context = this;
                const args = arguments;

                timer = setTimeout(function() {
                    callback.apply(context, args);
                }, delay);
            };
        }

        function normalizarTexto(texto) {
            return String(texto || '')
                .toLowerCase()
                .normalize('NFD')
                .replace(/[\u0300-\u036f]/g, '')
                .trim();
        }

        function extraerTexto(valor) {
            const temporal = document.createElement('div');
            temporal.innerHTML = valor || '';
            return temporal.textContent || temporal.innerText || '';
        }

        function escapeHtml(text) {
            return String(text || '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }
    </script>
</x-app-layout>