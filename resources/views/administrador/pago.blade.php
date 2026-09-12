<x-app-layout>
    <div class="pagos-admin-page">
        <div class="pagos-hero">
            <div class="pagos-hero-content">
                <div class="pagos-hero-left">
                    <div class="pagos-hero-icon">
                        <i class="money bill alternate icon"></i>
                    </div>

                    <div>
                        <h1 class="pagos-title">Gestión de Pagos</h1>
                        <p class="pagos-subtitle">Registra y administra recibos de pago</p>
                    </div>
                </div>

                <div class="pagos-hero-pill">
                    <i class="credit card outline icon"></i>
                    Administración de pagos
                </div>
            </div>
        </div>

        <div class="pagos-layout">
            <div class="pago-form-card">
                <div class="pago-card-header">
                    <div class="pago-card-title-wrap">
                        <div class="pago-card-icon">
                            <i class="plus circle icon"></i>
                        </div>

                        <div>
                            <h3 class="pago-card-title">Registrar Recibo de Pago</h3>
                            <p class="pago-card-subtitle">Crea un concepto de pago y asígnalo a los usuarios correspondientes.</p>
                        </div>
                    </div>
                </div>

                <div class="pago-card-body">
                    <form id="form-recibo">
                        @csrf

                        <div class="form-group">
                            <label class="form-label">Concepto de pago *</label>
                            <div class="input-icon-wrapper">
                                <i class="file alternate outline icon"></i>
                                <input
                                    type="text"
                                    name="concepto"
                                    class="form-input pago-input input-with-icon"
                                    placeholder="Ej. Cuota de mantenimiento abril"
                                    autocomplete="off"
                                    required
                                >
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Selecciona usuarios *</label>

                            <select id="usuarios-recibo" name="usuarios[]" multiple class="ui fluid search dropdown pago-input" required>
                                <option value="" disabled>Selecciona usuarios</option>
                                <option value="todos">Todos los autorizados</option>

                                @foreach($usuarios as $user)
                                    <option value="{{ $user->id }}" data-pago="{{ $user->pago }}">
                                        {{ $user->nombre }} ({{ $user->tipo }} - #{{ $user->casa }})
                                    </option>
                                @endforeach
                            </select>

                            <small class="form-help-text">
                                Puedes buscar por nombre, tipo o número de casa.
                            </small>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Monto para cada usuario *</label>
                            <div class="input-icon-wrapper">
                                <i class="dollar sign icon"></i>
                                <input
                                    type="number"
                                    name="cantidad"
                                    class="form-input pago-input input-with-icon"
                                    min="0"
                                    step="0.01"
                                    placeholder="Ej. 1500.00"
                                    autocomplete="off"
                                    required
                                >
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Fecha de vencimiento *</label>
                            <div class="input-icon-wrapper">
                                <i class="calendar alternate outline icon"></i>
                                <input
                                    type="date"
                                    name="vencimiento"
                                    id="fecha-vencimiento"
                                    class="form-input pago-input input-with-icon date-input"
                                    required
                                >
                            </div>
                        </div>

                        <button id="btn-recibo" class="btn btn-primary btn-lg btn-submit-pago" type="submit">
                            <i class="paper plane icon"></i>
                            Publicar recibo
                        </button>
                    </form>
                </div>
            </div>

            <div class="pagos-list-card">
                <div class="pago-card-header list-header">
                    <div class="pago-card-title-wrap">
                        <div class="pago-card-icon secondary">
                            <i class="list icon"></i>
                        </div>

                        <div>
                            <h3 class="pago-card-title">Lista de Pagos</h3>
                            <p class="pago-card-subtitle">Consulta, busca y administra los recibos publicados.</p>
                        </div>
                    </div>
                </div>

                <div class="pagos-toolbar">
                    <div class="filter-field">
                        <label class="form-label">Buscar pago</label>
                        <div class="ui icon input pagos-search-input">
                            <i class="search icon"></i>
                            <input
                                type="text"
                                id="buscar-pago"
                                placeholder="Concepto, monto, vencimiento o comprobantes..."
                                autocomplete="off"
                            >
                        </div>
                    </div>

                    <button type="button" class="btn btn-secondary btn-clear-filters" onclick="limpiarFiltrosPagos()">
                        <i class="times icon"></i>
                        Limpiar
                    </button>
                </div>

                <div class="pagos-summary">
                    <div class="summary-item">
                        <div class="summary-icon">
                            <i class="money bill alternate icon"></i>
                        </div>

                        <div>
                            <span>Pagos encontrados</span>
                            <strong id="total-pagos">0</strong>
                        </div>
                    </div>
                </div>

                <div id="pagos-loader" class="pagos-loader">
                    <div class="ui active centered inline text loader large">Cargando pagos...</div>
                </div>

                <div id="pagos-container" class="pagos-grid"></div>

                <div id="pagos-pagination" class="pagos-pagination" style="display: none;"></div>

                <div id="sin-pagos" class="empty-pagos-card" style="display: none;">
                    <div class="empty-pagos-icon">
                        <i class="money bill alternate outline icon"></i>
                    </div>

                    <h3>No hay pagos registrados</h3>
                    <p>Aún no se han publicado recibos de pago.</p>
                </div>

                <div id="sin-resultados" class="empty-pagos-card" style="display: none;">
                    <div class="empty-pagos-icon">
                        <i class="search icon"></i>
                    </div>

                    <h3>No se encontraron resultados</h3>
                    <p>No hay pagos que coincidan con tu búsqueda.</p>

                    <button type="button" class="btn btn-secondary btn-sm" onclick="limpiarFiltrosPagos()">
                        <i class="undo icon"></i>
                        Limpiar búsqueda
                    </button>
                </div>

                <div id="pagos-error" class="empty-pagos-card error" style="display: none;">
                    <div class="empty-pagos-icon error">
                        <i class="warning sign icon"></i>
                    </div>

                    <h3>Error al cargar pagos</h3>
                    <p>No se pudieron cargar los pagos. Intenta nuevamente.</p>

                    <button type="button" class="btn btn-primary btn-sm" onclick="cargarPagos()">
                        <i class="refresh icon"></i>
                        Reintentar
                    </button>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<div class="ui modal pago-evidencia-modal" id="modal-evidencia">
    <div class="header">
        <i class="file image icon"></i>
        Documento cargado
    </div>

    <div class="content evidencia-modal-content">
        <img id="imagen-evidencia" src="" alt="Evidencia">
    </div>
</div>

<style>
    :root {
        --pago-primary: #3b82f6;
        --pago-primary-dark: #2563eb;
        --pago-secondary: #667eea;
        --pago-success: #10b981;
        --pago-danger: #ef4444;
        --pago-warning: #f59e0b;
        --pago-text-main: #0f172a;
        --pago-text-muted: #64748b;
        --pago-text-soft: #94a3b8;
        --pago-border: #e2e8f0;
        --pago-surface: #ffffff;
        --pago-soft-bg: #f8fafc;
    }

    .pagos-admin-page {
        width: 100%;
        padding-bottom: 24px;
    }

    .pagos-hero {
        background:
            radial-gradient(circle at top right, rgba(255,255,255,0.24), transparent 34%),
            radial-gradient(circle at bottom left, rgba(255,255,255,0.14), transparent 30%),
            linear-gradient(135deg, var(--pago-primary) 0%, var(--pago-primary-dark) 100%);
        border-radius: 26px;
        padding: 26px;
        color: white;
        margin-bottom: 22px;
        box-shadow: 0 12px 30px rgba(37, 99, 235, 0.18);
        overflow: hidden;
    }

    .pagos-hero-content {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 18px;
        flex-wrap: wrap;
    }

    .pagos-hero-left {
        display: flex;
        align-items: center;
        gap: 16px;
        min-width: 0;
    }

    .pagos-hero-icon {
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

    .pagos-hero-icon i {
        color: white;
        font-size: 1.8rem;
        margin: 0 !important;
    }

    .pagos-title {
        margin: 0 0 6px 0;
        font-size: clamp(1.55rem, 3vw, 2.15rem);
        font-weight: 900;
        letter-spacing: -0.035em;
        line-height: 1.08;
    }

    .pagos-subtitle {
        margin: 0;
        opacity: 0.92;
        font-size: 1rem;
        line-height: 1.45;
    }

    .pagos-hero-pill {
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

    .pagos-hero-pill i {
        margin: 0 !important;
    }

    .pagos-layout {
        display: grid;
        grid-template-columns: minmax(300px, 420px) minmax(0, 1fr);
        gap: 24px;
        align-items: start;
    }

    .pago-form-card,
    .pagos-list-card,
    .empty-pagos-card {
        background: var(--pago-surface);
        border: 1px solid var(--pago-border);
        border-radius: 22px;
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.06);
        overflow: hidden;
    }

    .pago-form-card {
        position: sticky;
        top: 18px;
    }

    .pago-card-header {
        padding: 18px 20px;
        border-bottom: 1px solid #f1f5f9;
        background: linear-gradient(180deg, #ffffff 0%, #fbfdff 100%);
    }

    .pago-card-title-wrap {
        display: flex;
        align-items: center;
        gap: 14px;
        min-width: 0;
    }

    .pago-card-icon {
        width: 52px;
        height: 52px;
        border-radius: 16px;
        background: linear-gradient(135deg, var(--pago-primary) 0%, var(--pago-primary-dark) 100%);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        box-shadow: 0 8px 18px rgba(59, 130, 246, 0.24);
    }

    .pago-card-icon.secondary {
        background: linear-gradient(135deg, var(--pago-secondary) 0%, var(--pago-primary-dark) 100%);
    }

    .pago-card-icon i {
        margin: 0 !important;
        font-size: 1.25rem;
    }

    .pago-card-title {
        margin: 0;
        color: var(--pago-text-main);
        font-size: 1.12rem;
        font-weight: 900;
        line-height: 1.25;
    }

    .pago-card-subtitle {
        margin: 4px 0 0 0;
        color: var(--pago-text-muted);
        font-size: 0.9rem;
        line-height: 1.35;
    }

    .pago-card-body {
        padding: 20px;
    }

    .form-group {
        margin-bottom: 18px;
    }

    .form-label {
        display: block;
        color: var(--pago-text-main);
        font-weight: 800;
        margin-bottom: 8px;
        font-size: 0.9rem;
    }

    .pago-input {
        min-height: 46px;
        border-radius: 14px !important;
        width: 100%;
        max-width: 100%;
        box-sizing: border-box;
        border: 1px solid var(--pago-border) !important;
        transition: border-color 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
    }

    .pago-input:focus {
        border-color: rgba(59, 130, 246, 0.65) !important;
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.12) !important;
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
        color: var(--pago-primary-dark);
        pointer-events: none;
        z-index: 1;
        margin: 0 !important;
    }

    .input-with-icon {
        padding-left: 42px !important;
    }

    .form-help-text {
        display: block;
        color: var(--pago-text-soft);
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
        background: #eff6ff;
        opacity: 1;
    }

    .btn-submit-pago {
        width: 100%;
        min-height: 48px;
        border-radius: 14px;
        margin-top: 12px;
        display: inline-flex !important;
        align-items: center;
        justify-content: center;
        gap: 8px;
        font-weight: 900;
        box-shadow: 0 8px 18px rgba(59, 130, 246, 0.22);
    }

    .btn-submit-pago i {
        margin: 0 !important;
    }

    .pagos-toolbar {
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

    .pagos-search-input {
        width: 100%;
    }

    .pagos-search-input input {
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

    .pagos-summary {
        padding: 16px 20px 0;
    }

    .summary-item {
        display: inline-flex;
        align-items: center;
        gap: 12px;
        background: var(--pago-soft-bg);
        border: 1px solid #eef2f7;
        border-radius: 16px;
        padding: 12px 14px;
    }

    .summary-icon {
        width: 42px;
        height: 42px;
        border-radius: 14px;
        background: #dbeafe;
        color: var(--pago-primary-dark);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .summary-icon i {
        margin: 0 !important;
    }

    .summary-item span {
        display: block;
        color: var(--pago-text-muted);
        font-size: 0.78rem;
        font-weight: 800;
        margin-bottom: 2px;
    }

    .summary-item strong {
        display: block;
        color: var(--pago-text-main);
        font-size: 1.2rem;
        line-height: 1;
    }

    .pagos-loader {
        min-height: 220px;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 40px 20px;
    }

    .pagos-grid {
        padding: 20px;
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 16px;
    }

    .pago-item-card {
        border: 1px solid var(--pago-border);
        border-radius: 18px;
        background: white;
        box-shadow: 0 6px 18px rgba(15, 23, 42, 0.05);
        overflow: hidden;
        display: flex;
        flex-direction: column;
        transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
    }

    .pago-item-card:hover {
        transform: translateY(-2px);
        border-color: rgba(59, 130, 246, 0.35);
        box-shadow: 0 10px 24px rgba(15, 23, 42, 0.08);
    }

    .pago-item-header {
        padding: 16px;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: flex-start;
        gap: 12px;
    }

    .pago-item-icon {
        width: 46px;
        height: 46px;
        border-radius: 14px;
        background: #dbeafe;
        color: var(--pago-primary-dark);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .pago-item-icon i {
        margin: 0 !important;
        font-size: 1.15rem;
    }

    .pago-item-title {
        margin: 0;
        color: var(--pago-text-main);
        font-size: 1rem;
        font-weight: 900;
        line-height: 1.3;
        overflow-wrap: anywhere;
    }

    .pago-item-badges {
        display: flex;
        flex-wrap: wrap;
        gap: 7px;
        margin-top: 9px;
    }

    .pago-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 6px 9px;
        border-radius: 999px;
        font-size: 0.72rem;
        font-weight: 900;
        line-height: 1;
    }

    .pago-badge i {
        margin: 0 !important;
    }

    .pago-badge.vigente {
        background: #dcfce7;
        color: #166534;
    }

    .pago-badge.vencido {
        background: #fee2e2;
        color: #991b1b;
    }

    .pago-badge.progreso {
        background: #dbeafe;
        color: #1d4ed8;
    }

    .pago-item-body {
        padding: 16px;
        flex: 1;
        display: grid;
        gap: 10px;
    }

    .pago-meta-item {
        display: flex;
        align-items: flex-start;
        gap: 9px;
        color: var(--pago-text-muted);
        font-size: 0.88rem;
        line-height: 1.4;
        overflow-wrap: anywhere;
    }

    .pago-meta-item i {
        color: var(--pago-primary-dark);
        margin: 2px 0 0 0 !important;
        flex-shrink: 0;
    }

    .pago-progreso-box {
        background: var(--pago-soft-bg);
        border: 1px solid #eef2f7;
        border-radius: 14px;
        padding: 12px;
        overflow-wrap: anywhere;
    }

    .pago-progreso-box * {
        max-width: 100%;
    }

    .pago-item-footer {
        padding: 14px 16px 16px;
        border-top: 1px solid #f1f5f9;
        background: #fbfdff;
    }

    .pago-actions {
        width: 100%;
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 10px;
    }

    .pago-actions .btn,
    .pago-actions button,
    .pago-actions a {
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

    .pago-actions i {
        margin: 0 !important;
        flex-shrink: 0;
    }

    .pago-actions .btn-ver-detalles {
        background: #eff6ff !important;
        color: #1d4ed8 !important;
        border: 1px solid #bfdbfe !important;
    }

    .pago-actions .btn-eliminar {
        background: #fef2f2 !important;
        color: #b91c1c !important;
        border: 1px solid #fecaca !important;
    }

    .pago-actions .btn-ver-detalles:hover {
        background: #dbeafe !important;
    }

    .pago-actions .btn-eliminar:hover {
        background: #fee2e2 !important;
    }

    .pagos-pagination {
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
        border: 1px solid var(--pago-border);
        background: #ffffff;
        color: var(--pago-text-muted);
        border-radius: 12px;
        font-weight: 900;
        cursor: pointer;
        transition: background 0.2s ease, color 0.2s ease, border-color 0.2s ease;
    }

    .pagination-btn:hover {
        background: #eff6ff;
        color: var(--pago-primary-dark);
        border-color: rgba(59, 130, 246, 0.35);
    }

    .pagination-btn.active {
        background: linear-gradient(135deg, var(--pago-primary) 0%, var(--pago-primary-dark) 100%);
        color: #ffffff;
        border-color: transparent;
        box-shadow: 0 8px 18px rgba(59, 130, 246, 0.22);
    }

    .pagination-btn:disabled {
        opacity: 0.45;
        cursor: not-allowed;
        background: var(--pago-soft-bg);
        color: var(--pago-text-soft);
    }

    .pagination-info {
        color: var(--pago-text-muted);
        font-size: 0.86rem;
        font-weight: 800;
        margin: 0 8px;
    }

    .empty-pagos-card {
        margin: 20px;
        padding: 44px 20px;
        text-align: center;
    }

    .empty-pagos-icon {
        width: 78px;
        height: 78px;
        border-radius: 24px;
        margin: 0 auto 18px;
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.12), rgba(37, 99, 235, 0.16));
        color: var(--pago-primary-dark);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .empty-pagos-icon.error {
        background: #fee2e2;
        color: var(--pago-danger);
    }

    .empty-pagos-icon i {
        font-size: 2rem;
        margin: 0 !important;
    }

    .empty-pagos-card h3 {
        margin: 0 0 8px 0;
        color: #475569;
        font-weight: 900;
    }

    .empty-pagos-card p {
        margin: 0 0 18px 0;
        color: var(--pago-text-soft);
        line-height: 1.5;
    }

    .pago-evidencia-modal {
        border-radius: 20px !important;
        overflow: hidden;
    }

    .pago-evidencia-modal > .header {
        background: linear-gradient(135deg, var(--pago-secondary) 0%, var(--pago-primary-dark) 100%) !important;
        color: white !important;
        font-weight: 900 !important;
    }

    .pago-evidencia-modal > .header i {
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

    @media (max-width: 1200px) {
        .pagos-layout {
            grid-template-columns: 1fr;
        }

        .pago-form-card {
            position: static;
        }
    }

    @media (max-width: 900px) {
        .pagos-grid {
            grid-template-columns: 1fr;
        }

        .pagos-toolbar {
            grid-template-columns: 1fr;
        }

        .btn-clear-filters {
            width: 100%;
        }
    }

    @media (max-width: 768px) {
        .pagos-hero {
            border-radius: 0 0 24px 24px;
            margin: -8px -4px 18px;
            padding: 22px;
        }

        .pagos-hero-content {
            align-items: stretch;
        }

        .pagos-hero-left {
            align-items: flex-start;
        }

        .pagos-hero-icon {
            width: 54px;
            height: 54px;
            border-radius: 18px;
        }

        .pagos-title {
            font-size: 1.55rem;
        }

        .pagos-subtitle {
            font-size: 0.92rem;
        }

        .pagos-hero-pill {
            width: 100%;
            justify-content: center;
            border-radius: 16px;
        }

        .pago-form-card,
        .pagos-list-card,
        .empty-pagos-card {
            border-radius: 18px;
        }

        .pago-card-header,
        .pago-card-body,
        .pagos-toolbar {
            padding: 16px;
        }

        .pago-card-icon {
            width: 46px;
            height: 46px;
            border-radius: 14px;
        }

        .pago-card-title {
            font-size: 1.04rem;
        }

        .pago-card-subtitle {
            font-size: 0.85rem;
        }

        .pagos-summary {
            padding: 14px 16px 0;
        }

        .pagos-grid {
            padding: 16px;
        }

        .pago-item-card:hover {
            transform: none;
        }

        .pago-item-footer {
            padding: 12px;
            background: #ffffff;
        }

        .pago-actions {
            grid-template-columns: 1fr;
            gap: 8px;
        }

        .pago-actions .btn,
        .pago-actions button,
        .pago-actions a {
            min-height: 48px;
            font-size: 0.9rem;
        }

        .pagos-pagination {
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
        input[type="number"],
        input[type="date"],
        select {
            font-size: 16px !important;
        }
    }

    @media (max-width: 420px) {
        .pagos-hero-left {
            gap: 12px;
        }

        .pago-card-title-wrap,
        .pago-item-header {
            align-items: flex-start;
        }

        .pago-item-icon {
            width: 42px;
            height: 42px;
            border-radius: 13px;
        }

        .pago-item-title {
            font-size: 0.98rem;
        }

        .pago-badge {
            max-width: 100%;
            white-space: normal;
            line-height: 1.2;
        }
    }
</style>

<script>
    const BASE_URL = "{{ url('/') }}";

    let pagosOriginales = [];
    let pagosFiltrados = [];
    let paginaActualPagos = 1;
    let pagosPorPagina = 6;
    let isSubmittingPago = false;

    $(document).ready(function() {
        $('#modal-evidencia').modal();

        configurarUsuariosDropdown();
        establecerFechaMinimaPago();

        cargarPagos();

        $('#buscar-pago').on('keyup input change', function() {
            filtrarPagos();
        });

        $('#form-recibo').submit(function(e) {
            e.preventDefault();

            if (isSubmittingPago) {
                return false;
            }

            const $btn = $('#btn-recibo');
            const formData = $(this).serialize();

            isSubmittingPago = true;
            $btn.addClass('loading disabled').html('<i class="spinner loading icon"></i> Guardando...');

            alertify.confirm(
                '¿Confirmar notificación ahora?',
                'Se enviará correo a todos los usuarios seleccionados informando del pago y su fecha límite.',
                function() {
                    setTimeout(function() {
                        $.ajax({
                            url: `${BASE_URL}/administrador/pago/crear-pago`,
                            method: 'POST',
                            data: formData,
                            success: function(res) {
                                alertify.alert(res.header, res.message, function() {
                                    cargarPagos();
                                });

                                $('#form-recibo')[0].reset();
                                $('#usuarios-recibo').dropdown('clear');
                                establecerFechaMinimaPago();
                            },
                            error: function() {
                                alertify.error('Error al publicar recibo');
                            },
                            complete: function() {
                                $btn.removeClass('loading disabled').html('<i class="paper plane icon"></i> Publicar recibo');
                                isSubmittingPago = false;
                            }
                        });
                    }, 100);
                },
                function() {
                    $btn.removeClass('loading disabled').html('<i class="paper plane icon"></i> Publicar recibo');
                    isSubmittingPago = false;
                }
            ).set('labels', {
                ok: 'Sí, enviar',
                cancel: 'Cancelar'
            });
        });
    });

    function configurarUsuariosDropdown() {
        $('#usuarios-recibo').dropdown({
            allowAdditions: false,
            placeholder: 'Selecciona usuarios',
            message: {
                noResults: 'Sin resultados'
            },
            onAdd: function(addedValue) {
                if (addedValue === '' || addedValue === 'todos') {
                    if (addedValue === 'todos') {
                        const ids = $('#usuarios-recibo option[data-pago="1"]').map(function() {
                            return $(this).val();
                        }).get();

                        setTimeout(function() {
                            $('#usuarios-recibo').dropdown('set exactly', ids);
                        }, 10);
                    }

                    return false;
                }
            }
        });
    }

    function establecerFechaMinimaPago() {
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

    function cargarPagos() {
        $('#pagos-loader').show();
        $('#pagos-container').empty();
        $('#pagos-pagination').hide().empty();
        $('#sin-pagos').hide();
        $('#sin-resultados').hide();
        $('#pagos-error').hide();

        $.ajax({
            url: "{{ route('admin.pago.obtenerPagos') }}",
            method: 'GET',
            dataType: 'json',
            data: {
                start: 0,
                length: 1000
            },
            success: function(response) {
                $('#pagos-loader').hide();

                pagosOriginales = ordenarPagosPorFechaDesc(response.data || []);
                renderizarPagos(pagosOriginales);
            },
            error: function() {
                $('#pagos-loader').hide();
                $('#pagos-error').show();
            }
        });
    }

    function ordenarPagosPorFechaDesc(pagos) {
        return [...pagos].sort(function(a, b) {
            const fechaA = obtenerFechaPago(a);
            const fechaB = obtenerFechaPago(b);

            return fechaB - fechaA;
        });
    }

    function obtenerFechaPago(pago) {
        const fecha = extraerTexto(
            pago.created_at ||
            pago.fecha_creacion ||
            pago.vencimiento ||
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

    function renderizarPagos(pagos) {
        pagosFiltrados = pagos;
        paginaActualPagos = 1;
        renderizarPaginaPagos();
    }

    function renderizarPaginaPagos() {
        const container = $('#pagos-container');
        const pagination = $('#pagos-pagination');

        container.empty();
        pagination.empty();

        $('#total-pagos').text(pagosFiltrados.length);

        $('#sin-pagos').hide();
        $('#sin-resultados').hide();
        $('#pagos-error').hide();

        if (pagosOriginales.length === 0) {
            pagination.hide();
            $('#sin-pagos').show();
            return;
        }

        if (pagosFiltrados.length === 0) {
            pagination.hide();
            $('#sin-resultados').show();
            return;
        }

        const totalPaginas = Math.ceil(pagosFiltrados.length / pagosPorPagina);
        const inicio = (paginaActualPagos - 1) * pagosPorPagina;
        const fin = inicio + pagosPorPagina;
        const pagosPagina = pagosFiltrados.slice(inicio, fin);

        pagosPagina.forEach(function(pago) {
            const id = extraerTexto(pago.id || '');
            const concepto = extraerTexto(pago.concepto || 'Pago sin concepto');
            const cantidad = extraerTexto(pago.cantidad || '$0.00');
            const vencimiento = extraerTexto(pago.vencimiento || 'Sin fecha');
            const progresoHtml = pago.progreso || '';
            const estado = obtenerEstadoPagoPorVencimiento(vencimiento);

            const accionesHtml = `
                <button
                    type="button"
                    class="btn btn-secondary btn-sm btn-ver-detalles"
                    data-id="${escapeHtml(id)}"
                >
                    <i class="eye icon"></i>
                    Detalles
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
                <div class="pago-item-card">
                    <div class="pago-item-header">
                        <div class="pago-item-icon">
                            <i class="money bill alternate icon"></i>
                        </div>

                        <div style="min-width: 0; flex: 1;">
                            <h3 class="pago-item-title">${escapeHtml(concepto)}</h3>

                            <div class="pago-item-badges">
                                <span class="pago-badge ${estado.clase}">
                                    <i class="${estado.icono} icon"></i>
                                    ${estado.texto}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="pago-item-body">
                        <div class="pago-meta-item">
                            <i class="dollar sign icon"></i>
                            <span>${escapeHtml(cantidad)}</span>
                        </div>

                        <div class="pago-meta-item">
                            <i class="calendar alternate outline icon"></i>
                            <span>Vence: ${escapeHtml(vencimiento)}</span>
                        </div>

                        <div class="pago-progreso-box">
                            ${progresoHtml || `
                                <div class="pago-meta-item">
                                    <i class="file alternate outline icon"></i>
                                    <span>Sin comprobantes registrados</span>
                                </div>
                            `}
                        </div>
                    </div>

                    <div class="pago-item-footer">
                        <div class="pago-actions">
                            ${accionesHtml}
                        </div>
                    </div>
                </div>
            `;

            container.append(card);
        });

        renderizarPaginacionPagos(totalPaginas, pagosFiltrados.length);
    }

    function obtenerEstadoPagoPorVencimiento(vencimiento) {
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

    function renderizarPaginacionPagos(totalPaginas, totalPagos) {
        const pagination = $('#pagos-pagination');

        pagination.empty();

        if (totalPaginas <= 1) {
            pagination.hide();
            return;
        }

        const inicio = ((paginaActualPagos - 1) * pagosPorPagina) + 1;
        const fin = Math.min(paginaActualPagos * pagosPorPagina, totalPagos);

        pagination.show();

        pagination.append(`
            <button
                type="button"
                class="pagination-btn"
                onclick="cambiarPaginaPagos(${paginaActualPagos - 1})"
                ${paginaActualPagos === 1 ? 'disabled' : ''}
            >
                <i class="chevron left icon"></i>
            </button>
        `);

        const paginas = obtenerPaginasVisiblesPagos(totalPaginas);

        paginas.forEach(function(pagina) {
            if (pagina === '...') {
                pagination.append(`<span class="pagination-info">...</span>`);
                return;
            }

            pagination.append(`
                <button
                    type="button"
                    class="pagination-btn ${pagina === paginaActualPagos ? 'active' : ''}"
                    onclick="cambiarPaginaPagos(${pagina})"
                >
                    ${pagina}
                </button>
            `);
        });

        pagination.append(`
            <button
                type="button"
                class="pagination-btn"
                onclick="cambiarPaginaPagos(${paginaActualPagos + 1})"
                ${paginaActualPagos === totalPaginas ? 'disabled' : ''}
            >
                <i class="chevron right icon"></i>
            </button>
        `);

        pagination.append(`
            <div class="pagination-info">
                Mostrando ${inicio}-${fin} de ${totalPagos}
            </div>
        `);
    }

    function cambiarPaginaPagos(pagina) {
        const totalPaginas = Math.ceil(pagosFiltrados.length / pagosPorPagina);

        if (pagina < 1 || pagina > totalPaginas) {
            return;
        }

        paginaActualPagos = pagina;
        renderizarPaginaPagos();

        document.querySelector('.pagos-list-card')?.scrollIntoView({
            behavior: 'smooth',
            block: 'start'
        });
    }

    function obtenerPaginasVisiblesPagos(totalPaginas) {
        const paginas = [];

        if (totalPaginas <= 5) {
            for (let i = 1; i <= totalPaginas; i++) {
                paginas.push(i);
            }

            return paginas;
        }

        paginas.push(1);

        if (paginaActualPagos > 3) {
            paginas.push('...');
        }

        const inicio = Math.max(2, paginaActualPagos - 1);
        const fin = Math.min(totalPaginas - 1, paginaActualPagos + 1);

        for (let i = inicio; i <= fin; i++) {
            paginas.push(i);
        }

        if (paginaActualPagos < totalPaginas - 2) {
            paginas.push('...');
        }

        paginas.push(totalPaginas);

        return paginas;
    }

    function filtrarPagos() {
        const busqueda = normalizarTexto($('#buscar-pago').val());

        const filtrados = pagosOriginales.filter(function(pago) {
            const concepto = extraerTexto(pago.concepto || '');
            const cantidad = extraerTexto(pago.cantidad || '');
            const vencimiento = extraerTexto(pago.vencimiento || '');
            const progreso = extraerTexto(pago.progreso || '');

            const textoCompleto = normalizarTexto(
                concepto + ' ' + cantidad + ' ' + vencimiento + ' ' + progreso
            );

            return textoCompleto.includes(busqueda);
        });

        renderizarPagos(ordenarPagosPorFechaDesc(filtrados));
    }

    function limpiarFiltrosPagos() {
        $('#buscar-pago').val('');
        renderizarPagos(pagosOriginales);
    }

    $(document).on('click', '.btn-ver-evidencia', function() {
        const imgSrc = $(this).data('img');

        $('#imagen-evidencia').attr('src', imgSrc);
        $('#modal-evidencia').modal('show');
    });

    $(document).on('click', '.btn-ver-detalles', function() {
        const id = $(this).data('id');

        window.location.href = `${BASE_URL}/administrador/pago/detalle-pagos/${id}`;
    });

    $(document).on('click', '.btn-eliminar', function() {
        const id = $(this).data('id');

        alertify.confirm(
            '¿Eliminar Pago?',
            '¿Estás seguro de que deseas eliminar este pago?, todos los usuarios serán notificados.',
            function() {
                mostrarLoaderPantalla();

                setTimeout(function() {
                    $.ajax({
                        url: `${BASE_URL}/administrador/pago/eliminar-mainpago/${id}`,
                        method: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(res) {
                            alertify.alert(res.header, res.message, function() {
                                cargarPagos();
                            });
                        },
                        error: function() {
                            alertify.error('Error al eliminar');
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