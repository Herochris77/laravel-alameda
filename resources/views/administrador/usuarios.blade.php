<x-app-layout>
    <div class="usuarios-page">
        <div class="usuarios-hero">
            <div class="usuarios-hero-content">
                <div class="usuarios-hero-left">
                    <div class="usuarios-hero-icon">
                        <i class="users icon"></i>
                    </div>

                    <div>
                        <h1 class="usuarios-title">Usuarios del Condominio</h1>
                        <p class="usuarios-subtitle">Administra las cuentas de todos los vecinos</p>
                    </div>
                </div>

                <div class="usuarios-hero-pill">
                    <i class="user shield icon"></i>
                    Administración de usuarios
                </div>
            </div>
        </div>

        <div class="usuarios-toolbar-card">
            <div class="usuarios-toolbar-header">
                <div class="usuarios-toolbar-title-wrap">
                    <div class="usuarios-toolbar-icon">
                        <i class="filter icon"></i>
                    </div>

                    <div>
                        <h3 class="usuarios-card-title">Filtros de búsqueda</h3>
                        <p class="usuarios-card-subtitle">Busca, filtra y ordena los usuarios registrados.</p>
                    </div>
                </div>

                <button type="button" class="btn btn-secondary btn-clear-filters" id="btn-limpiar-filtros">
                    <i class="eraser icon"></i>
                    Limpiar filtros
                </button>
            </div>

            <div class="usuarios-filter-grid">
                <div class="filter-field filter-field-wide">
                    <label class="form-label">Buscar en todo</label>
                    <div class="ui icon input usuarios-search-input">
                        <i class="search icon"></i>
                        <input type="text" id="filtro-general" placeholder="Nombre, correo, celular, casa, estado...">
                    </div>
                </div>

                <div class="filter-field">
                    <label class="form-label">Casa</label>
                    <div class="ui icon input usuarios-search-input">
                        <i class="home icon"></i>
                        <input type="text" id="filtro-casa" placeholder="Ej. 12">
                    </div>
                </div>

                <div class="filter-field">
                    <label class="form-label">Tipo</label>
                    <div class="ui icon input usuarios-search-input">
                        <i class="tag icon"></i>
                        <input type="text" id="filtro-tipo" placeholder="Dueño, inquilino...">
                    </div>
                </div>

                <div class="filter-field">
                    <label class="form-label">Nombre</label>
                    <div class="ui icon input usuarios-search-input">
                        <i class="user icon"></i>
                        <input type="text" id="filtro-nombre" placeholder="Nombre">
                    </div>
                </div>

                <div class="filter-field">
                    <label class="form-label">Correo</label>
                    <div class="ui icon input usuarios-search-input">
                        <i class="mail icon"></i>
                        <input type="text" id="filtro-correo" placeholder="correo@dominio.com">
                    </div>
                </div>

                <div class="filter-field">
                    <label class="form-label">Celular</label>
                    <div class="ui icon input usuarios-search-input">
                        <i class="phone icon"></i>
                        <input type="text" id="filtro-celular" placeholder="Celular">
                    </div>
                </div>

                {{--
                    Filtro del aviso de privacidad: sirve para perseguir a
                    quienes faltan por aceptarlo, que es la lista que importa
                    mientras se junta el consentimiento de todos.
                --}}
                <div class="filter-field">
                    <label class="form-label">Aviso de privacidad</label>
                    <select id="filtro-aviso" class="form-input">
                        <option value="">Todos</option>
                        <option value="no">Falta que acepten</option>
                        <option value="si">Ya aceptaron</option>
                        <option value="viejo">Aceptaron una versión anterior</option>
                    </select>
                </div>
            </div>

            <div class="usuarios-actions-toolbar">
                <button type="button" class="btn btn-primary btn-toolbar-action" id="btn-ordenar-casa-asc">
                    <i class="sort numeric down icon"></i>
                    Casa menor a mayor
                </button>

                <button type="button" class="btn btn-secondary btn-toolbar-action" id="btn-ordenar-casa-desc">
                    <i class="sort numeric up icon"></i>
                    Casa mayor a menor
                </button>

                <button type="button" class="btn btn-secondary btn-toolbar-action" id="btn-recargar-tabla">
                    <i class="sync icon"></i>
                    Recargar
                </button>
            </div>
        </div>

        <div class="usuarios-list-card">
            <div class="usuarios-list-header">
                <div class="usuarios-toolbar-title-wrap">
                    <div class="usuarios-toolbar-icon secondary">
                        <i class="id card icon"></i>
                    </div>

                    <div>
                        <h3 class="usuarios-card-title">Listado de usuarios</h3>
                        <p class="usuarios-card-subtitle">Consulta datos, permisos, pagos y estado de acceso.</p>
                    </div>
                </div>
            </div>

            <div class="usuarios-summary">
                <div class="summary-item">
                    <div class="summary-icon">
                        <i class="users icon"></i>
                    </div>

                    <div>
                        <span>Usuarios encontrados</span>
                        <strong id="total-usuarios">0</strong>
                    </div>
                </div>
            </div>

            <div id="usuarios-loader" class="usuarios-loader">
                <div class="ui active centered inline text loader large">Cargando usuarios...</div>
            </div>

            <div id="usuarios-container" class="usuarios-grid"></div>

            <div id="usuarios-pagination" class="usuarios-pagination" style="display: none;"></div>

            <div id="sin-usuarios" class="empty-usuarios-card" style="display: none;">
                <div class="empty-usuarios-icon">
                    <i class="users icon"></i>
                </div>

                <h3>No hay usuarios registrados</h3>
                <p>Aún no existen usuarios disponibles para mostrar.</p>
            </div>

            <div id="sin-resultados" class="empty-usuarios-card" style="display: none;">
                <div class="empty-usuarios-icon">
                    <i class="search icon"></i>
                </div>

                <h3>No se encontraron resultados</h3>
                <p>No hay usuarios que coincidan con los filtros aplicados.</p>

                <button type="button" class="btn btn-secondary btn-sm" onclick="limpiarFiltrosUsuarios()">
                    <i class="undo icon"></i>
                    Limpiar filtros
                </button>
            </div>

            <div id="usuarios-error" class="empty-usuarios-card error" style="display: none;">
                <div class="empty-usuarios-icon error">
                    <i class="warning sign icon"></i>
                </div>

                <h3>Error al cargar usuarios</h3>
                <p>No se pudieron cargar los usuarios. Intenta nuevamente.</p>

                <button type="button" class="btn btn-primary btn-sm" onclick="cargarUsuarios()">
                    <i class="refresh icon"></i>
                    Reintentar
                </button>
            </div>
        </div>
    </div>

    <style>
        :root {
            --usuarios-primary: #2563eb;
            --usuarios-primary-dark: #1d4ed8;
            --usuarios-secondary: #667eea;
            --usuarios-success: #10b981;
            --usuarios-danger: #ef4444;
            --usuarios-warning: #f59e0b;
            --usuarios-text-main: #0f172a;
            --usuarios-text-muted: #64748b;
            --usuarios-text-soft: #94a3b8;
            --usuarios-border: #e2e8f0;
            --usuarios-surface: #ffffff;
            --usuarios-soft-bg: #f8fafc;
        }

        .usuarios-page {
            width: 100%;
            padding-bottom: 24px;
        }

        .usuarios-hero {
            background:
                radial-gradient(circle at top right, rgba(255,255,255,0.24), transparent 34%),
                radial-gradient(circle at bottom left, rgba(255,255,255,0.14), transparent 30%),
                linear-gradient(135deg, var(--usuarios-primary) 0%, var(--usuarios-primary-dark) 100%);
            border-radius: 26px;
            padding: 26px;
            color: white;
            margin-bottom: 22px;
            box-shadow: 0 12px 30px rgba(37, 99, 235, 0.18);
            overflow: hidden;
        }

        .usuarios-hero-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 18px;
            flex-wrap: wrap;
        }

        .usuarios-hero-left {
            display: flex;
            align-items: center;
            gap: 16px;
            min-width: 0;
        }

        .usuarios-hero-icon {
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

        .usuarios-hero-icon i {
            color: white;
            font-size: 1.8rem;
            margin: 0 !important;
        }

        .usuarios-title {
            margin: 0 0 6px 0;
            font-size: clamp(1.55rem, 3vw, 2.15rem);
            font-weight: 900;
            letter-spacing: -0.035em;
            line-height: 1.08;
        }

        .usuarios-subtitle {
            margin: 0;
            opacity: 0.92;
            font-size: 1rem;
            line-height: 1.45;
        }

        .usuarios-hero-pill {
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

        .usuarios-hero-pill i {
            margin: 0 !important;
        }

        .usuarios-toolbar-card,
        .usuarios-list-card,
        .empty-usuarios-card {
            background: var(--usuarios-surface);
            border: 1px solid var(--usuarios-border);
            border-radius: 22px;
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.06);
            overflow: hidden;
        }

        .usuarios-toolbar-card {
            margin-bottom: 22px;
            padding: 20px;
        }

        .usuarios-toolbar-header,
        .usuarios-list-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
        }

        .usuarios-list-header {
            padding: 18px 20px;
            border-bottom: 1px solid #f1f5f9;
            background: linear-gradient(180deg, #ffffff 0%, #fbfdff 100%);
        }

        .usuarios-toolbar-title-wrap {
            display: flex;
            align-items: center;
            gap: 14px;
            min-width: 0;
        }

        .usuarios-toolbar-icon {
            width: 52px;
            height: 52px;
            border-radius: 16px;
            background: linear-gradient(135deg, var(--usuarios-primary) 0%, var(--usuarios-primary-dark) 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            box-shadow: 0 8px 18px rgba(59, 130, 246, 0.24);
        }

        .usuarios-toolbar-icon.secondary {
            background: linear-gradient(135deg, var(--usuarios-secondary) 0%, var(--usuarios-primary-dark) 100%);
        }

        .usuarios-toolbar-icon i {
            margin: 0 !important;
            font-size: 1.25rem;
        }

        .usuarios-card-title {
            margin: 0;
            color: var(--usuarios-text-main);
            font-size: 1.12rem;
            font-weight: 900;
            line-height: 1.25;
        }

        .usuarios-card-subtitle {
            margin: 4px 0 0 0;
            color: var(--usuarios-text-muted);
            font-size: 0.9rem;
            line-height: 1.35;
        }

        .usuarios-filter-grid {
            display: grid;
            grid-template-columns: minmax(0, 1.4fr) repeat(3, minmax(160px, 1fr));
            gap: 14px;
            align-items: end;
            margin-top: 18px;
        }

        .filter-field {
            min-width: 0;
        }

        .filter-field-wide {
            grid-column: span 2;
        }

        .form-label {
            display: block;
            color: var(--usuarios-text-main);
            font-weight: 800;
            margin-bottom: 8px;
            font-size: 0.9rem;
        }

        .usuarios-search-input {
            width: 100%;
        }

        .usuarios-search-input input {
            min-height: 46px;
            border-radius: 14px !important;
        }

        .usuarios-actions-toolbar {
            margin-top: 18px;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .btn-toolbar-action,
        .btn-clear-filters {
            min-height: 44px;
            border-radius: 14px;
            display: inline-flex !important;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-weight: 900;
        }

        .btn-toolbar-action i,
        .btn-clear-filters i {
            margin: 0 !important;
        }

        .usuarios-summary {
            padding: 16px 20px 0;
        }

        .summary-item {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            background: var(--usuarios-soft-bg);
            border: 1px solid #eef2f7;
            border-radius: 16px;
            padding: 12px 14px;
        }

        .summary-icon {
            width: 42px;
            height: 42px;
            border-radius: 14px;
            background: #dbeafe;
            color: var(--usuarios-primary-dark);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .summary-icon i {
            margin: 0 !important;
        }

        .summary-item span {
            display: block;
            color: var(--usuarios-text-muted);
            font-size: 0.78rem;
            font-weight: 800;
            margin-bottom: 2px;
        }

        .summary-item strong {
            display: block;
            color: var(--usuarios-text-main);
            font-size: 1.2rem;
            line-height: 1;
        }

        .usuarios-loader {
            min-height: 220px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
        }

        .usuarios-grid {
            padding: 20px;
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 18px;
            align-items: stretch;
        }

        .usuario-item-card {
            border: 1px solid var(--usuarios-border);
            border-radius: 20px;
            background: white;
            box-shadow: 0 6px 18px rgba(15, 23, 42, 0.05);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
        }

        .usuario-item-card:hover {
            transform: translateY(-2px);
            border-color: rgba(37, 99, 235, 0.35);
            box-shadow: 0 10px 24px rgba(15, 23, 42, 0.08);
        }

        .usuario-item-header {
            padding: 16px;
            border-bottom: 1px solid #f1f5f9;
            display: flex;
            align-items: flex-start;
            gap: 12px;
        }

        .usuario-avatar {
            width: 48px;
            height: 48px;
            border-radius: 16px;
            background: #dbeafe;
            color: var(--usuarios-primary-dark);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-weight: 900;
            font-size: 1rem;
        }

        .usuario-avatar i {
            margin: 0 !important;
        }

        .usuario-item-title {
            margin: 0;
            color: var(--usuarios-text-main);
            font-size: 1rem;
            font-weight: 900;
            line-height: 1.3;
            overflow-wrap: anywhere;
        }

        .usuario-item-subtitle {
            margin-top: 4px;
            color: var(--usuarios-text-muted);
            font-size: 0.84rem;
            line-height: 1.35;
            overflow-wrap: anywhere;
        }

        .usuario-badges {
            display: flex;
            flex-wrap: wrap;
            gap: 7px;
            margin-top: 9px;
        }

        .usuario-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 6px 9px;
            border-radius: 999px;
            font-size: 0.72rem;
            font-weight: 900;
            line-height: 1;
        }

        .usuario-badge i {
            margin: 0 !important;
        }

        .usuario-badge.casa {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .usuario-badge.tipo {
            background: #f1f5f9;
            color: #475569;
        }

        /* Aviso de privacidad: verde aceptado, ámbar pendiente,
           morado si aceptó una versión que ya cambió. */
        .usuario-badge.aviso-si {
            background: #ecfdf5;
            color: #047857;
        }

        .usuario-badge.aviso-no {
            background: #fffbeb;
            color: #b45309;
        }

        .usuario-badge.aviso-viejo {
            background: #f5f3ff;
            color: #6d28d9;
        }

        .usuario-badge.activo {
            background: #dcfce7;
            color: #166534;
        }

        .usuario-badge.bloqueado {
            background: #fee2e2;
            color: #991b1b;
        }

        .usuario-body {
            padding: 16px;
            flex: 1;
            display: grid;
            gap: 10px;
        }

        .usuario-meta-item {
            display: flex;
            align-items: flex-start;
            gap: 9px;
            color: var(--usuarios-text-muted);
            font-size: 0.88rem;
            line-height: 1.4;
            overflow-wrap: anywhere;
        }

        .usuario-meta-item i {
            color: var(--usuarios-primary-dark);
            margin: 2px 0 0 0 !important;
            flex-shrink: 0;
        }

        .usuario-meta-item strong {
            color: var(--usuarios-text-main);
        }

        .usuario-html-labels {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            align-items: center;
        }

        .usuario-html-labels .ui.label,
        .usuario-html-labels .label {
            border-radius: 999px !important;
            font-weight: 900 !important;
            margin: 0 !important;
        }

        .usuario-footer {
            padding: 14px;
            border-top: 1px solid #f1f5f9;
            background: linear-gradient(180deg, #ffffff 0%, #fbfdff 100%);
        }

        .usuario-actions {
            width: 100%;
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 8px;
        }

        .usuario-actions .ui.buttons,
        .usuario-actions .ui.tiny.buttons {
            display: contents !important;
        }

        .usuario-actions .btn,
        .usuario-actions button,
        .usuario-actions a,
        .usuario-actions .ui.button {
            width: 100% !important;
            max-width: 100% !important;
            min-width: 0 !important;
            min-height: 42px;
            margin: 0 !important;
            padding: 9px 10px !important;
            border-radius: 13px !important;
            box-sizing: border-box;
            display: inline-flex !important;
            align-items: center;
            justify-content: center;
            gap: 7px;
            font-size: 0.78rem;
            font-weight: 900;
            line-height: 1.1;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            text-align: center;
            box-shadow: none !important;
        }

        .usuario-actions i {
            margin: 0 !important;
            flex-shrink: 0;
            font-size: 0.95rem;
        }

        .usuario-actions .btn-block {
            background: #fffbeb !important;
            color: #92400e !important;
            border: 1px solid #fde68a !important;
        }

        .usuario-actions .btn-block:hover {
            background: #fef3c7 !important;
        }

        .usuario-actions .btn-unblock {
            background: #ecfdf5 !important;
            color: #166534 !important;
            border: 1px solid #bbf7d0 !important;
        }

        .usuario-actions .btn-unblock:hover {
            background: #dcfce7 !important;
        }

        .usuario-actions .btn-eliminar {
            background: #fef2f2 !important;
            color: #b91c1c !important;
            border: 1px solid #fecaca !important;
        }

        .usuario-actions .btn-eliminar:hover {
            background: #fee2e2 !important;
        }

        .usuario-actions .btn-cargo {
            background: #f5f3ff;
            border: 1px solid #ddd6fe;
            color: #6d28d9;
        }

        .usuario-actions .btn-cargo:hover {
            background: #ede9fe;
            color: #5b21b6;
        }

        .usuario-actions .btn-admin,
        .usuario-actions .btn-user,
        .usuario-actions .btn-enviar-verificacion {
            background: #eff6ff !important;
            color: #1d4ed8 !important;
            border: 1px solid #bfdbfe !important;
        }

        .usuario-actions .btn-admin:hover,
        .usuario-actions .btn-user:hover,
        .usuario-actions .btn-enviar-verificacion:hover {
            background: #dbeafe !important;
        }

        .usuario-actions .btn-regresar-pago {
            background: #f0fdfa !important;
            color: #0f766e !important;
            border: 1px solid #99f6e4 !important;
        }

        .usuario-actions .btn-regresar-pago:hover {
            background: #ccfbf1 !important;
        }

        .usuarios-pagination {
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
            border: 1px solid var(--usuarios-border);
            background: #ffffff;
            color: var(--usuarios-text-muted);
            border-radius: 12px;
            font-weight: 900;
            cursor: pointer;
            transition: background 0.2s ease, color 0.2s ease, border-color 0.2s ease;
        }

        .pagination-btn:hover {
            background: #eff6ff;
            color: var(--usuarios-primary-dark);
            border-color: rgba(59, 130, 246, 0.35);
        }

        .pagination-btn.active {
            background: linear-gradient(135deg, var(--usuarios-primary) 0%, var(--usuarios-primary-dark) 100%);
            color: #ffffff;
            border-color: transparent;
            box-shadow: 0 8px 18px rgba(59, 130, 246, 0.22);
        }

        .pagination-btn:disabled {
            opacity: 0.45;
            cursor: not-allowed;
            background: var(--usuarios-soft-bg);
            color: var(--usuarios-text-soft);
        }

        .pagination-info {
            color: var(--usuarios-text-muted);
            font-size: 0.86rem;
            font-weight: 800;
            margin: 0 8px;
        }

        .empty-usuarios-card {
            margin: 20px;
            padding: 44px 20px;
            text-align: center;
        }

        .empty-usuarios-icon {
            width: 78px;
            height: 78px;
            border-radius: 24px;
            margin: 0 auto 18px;
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.12), rgba(37, 99, 235, 0.16));
            color: var(--usuarios-primary-dark);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .empty-usuarios-icon.error {
            background: #fee2e2;
            color: var(--usuarios-danger);
        }

        .empty-usuarios-icon i {
            font-size: 2rem;
            margin: 0 !important;
        }

        .empty-usuarios-card h3 {
            margin: 0 0 8px 0;
            color: #475569;
            font-weight: 900;
        }

        .empty-usuarios-card p {
            margin: 0 0 18px 0;
            color: var(--usuarios-text-soft);
            line-height: 1.5;
        }

        @media (max-width: 1200px) {
            .usuarios-filter-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .filter-field-wide {
                grid-column: span 2;
            }

            .usuarios-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 900px) {
            .usuarios-filter-grid {
                grid-template-columns: 1fr;
            }

            .filter-field-wide {
                grid-column: auto;
            }

            .btn-clear-filters,
            .btn-toolbar-action {
                width: 100%;
            }

            .usuarios-actions-toolbar {
                display: grid;
                grid-template-columns: 1fr;
            }

            .usuario-actions {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 768px) {
            .usuarios-hero {
                border-radius: 0 0 24px 24px;
                margin: -8px -4px 18px;
                padding: 22px;
            }

            .usuarios-hero-content {
                align-items: stretch;
            }

            .usuarios-hero-left {
                align-items: flex-start;
            }

            .usuarios-hero-icon {
                width: 54px;
                height: 54px;
                border-radius: 18px;
            }

            .usuarios-title {
                font-size: 1.55rem;
            }

            .usuarios-subtitle {
                font-size: 0.92rem;
            }

            .usuarios-hero-pill {
                width: 100%;
                justify-content: center;
                border-radius: 16px;
            }

            .usuarios-toolbar-card,
            .usuarios-list-card,
            .empty-usuarios-card {
                border-radius: 18px;
            }

            .usuarios-toolbar-card {
                padding: 16px;
            }

            .usuarios-list-header {
                padding: 16px;
            }

            .usuarios-toolbar-icon {
                width: 46px;
                height: 46px;
                border-radius: 14px;
            }

            .usuarios-card-title {
                font-size: 1.04rem;
            }

            .usuarios-card-subtitle {
                font-size: 0.85rem;
            }

            .usuarios-summary {
                padding: 14px 16px 0;
            }

            .usuarios-grid {
                padding: 16px;
            }

            .usuario-item-card:hover {
                transform: none;
            }

            .usuario-footer {
                padding: 12px;
                background: #ffffff;
            }

            .usuario-actions {
                grid-template-columns: 1fr;
                gap: 8px;
            }

            .usuario-actions .btn,
            .usuario-actions button,
            .usuario-actions a,
            .usuario-actions .ui.button {
                min-height: 48px;
                font-size: 0.88rem;
                white-space: normal;
            }

            .usuarios-pagination {
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
            select {
                font-size: 16px !important;
            }
        }

        @media (max-width: 420px) {
            .usuarios-hero-left {
                gap: 12px;
            }

            .usuarios-toolbar-title-wrap,
            .usuario-item-header {
                align-items: flex-start;
            }

            .usuario-avatar {
                width: 42px;
                height: 42px;
                border-radius: 13px;
            }

            .usuario-item-title {
                font-size: 0.98rem;
            }

            .usuario-badge {
                max-width: 100%;
                white-space: normal;
                line-height: 1.2;
            }
        }
    </style>

    {{-- Cargo en la mesa directiva provisional --}}
    <div class="ui modal" id="modal-cargo">
        <div class="header">
            <i class="briefcase icon"></i>
            Cargo en la mesa directiva provisional
        </div>

        <div class="content">
            <input type="hidden" id="cargo-user-id">

            <p style="margin-bottom:14px; color:#475569;">
                Asignando cargo a <strong id="cargo-nombre"></strong>.
            </p>

            <div class="form-group">
                <label class="form-label">Cargo</label>
                <select id="cargo-select" class="form-input" style="width:100%;">
                    <option value="">Sin cargo</option>
                    @foreach(\App\Models\User::CARGOS as $valor => $etiqueta)
                        <option value="{{ $valor }}">{{ $etiqueta }}</option>
                    @endforeach
                </select>
            </div>

            <div style="margin-top:14px; padding:12px 14px; background:#f0f9ff;
                        border:1px solid #bae6fd; border-radius:10px;
                        color:#075985; font-size:.88rem; line-height:1.55;">
                <i class="info circle icon"></i>
                Solo el <strong>Tesorero</strong> puede registrar recibos, validar pagos y
                eliminarlos. Los demás cargos consultan la cobranza y descargan el reporte,
                pero no mueven dinero.
                <br><br>
                Mientras <strong>nadie</strong> tenga el cargo de Tesorero, el módulo de pagos
                sigue abierto a toda la mesa directiva provisional, como hasta ahora.
            </div>
        </div>

        <div class="actions">
            <div class="ui black deny button">Cancelar</div>
            <button type="button" class="ui violet button" id="btn-guardar-cargo">
                <i class="check icon"></i>
                Guardar cargo
            </button>
        </div>
    </div>

    <script>
        const BASE_URL = "{{ url('/') }}";

        let usuariosOriginales = [];
        let usuariosFiltrados = [];
        let paginaActualUsuarios = 1;
        let usuariosPorPagina = 10;
        let ordenCasaUsuarios = 'asc';

        $(document).ready(function() {
            cargarUsuarios();

            $('#filtro-general, #filtro-casa, #filtro-tipo, #filtro-nombre, #filtro-correo, #filtro-celular, #filtro-aviso').on('keyup input change', debounce(function() {
                filtrarUsuarios();
            }, 250));

            $('#btn-ordenar-casa-asc').on('click', function() {
                ordenCasaUsuarios = 'asc';
                renderizarUsuarios(ordenarUsuariosPorCasa(usuariosFiltrados, ordenCasaUsuarios));
            });

            $('#btn-ordenar-casa-desc').on('click', function() {
                ordenCasaUsuarios = 'desc';
                renderizarUsuarios(ordenarUsuariosPorCasa(usuariosFiltrados, ordenCasaUsuarios));
            });

            $('#btn-recargar-tabla').on('click', function() {
                cargarUsuarios();
            });

            $('#btn-limpiar-filtros').on('click', function() {
                limpiarFiltrosUsuarios();
            });
        });

        function cargarUsuarios() {
            $('#usuarios-loader').show();
            $('#usuarios-container').empty();
            $('#usuarios-pagination').hide().empty();
            $('#sin-usuarios').hide();
            $('#sin-resultados').hide();
            $('#usuarios-error').hide();

            $.ajax({
                url: "{{ route('admin.usuarios.obtenerUsuarios') }}",
                method: 'GET',
                dataType: 'json',
                data: {
                    start: 0,
                    length: 1000
                },
                success: function(response) {
                    $('#usuarios-loader').hide();

                    usuariosOriginales = ordenarUsuariosPorCasa(response.data || [], ordenCasaUsuarios);
                    renderizarUsuarios(usuariosOriginales);
                },
                error: function() {
                    $('#usuarios-loader').hide();
                    $('#usuarios-error').show();
                }
            });
        }

        function renderizarUsuarios(usuarios) {
            usuariosFiltrados = usuarios;
            paginaActualUsuarios = 1;
            renderizarPaginaUsuarios();
        }

        function renderizarPaginaUsuarios() {
            const container = $('#usuarios-container');
            const pagination = $('#usuarios-pagination');

            container.empty();
            pagination.empty();

            $('#total-usuarios').text(usuariosFiltrados.length);

            $('#sin-usuarios').hide();
            $('#sin-resultados').hide();
            $('#usuarios-error').hide();

            if (usuariosOriginales.length === 0) {
                pagination.hide();
                $('#sin-usuarios').show();
                return;
            }

            if (usuariosFiltrados.length === 0) {
                pagination.hide();
                $('#sin-resultados').show();
                return;
            }

            /*
             * Estado del aviso de privacidad.
             *
             * Es el respaldo del consentimiento expreso: para una auditoría
             * hay que poder decir quién aceptó, cuándo y qué versión. Si el
             * dato no viene (base sin migrar) no se pinta nada.
             */
            function insigniaAviso(aviso) {
                if (!aviso) return '';

                if (!aviso.acepto) {
                    return `<span class="usuario-badge aviso-no" title="Todavía no acepta el Aviso de Privacidad">
                                <i class="hourglass half icon"></i> Aviso pendiente
                            </span>`;
                }

                // Aceptó, pero el aviso cambió de versión desde entonces: su
                // consentimiento no ampara el documento vigente.
                if (!aviso.vigente) {
                    return `<span class="usuario-badge aviso-viejo"
                                  title="Aceptó la versión ${escapeHtml(aviso.version || '')} el ${escapeHtml(aviso.fecha || '')}; el aviso cambió desde entonces">
                                <i class="redo icon"></i> Aviso desactualizado
                            </span>`;
                }

                return `<span class="usuario-badge aviso-si"
                              title="Aceptó el ${escapeHtml(aviso.fecha || '')} · versión ${escapeHtml(aviso.version || '')}">
                            <i class="check circle icon"></i> Aviso aceptado
                        </span>`;
            }

            const totalPaginas = Math.ceil(usuariosFiltrados.length / usuariosPorPagina);
            const inicio = (paginaActualUsuarios - 1) * usuariosPorPagina;
            const fin = inicio + usuariosPorPagina;
            const usuariosPagina = usuariosFiltrados.slice(inicio, fin);

            usuariosPagina.forEach(function(usuario) {
                const id = extraerTexto(usuario.id || obtenerIdDesdeAcciones(usuario.acciones || ''));
                const rol = usuario.rol || '';
                const rolTexto = extraerTexto(rol || '');
                const nombre = extraerTexto(usuario.nombre || 'Usuario sin nombre');
                const correo = extraerTexto(usuario.correo || 'Sin correo');
                const celular = extraerTexto(usuario.celular || 'Sin celular');
                const casa = extraerTexto(usuario.casa || 'Sin casa');
                const tipo = extraerTexto(usuario.tipo || 'Sin tipo');
                const pago = usuario.pago || '';
                const estado = usuario.estado || '';
                const accionesOriginales = usuario.acciones || '';
                const estadoTexto = extraerTexto(estado || '');
                const iniciales = obtenerIniciales(nombre);
                const estadoInfo = obtenerEstadoUsuario(estadoTexto);
                const accionesHtml = construirAccionesUsuario(id, estadoInfo, rolTexto, accionesOriginales, {
                    nombre: nombre,
                    cargo: String(usuario.cargo_actual || ''),
                    puedeTenerCargo: Boolean(usuario.puede_tener_cargo)
                });

                const card = `
                    <div class="usuario-item-card">
                        <div class="usuario-item-header">
                            <div class="usuario-avatar">
                                ${iniciales || '<i class="user icon"></i>'}
                            </div>

                            <div style="min-width: 0; flex: 1;">
                                <h3 class="usuario-item-title">${escapeHtml(nombre)}</h3>
                                <div class="usuario-item-subtitle">${escapeHtml(correo)}</div>

                                <div class="usuario-badges">
                                    <span class="usuario-badge casa">
                                        <i class="home icon"></i>
                                        Casa ${escapeHtml(casa)}
                                    </span>

                                    <span class="usuario-badge tipo">
                                        <i class="tag icon"></i>
                                        ${escapeHtml(tipo)}
                                    </span>

                                    <span class="usuario-badge ${estadoInfo.clase}">
                                        <i class="${estadoInfo.icono} icon"></i>
                                        ${escapeHtml(estadoInfo.texto)}
                                    </span>

                                    ${insigniaAviso(usuario.aviso)}
                                </div>
                            </div>
                        </div>

                        <div class="usuario-body">
                            <div class="usuario-meta-item">
                                <i class="phone icon"></i>
                                <span>${escapeHtml(celular)}</span>
                            </div>

                            <div class="usuario-meta-item">
                                <i class="shield alternate icon"></i>
                                <span><strong>Rol:</strong></span>
                                <span class="usuario-html-labels">${rol || escapeHtml(rolTexto || 'Sin rol')}</span>
                            </div>

                            <div class="usuario-meta-item">
                                <i class="credit card outline icon"></i>
                                <span><strong>Pagos:</strong></span>
                                <span class="usuario-html-labels">${pago || 'Sin información'}</span>
                            </div>

                            <div class="usuario-meta-item">
                                <i class="info circle icon"></i>
                                <span><strong>Estado:</strong></span>
                                <span class="usuario-html-labels">${estado || escapeHtml(estadoInfo.texto)}</span>
                            </div>
                        </div>

                        <div class="usuario-footer">
                            <div class="usuario-actions">
                                ${accionesHtml}
                            </div>
                        </div>
                    </div>
                `;

                container.append(card);
            });

            renderizarPaginacionUsuarios(totalPaginas, usuariosFiltrados.length);
        }

        // Etiquetas legibles de los cargos, en el mismo orden que User::CARGOS.
        const CARGOS = @json(\App\Models\User::CARGOS);

        function nombreDeCargo(valor) {
            return CARGOS[valor] || valor;
        }

        function construirAccionesUsuario(id, estadoInfo, rolTexto, accionesOriginales, extra = {}) {
            if (!id) {
                return `
                    <button type="button" class="btn btn-secondary btn-sm disabled" disabled>
                        <i class="info circle icon"></i>
                        Sin acciones
                    </button>
                `;
            }

            const estaBloqueado = estadoInfo.clase === 'bloqueado';
            const esAdmin = normalizarTexto(rolTexto).includes('admin');
            const requiereVerificacion = normalizarTexto(accionesOriginales).includes('verificacion') ||
                normalizarTexto(accionesOriginales).includes('verificación') ||
                accionesOriginales.includes('btn-enviar-verificacion');

            const botonBloqueo = estaBloqueado
                ? `
                    <button type="button" class="btn btn-sm btn-unblock" data-id="${escapeHtml(id)}">
                        <i class="unlock icon"></i>
                        Desbloquear
                    </button>
                `
                : `
                    <button type="button" class="btn btn-sm btn-block" data-id="${escapeHtml(id)}">
                        <i class="ban icon"></i>
                        Bloquear
                    </button>
                `;

            const botonRol = esAdmin
                ? `
                    <button type="button" class="btn btn-sm btn-user" data-id="${escapeHtml(id)}">
                        <i class="user icon"></i>
                        Usuario
                    </button>
                `
                : `
                    <button type="button" class="btn btn-sm btn-admin" data-id="${escapeHtml(id)}">
                        <i class="user shield icon"></i>
                        Admin
                    </button>
                `;

            const botonVerificacion = requiereVerificacion
                ? `
                    <button type="button" class="btn btn-sm btn-enviar-verificacion" data-id="${escapeHtml(id)}">
                        <i class="mail icon"></i>
                        Verificar
                    </button>
                `
                : '';

            /*
             * Cargo en la mesa directiva provisional. Solo tiene sentido para quien es
             * parte de ella; a un vecino no se le ofrece.
             */
            const botonCargo = extra.puedeTenerCargo
                ? `
                    <button type="button" class="btn btn-sm btn-cargo"
                        data-id="${escapeHtml(id)}"
                        data-nombre="${escapeHtml(extra.nombre || '')}"
                        data-cargo="${escapeHtml(extra.cargo || '')}"
                        title="Define quién maneja el dinero de la mesa directiva provisional">
                        <i class="briefcase icon"></i>
                        ${extra.cargo ? 'Cargo: ' + nombreDeCargo(extra.cargo) : 'Asignar cargo'}
                    </button>
                `
                : '';

            const puedeRegresarPago = accionesOriginales.includes('btn-regresar-pago');
            const botonPago = puedeRegresarPago
                ? `
                    <button type="button" class="btn btn-sm btn-regresar-pago" data-id="${escapeHtml(id)}">
                        <i class="undo icon"></i>
                        Pago al dueño
                    </button>
                `
                : '';

            return `
                <button type="button" class="btn btn-sm btn-eliminar" data-id="${escapeHtml(id)}">
                    <i class="trash icon"></i>
                    Eliminar
                </button>

                ${botonBloqueo}

                ${botonRol}

                ${botonVerificacion}

                ${botonCargo}

                ${botonPago}
            `;
        }

        function obtenerIdDesdeAcciones(html) {
            if (!html) {
                return '';
            }

            const temp = document.createElement('div');
            temp.innerHTML = html;

            const element = temp.querySelector('[data-id]');

            return element ? element.getAttribute('data-id') : '';
        }

        function renderizarPaginacionUsuarios(totalPaginas, totalUsuarios) {
            const pagination = $('#usuarios-pagination');

            pagination.empty();

            if (totalPaginas <= 1) {
                pagination.hide();
                return;
            }

            const inicio = ((paginaActualUsuarios - 1) * usuariosPorPagina) + 1;
            const fin = Math.min(paginaActualUsuarios * usuariosPorPagina, totalUsuarios);

            pagination.show();

            pagination.append(`
                <button
                    type="button"
                    class="pagination-btn"
                    onclick="cambiarPaginaUsuarios(${paginaActualUsuarios - 1})"
                    ${paginaActualUsuarios === 1 ? 'disabled' : ''}
                >
                    <i class="chevron left icon"></i>
                </button>
            `);

            const paginas = obtenerPaginasVisiblesUsuarios(totalPaginas);

            paginas.forEach(function(pagina) {
                if (pagina === '...') {
                    pagination.append(`<span class="pagination-info">...</span>`);
                    return;
                }

                pagination.append(`
                    <button
                        type="button"
                        class="pagination-btn ${pagina === paginaActualUsuarios ? 'active' : ''}"
                        onclick="cambiarPaginaUsuarios(${pagina})"
                    >
                        ${pagina}
                    </button>
                `);
            });

            pagination.append(`
                <button
                    type="button"
                    class="pagination-btn"
                    onclick="cambiarPaginaUsuarios(${paginaActualUsuarios + 1})"
                    ${paginaActualUsuarios === totalPaginas ? 'disabled' : ''}
                >
                    <i class="chevron right icon"></i>
                </button>
            `);

            pagination.append(`
                <div class="pagination-info">
                    Mostrando ${inicio}-${fin} de ${totalUsuarios}
                </div>
            `);
        }

        function cambiarPaginaUsuarios(pagina) {
            const totalPaginas = Math.ceil(usuariosFiltrados.length / usuariosPorPagina);

            if (pagina < 1 || pagina > totalPaginas) {
                return;
            }

            paginaActualUsuarios = pagina;
            renderizarPaginaUsuarios();

            document.querySelector('.usuarios-list-card')?.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }

        function obtenerPaginasVisiblesUsuarios(totalPaginas) {
            const paginas = [];

            if (totalPaginas <= 5) {
                for (let i = 1; i <= totalPaginas; i++) {
                    paginas.push(i);
                }

                return paginas;
            }

            paginas.push(1);

            if (paginaActualUsuarios > 3) {
                paginas.push('...');
            }

            const inicio = Math.max(2, paginaActualUsuarios - 1);
            const fin = Math.min(totalPaginas - 1, paginaActualUsuarios + 1);

            for (let i = inicio; i <= fin; i++) {
                paginas.push(i);
            }

            if (paginaActualUsuarios < totalPaginas - 2) {
                paginas.push('...');
            }

            paginas.push(totalPaginas);

            return paginas;
        }

        function filtrarUsuarios() {
            const general = normalizarTexto($('#filtro-general').val());
            const casaFiltro = normalizarTexto($('#filtro-casa').val());
            const tipoFiltro = normalizarTexto($('#filtro-tipo').val());
            const nombreFiltro = normalizarTexto($('#filtro-nombre').val());
            const correoFiltro = normalizarTexto($('#filtro-correo').val());
            const celularFiltro = normalizarTexto($('#filtro-celular').val());
            const avisoFiltro = $('#filtro-aviso').val();

            const filtrados = usuariosOriginales.filter(function(usuario) {
                const rol = extraerTexto(usuario.rol || '');
                const nombre = extraerTexto(usuario.nombre || '');
                const correo = extraerTexto(usuario.correo || '');
                const celular = extraerTexto(usuario.celular || '');
                const casa = extraerTexto(usuario.casa || '');
                const tipo = extraerTexto(usuario.tipo || '');
                const pago = extraerTexto(usuario.pago || '');
                const estado = extraerTexto(usuario.estado || '');
                const acciones = extraerTexto(usuario.acciones || '');

                const textoCompleto = normalizarTexto(
                    rol + ' ' + nombre + ' ' + correo + ' ' + celular + ' ' + casa + ' ' + tipo + ' ' + pago + ' ' + estado + ' ' + acciones
                );

                const coincideGeneral = textoCompleto.includes(general);
                const coincideCasa = normalizarTexto(casa).includes(casaFiltro);
                const coincideTipo = normalizarTexto(tipo).includes(tipoFiltro);
                const coincideNombre = normalizarTexto(nombre).includes(nombreFiltro);
                const coincideCorreo = normalizarTexto(correo).includes(correoFiltro);
                const coincideCelular = normalizarTexto(celular).includes(celularFiltro);

                // Aviso de privacidad. Sin el dato (base sin migrar) no se
                // filtra nada, para no esconder usuarios por una columna que
                // todavía no existe.
                let coincideAviso = true;

                if (avisoFiltro && usuario.aviso) {
                    const a = usuario.aviso;

                    if (avisoFiltro === 'no') coincideAviso = !a.acepto;
                    if (avisoFiltro === 'si') coincideAviso = a.acepto && a.vigente;
                    if (avisoFiltro === 'viejo') coincideAviso = a.acepto && !a.vigente;
                }

                return coincideGeneral &&
                    coincideCasa &&
                    coincideTipo &&
                    coincideNombre &&
                    coincideCorreo &&
                    coincideCelular &&
                    coincideAviso;
            });

            renderizarUsuarios(ordenarUsuariosPorCasa(filtrados, ordenCasaUsuarios));
        }

        function limpiarFiltrosUsuarios() {
            $('#filtro-general').val('');
            $('#filtro-casa').val('');
            $('#filtro-tipo').val('');
            $('#filtro-nombre').val('');
            $('#filtro-correo').val('');
            $('#filtro-celular').val('');
            $('#filtro-aviso').val('');

            ordenCasaUsuarios = 'asc';
            renderizarUsuarios(ordenarUsuariosPorCasa(usuariosOriginales, ordenCasaUsuarios));
        }

        function ordenarUsuariosPorCasa(usuarios, direccion) {
            return [...usuarios].sort(function(a, b) {
                const casaA = obtenerNumeroCasa(a.casa);
                const casaB = obtenerNumeroCasa(b.casa);

                if (direccion === 'desc') {
                    return casaB - casaA;
                }

                return casaA - casaB;
            });
        }

        function obtenerNumeroCasa(valor) {
            const texto = extraerTexto(valor || '');
            const match = texto.match(/\d+/);

            return match ? parseInt(match[0], 10) : 0;
        }

        function obtenerIniciales(nombre) {
            const limpio = extraerTexto(nombre || '').trim();

            if (!limpio) {
                return '';
            }

            return limpio
                .split(/\s+/)
                .slice(0, 2)
                .map(function(parte) {
                    return parte.charAt(0).toUpperCase();
                })
                .join('');
        }

        function obtenerEstadoUsuario(valor) {
            const texto = normalizarTexto(valor || '');

            if (texto.includes('bloque') || texto.includes('inactivo') || texto.includes('suspend')) {
                return {
                    texto: 'Bloqueado',
                    clase: 'bloqueado',
                    icono: 'ban'
                };
            }

            return {
                texto: extraerTexto(valor || 'Activo') || 'Activo',
                clase: 'activo',
                icono: 'check circle'
            };
        }

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

        $(document).on('click', '.btn-block', function() {
            const id = $(this).data('id');

            alertify.confirm(
                '¿Bloquear usuario ahora?',
                '¿Realmente deseas bloquear al usuario?, esto quitará por completo el acceso al usuario',
                function() {
                    mostrarLoaderPantalla();

                    setTimeout(function() {
                        $.ajax({
                            url: `${BASE_URL}/administrador/usuarios/bloquear-usuario/${id}`,
                            method: 'GET',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                alertify.alert(response.header, response.message, function() {
                                    cargarUsuarios();
                                });
                            },
                            error: function() {
                                alertify.error('Ocurrió un error al actualizar');
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

        $(document).on('click', '.btn-unblock', function() {
            const id = $(this).data('id');

            alertify.confirm(
                '¿Desbloquear usuario ahora?',
                '¿Realmente deseas desbloquear al usuario?, esto dará por completo el acceso al usuario',
                function() {
                    mostrarLoaderPantalla();

                    setTimeout(function() {
                        $.ajax({
                            url: `${BASE_URL}/administrador/usuarios/desbloquear-usuario/${id}`,
                            method: 'GET',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                alertify.alert(response.header, response.message, function() {
                                    cargarUsuarios();
                                });
                            },
                            error: function() {
                                alertify.error('Ocurrió un error al actualizar');
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

        $(document).on('click', '.btn-eliminar', function() {
            const id = $(this).data('id');

            alertify.confirm(
                '¿Eliminar usuario ahora?',
                '¿Realmente deseas eliminar al usuario?, este proceso no se podrá deshacer',
                function() {
                    mostrarLoaderPantalla();

                    setTimeout(function() {
                        $.ajax({
                            url: `${BASE_URL}/administrador/usuarios/eliminar-usuario/${id}`,
                            method: 'GET',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                alertify.alert(response.header, response.message, function() {
                                    cargarUsuarios();
                                });
                            },
                            error: function() {
                                alertify.error('Ocurrió un error al actualizar');
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

        /*
         * Cargo en la mesa directiva provisional.
         *
         * Define quién puede mover dinero: solo el Tesorero registra recibos
         * y valida pagos. Mientras nadie tenga ese cargo, el módulo sigue
         * abierto a toda la mesa, así que asignarlo es lo que activa la
         * separación de permisos.
         */
        $(document).on('click', '.btn-cargo', function() {
            const id = $(this).data('id');
            const nombre = $(this).data('nombre');
            const actual = String($(this).data('cargo') || '');

            $('#cargo-user-id').val(id);
            $('#cargo-nombre').text(nombre);
            $('#cargo-select').val(actual);

            $('#modal-cargo').modal('show');
        });

        $(document).on('click', '#btn-guardar-cargo', function() {
            const id = $('#cargo-user-id').val();
            const cargo = $('#cargo-select').val();

            mostrarLoaderPantalla();

            $.ajax({
                url: `${BASE_URL}/administrador/usuarios/asignar-cargo/${id}`,
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    cargo: cargo
                },
                success: function(response) {
                    $('#modal-cargo').modal('hide');
                    alertify.alert(response.header, response.message, function() {
                        cargarUsuarios();
                    });
                },
                error: function(xhr) {
                    const res = xhr.responseJSON;
                    alertify.alert(
                        (res && res.header) || 'Error',
                        (res && res.message) || 'No se pudo actualizar el cargo.'
                    );
                },
                complete: function() {
                    ocultarLoaderPantalla();
                }
            });
        });

        $(document).on('click', '.btn-admin', function() {
            const id = $(this).data('id');

            alertify.confirm(
                '¿Convertir en administrador?',
                '¿Realmente deseas convertir en administrador al usuario?, tendrá acceso al módulo de administración desde este momento',
                function() {
                    mostrarLoaderPantalla();

                    setTimeout(function() {
                        $.ajax({
                            url: `${BASE_URL}/administrador/usuarios/administrador-usuario/${id}`,
                            method: 'GET',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                alertify.alert(response.header, response.message, function() {
                                    cargarUsuarios();
                                });
                            },
                            error: function() {
                                alertify.error('Ocurrió un error al actualizar');
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

        $(document).on('click', '.btn-user', function() {
            const id = $(this).data('id');

            alertify.confirm(
                '¿Convertir en usuario?',
                '¿Realmente deseas convertir en usuario al usuario?, tendrá acceso solo a los módulos principales',
                function() {
                    mostrarLoaderPantalla();

                    setTimeout(function() {
                        $.ajax({
                            url: `${BASE_URL}/administrador/usuarios/usuario-usuario/${id}`,
                            method: 'GET',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                alertify.alert(response.header, response.message, function() {
                                    cargarUsuarios();
                                });
                            },
                            error: function() {
                                alertify.error('Ocurrió un error al actualizar');
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

        $(document).on('click', '.btn-enviar-verificacion', function() {
            const id = $(this).data('id');

            alertify.confirm(
                'Enviar verificación',
                '¿Enviar recordatorio de verificación de correo a este usuario?',
                function() {
                    mostrarLoaderPantalla();

                    setTimeout(function() {
                        $.ajax({
                            url: `${BASE_URL}/administrador/usuarios/enviar-verificacion/${id}`,
                            method: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                if (response.success) {
                                    alertify.success(response.message);
                                } else {
                                    alertify.alert(response.header, response.message);
                                }

                                cargarUsuarios();
                            },
                            error: function() {
                                alertify.error('Error al enviar el correo');
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

        $(document).on('click', '.btn-regresar-pago', function() {
            const id = $(this).data('id');

            alertify.confirm(
                'Regresar pago al dueño',
                '¿Regresar la responsabilidad del pago al dueño de esta casa? Los próximos recibos se generarán para el dueño y ya no para este inquilino.',
                function() {
                    mostrarLoaderPantalla();

                    setTimeout(function() {
                        $.ajax({
                            url: `${BASE_URL}/administrador/usuarios/regresar-pago/${id}`,
                            method: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                if (response.success) {
                                    alertify.success(response.message);
                                } else {
                                    alertify.alert(response.header, response.message);
                                }

                                cargarUsuarios();
                            },
                            error: function() {
                                alertify.error('Error al regresar el pago al dueño');
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
            ).set('labels', { ok: 'Sí, regresar al dueño', cancel: 'Cancelar' });
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
</x-app-layout>