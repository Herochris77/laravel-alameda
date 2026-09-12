<x-app-layout>
    <div class="comunicados-admin-page">
        <div class="comunicados-hero">
            <div class="comunicados-hero-content">
                <div class="comunicados-hero-left">
                    <div class="comunicados-hero-icon">
                        <i class="bullhorn icon"></i>
                    </div>

                    <div>
                        <h1 class="comunicados-title">Gestión de Comunicados</h1>
                        <p class="comunicados-subtitle">Publica avisos importantes para los vecinos</p>
                    </div>
                </div>

                <div class="comunicados-hero-pill">
                    <i class="announcement icon"></i>
                    Administración de avisos
                </div>
            </div>
        </div>

        <div class="comunicados-layout">
            <div class="comunicado-form-card">
                <div class="comunicado-card-header">
                    <div class="comunicado-card-title-wrap">
                        <div class="comunicado-card-icon">
                            <i class="plus circle icon"></i>
                        </div>

                        <div>
                            <h3 class="comunicado-card-title">Nuevo Comunicado</h3>
                            <p class="comunicado-card-subtitle">Redacta y publica un aviso para los vecinos.</p>
                        </div>
                    </div>
                </div>

                <div class="comunicado-card-body">
                    <form id="form-comunicado">
                        @csrf

                        <div class="form-group">
                            <label class="form-label">Título *</label>
                            <div class="input-icon-wrapper">
                                <i class="heading icon"></i>
                                <input
                                    type="text"
                                    name="titulo"
                                    class="form-input comunicado-input input-with-icon"
                                    placeholder="Título del comunicado"
                                    autocomplete="off"
                                    maxlength="100"
                                    required
                                >
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Comunicado *</label>
                            <textarea
                                name="contenido"
                                class="form-input comunicado-input comunicado-textarea"
                                rows="5"
                                placeholder="Escribe el comunicado aquí..."
                                autocomplete="off"
                                maxlength="880"
                                required
                            ></textarea>

                            <small class="form-help-text">
                                Máximo 880 caracteres. Procura que el mensaje sea claro y breve.
                            </small>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Fecha de vencimiento *</label>

                            <div class="input-icon-wrapper">
                                <i class="calendar alternate outline icon"></i>
                                <input
                                    type="date"
                                    name="fecha_vencimiento"
                                    id="fecha-vencimiento"
                                    class="form-input comunicado-input input-with-icon date-input"
                                    required
                                >
                            </div>
                        </div>

                        <button id="btn-publicar" class="btn btn-primary btn-lg btn-submit-comunicado" type="submit">
                            <i class="paper plane icon"></i>
                            Publicar comunicado
                        </button>
                    </form>
                </div>
            </div>

            <div class="comunicados-list-card">
                <div class="comunicado-card-header list-header">
                    <div class="comunicado-card-title-wrap">
                        <div class="comunicado-card-icon secondary">
                            <i class="list icon"></i>
                        </div>

                        <div>
                            <h3 class="comunicado-card-title">Lista de Comunicados</h3>
                            <p class="comunicado-card-subtitle">Consulta, busca y administra los avisos publicados.</p>
                        </div>
                    </div>
                </div>

                <div class="comunicados-toolbar">
                    <div class="filter-field">
                        <label class="form-label">Buscar comunicado</label>
                        <div class="ui icon input comunicados-search-input">
                            <i class="search icon"></i>
                            <input
                                type="text"
                                id="buscar-comunicado"
                                placeholder="Título, contenido, autor o fecha..."
                                autocomplete="off"
                            >
                        </div>
                    </div>

                    <button type="button" class="btn btn-secondary btn-clear-filters" onclick="limpiarFiltrosComunicados()">
                        <i class="times icon"></i>
                        Limpiar
                    </button>
                </div>

                <div class="comunicados-summary">
                    <div class="summary-item">
                        <div class="summary-icon">
                            <i class="bullhorn icon"></i>
                        </div>

                        <div>
                            <span>Comunicados encontrados</span>
                            <strong id="total-comunicados">0</strong>
                        </div>
                    </div>
                </div>

                <div id="comunicados-loader" class="comunicados-loader">
                    <div class="ui active centered inline text loader large">Cargando comunicados...</div>
                </div>

                <div id="comunicados-container" class="comunicados-grid"></div>

                <div id="comunicados-pagination" class="comunicados-pagination" style="display: none;"></div>

                <div id="sin-comunicados" class="empty-comunicados-card" style="display: none;">
                    <div class="empty-comunicados-icon">
                        <i class="bullhorn icon"></i>
                    </div>

                    <h3>No hay comunicados registrados</h3>
                    <p>Aún no se han publicado comunicados para los vecinos.</p>
                </div>

                <div id="sin-resultados" class="empty-comunicados-card" style="display: none;">
                    <div class="empty-comunicados-icon">
                        <i class="search icon"></i>
                    </div>

                    <h3>No se encontraron resultados</h3>
                    <p>No hay comunicados que coincidan con tu búsqueda.</p>

                    <button type="button" class="btn btn-secondary btn-sm" onclick="limpiarFiltrosComunicados()">
                        <i class="undo icon"></i>
                        Limpiar búsqueda
                    </button>
                </div>

                <div id="comunicados-error" class="empty-comunicados-card error" style="display: none;">
                    <div class="empty-comunicados-icon error">
                        <i class="warning sign icon"></i>
                    </div>

                    <h3>Error al cargar comunicados</h3>
                    <p>No se pudieron cargar los comunicados. Intenta nuevamente.</p>

                    <button type="button" class="btn btn-primary btn-sm" onclick="cargarComunicados()">
                        <i class="refresh icon"></i>
                        Reintentar
                    </button>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<style>
    :root {
        --com-primary: #f59e0b;
        --com-primary-dark: #d97706;
        --com-secondary: #667eea;
        --com-success: #10b981;
        --com-danger: #ef4444;
        --com-danger-dark: #dc2626;
        --com-text-main: #0f172a;
        --com-text-muted: #64748b;
        --com-text-soft: #94a3b8;
        --com-border: #e2e8f0;
        --com-surface: #ffffff;
        --com-soft-bg: #f8fafc;
    }

    .comunicados-admin-page {
        width: 100%;
        padding-bottom: 24px;
    }

    .comunicados-hero {
        background:
            radial-gradient(circle at top right, rgba(255,255,255,0.24), transparent 34%),
            radial-gradient(circle at bottom left, rgba(255,255,255,0.14), transparent 30%),
            linear-gradient(135deg, var(--com-primary) 0%, var(--com-primary-dark) 100%);
        border-radius: 26px;
        padding: 26px;
        color: white;
        margin-bottom: 22px;
        box-shadow: 0 12px 30px rgba(217, 119, 6, 0.18);
        overflow: hidden;
    }

    .comunicados-hero-content {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 18px;
        flex-wrap: wrap;
    }

    .comunicados-hero-left {
        display: flex;
        align-items: center;
        gap: 16px;
        min-width: 0;
    }

    .comunicados-hero-icon {
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

    .comunicados-hero-icon i {
        color: white;
        font-size: 1.8rem;
        margin: 0 !important;
    }

    .comunicados-title {
        margin: 0 0 6px 0;
        font-size: clamp(1.55rem, 3vw, 2.15rem);
        font-weight: 900;
        letter-spacing: -0.035em;
        line-height: 1.08;
    }

    .comunicados-subtitle {
        margin: 0;
        opacity: 0.92;
        font-size: 1rem;
        line-height: 1.45;
    }

    .comunicados-hero-pill {
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

    .comunicados-hero-pill i {
        margin: 0 !important;
    }

    .comunicados-layout {
        display: grid;
        grid-template-columns: minmax(300px, 420px) minmax(0, 1fr);
        gap: 24px;
        align-items: start;
    }

    .comunicado-form-card,
    .comunicados-list-card,
    .empty-comunicados-card {
        background: var(--com-surface);
        border: 1px solid var(--com-border);
        border-radius: 22px;
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.06);
        overflow: hidden;
    }

    .comunicado-form-card {
        position: sticky;
        top: 18px;
    }

    .comunicado-card-header {
        padding: 18px 20px;
        border-bottom: 1px solid #f1f5f9;
        background: linear-gradient(180deg, #ffffff 0%, #fbfdff 100%);
    }

    .comunicado-card-title-wrap {
        display: flex;
        align-items: center;
        gap: 14px;
        min-width: 0;
    }

    .comunicado-card-icon {
        width: 52px;
        height: 52px;
        border-radius: 16px;
        background: linear-gradient(135deg, var(--com-primary) 0%, var(--com-primary-dark) 100%);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        box-shadow: 0 8px 18px rgba(245, 158, 11, 0.24);
    }

    .comunicado-card-icon.secondary {
        background: linear-gradient(135deg, var(--com-secondary) 0%, var(--com-primary-dark) 100%);
    }

    .comunicado-card-icon i {
        margin: 0 !important;
        font-size: 1.25rem;
    }

    .comunicado-card-title {
        margin: 0;
        color: var(--com-text-main);
        font-size: 1.12rem;
        font-weight: 900;
        line-height: 1.25;
    }

    .comunicado-card-subtitle {
        margin: 4px 0 0 0;
        color: var(--com-text-muted);
        font-size: 0.9rem;
        line-height: 1.35;
    }

    .comunicado-card-body {
        padding: 20px;
    }

    .form-group {
        margin-bottom: 18px;
    }

    .form-label {
        display: block;
        color: var(--com-text-main);
        font-weight: 800;
        margin-bottom: 8px;
        font-size: 0.9rem;
    }

    .comunicado-input {
        min-height: 46px;
        border-radius: 14px !important;
        width: 100%;
        max-width: 100%;
        box-sizing: border-box;
        border: 1px solid var(--com-border) !important;
        transition: border-color 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
    }

    .comunicado-input:focus {
        border-color: rgba(245, 158, 11, 0.65) !important;
        box-shadow: 0 0 0 4px rgba(245, 158, 11, 0.12) !important;
        outline: none;
        background: white;
    }

    .comunicado-textarea {
        resize: vertical;
        min-height: 140px;
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
        color: var(--com-primary);
        pointer-events: none;
        z-index: 1;
        margin: 0 !important;
    }

    .input-with-icon {
        padding-left: 42px !important;
    }

    .form-help-text {
        display: block;
        color: var(--com-text-soft);
        font-size: 0.8rem;
        margin-top: 6px;
        line-height: 1.35;
    }

    input[type="date"] {
        -webkit-appearance: none;
        appearance: none;
    }

    input[type="date"]::-webkit-calendar-picker-indicator {
        cursor: pointer;
        opacity: 0.6;
        padding: 4px;
        margin-right: 4px;
        border-radius: 4px;
    }

    input[type="date"]::-webkit-calendar-picker-indicator:hover {
        background: #fff7ed;
        opacity: 1;
    }

    .btn-submit-comunicado {
        width: 100%;
        min-height: 48px;
        border-radius: 14px;
        margin-top: 12px;
        display: inline-flex !important;
        align-items: center;
        justify-content: center;
        gap: 8px;
        font-weight: 900;
        box-shadow: 0 8px 18px rgba(245, 158, 11, 0.22);
    }

    .btn-submit-comunicado i {
        margin: 0 !important;
    }

    .comunicados-toolbar {
        padding: 18px 20px;
        display: grid;
        grid-template-columns: minmax(0, 1fr) auto;
        gap: 14px;
        align-items: end;
        border-bottom: 1px solid #f1f5f9;
    }

    .filter-field {
        min-width: 0;
    }

    .comunicados-search-input {
        width: 100%;
    }

    .comunicados-search-input input {
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

    .comunicados-summary {
        padding: 16px 20px 0;
    }

    .summary-item {
        display: inline-flex;
        align-items: center;
        gap: 12px;
        background: var(--com-soft-bg);
        border: 1px solid #eef2f7;
        border-radius: 16px;
        padding: 12px 14px;
    }

    .summary-icon {
        width: 42px;
        height: 42px;
        border-radius: 14px;
        background: #fff7ed;
        color: var(--com-primary-dark);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .summary-icon i {
        margin: 0 !important;
    }

    .summary-item span {
        display: block;
        color: var(--com-text-muted);
        font-size: 0.78rem;
        font-weight: 800;
        margin-bottom: 2px;
    }

    .summary-item strong {
        display: block;
        color: var(--com-text-main);
        font-size: 1.2rem;
        line-height: 1;
    }

    .comunicados-loader {
        min-height: 220px;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 40px 20px;
    }

    .comunicados-grid {
        padding: 20px;
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 16px;
    }

    .comunicado-item-card {
        border: 1px solid var(--com-border);
        border-radius: 18px;
        background: white;
        box-shadow: 0 6px 18px rgba(15, 23, 42, 0.05);
        overflow: hidden;
        display: flex;
        flex-direction: column;
        transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
    }

    .comunicado-item-card:hover {
        transform: translateY(-2px);
        border-color: rgba(245, 158, 11, 0.35);
        box-shadow: 0 10px 24px rgba(15, 23, 42, 0.08);
    }

    .comunicado-item-header {
        padding: 16px;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: flex-start;
        gap: 12px;
    }

    .comunicado-item-icon {
        width: 46px;
        height: 46px;
        border-radius: 14px;
        background: #fff7ed;
        color: var(--com-primary-dark);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .comunicado-item-icon i {
        margin: 0 !important;
        font-size: 1.15rem;
    }

    .comunicado-item-title {
        margin: 0;
        color: var(--com-text-main);
        font-size: 1rem;
        font-weight: 900;
        line-height: 1.3;
        overflow-wrap: anywhere;
    }

    .comunicado-item-badges {
        display: flex;
        flex-wrap: wrap;
        gap: 7px;
        margin-top: 9px;
    }

    .com-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 6px 9px;
        border-radius: 999px;
        font-size: 0.72rem;
        font-weight: 900;
        line-height: 1;
    }

    .com-badge i {
        margin: 0 !important;
    }

    .com-badge.vigente {
        background: #dcfce7;
        color: #166534;
    }

    .com-badge.vencido {
        background: #fee2e2;
        color: #991b1b;
    }

    .com-badge.autor {
        background: #dbeafe;
        color: #1d4ed8;
    }

    .comunicado-item-body {
        padding: 16px;
        flex: 1;
        display: grid;
        gap: 12px;
    }

    .comunicado-content {
        color: var(--com-text-muted);
        font-size: 0.9rem;
        line-height: 1.5;
        overflow-wrap: anywhere;
        display: -webkit-box;
        -webkit-line-clamp: 5;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .comunicado-meta-item {
        display: flex;
        align-items: flex-start;
        gap: 9px;
        color: var(--com-text-muted);
        font-size: 0.88rem;
        line-height: 1.4;
        overflow-wrap: anywhere;
    }

    .comunicado-meta-item i {
        color: var(--com-primary);
        margin: 2px 0 0 0 !important;
        flex-shrink: 0;
    }

    .comunicado-item-footer {
        padding: 14px 16px 16px;
        border-top: 1px solid #f1f5f9;
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        flex-wrap: wrap;
    }

    .comunicado-item-footer .btn,
    .comunicado-item-footer button,
    .comunicado-item-footer a {
        min-height: 40px;
        border-radius: 12px !important;
        display: inline-flex !important;
        align-items: center;
        justify-content: center;
        gap: 7px;
    }

    .comunicados-pagination {
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
        border: 1px solid var(--com-border);
        background: #ffffff;
        color: var(--com-text-muted);
        border-radius: 12px;
        font-weight: 900;
        cursor: pointer;
        transition: background 0.2s ease, color 0.2s ease, border-color 0.2s ease;
    }

    .pagination-btn:hover {
        background: #fff7ed;
        color: var(--com-primary-dark);
        border-color: rgba(245, 158, 11, 0.35);
    }

    .pagination-btn.active {
        background: linear-gradient(135deg, var(--com-primary) 0%, var(--com-primary-dark) 100%);
        color: #ffffff;
        border-color: transparent;
        box-shadow: 0 8px 18px rgba(245, 158, 11, 0.22);
    }

    .pagination-btn:disabled {
        opacity: 0.45;
        cursor: not-allowed;
        background: var(--com-soft-bg);
        color: var(--com-text-soft);
    }

    .pagination-info {
        color: var(--com-text-muted);
        font-size: 0.86rem;
        font-weight: 800;
        margin: 0 8px;
    }

    .empty-comunicados-card {
        margin: 20px;
        padding: 44px 20px;
        text-align: center;
    }

    .empty-comunicados-icon {
        width: 78px;
        height: 78px;
        border-radius: 24px;
        margin: 0 auto 18px;
        background: linear-gradient(135deg, rgba(245, 158, 11, 0.12), rgba(217, 119, 6, 0.16));
        color: var(--com-primary-dark);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .empty-comunicados-icon.error {
        background: #fee2e2;
        color: var(--com-danger);
    }

    .empty-comunicados-icon i {
        font-size: 2rem;
        margin: 0 !important;
    }

    .empty-comunicados-card h3 {
        margin: 0 0 8px 0;
        color: #475569;
        font-weight: 900;
    }

    .empty-comunicados-card p {
        margin: 0 0 18px 0;
        color: var(--com-text-soft);
        line-height: 1.5;
    }

    @media (max-width: 1200px) {
        .comunicados-layout {
            grid-template-columns: 1fr;
        }

        .comunicado-form-card {
            position: static;
        }
    }

    @media (max-width: 900px) {
        .comunicados-grid {
            grid-template-columns: 1fr;
        }

        .comunicados-toolbar {
            grid-template-columns: 1fr;
        }

        .btn-clear-filters {
            width: 100%;
        }

        .comunicado-item-footer .btn,
        .comunicado-item-footer button,
        .comunicado-item-footer a {
            flex: 1;
        }
    }

    @media (max-width: 768px) {
        .comunicados-hero {
            border-radius: 0 0 24px 24px;
            margin: -8px -4px 18px;
            padding: 22px;
        }

        .comunicados-hero-content {
            align-items: stretch;
        }

        .comunicados-hero-left {
            align-items: flex-start;
        }

        .comunicados-hero-icon {
            width: 54px;
            height: 54px;
            border-radius: 18px;
        }

        .comunicados-title {
            font-size: 1.55rem;
        }

        .comunicados-subtitle {
            font-size: 0.92rem;
        }

        .comunicados-hero-pill {
            width: 100%;
            justify-content: center;
            border-radius: 16px;
        }

        .comunicado-form-card,
        .comunicados-list-card,
        .empty-comunicados-card {
            border-radius: 18px;
        }

        .comunicado-card-header,
        .comunicado-card-body,
        .comunicados-toolbar {
            padding: 16px;
        }

        .comunicado-card-icon {
            width: 46px;
            height: 46px;
            border-radius: 14px;
        }

        .comunicado-card-title {
            font-size: 1.04rem;
        }

        .comunicado-card-subtitle {
            font-size: 0.85rem;
        }

        .comunicados-summary {
            padding: 14px 16px 0;
        }

        .comunicados-grid {
            padding: 16px;
        }

        .comunicados-pagination {
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
        textarea {
            font-size: 16px !important;
        }
    }

    @media (max-width: 420px) {
        .comunicados-hero-left {
            gap: 12px;
        }

        .comunicado-card-title-wrap {
            align-items: flex-start;
        }

        .comunicado-item-header {
            align-items: flex-start;
        }

        .comunicado-item-footer {
            flex-direction: column;
        }

        .comunicado-item-footer .btn,
        .comunicado-item-footer button,
        .comunicado-item-footer a {
            width: 100%;
        }
    }
</style>

<script>
    const BASE_URL = "{{ url('/') }}";

    let comunicadosOriginales = [];
    let comunicadosFiltrados = [];
    let paginaActualComunicados = 1;
    let comunicadosPorPagina = 6;
    let isSubmittingComunicado = false;

    $(document).ready(function() {
        establecerFechaMinimaComunicado();
        cargarComunicados();

        $('#buscar-comunicado').on('keyup input change', function() {
            filtrarComunicados();
        });

        $('#form-comunicado').submit(function(e) {
            e.preventDefault();

            if (isSubmittingComunicado) {
                return false;
            }

            const $btn = $('#btn-publicar');

            isSubmittingComunicado = true;
            $btn.addClass('loading disabled').html('<i class="loading spinner icon"></i> Publicando...');

            const data = $(this).serialize();

            $.ajax({
                url: "{{ route('admin.comunicado.crearComunicado') }}",
                method: 'POST',
                data: data,
                success: function(response) {
                    alertify.alert(response.header, response.message, function() {
                        cargarComunicados();
                    });

                    $('#form-comunicado')[0].reset();
                    establecerFechaMinimaComunicado();
                },
                error: function() {
                    alertify.error('Error al publicar. Intenta nuevamente.');
                },
                complete: function() {
                    $btn.removeClass('loading disabled').html('<i class="paper plane icon"></i> Publicar comunicado');
                    isSubmittingComunicado = false;
                }
            });
        });
    });

    function establecerFechaMinimaComunicado() {
        const today = new Date();
        const year = today.getFullYear();
        const month = String(today.getMonth() + 1).padStart(2, '0');
        const day = String(today.getDate()).padStart(2, '0');
        const todayStr = year + '-' + month + '-' + day;

        const fechaInput = document.getElementById('fecha-vencimiento');

        if (fechaInput) {
            fechaInput.min = todayStr;
        }
    }

    function cargarComunicados() {
        $('#comunicados-loader').show();
        $('#comunicados-container').empty();
        $('#comunicados-pagination').hide().empty();
        $('#sin-comunicados').hide();
        $('#sin-resultados').hide();
        $('#comunicados-error').hide();

        $.ajax({
            url: "{{ route('admin.comunicado.obtenerComunicados') }}",
            method: 'GET',
            dataType: 'json',
            data: {
                start: 0,
                length: 1000
            },
            success: function(response) {
                $('#comunicados-loader').hide();

                comunicadosOriginales = ordenarComunicadosPorFechaDesc(response.data || []);
                renderizarComunicados(comunicadosOriginales);
            },
            error: function() {
                $('#comunicados-loader').hide();
                $('#comunicados-error').show();
            }
        });
    }

    function ordenarComunicadosPorFechaDesc(comunicados) {
        return [...comunicados].sort(function(a, b) {
            const fechaA = obtenerFechaComunicado(a);
            const fechaB = obtenerFechaComunicado(b);

            return fechaB - fechaA;
        });
    }

    function obtenerFechaComunicado(comunicado) {
        const fecha = extraerTexto(
            comunicado.created_at ||
            comunicado.fecha_creacion ||
            comunicado.vencimiento ||
            comunicado.fecha_vencimiento ||
            ''
        );

        if (!fecha) {
            return 0;
        }

        const partes = fecha.match(/^(\d{1,2})\/(\d{1,2})\/(\d{4})/);

        if (partes) {
            const dia = partes[1].padStart(2, '0');
            const mes = partes[2].padStart(2, '0');
            const anio = partes[3];

            return new Date(`${anio}-${mes}-${dia}`).getTime();
        }

        return new Date(fecha).getTime() || 0;
    }

    function renderizarComunicados(comunicados) {
        comunicadosFiltrados = comunicados;
        paginaActualComunicados = 1;
        renderizarPaginaComunicados();
    }

    function renderizarPaginaComunicados() {
        const container = $('#comunicados-container');
        const pagination = $('#comunicados-pagination');

        container.empty();
        pagination.empty();

        $('#total-comunicados').text(comunicadosFiltrados.length);

        $('#sin-comunicados').hide();
        $('#sin-resultados').hide();
        $('#comunicados-error').hide();

        if (comunicadosOriginales.length === 0) {
            pagination.hide();
            $('#sin-comunicados').show();
            return;
        }

        if (comunicadosFiltrados.length === 0) {
            pagination.hide();
            $('#sin-resultados').show();
            return;
        }

        const totalPaginas = Math.ceil(comunicadosFiltrados.length / comunicadosPorPagina);
        const inicio = (paginaActualComunicados - 1) * comunicadosPorPagina;
        const fin = inicio + comunicadosPorPagina;
        const comunicadosPagina = comunicadosFiltrados.slice(inicio, fin);

        comunicadosPagina.forEach(function(comunicado) {
            const titulo = extraerTexto(comunicado.titulo || 'Comunicado sin título');
            const contenido = extraerTexto(comunicado.comunicado || comunicado.contenido || 'Sin contenido');
            const vencimiento = extraerTexto(comunicado.vencimiento || comunicado.fecha_vencimiento || 'Sin fecha');
            const autor = extraerTexto(comunicado.autor || comunicado.creado_por || 'Sin autor');
            const accionesHtml = comunicado.acciones || '';
            const estado = obtenerEstadoComunicado(vencimiento);

            const card = `
                <div class="comunicado-item-card">
                    <div class="comunicado-item-header">
                        <div class="comunicado-item-icon">
                            <i class="bullhorn icon"></i>
                        </div>

                        <div style="min-width: 0; flex: 1;">
                            <h3 class="comunicado-item-title">${escapeHtml(titulo)}</h3>

                            <div class="comunicado-item-badges">
                                <span class="com-badge ${estado.clase}">
                                    <i class="${estado.icono} icon"></i>
                                    ${estado.texto}
                                </span>

                                <span class="com-badge autor">
                                    <i class="user icon"></i>
                                    ${escapeHtml(autor)}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="comunicado-item-body">
                        <div class="comunicado-content">
                            ${escapeHtml(contenido)}
                        </div>

                        <div class="comunicado-meta-item">
                            <i class="calendar alternate outline icon"></i>
                            <span>Vence: ${escapeHtml(vencimiento)}</span>
                        </div>
                    </div>

                    <div class="comunicado-item-footer">
                        ${accionesHtml || `
                            <button type="button" class="btn btn-secondary btn-sm disabled" disabled>
                                <i class="info circle icon"></i>
                                Sin acciones
                            </button>
                        `}
                    </div>
                </div>
            `;

            container.append(card);
        });

        renderizarPaginacionComunicados(totalPaginas, comunicadosFiltrados.length);
    }

    function renderizarPaginacionComunicados(totalPaginas, totalComunicados) {
        const pagination = $('#comunicados-pagination');

        pagination.empty();

        if (totalPaginas <= 1) {
            pagination.hide();
            return;
        }

        const inicio = ((paginaActualComunicados - 1) * comunicadosPorPagina) + 1;
        const fin = Math.min(paginaActualComunicados * comunicadosPorPagina, totalComunicados);

        pagination.show();

        pagination.append(`
            <button
                type="button"
                class="pagination-btn"
                onclick="cambiarPaginaComunicados(${paginaActualComunicados - 1})"
                ${paginaActualComunicados === 1 ? 'disabled' : ''}
            >
                <i class="chevron left icon"></i>
            </button>
        `);

        const paginas = obtenerPaginasVisiblesComunicados(totalPaginas);

        paginas.forEach(function(pagina) {
            if (pagina === '...') {
                pagination.append(`<span class="pagination-info">...</span>`);
                return;
            }

            pagination.append(`
                <button
                    type="button"
                    class="pagination-btn ${pagina === paginaActualComunicados ? 'active' : ''}"
                    onclick="cambiarPaginaComunicados(${pagina})"
                >
                    ${pagina}
                </button>
            `);
        });

        pagination.append(`
            <button
                type="button"
                class="pagination-btn"
                onclick="cambiarPaginaComunicados(${paginaActualComunicados + 1})"
                ${paginaActualComunicados === totalPaginas ? 'disabled' : ''}
            >
                <i class="chevron right icon"></i>
            </button>
        `);

        pagination.append(`
            <div class="pagination-info">
                Mostrando ${inicio}-${fin} de ${totalComunicados}
            </div>
        `);
    }

    function cambiarPaginaComunicados(pagina) {
        const totalPaginas = Math.ceil(comunicadosFiltrados.length / comunicadosPorPagina);

        if (pagina < 1 || pagina > totalPaginas) {
            return;
        }

        paginaActualComunicados = pagina;
        renderizarPaginaComunicados();

        document.querySelector('.comunicados-list-card')?.scrollIntoView({
            behavior: 'smooth',
            block: 'start'
        });
    }

    function obtenerPaginasVisiblesComunicados(totalPaginas) {
        const paginas = [];

        if (totalPaginas <= 5) {
            for (let i = 1; i <= totalPaginas; i++) {
                paginas.push(i);
            }

            return paginas;
        }

        paginas.push(1);

        if (paginaActualComunicados > 3) {
            paginas.push('...');
        }

        const inicio = Math.max(2, paginaActualComunicados - 1);
        const fin = Math.min(totalPaginas - 1, paginaActualComunicados + 1);

        for (let i = inicio; i <= fin; i++) {
            paginas.push(i);
        }

        if (paginaActualComunicados < totalPaginas - 2) {
            paginas.push('...');
        }

        paginas.push(totalPaginas);

        return paginas;
    }

    function filtrarComunicados() {
        const busqueda = normalizarTexto($('#buscar-comunicado').val());

        const filtrados = comunicadosOriginales.filter(function(comunicado) {
            const titulo = extraerTexto(comunicado.titulo || '');
            const contenido = extraerTexto(comunicado.comunicado || comunicado.contenido || '');
            const vencimiento = extraerTexto(comunicado.vencimiento || comunicado.fecha_vencimiento || '');
            const autor = extraerTexto(comunicado.autor || comunicado.creado_por || '');

            const textoCompleto = normalizarTexto(
                titulo + ' ' + contenido + ' ' + vencimiento + ' ' + autor
            );

            return textoCompleto.includes(busqueda);
        });

        renderizarComunicados(ordenarComunicadosPorFechaDesc(filtrados));
    }

    function limpiarFiltrosComunicados() {
        $('#buscar-comunicado').val('');
        renderizarComunicados(comunicadosOriginales);
    }

    function obtenerEstadoComunicado(vencimiento) {
        const fecha = obtenerFechaDesdeTexto(vencimiento);

        if (!fecha) {
            return {
                clase: 'vigente',
                icono: 'clock outline',
                texto: 'Vigente'
            };
        }

        const hoy = new Date();
        hoy.setHours(0, 0, 0, 0);

        fecha.setHours(0, 0, 0, 0);

        if (fecha < hoy) {
            return {
                clase: 'vencido',
                icono: 'times circle',
                texto: 'Vencido'
            };
        }

        return {
            clase: 'vigente',
            icono: 'check circle',
            texto: 'Vigente'
        };
    }

    function obtenerFechaDesdeTexto(fechaTexto) {
        const fecha = extraerTexto(fechaTexto || '');

        if (!fecha) {
            return null;
        }

        const partes = fecha.match(/^(\d{1,2})\/(\d{1,2})\/(\d{4})/);

        if (partes) {
            const dia = partes[1].padStart(2, '0');
            const mes = partes[2].padStart(2, '0');
            const anio = partes[3];

            return new Date(`${anio}-${mes}-${dia}`);
        }

        const parsed = new Date(fecha);

        if (isNaN(parsed.getTime())) {
            return null;
        }

        return parsed;
    }

    $(document).on('click', '.btn-eliminar', function() {
        const id = $(this).data('id');

        alertify.confirm(
            '¿Eliminar comunicado?',
            '¿Estás seguro de que quieres eliminar este comunicado?',
            function() {
                mostrarLoaderPantalla();

                setTimeout(function() {
                    $.ajax({
                        url: `${BASE_URL}/administrador/comunicado/eliminar-comunicados/${id}`,
                        method: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            alertify.alert(response.header, response.message, function() {
                                cargarComunicados();
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