<x-app-layout>
    <style>
        :root {
            --uasm-primary: #14b8a6;
            --uasm-primary-dark: #0f766e;
            --uasm-success: #10b981;
            --uasm-danger: #ef4444;
            --uasm-warning: #f59e0b;
            --uasm-text-main: #0f172a;
            --uasm-text-muted: #64748b;
            --uasm-text-soft: #94a3b8;
            --uasm-border: #e2e8f0;
            --uasm-surface: #ffffff;
            --uasm-soft-bg: #f8fafc;
        }

        .uasm-page {
            width: 100%;
            padding-bottom: 24px;
        }

        /* ---------- HÉROE ---------- */
        .uasm-hero {
            background:
                radial-gradient(circle at top right, rgba(255,255,255,0.24), transparent 34%),
                radial-gradient(circle at bottom left, rgba(255,255,255,0.14), transparent 30%),
                linear-gradient(135deg, var(--uasm-primary) 0%, var(--uasm-primary-dark) 100%);
            border-radius: 26px;
            padding: 26px;
            color: white;
            margin-bottom: 24px;
            position: relative;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.08);
        }

        .uasm-hero::before {
            content: '';
            position: absolute;
            width: 320px;
            height: 320px;
            border-radius: 999px;
            background: rgba(255,255,255,0.10);
            right: -100px;
            top: -150px;
            pointer-events: none;
        }

        .uasm-hero::after {
            content: '';
            position: absolute;
            width: 140px;
            height: 140px;
            border-radius: 999px;
            background: rgba(255,255,255,0.08);
            right: 60px;
            bottom: -70px;
            pointer-events: none;
        }

        .uasm-hero-content {
            display: flex;
            align-items: center;
            gap: 18px;
            position: relative;
            z-index: 2;
        }

        .uasm-hero-icon {
            width: 64px;
            height: 64px;
            border-radius: 20px;
            background: rgba(255,255,255,0.16);
            border: 1px solid rgba(255,255,255,0.22);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            box-shadow: 0 12px 28px rgba(15, 23, 42, 0.12);
        }

        .uasm-hero-icon i {
            margin: 0 !important;
            color: white;
            font-size: 1.8rem;
            line-height: 1 !important;
        }

        .uasm-hero-text h1 {
            margin: 0;
            font-size: clamp(1.4rem, 2.5vw, 1.9rem);
            font-weight: 800;
            letter-spacing: -0.02em;
        }

        .uasm-hero-text p {
            margin: 4px 0 0;
            opacity: 0.92;
            font-size: 1rem;
        }

        .uasm-hero-bg-icon {
            position: absolute;
            right: 30px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 4.5rem;
            opacity: 0.18;
            z-index: 1;
        }

        .uasm-hero-bg-icon i {
            margin: 0 !important;
        }

        /* ---------- CARD ---------- */
        .uasm-card {
            background: var(--uasm-surface);
            border: 1px solid var(--uasm-border);
            border-radius: 22px;
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.06);
            padding: 20px 22px;
            margin-bottom: 16px;
        }

        .uasm-card-titulo {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 12px;
        }

        .uasm-card-titulo .titulo {
            font-weight: 800;
            font-size: 1.02rem;
            color: var(--uasm-text-main);
        }

        .uasm-card-titulo .meta {
            font-size: 0.82rem;
            color: var(--uasm-text-muted);
            margin-top: 3px;
            display: flex;
            align-items: center;
            gap: 6px;
            flex-wrap: wrap;
        }

        .uasm-card-titulo .meta i {
            margin: 0 !important;
        }

        .uasm-card-titulo .estado-linea {
            margin-top: 8px;
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        /* ---------- BADGES ---------- */
        .uasm-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 11px;
            border-radius: 999px;
            font-size: 0.75rem;
            font-weight: 800;
            white-space: nowrap;
        }

        .b-convocada { background: #fef3c7; color: #92400e; }
        .b-en_curso { background: #dcfce7; color: #166534; }
        .b-cerrada { background: #e2e8f0; color: #475569; }

        .uasm-hint-voto {
            font-size: 0.78rem;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .uasm-hint-voto.tiene { color: var(--uasm-primary-dark); }
        .uasm-hint-voto.no { color: var(--uasm-text-soft); }

        /* ---------- FILTROS ---------- */
        .uasm-filtros {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            align-items: center;
        }

        .uasm-busqueda {
            position: relative;
            flex: 1 1 280px;
            min-width: 220px;
        }

        .uasm-busqueda > i {
            position: absolute;
            left: 13px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--uasm-text-soft);
            margin: 0 !important;
            pointer-events: none;
            z-index: 1;
        }

        .uasm-filtro-input {
            width: 100%;
            padding: 11px 14px 11px 40px;
            border: 1px solid var(--uasm-border);
            border-radius: 12px;
            font-size: 0.9rem;
            color: var(--uasm-text-main);
            background: var(--uasm-surface);
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
        }

        .uasm-filtro-input:focus {
            outline: none;
            border-color: var(--uasm-primary);
            box-shadow: 0 0 0 3px rgba(20, 184, 166, 0.15);
        }

        .uasm-fecha-filtro {
            display: flex;
            align-items: center;
            gap: 6px;
            flex-wrap: wrap;
        }

        .uasm-fecha-filtro .sep {
            font-size: 0.8rem;
            color: var(--uasm-text-soft);
            font-weight: 700;
        }

        .uasm-fecha-filtro input[type="date"] {
            padding: 9px 10px;
            border: 1px solid var(--uasm-border);
            border-radius: 10px;
            font-size: 0.85rem;
            color: var(--uasm-text-main);
            background: var(--uasm-surface);
        }

        .uasm-fecha-filtro input[type="date"]:focus {
            outline: none;
            border-color: var(--uasm-primary);
        }

        .uasm-filtro-limpia {
            border: 1px solid var(--uasm-border);
            background: var(--uasm-soft-bg);
            color: var(--uasm-text-muted);
            border-radius: 10px;
            padding: 10px 14px;
            cursor: pointer;
            font-size: 0.85rem;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: background 0.15s ease;
        }

        .uasm-filtro-limpia:hover {
            background: #eef2f7;
        }

        /* ---------- ENLACE ---------- */
        .uasm-link {
            color: var(--uasm-primary-dark);
            font-weight: 700;
            text-decoration: underline;
            text-underline-offset: 3px;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            word-break: break-all;
            transition: color 0.15s ease;
        }

        .uasm-link:hover {
            color: #0d9488;
            text-decoration-thickness: 2px;
        }

        .uasm-link i {
            margin: 0 !important;
        }

        /* ---------- BOTONES ---------- */
        .uasm-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            border: none;
            border-radius: 12px;
            padding: 11px 20px;
            font-weight: 700;
            cursor: pointer;
            font-size: 0.88rem;
            text-decoration: none;
            transition: all 0.18s ease;
            line-height: 1.2;
        }

        .uasm-btn i {
            margin: 0 !important;
        }

        .uasm-btn.primary {
            background: linear-gradient(135deg, var(--uasm-primary) 0%, var(--uasm-primary-dark) 100%);
            color: #fff;
            box-shadow: 0 8px 20px rgba(20, 184, 166, 0.24);
        }

        .uasm-btn.primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 28px rgba(20, 184, 166, 0.30);
        }

        .uasm-btn.ghost {
            background: var(--uasm-soft-bg);
            color: var(--uasm-text-main);
            border: 1px solid #eef2f7;
        }

        .uasm-btn.ghost:hover {
            background: #eef2f7;
        }

        .uasm-btn.sm {
            padding: 8px 13px;
            font-size: 0.8rem;
            border-radius: 10px;
        }

        .uasm-btn.block {
            width: 100%;
        }

        /* ---------- CAJAS DE INFORMACIÓN ---------- */
        .cred-box {
            background: var(--uasm-soft-bg);
            border: 1px solid #eef2f7;
            border-radius: 14px;
            padding: 14px 16px;
            margin: 12px 0;
        }

        .cred-row {
            display: flex;
            justify-content: space-between;
            font-size: 0.85rem;
            color: var(--uasm-text-main);
            font-weight: 600;
            flex-wrap: wrap;
            gap: 6px;
        }

        .cred-fila {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.85rem;
            margin-top: 6px;
        }

        .cred-fila i {
            margin: 0 !important;
        }

        .cred-fila.ok { color: #166534; }
        .cred-fila.wait { color: #92400e; }
        .cred-fila.info { color: var(--uasm-text-muted); }
        .cred-fila.fuerte { font-weight: 700; color: var(--uasm-text-main); }

        /* ---------- QUÓRUM ---------- */
        .quorum-bar {
            height: 8px;
            background: #e9eef5;
            border-radius: 20px;
            overflow: hidden;
            margin-top: 8px;
        }

        .quorum-fill {
            height: 100%;
            background: linear-gradient(90deg, var(--uasm-success) 0%, #34d399 100%);
            border-radius: 20px;
            transition: width 0.3s ease;
        }

        /* ---------- SECCIONES ---------- */
        .seccion-titulo {
            margin: 18px 0 10px;
            font-size: 0.95rem;
            font-weight: 900;
            color: var(--uasm-text-main);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .seccion-titulo i {
            margin: 0 !important;
            color: var(--uasm-primary);
        }

        .uasm-desc {
            color: var(--uasm-text-muted);
            font-size: 0.88rem;
            margin: 0 0 10px;
        }

        /* ---------- VOTOS ---------- */
        .voto-opt {
            display: flex;
            align-items: center;
            gap: 10px;
            border: 2px solid var(--uasm-border);
            border-radius: 12px;
            padding: 13px 15px;
            margin-bottom: 8px;
            cursor: pointer;
            transition: border-color 0.15s ease, background 0.15s ease, transform 0.12s ease;
            background: var(--uasm-surface);
        }

        .voto-opt:hover {
            border-color: #99f6e4;
            transform: translateY(-1px);
        }

        .voto-opt.sel {
            border-color: var(--uasm-primary);
            background: rgba(20, 184, 166, 0.08);
        }

        .voto-opt i {
            margin: 0 !important;
            color: var(--uasm-text-muted);
            flex-shrink: 0;
        }

        .voto-opt.sel i {
            color: var(--uasm-primary);
        }

        .voto-opt .opt-text {
            font-size: 0.92rem;
            font-weight: 600;
            color: var(--uasm-text-main);
        }

        /* ---------- ORDENAMIENTO ---------- */
        .orden-item {
            display: flex;
            align-items: center;
            gap: 10px;
            border: 1px solid var(--uasm-border);
            border-radius: 12px;
            padding: 11px 13px;
            margin-bottom: 8px;
            background: var(--uasm-surface);
            cursor: grab;
        }

        .orden-pos {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: rgba(20, 184, 166, 0.12);
            color: var(--uasm-primary-dark);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 0.85rem;
            flex-shrink: 0;
        }

        .orden-accion {
            color: var(--uasm-text-soft);
            margin: 0 !important;
        }

        .orden-aviso {
            font-size: 0.82rem;
            color: var(--uasm-text-muted);
            margin: 6px 0;
        }

        /* ---------- RESULTADOS ---------- */
        .res-row {
            margin-top: 8px;
        }

        .res-row .res-top {
            display: flex;
            justify-content: space-between;
            font-size: 0.82rem;
            color: var(--uasm-text-muted);
        }

        .res-bar {
            height: 8px;
            background: #f1f5f9;
            border-radius: 20px;
            overflow: hidden;
            margin-top: 4px;
        }

        .res-fill {
            height: 100%;
            border-radius: 20px;
            transition: width 0.3s ease;
        }

        .res-foot {
            margin-top: 8px;
            font-size: 0.8rem;
            color: var(--uasm-text-muted);
        }

        .voto-registrado {
            font-size: 0.8rem;
            color: var(--uasm-primary-dark);
            margin-top: 8px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .voto-registrado i {
            margin: 0 !important;
        }

        .motivo-bloqueo {
            font-size: 0.82rem;
            color: var(--uasm-text-soft);
            margin-top: 8px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .motivo-bloqueo i {
            margin: 0 !important;
        }

        .multi-casas {
            font-size: 0.8rem;
            color: var(--uasm-primary-dark);
            margin: 8px 0;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .multi-casas i {
            margin: 0 !important;
        }

        /* ---------- ESTADO VACÍO ---------- */
        .empty-state {
            text-align: center;
            padding: 40px 16px;
        }

        .empty-state .empty-icon {
            width: 68px;
            height: 68px;
            border-radius: 50%;
            background: #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 14px;
            color: var(--uasm-text-soft);
            font-size: 1.7rem;
        }

        .empty-state .empty-icon i {
            margin: 0 !important;
        }

        .empty-state p {
            color: var(--uasm-text-muted);
            font-size: 0.9rem;
            margin: 0;
        }

        /* ---------- MODALES ---------- */
        .modal-cabecera {
            background: linear-gradient(135deg, var(--uasm-primary) 0%, var(--uasm-primary-dark) 100%) !important;
            color: white !important;
        }

        .delegar-label {
            font-size: 0.8rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: var(--uasm-text-muted);
            margin: 12px 0 6px;
        }

        .delegar-select {
            width: 100%;
            padding: 11px 14px;
            border: 1px solid var(--uasm-border);
            border-radius: 12px;
            font-size: 0.92rem;
            color: var(--uasm-text-main);
            background: var(--uasm-surface);
        }

        .delegar-aviso {
            font-size: 0.78rem;
            color: #92400e;
            margin-top: 10px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .delegar-aviso i {
            margin: 0 !important;
        }

        /* ---------- RESPONSIVE ---------- */
        @media (max-width: 768px) {
            .uasm-hero {
                border-radius: 20px;
                padding: 22px;
                margin-bottom: 18px;
            }

            .uasm-hero-icon {
                width: 54px;
                height: 54px;
                border-radius: 16px;
            }

            .uasm-hero-icon i {
                font-size: 1.5rem;
            }

            .uasm-hero-bg-icon {
                display: none;
            }

            .uasm-card {
                border-radius: 18px;
                padding: 16px 18px;
            }

            .uasm-card-titulo {
                flex-direction: column;
            }

            .uasm-card-titulo .uasm-btn {
                width: 100%;
            }
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>

    <div class="uasm-page">
        <!-- HÉROE -->
        <div class="uasm-hero">
            <div class="uasm-hero-content">
                <div class="uasm-hero-icon">
                    <i class="gavel icon"></i>
                </div>
                <div class="uasm-hero-text">
                    <h1>Asambleas</h1>
                    <p>Participa y ejerce el voto de tu casa</p>
                </div>
            </div>
            <i class="gavel icon uasm-hero-bg-icon"></i>
        </div>

        <div class="uasm-card">
            <div class="uasm-filtros">
                <div class="uasm-busqueda">
                    <i class="search icon"></i>
                    <input type="text" id="filtro-texto" class="uasm-filtro-input" placeholder="Buscar por título, descripción o lugar...">
                </div>

                <div class="uasm-fecha-filtro">
                    <input type="date" id="filtro-desde" title="Desde">
                    <span class="sep">a</span>
                    <input type="date" id="filtro-hasta" title="Hasta">
                </div>

                <button type="button" id="limpiar-filtros" class="uasm-filtro-limpia">
                    <i class="undo icon"></i> Limpiar
                </button>
            </div>
        </div>

        <div id="lista">
            <div class="uasm-card empty-state">
                <div class="empty-icon"><i class="gavel icon"></i></div>
                <p>Cargando asambleas...</p>
            </div>
        </div>
    </div>

    <!-- MODAL DETALLE -->
    <div class="ui modal" id="modal-asm">
        <i class="close icon"></i>
        <div class="header modal-cabecera" id="m-titulo"><i class="gavel icon"></i> Asamblea</div>
        <div class="content" id="m-content" style="max-height:72vh; overflow-y:auto;"></div>
    </div>

    <!-- MODAL DELEGAR -->
    <div class="ui modal" id="modal-delegar">
        <div class="header modal-cabecera"><i class="user share icon"></i> Delegar mi voto</div>
        <div class="content" id="delegar-content"></div>
    </div>

    <script>
        let detalleActual = null;
        let poll = null;
        let asambleasOriginales = [];
        let asambleasFiltradas = [];

        $(function () {
            $('#modal-asm').modal({ onHide: function () { detenerPoll(); } });
            $('#modal-delegar').modal();

            $('#filtro-texto').on('input', aplicarFiltros);
            $('#filtro-desde, #filtro-hasta').on('change', aplicarFiltros);
            $('#limpiar-filtros').on('click', limpiarFiltros);

            cargar();
        });

        function cargar() {
            $.get('{{ route("usuario.asamblea.listar") }}', function (r) {
                if (!r.success) return;
                asambleasOriginales = r.asambleas || [];
                aplicarFiltros();
            });
        }

        function aplicarFiltros() {
            const texto = ($('#filtro-texto').val() || '').trim().toLowerCase();
            const desde = $('#filtro-desde').val();
            const hasta = $('#filtro-hasta').val();

            asambleasFiltradas = asambleasOriginales.filter(function (a) {
                if (texto) {
                    const campos = [a.titulo, a.descripcion, a.lugar, a.fecha, a.hora, a.estado];
                    const coincide = campos.some(function (c) {
                        return String(c == null ? '' : c).toLowerCase().includes(texto);
                    });
                    if (!coincide) return false;
                }
                if (desde && a.fecha_iso && a.fecha_iso < desde) return false;
                if (hasta && a.fecha_iso && a.fecha_iso > hasta) return false;
                return true;
            });

            renderLista();
        }

        function limpiarFiltros() {
            $('#filtro-texto').val('');
            $('#filtro-desde').val('');
            $('#filtro-hasta').val('');
            aplicarFiltros();
        }

        function renderLista() {
            if (!asambleasOriginales.length) {
                $('#lista').html(`
                    <div class="uasm-card empty-state">
                        <div class="empty-icon"><i class="gavel icon"></i></div>
                        <p>No hay asambleas por ahora.</p>
                    </div>`);
                return;
            }

            if (!asambleasFiltradas.length) {
                $('#lista').html(`
                    <div class="uasm-card empty-state">
                        <div class="empty-icon"><i class="search icon"></i></div>
                        <p>No hay asambleas que coincidan con los filtros.</p>
                    </div>`);
                return;
            }

            let html = '';
            asambleasFiltradas.forEach(function (a) {
                html += `
                    <div class="uasm-card">
                        <div class="uasm-card-titulo">
                            <div style="min-width:0;">
                                <div class="titulo">${esc(a.titulo)}</div>
                                <div class="meta">
                                    ${a.fecha ? '<i class="calendar alternate outline icon"></i> ' + a.fecha : ''}
                                    ${a.hora ? '· ' + a.hora : ''}
                                    ${a.lugar ? '· ' + enlazarUrls(a.lugar) : ''}
                                </div>
                                <div class="estado-linea">
                                    <span class="uasm-badge b-${a.estado}">${a.estado.replace('_',' ')}</span>
                                    ${a.num_votos
                                        ? `<span class="uasm-hint-voto tiene"><i class="check icon"></i> ejerces ${a.num_votos} voto(s)</span>`
                                        : '<span class="uasm-hint-voto no">sin voto</span>'}
                                </div>
                            </div>
                            <button class="uasm-btn ghost" onclick="abrir(${a.id})"><i class="eye icon"></i> Abrir</button>
                        </div>
                    </div>`;
            });
            $('#lista').html(html);
        }

        function detectarUrl(valor) {
            if (!valor) return null;
            const t = String(valor).trim();
            if (/^(https?:\/\/)/i.test(t)) return t;
            if (/^www\./i.test(t)) return 'https://' + t;
            return null;
        }

        function enlazarUrls(texto) {
            const partes = String(texto).split(/(https?:\/\/[^\s]+|www\.[^\s]+)/g);
            return partes.map(function (p) {
                const url = detectarUrl(p);
                if (url) {
                    return '<a class="uasm-link" href="' + esc(url) + '" target="_blank" rel="noopener noreferrer" title="Abrir enlace en una pestaña nueva"><i class="external alternate icon"></i> ' + esc(p) + '</a>';
                }
                return esc(p);
            }).join('');
        }

        window.abrir = function (id) {
            $.get('{{ url("usuario/asamblea/detalle") }}/' + id, function (r) {
                if (!r.success) return;
                detalleActual = r.asamblea;
                render(r.asamblea);
                $('#modal-asm').modal('show');
                initSortables();
                if (r.asamblea.estado === 'en_curso') iniciarPoll(id);
            });
        };

        function render(a) {
            $('#m-titulo').html('<i class="gavel icon"></i> ' + esc(a.titulo));
            let html = '';
            if (a.descripcion) html += `<p class="uasm-desc">${esc(a.descripcion)}</p>`;

            if (a.fecha || a.hora || a.lugar) {
                html += `<div class="cred-fila" style="margin:0 0 12px;flex-wrap:wrap;">
                    ${a.fecha ? '<i class="calendar alternate outline icon"></i> ' + esc(a.fecha) : ''}
                    ${a.hora ? '· ' + esc(a.hora) : ''}
                    ${a.lugar ? '<i class="map marker alternate icon"></i> ' + enlazarUrls(a.lugar) : ''}
                </div>`;
            }

            // Quórum
            const q = a.quorum;
            html += `<div class="cred-box"><div class="cred-row"><span>Quórum</span><span>${q.presentes}/${q.total} (${q.pct}%)</span></div><div class="quorum-bar"><div class="quorum-fill" style="width:${Math.min(q.pct,100)}%;"></div></div></div>`;

            // Credencial de voto
            html += '<div class="cred-box">';
            if (a.mis_casas.length) {
                html += `<div class="cred-fila fuerte"><i class="id badge icon"></i> Ejerces el voto de: ${a.mis_casas.map(c => 'Casa ' + esc(c)).join(', ')}</div>`;
            } else {
                html += `<div class="cred-fila info"><i class="ban icon"></i> No tienes voto en esta asamblea${a.delegacion ? ' (delegaste tu voto a ' + esc(a.delegacion.representante) + ')' : ''}.</div>`;
            }
            if (a.control_asistencia && a.mi_casa) {
                html += a.presente_mi_casa
                    ? `<div class="cred-fila ok"><i class="check circle icon"></i> Tu casa fue registrada como presente.</div>`
                    : `<div class="cred-fila wait"><i class="clock icon"></i> Esperando que el administrador registre tu asistencia para poder votar.</div>`;
            }
            // Delegación
            if (a.soy_dueno && a.puede_delegar) {
                if (a.delegacion) {
                    html += `<div class="cred-fila info" style="flex-wrap:wrap;"><span>Poder otorgado a <strong>${esc(a.delegacion.representante)}</strong> (${a.delegacion.tipo}).</span>
                        <button class="uasm-btn ghost sm" onclick="revocar(${a.id})"><i class="undo icon"></i> Revocar poder</button></div>`;
                } else {
                    html += `<div class="cred-fila"><button class="uasm-btn ghost sm" onclick="abrirDelegar(${a.id})"><i class="user share icon"></i> Delegar mi voto</button></div>`;
                }
            }
            html += '</div>';

            // Puntos
            html += '<div class="seccion-titulo"><i class="list ol icon"></i> Orden del día</div>';
            a.puntos.forEach(function (p, i) { html += renderPunto(a, p, i); });

            $('#m-content').html(html);
        }

        function miVotoDe(p) {
            // Toma el voto de mi primera casa (todas votan igual).
            const casas = Object.keys(p.mis_votos || {});
            if (!casas.length) return null;
            return p.mis_votos[casas[0]];
        }

        function renderPunto(a, p, i) {
            const puedeVotar = a.estado === 'en_curso' && p.estado !== 'cerrado' && a.mis_casas.length > 0
                && (!a.control_asistencia || a.presente_mi_casa);
            let html = `<div class="uasm-card"><div class="titulo">${i + 1}. ${esc(p.titulo)}</div>`;
            if (p.descripcion) html += `<div class="uasm-desc" style="margin:4px 0 0;">${esc(p.descripcion)}</div>`;

            if (a.estado === 'cerrada' && p.resultados) {
                html += renderResultado(p.resultados);
                const mv = miVotoDe(p);
                if (mv) html += `<div class="voto-registrado"><i class="check icon"></i> Ya votaste este punto.</div>`;
                return html + '</div>';
            }

            if (!puedeVotar) {
                let motivo = 'La votación no está disponible.';
                let icono = 'info circle';
                if (a.estado === 'convocada') { motivo = 'La asamblea aún no inicia.'; icono = 'clock'; }
                else if (!a.mis_casas.length) { motivo = 'No tienes voto en esta asamblea.'; icono = 'ban'; }
                else if (a.control_asistencia && !a.presente_mi_casa) { motivo = 'Debes ser registrado como presente para votar.'; icono = 'user clock'; }
                else if (p.estado === 'cerrado') { motivo = 'Este punto está cerrado.'; icono = 'lock'; }
                html += `<div class="motivo-bloqueo"><i class="${icono} icon"></i> ${motivo}</div>`;
                return html + '</div>';
            }

            if (a.mis_casas.length > 1) html += `<div class="multi-casas"><i class="users icon"></i> Tu voto contará por ${a.mis_casas.length} casas.</div>`;

            const mv = miVotoDe(p);
            if (p.tipo === 'si_no') {
                const v = mv && mv[0] ? mv[0].valor : null;
                const opt = (val, lbl, icon) => `<div class="voto-opt ${v === val ? 'sel' : ''}" onclick="votarSiNo(${a.id}, ${p.id}, '${val}')"><i class="${icon} icon"></i> <span class="opt-text">${lbl}</span></div>`;
                html += opt('a_favor', 'A favor', 'thumbs up outline') + opt('en_contra', 'En contra', 'thumbs down outline') + opt('abstencion', 'Abstención', 'minus');
            } else if (p.tipo === 'opciones') {
                const sel = mv && mv[0] ? mv[0].opcion_id : null;
                p.opciones.forEach(o => { html += `<div class="voto-opt ${sel == o.id ? 'sel' : ''}" onclick="votarOpcion(${a.id}, ${p.id}, ${o.id}, this)"><i class="circle outline icon"></i> <span class="opt-text">${esc(o.opcion)}</span></div>`; });
            } else if (p.tipo === 'ordenamiento') {
                let orden = p.opciones.slice();
                if (mv && mv.length) {
                    const byId = {}; p.opciones.forEach(o => byId[o.id] = o);
                    const ord = mv.slice().sort((x, y) => x.posicion - y.posicion).map(r => byId[r.opcion_id]).filter(Boolean);
                    if (ord.length === p.opciones.length) orden = ord;
                }
                html += `<div class="orden-aviso">Arrastra para ordenar (#1 = mayor prioridad):</div><div class="orden-list" id="orden-${p.id}">`;
                orden.forEach((o, k) => { html += `<div class="orden-item" data-opcion="${o.id}"><span class="orden-pos">${k + 1}</span><span class="opt-text" style="flex:1;">${esc(o.opcion)}</span><i class="grip lines icon orden-accion"></i></div>`; });
                html += `</div><button class="uasm-btn primary block" style="margin-top:10px;" onclick="votarOrden(${a.id}, ${p.id})"><i class="save icon"></i> Guardar mi orden</button>`;
            }
            return html + '</div>';
        }

        function renderResultado(r) {
            if (r.tipo === 'si_no') {
                const t = r.total_casas || 0;
                const row = (l, n, c) => `<div class="res-row"><div class="res-top"><span>${l}</span><span>${n}</span></div><div class="res-bar"><div class="res-fill" style="width:${t?(n/t*100):0}%;background:${c};"></div></div></div>`;
                return `<div style="margin-top:8px;">${row('A favor', r.conteo.a_favor, '#10b981')}${row('En contra', r.conteo.en_contra, '#ef4444')}${row('Abstención', r.conteo.abstencion, '#94a3b8')}<div class="res-foot"><span class="uasm-badge ${r.aprobado?'b-en_curso':'b-cerrada'}">${r.aprobado?'Aprobado':'No aprobado'}</span></div></div>`;
            }
            if (r.tipo === 'opciones') {
                let h = '<div style="margin-top:8px;">';
                r.resultados.forEach(o => h += `<div class="res-row"><div class="res-top"><span>${esc(o.opcion)}</span><span>${o.votos} (${o.pct}%)</span></div><div class="res-bar"><div class="res-fill" style="width:${o.pct}%;background:#0d9488;"></div></div></div>`);
                return h + `<div class="res-foot">Ganador: <strong>${esc(r.ganador||'—')}</strong></div></div>`;
            }
            if (r.tipo === 'ordenamiento') {
                let h = '<div style="margin-top:8px;">';
                r.ranking.forEach((f, i) => h += `<div class="res-row"><div class="res-top"><span>${i+1}. ${esc(f.opcion)}</span><span>${f.puntos} pts</span></div><div class="res-bar"><div class="res-fill" style="width:${f.pct}%;background:#0d9488;"></div></div></div>`);
                return h + '</div>';
            }
            return '';
        }

        function initSortables() {
            document.querySelectorAll('.orden-list').forEach(function (el) {
                if (el._s) return;
                el._s = Sortable.create(el, { animation: 150, onEnd: function () { el.querySelectorAll('.orden-item').forEach((it, i) => it.querySelector('.orden-pos').textContent = i + 1); } });
            });
        }

        window.votarSiNo = function (aid, pid, valor) {
            enviarVoto(aid, { punto_id: pid, valor: valor });
        };
        window.votarOpcion = function (aid, pid, opcionId, el) {
            $(el).closest('.uasm-card').find('.voto-opt').removeClass('sel'); $(el).addClass('sel');
            enviarVoto(aid, { punto_id: pid, opcion_id: opcionId });
        };
        window.votarOrden = function (aid, pid) {
            const orden = Array.from(document.querySelectorAll('#orden-' + pid + ' .orden-item')).map(it => parseInt(it.getAttribute('data-opcion')));
            enviarVoto(aid, { punto_id: pid, orden: orden });
        };

        function enviarVoto(aid, data) {
            $.ajax({ url: '{{ url("usuario/asamblea/votar") }}/' + aid, type: 'POST',
                data: Object.assign({ _token: '{{ csrf_token() }}' }, data),
                success: function (r) { if (r.success) { alertify.success(r.message); abrir(aid); } else { alertify.error(r.message); } },
                error: function (x) { alertify.error(x.responseJSON?.message || 'No se pudo registrar el voto'); } });
        }

        window.abrirDelegar = function (aid) {
            const a = detalleActual;
            let html = '<p class="uasm-desc">Elige quién ejercerá el voto de tu casa en esta asamblea.</p>';
            if (a.elegibles_inquilino.length) {
                html += '<div class="delegar-label">A mi inquilino</div>';
                a.elegibles_inquilino.forEach(u => html += `<div class="voto-opt" onclick="delegar(${aid}, ${u.id}, 'inquilino')"><i class="home icon"></i> <span class="opt-text">${esc(u.nombre)}</span></div>`);
            }
            html += '<div class="delegar-label">A otro vecino</div>';
            html += '<select id="sel-vecino" class="delegar-select"><option value="">Selecciona un vecino...</option>';
            a.elegibles_vecinos.forEach(u => html += `<option value="${u.id}">Casa ${esc(u.casa)} — ${esc(u.nombre)}</option>`);
            html += '</select>';
            html += `<button class="uasm-btn primary block" style="margin-top:12px;" onclick="delegarVecino(${aid})"><i class="check icon"></i> Otorgar poder al vecino</button>`;
            html += '<p class="delegar-aviso"><i class="warning icon"></i> Válido solo para esta asamblea. Mientras dure, tú no podrás votar.</p>';
            $('#delegar-content').html(html);
            $('#modal-delegar').modal('show');
        };
        window.delegarVecino = function (aid) {
            const id = $('#sel-vecino').val();
            if (!id) { alertify.error('Selecciona un vecino'); return; }
            delegar(aid, id, 'vecino');
        };
        window.delegar = function (aid, repId, tipo) {
            $.ajax({ url: '{{ url("usuario/asamblea/delegar") }}/' + aid, type: 'POST',
                data: { _token: '{{ csrf_token() }}', representante_id: repId, tipo: tipo },
                success: function (r) { if (r.success) { alertify.success(r.message); $('#modal-delegar').modal('hide'); abrir(aid); } else { alertify.error(r.message); } },
                error: function (x) { alertify.error(x.responseJSON?.message || 'No se pudo delegar'); } });
        };
        window.revocar = function (aid) {
            $.ajax({ url: '{{ url("usuario/asamblea/revocar") }}/' + aid, type: 'POST', data: { _token: '{{ csrf_token() }}' },
                success: function (r) { if (r.success) { alertify.success(r.message); abrir(aid); } else { alertify.error(r.message); } } });
        };

        function iniciarPoll(id) {
            detenerPoll();
            poll = setInterval(function () {
                $.get('{{ url("usuario/asamblea/detalle") }}/' + id, function (r) {
                    if (!r.success) return;
                    detalleActual = r.asamblea; render(r.asamblea); initSortables();
                    if (r.asamblea.estado !== 'en_curso') detenerPoll();
                });
            }, 12000);
        }
        function detenerPoll() { if (poll) { clearInterval(poll); poll = null; } }

        function esc(t) { return String(t == null ? '' : t).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }
    </script>
</x-app-layout>
