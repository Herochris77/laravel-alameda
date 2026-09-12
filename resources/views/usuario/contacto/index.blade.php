<x-app-layout>
    <div class="page-header">
        <div class="page-header-left">
            <div class="page-icon">
                <i class="phone icon"></i>
            </div>
            <div>
                <h1 class="page-title">Contactos Recomendados</h1>
                <p class="page-subtitle">Contactos útiles para emergencias y servicios de la comunidad</p>
            </div>
        </div>
    </div>

    <div class="filters-card">
        <div class="filters-header">
            <div>
                <h3 class="filters-title">Buscar contactos</h3>
                <p class="filters-subtitle">Encuentra servicios de emergencia o recomendados rápidamente</p>
            </div>
        </div>

        <div class="filters-grid">
            <div class="filter-field">
                <label class="form-label">Buscar contacto</label>
                <div class="ui icon input contact-search-input">
                    <i class="search icon"></i>
                    <input
                        type="text"
                        id="buscar-contacto"
                        placeholder="Nombre, teléfono o descripción..."
                        autocomplete="off"
                        onkeyup="filtrarContactos()"
                    >
                </div>
            </div>

            <div class="filter-field">
                <label class="form-label">Tipo de contacto</label>
                <select class="ui fluid dropdown" id="filtro-tipo" onchange="filtrarContactos()">
                    <option value="todos">Todos los contactos</option>
                    <option value="emergencia">Emergencia</option>
                    <option value="recomendado">Recomendado</option>
                </select>
            </div>
        </div>
    </div>

    <div id="contactos-loader">
        <div class="ui active centered inline text loader large">Cargando contactos...</div>
    </div>

    <div id="contactos-resumen" class="contactos-count" style="display: none;"></div>

    <div id="contactos-container" class="contactos-grid"></div>

    <div id="sin-resultados" class="card" style="display: none;">
        <div class="empty-state">
            <div class="empty-state-icon">
                <i class="search icon"></i>
            </div>
            <h3>No se encontraron contactos</h3>
            <p>No hay contactos que coincidan con tu búsqueda.</p>
        </div>
    </div>
</x-app-layout>

<style>
    :root {
        --primary: #667eea;
        --primary-dark: #4f46e5;
        --danger: #ef4444;
        --danger-dark: #dc2626;
        --success: #10b981;
        --success-dark: #059669;
        --text-main: #0f172a;
        --text-muted: #64748b;
        --border: #e2e8f0;
        --surface: #ffffff;
        --soft-bg: #f8fafc;
    }

    .filters-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 20px;
        padding: 20px;
        margin-bottom: 24px;
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.06);
    }

    .filters-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 16px;
        margin-bottom: 18px;
    }

    .filters-title {
        margin: 0;
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--text-main);
    }

    .filters-subtitle {
        margin: 4px 0 0 0;
        font-size: 0.9rem;
        color: var(--text-muted);
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

    .contact-search-input {
        width: 100%;
    }

    .contact-search-input input,
    #filtro-tipo {
        min-height: 44px;
        border-radius: 12px !important;
    }

    .contactos-count {
        color: var(--text-muted);
        font-size: 0.92rem;
        margin: -8px 0 16px 2px;
    }

    .contactos-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 20px;
    }

    .contacto-card {
        height: 100%;
        border-radius: 20px;
        overflow: hidden;
        background: var(--surface);
        border: 1px solid var(--border);
        box-shadow: 0 6px 18px rgba(15, 23, 42, 0.06);
        transition: transform 0.25s ease, box-shadow 0.25s ease, border-color 0.25s ease;
        display: flex;
        flex-direction: column;
    }

    .contacto-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 14px 32px rgba(15, 23, 42, 0.12);
        border-color: rgba(102, 126, 234, 0.35);
    }

    .contacto-header {
        padding: 18px;
        display: flex;
        align-items: center;
        gap: 14px;
        border-bottom: 1px solid #f1f5f9;
        min-width: 0;
    }

    .contacto-icon {
        width: 52px;
        height: 52px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        box-shadow: 0 8px 18px rgba(15, 23, 42, 0.12);
    }

    .contacto-icon.emergencia {
        background: linear-gradient(135deg, var(--danger) 0%, var(--danger-dark) 100%);
    }

    .contacto-icon.recomendado {
        background: linear-gradient(135deg, var(--success) 0%, var(--success-dark) 100%);
    }

    .contacto-icon i {
        color: white;
        font-size: 1.25rem;
    }

    .contacto-info {
        min-width: 0;
        flex: 1;
    }

    .contacto-nombre {
        font-size: 1.05rem;
        font-weight: 700;
        color: var(--text-main);
        margin: 0;
        line-height: 1.25;
        overflow-wrap: anywhere;
    }

    .contacto-tipo {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        margin-top: 8px;
        padding: 4px 10px;
        border-radius: 999px;
        font-size: 0.75rem;
        font-weight: 700;
    }

    .contacto-tipo.emergencia {
        background: #fee2e2;
        color: #991b1b;
    }

    .contacto-tipo.recomendado {
        background: #dcfce7;
        color: #166534;
    }

    .contacto-body {
        padding: 18px;
        flex: 1;
    }

    .contacto-numero {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 12px;
        padding: 12px;
        background: var(--soft-bg);
        border-radius: 14px;
    }

    .contacto-numero i {
        color: var(--primary);
        flex-shrink: 0;
    }

    .contacto-numero a {
        color: var(--primary-dark);
        font-weight: 800;
        font-size: 1.05rem;
        text-decoration: none;
        overflow-wrap: anywhere;
    }

    .contacto-numero a:hover {
        text-decoration: underline;
    }

    .contacto-descripcion {
        color: var(--text-muted);
        font-size: 0.92rem;
        line-height: 1.55;
        margin: 0;
    }

    .contacto-footer {
        padding: 16px 18px 18px;
        border-top: 1px solid #f1f5f9;
        display: grid;
        grid-template-columns: 1fr auto;
        gap: 10px;
    }

    .contacto-footer .btn {
        min-height: 42px;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        white-space: nowrap;
    }

    .contacto-footer .btn-copy-only {
        width: 44px;
        padding: 0;
    }

    #contactos-loader {
        min-height: 220px;
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 40px 20px;
    }

    #sin-resultados {
        border-radius: 20px;
        border: 1px solid var(--border);
    }

    .btn.disabled,
    .btn:disabled {
        opacity: 0.55;
        cursor: not-allowed;
        pointer-events: none;
    }

    @media (max-width: 1200px) {
        .contactos-grid {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }
    }

    @media (max-width: 992px) {
        .contactos-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .filters-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 640px) {
        .page-header {
            align-items: flex-start;
        }

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

        .filters-card {
            padding: 16px;
            border-radius: 18px;
            margin-bottom: 18px;
        }

        .filters-header {
            margin-bottom: 14px;
        }

        .filters-title {
            font-size: 1rem;
        }

        .filters-subtitle {
            font-size: 0.85rem;
        }

        .contactos-grid {
            grid-template-columns: 1fr;
            gap: 16px;
        }

        .contacto-card {
            border-radius: 18px;
        }

        .contacto-header {
            padding: 16px;
        }

        .contacto-icon {
            width: 46px;
            height: 46px;
            border-radius: 14px;
        }

        .contacto-body {
            padding: 16px;
        }

        .contacto-footer {
            padding: 14px 16px 16px;
            grid-template-columns: 1fr;
        }

        .contacto-footer .btn,
        .contacto-footer .btn-copy-only {
            width: 100%;
        }

        .contacto-footer .btn-copy-only::after {
            content: "Copiar número";
            margin-left: 8px;
        }
    }

    @media (hover: none) {
        .contacto-card:hover {
            transform: none;
        }
    }
</style>

<script>
    var todosContactos = [];

    function cargarContactos() {
        $('#contactos-loader').show();
        $('#contactos-container').empty();
        $('#sin-resultados').hide();
        $('#contactos-resumen').hide();

        $.ajax({
            url: "{{ route('usuario.contacto.obtenerContactos') }}",
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                $('#contactos-loader').hide();

                const contactos = response.data || [];
                todosContactos = contactos;

                renderizarContactos(contactos);
            },
            error: function() {
                $('#contactos-loader').hide();
                $('#contactos-resumen').hide();

                $('#contactos-container').html(`
                    <div class="card" style="grid-column: 1 / -1; border-radius: 20px;">
                        <div class="empty-state">
                            <div class="empty-state-icon" style="background: #fee2e2;">
                                <i class="warning icon" style="color: #ef4444;"></i>
                            </div>
                            <h3>Error al cargar</h3>
                            <p>No se pudieron cargar los contactos. Intenta de nuevo.</p>
                            <button type="button" class="btn btn-primary btn-sm" onclick="cargarContactos()">
                                <i class="refresh icon"></i>
                                Reintentar
                            </button>
                        </div>
                    </div>
                `);
            }
        });
    }

    function renderizarContactos(contactos) {
        const container = $('#contactos-container');
        container.empty();

        if (contactos.length === 0) {
            $('#contactos-resumen').hide();
            $('#sin-resultados').css('display', 'block');
            return;
        }

        $('#sin-resultados').css('display', 'none');

        $('#contactos-resumen')
            .text(`${contactos.length} contacto${contactos.length === 1 ? '' : 's'} encontrado${contactos.length === 1 ? '' : 's'}`)
            .show();

        contactos.forEach(function(contacto) {
            const nombre = contacto.nombre_contacto || 'Sin nombre';
            const numero = contacto.numero_contacto || '';
            const descripcion = contacto.descripcion || '';
            const paginaWeb = contacto.pagina_web || '';
            const tipoContacto = contacto.tipo_contacto || 'recomendado';

            const isEmergencia = tipoContacto === 'emergencia';
            const tipoClass = isEmergencia ? 'emergencia' : 'recomendado';
            const tipoText = isEmergencia ? 'Emergencia' : 'Recomendado';
            const tipoIcon = isEmergencia ? 'warning circle' : 'star';

            const cardHtml = `
                <div
                    class="contacto-card contacto-item"
                    data-nombre="${escapeHtml(nombre).toLowerCase()}"
                    data-numero="${escapeHtml(numero).toLowerCase()}"
                    data-descripcion="${escapeHtml(descripcion).toLowerCase()}"
                    data-tipo="${escapeHtml(tipoContacto)}"
                >
                    <div class="contacto-header">
                        <div class="contacto-icon ${tipoClass}">
                            <i class="${tipoIcon} icon"></i>
                        </div>

                        <div class="contacto-info">
                            <h3 class="contacto-nombre">${escapeHtml(nombre)}</h3>
                            <span class="contacto-tipo ${tipoClass}">
                                <i class="${tipoIcon} icon"></i>
                                ${tipoText}
                            </span>
                        </div>
                    </div>

                    <div class="contacto-body">
                        <div class="contacto-numero">
                            <i class="phone icon"></i>
                            ${numero
                                ? `<a href="tel:${escapeAttribute(numero)}">${escapeHtml(numero)}</a>`
                                : `<span style="color:#94a3b8;">Sin número registrado</span>`
                            }
                        </div>

                        ${descripcion
                            ? `<p class="contacto-descripcion">${escapeHtml(descripcion)}</p>`
                            : `<p class="contacto-descripcion">Sin descripción disponible.</p>`
                        }
                    </div>

                    <div class="contacto-footer">
                        ${paginaWeb ? `
                            <a href="${escapeAttribute(paginaWeb)}" target="_blank" rel="noopener noreferrer" class="btn btn-secondary btn-sm">
                                <i class="external link icon"></i>
                                Visitar sitio
                            </a>
                        ` : `
                            <button class="btn btn-secondary btn-sm disabled" type="button" disabled>
                                <i class="globe icon"></i>
                                Sin sitio web
                            </button>
                        `}

                        <button
                            onclick="copiarNumero('${escapeJs(numero)}')"
                            class="btn btn-secondary btn-sm btn-copy-only"
                            type="button"
                            ${numero ? '' : 'disabled'}
                            title="Copiar número"
                        >
                            <i class="copy icon"></i>
                        </button>
                    </div>
                </div>
            `;

            container.append(cardHtml);
        });
    }

    function filtrarContactos() {
        const busqueda = $('#buscar-contacto').val().trim().toLowerCase();
        const tipoFiltro = $('#filtro-tipo').val();

        const filtrados = todosContactos.filter(function(contacto) {
            const nombre = (contacto.nombre_contacto || '').toLowerCase();
            const numero = (contacto.numero_contacto || '').toLowerCase();
            const descripcion = (contacto.descripcion || '').toLowerCase();
            const tipo = contacto.tipo_contacto || '';

            const textoMatch =
                nombre.includes(busqueda) ||
                numero.includes(busqueda) ||
                descripcion.includes(busqueda);

            const tipoMatch = tipoFiltro === 'todos' || tipo === tipoFiltro;

            return textoMatch && tipoMatch;
        });

        renderizarContactos(filtrados);
    }

    function copiarNumero(numero) {
        if (!numero) {
            alertify.error('Este contacto no tiene número registrado');
            return;
        }

        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(numero).then(function() {
                alertify.success('Número copiado al portapapeles');
            }, function() {
                copiarNumeroFallback(numero);
            });
        } else {
            copiarNumeroFallback(numero);
        }
    }

    function copiarNumeroFallback(numero) {
        const inputTemporal = document.createElement('input');
        inputTemporal.value = numero;
        document.body.appendChild(inputTemporal);
        inputTemporal.select();

        try {
            document.execCommand('copy');
            alertify.success('Número copiado al portapapeles');
        } catch (e) {
            alertify.error('Error al copiar el número');
        }

        document.body.removeChild(inputTemporal);
    }

    function escapeHtml(text) {
        return String(text)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function escapeAttribute(text) {
        return escapeHtml(text).replace(/`/g, '&#096;');
    }

    function escapeJs(text) {
        return String(text)
            .replace(/\\/g, '\\\\')
            .replace(/'/g, "\\'")
            .replace(/"/g, '\\"')
            .replace(/\n/g, '\\n')
            .replace(/\r/g, '\\r');
    }

    $(document).ready(function() {
        $('#filtro-tipo').dropdown();
        cargarContactos();
    });
</script>