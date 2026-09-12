<x-app-layout>
    <div class="documentos-page">
        <div class="page-header documentos-header">
            <div class="page-header-left">
                <div class="page-icon documentos-page-icon">
                    <i class="file alternate icon"></i>
                </div>

                <div>
                    <h1 class="page-title">Documentos y Comprobantes</h1>
                    <p class="page-subtitle">Consulta documentos generales y comprobantes de gastos del condominio</p>
                </div>
            </div>
        </div>

        <div class="documentos-summary-card">
            <div class="summary-item">
                <div class="summary-icon total">
                    <i class="folder open icon"></i>
                </div>

                <div>
                    <span class="summary-label">Documentos encontrados</span>
                    <strong class="summary-value" id="total-documentos">0</strong>
                </div>
            </div>

            <div class="summary-helper">
                <i class="search icon"></i>
                <span>Busca por título, descripción, tipo, monto o fecha.</span>
            </div>
        </div>

        <div class="documentos-filters-card">
            <div class="filters-grid">
                <div class="filter-field">
                    <label class="form-label">Buscar documento</label>

                    <div class="ui icon input documentos-search-input">
                        <i class="search icon"></i>
                        <input
                            type="text"
                            id="buscar-documento"
                            placeholder="Ej. agua, mantenimiento, reglamento..."
                            autocomplete="off"
                        >
                    </div>
                </div>

                <div class="filter-field">
                    <label class="form-label">Filtrar por tipo</label>

                    <select id="filtro-categoria" class="ui fluid dropdown documentos-dropdown">
                        <option value="todos">Todos los tipos</option>
                    </select>
                </div>
            </div>
        </div>

        <div id="documentos-loader" class="documentos-loader">
            <div class="ui active centered inline text loader large">Cargando documentos...</div>
        </div>

        <div id="documentos-container" class="documentos-grid"></div>

        <div id="sin-resultados" class="documentos-empty-card" style="display: none;">
            <div class="empty-state">
                <div class="empty-state-icon">
                    <i class="search icon"></i>
                </div>

                <h3>No se encontraron documentos</h3>
                <p>No hay documentos que coincidan con tu búsqueda o filtro seleccionado.</p>

                <button type="button" class="btn btn-secondary btn-sm" onclick="limpiarFiltrosDocumentos()">
                    <i class="undo icon"></i>
                    Limpiar filtros
                </button>
            </div>
        </div>

        <div id="documentos-error" class="documentos-empty-card error-card" style="display: none;">
            <div class="empty-state">
                <div class="empty-state-icon error">
                    <i class="warning sign icon"></i>
                </div>

                <h3>Error al cargar documentos</h3>
                <p>No se pudieron cargar los documentos. Intenta de nuevo.</p>

                <button type="button" class="btn btn-primary btn-sm" onclick="cargarDocumentos()">
                    <i class="refresh icon"></i>
                    Reintentar
                </button>
            </div>
        </div>
    </div>
</x-app-layout>

<style>
    :root {
        --primary: #8b5cf6;
        --primary-dark: #7c3aed;
        --secondary: #6366f1;
        --success: #10b981;
        --danger: #ef4444;
        --warning: #f59e0b;
        --info: #3b82f6;
        --text-main: #0f172a;
        --text-muted: #64748b;
        --text-soft: #94a3b8;
        --border: #e2e8f0;
        --surface: #ffffff;
        --soft-bg: #f8fafc;
    }

    .documentos-page {
        width: 100%;
    }

    .documentos-header {
        gap: 20px;
        margin-bottom: 22px;
    }

    .documentos-page-icon {
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
    }

    .documentos-info-card,
    .documentos-summary-card,
    .documentos-filters-card,
    .documentos-empty-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 22px;
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.06);
    }

    .documentos-info-card {
        display: flex;
        align-items: flex-start;
        gap: 14px;
        padding: 18px 20px;
        margin-bottom: 20px;
        background:
            linear-gradient(135deg, rgba(139, 92, 246, 0.08) 0%, rgba(124, 58, 237, 0.05) 100%),
            #ffffff;
    }

    .documentos-info-card .info-icon {
        width: 48px;
        height: 48px;
        border-radius: 16px;
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        box-shadow: 0 8px 18px rgba(139, 92, 246, 0.24);
    }

    .documentos-info-card .info-icon i {
        margin: 0;
        font-size: 1.25rem;
    }

    .documentos-info-card h3 {
        margin: 0 0 4px 0;
        color: var(--text-main);
        font-size: 1rem;
        font-weight: 800;
    }

    .documentos-info-card p {
        margin: 0;
        color: var(--text-muted);
        line-height: 1.5;
        font-size: 0.92rem;
    }

    .documentos-summary-card {
        padding: 18px 20px;
        margin-bottom: 20px;
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
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        box-shadow: 0 8px 18px rgba(139, 92, 246, 0.24);
    }

    .summary-icon.total {
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
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
        display: block;
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
        margin: 0;
    }

    .documentos-filters-card {
        padding: 18px 20px;
        margin-bottom: 22px;
    }

    .filters-grid {
        display: grid;
        grid-template-columns: minmax(0, 2fr) minmax(220px, 1fr);
        gap: 16px;
        align-items: end;
    }

    .filter-field {
        width: 100%;
    }

    .documentos-search-input {
        width: 100%;
    }

    .documentos-search-input input,
    .documentos-dropdown {
        min-height: 46px;
        border-radius: 14px !important;
    }

    .documentos-loader {
        min-height: 220px;
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 40px 20px;
    }

    .documentos-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 20px;
    }

    .documento-card {
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

    .documento-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 16px 34px rgba(15, 23, 42, 0.12);
        border-color: rgba(139, 92, 246, 0.35);
    }

    .documento-card-header {
        padding: 18px;
        display: flex;
        align-items: flex-start;
        gap: 14px;
        border-bottom: 1px solid #f1f5f9;
    }

    .documento-icon {
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

    .documento-icon i {
        margin: 0;
        font-size: 1.2rem;
    }

    .documento-title-wrap {
        min-width: 0;
        flex: 1;
    }

    .documento-title {
        margin: 0;
        color: var(--text-main);
        font-size: 1.08rem;
        font-weight: 800;
        line-height: 1.25;
        overflow-wrap: anywhere;
    }

    .documento-category {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        margin-top: 9px;
        padding: 6px 10px;
        border-radius: 999px;
        background: #ede9fe;
        color: #5b21b6;
        font-size: 0.75rem;
        font-weight: 800;
    }

    .documento-category i {
        margin: 0;
    }

    .documento-card-body {
        padding: 18px;
        flex: 1;
    }

    .documento-description {
        color: var(--text-muted);
        font-size: 0.92rem;
        line-height: 1.55;
        margin: 0 0 14px 0;
        overflow-wrap: anywhere;
    }

    .documento-meta {
        display: grid;
        gap: 10px;
    }

    .documento-meta-item {
        display: flex;
        align-items: center;
        gap: 10px;
        background: var(--soft-bg);
        border: 1px solid #eef2f7;
        border-radius: 14px;
        padding: 11px 12px;
        color: var(--text-main);
        font-size: 0.9rem;
        font-weight: 700;
    }

    .documento-meta-item i {
        color: var(--primary);
        margin: 0;
        flex-shrink: 0;
    }

    .documento-meta-item span {
        overflow-wrap: anywhere;
    }

    .documento-card-footer {
        padding: 16px 18px 18px;
        border-top: 1px solid #f1f5f9;
    }

    .documento-action-wrap {
        width: 100%;
    }

    .documento-action-wrap a,
    .documento-action-wrap button {
        width: 100%;
        min-height: 44px;
        border-radius: 14px !important;
        display: inline-flex !important;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .documento-no-file {
        min-height: 44px;
        border-radius: 14px;
        background: var(--soft-bg);
        color: var(--text-soft);
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        font-size: 0.9rem;
        font-weight: 700;
    }

    .documento-no-file i {
        margin: 0;
    }

    .documentos-empty-card {
        padding: 44px 20px;
        text-align: center;
    }

    .documentos-empty-card .empty-state {
        max-width: 420px;
        margin: 0 auto;
    }

    .documentos-empty-card .empty-state-icon {
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

    .documentos-empty-card .empty-state-icon.error {
        background: #fee2e2;
        color: var(--danger);
    }

    .documentos-empty-card .empty-state-icon i {
        margin: 0;
        font-size: 2rem;
    }

    .documentos-empty-card h3 {
        margin: 0 0 8px 0;
        color: var(--text-main);
        font-weight: 800;
    }

    .documentos-empty-card p {
        margin: 0 0 18px 0;
        color: var(--text-muted);
        line-height: 1.5;
    }

    @media (max-width: 1200px) {
        .documentos-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 768px) {
        .documentos-header {
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

        .documentos-info-card,
        .documentos-summary-card,
        .documentos-filters-card,
        .documentos-empty-card {
            border-radius: 18px;
        }

        .documentos-info-card {
            padding: 16px;
        }

        .documentos-summary-card {
            align-items: flex-start;
            flex-direction: column;
            padding: 16px;
        }

        .summary-helper {
            width: 100%;
            border-radius: 14px;
            align-items: flex-start;
        }

        .summary-icon {
            width: 44px;
            height: 44px;
            border-radius: 14px;
        }

        .summary-value {
            font-size: 1.35rem;
        }

        .filters-grid {
            grid-template-columns: 1fr;
        }

        .documentos-grid {
            grid-template-columns: 1fr;
            gap: 16px;
        }

        .documento-card {
            border-radius: 18px;
        }

        .documento-card-header,
        .documento-card-body {
            padding: 16px;
        }

        .documento-icon {
            width: 46px;
            height: 46px;
            border-radius: 14px;
        }

        .documento-title {
            font-size: 1.02rem;
        }

        .documento-card-footer {
            padding: 14px 16px 16px;
        }

        .documentos-search-input input {
            font-size: 16px;
        }
    }

    @media (max-width: 420px) {
        .documentos-info-card {
            flex-direction: column;
        }

        .documento-card-header {
            align-items: flex-start;
        }
    }

    @media (hover: none) {
        .documento-card:hover {
            transform: none;
        }
    }
</style>

<script>
    let documentosOriginales = [];

    function cargarDocumentos() {
        $('#documentos-loader').show();
        $('#documentos-container').empty();
        $('#sin-resultados').hide();
        $('#documentos-error').hide();

        $.ajax({
            url: "{{ route('usuario.documentos.obtener') }}",
            method: 'GET',
            dataType: 'json',
            data: {
                start: 0,
                length: 1000
            },
            success: function(response) {
                $('#documentos-loader').hide();

                documentosOriginales = response.data || [];

                llenarCategorias(documentosOriginales);
                renderizarDocumentos(documentosOriginales);
            },
            error: function() {
                $('#documentos-loader').hide();
                $('#documentos-error').show();
            }
        });
    }

    function llenarCategorias(documentos) {
        const categorias = new Set();

        documentos.forEach(function(documento) {
            const categoriaTexto = extraerTexto(documento.categoria || '').trim();

            if (categoriaTexto) {
                categorias.add(categoriaTexto);
            }
        });

        const filtro = $('#filtro-categoria');
        const valorActual = filtro.val();

        filtro.empty();
        filtro.append('<option value="todos">Todos los tipos</option>');

        Array.from(categorias).sort().forEach(function(categoria) {
            filtro.append(`<option value="${escapeAttribute(categoria)}">${escapeHtml(categoria)}</option>`);
        });

        filtro.dropdown('refresh');

        if (valorActual && valorActual !== 'todos') {
            filtro.dropdown('set selected', valorActual);
        }
    }

    function renderizarDocumentos(documentos) {
        const container = $('#documentos-container');
        container.empty();

        $('#total-documentos').text(documentos.length);

        if (documentos.length === 0) {
            $('#sin-resultados').show();
            return;
        }

        $('#sin-resultados').hide();

        documentos.forEach(function(documento) {
            const titulo = documento.titulo || 'Documento sin título';
            const descripcion = documento.descripcion || 'Sin descripción disponible.';
            const categoria = extraerTexto(documento.categoria || 'General') || 'General';
            const cantidad = documento.cantidad || 'N/A';
            const fecha = documento.fecha || 'Sin fecha';
            const documentoHtml = documento.documento || '';

            const card = `
                <div class="documento-card">
                    <div class="documento-card-header">
                        <div class="documento-icon">
                            <i class="${obtenerIconoCategoria(categoria)} icon"></i>
                        </div>

                        <div class="documento-title-wrap">
                            <h3 class="documento-title">${escapeHtml(extraerTexto(titulo))}</h3>

                            <span class="documento-category">
                                <i class="tag icon"></i>
                                ${escapeHtml(categoria)}
                            </span>
                        </div>
                    </div>

                    <div class="documento-card-body">
                        <p class="documento-description">${escapeHtml(extraerTexto(descripcion))}</p>

                        <div class="documento-meta">
                            <div class="documento-meta-item">
                                <i class="dollar sign icon"></i>
                                <span>${escapeHtml(extraerTexto(cantidad))}</span>
                            </div>

                            <div class="documento-meta-item">
                                <i class="calendar alternate outline icon"></i>
                                <span>${escapeHtml(extraerTexto(fecha))}</span>
                            </div>
                        </div>
                    </div>

                    <div class="documento-card-footer">
                        <div class="documento-action-wrap">
                            ${documentoHtml
                                ? documentoHtml
                                : `<div class="documento-no-file">
                                    <i class="file outline icon"></i>
                                    Sin archivo disponible
                                </div>`
                            }
                        </div>
                    </div>
                </div>
            `;

            container.append(card);
        });
    }

    function filtrarDocumentos() {
        const busqueda = normalizarTexto($('#buscar-documento').val());
        const categoria = $('#filtro-categoria').val();

        const filtrados = documentosOriginales.filter(function(documento) {
            const titulo = extraerTexto(documento.titulo || '');
            const descripcion = extraerTexto(documento.descripcion || '');
            const categoriaTexto = extraerTexto(documento.categoria || '');
            const cantidad = extraerTexto(documento.cantidad || '');
            const fecha = extraerTexto(documento.fecha || '');

            const textoCompleto = normalizarTexto(
                titulo + ' ' + descripcion + ' ' + categoriaTexto + ' ' + cantidad + ' ' + fecha
            );

            const coincideBusqueda = textoCompleto.includes(busqueda);
            const coincideCategoria = categoria === 'todos' || categoriaTexto.trim() === categoria;

            return coincideBusqueda && coincideCategoria;
        });

        renderizarDocumentos(filtrados);
    }

    function limpiarFiltrosDocumentos() {
        $('#buscar-documento').val('');
        $('#filtro-categoria').dropdown('set selected', 'todos');

        renderizarDocumentos(documentosOriginales);
    }

    function obtenerIconoCategoria(categoria) {
        const texto = normalizarTexto(categoria);

        if (texto.includes('agua')) return 'tint';
        if (texto.includes('luz') || texto.includes('electric')) return 'bolt';
        if (texto.includes('mantenimiento')) return 'tools';
        if (texto.includes('reglamento') || texto.includes('documento')) return 'file alternate';
        if (texto.includes('comprobante') || texto.includes('gasto')) return 'receipt';

        return 'file alternate';
    }

    function normalizarTexto(texto) {
        return (texto || '')
            .toString()
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

    function escapeAttribute(text) {
        return escapeHtml(text).replace(/`/g, '&#096;');
    }

    $(document).ready(function() {
        $('#filtro-categoria').dropdown();

        cargarDocumentos();

        $('#buscar-documento').on('keyup input change', function() {
            filtrarDocumentos();
        });

        $('#filtro-categoria').on('change', function() {
            filtrarDocumentos();
        });
    });
</script>