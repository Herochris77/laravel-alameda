<x-app-layout>
    <div class="documentos-admin-page">
        <div class="documentos-hero">
            <div class="documentos-hero-content">
                <div class="documentos-hero-left">
                    <div class="documentos-hero-icon">
                        <i class="file alternate icon"></i>
                    </div>

                    <div>
                        <h1 class="documentos-title">Gestión de Documentos</h1>
                        <p class="documentos-subtitle">Sube documentos, reglamentos y comprobantes de gastos</p>
                    </div>
                </div>

                <div class="documentos-hero-pill">
                    <i class="folder open icon"></i>
                    Administración documental
                </div>
            </div>
        </div>

        <div class="documentos-layout">
            <div class="documento-form-card">
                <div class="documento-card-header">
                    <div class="documento-card-title-wrap">
                        <div class="documento-card-icon">
                            <i class="plus circle icon"></i>
                        </div>

                        <div>
                            <h3 class="documento-card-title">Registrar Documento</h3>
                            <p class="documento-card-subtitle">Carga documentos generales o comprobantes de gasto.</p>
                        </div>
                    </div>
                </div>

                <div class="documento-card-body">
                    <form id="form-documento" enctype="multipart/form-data">
                        @csrf

                        <div class="form-group">
                            <label class="form-label">Título *</label>
                            <div class="input-icon-wrapper">
                                <i class="heading icon"></i>
                                <input
                                    type="text"
                                    name="titulo"
                                    class="form-input documento-input input-with-icon"
                                    placeholder="Ej. Recibo de luz febrero 2026"
                                    autocomplete="off"
                                    required
                                >
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Descripción *</label>
                            <textarea
                                name="descripcion"
                                class="form-input documento-input documento-textarea"
                                rows="3"
                                placeholder="Breve descripción..."
                                autocomplete="off"
                                required
                            ></textarea>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Tipo de documento *</label>
                            <select name="tipo_documento" id="tipo-documento" class="ui fluid dropdown documento-input" autocomplete="off" required>
                                <option value="">Selecciona tipo</option>
                                <option value="general">General / Reglamento</option>
                                <option value="referencia">Comprobante de Gasto</option>
                            </select>
                        </div>

                        <div id="gasto-fields" class="gasto-fields" style="display: none;">
                            <div class="gasto-fields-header">
                                <div class="gasto-fields-icon">
                                    <i class="receipt icon"></i>
                                </div>

                                <div>
                                    <strong>Datos del gasto</strong>
                                    <span>Completa esta información para registrar el comprobante.</span>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Categoría del gasto *</label>
                                <select name="categoria_gasto" id="categoria-gasto" class="ui fluid dropdown documento-input">
                                    <option value="">Selecciona categoría</option>
                                    <option value="luz">Luz / Electricidad</option>
                                    <option value="agua">Agua</option>
                                    <option value="mantenimiento">Mantenimiento</option>
                                    <option value="seguridad">Seguridad</option>
                                    <option value="limpieza">Limpieza</option>
                                    <option value="jardineria">Jardinería</option>
                                    <option value="otro">Otro</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Cantidad (monto del gasto) *</label>
                                <div class="input-icon-wrapper">
                                    <i class="dollar sign icon"></i>
                                    <input
                                        type="number"
                                        name="cantidad"
                                        id="cantidad-gasto"
                                        class="form-input documento-input input-with-icon"
                                        step="0.01"
                                        min="0"
                                        placeholder="0.00"
                                    >
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Concepto de pago asociado</label>

                                <select
                                    name="concepto_pago"
                                    id="concepto-pago"
                                    class="ui fluid search selection dropdown documento-input"
                                >
                                    <option value="">Buscar o seleccionar concepto</option>
                                </select>

                                <small class="form-help-text">
                                    Puedes escribir para buscar dentro de la lista de conceptos.
                                </small>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Archivo del documento *</label>

                            <div class="upload-area" id="upload-area-archivo">
                                <input type="file" name="doc_path" id="doc-path-input" accept=".pdf,.doc,.docx,.xls,.xlsx,image/*" hidden>

                                <label for="doc-path-input" class="upload-label">
                                    <div class="upload-placeholder" id="upload-placeholder">
                                        <div class="upload-icon">
                                            <i class="cloud upload icon"></i>
                                        </div>

                                        <div class="upload-text">
                                            <span class="primary">Arrastra el archivo aquí</span>
                                            <span class="secondary">o haz clic para seleccionar</span>
                                        </div>

                                        <div class="upload-formats">PDF, Word, Excel o Imagen, máximo 5MB</div>
                                    </div>

                                    <div id="upload-preview" class="upload-preview-container" style="display: none;">
                                        <div id="preview-content" class="preview-content">
                                            <button type="button" class="remove-preview-btn" id="remove-preview-btn" title="Quitar archivo">
                                                <i class="times icon"></i>
                                            </button>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <button id="btn-documento" class="btn btn-primary btn-lg btn-submit-documento" type="submit">
                            <i class="upload icon"></i>
                            Subir documento
                        </button>
                    </form>
                </div>
            </div>

            <div class="documentos-list-card">
                <div class="documento-card-header list-header">
                    <div class="documento-card-title-wrap">
                        <div class="documento-card-icon secondary">
                            <i class="folder open icon"></i>
                        </div>

                        <div>
                            <h3 class="documento-card-title">Documentos Registrados</h3>
                            <p class="documento-card-subtitle">Consulta, filtra y administra los archivos cargados.</p>
                        </div>
                    </div>
                </div>

                <div class="documentos-toolbar">
                    <div class="filter-field">
                        <label class="form-label">Buscar documento</label>
                        <div class="ui icon input documentos-search-input">
                            <i class="search icon"></i>
                            <input
                                type="text"
                                id="buscar-documento"
                                placeholder="Título, categoría, tipo, monto o fecha..."
                                autocomplete="off"
                            >
                        </div>
                    </div>

                    <div class="filter-field">
                        <label class="form-label">Tipo</label>
                        <select id="filtro-tipo-documento" class="ui fluid dropdown documento-input">
                            <option value="todos">Todos</option>
                            <option value="general">General / Reglamento</option>
                            <option value="referencia">Comprobante de Gasto</option>
                        </select>
                    </div>

                    <button type="button" class="btn btn-secondary btn-clear-filters" onclick="limpiarFiltrosDocumentos()">
                        <i class="times icon"></i>
                        Limpiar
                    </button>
                </div>

                <div class="documentos-summary">
                    <div class="summary-item">
                        <div class="summary-icon">
                            <i class="file alternate icon"></i>
                        </div>

                        <div>
                            <span>Documentos encontrados</span>
                            <strong id="total-documentos">0</strong>
                        </div>
                    </div>
                </div>

                <div id="documentos-loader" class="documentos-loader">
                    <div class="ui active centered inline text loader large">Cargando documentos...</div>
                </div>

                <div id="documentos-container" class="documentos-grid"></div>

                <div id="documentos-pagination" class="documentos-pagination" style="display: none;"></div>

                <div id="sin-documentos" class="empty-documentos-card" style="display: none;">
                    <div class="empty-documentos-icon">
                        <i class="folder open outline icon"></i>
                    </div>

                    <h3>No hay documentos registrados</h3>
                    <p>Aún no se han subido documentos o comprobantes.</p>
                </div>

                <div id="sin-resultados" class="empty-documentos-card" style="display: none;">
                    <div class="empty-documentos-icon">
                        <i class="search icon"></i>
                    </div>

                    <h3>No se encontraron resultados</h3>
                    <p>No hay documentos que coincidan con tu búsqueda o filtro.</p>

                    <button type="button" class="btn btn-secondary btn-sm" onclick="limpiarFiltrosDocumentos()">
                        <i class="undo icon"></i>
                        Limpiar filtros
                    </button>
                </div>

                <div id="documentos-error" class="empty-documentos-card error" style="display: none;">
                    <div class="empty-documentos-icon error">
                        <i class="warning sign icon"></i>
                    </div>

                    <h3>Error al cargar documentos</h3>
                    <p>No se pudieron cargar los documentos. Intenta nuevamente.</p>

                    <button type="button" class="btn btn-primary btn-sm" onclick="cargarDocumentos()">
                        <i class="refresh icon"></i>
                        Reintentar
                    </button>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<div class="ui fullscreen modal documento-preview-modal" id="modal-archivo">
    <div class="header">
        <i class="file alternate icon"></i>
        Documento cargado
    </div>

    <div class="content">
        <div id="contenedor-imagen" class="preview-modal-body">
            <img id="archivo-preview-img" src="" alt="Vista previa">
        </div>

        <div id="contenedor-pdf" class="preview-modal-body">
            <iframe id="archivo-preview-pdf" src=""></iframe>
        </div>

        <div id="contenedor-download" class="preview-modal-download">
            <div class="download-icon">
                <i class="download icon"></i>
            </div>

            <h3>Vista previa no disponible</h3>
            <p>Este tipo de archivo puede descargarse para consultarlo.</p>

            <a id="archivo-preview-download" href="" target="_blank" class="btn btn-primary">
                <i class="download icon"></i>
                Descargar documento
            </a>
        </div>
    </div>
</div>

<style>
    :root {
        --primary: #8b5cf6;
        --primary-dark: #7c3aed;
        --secondary: #667eea;
        --success: #10b981;
        --danger: #ef4444;
        --danger-dark: #dc2626;
        --warning: #f59e0b;
        --info: #3b82f6;
        --text-main: #0f172a;
        --text-muted: #64748b;
        --text-soft: #94a3b8;
        --border: #e2e8f0;
        --surface: #ffffff;
        --soft-bg: #f8fafc;
    }

    .documentos-admin-page {
        width: 100%;
        padding-bottom: 24px;
    }

    .documentos-hero {
        background:
            radial-gradient(circle at top right, rgba(255,255,255,0.24), transparent 34%),
            radial-gradient(circle at bottom left, rgba(255,255,255,0.14), transparent 30%),
            linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        border-radius: 26px;
        padding: 26px;
        color: white;
        margin-bottom: 22px;
        box-shadow: 0 12px 30px rgba(124, 58, 237, 0.18);
        overflow: hidden;
    }

    .documentos-hero-content {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 18px;
        flex-wrap: wrap;
    }

    .documentos-hero-left {
        display: flex;
        align-items: center;
        gap: 16px;
        min-width: 0;
    }

    .documentos-hero-icon {
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

    .documentos-hero-icon i {
        color: white;
        font-size: 1.8rem;
        margin: 0 !important;
    }

    .documentos-title {
        margin: 0 0 6px 0;
        font-size: clamp(1.55rem, 3vw, 2.15rem);
        font-weight: 900;
        letter-spacing: -0.035em;
        line-height: 1.08;
    }

    .documentos-subtitle {
        margin: 0;
        opacity: 0.92;
        font-size: 1rem;
        line-height: 1.45;
    }

    .documentos-hero-pill {
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

    .documentos-hero-pill i {
        margin: 0 !important;
    }

    .documentos-layout {
        display: grid;
        grid-template-columns: minmax(300px, 420px) minmax(0, 1fr);
        gap: 24px;
        align-items: start;
    }

    .documento-form-card,
    .documentos-list-card,
    .empty-documentos-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 22px;
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.06);
        overflow: hidden;
    }

    .documento-form-card {
        position: sticky;
        top: 18px;
    }

    .documento-card-header {
        padding: 18px 20px;
        border-bottom: 1px solid #f1f5f9;
        background: linear-gradient(180deg, #ffffff 0%, #fbfdff 100%);
    }

    .documento-card-title-wrap {
        display: flex;
        align-items: center;
        gap: 14px;
        min-width: 0;
    }

    .documento-card-icon {
        width: 52px;
        height: 52px;
        border-radius: 16px;
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        box-shadow: 0 8px 18px rgba(139, 92, 246, 0.24);
    }

    .documento-card-icon.secondary {
        background: linear-gradient(135deg, var(--secondary) 0%, var(--primary-dark) 100%);
    }

    .documento-card-icon i {
        margin: 0 !important;
        font-size: 1.25rem;
    }

    .documento-card-title {
        margin: 0;
        color: var(--text-main);
        font-size: 1.12rem;
        font-weight: 900;
        line-height: 1.25;
    }

    .documento-card-subtitle {
        margin: 4px 0 0 0;
        color: var(--text-muted);
        font-size: 0.9rem;
        line-height: 1.35;
    }

    .documento-card-body {
        padding: 20px;
    }

    .documento-input {
        min-height: 46px;
        border-radius: 14px !important;
        width: 100%;
        max-width: 100%;
        box-sizing: border-box;
    }

    .documento-textarea {
        resize: vertical;
        min-height: 96px;
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
        pointer-events: none;
        z-index: 1;
        margin: 0 !important;
    }

    .input-with-icon {
        padding-left: 42px !important;
    }

    .form-help-text {
        display: block;
        color: var(--text-soft);
        font-size: 0.8rem;
        margin-top: 6px;
        line-height: 1.35;
    }

    .gasto-fields {
        background: var(--soft-bg);
        border: 1px solid #eef2f7;
        border-radius: 18px;
        padding: 16px;
        margin-bottom: 18px;
    }

    .gasto-fields-header {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        margin-bottom: 16px;
    }

    .gasto-fields-icon {
        width: 42px;
        height: 42px;
        border-radius: 14px;
        background: #ede9fe;
        color: var(--primary-dark);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .gasto-fields-icon i {
        margin: 0 !important;
    }

    .gasto-fields-header strong {
        display: block;
        color: var(--text-main);
        font-weight: 900;
        margin-bottom: 3px;
    }

    .gasto-fields-header span {
        display: block;
        color: var(--text-muted);
        font-size: 0.84rem;
        line-height: 1.35;
    }

    .upload-area {
        width: 100%;
        min-height: 220px;
        border-radius: 18px;
        border: 2px dashed #cbd5e1;
        background: var(--soft-bg);
        transition: border-color 0.2s ease, background 0.2s ease;
        overflow: hidden;
    }

    .upload-area:hover,
    .upload-area.dragover {
        border-color: var(--primary);
        background: #f5f3ff;
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
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 14px;
        box-shadow: 0 8px 18px rgba(139, 92, 246, 0.24);
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
        color: var(--text-main);
        font-weight: 900;
    }

    .upload-text .secondary,
    .upload-formats {
        color: var(--text-muted);
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

    .preview-content {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 12px;
        position: relative;
    }

    .preview-image {
        max-width: 100%;
        max-height: 210px;
        border-radius: 14px;
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.12);
        object-fit: contain;
    }

    .preview-file {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 14px;
        background: #f5f3ff;
        border-radius: 16px;
        border: 1px solid rgba(139, 92, 246, 0.24);
        width: 100%;
        box-sizing: border-box;
    }

    .preview-file-icon {
        width: 52px;
        height: 52px;
        border-radius: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.45rem;
        flex-shrink: 0;
    }

    .preview-file-icon i {
        margin: 0 !important;
    }

    .preview-file-icon.pdf {
        background: #fee2e2;
        color: #dc2626;
    }

    .preview-file-icon.doc {
        background: #dbeafe;
        color: #2563eb;
    }

    .preview-file-icon.xls {
        background: #d1fae5;
        color: #059669;
    }

    .preview-file-icon.default {
        background: #e2e8f0;
        color: #475569;
    }

    .preview-file-info {
        flex: 1;
        min-width: 0;
    }

    .preview-file-name {
        font-weight: 900;
        color: var(--text-main);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .preview-file-size {
        font-size: 0.82rem;
        color: var(--text-muted);
        margin-top: 3px;
    }

    .preview-badge {
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

    .preview-badge i {
        margin: 0 !important;
    }

    .remove-preview-btn {
        position: absolute;
        top: -10px;
        right: -10px;
        width: 34px;
        height: 34px;
        border-radius: 999px;
        border: none;
        background: var(--danger);
        color: white;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background 0.2s ease, transform 0.2s ease;
        box-shadow: 0 8px 18px rgba(239, 68, 68, 0.28);
        z-index: 10;
    }

    .remove-preview-btn:hover {
        transform: scale(1.08);
        background: var(--danger-dark);
    }

    .remove-preview-btn i {
        margin: 0 !important;
    }

    .btn-submit-documento {
        width: 100%;
        min-height: 48px;
        border-radius: 14px;
        margin-top: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .documentos-toolbar {
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

    .documentos-search-input {
        width: 100%;
    }

    .documentos-search-input input {
        min-height: 46px;
        border-radius: 14px !important;
    }

    .btn-clear-filters {
        min-height: 46px;
        border-radius: 14px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        white-space: nowrap;
    }

    .documentos-summary {
        padding: 16px 20px 0;
    }

    .summary-item {
        display: inline-flex;
        align-items: center;
        gap: 12px;
        background: var(--soft-bg);
        border: 1px solid #eef2f7;
        border-radius: 16px;
        padding: 12px 14px;
    }

    .summary-icon {
        width: 42px;
        height: 42px;
        border-radius: 14px;
        background: #ede9fe;
        color: var(--primary-dark);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .summary-icon i {
        margin: 0 !important;
    }

    .summary-item span {
        display: block;
        color: var(--text-muted);
        font-size: 0.78rem;
        font-weight: 800;
        margin-bottom: 2px;
    }

    .summary-item strong {
        display: block;
        color: var(--text-main);
        font-size: 1.2rem;
        line-height: 1;
    }

    .documentos-loader {
        min-height: 220px;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 40px 20px;
    }

    .documentos-grid {
        padding: 20px;
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 16px;
    }

    .documento-item-card {
        border: 1px solid var(--border);
        border-radius: 18px;
        background: white;
        box-shadow: 0 6px 18px rgba(15, 23, 42, 0.05);
        overflow: hidden;
        display: flex;
        flex-direction: column;
    }

    .documento-item-header {
        padding: 16px;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: flex-start;
        gap: 12px;
    }

    .documento-item-icon {
        width: 46px;
        height: 46px;
        border-radius: 14px;
        background: #ede9fe;
        color: var(--primary-dark);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .documento-item-icon i {
        margin: 0 !important;
        font-size: 1.15rem;
    }

    .documento-item-title {
        margin: 0;
        color: var(--text-main);
        font-size: 1rem;
        font-weight: 900;
        line-height: 1.3;
        overflow-wrap: anywhere;
    }

    .documento-item-badges {
        display: flex;
        flex-wrap: wrap;
        gap: 7px;
        margin-top: 9px;
    }

    .doc-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 6px 9px;
        border-radius: 999px;
        font-size: 0.72rem;
        font-weight: 900;
        line-height: 1;
    }

    .doc-badge i {
        margin: 0 !important;
    }

    .doc-badge.tipo-general {
        background: #dbeafe;
        color: #1d4ed8;
    }

    .doc-badge.tipo-referencia {
        background: #dcfce7;
        color: #166534;
    }

    .doc-badge.categoria {
        background: #fef3c7;
        color: #92400e;
    }

    .documento-item-body {
        padding: 16px;
        flex: 1;
        display: grid;
        gap: 10px;
    }

    .documento-meta-item {
        display: flex;
        align-items: flex-start;
        gap: 9px;
        color: var(--text-muted);
        font-size: 0.88rem;
        line-height: 1.4;
        overflow-wrap: anywhere;
    }

    .documento-meta-item i {
        color: var(--primary);
        margin: 2px 0 0 0 !important;
        flex-shrink: 0;
    }

    .documento-item-footer {
        padding: 14px 16px 16px;
        border-top: 1px solid #f1f5f9;
        display: grid;
        grid-template-columns: 1fr auto;
        gap: 10px;
        align-items: center;
    }

    .documento-file-action {
        min-width: 0;
    }

    .documento-file-action .btn,
    .documento-file-action button,
    .documento-file-action a {
        min-height: 40px;
        border-radius: 12px !important;
        display: inline-flex !important;
        align-items: center;
        justify-content: center;
        gap: 7px;
    }

    .documento-actions {
        display: flex;
        gap: 8px;
        justify-content: flex-end;
        flex-wrap: wrap;
    }

    .documento-actions .btn,
    .documento-actions button,
    .documento-actions a {
        min-height: 40px;
        border-radius: 12px !important;
        display: inline-flex !important;
        align-items: center;
        justify-content: center;
        gap: 7px;
    }

    .documentos-pagination {
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
        border: 1px solid var(--border);
        background: #ffffff;
        color: var(--text-muted);
        border-radius: 12px;
        font-weight: 900;
        cursor: pointer;
        transition: background 0.2s ease, color 0.2s ease, border-color 0.2s ease;
    }

    .pagination-btn:hover {
        background: #f5f3ff;
        color: var(--primary-dark);
        border-color: rgba(139, 92, 246, 0.35);
    }

    .pagination-btn.active {
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        color: #ffffff;
        border-color: transparent;
        box-shadow: 0 8px 18px rgba(139, 92, 246, 0.22);
    }

    .pagination-btn:disabled {
        opacity: 0.45;
        cursor: not-allowed;
        background: var(--soft-bg);
        color: var(--text-soft);
    }

    .pagination-info {
        color: var(--text-muted);
        font-size: 0.86rem;
        font-weight: 800;
        margin: 0 8px;
    }

    .empty-documentos-card {
        margin: 20px;
        padding: 44px 20px;
        text-align: center;
    }

    .empty-documentos-icon {
        width: 78px;
        height: 78px;
        border-radius: 24px;
        margin: 0 auto 18px;
        background: linear-gradient(135deg, rgba(139, 92, 246, 0.12), rgba(124, 58, 237, 0.16));
        color: var(--primary);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .empty-documentos-icon.error {
        background: #fee2e2;
        color: var(--danger);
    }

    .empty-documentos-icon i {
        font-size: 2rem;
        margin: 0 !important;
    }

    .empty-documentos-card h3 {
        margin: 0 0 8px 0;
        color: #475569;
        font-weight: 900;
    }

    .empty-documentos-card p {
        margin: 0 0 18px 0;
        color: var(--text-soft);
        line-height: 1.5;
    }

    .documento-preview-modal > .header {
        background: linear-gradient(135deg, var(--secondary) 0%, var(--primary-dark) 100%) !important;
        color: white !important;
    }

    .documento-preview-modal > .header i {
        margin-right: 8px !important;
    }

    .documento-preview-modal > .content {
        text-align: center;
        padding: 20px !important;
    }

    .preview-modal-body {
        margin-bottom: 20px;
    }

    #archivo-preview-img {
        max-width: 90%;
        max-height: 70vh;
        border-radius: 14px;
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.18);
    }

    #archivo-preview-pdf {
        width: 92%;
        height: 72vh;
        border: 1px solid #ddd;
        border-radius: 14px;
    }

    .preview-modal-download {
        display: none;
        padding: 48px 20px;
    }

    .download-icon {
        width: 78px;
        height: 78px;
        border-radius: 24px;
        margin: 0 auto 18px;
        background: #ede9fe;
        color: var(--primary-dark);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .download-icon i {
        margin: 0 !important;
        font-size: 2rem;
    }

    .preview-modal-download h3 {
        margin: 0 0 8px 0;
        color: var(--text-main);
        font-weight: 900;
    }

    .preview-modal-download p {
        margin: 0 0 18px 0;
        color: var(--text-muted);
    }

    @media (max-width: 1200px) {
        .documentos-layout {
            grid-template-columns: 1fr;
        }

        .documento-form-card {
            position: static;
        }
    }

    @media (max-width: 900px) {
        .documentos-grid {
            grid-template-columns: 1fr;
        }

        .documentos-toolbar {
            grid-template-columns: 1fr;
        }

        .btn-clear-filters {
            width: 100%;
        }

        .documento-item-footer {
            grid-template-columns: 1fr;
        }

        .documento-file-action .btn,
        .documento-file-action button,
        .documento-file-action a {
            width: 100%;
        }

        .documento-actions {
            justify-content: stretch;
        }

        .documento-actions .btn,
        .documento-actions button,
        .documento-actions a {
            flex: 1;
        }
    }

    @media (max-width: 768px) {
        .documentos-hero {
            border-radius: 0 0 24px 24px;
            margin: -8px -4px 18px;
            padding: 22px;
        }

        .documentos-hero-content {
            align-items: stretch;
        }

        .documentos-hero-left {
            align-items: flex-start;
        }

        .documentos-hero-icon {
            width: 54px;
            height: 54px;
            border-radius: 18px;
        }

        .documentos-title {
            font-size: 1.55rem;
        }

        .documentos-subtitle {
            font-size: 0.92rem;
        }

        .documentos-hero-pill {
            width: 100%;
            justify-content: center;
            border-radius: 16px;
        }

        .documento-form-card,
        .documentos-list-card,
        .empty-documentos-card {
            border-radius: 18px;
        }

        .documento-card-header,
        .documento-card-body,
        .documentos-toolbar {
            padding: 16px;
        }

        .documento-card-icon {
            width: 46px;
            height: 46px;
            border-radius: 14px;
        }

        .documento-card-title {
            font-size: 1.04rem;
        }

        .documento-card-subtitle {
            font-size: 0.85rem;
        }

        .documentos-summary {
            padding: 14px 16px 0;
        }

        .documentos-grid {
            padding: 16px;
        }

        .documentos-pagination {
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

        #archivo-preview-img {
            max-width: 100%;
            max-height: 65vh;
        }

        #archivo-preview-pdf {
            width: 100%;
            height: 65vh;
        }

        .documento-preview-modal > .content {
            padding: 14px !important;
        }
    }

    @media (max-width: 420px) {
        .documentos-hero-left {
            gap: 12px;
        }

        .documento-card-title-wrap,
        .gasto-fields-header {
            align-items: flex-start;
        }

        .preview-file {
            align-items: flex-start;
            flex-direction: column;
        }

        .documento-actions {
            flex-direction: column;
        }

        .documento-actions .btn,
        .documento-actions button,
        .documento-actions a {
            width: 100%;
        }
    }
</style>

<script>
    const BASE_URL = "{{ url('/') }}";

    let documentosOriginales = [];
    let documentosFiltrados = [];
    let paginaActual = 1;
    let documentosPorPagina = 6;
    let isSubmittingDocumento = false;

    $(document).ready(function() {
        $('#modal-archivo').modal();

        $('#tipo-documento').dropdown();
        $('#categoria-gasto').dropdown();
        $('#filtro-tipo-documento').dropdown();

        $('#concepto-pago').dropdown({
            fullTextSearch: true,
            clearable: true,
            placeholder: 'Buscar o seleccionar concepto',
            message: {
                noResults: 'No se encontraron conceptos'
            }
        });

        configurarUploadDocumento();
        configurarFormularioDocumento();

        cargarDocumentos();

        $('#buscar-documento').on('keyup input change', function() {
            filtrarDocumentos();
        });

        $('#filtro-tipo-documento').on('change', function() {
            filtrarDocumentos();
        });
    });

    function configurarFormularioDocumento() {
        $('#tipo-documento').on('change', function() {
            const gastoFields = $('#gasto-fields');
            const cantidadInput = $('#cantidad-gasto');
            const categoriaInput = $('#categoria-gasto');

            if ($(this).val() === 'referencia') {
                gastoFields.slideDown(160);
                cantidadInput.attr('required', true);
                categoriaInput.attr('required', true);
                cargarConceptos();
            } else {
                gastoFields.slideUp(160);
                cantidadInput.attr('required', false).val('');
                categoriaInput.attr('required', false).val('');
                $('#categoria-gasto').dropdown('clear');
                $('#concepto-pago').dropdown('clear');
            }
        });

        $('#form-documento').submit(function(e) {
            e.preventDefault();

            if (isSubmittingDocumento) {
                return false;
            }

            const $btn = $('#btn-documento');
            const fileInput = $('#doc-path-input')[0];
            const file = fileInput.files[0];

            if (!file) {
                alertify.error('Debes seleccionar un archivo.');
                return;
            }

            const validation = validarArchivoDocumento(file);

            if (!validation.valid) {
                alertify.error(validation.message);
                fileInput.value = '';
                limpiarPreviewArchivo();
                return;
            }

            isSubmittingDocumento = true;
            $btn.addClass('loading disabled').html('<i class="spinner loading icon"></i> Subiendo...');

            const formData = new FormData(this);

            $.ajax({
                url: "{{ route('admin.documento.crearDocumento') }}",
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(res) {
                    alertify.alert(res.header || 'Documento registrado', res.message || 'El documento se registró correctamente.', function() {
                        cargarDocumentos();
                    });

                    resetFormularioDocumento();
                },
                error: function(xhr) {
                    const message = xhr.responseJSON?.message || 'Error al registrar el documento.';
                    alertify.error(message);
                },
                complete: function() {
                    $btn.removeClass('loading disabled').html('<i class="upload icon"></i> Subir documento');
                    isSubmittingDocumento = false;
                }
            });
        });
    }

    function cargarConceptos() {
        $.ajax({
            url: `${BASE_URL}/administrador/documento/conceptos`,
            type: 'GET',
            success: function(data) {
                const dropdown = $('#concepto-pago');

                dropdown.dropdown('clear');
                dropdown.empty();

                dropdown.append('<option value="">Buscar o seleccionar concepto</option>');

                data.forEach(function(pago) {
                    dropdown.append(`<option value="${pago.id}">${escapeHtml(pago.concepto)}</option>`);
                });

                dropdown.dropdown('refresh');

                dropdown.dropdown({
                    fullTextSearch: true,
                    clearable: true,
                    placeholder: 'Buscar o seleccionar concepto',
                    message: {
                        noResults: 'No se encontraron conceptos'
                    }
                });
            },
            error: function() {
                alertify.error('No se pudieron cargar los conceptos de pago.');
            }
        });
    }

    function configurarUploadDocumento() {
        const uploadArea = document.getElementById('upload-area-archivo');
        const fileInput = document.getElementById('doc-path-input');

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
                handleFilePreview(fileInput.files[0]);
            }
        });

        fileInput.addEventListener('change', function() {
            if (this.files.length > 0) {
                handleFilePreview(this.files[0]);
            }
        });

        $(document).on('click', '#remove-preview-btn', function(e) {
            e.preventDefault();
            e.stopPropagation();
            fileInput.value = '';
            limpiarPreviewArchivo();
        });
    }

    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    function validarArchivoDocumento(file) {
        const maxSizeMB = 5;
        const allowedTypes = [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        ];

        const isImage = file.type.startsWith('image/');

        if (file.size > maxSizeMB * 1024 * 1024) {
            return {
                valid: false,
                message: 'El archivo no debe superar los 5 MB.'
            };
        }

        if (!allowedTypes.includes(file.type) && !isImage) {
            return {
                valid: false,
                message: 'Tipo de archivo no permitido.'
            };
        }

        return {
            valid: true,
            message: ''
        };
    }

    function handleFilePreview(file) {
        if (!file) return;

        const validation = validarArchivoDocumento(file);

        if (!validation.valid) {
            alertify.error(validation.message);
            $('#doc-path-input').val('');
            limpiarPreviewArchivo();
            return;
        }

        const previewContainer = document.getElementById('upload-preview');
        const previewContent = document.getElementById('preview-content');
        const placeholder = document.getElementById('upload-placeholder');

        const extension = file.name.split('.').pop().toLowerCase();
        const isImage = file.type.startsWith('image/');

        if (isImage) {
            const reader = new FileReader();

            reader.onload = function(e) {
                previewContent.innerHTML = `
                    <button type="button" class="remove-preview-btn" id="remove-preview-btn" title="Quitar archivo">
                        <i class="times icon"></i>
                    </button>

                    <img src="${e.target.result}" alt="Preview" class="preview-image">

                    <span class="preview-badge">
                        <i class="check icon"></i>
                        Imagen seleccionada
                    </span>
                `;
            };

            reader.readAsDataURL(file);
        } else {
            const fileData = obtenerIconoArchivo(extension);
            const sizeMB = (file.size / (1024 * 1024)).toFixed(2);

            previewContent.innerHTML = `
                <button type="button" class="remove-preview-btn" id="remove-preview-btn" title="Quitar archivo">
                    <i class="times icon"></i>
                </button>

                <div class="preview-file">
                    <div class="preview-file-icon ${fileData.className}">
                        <i class="${fileData.icon} icon"></i>
                    </div>

                    <div class="preview-file-info">
                        <div class="preview-file-name">${escapeHtml(file.name)}</div>
                        <div class="preview-file-size">${sizeMB} MB</div>
                    </div>
                </div>

                <span class="preview-badge">
                    <i class="check icon"></i>
                    Archivo seleccionado
                </span>
            `;
        }

        placeholder.style.display = 'none';
        previewContainer.style.display = 'block';
    }

    function limpiarPreviewArchivo() {
        const previewContainer = document.getElementById('upload-preview');
        const placeholder = document.getElementById('upload-placeholder');
        const previewContent = document.getElementById('preview-content');

        if (previewContent) {
            previewContent.innerHTML = `
                <button type="button" class="remove-preview-btn" id="remove-preview-btn" title="Quitar archivo">
                    <i class="times icon"></i>
                </button>
            `;
        }

        if (previewContainer) {
            previewContainer.style.display = 'none';
        }

        if (placeholder) {
            placeholder.style.display = 'flex';
        }
    }

    function resetFormularioDocumento() {
        $('#form-documento')[0].reset();

        $('#tipo-documento').dropdown('clear');
        $('#categoria-gasto').dropdown('clear');
        $('#concepto-pago').dropdown('clear');

        $('#gasto-fields').hide();
        $('#cantidad-gasto').attr('required', false).val('');
        $('#categoria-gasto').attr('required', false);

        $('#doc-path-input').val('');
        limpiarPreviewArchivo();
    }

    function cargarDocumentos() {
        $('#documentos-loader').show();
        $('#documentos-container').empty();
        $('#documentos-pagination').hide().empty();
        $('#sin-documentos').hide();
        $('#sin-resultados').hide();
        $('#documentos-error').hide();
    
        $.ajax({
            url: "{{ route('admin.documento.obtenerDocumentos') }}",
            method: 'GET',
            dataType: 'json',
            data: {
                start: 0,
                length: 1000
            },
            success: function(response) {
                $('#documentos-loader').hide();
    
                documentosOriginales = ordenarDocumentosPorFechaDesc(response.data || []);
                renderizarDocumentos(documentosOriginales);
            },
            error: function() {
                $('#documentos-loader').hide();
                $('#documentos-error').show();
            }
        });
    }
    
    function ordenarDocumentosPorFechaDesc(documentos) {
        return [...documentos].sort(function(a, b) {
            const fechaA = obtenerFechaDocumento(a);
            const fechaB = obtenerFechaDocumento(b);
    
            return fechaB - fechaA;
        });
    }
    
    function obtenerFechaDocumento(documento) {
        const fecha = extraerTexto(documento.created_at || documento.fecha || '');
    
        if (!fecha) {
            return 0;
        }
    
        // Si viene como "25/05/2026", lo convierte a formato compatible
        const partes = fecha.match(/^(\d{1,2})\/(\d{1,2})\/(\d{4})/);
    
        if (partes) {
            const dia = partes[1].padStart(2, '0');
            const mes = partes[2].padStart(2, '0');
            const anio = partes[3];
    
            return new Date(`${anio}-${mes}-${dia}`).getTime();
        }
    
        return new Date(fecha).getTime() || 0;
    }

    function renderizarDocumentos(documentos) {
        documentosFiltrados = documentos;
        paginaActual = 1;
        renderizarPaginaDocumentos();
    }

    function renderizarPaginaDocumentos() {
        const container = $('#documentos-container');
        const pagination = $('#documentos-pagination');

        container.empty();
        pagination.empty();

        $('#total-documentos').text(documentosFiltrados.length);

        $('#sin-documentos').hide();
        $('#sin-resultados').hide();
        $('#documentos-error').hide();

        if (documentosOriginales.length === 0) {
            pagination.hide();
            $('#sin-documentos').show();
            return;
        }

        if (documentosFiltrados.length === 0) {
            pagination.hide();
            $('#sin-resultados').show();
            return;
        }

        const totalPaginas = Math.ceil(documentosFiltrados.length / documentosPorPagina);
        const inicio = (paginaActual - 1) * documentosPorPagina;
        const fin = inicio + documentosPorPagina;
        const documentosPagina = documentosFiltrados.slice(inicio, fin);

        documentosPagina.forEach(function(documento) {
            const titulo = extraerTexto(documento.titulo || 'Documento sin título');
            const tipo = extraerTexto(documento.tipo || '');
            const categoria = extraerTexto(documento.categoria || 'General');
            const cantidad = extraerTexto(documento.cantidad_formatted || 'N/A');
            const fecha = extraerTexto(documento.created_at || 'Sin fecha');
            const documentoHtml = documento.documento || '';
            const accionesHtml = documento.acciones || '';

            const tipoNormalizado = normalizarTexto(tipo);
            const tipoBadgeClass = tipoNormalizado.includes('gasto') || tipoNormalizado.includes('referencia')
                ? 'tipo-referencia'
                : 'tipo-general';

            const tipoIcon = tipoBadgeClass === 'tipo-referencia' ? 'receipt' : 'file alternate';

            const card = `
                <div class="documento-item-card">
                    <div class="documento-item-header">
                        <div class="documento-item-icon">
                            <i class="${obtenerIconoCategoria(categoria)} icon"></i>
                        </div>

                        <div style="min-width: 0; flex: 1;">
                            <h3 class="documento-item-title">${escapeHtml(titulo)}</h3>

                            <div class="documento-item-badges">
                                <span class="doc-badge ${tipoBadgeClass}">
                                    <i class="${tipoIcon} icon"></i>
                                    ${escapeHtml(tipo || 'General')}
                                </span>

                                <span class="doc-badge categoria">
                                    <i class="tag icon"></i>
                                    ${escapeHtml(categoria || 'General')}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="documento-item-body">
                        <div class="documento-meta-item">
                            <i class="dollar sign icon"></i>
                            <span>${escapeHtml(cantidad)}</span>
                        </div>

                        <div class="documento-meta-item">
                            <i class="calendar alternate outline icon"></i>
                            <span>${escapeHtml(fecha)}</span>
                        </div>
                    </div>

                    <div class="documento-item-footer">
                        <div class="documento-file-action">
                            ${documentoHtml || `
                                <button type="button" class="btn btn-secondary btn-sm disabled" disabled>
                                    <i class="file outline icon"></i>
                                    Sin archivo
                                </button>
                            `}
                        </div>

                        <div class="documento-actions">
                            ${accionesHtml}
                        </div>
                    </div>
                </div>
            `;

            container.append(card);
        });

        renderizarPaginacion(totalPaginas, documentosFiltrados.length);
    }

    function renderizarPaginacion(totalPaginas, totalDocumentos) {
        const pagination = $('#documentos-pagination');

        pagination.empty();

        if (totalPaginas <= 1) {
            pagination.hide();
            return;
        }

        const inicio = ((paginaActual - 1) * documentosPorPagina) + 1;
        const fin = Math.min(paginaActual * documentosPorPagina, totalDocumentos);

        pagination.show();

        pagination.append(`
            <button
                type="button"
                class="pagination-btn"
                onclick="cambiarPaginaDocumentos(${paginaActual - 1})"
                ${paginaActual === 1 ? 'disabled' : ''}
            >
                <i class="chevron left icon"></i>
            </button>
        `);

        const paginas = obtenerPaginasVisibles(totalPaginas);

        paginas.forEach(function(pagina) {
            if (pagina === '...') {
                pagination.append(`<span class="pagination-info">...</span>`);
                return;
            }

            pagination.append(`
                <button
                    type="button"
                    class="pagination-btn ${pagina === paginaActual ? 'active' : ''}"
                    onclick="cambiarPaginaDocumentos(${pagina})"
                >
                    ${pagina}
                </button>
            `);
        });

        pagination.append(`
            <button
                type="button"
                class="pagination-btn"
                onclick="cambiarPaginaDocumentos(${paginaActual + 1})"
                ${paginaActual === totalPaginas ? 'disabled' : ''}
            >
                <i class="chevron right icon"></i>
            </button>
        `);

        pagination.append(`
            <div class="pagination-info">
                Mostrando ${inicio}-${fin} de ${totalDocumentos}
            </div>
        `);
    }

    function cambiarPaginaDocumentos(pagina) {
        const totalPaginas = Math.ceil(documentosFiltrados.length / documentosPorPagina);

        if (pagina < 1 || pagina > totalPaginas) {
            return;
        }

        paginaActual = pagina;
        renderizarPaginaDocumentos();

        document.querySelector('.documentos-list-card')?.scrollIntoView({
            behavior: 'smooth',
            block: 'start'
        });
    }

    function obtenerPaginasVisibles(totalPaginas) {
        const paginas = [];

        if (totalPaginas <= 5) {
            for (let i = 1; i <= totalPaginas; i++) {
                paginas.push(i);
            }

            return paginas;
        }

        paginas.push(1);

        if (paginaActual > 3) {
            paginas.push('...');
        }

        const inicio = Math.max(2, paginaActual - 1);
        const fin = Math.min(totalPaginas - 1, paginaActual + 1);

        for (let i = inicio; i <= fin; i++) {
            paginas.push(i);
        }

        if (paginaActual < totalPaginas - 2) {
            paginas.push('...');
        }

        paginas.push(totalPaginas);

        return paginas;
    }

    function filtrarDocumentos() {
        const busqueda = normalizarTexto($('#buscar-documento').val());
        const tipoFiltro = $('#filtro-tipo-documento').val();

        const filtrados = documentosOriginales.filter(function(documento) {
            const titulo = extraerTexto(documento.titulo || '');
            const tipo = extraerTexto(documento.tipo || '');
            const categoria = extraerTexto(documento.categoria || '');
            const cantidad = extraerTexto(documento.cantidad_formatted || '');
            const fecha = extraerTexto(documento.created_at || '');

            const textoCompleto = normalizarTexto(
                titulo + ' ' + tipo + ' ' + categoria + ' ' + cantidad + ' ' + fecha
            );

            const tipoNormalizado = normalizarTexto(tipo);

            const coincideBusqueda = textoCompleto.includes(busqueda);

            let coincideTipo = true;

            if (tipoFiltro === 'general') {
                coincideTipo = tipoNormalizado.includes('general') || tipoNormalizado.includes('reglamento');
            }

            if (tipoFiltro === 'referencia') {
                coincideTipo = tipoNormalizado.includes('gasto') || tipoNormalizado.includes('referencia') || tipoNormalizado.includes('comprobante');
            }

            return coincideBusqueda && coincideTipo;
        });

        renderizarDocumentos(ordenarDocumentosPorFechaDesc(filtrados));
    }

    function limpiarFiltrosDocumentos() {
        $('#buscar-documento').val('');
        $('#filtro-tipo-documento').dropdown('set selected', 'todos');
        renderizarDocumentos(documentosOriginales);
    }

    $(document).on('click', '.btn-ver-archivo', function() {
        const src = $(this).data('img');
        const extension = String(src || '').split('.').pop().toLowerCase();

        $('#archivo-preview-img').hide().attr('src', '');
        $('#archivo-preview-pdf').hide().attr('src', '');
        $('#contenedor-download').hide();
        $('#archivo-preview-download').attr('href', '');

        if (['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'svg'].includes(extension)) {
            $('#archivo-preview-img').attr('src', src).show();
            $('#contenedor-imagen').show();
            $('#contenedor-pdf').hide();
        } else if (extension === 'pdf') {
            $('#archivo-preview-pdf').attr('src', src).show();
            $('#contenedor-pdf').show();
            $('#contenedor-imagen').hide();
        } else {
            $('#archivo-preview-download').attr('href', src);
            $('#contenedor-download').show();
            $('#contenedor-imagen').hide();
            $('#contenedor-pdf').hide();
        }

        $('#modal-archivo').modal('show');
    });

    $(document).on('click', '.btn-eliminar', function() {
        const id = $(this).data('id');

        alertify.confirm(
            '¿Eliminar Documento?',
            '¿Estás seguro de que deseas eliminar este documento? Todos los usuarios serán notificados.',
            function() {
                mostrarLoaderPantalla();

                setTimeout(function() {
                    $.ajax({
                        url: `${BASE_URL}/administrador/documento/eliminar-documento/${id}`,
                        method: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(res) {
                            alertify.alert(res.header || 'Documento eliminado', res.message || 'El documento fue eliminado.', function() {
                                cargarDocumentos();
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

    function obtenerIconoArchivo(extension) {
        if (extension === 'pdf') {
            return {
                className: 'pdf',
                icon: 'file pdf'
            };
        }

        if (['doc', 'docx'].includes(extension)) {
            return {
                className: 'doc',
                icon: 'file word'
            };
        }

        if (['xls', 'xlsx'].includes(extension)) {
            return {
                className: 'xls',
                icon: 'file excel'
            };
        }

        return {
            className: 'default',
            icon: 'file alternate'
        };
    }

    function obtenerIconoCategoria(categoria) {
        const texto = normalizarTexto(categoria);

        if (texto.includes('luz') || texto.includes('electric')) return 'bolt';
        if (texto.includes('agua')) return 'tint';
        if (texto.includes('mantenimiento')) return 'tools';
        if (texto.includes('seguridad')) return 'shield alternate';
        if (texto.includes('limpieza')) return 'broom';
        if (texto.includes('jardineria')) return 'leaf';
        if (texto.includes('gasto') || texto.includes('comprobante')) return 'receipt';

        return 'file alternate';
    }
ca
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