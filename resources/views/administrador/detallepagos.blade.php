<x-app-layout>
    <div class="detalle-pagos-page">
        <div class="detalle-pagos-hero">
            <div class="detalle-pagos-hero-content">
                <div class="detalle-pagos-hero-left">
                    <a href="{{ route('admin.pago.nuevoPago') }}" class="btn btn-secondary btn-back-detalle">
                        <i class="arrow left icon"></i>
                    </a>

                    <div class="detalle-pagos-hero-icon">
                        <i class="money bill alternate icon"></i>
                    </div>

                    <div>
                        <h1 class="detalle-pagos-title">Detalle de Pagos</h1>
                        <p class="detalle-pagos-subtitle">
                            {{ $info->concepto }} - ${{ number_format($info->cantidad, 2) }} por usuario
                        </p>
                    </div>
                </div>

                <div class="detalle-pagos-hero-acciones">
                    {{-- Reporte de este concepto: quién pagó, cuánto entró
                         realmente a la cuenta y qué falta por cobrar. --}}
                    <a href="{{ route('admin.pago.exportarConcepto', $id_pago) }}"
                       class="btn-exportar-concepto"
                       title="Descarga un CSV con el detalle de este concepto y los totales de lo que entró a la cuenta. Se abre en Excel.">
                        <i class="file excel outline icon"></i>
                        Descargar reporte
                    </a>

                    <div class="detalle-pagos-hero-pill">
                        <i class="users icon"></i>
                        Validación de pagos
                    </div>
                </div>
            </div>
        </div>

        <div class="detalle-summary-card">
            <div class="detalle-summary-grid">
                <div class="detalle-summary-item">
                    <div class="detalle-summary-icon concepto">
                        <i class="sticky note icon"></i>
                    </div>

                    <div>
                        <span>Concepto</span>
                        <strong>{{ $info->concepto }}</strong>
                    </div>
                </div>

                <div class="detalle-summary-item">
                    <div class="detalle-summary-icon monto">
                        <i class="dollar sign icon"></i>
                    </div>

                    <div>
                        <span>Monto por usuario</span>
                        <strong>${{ number_format($info->cantidad, 2) }}</strong>
                    </div>
                </div>

                <div class="detalle-summary-item">
                    <div class="detalle-summary-icon fecha">
                        <i class="calendar icon"></i>
                    </div>

                    <div>
                        <span>Fecha de vencimiento</span>
                        <strong>{{ \Carbon\Carbon::parse($info->vencimiento)->translatedFormat('j \d\e F \d\e Y') }}</strong>
                    </div>
                </div>
            </div>
        </div>

        <div class="detalle-list-card">
            <div class="detalle-card-header">
                <div class="detalle-card-title-wrap">
                    <div class="detalle-card-icon">
                        <i class="list icon"></i>
                    </div>

                    <div>
                        <h3 class="detalle-card-title">Detalle de Pagos por Usuario</h3>
                        <p class="detalle-card-subtitle">Consulta comprobantes, estados y validaciones por usuario.</p>
                    </div>
                </div>

                <button class="btn btn-primary btn-add-user" id="btn-agregar-usuario">
                    <i class="user plus icon"></i>
                    Agregar usuario
                </button>
            </div>

            <div class="detalle-toolbar">
                <div class="filter-field">
                    <label class="form-label">Buscar usuario</label>
                    <div class="ui icon input detalle-search-input">
                        <i class="search icon"></i>
                        <input
                            type="text"
                            id="buscar-detalle-pago"
                            placeholder="Usuario, estado, monto, tarde o comprobante..."
                            autocomplete="off"
                        >
                    </div>
                </div>

                <div class="filter-field">
                    <label class="form-label">Estado</label>
                    <select id="filtro-estado-detalle" class="ui fluid dropdown detalle-input">
                        <option value="todos">Todos</option>
                        <option value="pendiente">Pendiente</option>
                        <option value="pagado">Pagado</option>
                        <option value="rechazado">Rechazado</option>
                    </select>
                </div>

                <div class="filter-field">
                    <label class="form-label">Comprobante</label>
                    <select id="filtro-comprobante-detalle" class="ui fluid dropdown detalle-input">
                        <option value="todos">Todos</option>
                        <option value="con">Con comprobante</option>
                        <option value="sin">Sin comprobante</option>
                    </select>
                </div>

                <div class="filter-field">
                    <label class="form-label">Tiempo de pago</label>
                    <select id="filtro-tiempo-detalle" class="ui fluid dropdown detalle-input">
                        <option value="todos">Todos</option>
                        <option value="atiempo">A tiempo</option>
                        <option value="tarde">Tarde</option>
                    </select>
                </div>

                <button type="button" class="btn btn-secondary btn-clear-filters" onclick="limpiarFiltrosDetallePagos()">
                    <i class="times icon"></i>
                    Limpiar
                </button>
            </div>

            <div class="detalle-count-summary">
                <div class="summary-item">
                    <div class="summary-icon">
                        <i class="users icon"></i>
                    </div>

                    <div>
                        <span>Registros encontrados</span>
                        <strong id="total-detalle-pagos">0</strong>
                    </div>
                </div>
            </div>

            <div id="detalle-pagos-loader" class="detalle-pagos-loader">
                <div class="ui active centered inline text loader large">Cargando pagos...</div>
            </div>

            <div id="detalle-pagos-container" class="detalle-pagos-grid"></div>

            <div id="detalle-pagos-pagination" class="detalle-pagos-pagination" style="display: none;"></div>

            <div id="sin-detalle-pagos" class="empty-detalle-card" style="display: none;">
                <div class="empty-detalle-icon">
                    <i class="users icon"></i>
                </div>

                <h3>No hay pagos asignados</h3>
                <p>Aún no hay usuarios asignados a este concepto de pago.</p>
            </div>

            <div id="sin-resultados" class="empty-detalle-card" style="display: none;">
                <div class="empty-detalle-icon">
                    <i class="search icon"></i>
                </div>

                <h3>No se encontraron resultados</h3>
                <p>No hay registros que coincidan con tu búsqueda o filtros.</p>

                <button type="button" class="btn btn-secondary btn-sm" onclick="limpiarFiltrosDetallePagos()">
                    <i class="undo icon"></i>
                    Limpiar filtros
                </button>
            </div>

            <div id="detalle-pagos-error" class="empty-detalle-card error" style="display: none;">
                <div class="empty-detalle-icon error">
                    <i class="warning sign icon"></i>
                </div>

                <h3>Error al cargar pagos</h3>
                <p>No se pudieron cargar los detalles del pago. Intenta nuevamente.</p>

                <button type="button" class="btn btn-primary btn-sm" onclick="cargarDetallePagos()">
                    <i class="refresh icon"></i>
                    Reintentar
                </button>
            </div>
        </div>
    </div>
</x-app-layout>

<div class="ui right sidebar detalle-drawer" id="drawer-evidencia">
    <div class="drawer-header">
        <span>
            <i class="file image icon"></i>
            Documento cargado
        </span>

        <button class="ui icon button drawer-close" type="button">
            <i class="times icon"></i>
        </button>
    </div>

    <div class="drawer-body">
        <img id="imagen-evidencia" src="" alt="Evidencia">
    </div>
</div>

<div id="drawer-dimmer"></div>

<div class="ui modal detalle-edit-modal" id="modal-editar">
    <div class="header">
        <i class="edit icon"></i>
        Editar validación
    </div>

    <div class="content">
        <form id="form-editar">
            @csrf

            <input type="hidden" name="id" id="edit-id">
            <input type="hidden" name="cantidad_pago_original" id="edit-cantidad-original">

            <div class="form-group">
                <label class="form-label">Estado actual del pago *</label>
                <select name="estado" id="edit-estado" class="form-input detalle-input" required>
                    <option value="">Selecciona una opción</option>
                    <option value="pendiente">Pendiente</option>
                    <option value="rechazado">Rechazado</option>
                    <option value="pagado">Pagado</option>
                </select>
            </div>

            <div class="form-group" id="campo-cantidad-pago" style="display: none;">
                <label class="form-label">Monto pagado *</label>

                <div class="input-icon-wrapper">
                    <i class="dollar sign icon"></i>
                    <input
                        type="number"
                        name="cantidad_pago"
                        id="edit-cantidad-pago"
                        class="form-input detalle-input input-with-icon"
                        min="0"
                        step="0.01"
                        placeholder="Ej. 1500.00"
                    >
                </div>

                <p class="form-help-text">
                    <i class="info circle icon"></i>
                    El usuario no subió comprobante, ingresa el monto con el que se registrará el pago.
                </p>
            </div>

            <div class="form-group" id="campo-comentario-rechazo" style="display: none;">
                <label class="form-label">Motivo del rechazo *</label>

                <textarea
                    name="comentario_rechazo"
                    id="edit-comentario-rechazo"
                    class="form-input detalle-input"
                    rows="3"
                    maxlength="500"
                    placeholder="Ej. Registraste que pagaste a tiempo y la transferencia es del 25. Requerimos el pago con recargo."
                ></textarea>

                <p class="form-help-text">
                    <i class="info circle icon"></i>
                    El vecino recibe este texto por correo y en su notificación,
                    y lo ve en su pantalla al volver a subir el comprobante.
                    Sé concreto sobre qué debe corregir.
                </p>
            </div>

            <div class="form-group">
                <label class="form-label">Fecha real del pago</label>

                <div class="input-icon-wrapper">
                    <i class="calendar alternate outline icon"></i>
                    <input
                        type="date"
                        name="fecha_pago"
                        id="edit-fecha-pago"
                        class="form-input detalle-input input-with-icon"
                        max="{{ now()->format('Y-m-d') }}"
                    >
                </div>

                <p class="form-help-text">
                    <i class="info circle icon"></i>
                    Es la fecha en que el vecino hizo el movimiento, no la de hoy.
                    De ella depende si el pago cuenta como puntual o tardío.
                    Déjala vacía para conservar la que ya estaba registrada.
                </p>
            </div>
        </form>
    </div>

    <div class="actions detalle-modal-actions">
        <div class="ui deny button btn-modal-cancel">Cancelar</div>

        <button id="btn-guardar-cambios" type="submit" form="form-editar" class="btn btn-primary">
            <i class="check icon"></i>
            Guardar cambios
        </button>
    </div>
</div>

<div class="ui modal detalle-add-modal" id="modal-agregar-usuario">
    <div class="header">
        <i class="user plus icon"></i>
        Agregar usuario al concepto
    </div>

    <div class="content">
        <form id="form-agregar-usuario">
            @csrf

            <input type="hidden" name="pago_id" value="{{ $id_pago }}">

            <div class="form-group">
                <label class="form-label">Seleccionar usuario *</label>

                <select name="user_id" id="select-usuario" class="ui fluid search selection dropdown detalle-input" required>
                    <option value="">— Selecciona un usuario —</option>

                    @foreach($usuariosDisponibles as $usuario)
                        <option value="{{ $usuario->id }}">
                            {{ $usuario->nombre }} (Casa #{{ $usuario->casa }})
                        </option>
                    @endforeach
                </select>

                @if($usuariosDisponibles->isEmpty())
                    <p class="form-help-text">
                        <i class="info circle icon"></i>
                        Todos los usuarios ya tienen asignado este concepto.
                    </p>
                @endif
            </div>
        </form>
    </div>

    <div class="actions detalle-modal-actions">
        <div class="ui deny button btn-modal-cancel">Cancelar</div>

        <button
            id="btn-guardar-usuario"
            type="submit"
            form="form-agregar-usuario"
            class="btn btn-primary"
            @if($usuariosDisponibles->isEmpty()) disabled @endif
        >
            <i class="check icon"></i>
            Agregar
        </button>
    </div>
</div>

<style>
    :root {
        --detalle-primary: #3b82f6;
        --detalle-primary-dark: #2563eb;
        --detalle-secondary: #667eea;
        --detalle-success: #10b981;
        --detalle-danger: #ef4444;
        --detalle-warning: #f59e0b;
        --detalle-text-main: #0f172a;
        --detalle-text-muted: #64748b;
        --detalle-text-soft: #94a3b8;
        --detalle-border: #e2e8f0;
        --detalle-surface: #ffffff;
        --detalle-soft-bg: #f8fafc;
    }

    .detalle-pagos-page {
        width: 100%;
        padding-bottom: 24px;
    }

    .detalle-pagos-hero {
        background:
            radial-gradient(circle at top right, rgba(255,255,255,0.24), transparent 34%),
            radial-gradient(circle at bottom left, rgba(255,255,255,0.14), transparent 30%),
            linear-gradient(135deg, var(--detalle-primary) 0%, var(--detalle-primary-dark) 100%);
        border-radius: 26px;
        padding: 26px;
        color: white;
        margin-bottom: 22px;
        box-shadow: 0 12px 30px rgba(37, 99, 235, 0.18);
        overflow: hidden;
    }

    .detalle-pagos-hero-content {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 18px;
        flex-wrap: wrap;
    }

    .detalle-pagos-hero-left {
        display: flex;
        align-items: center;
        gap: 16px;
        min-width: 0;
    }

    .btn-back-detalle {
        width: 44px;
        min-width: 44px;
        height: 44px;
        border-radius: 14px !important;
        display: inline-flex !important;
        align-items: center;
        justify-content: center;
        background: rgba(255,255,255,0.16) !important;
        color: white !important;
        border: 1px solid rgba(255,255,255,0.22) !important;
    }

    .btn-back-detalle i {
        margin: 0 !important;
    }

    .detalle-pagos-hero-icon {
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

    .detalle-pagos-hero-icon i {
        color: white;
        font-size: 1.8rem;
        margin: 0 !important;
    }

    .detalle-pagos-title {
        margin: 0 0 6px 0;
        font-size: clamp(1.55rem, 3vw, 2.15rem);
        font-weight: 900;
        letter-spacing: -0.035em;
        line-height: 1.08;
    }

    .detalle-pagos-subtitle {
        margin: 0;
        opacity: 0.92;
        font-size: 1rem;
        line-height: 1.45;
        overflow-wrap: anywhere;
    }

    /* El hero lleva el boton de reporte junto a la etiqueta de validacion. */
    .detalle-pagos-hero-acciones {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }

    .btn-exportar-concepto {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 14px;
        border-radius: 999px;
        background: rgba(255,255,255,0.92);
        border: 1px solid rgba(255,255,255,0.35);
        color: #047857;
        font-size: 0.86rem;
        font-weight: 800;
        text-decoration: none;
        white-space: nowrap;
        transition: background .15s ease;
    }

    .btn-exportar-concepto:hover {
        background: #ffffff;
        color: #065f46;
    }

    .btn-exportar-concepto i {
        margin: 0 !important;
    }

    .detalle-pagos-hero-pill {
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

    .detalle-pagos-hero-pill i {
        margin: 0 !important;
    }

    .detalle-summary-card,
    .detalle-list-card,
    .empty-detalle-card {
        background: var(--detalle-surface);
        border: 1px solid var(--detalle-border);
        border-radius: 22px;
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.06);
        overflow: hidden;
    }

    .detalle-summary-card {
        margin-bottom: 24px;
        padding: 20px;
    }

    .detalle-summary-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 16px;
    }

    .detalle-summary-item {
        display: flex;
        align-items: center;
        gap: 12px;
        background: var(--detalle-soft-bg);
        border: 1px solid #eef2f7;
        border-radius: 18px;
        padding: 14px;
        min-width: 0;
    }

    .detalle-summary-icon {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .detalle-summary-icon.concepto {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    .detalle-summary-icon.monto {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    }

    .detalle-summary-icon.fecha {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    }

    .detalle-summary-icon i {
        margin: 0 !important;
    }

    .detalle-summary-item span {
        display: block;
        color: var(--detalle-text-muted);
        font-size: 0.78rem;
        font-weight: 800;
        margin-bottom: 3px;
    }

    .detalle-summary-item strong {
        display: block;
        color: var(--detalle-text-main);
        font-size: 0.95rem;
        line-height: 1.35;
        overflow-wrap: anywhere;
    }

    .detalle-card-header {
        padding: 18px 20px;
        border-bottom: 1px solid #f1f5f9;
        background: linear-gradient(180deg, #ffffff 0%, #fbfdff 100%);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
    }

    .detalle-card-title-wrap {
        display: flex;
        align-items: center;
        gap: 14px;
        min-width: 0;
    }

    .detalle-card-icon {
        width: 52px;
        height: 52px;
        border-radius: 16px;
        background: linear-gradient(135deg, var(--detalle-primary) 0%, var(--detalle-primary-dark) 100%);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        box-shadow: 0 8px 18px rgba(59, 130, 246, 0.24);
    }

    .detalle-card-icon i {
        margin: 0 !important;
        font-size: 1.25rem;
    }

    .detalle-card-title {
        margin: 0;
        color: var(--detalle-text-main);
        font-size: 1.12rem;
        font-weight: 900;
        line-height: 1.25;
    }

    .detalle-card-subtitle {
        margin: 4px 0 0 0;
        color: var(--detalle-text-muted);
        font-size: 0.9rem;
        line-height: 1.35;
    }

    .btn-add-user {
        min-height: 44px;
        border-radius: 14px;
        display: inline-flex !important;
        align-items: center;
        justify-content: center;
        gap: 8px;
        white-space: nowrap;
    }

    .btn-add-user i {
        margin: 0 !important;
    }

    .detalle-toolbar {
        padding: 18px 20px;
        display: grid;
        grid-template-columns: minmax(0, 1fr) minmax(150px, 190px) minmax(170px, 210px) minmax(160px, 210px) auto;
        gap: 14px;
        align-items: end;
        border-bottom: 1px solid #f1f5f9;
    }

    .filter-field {
        min-width: 0;
    }

    .form-group {
        margin-bottom: 18px;
    }

    .form-label {
        display: block;
        color: var(--detalle-text-main);
        font-weight: 800;
        margin-bottom: 8px;
        font-size: 0.9rem;
    }

    .detalle-input {
        min-height: 46px;
        border-radius: 14px !important;
        width: 100%;
        max-width: 100%;
        box-sizing: border-box;
        border: 1px solid var(--detalle-border) !important;
        transition: border-color 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
    }

    .detalle-input:focus {
        border-color: rgba(59, 130, 246, 0.65) !important;
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.12) !important;
        outline: none;
        background: white;
    }

    .detalle-search-input {
        width: 100%;
    }

    .detalle-search-input input {
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

    .input-icon-wrapper {
        position: relative;
        display: flex;
        align-items: center;
        width: 100%;
    }

    .input-icon-wrapper > i {
        position: absolute;
        left: 14px;
        color: var(--detalle-primary-dark);
        pointer-events: none;
        z-index: 1;
        margin: 0 !important;
    }

    .input-with-icon {
        padding-left: 42px !important;
    }

    .form-help-text {
        display: block;
        color: var(--detalle-text-soft);
        font-size: 0.82rem;
        margin-top: 8px;
        line-height: 1.45;
    }

    .form-help-text i {
        margin-right: 4px !important;
    }

    .detalle-count-summary {
        padding: 16px 20px 0;
    }

    .summary-item {
        display: inline-flex;
        align-items: center;
        gap: 12px;
        background: var(--detalle-soft-bg);
        border: 1px solid #eef2f7;
        border-radius: 16px;
        padding: 12px 14px;
    }

    .summary-icon {
        width: 42px;
        height: 42px;
        border-radius: 14px;
        background: #dbeafe;
        color: var(--detalle-primary-dark);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .summary-icon i {
        margin: 0 !important;
    }

    .summary-item span {
        display: block;
        color: var(--detalle-text-muted);
        font-size: 0.78rem;
        font-weight: 800;
        margin-bottom: 2px;
    }

    .summary-item strong {
        display: block;
        color: var(--detalle-text-main);
        font-size: 1.2rem;
        line-height: 1;
    }

    .detalle-pagos-loader {
        min-height: 220px;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 40px 20px;
    }

    .detalle-pagos-grid {
        padding: 20px;
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 16px;
    }

    .detalle-pago-card {
        border: 1px solid var(--detalle-border);
        border-radius: 18px;
        background: white;
        box-shadow: 0 6px 18px rgba(15, 23, 42, 0.05);
        overflow: hidden;
        display: flex;
        flex-direction: column;
        transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
    }

    .detalle-pago-card.tarde-card {
        border-color: rgba(245, 158, 11, 0.35);
        box-shadow: 0 8px 22px rgba(245, 158, 11, 0.08);
    }

    .detalle-pago-card:hover {
        transform: translateY(-2px);
        border-color: rgba(59, 130, 246, 0.35);
        box-shadow: 0 10px 24px rgba(15, 23, 42, 0.08);
    }

    .detalle-pago-card.tarde-card:hover {
        border-color: rgba(245, 158, 11, 0.5);
    }

    .detalle-pago-header {
        padding: 16px;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: flex-start;
        gap: 12px;
    }

    .detalle-pago-icon {
        width: 46px;
        height: 46px;
        border-radius: 14px;
        background: #dbeafe;
        color: var(--detalle-primary-dark);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .detalle-pago-card.tarde-card .detalle-pago-icon {
        background: #fef3c7;
        color: #b45309;
    }

    .detalle-pago-icon i {
        margin: 0 !important;
        font-size: 1.15rem;
    }

    .detalle-pago-title {
        margin: 0;
        color: var(--detalle-text-main);
        font-size: 1rem;
        font-weight: 900;
        line-height: 1.3;
        overflow-wrap: anywhere;
    }

    .detalle-pago-badges {
        display: flex;
        flex-wrap: wrap;
        gap: 7px;
        margin-top: 9px;
    }

    .detalle-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 6px 9px;
        border-radius: 999px;
        font-size: 0.72rem;
        font-weight: 900;
        line-height: 1;
    }

    .detalle-badge i {
        margin: 0 !important;
    }

    .detalle-badge.pendiente {
        background: #fef3c7;
        color: #92400e;
    }

    .detalle-badge.pagado {
        background: #dcfce7;
        color: #166534;
    }

    .detalle-badge.rechazado {
        background: #fee2e2;
        color: #991b1b;
    }

    .detalle-badge.comprobante {
        background: #dbeafe;
        color: #1d4ed8;
    }

    .detalle-badge.sin-comprobante {
        background: #f1f5f9;
        color: #475569;
    }

    .detalle-badge.tarde {
        background: #ffedd5;
        color: #c2410c;
        border: 1px solid rgba(249, 115, 22, 0.18);
    }

    .detalle-badge.atiempo {
        background: #ecfdf5;
        color: #047857;
        border: 1px solid rgba(16, 185, 129, 0.18);
    }

    .detalle-pago-body {
        padding: 16px;
        flex: 1;
        display: grid;
        gap: 10px;
    }

    .detalle-meta-item {
        display: flex;
        align-items: flex-start;
        gap: 9px;
        color: var(--detalle-text-muted);
        font-size: 0.88rem;
        line-height: 1.4;
        overflow-wrap: anywhere;
    }

    .detalle-meta-item i {
        color: var(--detalle-primary-dark);
        margin: 2px 0 0 0 !important;
        flex-shrink: 0;
    }

    .detalle-pago-card.tarde-card .detalle-meta-item i {
        color: #b45309;
    }

    .detalle-pago-footer {
        padding: 14px 16px 16px;
        border-top: 1px solid #f1f5f9;
        background: #fbfdff;
    }

    .detalle-actions {
        width: 100%;
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 10px;
    }

    .detalle-actions .btn,
    .detalle-actions button,
    .detalle-actions a {
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

    .detalle-actions i {
        margin: 0 !important;
        flex-shrink: 0;
    }

    .detalle-actions .btn-ver-evidencia {
        background: #f8fafc !important;
        color: #475569 !important;
        border: 1px solid #cbd5e1 !important;
    }

    .detalle-actions .btn-editar {
        background: #eff6ff !important;
        color: #1d4ed8 !important;
        border: 1px solid #bfdbfe !important;
    }

    .detalle-actions .btn-eliminar {
        background: #fef2f2 !important;
        color: #b91c1c !important;
        border: 1px solid #fecaca !important;
    }

    .detalle-actions .btn-ver-evidencia:hover {
        background: #e2e8f0 !important;
    }

    .detalle-actions .btn-editar:hover {
        background: #dbeafe !important;
    }

    .detalle-actions .btn-eliminar:hover {
        background: #fee2e2 !important;
    }

    .detalle-pagos-pagination {
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
        border: 1px solid var(--detalle-border);
        background: #ffffff;
        color: var(--detalle-text-muted);
        border-radius: 12px;
        font-weight: 900;
        cursor: pointer;
        transition: background 0.2s ease, color 0.2s ease, border-color 0.2s ease;
    }

    .pagination-btn:hover {
        background: #eff6ff;
        color: var(--detalle-primary-dark);
        border-color: rgba(59, 130, 246, 0.35);
    }

    .pagination-btn.active {
        background: linear-gradient(135deg, var(--detalle-primary) 0%, var(--detalle-primary-dark) 100%);
        color: #ffffff;
        border-color: transparent;
        box-shadow: 0 8px 18px rgba(59, 130, 246, 0.22);
    }

    .pagination-btn:disabled {
        opacity: 0.45;
        cursor: not-allowed;
        background: var(--detalle-soft-bg);
        color: var(--detalle-text-soft);
    }

    .pagination-info {
        color: var(--detalle-text-muted);
        font-size: 0.86rem;
        font-weight: 800;
        margin: 0 8px;
    }

    .empty-detalle-card {
        margin: 20px;
        padding: 44px 20px;
        text-align: center;
    }

    .empty-detalle-icon {
        width: 78px;
        height: 78px;
        border-radius: 24px;
        margin: 0 auto 18px;
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.12), rgba(37, 99, 235, 0.16));
        color: var(--detalle-primary-dark);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .empty-detalle-icon.error {
        background: #fee2e2;
        color: var(--detalle-danger);
    }

    .empty-detalle-icon i {
        font-size: 2rem;
        margin: 0 !important;
    }

    .empty-detalle-card h3 {
        margin: 0 0 8px 0;
        color: #475569;
        font-weight: 900;
    }

    .empty-detalle-card p {
        margin: 0 0 18px 0;
        color: var(--detalle-text-soft);
        line-height: 1.5;
    }

    .ui.right.sidebar#drawer-evidencia {
        width: 520px !important;
        background: #ffffff;
        border-left: 1px solid rgba(148,163,184,0.22);
        box-shadow: -8px 0 30px rgba(15,23,42,0.1);
        overflow-y: auto;
        z-index: 1002;
    }

    #drawer-dimmer {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(15,23,42,0.45);
        z-index: 1001;
        cursor: pointer;
    }

    .drawer-header {
        padding: 20px 24px;
        font-size: 1.05rem;
        font-weight: 900;
        position: relative;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        background: linear-gradient(135deg, var(--detalle-secondary) 0%, var(--detalle-primary-dark) 100%);
        color: white;
    }

    .drawer-header i {
        margin-right: 8px !important;
    }

    .drawer-close {
        background: rgba(255,255,255,0.14) !important;
        color: white !important;
        border-radius: 12px !important;
        margin: 0 !important;
    }

    .drawer-close i {
        margin: 0 !important;
    }

    .drawer-body {
        padding: 24px;
        min-height: calc(100vh - 72px);
        display: flex;
        align-items: flex-start;
        justify-content: center;
        background: var(--detalle-soft-bg);
    }

    #imagen-evidencia {
        max-width: 100%;
        border-radius: 14px;
        box-shadow: 0 8px 24px rgba(15,23,42,0.18);
        object-fit: contain;
    }

    .detalle-edit-modal,
    .detalle-add-modal {
        border-radius: 20px !important;
        overflow: hidden;
    }

    .detalle-edit-modal > .header {
        background: linear-gradient(135deg, var(--detalle-secondary) 0%, var(--detalle-primary-dark) 100%) !important;
        color: white !important;
        font-weight: 900 !important;
    }

    .detalle-add-modal > .header {
        background: linear-gradient(135deg, var(--detalle-success) 0%, #059669 100%) !important;
        color: white !important;
        font-weight: 900 !important;
    }

    .detalle-edit-modal > .header i,
    .detalle-add-modal > .header i {
        margin-right: 8px !important;
    }

    .detalle-edit-modal > .content,
    .detalle-add-modal > .content {
        padding: 20px !important;
    }

    .detalle-modal-actions {
        display: flex !important;
        justify-content: flex-end;
        gap: 10px;
        flex-wrap: wrap;
        padding: 16px 20px !important;
        background: var(--detalle-soft-bg) !important;
        border-top: 1px solid #f1f5f9 !important;
    }

    .detalle-modal-actions .button,
    .detalle-modal-actions .btn {
        min-height: 42px;
        border-radius: 12px !important;
        display: inline-flex !important;
        align-items: center;
        justify-content: center;
        gap: 7px;
    }

    @media (max-width: 1280px) {
        .detalle-toolbar {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .btn-clear-filters {
            grid-column: 1 / -1;
            width: 100%;
        }
    }

    @media (max-width: 1200px) {
        .detalle-summary-grid {
            grid-template-columns: 1fr;
        }

        .detalle-pagos-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 900px) {
        .detalle-toolbar {
            grid-template-columns: 1fr;
        }

        .btn-clear-filters,
        .btn-add-user {
            width: 100%;
        }

        .detalle-card-header {
            align-items: stretch;
        }
    }

    @media (max-width: 768px) {
        .detalle-pagos-hero {
            border-radius: 0 0 24px 24px;
            margin: -8px -4px 18px;
            padding: 22px;
        }

        .detalle-pagos-hero-content {
            align-items: stretch;
        }

        .detalle-pagos-hero-left {
            align-items: flex-start;
            flex-wrap: wrap;
        }

        .detalle-pagos-hero-icon {
            width: 54px;
            height: 54px;
            border-radius: 18px;
        }

        .detalle-pagos-title {
            font-size: 1.55rem;
        }

        .detalle-pagos-subtitle {
            font-size: 0.92rem;
        }

        .detalle-pagos-hero-pill {
            width: 100%;
            justify-content: center;
            border-radius: 16px;
        }

        .detalle-summary-card,
        .detalle-list-card,
        .empty-detalle-card {
            border-radius: 18px;
        }

        .detalle-summary-card {
            padding: 16px;
        }

        .detalle-card-header,
        .detalle-toolbar {
            padding: 16px;
        }

        .detalle-card-icon {
            width: 46px;
            height: 46px;
            border-radius: 14px;
        }

        .detalle-card-title {
            font-size: 1.04rem;
        }

        .detalle-card-subtitle {
            font-size: 0.85rem;
        }

        .detalle-count-summary {
            padding: 14px 16px 0;
        }

        .detalle-pagos-grid {
            padding: 16px;
        }

        .detalle-pago-card:hover {
            transform: none;
        }

        .detalle-pago-footer {
            padding: 12px;
            background: #ffffff;
        }

        .detalle-actions {
            grid-template-columns: 1fr;
            gap: 8px;
        }

        .detalle-actions .btn,
        .detalle-actions button,
        .detalle-actions a {
            min-height: 48px;
            font-size: 0.9rem;
        }

        .detalle-pagos-pagination {
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
        select {
            font-size: 16px !important;
        }

        .ui.right.sidebar#drawer-evidencia {
            width: 100% !important;
        }

        .drawer-body {
            padding: 16px;
        }

        .detalle-modal-actions {
            padding: 14px !important;
        }

        .detalle-modal-actions .button,
        .detalle-modal-actions .btn {
            flex: 1;
        }
    }

    @media (max-width: 420px) {
        .detalle-pagos-hero-left {
            gap: 12px;
        }

        .detalle-card-title-wrap,
        .detalle-pago-header {
            align-items: flex-start;
        }

        .detalle-summary-item {
            align-items: flex-start;
        }

        .detalle-pago-icon {
            width: 42px;
            height: 42px;
            border-radius: 13px;
        }

        .detalle-pago-title {
            font-size: 0.98rem;
        }

        .detalle-badge {
            max-width: 100%;
            white-space: normal;
            line-height: 1.2;
        }

        .detalle-modal-actions {
            flex-direction: column-reverse;
        }

        .detalle-modal-actions .button,
        .detalle-modal-actions .btn {
            width: 100%;
        }
    }
</style>

<script>
    const BASE_URL = "{{ url('/') }}";
    const id_pago = '{{ $id_pago }}';

    let detallePagosOriginales = [];
    let detallePagosFiltrados = [];
    let paginaActualDetallePagos = 1;
    let detallePagosPorPagina = 6;
    let isSubmittingEstado = false;
    let isSubmittingUsuario = false;

    $(document).ready(function() {
        $('#modal-editar').modal();
        $('#modal-agregar-usuario').modal();

        $('.ui.right.sidebar').sidebar({
            dimPage: false,
            transition: 'overlay',
            mobileTransition: 'overlay'
        });

        $(document).on('click', '.drawer-close, #drawer-dimmer', function() {
            $('#drawer-evidencia').sidebar('hide');
            $('#drawer-dimmer').fadeOut(300);
        });

        $('select[name="user_id"]').dropdown({
            fullTextSearch: true,
            clearable: true,
            placeholder: 'Selecciona un usuario'
        });

        $('#filtro-estado-detalle').dropdown();
        $('#filtro-comprobante-detalle').dropdown();
        $('#filtro-tiempo-detalle').dropdown();

        cargarDetallePagos();

        $('#buscar-detalle-pago').on('keyup input change', function() {
            filtrarDetallePagos();
        });

        $('#filtro-estado-detalle').on('change', function() {
            filtrarDetallePagos();
        });

        $('#filtro-comprobante-detalle').on('change', function() {
            filtrarDetallePagos();
        });

        $('#filtro-tiempo-detalle').on('change', function() {
            filtrarDetallePagos();
        });
    });

    function cargarDetallePagos() {
        $('#detalle-pagos-loader').show();
        $('#detalle-pagos-container').empty();
        $('#detalle-pagos-pagination').hide().empty();
        $('#sin-detalle-pagos').hide();
        $('#sin-resultados').hide();
        $('#detalle-pagos-error').hide();

        $.ajax({
            url: "{{ route('admin.pago.obtenerDetallepagos') }}",
            method: 'GET',
            dataType: 'json',
            data: {
                id: id_pago,
                start: 0,
                length: 1000
            },
            success: function(response) {
                $('#detalle-pagos-loader').hide();

                detallePagosOriginales = response.data || [];
                renderizarDetallePagos(detallePagosOriginales);
            },
            error: function() {
                $('#detalle-pagos-loader').hide();
                $('#detalle-pagos-error').show();
            }
        });
    }

    function renderizarDetallePagos(detalles) {
        detallePagosFiltrados = detalles;
        paginaActualDetallePagos = 1;
        renderizarPaginaDetallePagos();
    }

    function renderizarPaginaDetallePagos() {
        const container = $('#detalle-pagos-container');
        const pagination = $('#detalle-pagos-pagination');

        container.empty();
        pagination.empty();

        $('#total-detalle-pagos').text(detallePagosFiltrados.length);

        $('#sin-detalle-pagos').hide();
        $('#sin-resultados').hide();
        $('#detalle-pagos-error').hide();

        if (detallePagosOriginales.length === 0) {
            pagination.hide();
            $('#sin-detalle-pagos').show();
            return;
        }

        if (detallePagosFiltrados.length === 0) {
            pagination.hide();
            $('#sin-resultados').show();
            return;
        }

        const totalPaginas = Math.ceil(detallePagosFiltrados.length / detallePagosPorPagina);
        const inicio = (paginaActualDetallePagos - 1) * detallePagosPorPagina;
        const fin = inicio + detallePagosPorPagina;
        const detallesPagina = detallePagosFiltrados.slice(inicio, fin);

        detallesPagina.forEach(function(detalle) {
            const id = extraerTexto(detalle.id || '');
            const usuario = extraerTexto(detalle.usuario || 'Usuario no disponible');
            const pagoHtml = detalle.pago || '';
            const cantidadPago = extraerTexto(detalle.cantidad_pago || 'Sin pago registrado');
            const estadoTexto = extraerTexto(detalle.estado || 'Pendiente');
            const estado = obtenerEstadoDetallePago(estadoTexto);
            const evidenciaSrc = obtenerSrcEvidencia(pagoHtml);
            const tieneComprobante = Boolean(evidenciaSrc);
            const esTarde = obtenerPagoTarde(detalle);
            const fechaPagoTexto = extraerTexto(detalle.fecha_pago || '') || 'Sin registrar';
            const saldoFavor = parseFloat(detalle.saldo_favor || 0) || 0;

            const evidenciaButton = tieneComprobante
                ? `
                    <button type="button" class="btn btn-secondary btn-sm btn-ver-evidencia" data-img="${escapeHtml(evidenciaSrc)}">
                        <i class="image icon"></i>
                        Comprobante
                    </button>
                `
                : `
                    <button type="button" class="btn btn-secondary btn-sm disabled" disabled>
                        <i class="image outline icon"></i>
                        Sin comprobante
                    </button>
                `;

            const badgeTarde = esTarde
                ? `
                    <span class="detalle-badge tarde" title="Pago realizado después del vencimiento">
                        <i class="clock icon"></i>
                        Tarde
                    </span>
                `
                : `
                    <span class="detalle-badge atiempo" title="Pago dentro del periodo">
                        <i class="check icon"></i>
                        A tiempo
                    </span>
                `;

            const accionesHtml = `
                ${evidenciaButton}

                <button
                    type="button"
                    class="btn btn-secondary btn-sm btn-editar"
                    data-id="${escapeHtml(id)}"
                    data-estado="${escapeHtml(estado.valor)}"
                    data-cantidad-pago="${escapeHtml(extraerNumero(cantidadPago))}"
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
                <div class="detalle-pago-card ${esTarde ? 'tarde-card' : ''}">
                    <div class="detalle-pago-header">
                        <div class="detalle-pago-icon">
                            <i class="${esTarde ? 'clock' : 'user'} icon"></i>
                        </div>

                        <div style="min-width: 0; flex: 1;">
                            <h3 class="detalle-pago-title">${escapeHtml(usuario)}</h3>

                            <div class="detalle-pago-badges">
                                <span class="detalle-badge ${estado.clase}">
                                    <i class="${estado.icono} icon"></i>
                                    ${escapeHtml(estado.texto)}
                                </span>

                                <span class="detalle-badge ${tieneComprobante ? 'comprobante' : 'sin-comprobante'}">
                                    <i class="${tieneComprobante ? 'file image' : 'file outline'} icon"></i>
                                    ${tieneComprobante ? 'Con comprobante' : 'Sin comprobante'}
                                </span>

                                ${badgeTarde}
                            </div>
                        </div>
                    </div>

                    <div class="detalle-pago-body">
                        <div class="detalle-meta-item">
                            <i class="dollar sign icon"></i>
                            <span>${escapeHtml(cantidadPago)}</span>
                        </div>

                        <div class="detalle-meta-item">
                            <i class="calendar alternate outline icon"></i>
                            <span>Pagado: ${escapeHtml(fechaPagoTexto)}</span>
                        </div>

                        ${saldoFavor > 0 ? `
                            <div class="detalle-meta-item" title="Dinero que este vecino pagó de más y está disponible para cubrir recibos.">
                                <i class="piggy bank icon"></i>
                                <span>Saldo a favor: <strong>$${saldoFavor.toLocaleString('es-MX', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</strong></span>
                            </div>
                        ` : ''}

                        ${esTarde ? `
                            <div class="detalle-meta-item">
                                <i class="clock icon"></i>
                                <span>El pago se realizó después de la fecha de vencimiento.</span>
                            </div>
                        ` : ''}
                    </div>

                    <div class="detalle-pago-footer">
                        <div class="detalle-actions">
                            ${accionesHtml}
                        </div>
                    </div>
                </div>
            `;

            container.append(card);
        });

        renderizarPaginacionDetallePagos(totalPaginas, detallePagosFiltrados.length);
    }

    function obtenerPagoTarde(detalle) {
        /*
         * El backend ahora envía la puntualidad ya calculada contra la fecha
         * real del pago. Se usa ese dato cuando viene; el parseo de HTML de
         * abajo queda solo como respaldo para respuestas antiguas en caché.
         */
        if (typeof detalle.es_tarde === 'boolean') {
            return detalle.es_tarde;
        }

        const pagoHtml = String(detalle.pago || '');
        const estadoHtml = String(detalle.estado || '');
        const textoPago = normalizarTexto(extraerTexto(pagoHtml));
        const textoEstado = normalizarTexto(extraerTexto(estadoHtml));
        const htmlCompleto = normalizarTexto(pagoHtml + ' ' + estadoHtml);

        return (
            textoPago.includes('tarde') ||
            textoEstado.includes('tarde') ||
            htmlCompleto.includes('pago realizado despues del vencimiento') ||
            htmlCompleto.includes('pago vencido') ||
            htmlCompleto.includes('clock icon') ||
            htmlCompleto.includes('ui orange mini label')
        );
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

    function obtenerEstadoDetallePago(valor) {
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

    function renderizarPaginacionDetallePagos(totalPaginas, totalDetalles) {
        const pagination = $('#detalle-pagos-pagination');

        pagination.empty();

        if (totalPaginas <= 1) {
            pagination.hide();
            return;
        }

        const inicio = ((paginaActualDetallePagos - 1) * detallePagosPorPagina) + 1;
        const fin = Math.min(paginaActualDetallePagos * detallePagosPorPagina, totalDetalles);

        pagination.show();

        pagination.append(`
            <button
                type="button"
                class="pagination-btn"
                onclick="cambiarPaginaDetallePagos(${paginaActualDetallePagos - 1})"
                ${paginaActualDetallePagos === 1 ? 'disabled' : ''}
            >
                <i class="chevron left icon"></i>
            </button>
        `);

        const paginas = obtenerPaginasVisiblesDetallePagos(totalPaginas);

        paginas.forEach(function(pagina) {
            if (pagina === '...') {
                pagination.append(`<span class="pagination-info">...</span>`);
                return;
            }

            pagination.append(`
                <button
                    type="button"
                    class="pagination-btn ${pagina === paginaActualDetallePagos ? 'active' : ''}"
                    onclick="cambiarPaginaDetallePagos(${pagina})"
                >
                    ${pagina}
                </button>
            `);
        });

        pagination.append(`
            <button
                type="button"
                class="pagination-btn"
                onclick="cambiarPaginaDetallePagos(${paginaActualDetallePagos + 1})"
                ${paginaActualDetallePagos === totalPaginas ? 'disabled' : ''}
            >
                <i class="chevron right icon"></i>
            </button>
        `);

        pagination.append(`
            <div class="pagination-info">
                Mostrando ${inicio}-${fin} de ${totalDetalles}
            </div>
        `);
    }

    function cambiarPaginaDetallePagos(pagina) {
        const totalPaginas = Math.ceil(detallePagosFiltrados.length / detallePagosPorPagina);

        if (pagina < 1 || pagina > totalPaginas) {
            return;
        }

        paginaActualDetallePagos = pagina;
        renderizarPaginaDetallePagos();

        document.querySelector('.detalle-list-card')?.scrollIntoView({
            behavior: 'smooth',
            block: 'start'
        });
    }

    function obtenerPaginasVisiblesDetallePagos(totalPaginas) {
        const paginas = [];

        if (totalPaginas <= 5) {
            for (let i = 1; i <= totalPaginas; i++) {
                paginas.push(i);
            }

            return paginas;
        }

        paginas.push(1);

        if (paginaActualDetallePagos > 3) {
            paginas.push('...');
        }

        const inicio = Math.max(2, paginaActualDetallePagos - 1);
        const fin = Math.min(totalPaginas - 1, paginaActualDetallePagos + 1);

        for (let i = inicio; i <= fin; i++) {
            paginas.push(i);
        }

        if (paginaActualDetallePagos < totalPaginas - 2) {
            paginas.push('...');
        }

        paginas.push(totalPaginas);

        return paginas;
    }

    function filtrarDetallePagos() {
        const busqueda = normalizarTexto($('#buscar-detalle-pago').val());
        const estadoFiltro = $('#filtro-estado-detalle').val();
        const comprobanteFiltro = $('#filtro-comprobante-detalle').val();
        const tiempoFiltro = $('#filtro-tiempo-detalle').val();

        const filtrados = detallePagosOriginales.filter(function(detalle) {
            const usuario = extraerTexto(detalle.usuario || '');
            const pago = detalle.pago || '';
            const pagoTexto = extraerTexto(pago || '');
            const cantidadPago = extraerTexto(detalle.cantidad_pago || '');
            const estadoTexto = extraerTexto(detalle.estado || '');
            const estado = obtenerEstadoDetallePago(estadoTexto);
            const tieneComprobante = Boolean(obtenerSrcEvidencia(pago));
            const esTarde = obtenerPagoTarde(detalle);

            const textoCompleto = normalizarTexto(
                usuario + ' ' +
                pagoTexto + ' ' +
                cantidadPago + ' ' +
                extraerTexto(detalle.fecha_pago || '') + ' ' +
                estadoTexto + ' ' +
                estado.texto + ' ' +
                (tieneComprobante ? 'con comprobante' : 'sin comprobante') + ' ' +
                (esTarde ? 'tarde pago tardio vencido despues del vencimiento' : 'a tiempo')
            );

            const coincideBusqueda = textoCompleto.includes(busqueda);
            const coincideEstado = estadoFiltro === 'todos' || estado.valor === estadoFiltro;

            let coincideComprobante = true;

            if (comprobanteFiltro === 'con') {
                coincideComprobante = tieneComprobante;
            }

            if (comprobanteFiltro === 'sin') {
                coincideComprobante = !tieneComprobante;
            }

            let coincideTiempo = true;

            if (tiempoFiltro === 'tarde') {
                coincideTiempo = esTarde;
            }

            if (tiempoFiltro === 'atiempo') {
                coincideTiempo = !esTarde;
            }

            return coincideBusqueda && coincideEstado && coincideComprobante && coincideTiempo;
        });

        renderizarDetallePagos(filtrados);
    }

    function limpiarFiltrosDetallePagos() {
        $('#buscar-detalle-pago').val('');
        $('#filtro-estado-detalle').dropdown('set selected', 'todos');
        $('#filtro-comprobante-detalle').dropdown('set selected', 'todos');
        $('#filtro-tiempo-detalle').dropdown('set selected', 'todos');
        renderizarDetallePagos(detallePagosOriginales);
    }

    $(document).on('click', '.btn-ver-evidencia', function() {
        const src = $(this).data('img');

        $('#imagen-evidencia').attr('src', src);
        $('#drawer-evidencia').sidebar('show');
        $('#drawer-dimmer').fadeIn(300);
    });

    $(document).on('click', '.btn-editar', function() {
        const $btn = $(this);
        const cantidadPago = $btn.data('cantidad-pago');

        $('#edit-id').val($btn.data('id'));
        $('#edit-estado').val($btn.data('estado'));
        $('#edit-cantidad-original').val(cantidadPago);
        $('#edit-cantidad-pago').val('');
        $('#campo-cantidad-pago').hide();
        $('#edit-cantidad-pago').prop('required', false);
        $('#edit-fecha-pago').val($btn.data('fecha-pago') || '');
        $('#edit-comentario-rechazo').val($btn.data('comentario-rechazo') || '');

        // Deja los campos coherentes con el estado que ya trae el recibo,
        // sin esperar a que el tesorero toque el selector.
        alternarCamposEstado();

        $('#modal-editar').modal('show');
    });

    function alternarCamposEstado() {
        const estado = $('#edit-estado').val();
        const cantidadOriginal = $('#edit-cantidad-original').val();

        if (estado === 'pagado' && !cantidadOriginal) {
            $('#campo-cantidad-pago').show();
            $('#edit-cantidad-pago').prop('required', true);
        } else {
            $('#campo-cantidad-pago').hide();
            $('#edit-cantidad-pago').prop('required', false);
        }

        // El motivo solo aplica al rechazar, y ahí es obligatorio: sin él
        // el vecino no sabe qué corregir antes de volver a subir.
        if (estado === 'rechazado') {
            $('#campo-comentario-rechazo').show();
            $('#edit-comentario-rechazo').prop('required', true);
        } else {
            $('#campo-comentario-rechazo').hide();
            $('#edit-comentario-rechazo').prop('required', false);
        }
    }

    $('#edit-estado').on('change', alternarCamposEstado);

    $('#form-editar').submit(function(e) {
        e.preventDefault();

        if (isSubmittingEstado) {
            return false;
        }

        isSubmittingEstado = true;
        mostrarLoaderPantalla();

        const id = $('#edit-id').val();
        const data = $(this).serialize();

        setTimeout(function() {
            $.ajax({
                url: `${BASE_URL}/administrador/pago/actualizar-pago/${id}`,
                method: 'POST',
                data: data,
                success: function(res) {
                    alertify.alert(res.header, res.message, function() {
                        $('#modal-editar').modal('hide');
                        cargarDetallePagos();
                    });
                },
                error: function(xhr) {
                    // Un 422 es un error de captura (por ejemplo, rechazar sin
                    // escribir el motivo): conviene decir qué falta en vez de
                    // un mensaje genérico.
                    const res = xhr.responseJSON;

                    if (xhr.status === 422 && res && res.message) {
                        alertify.alert(res.header || 'Faltan datos', res.message);
                        return;
                    }

                    alertify.error('Error al actualizar');
                },
                complete: function() {
                    ocultarLoaderPantalla();
                    isSubmittingEstado = false;
                }
            });
        }, 100);
    });

    $(document).on('click', '#btn-agregar-usuario', function() {
        $('#modal-agregar-usuario').modal('show');
    });

    $('#form-agregar-usuario').submit(function(e) {
        e.preventDefault();

        if (isSubmittingUsuario) {
            return false;
        }

        const $btn = $('#btn-guardar-usuario');
        const data = $(this).serialize();

        isSubmittingUsuario = true;
        $btn.addClass('loading disabled').prop('disabled', true);

        $.ajax({
            url: `${BASE_URL}/administrador/pago/agregar-usuario`,
            method: 'POST',
            data: data,
            success: function(res) {
                alertify.alert(res.header, res.message, function() {
                    $('#modal-agregar-usuario').modal('hide');
                    cargarDetallePagos();
                });
            },
            error: function() {
                alertify.error('Error al agregar usuario');
            },
            complete: function() {
                $btn.removeClass('loading disabled').prop('disabled', false);
                isSubmittingUsuario = false;
            }
        });
    });

    $(document).on('click', '.btn-eliminar', function() {
        const id = $(this).data('id');

        alertify.confirm(
            '¿Eliminar pago?',
            '¿Estás seguro de que quieres eliminar este pago? El usuario será notificado.',
            function() {
                mostrarLoaderPantalla();

                setTimeout(function() {
                    $.ajax({
                        url: `${BASE_URL}/administrador/pago/eliminar-pago/${id}`,
                        method: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            alertify.alert(response.header, response.message, function() {
                                cargarDetallePagos();
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