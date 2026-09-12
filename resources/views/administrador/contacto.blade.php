<x-app-layout>
    <div class="contactos-admin-page">
        <div class="contactos-hero">
            <div class="contactos-hero-content">
                <div class="contactos-hero-left">
                    <div class="contactos-hero-icon">
                        <i class="address book icon"></i>
                    </div>

                    <div>
                        <h1 class="contactos-title">Gestión de Contactos</h1>
                        <p class="contactos-subtitle">Administra contactos de emergencia y recomendados</p>
                    </div>
                </div>

                <div class="contactos-hero-pill">
                    <i class="phone icon"></i>
                    Directorio vecinal
                </div>
            </div>
        </div>

        <div class="contactos-layout">
            <div class="contacto-form-card">
                <div class="contacto-card-header">
                    <div class="contacto-card-title-wrap">
                        <div class="contacto-card-icon">
                            <i class="plus circle icon"></i>
                        </div>

                        <div>
                            <h3 class="contacto-card-title">Nuevo Contacto</h3>
                            <p class="contacto-card-subtitle">Registra teléfonos útiles para los vecinos.</p>
                        </div>
                    </div>
                </div>

                <div class="contacto-card-body">
                    <form id="form-contacto">
                        @csrf

                        <div class="form-group">
                            <label class="form-label">Nombre del contacto *</label>
                            <div class="input-icon-wrapper">
                                <i class="user icon"></i>
                                <input
                                    type="text"
                                    name="nombre_contacto"
                                    class="form-input contacto-input input-with-icon"
                                    placeholder="Ej. Protección Civil"
                                    autocomplete="off"
                                    required
                                >
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Número de contacto *</label>
                            <div class="input-icon-wrapper">
                                <i class="phone icon"></i>
                                <input
                                    type="text"
                                    name="numero_contacto"
                                    class="form-input contacto-input input-with-icon"
                                    placeholder="10 dígitos"
                                    maxlength="10"
                                    autocomplete="off"
                                    inputmode="numeric"
                                    required
                                >
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Tipo de contacto *</label>
                            <select name="tipo_contacto" class="form-input contacto-input" required>
                                <option value="">Selecciona una opción</option>
                                <option value="emergencia">Línea de emergencia</option>
                                <option value="recomendado">Contacto recomendado</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Página web</label>
                            <div class="input-icon-wrapper">
                                <i class="globe icon"></i>
                                <input
                                    type="url"
                                    name="pagina_web"
                                    class="form-input contacto-input input-with-icon"
                                    placeholder="https://..."
                                    autocomplete="off"
                                >
                            </div>

                            <small class="form-help-text">
                                Opcional. Agrega el sitio oficial o una página de referencia.
                            </small>
                        </div>

                        <button id="btn-guardar" class="btn btn-primary btn-lg btn-submit-contacto" type="submit">
                            <i class="save icon"></i>
                            Guardar contacto
                        </button>
                    </form>
                </div>
            </div>

            <div class="contactos-list-card">
                <div class="contacto-card-header list-header">
                    <div class="contacto-card-title-wrap">
                        <div class="contacto-card-icon secondary">
                            <i class="list icon"></i>
                        </div>

                        <div>
                            <h3 class="contacto-card-title">Lista de Contactos</h3>
                            <p class="contacto-card-subtitle">Consulta, busca y administra los contactos registrados.</p>
                        </div>
                    </div>
                </div>

                <div class="contactos-toolbar">
                    <div class="filter-field">
                        <label class="form-label">Buscar contacto</label>
                        <div class="ui icon input contactos-search-input">
                            <i class="search icon"></i>
                            <input
                                type="text"
                                id="buscar-contacto"
                                placeholder="Nombre, número, tipo o página web..."
                                autocomplete="off"
                            >
                        </div>
                    </div>

                    <div class="filter-field">
                        <label class="form-label">Tipo</label>
                        <select id="filtro-tipo-contacto" class="ui fluid dropdown contacto-input">
                            <option value="todos">Todos</option>
                            <option value="emergencia">Línea de emergencia</option>
                            <option value="recomendado">Contacto recomendado</option>
                        </select>
                    </div>

                    <button type="button" class="btn btn-secondary btn-clear-filters" onclick="limpiarFiltrosContactos()">
                        <i class="times icon"></i>
                        Limpiar
                    </button>
                </div>

                <div class="contactos-summary">
                    <div class="summary-item">
                        <div class="summary-icon">
                            <i class="address book icon"></i>
                        </div>

                        <div>
                            <span>Contactos encontrados</span>
                            <strong id="total-contactos">0</strong>
                        </div>
                    </div>
                </div>

                <div id="contactos-loader" class="contactos-loader">
                    <div class="ui active centered inline text loader large">Cargando contactos...</div>
                </div>

                <div id="contactos-container" class="contactos-grid"></div>

                <div id="contactos-pagination" class="contactos-pagination" style="display: none;"></div>

                <div id="sin-contactos" class="empty-contactos-card" style="display: none;">
                    <div class="empty-contactos-icon">
                        <i class="address book outline icon"></i>
                    </div>

                    <h3>No hay contactos registrados</h3>
                    <p>Aún no se han agregado contactos de emergencia o recomendados.</p>
                </div>

                <div id="sin-resultados" class="empty-contactos-card" style="display: none;">
                    <div class="empty-contactos-icon">
                        <i class="search icon"></i>
                    </div>

                    <h3>No se encontraron resultados</h3>
                    <p>No hay contactos que coincidan con tu búsqueda o filtro.</p>

                    <button type="button" class="btn btn-secondary btn-sm" onclick="limpiarFiltrosContactos()">
                        <i class="undo icon"></i>
                        Limpiar filtros
                    </button>
                </div>

                <div id="contactos-error" class="empty-contactos-card error" style="display: none;">
                    <div class="empty-contactos-icon error">
                        <i class="warning sign icon"></i>
                    </div>

                    <h3>Error al cargar contactos</h3>
                    <p>No se pudieron cargar los contactos. Intenta nuevamente.</p>

                    <button type="button" class="btn btn-primary btn-sm" onclick="cargarContactos()">
                        <i class="refresh icon"></i>
                        Reintentar
                    </button>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<div class="ui modal contacto-edit-modal" id="modal-editar">
    <div class="header">
        <i class="edit icon"></i>
        Editar Contacto
    </div>

    <div class="content">
        <form id="form-editar-contacto">
            @csrf

            <input type="hidden" name="id" id="edit-id">

            <div class="form-group">
                <label class="form-label">Nombre del contacto *</label>
                <div class="input-icon-wrapper">
                    <i class="user icon"></i>
                    <input
                        type="text"
                        name="nombre_contacto"
                        id="edit-nombre"
                        class="form-input contacto-input input-with-icon"
                        autocomplete="off"
                        required
                    >
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Número de contacto *</label>
                <div class="input-icon-wrapper">
                    <i class="phone icon"></i>
                    <input
                        type="text"
                        name="numero_contacto"
                        id="edit-numero"
                        class="form-input contacto-input input-with-icon"
                        maxlength="10"
                        inputmode="numeric"
                        autocomplete="off"
                        required
                    >
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Tipo de contacto *</label>
                <select name="tipo_contacto" id="edit-tipo" class="form-input contacto-input" required>
                    <option value="">Selecciona una opción</option>
                    <option value="emergencia">Línea de emergencia</option>
                    <option value="recomendado">Contacto recomendado</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Página web</label>
                <div class="input-icon-wrapper">
                    <i class="globe icon"></i>
                    <input
                        type="url"
                        name="pagina_web"
                        id="edit-pagina"
                        class="form-input contacto-input input-with-icon"
                        autocomplete="off"
                    >
                </div>
            </div>
        </form>
    </div>

    <div class="actions contacto-modal-actions">
        <div class="ui deny button btn-modal-cancel">Cancelar</div>

        <button type="submit" form="form-editar-contacto" id="btn-actualizar-contacto" class="btn btn-primary">
            <i class="check icon"></i>
            Guardar cambios
        </button>
    </div>
</div>

<style>
    :root {
        --contact-primary: #10b981;
        --contact-primary-dark: #059669;
        --contact-secondary: #667eea;
        --contact-danger: #ef4444;
        --contact-danger-dark: #dc2626;
        --contact-warning: #f59e0b;
        --contact-info: #3b82f6;
        --contact-text-main: #0f172a;
        --contact-text-muted: #64748b;
        --contact-text-soft: #94a3b8;
        --contact-border: #e2e8f0;
        --contact-surface: #ffffff;
        --contact-soft-bg: #f8fafc;
    }

    .contactos-admin-page {
        width: 100%;
        padding-bottom: 24px;
    }

    .contactos-hero {
        background:
            radial-gradient(circle at top right, rgba(255,255,255,0.24), transparent 34%),
            radial-gradient(circle at bottom left, rgba(255,255,255,0.14), transparent 30%),
            linear-gradient(135deg, var(--contact-primary) 0%, var(--contact-primary-dark) 100%);
        border-radius: 26px;
        padding: 26px;
        color: white;
        margin-bottom: 22px;
        box-shadow: 0 12px 30px rgba(5, 150, 105, 0.18);
        overflow: hidden;
    }

    .contactos-hero-content {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 18px;
        flex-wrap: wrap;
    }

    .contactos-hero-left {
        display: flex;
        align-items: center;
        gap: 16px;
        min-width: 0;
    }

    .contactos-hero-icon {
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

    .contactos-hero-icon i {
        color: white;
        font-size: 1.8rem;
        margin: 0 !important;
    }

    .contactos-title {
        margin: 0 0 6px 0;
        font-size: clamp(1.55rem, 3vw, 2.15rem);
        font-weight: 900;
        letter-spacing: -0.035em;
        line-height: 1.08;
    }

    .contactos-subtitle {
        margin: 0;
        opacity: 0.92;
        font-size: 1rem;
        line-height: 1.45;
    }

    .contactos-hero-pill {
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

    .contactos-hero-pill i {
        margin: 0 !important;
    }

    .contactos-layout {
        display: grid;
        grid-template-columns: minmax(300px, 420px) minmax(0, 1fr);
        gap: 24px;
        align-items: start;
    }

    .contacto-form-card,
    .contactos-list-card,
    .empty-contactos-card {
        background: var(--contact-surface);
        border: 1px solid var(--contact-border);
        border-radius: 22px;
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.06);
        overflow: hidden;
    }

    .contacto-form-card {
        position: sticky;
        top: 18px;
    }

    .contacto-card-header {
        padding: 18px 20px;
        border-bottom: 1px solid #f1f5f9;
        background: linear-gradient(180deg, #ffffff 0%, #fbfdff 100%);
    }

    .contacto-card-title-wrap {
        display: flex;
        align-items: center;
        gap: 14px;
        min-width: 0;
    }

    .contacto-card-icon {
        width: 52px;
        height: 52px;
        border-radius: 16px;
        background: linear-gradient(135deg, var(--contact-primary) 0%, var(--contact-primary-dark) 100%);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        box-shadow: 0 8px 18px rgba(16, 185, 129, 0.24);
    }

    .contacto-card-icon.secondary {
        background: linear-gradient(135deg, var(--contact-secondary) 0%, var(--contact-primary-dark) 100%);
    }

    .contacto-card-icon i {
        margin: 0 !important;
        font-size: 1.25rem;
    }

    .contacto-card-title {
        margin: 0;
        color: var(--contact-text-main);
        font-size: 1.12rem;
        font-weight: 900;
        line-height: 1.25;
    }

    .contacto-card-subtitle {
        margin: 4px 0 0 0;
        color: var(--contact-text-muted);
        font-size: 0.9rem;
        line-height: 1.35;
    }

    .contacto-card-body {
        padding: 20px;
    }

    .form-group {
        margin-bottom: 18px;
    }

    .form-label {
        display: block;
        color: var(--contact-text-main);
        font-weight: 800;
        margin-bottom: 8px;
        font-size: 0.9rem;
    }

    .contacto-input {
        min-height: 46px;
        border-radius: 14px !important;
        width: 100%;
        max-width: 100%;
        box-sizing: border-box;
        border: 1px solid var(--contact-border) !important;
        transition: border-color 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
    }

    .contacto-input:focus {
        border-color: rgba(16, 185, 129, 0.65) !important;
        box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.12) !important;
        outline: none;
        background: white;
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
        color: var(--contact-primary-dark);
        pointer-events: none;
        z-index: 1;
        margin: 0 !important;
    }

    .input-with-icon {
        padding-left: 42px !important;
    }

    .form-help-text {
        display: block;
        color: var(--contact-text-soft);
        font-size: 0.8rem;
        margin-top: 6px;
        line-height: 1.35;
    }

    .btn-submit-contacto {
        width: 100%;
        min-height: 48px;
        border-radius: 14px;
        margin-top: 12px;
        display: inline-flex !important;
        align-items: center;
        justify-content: center;
        gap: 8px;
        font-weight: 900;
        box-shadow: 0 8px 18px rgba(16, 185, 129, 0.22);
    }

    .btn-submit-contacto i {
        margin: 0 !important;
    }

    .contactos-toolbar {
        padding: 18px 20px;
        display: grid;
        grid-template-columns: minmax(0, 1fr) minmax(200px, 240px) auto;
        gap: 14px;
        align-items: end;
        border-bottom: 1px solid #f1f5f9;
    }

    .filter-field {
        min-width: 0;
    }

    .contactos-search-input {
        width: 100%;
    }

    .contactos-search-input input {
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

    .contactos-summary {
        padding: 16px 20px 0;
    }

    .summary-item {
        display: inline-flex;
        align-items: center;
        gap: 12px;
        background: var(--contact-soft-bg);
        border: 1px solid #eef2f7;
        border-radius: 16px;
        padding: 12px 14px;
    }

    .summary-icon {
        width: 42px;
        height: 42px;
        border-radius: 14px;
        background: #d1fae5;
        color: var(--contact-primary-dark);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .summary-icon i {
        margin: 0 !important;
    }

    .summary-item span {
        display: block;
        color: var(--contact-text-muted);
        font-size: 0.78rem;
        font-weight: 800;
        margin-bottom: 2px;
    }

    .summary-item strong {
        display: block;
        color: var(--contact-text-main);
        font-size: 1.2rem;
        line-height: 1;
    }

    .contactos-loader {
        min-height: 220px;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 40px 20px;
    }

    .contactos-grid {
        padding: 20px;
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 16px;
    }

    .contacto-item-card {
        border: 1px solid var(--contact-border);
        border-radius: 18px;
        background: white;
        box-shadow: 0 6px 18px rgba(15, 23, 42, 0.05);
        overflow: hidden;
        display: flex;
        flex-direction: column;
        transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
    }

    .contacto-item-card:hover {
        transform: translateY(-2px);
        border-color: rgba(16, 185, 129, 0.35);
        box-shadow: 0 10px 24px rgba(15, 23, 42, 0.08);
    }

    .contacto-item-header {
        padding: 16px;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: flex-start;
        gap: 12px;
    }

    .contacto-item-icon {
        width: 46px;
        height: 46px;
        border-radius: 14px;
        background: #d1fae5;
        color: var(--contact-primary-dark);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .contacto-item-icon.emergencia {
        background: #fee2e2;
        color: #dc2626;
    }

    .contacto-item-icon.recomendado {
        background: #dbeafe;
        color: #2563eb;
    }

    .contacto-item-icon i {
        margin: 0 !important;
        font-size: 1.15rem;
    }

    .contacto-item-title {
        margin: 0;
        color: var(--contact-text-main);
        font-size: 1rem;
        font-weight: 900;
        line-height: 1.3;
        overflow-wrap: anywhere;
    }

    .contacto-item-badges {
        display: flex;
        flex-wrap: wrap;
        gap: 7px;
        margin-top: 9px;
    }

    .contact-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 6px 9px;
        border-radius: 999px;
        font-size: 0.72rem;
        font-weight: 900;
        line-height: 1;
    }

    .contact-badge i {
        margin: 0 !important;
    }

    .contact-badge.emergencia {
        background: #fee2e2;
        color: #991b1b;
    }

    .contact-badge.recomendado {
        background: #dbeafe;
        color: #1d4ed8;
    }

    .contacto-item-body {
        padding: 16px;
        flex: 1;
        display: grid;
        gap: 10px;
    }

    .contacto-meta-item {
        display: flex;
        align-items: flex-start;
        gap: 9px;
        color: var(--contact-text-muted);
        font-size: 0.88rem;
        line-height: 1.4;
        overflow-wrap: anywhere;
    }

    .contacto-meta-item i {
        color: var(--contact-primary-dark);
        margin: 2px 0 0 0 !important;
        flex-shrink: 0;
    }

    .contacto-meta-item a {
        color: var(--contact-info);
        font-weight: 800;
        overflow-wrap: anywhere;
    }

    .contacto-item-footer {
        padding: 14px 16px 16px;
        border-top: 1px solid #f1f5f9;
        background: #fbfdff;
    }
    
    .contacto-actions {
        width: 100%;
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 10px;
    }
    
    .contacto-actions .btn,
    .contacto-actions button,
    .contacto-actions a {
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
    
    .contacto-actions i {
        margin: 0 !important;
        flex-shrink: 0;
    }
    
    .contacto-actions .btn-editar {
        background: #eff6ff !important;
        color: #1d4ed8 !important;
        border: 1px solid #bfdbfe !important;
    }
    
    .contacto-actions .btn-eliminar {
        background: #fef2f2 !important;
        color: #b91c1c !important;
        border: 1px solid #fecaca !important;
    }
    
    .contacto-actions .btn-editar:hover {
        background: #dbeafe !important;
    }
    
    .contacto-actions .btn-eliminar:hover {
        background: #fee2e2 !important;
    }

    .contactos-pagination {
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
        border: 1px solid var(--contact-border);
        background: #ffffff;
        color: var(--contact-text-muted);
        border-radius: 12px;
        font-weight: 900;
        cursor: pointer;
        transition: background 0.2s ease, color 0.2s ease, border-color 0.2s ease;
    }

    .pagination-btn:hover {
        background: #ecfdf5;
        color: var(--contact-primary-dark);
        border-color: rgba(16, 185, 129, 0.35);
    }

    .pagination-btn.active {
        background: linear-gradient(135deg, var(--contact-primary) 0%, var(--contact-primary-dark) 100%);
        color: #ffffff;
        border-color: transparent;
        box-shadow: 0 8px 18px rgba(16, 185, 129, 0.22);
    }

    .pagination-btn:disabled {
        opacity: 0.45;
        cursor: not-allowed;
        background: var(--contact-soft-bg);
        color: var(--contact-text-soft);
    }

    .pagination-info {
        color: var(--contact-text-muted);
        font-size: 0.86rem;
        font-weight: 800;
        margin: 0 8px;
    }

    .empty-contactos-card {
        margin: 20px;
        padding: 44px 20px;
        text-align: center;
    }

    .empty-contactos-icon {
        width: 78px;
        height: 78px;
        border-radius: 24px;
        margin: 0 auto 18px;
        background: linear-gradient(135deg, rgba(16, 185, 129, 0.12), rgba(5, 150, 105, 0.16));
        color: var(--contact-primary-dark);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .empty-contactos-icon.error {
        background: #fee2e2;
        color: var(--contact-danger);
    }

    .empty-contactos-icon i {
        font-size: 2rem;
        margin: 0 !important;
    }

    .empty-contactos-card h3 {
        margin: 0 0 8px 0;
        color: #475569;
        font-weight: 900;
    }

    .empty-contactos-card p {
        margin: 0 0 18px 0;
        color: var(--contact-text-soft);
        line-height: 1.5;
    }

    .contacto-edit-modal {
        border-radius: 20px !important;
        overflow: hidden;
    }

    .contacto-edit-modal > .header {
        background: linear-gradient(135deg, var(--contact-secondary) 0%, var(--contact-primary-dark) 100%) !important;
        color: white !important;
        font-weight: 900 !important;
    }

    .contacto-edit-modal > .header i {
        margin-right: 8px !important;
    }

    .contacto-edit-modal > .content {
        padding: 20px !important;
    }

    .contacto-modal-actions {
        display: flex !important;
        justify-content: flex-end;
        gap: 10px;
        flex-wrap: wrap;
        padding: 16px 20px !important;
        background: var(--contact-soft-bg) !important;
        border-top: 1px solid #f1f5f9 !important;
    }

    .contacto-modal-actions .button,
    .contacto-modal-actions .btn {
        min-height: 42px;
        border-radius: 12px !important;
        display: inline-flex !important;
        align-items: center;
        justify-content: center;
        gap: 7px;
    }

    @media (max-width: 1200px) {
        .contactos-layout {
            grid-template-columns: 1fr;
        }

        .contacto-form-card {
            position: static;
        }
    }

    @media (max-width: 900px) {
        .contactos-grid {
            grid-template-columns: 1fr;
        }

        .contactos-toolbar {
            grid-template-columns: 1fr;
        }

        .btn-clear-filters {
            width: 100%;
        }
    }

    @media (max-width: 768px) {
        .contactos-hero {
            border-radius: 0 0 24px 24px;
            margin: -8px -4px 18px;
            padding: 22px;
        }

        .contactos-hero-content {
            align-items: stretch;
        }

        .contactos-hero-left {
            align-items: flex-start;
        }

        .contactos-hero-icon {
            width: 54px;
            height: 54px;
            border-radius: 18px;
        }

        .contactos-title {
            font-size: 1.55rem;
        }

        .contactos-subtitle {
            font-size: 0.92rem;
        }

        .contactos-hero-pill {
            width: 100%;
            justify-content: center;
            border-radius: 16px;
        }

        .contacto-form-card,
        .contactos-list-card,
        .empty-contactos-card {
            border-radius: 18px;
        }

        .contacto-card-header,
        .contacto-card-body,
        .contactos-toolbar {
            padding: 16px;
        }

        .contacto-card-icon {
            width: 46px;
            height: 46px;
            border-radius: 14px;
        }

        .contacto-card-title {
            font-size: 1.04rem;
        }

        .contacto-card-subtitle {
            font-size: 0.85rem;
        }

        .contactos-summary {
            padding: 14px 16px 0;
        }

        .contactos-grid {
            padding: 16px;
        }

        .contacto-item-card:hover {
            transform: none;
        }

        .contacto-item-footer {
            padding: 12px;
            position: sticky;
            bottom: 0;
            background: #ffffff;
            box-shadow: 0 -8px 18px rgba(15, 23, 42, 0.04);
        }

        .contacto-actions {
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 8px;
        }

        .contacto-actions .btn,
        .contacto-actions button,
        .contacto-actions a,
        .contacto-item-footer .btn,
        .contacto-item-footer button,
        .contacto-item-footer a {
            min-height: 46px;
            font-size: 0.86rem;
        }

        .contactos-pagination {
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
        input[type="url"],
        select {
            font-size: 16px !important;
        }

        .contacto-modal-actions {
            padding: 14px !important;
        }

        .contacto-modal-actions .button,
        .contacto-modal-actions .btn {
            flex: 1;
        }
    }

    @media (max-width: 480px) {
        .contactos-grid {
            padding: 12px;
            gap: 14px;
        }

        .contacto-item-header {
            padding: 14px;
        }

        .contacto-item-body {
            padding: 14px;
            gap: 9px;
        }

        .contacto-meta-item {
            font-size: 0.86rem;
        }

        .contacto-actions {
            grid-template-columns: 1fr;
        }

        .contacto-actions .btn,
        .contacto-actions button,
        .contacto-actions a,
        .contacto-item-footer .btn,
        .contacto-item-footer button,
        .contacto-item-footer a {
            min-height: 48px;
            width: 100% !important;
            justify-content: center !important;
            font-size: 0.9rem;
        }

        .contacto-actions .btn-editar::after,
        .contacto-item-footer .btn-editar::after {
            content: "";
        }

        .contacto-actions .btn-eliminar::after,
        .contacto-item-footer .btn-eliminar::after {
            content: "";
        }
    }

    @media (max-width: 420px) {
        .contactos-hero-left {
            gap: 12px;
        }

        .contacto-card-title-wrap {
            align-items: flex-start;
        }

        .contacto-item-header {
            align-items: flex-start;
        }

        .contacto-item-icon {
            width: 42px;
            height: 42px;
            border-radius: 13px;
        }

        .contacto-item-title {
            font-size: 0.98rem;
        }

        .contact-badge {
            max-width: 100%;
            white-space: normal;
            line-height: 1.2;
        }

        .contacto-modal-actions {
            flex-direction: column-reverse;
        }

        .contacto-modal-actions .button,
        .contacto-modal-actions .btn {
            width: 100%;
        }
    }
</style>

<script>
    const BASE_URL = "{{ url('/') }}";

    let contactosOriginales = [];
    let contactosFiltrados = [];
    let paginaActualContactos = 1;
    let contactosPorPagina = 6;
    let isSubmittingNuevoContacto = false;
    let isSubmittingContacto = false;

    $(document).ready(function() {
        $('#modal-editar').modal();

        $('#filtro-tipo-contacto').dropdown();

        cargarContactos();

        $('#buscar-contacto').on('keyup input change', function() {
            filtrarContactos();
        });

        $('#filtro-tipo-contacto').on('change', function() {
            filtrarContactos();
        });

        $('#form-contacto').submit(function(e) {
            e.preventDefault();

            if (isSubmittingNuevoContacto) {
                return false;
            }

            const $btn = $('#btn-guardar');

            isSubmittingNuevoContacto = true;
            $btn.addClass('loading disabled').html('<i class="spinner loading icon"></i> Guardando...');

            const data = $(this).serialize();

            $.ajax({
                url: "{{ route('admin.contacto.crearContacto') }}",
                method: 'POST',
                data: data,
                success: function(res) {
                    alertify.alert(res.header, res.message, function() {
                        cargarContactos();
                    });

                    $('#form-contacto')[0].reset();
                },
                error: function() {
                    alertify.error('Error al guardar');
                },
                complete: function() {
                    $btn.removeClass('loading disabled').html('<i class="save icon"></i> Guardar contacto');
                    isSubmittingNuevoContacto = false;
                }
            });
        });
    });

    function cargarContactos() {
        $('#contactos-loader').show();
        $('#contactos-container').empty();
        $('#contactos-pagination').hide().empty();
        $('#sin-contactos').hide();
        $('#sin-resultados').hide();
        $('#contactos-error').hide();

        $.ajax({
            url: "{{ route('admin.contacto.obtenerContactos') }}",
            method: 'GET',
            dataType: 'json',
            data: {
                start: 0,
                length: 1000
            },
            success: function(response) {
                $('#contactos-loader').hide();

                contactosOriginales = ordenarContactos(response.data || []);
                renderizarContactos(contactosOriginales);
            },
            error: function() {
                $('#contactos-loader').hide();
                $('#contactos-error').show();
            }
        });
    }

    function ordenarContactos(contactos) {
        return [...contactos].sort(function(a, b) {
            const nombreA = normalizarTexto(extraerTexto(a.nombre_contacto || ''));
            const nombreB = normalizarTexto(extraerTexto(b.nombre_contacto || ''));

            return nombreA.localeCompare(nombreB);
        });
    }

    function renderizarContactos(contactos) {
        contactosFiltrados = contactos;
        paginaActualContactos = 1;
        renderizarPaginaContactos();
    }

    function renderizarPaginaContactos() {
        const container = $('#contactos-container');
        const pagination = $('#contactos-pagination');

        container.empty();
        pagination.empty();

        $('#total-contactos').text(contactosFiltrados.length);

        $('#sin-contactos').hide();
        $('#sin-resultados').hide();
        $('#contactos-error').hide();

        if (contactosOriginales.length === 0) {
            pagination.hide();
            $('#sin-contactos').show();
            return;
        }

        if (contactosFiltrados.length === 0) {
            pagination.hide();
            $('#sin-resultados').show();
            return;
        }

        const totalPaginas = Math.ceil(contactosFiltrados.length / contactosPorPagina);
        const inicio = (paginaActualContactos - 1) * contactosPorPagina;
        const fin = inicio + contactosPorPagina;
        const contactosPagina = contactosFiltrados.slice(inicio, fin);

        contactosPagina.forEach(function(contacto) {
            const id = extraerTexto(contacto.id || '');
            const nombre = extraerTexto(contacto.nombre_contacto || 'Contacto sin nombre');
            const numero = extraerTexto(contacto.numero_contacto || 'Sin número');
            const tipo = extraerTexto(contacto.tipo_contacto || '');
            const paginaWeb = extraerTexto(contacto.pagina_web || '');

            const tipoNormalizado = normalizarTexto(tipo);
            const esEmergencia = tipoNormalizado.includes('emergencia');
            const tipoClase = esEmergencia ? 'emergencia' : 'recomendado';
            const tipoTexto = esEmergencia ? 'Línea de emergencia' : 'Contacto recomendado';
            const tipoIcono = esEmergencia ? 'ambulance' : 'star';

            const paginaHtml = paginaWeb
                ? `<a href="${escapeHtml(paginaWeb)}" target="_blank" rel="noopener noreferrer">${escapeHtml(paginaWeb)}</a>`
                : `<span>Sin página web</span>`;

            const accionesFallback = `
                <button
                    type="button"
                    class="btn btn-secondary btn-sm btn-editar"
                    data-id="${escapeHtml(id)}"
                    data-nombre="${escapeHtml(nombre)}"
                    data-numero="${escapeHtml(numero)}"
                    data-tipo="${escapeHtml(tipo)}"
                    data-pagina="${escapeHtml(paginaWeb)}"
                >
                    <i class="edit icon"></i>
                    Editar
                </button>

                <button
                    type="button"
                    class="btn btn-danger btn-sm btn-eliminar"
                    data-id="${escapeHtml(id)}"
                >
                    <i class="trash icon"></i>
                    Eliminar
                </button>
            `;

            const card = `
                <div class="contacto-item-card">
                    <div class="contacto-item-header">
                        <div class="contacto-item-icon ${tipoClase}">
                            <i class="${tipoIcono} icon"></i>
                        </div>

                        <div style="min-width: 0; flex: 1;">
                            <h3 class="contacto-item-title">${escapeHtml(nombre)}</h3>

                            <div class="contacto-item-badges">
                                <span class="contact-badge ${tipoClase}">
                                    <i class="${tipoIcono} icon"></i>
                                    ${escapeHtml(tipoTexto)}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="contacto-item-body">
                        <div class="contacto-meta-item">
                            <i class="phone icon"></i>
                            <span>${escapeHtml(numero)}</span>
                        </div>

                        <div class="contacto-meta-item">
                            <i class="globe icon"></i>
                            ${paginaHtml}
                        </div>
                    </div>

                    <div class="contacto-item-footer">
                        <div class="contacto-actions">
                            ${accionesFallback}
                        </div>
                    </div>
                </div>
            `;

            container.append(card);
        });

        renderizarPaginacionContactos(totalPaginas, contactosFiltrados.length);
    }

    function renderizarPaginacionContactos(totalPaginas, totalContactos) {
        const pagination = $('#contactos-pagination');

        pagination.empty();

        if (totalPaginas <= 1) {
            pagination.hide();
            return;
        }

        const inicio = ((paginaActualContactos - 1) * contactosPorPagina) + 1;
        const fin = Math.min(paginaActualContactos * contactosPorPagina, totalContactos);

        pagination.show();

        pagination.append(`
            <button
                type="button"
                class="pagination-btn"
                onclick="cambiarPaginaContactos(${paginaActualContactos - 1})"
                ${paginaActualContactos === 1 ? 'disabled' : ''}
            >
                <i class="chevron left icon"></i>
            </button>
        `);

        const paginas = obtenerPaginasVisiblesContactos(totalPaginas);

        paginas.forEach(function(pagina) {
            if (pagina === '...') {
                pagination.append(`<span class="pagination-info">...</span>`);
                return;
            }

            pagination.append(`
                <button
                    type="button"
                    class="pagination-btn ${pagina === paginaActualContactos ? 'active' : ''}"
                    onclick="cambiarPaginaContactos(${pagina})"
                >
                    ${pagina}
                </button>
            `);
        });

        pagination.append(`
            <button
                type="button"
                class="pagination-btn"
                onclick="cambiarPaginaContactos(${paginaActualContactos + 1})"
                ${paginaActualContactos === totalPaginas ? 'disabled' : ''}
            >
                <i class="chevron right icon"></i>
            </button>
        `);

        pagination.append(`
            <div class="pagination-info">
                Mostrando ${inicio}-${fin} de ${totalContactos}
            </div>
        `);
    }

    function cambiarPaginaContactos(pagina) {
        const totalPaginas = Math.ceil(contactosFiltrados.length / contactosPorPagina);

        if (pagina < 1 || pagina > totalPaginas) {
            return;
        }

        paginaActualContactos = pagina;
        renderizarPaginaContactos();

        document.querySelector('.contactos-list-card')?.scrollIntoView({
            behavior: 'smooth',
            block: 'start'
        });
    }

    function obtenerPaginasVisiblesContactos(totalPaginas) {
        const paginas = [];

        if (totalPaginas <= 5) {
            for (let i = 1; i <= totalPaginas; i++) {
                paginas.push(i);
            }

            return paginas;
        }

        paginas.push(1);

        if (paginaActualContactos > 3) {
            paginas.push('...');
        }

        const inicio = Math.max(2, paginaActualContactos - 1);
        const fin = Math.min(totalPaginas - 1, paginaActualContactos + 1);

        for (let i = inicio; i <= fin; i++) {
            paginas.push(i);
        }

        if (paginaActualContactos < totalPaginas - 2) {
            paginas.push('...');
        }

        paginas.push(totalPaginas);

        return paginas;
    }

    function filtrarContactos() {
        const busqueda = normalizarTexto($('#buscar-contacto').val());
        const tipoFiltro = $('#filtro-tipo-contacto').val();

        const filtrados = contactosOriginales.filter(function(contacto) {
            const nombre = extraerTexto(contacto.nombre_contacto || '');
            const numero = extraerTexto(contacto.numero_contacto || '');
            const tipo = extraerTexto(contacto.tipo_contacto || '');
            const paginaWeb = extraerTexto(contacto.pagina_web || '');

            const textoCompleto = normalizarTexto(
                nombre + ' ' + numero + ' ' + tipo + ' ' + paginaWeb
            );

            const tipoNormalizado = normalizarTexto(tipo);

            const coincideBusqueda = textoCompleto.includes(busqueda);
            let coincideTipo = true;

            if (tipoFiltro === 'emergencia') {
                coincideTipo = tipoNormalizado.includes('emergencia');
            }

            if (tipoFiltro === 'recomendado') {
                coincideTipo = tipoNormalizado.includes('recomendado');
            }

            return coincideBusqueda && coincideTipo;
        });

        renderizarContactos(ordenarContactos(filtrados));
    }

    function limpiarFiltrosContactos() {
        $('#buscar-contacto').val('');
        $('#filtro-tipo-contacto').dropdown('set selected', 'todos');
        renderizarContactos(contactosOriginales);
    }

    $(document).on('click', '.btn-eliminar', function() {
        const id = $(this).data('id');

        alertify.confirm(
            '¿Eliminar contacto?',
            '¿Estás seguro de que deseas eliminar este contacto?',
            function() {
                mostrarLoaderPantalla();

                setTimeout(function() {
                    $.ajax({
                        url: `${BASE_URL}/administrador/contacto/eliminar-contacto/${id}`,
                        method: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(res) {
                            alertify.success(res.message || 'Eliminado');
                            cargarContactos();
                        },
                        error: function() {
                            alertify.error('Ocurrió un error al eliminar');
                        },
                        complete: function() {
                            ocultarLoaderPantalla();
                        }
                    });
                }, 100);
            },
            function() {
                alertify.message('Cancelado');
            }
        );
    });

    $(document).on('click', '.btn-editar', function() {
        const $btn = $(this);

        $('#edit-id').val($btn.data('id'));
        $('#edit-nombre').val($btn.data('nombre'));
        $('#edit-numero').val($btn.data('numero'));
        $('#edit-tipo').val($btn.data('tipo'));
        $('#edit-pagina').val($btn.data('pagina'));

        $('#modal-editar').modal('show');
    });

    $('#form-editar-contacto').submit(function(e) {
        e.preventDefault();

        if (isSubmittingContacto) {
            return false;
        }

        const id = $('#edit-id').val();
        const data = $(this).serialize();
        const $btn = $('#btn-actualizar-contacto');

        isSubmittingContacto = true;
        $btn.addClass('loading disabled').prop('disabled', true);

        $.ajax({
            url: `${BASE_URL}/administrador/contacto/actualizar-contacto/${id}`,
            method: 'POST',
            data: data,
            success: function(res) {
                alertify.alert(res.header, res.message, function() {
                    cargarContactos();
                });

                $('#modal-editar').modal('hide');
            },
            error: function() {
                alertify.error('Error al actualizar');
            },
            complete: function() {
                $btn.removeClass('loading disabled').prop('disabled', false);
                isSubmittingContacto = false;
            }
        });
    });

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