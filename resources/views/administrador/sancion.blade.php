<x-app-layout>
    <div class="sanciones-admin-page">
        <div class="sanciones-hero">
            <div class="sanciones-hero-content">
                <div class="sanciones-hero-left">
                    <div class="sanciones-hero-icon">
                        <i class="gavel icon"></i>
                    </div>

                    <div>
                        <h1 class="sanciones-title">Gestión de Sanciones</h1>
                        <p class="sanciones-subtitle">Registra y administra multas e infracciones</p>
                    </div>
                </div>

                <div class="sanciones-hero-pill">
                    <i class="warning sign icon"></i>
                    Administración de sanciones
                </div>
            </div>
        </div>

        <div class="sanciones-layout">
            <div class="sancion-form-card">
                <div class="sancion-card-header">
                    <div class="sancion-card-title-wrap">
                        <div class="sancion-card-icon">
                            <i class="plus circle icon"></i>
                        </div>

                        <div>
                            <h3 class="sancion-card-title">Registrar Sanción</h3>
                            <p class="sancion-card-subtitle">Captura la información de la infracción y su evidencia.</p>
                        </div>
                    </div>
                </div>

                <div class="sancion-card-body">
                    <form id="form-sancion" enctype="multipart/form-data">
                        @csrf

                        <div class="form-group">
                            <label class="form-label">Usuario a sancionar *</label>
                            <select name="user_id" class="ui fluid search selection dropdown sancion-input" required>
                                <option value="">Selecciona un usuario</option>
                                @foreach($usuarios as $user)
                                    <option value="{{ $user->id }}">
                                        {{ $user->nombre }} ({{ $user->tipo }} - #{{ $user->casa }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Motivo de sanción *</label>
                            <div class="input-icon-wrapper">
                                <i class="exclamation triangle icon"></i>
                                <input
                                    type="text"
                                    name="motivo"
                                    class="form-input sancion-input input-with-icon"
                                    placeholder="Ej. Incumplimiento de reglamento"
                                    autocomplete="off"
                                    required
                                >
                            </div>
                        </div>

                        <div class="sancion-form-grid">
                            <div class="form-group">
                                <label class="form-label">Monto de la sanción *</label>
                                <div class="input-icon-wrapper">
                                    <i class="dollar sign icon"></i>
                                    <input
                                        type="number"
                                        name="monto"
                                        class="form-input sancion-input input-with-icon"
                                        min="0"
                                        step="0.01"
                                        placeholder="Ej. 500.00"
                                        autocomplete="off"
                                        required
                                    >
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Número de incidencia *</label>
                                <div class="input-icon-wrapper">
                                    <i class="hashtag icon"></i>
                                    <input
                                        type="text"
                                        name="incidencia"
                                        class="form-input sancion-input input-with-icon"
                                        placeholder="# incidencia"
                                        autocomplete="off"
                                        required
                                    >
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Foto de evidencia *</label>

                            <div class="upload-area" id="upload-area-evidencia">
                                <input type="file" name="evidencia" accept="image/*" id="evidencia-input" hidden>

                                <label for="evidencia-input" class="upload-label">
                                    <div class="upload-placeholder" id="upload-placeholder-evidencia">
                                        <div class="upload-icon">
                                            <i class="camera icon"></i>
                                        </div>

                                        <div class="upload-text">
                                            <span class="primary">Arrastra la imagen aquí</span>
                                            <span class="secondary">o haz clic para seleccionar</span>
                                        </div>

                                        <div class="upload-formats">JPG, PNG o WEBP, máximo 5MB</div>
                                    </div>

                                    <div id="upload-preview-evidencia" class="upload-preview-container" style="display: none;">
                                        <div id="preview-content-evidencia" class="preview-content-evidencia">
                                            <button type="button" class="remove-preview-btn-evidencia" id="remove-preview-btn-evidencia" title="Quitar imagen">
                                                <i class="times icon"></i>
                                            </button>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Comentario *</label>
                            <textarea
                                name="comentario"
                                class="form-input sancion-input sancion-textarea"
                                rows="3"
                                placeholder="Detalles adicionales..."
                                autocomplete="off"
                                required
                            ></textarea>
                        </div>

                        <button id="btn-sancion" class="btn btn-primary btn-lg btn-submit-sancion" type="submit">
                            <i class="gavel icon"></i>
                            Registrar sanción
                        </button>
                    </form>
                </div>
            </div>

            <div class="sanciones-list-card">
                <div class="sancion-card-header list-header">
                    <div class="sancion-card-title-wrap">
                        <div class="sancion-card-icon secondary">
                            <i class="list icon"></i>
                        </div>

                        <div>
                            <h3 class="sancion-card-title">Lista de Sanciones</h3>
                            <p class="sancion-card-subtitle">Consulta, filtra y administra las sanciones registradas.</p>
                        </div>
                    </div>
                </div>

                <div class="sanciones-toolbar">
                    <div class="filter-field">
                        <label class="form-label">Buscar sanción</label>
                        <div class="ui icon input sanciones-search-input">
                            <i class="search icon"></i>
                            <input
                                type="text"
                                id="buscar-sancion"
                                placeholder="Usuario, motivo, monto, incidencia, comentario o estado..."
                                autocomplete="off"
                            >
                        </div>
                    </div>

                    <div class="filter-field">
                        <label class="form-label">Estado de pago</label>
                        <select id="filtro-estado-sancion" class="ui fluid dropdown sancion-input">
                            <option value="todos">Todos</option>
                            <option value="pendiente">Pendiente</option>
                            <option value="pagado">Pagado</option>
                            <option value="rechazado">Rechazado</option>
                        </select>
                    </div>

                    <button type="button" class="btn btn-secondary btn-clear-filters" onclick="limpiarFiltrosSanciones()">
                        <i class="times icon"></i>
                        Limpiar
                    </button>
                </div>

                <div class="sanciones-summary">
                    <div class="summary-item">
                        <div class="summary-icon">
                            <i class="gavel icon"></i>
                        </div>

                        <div>
                            <span>Sanciones encontradas</span>
                            <strong id="total-sanciones">0</strong>
                        </div>
                    </div>
                </div>

                <div id="sanciones-loader" class="sanciones-loader">
                    <div class="ui active centered inline text loader large">Cargando sanciones...</div>
                </div>

                <div id="sanciones-container" class="sanciones-grid"></div>

                <div id="sanciones-pagination" class="sanciones-pagination" style="display: none;"></div>

                <div id="sin-sanciones" class="empty-sanciones-card" style="display: none;">
                    <div class="empty-sanciones-icon">
                        <i class="gavel icon"></i>
                    </div>

                    <h3>No hay sanciones registradas</h3>
                    <p>Aún no se han registrado multas o infracciones.</p>
                </div>

                <div id="sin-resultados" class="empty-sanciones-card" style="display: none;">
                    <div class="empty-sanciones-icon">
                        <i class="search icon"></i>
                    </div>

                    <h3>No se encontraron resultados</h3>
                    <p>No hay sanciones que coincidan con tu búsqueda o filtro.</p>

                    <button type="button" class="btn btn-secondary btn-sm" onclick="limpiarFiltrosSanciones()">
                        <i class="undo icon"></i>
                        Limpiar filtros
                    </button>
                </div>

                <div id="sanciones-error" class="empty-sanciones-card error" style="display: none;">
                    <div class="empty-sanciones-icon error">
                        <i class="warning sign icon"></i>
                    </div>

                    <h3>Error al cargar sanciones</h3>
                    <p>No se pudieron cargar las sanciones. Intenta nuevamente.</p>

                    <button type="button" class="btn btn-primary btn-sm" onclick="cargarSanciones()">
                        <i class="refresh icon"></i>
                        Reintentar
                    </button>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<div class="ui modal sancion-evidencia-modal" id="modal-evidencia">
    <div class="header">
        <i class="file image icon"></i>
        Evidencia cargada
    </div>

    <div class="content evidencia-modal-content">
        <img id="imagen-evidencia" src="" alt="Evidencia">
    </div>
</div>

<div class="ui modal sancion-edit-modal" id="modal-editar-sancion">
    <div class="header">
        <i class="edit icon"></i>
        Editar Sanción
    </div>

    <div class="content">
        <form id="form-editar-sancion">
            @csrf

            <input type="hidden" name="id" id="edit-id">

            <div class="form-group">
                <label class="form-label">Motivo *</label>
                <div class="input-icon-wrapper">
                    <i class="exclamation triangle icon"></i>
                    <input
                        type="text"
                        name="motivo"
                        id="edit-motivo"
                        class="form-input sancion-input input-with-icon"
                        autocomplete="off"
                        required
                    >
                </div>
            </div>

            <div class="sancion-form-grid">
                <div class="form-group">
                    <label class="form-label">Monto *</label>
                    <div class="input-icon-wrapper">
                        <i class="dollar sign icon"></i>
                        <input
                            type="number"
                            name="monto"
                            id="edit-monto"
                            class="form-input sancion-input input-with-icon"
                            autocomplete="off"
                            step="0.01"
                            min="0"
                            required
                        >
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Incidencia *</label>
                    <div class="input-icon-wrapper">
                        <i class="hashtag icon"></i>
                        <input
                            type="text"
                            name="incidencia"
                            id="edit-incidencia"
                            class="form-input sancion-input input-with-icon"
                            autocomplete="off"
                            required
                        >
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Comentario *</label>
                <textarea
                    name="comentario"
                    id="edit-comentario"
                    class="form-input sancion-input sancion-textarea"
                    rows="3"
                    autocomplete="off"
                    required
                ></textarea>
            </div>

            <div class="form-group">
                <label class="form-label">Estado actual del pago *</label>
                <select name="estado" id="edit-estado" class="form-input sancion-input" required>
                    <option value="">Selecciona una opción</option>
                    <option value="pendiente">Pendiente</option>
                    <option value="rechazado">Rechazado</option>
                    <option value="pagado">Pagado</option>
                </select>
            </div>
        </form>
    </div>

    <div class="actions sancion-modal-actions">
        <div class="ui deny button btn-modal-cancel">Cancelar</div>

        <button id="btn-guardar-cambios" type="submit" form="form-editar-sancion" class="btn btn-primary">
            <i class="check icon"></i>
            Guardar cambios
        </button>
    </div>
</div>

<style>
    :root {
        --sancion-primary: #ef4444;
        --sancion-primary-dark: #dc2626;
        --sancion-secondary: #667eea;
        --sancion-success: #10b981;
        --sancion-warning: #f59e0b;
        --sancion-info: #3b82f6;
        --sancion-text-main: #0f172a;
        --sancion-text-muted: #64748b;
        --sancion-text-soft: #94a3b8;
        --sancion-border: #e2e8f0;
        --sancion-surface: #ffffff;
        --sancion-soft-bg: #f8fafc;
    }

    .sanciones-admin-page {
        width: 100%;
        padding-bottom: 24px;
    }

    .sanciones-hero {
        background:
            radial-gradient(circle at top right, rgba(255,255,255,0.24), transparent 34%),
            radial-gradient(circle at bottom left, rgba(255,255,255,0.14), transparent 30%),
            linear-gradient(135deg, var(--sancion-primary) 0%, var(--sancion-primary-dark) 100%);
        border-radius: 26px;
        padding: 26px;
        color: white;
        margin-bottom: 22px;
        box-shadow: 0 12px 30px rgba(220, 38, 38, 0.18);
        overflow: hidden;
    }

    .sanciones-hero-content {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 18px;
        flex-wrap: wrap;
    }

    .sanciones-hero-left {
        display: flex;
        align-items: center;
        gap: 16px;
        min-width: 0;
    }

    .sanciones-hero-icon {
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

    .sanciones-hero-icon i {
        color: white;
        font-size: 1.8rem;
        margin: 0 !important;
    }

    .sanciones-title {
        margin: 0 0 6px 0;
        font-size: clamp(1.55rem, 3vw, 2.15rem);
        font-weight: 900;
        letter-spacing: -0.035em;
        line-height: 1.08;
    }

    .sanciones-subtitle {
        margin: 0;
        opacity: 0.92;
        font-size: 1rem;
        line-height: 1.45;
    }

    .sanciones-hero-pill {
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

    .sanciones-hero-pill i {
        margin: 0 !important;
    }

    .sanciones-layout {
        display: grid;
        grid-template-columns: minmax(300px, 430px) minmax(0, 1fr);
        gap: 24px;
        align-items: start;
    }

    .sancion-form-card,
    .sanciones-list-card,
    .empty-sanciones-card {
        background: var(--sancion-surface);
        border: 1px solid var(--sancion-border);
        border-radius: 22px;
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.06);
        overflow: hidden;
    }

    .sancion-form-card {
        position: sticky;
        top: 18px;
    }

    .sancion-card-header {
        padding: 18px 20px;
        border-bottom: 1px solid #f1f5f9;
        background: linear-gradient(180deg, #ffffff 0%, #fbfdff 100%);
    }

    .sancion-card-title-wrap {
        display: flex;
        align-items: center;
        gap: 14px;
        min-width: 0;
    }

    .sancion-card-icon {
        width: 52px;
        height: 52px;
        border-radius: 16px;
        background: linear-gradient(135deg, var(--sancion-primary) 0%, var(--sancion-primary-dark) 100%);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        box-shadow: 0 8px 18px rgba(239, 68, 68, 0.24);
    }

    .sancion-card-icon.secondary {
        background: linear-gradient(135deg, var(--sancion-secondary) 0%, var(--sancion-primary-dark) 100%);
    }

    .sancion-card-icon i {
        margin: 0 !important;
        font-size: 1.25rem;
    }

    .sancion-card-title {
        margin: 0;
        color: var(--sancion-text-main);
        font-size: 1.12rem;
        font-weight: 900;
        line-height: 1.25;
    }

    .sancion-card-subtitle {
        margin: 4px 0 0 0;
        color: var(--sancion-text-muted);
        font-size: 0.9rem;
        line-height: 1.35;
    }

    .sancion-card-body {
        padding: 20px;
    }

    .form-group {
        margin-bottom: 18px;
    }

    .form-label {
        display: block;
        color: var(--sancion-text-main);
        font-weight: 800;
        margin-bottom: 8px;
        font-size: 0.9rem;
    }

    .sancion-form-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 16px;
    }

    .sancion-input {
        min-height: 46px;
        border-radius: 14px !important;
        width: 100%;
        max-width: 100%;
        box-sizing: border-box;
        border: 1px solid var(--sancion-border) !important;
        transition: border-color 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
    }

    .sancion-input:focus {
        border-color: rgba(239, 68, 68, 0.65) !important;
        box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.12) !important;
        outline: none;
        background: white;
    }

    .sancion-textarea {
        resize: vertical;
        min-height: 110px;
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
        color: var(--sancion-primary-dark);
        pointer-events: none;
        z-index: 1;
        margin: 0 !important;
    }

    .input-with-icon {
        padding-left: 42px !important;
    }

    .upload-area {
        width: 100%;
        min-height: 220px;
        border-radius: 18px;
        border: 2px dashed #cbd5e1;
        background: var(--sancion-soft-bg);
        transition: border-color 0.2s ease, background 0.2s ease;
        overflow: hidden;
    }

    .upload-area:hover,
    .upload-area.dragover {
        border-color: var(--sancion-primary);
        background: #fef2f2;
    }

    .upload-label {
        display: block;
        width: 100%;
        cursor: pointer;
        margin: 0;
    }

    .upload-placeholder {
        min-height: 220px;
        padding: 24px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }

    .upload-icon {
        width: 62px;
        height: 62px;
        border-radius: 20px;
        background: linear-gradient(135deg, var(--sancion-primary) 0%, var(--sancion-primary-dark) 100%);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 14px;
        box-shadow: 0 8px 18px rgba(239, 68, 68, 0.24);
    }

    .upload-icon i {
        margin: 0 !important;
        font-size: 1.45rem;
    }

    .upload-text {
        display: flex;
        flex-direction: column;
        gap: 4px;
        text-align: center;
    }

    .upload-text .primary {
        color: var(--sancion-text-main);
        font-weight: 900;
    }

    .upload-text .secondary,
    .upload-formats {
        color: var(--sancion-text-muted);
        font-size: 0.86rem;
    }

    .upload-formats {
        margin-top: 8px;
        text-align: center;
    }

    .upload-preview-container {
        width: 100%;
        padding: 20px;
        position: relative;
    }

    .preview-content-evidencia {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 12px;
        position: relative;
    }

    .preview-image-evidencia {
        max-width: 100%;
        max-height: 210px;
        border-radius: 14px;
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.12);
        object-fit: contain;
    }

    .remove-preview-btn-evidencia {
        position: absolute;
        top: -10px;
        right: -10px;
        width: 34px;
        height: 34px;
        border-radius: 999px;
        border: none;
        background: var(--sancion-primary);
        color: white;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background 0.2s ease, transform 0.2s ease;
        box-shadow: 0 8px 18px rgba(239, 68, 68, 0.28);
        z-index: 10;
    }

    .remove-preview-btn-evidencia:hover {
        transform: scale(1.08);
        background: var(--sancion-primary-dark);
    }

    .remove-preview-btn-evidencia i {
        margin: 0 !important;
    }

    .preview-badge-evidencia {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 7px 12px;
        background: #dcfce7;
        color: #166534;
        border-radius: 999px;
        font-size: 0.78rem;
        font-weight: 900;
    }

    .preview-badge-evidencia i {
        margin: 0 !important;
    }

    .btn-submit-sancion {
        width: 100%;
        min-height: 48px;
        border-radius: 14px;
        margin-top: 12px;
        display: inline-flex !important;
        align-items: center;
        justify-content: center;
        gap: 8px;
        font-weight: 900;
        box-shadow: 0 8px 18px rgba(239, 68, 68, 0.22);
    }

    .btn-submit-sancion i {
        margin: 0 !important;
    }

    .sanciones-toolbar {
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

    .sanciones-search-input {
        width: 100%;
    }

    .sanciones-search-input input {
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

    .sanciones-summary {
        padding: 16px 20px 0;
    }

    .summary-item {
        display: inline-flex;
        align-items: center;
        gap: 12px;
        background: var(--sancion-soft-bg);
        border: 1px solid #eef2f7;
        border-radius: 16px;
        padding: 12px 14px;
    }

    .summary-icon {
        width: 42px;
        height: 42px;
        border-radius: 14px;
        background: #fee2e2;
        color: var(--sancion-primary-dark);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .summary-icon i {
        margin: 0 !important;
    }

    .summary-item span {
        display: block;
        color: var(--sancion-text-muted);
        font-size: 0.78rem;
        font-weight: 800;
        margin-bottom: 2px;
    }

    .summary-item strong {
        display: block;
        color: var(--sancion-text-main);
        font-size: 1.2rem;
        line-height: 1;
    }

    .sanciones-loader {
        min-height: 220px;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 40px 20px;
    }

    .sanciones-grid {
        padding: 20px;
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 16px;
    }

    .sancion-item-card {
        border: 1px solid var(--sancion-border);
        border-radius: 18px;
        background: white;
        box-shadow: 0 6px 18px rgba(15, 23, 42, 0.05);
        overflow: hidden;
        display: flex;
        flex-direction: column;
        transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
    }

    .sancion-item-card:hover {
        transform: translateY(-2px);
        border-color: rgba(239, 68, 68, 0.35);
        box-shadow: 0 10px 24px rgba(15, 23, 42, 0.08);
    }

    .sancion-item-header {
        padding: 16px;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: flex-start;
        gap: 12px;
    }

    .sancion-item-icon {
        width: 46px;
        height: 46px;
        border-radius: 14px;
        background: #fee2e2;
        color: var(--sancion-primary-dark);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .sancion-item-icon i {
        margin: 0 !important;
        font-size: 1.15rem;
    }

    .sancion-item-title {
        margin: 0;
        color: var(--sancion-text-main);
        font-size: 1rem;
        font-weight: 900;
        line-height: 1.3;
        overflow-wrap: anywhere;
    }

    .sancion-item-subtitle {
        margin-top: 4px;
        color: var(--sancion-text-muted);
        font-size: 0.84rem;
        line-height: 1.35;
        overflow-wrap: anywhere;
    }

    .sancion-item-badges {
        display: flex;
        flex-wrap: wrap;
        gap: 7px;
        margin-top: 9px;
    }

    .sancion-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 6px 9px;
        border-radius: 999px;
        font-size: 0.72rem;
        font-weight: 900;
        line-height: 1;
    }

    .sancion-badge i {
        margin: 0 !important;
    }

    .sancion-badge.pendiente {
        background: #fef3c7;
        color: #92400e;
    }

    .sancion-badge.pagado {
        background: #dcfce7;
        color: #166534;
    }

    .sancion-badge.rechazado {
        background: #fee2e2;
        color: #991b1b;
    }

    .sancion-badge.incidencia {
        background: #dbeafe;
        color: #1d4ed8;
    }

    .sancion-item-body {
        padding: 16px;
        flex: 1;
        display: grid;
        gap: 10px;
    }

    .sancion-meta-item {
        display: flex;
        align-items: flex-start;
        gap: 9px;
        color: var(--sancion-text-muted);
        font-size: 0.88rem;
        line-height: 1.4;
        overflow-wrap: anywhere;
    }

    .sancion-meta-item i {
        color: var(--sancion-primary-dark);
        margin: 2px 0 0 0 !important;
        flex-shrink: 0;
    }

    .sancion-comentario {
        color: var(--sancion-text-muted);
        font-size: 0.88rem;
        line-height: 1.5;
        overflow-wrap: anywhere;
        display: -webkit-box;
        -webkit-line-clamp: 4;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .sancion-item-footer {
        padding: 14px 16px 16px;
        border-top: 1px solid #f1f5f9;
        background: #fbfdff;
    }

    .sancion-actions {
        width: 100%;
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 10px;
    }

    .sancion-actions .btn,
    .sancion-actions button,
    .sancion-actions a {
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
        font-size: 0.84rem;
        font-weight: 900;
        line-height: 1.1;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .sancion-actions i {
        margin: 0 !important;
        flex-shrink: 0;
    }

    .sancion-actions .btn-ver-evidencia {
        background: #f8fafc !important;
        color: #475569 !important;
        border: 1px solid #cbd5e1 !important;
    }

    .sancion-actions .btn-editar {
        background: #eff6ff !important;
        color: #1d4ed8 !important;
        border: 1px solid #bfdbfe !important;
    }

    .sancion-actions .btn-eliminar {
        background: #fef2f2 !important;
        color: #b91c1c !important;
        border: 1px solid #fecaca !important;
    }

    .sanciones-pagination {
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
        border: 1px solid var(--sancion-border);
        background: #ffffff;
        color: var(--sancion-text-muted);
        border-radius: 12px;
        font-weight: 900;
        cursor: pointer;
        transition: background 0.2s ease, color 0.2s ease, border-color 0.2s ease;
    }

    .pagination-btn:hover {
        background: #fef2f2;
        color: var(--sancion-primary-dark);
        border-color: rgba(239, 68, 68, 0.35);
    }

    .pagination-btn.active {
        background: linear-gradient(135deg, var(--sancion-primary) 0%, var(--sancion-primary-dark) 100%);
        color: #ffffff;
        border-color: transparent;
        box-shadow: 0 8px 18px rgba(239, 68, 68, 0.22);
    }

    .pagination-btn:disabled {
        opacity: 0.45;
        cursor: not-allowed;
        background: var(--sancion-soft-bg);
        color: var(--sancion-text-soft);
    }

    .pagination-info {
        color: var(--sancion-text-muted);
        font-size: 0.86rem;
        font-weight: 800;
        margin: 0 8px;
    }

    .empty-sanciones-card {
        margin: 20px;
        padding: 44px 20px;
        text-align: center;
    }

    .empty-sanciones-icon {
        width: 78px;
        height: 78px;
        border-radius: 24px;
        margin: 0 auto 18px;
        background: linear-gradient(135deg, rgba(239, 68, 68, 0.12), rgba(220, 38, 38, 0.16));
        color: var(--sancion-primary-dark);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .empty-sanciones-icon.error {
        background: #fee2e2;
        color: var(--sancion-primary);
    }

    .empty-sanciones-icon i {
        font-size: 2rem;
        margin: 0 !important;
    }

    .empty-sanciones-card h3 {
        margin: 0 0 8px 0;
        color: #475569;
        font-weight: 900;
    }

    .empty-sanciones-card p {
        margin: 0 0 18px 0;
        color: var(--sancion-text-soft);
        line-height: 1.5;
    }

    .sancion-evidencia-modal,
    .sancion-edit-modal {
        border-radius: 20px !important;
        overflow: hidden;
    }

    .sancion-evidencia-modal > .header {
        background: linear-gradient(135deg, var(--sancion-primary) 0%, var(--sancion-primary-dark) 100%) !important;
        color: white !important;
        font-weight: 900 !important;
    }

    .sancion-edit-modal > .header {
        background: linear-gradient(135deg, var(--sancion-secondary) 0%, var(--sancion-primary-dark) 100%) !important;
        color: white !important;
        font-weight: 900 !important;
    }

    .sancion-evidencia-modal > .header i,
    .sancion-edit-modal > .header i {
        margin-right: 8px !important;
    }

    .evidencia-modal-content {
        text-align: center;
        padding: 20px !important;
    }

    #imagen-evidencia {
        max-width: 100%;
        max-height: 72vh;
        border-radius: 14px;
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.18);
        object-fit: contain;
    }

    .sancion-edit-modal > .content {
        padding: 20px !important;
    }

    .sancion-modal-actions {
        display: flex !important;
        justify-content: flex-end;
        gap: 10px;
        flex-wrap: wrap;
        padding: 16px 20px !important;
        background: var(--sancion-soft-bg) !important;
        border-top: 1px solid #f1f5f9 !important;
    }

    .sancion-modal-actions .button,
    .sancion-modal-actions .btn {
        min-height: 42px;
        border-radius: 12px !important;
        display: inline-flex !important;
        align-items: center;
        justify-content: center;
        gap: 7px;
    }

    @media (max-width: 1200px) {
        .sanciones-layout {
            grid-template-columns: 1fr;
        }

        .sancion-form-card {
            position: static;
        }
    }

    @media (max-width: 900px) {
        .sanciones-grid {
            grid-template-columns: 1fr;
        }

        .sanciones-toolbar {
            grid-template-columns: 1fr;
        }

        .btn-clear-filters {
            width: 100%;
        }

        .sancion-actions {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 768px) {
        .sanciones-hero {
            border-radius: 0 0 24px 24px;
            margin: -8px -4px 18px;
            padding: 22px;
        }

        .sanciones-hero-content {
            align-items: stretch;
        }

        .sanciones-hero-left {
            align-items: flex-start;
        }

        .sanciones-hero-icon {
            width: 54px;
            height: 54px;
            border-radius: 18px;
        }

        .sanciones-title {
            font-size: 1.55rem;
        }

        .sanciones-subtitle {
            font-size: 0.92rem;
        }

        .sanciones-hero-pill {
            width: 100%;
            justify-content: center;
            border-radius: 16px;
        }

        .sancion-form-card,
        .sanciones-list-card,
        .empty-sanciones-card {
            border-radius: 18px;
        }

        .sancion-card-header,
        .sancion-card-body,
        .sanciones-toolbar {
            padding: 16px;
        }

        .sancion-card-icon {
            width: 46px;
            height: 46px;
            border-radius: 14px;
        }

        .sancion-card-title {
            font-size: 1.04rem;
        }

        .sancion-card-subtitle {
            font-size: 0.85rem;
        }

        .sancion-form-grid {
            grid-template-columns: 1fr;
            gap: 0;
        }

        .sanciones-summary {
            padding: 14px 16px 0;
        }

        .sanciones-grid {
            padding: 16px;
        }

        .sancion-item-card:hover {
            transform: none;
        }

        .sancion-item-footer {
            padding: 12px;
            background: #ffffff;
        }

        .sancion-actions {
            grid-template-columns: 1fr;
            gap: 8px;
        }

        .sancion-actions .btn,
        .sancion-actions button,
        .sancion-actions a {
            min-height: 48px;
            font-size: 0.9rem;
        }

        .sanciones-pagination {
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

        .upload-placeholder {
            min-height: 190px;
            padding: 20px 16px;
        }

        .upload-area {
            min-height: 190px;
        }

        input[type="text"],
        input[type="number"],
        select,
        textarea {
            font-size: 16px !important;
        }

        .sancion-modal-actions {
            padding: 14px !important;
        }

        .sancion-modal-actions .button,
        .sancion-modal-actions .btn {
            flex: 1;
        }
    }

    @media (max-width: 420px) {
        .sanciones-hero-left {
            gap: 12px;
        }

        .sancion-card-title-wrap,
        .sancion-item-header {
            align-items: flex-start;
        }

        .sancion-item-icon {
            width: 42px;
            height: 42px;
            border-radius: 13px;
        }

        .sancion-item-title {
            font-size: 0.98rem;
        }

        .sancion-badge {
            max-width: 100%;
            white-space: normal;
            line-height: 1.2;
        }

        .sancion-modal-actions {
            flex-direction: column-reverse;
        }

        .sancion-modal-actions .button,
        .sancion-modal-actions .btn {
            width: 100%;
        }
    }
</style>

<script>
    const BASE_URL = "{{ url('/') }}";

    let sancionesOriginales = [];
    let sancionesFiltradas = [];
    let paginaActualSanciones = 1;
    let sancionesPorPagina = 6;
    let isSubmittingSancion = false;
    let isSubmittingSancionEdit = false;

    $(document).ready(function() {
        $('#modal-evidencia').modal();
        $('#modal-editar-sancion').modal();

        $('select[name="user_id"]').dropdown({
            fullTextSearch: true,
            clearable: true,
            placeholder: 'Selecciona un usuario'
        });

        $('#filtro-estado-sancion').dropdown();

        configurarUploadEvidencia();
        cargarSanciones();

        $('#buscar-sancion').on('keyup input change', function() {
            filtrarSanciones();
        });

        $('#filtro-estado-sancion').on('change', function() {
            filtrarSanciones();
        });

        $('#form-sancion').submit(function(e) {
            e.preventDefault();

            if (isSubmittingSancion) {
                return false;
            }

            const $btn = $('#btn-sancion');
            const fileInput = $('input[name="evidencia"]')[0];
            const file = fileInput.files[0];

            if (!file) {
                alertify.error('Debes seleccionar una imagen de evidencia.');
                return;
            }

            const validation = validarEvidencia(file);

            if (!validation.valid) {
                alertify.error(validation.message);
                fileInput.value = '';
                limpiarPreviewEvidencia();
                return;
            }

            isSubmittingSancion = true;
            $btn.addClass('loading disabled').html('<i class="spinner loading icon"></i> Enviando...');

            const formData = new FormData(this);

            $.ajax({
                url: "{{ route('admin.sancion.crearSancion') }}",
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(res) {
                    alertify.alert(res.header, res.message, function() {
                        cargarSanciones();
                    });

                    resetFormularioSancion();
                },
                error: function() {
                    alertify.error('Error al registrar sanción');
                },
                complete: function() {
                    $btn.removeClass('loading disabled').html('<i class="gavel icon"></i> Registrar sanción');
                    isSubmittingSancion = false;
                }
            });
        });
    });

    function configurarUploadEvidencia() {
        const uploadArea = document.getElementById('upload-area-evidencia');
        const fileInput = document.getElementById('evidencia-input');

        if (!uploadArea || !fileInput) {
            return;
        }

        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(function(eventName) {
            uploadArea.addEventListener(eventName, preventDefaults, false);
        });

        ['dragenter', 'dragover'].forEach(function(eventName) {
            uploadArea.addEventListener(eventName, function() {
                uploadArea.classList.add('dragover');
            }, false);
        });

        ['dragleave', 'drop'].forEach(function(eventName) {
            uploadArea.addEventListener(eventName, function() {
                uploadArea.classList.remove('dragover');
            }, false);
        });

        uploadArea.addEventListener('drop', function(e) {
            const dt = e.dataTransfer;

            if (dt.files.length > 0) {
                fileInput.files = dt.files;
                handleEvidenciaPreview(fileInput.files[0]);
            }
        });

        fileInput.addEventListener('change', function() {
            if (this.files.length > 0) {
                handleEvidenciaPreview(this.files[0]);
            }
        });

        $(document).on('click', '#remove-preview-btn-evidencia', function(e) {
            e.preventDefault();
            e.stopPropagation();

            fileInput.value = '';
            limpiarPreviewEvidencia();
        });
    }

    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    function validarEvidencia(file) {
        if (file.size > 5 * 1024 * 1024) {
            return {
                valid: false,
                message: 'La imagen no debe pesar más de 5 MB.'
            };
        }

        if (!file.type.startsWith('image/')) {
            return {
                valid: false,
                message: 'El archivo debe ser una imagen válida.'
            };
        }

        return {
            valid: true,
            message: ''
        };
    }

    function handleEvidenciaPreview(file) {
        if (!file) {
            return;
        }

        const validation = validarEvidencia(file);

        if (!validation.valid) {
            alertify.error(validation.message);
            $('#evidencia-input').val('');
            limpiarPreviewEvidencia();
            return;
        }

        const reader = new FileReader();

        reader.onload = function(e) {
            $('#preview-content-evidencia').html(`
                <button type="button" class="remove-preview-btn-evidencia" id="remove-preview-btn-evidencia" title="Quitar imagen">
                    <i class="times icon"></i>
                </button>

                <img src="${e.target.result}" alt="Preview" class="preview-image-evidencia">

                <span class="preview-badge-evidencia">
                    <i class="check icon"></i>
                    Imagen seleccionada
                </span>
            `);
        };

        reader.readAsDataURL(file);

        $('#upload-placeholder-evidencia').hide();
        $('#upload-preview-evidencia').show();
    }

    function limpiarPreviewEvidencia() {
        $('#preview-content-evidencia').html(`
            <button type="button" class="remove-preview-btn-evidencia" id="remove-preview-btn-evidencia" title="Quitar imagen">
                <i class="times icon"></i>
            </button>
        `);

        $('#upload-preview-evidencia').hide();
        $('#upload-placeholder-evidencia').css('display', 'flex');
    }

    function resetFormularioSancion() {
        $('#form-sancion')[0].reset();
        $('select[name="user_id"]').dropdown('clear');
        $('#evidencia-input').val('');
        limpiarPreviewEvidencia();
    }

    function cargarSanciones() {
        $('#sanciones-loader').show();
        $('#sanciones-container').empty();
        $('#sanciones-pagination').hide().empty();
        $('#sin-sanciones').hide();
        $('#sin-resultados').hide();
        $('#sanciones-error').hide();

        $.ajax({
            url: "{{ route('admin.sancion.obtenerSanciones') }}",
            method: 'GET',
            dataType: 'json',
            data: {
                start: 0,
                length: 1000
            },
            success: function(response) {
                $('#sanciones-loader').hide();

                sancionesOriginales = response.data || [];
                renderizarSanciones(sancionesOriginales);
            },
            error: function() {
                $('#sanciones-loader').hide();
                $('#sanciones-error').show();
            }
        });
    }

    function renderizarSanciones(sanciones) {
        sancionesFiltradas = sanciones;
        paginaActualSanciones = 1;
        renderizarPaginaSanciones();
    }

    function renderizarPaginaSanciones() {
        const container = $('#sanciones-container');
        const pagination = $('#sanciones-pagination');

        container.empty();
        pagination.empty();

        $('#total-sanciones').text(sancionesFiltradas.length);

        $('#sin-sanciones').hide();
        $('#sin-resultados').hide();
        $('#sanciones-error').hide();

        if (sancionesOriginales.length === 0) {
            pagination.hide();
            $('#sin-sanciones').show();
            return;
        }

        if (sancionesFiltradas.length === 0) {
            pagination.hide();
            $('#sin-resultados').show();
            return;
        }

        const totalPaginas = Math.ceil(sancionesFiltradas.length / sancionesPorPagina);
        const inicio = (paginaActualSanciones - 1) * sancionesPorPagina;
        const fin = inicio + sancionesPorPagina;
        const sancionesPagina = sancionesFiltradas.slice(inicio, fin);

        sancionesPagina.forEach(function(sancion) {
            const id = extraerTexto(sancion.id || '');
            const usuario = extraerTexto(sancion.usuario || 'Usuario no disponible');
            const motivo = extraerTexto(sancion.motivo || 'Sin motivo');
            const monto = extraerTexto(sancion.monto || '$0.00');
            const incidencia = extraerTexto(sancion.incidencia || 'Sin incidencia');
            const comentario = extraerTexto(sancion.comentario || 'Sin comentario');
            const pago = extraerTexto(sancion.pago || sancion.estado || 'Pendiente');
            const evidenciaHtml = sancion.evidencia || '';
            const estado = obtenerEstadoPago(pago);
            const evidenciaSrc = obtenerSrcEvidencia(evidenciaHtml);

            const evidenciaButton = evidenciaSrc
                ? `
                    <button type="button" class="btn btn-secondary btn-sm btn-ver-evidencia" data-img="${escapeHtml(evidenciaSrc)}">
                        <i class="image icon"></i>
                        Evidencia
                    </button>
                `
                : `
                    <button type="button" class="btn btn-secondary btn-sm disabled" disabled>
                        <i class="image outline icon"></i>
                        Sin evidencia
                    </button>
                `;

            const accionesHtml = `
                ${evidenciaButton}

                <button
                    type="button"
                    class="btn btn-secondary btn-sm btn-editar"
                    data-id="${escapeHtml(id)}"
                    data-motivo="${escapeHtml(motivo)}"
                    data-monto="${escapeHtml(extraerNumero(monto))}"
                    data-incidencia="${escapeHtml(incidencia)}"
                    data-comentario="${escapeHtml(comentario)}"
                    data-estado="${escapeHtml(estado.valor)}"
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
                <div class="sancion-item-card">
                    <div class="sancion-item-header">
                        <div class="sancion-item-icon">
                            <i class="gavel icon"></i>
                        </div>

                        <div style="min-width: 0; flex: 1;">
                            <h3 class="sancion-item-title">${escapeHtml(usuario)}</h3>
                            <div class="sancion-item-subtitle">${escapeHtml(motivo)}</div>

                            <div class="sancion-item-badges">
                                <span class="sancion-badge ${estado.clase}">
                                    <i class="${estado.icono} icon"></i>
                                    ${escapeHtml(estado.texto)}
                                </span>

                                <span class="sancion-badge incidencia">
                                    <i class="hashtag icon"></i>
                                    ${escapeHtml(incidencia)}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="sancion-item-body">
                        <div class="sancion-meta-item">
                            <i class="dollar sign icon"></i>
                            <span>${escapeHtml(monto)}</span>
                        </div>

                        <div class="sancion-meta-item">
                            <i class="comment alternate outline icon"></i>
                            <span class="sancion-comentario">${escapeHtml(comentario)}</span>
                        </div>
                    </div>

                    <div class="sancion-item-footer">
                        <div class="sancion-actions">
                            ${accionesHtml}
                        </div>
                    </div>
                </div>
            `;

            container.append(card);
        });

        renderizarPaginacionSanciones(totalPaginas, sancionesFiltradas.length);
    }

    function obtenerSrcEvidencia(html) {
        if (!html) {
            return '';
        }

        const temp = document.createElement('div');
        temp.innerHTML = html;

        const btn = temp.querySelector('[data-img]');
        const img = temp.querySelector('img');
        const link = temp.querySelector('a');

        if (btn && btn.getAttribute('data-img')) {
            return btn.getAttribute('data-img');
        }

        if (img && img.getAttribute('src')) {
            return img.getAttribute('src');
        }

        if (link && link.getAttribute('href')) {
            return link.getAttribute('href');
        }

        return '';
    }

    function obtenerEstadoPago(valor) {
        const texto = normalizarTexto(extraerTexto(valor || ''));

        if (texto.includes('pagado')) {
            return {
                valor: 'pagado',
                texto: 'Pagado',
                clase: 'pagado',
                icono: 'check circle'
            };
        }

        if (texto.includes('rechazado')) {
            return {
                valor: 'rechazado',
                texto: 'Rechazado',
                clase: 'rechazado',
                icono: 'times circle'
            };
        }

        return {
            valor: 'pendiente',
            texto: 'Pendiente',
            clase: 'pendiente',
            icono: 'clock outline'
        };
    }

    function extraerNumero(valor) {
        const texto = extraerTexto(valor || '');
        const match = texto.replace(/,/g, '').match(/[\d]+(\.\d+)?/);

        return match ? match[0] : '';
    }

    function renderizarPaginacionSanciones(totalPaginas, totalSanciones) {
        const pagination = $('#sanciones-pagination');

        pagination.empty();

        if (totalPaginas <= 1) {
            pagination.hide();
            return;
        }

        const inicio = ((paginaActualSanciones - 1) * sancionesPorPagina) + 1;
        const fin = Math.min(paginaActualSanciones * sancionesPorPagina, totalSanciones);

        pagination.show();

        pagination.append(`
            <button
                type="button"
                class="pagination-btn"
                onclick="cambiarPaginaSanciones(${paginaActualSanciones - 1})"
                ${paginaActualSanciones === 1 ? 'disabled' : ''}
            >
                <i class="chevron left icon"></i>
            </button>
        `);

        const paginas = obtenerPaginasVisiblesSanciones(totalPaginas);

        paginas.forEach(function(pagina) {
            if (pagina === '...') {
                pagination.append(`<span class="pagination-info">...</span>`);
                return;
            }

            pagination.append(`
                <button
                    type="button"
                    class="pagination-btn ${pagina === paginaActualSanciones ? 'active' : ''}"
                    onclick="cambiarPaginaSanciones(${pagina})"
                >
                    ${pagina}
                </button>
            `);
        });

        pagination.append(`
            <button
                type="button"
                class="pagination-btn"
                onclick="cambiarPaginaSanciones(${paginaActualSanciones + 1})"
                ${paginaActualSanciones === totalPaginas ? 'disabled' : ''}
            >
                <i class="chevron right icon"></i>
            </button>
        `);

        pagination.append(`
            <div class="pagination-info">
                Mostrando ${inicio}-${fin} de ${totalSanciones}
            </div>
        `);
    }

    function cambiarPaginaSanciones(pagina) {
        const totalPaginas = Math.ceil(sancionesFiltradas.length / sancionesPorPagina);

        if (pagina < 1 || pagina > totalPaginas) {
            return;
        }

        paginaActualSanciones = pagina;
        renderizarPaginaSanciones();

        document.querySelector('.sanciones-list-card')?.scrollIntoView({
            behavior: 'smooth',
            block: 'start'
        });
    }

    function obtenerPaginasVisiblesSanciones(totalPaginas) {
        const paginas = [];

        if (totalPaginas <= 5) {
            for (let i = 1; i <= totalPaginas; i++) {
                paginas.push(i);
            }

            return paginas;
        }

        paginas.push(1);

        if (paginaActualSanciones > 3) {
            paginas.push('...');
        }

        const inicio = Math.max(2, paginaActualSanciones - 1);
        const fin = Math.min(totalPaginas - 1, paginaActualSanciones + 1);

        for (let i = inicio; i <= fin; i++) {
            paginas.push(i);
        }

        if (paginaActualSanciones < totalPaginas - 2) {
            paginas.push('...');
        }

        paginas.push(totalPaginas);

        return paginas;
    }

    function filtrarSanciones() {
        const busqueda = normalizarTexto($('#buscar-sancion').val());
        const estadoFiltro = $('#filtro-estado-sancion').val();

        const filtradas = sancionesOriginales.filter(function(sancion) {
            const usuario = extraerTexto(sancion.usuario || '');
            const motivo = extraerTexto(sancion.motivo || '');
            const monto = extraerTexto(sancion.monto || '');
            const incidencia = extraerTexto(sancion.incidencia || '');
            const comentario = extraerTexto(sancion.comentario || '');
            const pago = extraerTexto(sancion.pago || sancion.estado || '');

            const textoCompleto = normalizarTexto(
                usuario + ' ' + motivo + ' ' + monto + ' ' + incidencia + ' ' + comentario + ' ' + pago
            );

            const estado = obtenerEstadoPago(pago);

            const coincideBusqueda = textoCompleto.includes(busqueda);
            const coincideEstado = estadoFiltro === 'todos' || estado.valor === estadoFiltro;

            return coincideBusqueda && coincideEstado;
        });

        renderizarSanciones(filtradas);
    }

    function limpiarFiltrosSanciones() {
        $('#buscar-sancion').val('');
        $('#filtro-estado-sancion').dropdown('set selected', 'todos');
        renderizarSanciones(sancionesOriginales);
    }

    $(document).on('click', '.btn-ver-evidencia', function() {
        const src = $(this).data('img');

        $('#imagen-evidencia').attr('src', src);
        $('#modal-evidencia').modal('show');
    });

    $(document).on('click', '.btn-editar', function() {
        const $btn = $(this);

        $('#edit-id').val($btn.data('id'));
        $('#edit-motivo').val($btn.data('motivo'));
        $('#edit-monto').val($btn.data('monto'));
        $('#edit-incidencia').val($btn.data('incidencia'));
        $('#edit-comentario').val($btn.data('comentario'));
        $('#edit-estado').val($btn.data('estado'));

        $('#modal-editar-sancion').modal('show');
    });

    $('#form-editar-sancion').submit(function(e) {
        e.preventDefault();

        if (isSubmittingSancionEdit) {
            return false;
        }

        const $btn = $('#btn-guardar-cambios');
        const id = $('#edit-id').val();
        const data = $(this).serialize();

        isSubmittingSancionEdit = true;
        $btn.addClass('loading disabled').prop('disabled', true);

        $.ajax({
            url: `${BASE_URL}/administrador/sancion/actualizar-sancion/${id}`,
            method: 'POST',
            data: data,
            success: function(res) {
                alertify.alert(res.header, res.message, function() {
                    $('#modal-editar-sancion').modal('hide');
                    cargarSanciones();
                });
            },
            error: function() {
                alertify.error('Error al actualizar');
            },
            complete: function() {
                $btn.removeClass('loading disabled').prop('disabled', false);
                isSubmittingSancionEdit = false;
            }
        });
    });

    $(document).on('click', '.btn-eliminar', function() {
        const id = $(this).data('id');

        alertify.confirm(
            '¿Eliminar sanción?',
            '¿Estás seguro de que deseas eliminar esta sanción?',
            function() {
                mostrarLoaderPantalla();

                setTimeout(function() {
                    $.ajax({
                        url: `${BASE_URL}/administrador/sancion/eliminar-sancion/${id}`,
                        method: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(res) {
                            alertify.alert(res.header, res.message, function() {
                                cargarSanciones();
                            });
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