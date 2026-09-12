<x-app-layout>
    <style>
        :root {
            --asm-primary: #3b82f6;
            --asm-primary-dark: #2563eb;
            --asm-success: #10b981;
            --asm-danger: #ef4444;
            --asm-warning: #f59e0b;
            --asm-text-main: #0f172a;
            --asm-text-muted: #64748b;
            --asm-text-soft: #94a3b8;
            --asm-border: #e2e8f0;
            --asm-surface: #ffffff;
            --asm-soft-bg: #f8fafc;
        }

        .asamblea-page {
            width: 100%;
            padding-bottom: 24px;
        }

        /* ---------- HÉROE ---------- */
        .asamblea-hero {
            background:
                radial-gradient(circle at top right, rgba(255,255,255,0.24), transparent 34%),
                radial-gradient(circle at bottom left, rgba(255,255,255,0.14), transparent 30%),
                linear-gradient(135deg, var(--asm-primary) 0%, var(--asm-primary-dark) 100%);
            border-radius: 26px;
            padding: 26px;
            color: white;
            margin-bottom: 24px;
            position: relative;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.08);
        }

        .asamblea-hero::before {
            content: '';
            position: absolute;
            width: 340px;
            height: 340px;
            border-radius: 999px;
            background: rgba(255,255,255,0.10);
            right: -110px;
            top: -160px;
            pointer-events: none;
        }

        .asamblea-hero::after {
            content: '';
            position: absolute;
            width: 130px;
            height: 130px;
            border-radius: 999px;
            background: rgba(255,255,255,0.08);
            right: 70px;
            bottom: -65px;
            pointer-events: none;
        }

        .hero-content {
            display: flex;
            align-items: center;
            gap: 18px;
            position: relative;
            z-index: 2;
        }

        .hero-icon {
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

        .hero-icon i {
            margin: 0 !important;
            color: white;
            font-size: 1.8rem;
            line-height: 1 !important;
        }

        .hero-content h1 {
            margin: 0;
            font-size: clamp(1.4rem, 2.5vw, 1.9rem);
            font-weight: 800;
            letter-spacing: -0.02em;
        }

        .hero-content p {
            margin: 4px 0 0;
            opacity: 0.92;
            font-size: 1rem;
        }

        .hero-bg-icon {
            position: absolute;
            right: 30px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 4.5rem;
            opacity: 0.18;
            z-index: 1;
        }

        .hero-bg-icon i {
            margin: 0 !important;
        }

        /* ---------- GRID PRINCIPAL ---------- */
        .asamblea-grid {
            display: grid;
            grid-template-columns: 400px 1fr;
            gap: 22px;
            align-items: start;
        }

        @media (max-width: 900px) {
            .asamblea-grid {
                grid-template-columns: 1fr;
            }
        }

        /* ---------- CARD ---------- */
        .asamblea-card {
            background: var(--asm-surface);
            border: 1px solid var(--asm-border);
            border-radius: 22px;
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.06);
            overflow: hidden;
            margin-bottom: 20px;
        }

        .card-header {
            padding: 18px 22px;
            border-bottom: 1px solid #f1f5f9;
            background: linear-gradient(180deg, #ffffff 0%, #fbfdff 100%);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .card-header i {
            margin: 0 !important;
            color: var(--asm-primary);
            font-size: 1.15rem;
        }

        .card-header h3 {
            margin: 0;
            color: var(--asm-text-main);
            font-size: 1.05rem;
            font-weight: 900;
        }

        .card-header .badge-count {
            margin-left: auto;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 24px;
            height: 24px;
            padding: 0 7px;
            border-radius: 999px;
            background: var(--asm-primary);
            color: white;
            font-size: 0.72rem;
            font-weight: 800;
        }

        .card-body {
            padding: 20px 22px;
        }

        /* ---------- FORMULARIOS ---------- */
        .asm-field {
            display: block;
            font-size: 0.78rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--asm-text-muted);
            margin: 14px 0 6px;
        }

        .asm-field:first-child {
            margin-top: 0;
        }

        .asm-input,
        .asm-select,
        .asm-textarea {
            width: 100%;
            padding: 11px 14px;
            border: 1px solid var(--asm-border);
            border-radius: 12px;
            font-size: 0.92rem;
            color: var(--asm-text-main);
            background: var(--asm-surface);
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
            box-sizing: border-box;
        }

        .asm-input:focus,
        .asm-select:focus,
        .asm-textarea:focus {
            outline: none;
            border-color: var(--asm-primary);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.12);
        }

        .field-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }

        .check-field {
            display: flex;
            align-items: center;
            gap: 9px;
            margin-top: 16px;
            font-size: 0.85rem;
            color: var(--asm-text-muted);
            font-weight: 600;
            cursor: pointer;
        }

        .check-field input[type="checkbox"] {
            width: 17px;
            height: 17px;
            accent-color: var(--asm-primary);
            flex-shrink: 0;
        }

        /* ---------- PUNTOS DEL DÍA ---------- */
        .punto-row {
            border: 1.5px dashed #cbd5e1;
            border-radius: 14px;
            padding: 14px;
            margin-bottom: 12px;
            background: #fcfdff;
        }

        .punto-row .punto-head {
            display: flex;
            gap: 8px;
            align-items: center;
        }

        .punto-row .punto-titulo {
            flex: 1;
            height: 42px;
        }

        .btn-del-punto {
            width: 42px;
            height: 42px;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex: 0 0 auto;
        }

        .btn-del-punto i {
            margin: 0 !important;
        }

        .punto-hint {
            display: none;
            color: var(--asm-text-muted);
            font-size: 0.78rem;
            line-height: 1.5;
            margin-top: 8px;
        }

        /* ---------- BOTONES ---------- */
        .asm-btn {
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

        .asm-btn i {
            margin: 0 !important;
        }

        .asm-btn.primary {
            background: linear-gradient(135deg, var(--asm-primary) 0%, var(--asm-primary-dark) 100%);
            color: #fff;
            box-shadow: 0 8px 20px rgba(59, 130, 246, 0.22);
        }

        .asm-btn.primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 28px rgba(59, 130, 246, 0.30);
        }

        .asm-btn.ghost {
            background: var(--asm-soft-bg);
            color: var(--asm-text-main);
            border: 1px solid #eef2f7;
        }

        .asm-btn.ghost:hover {
            background: #eef2f7;
        }

        .asm-btn.danger {
            background: #fff;
            color: var(--asm-danger);
            border: 1px solid #fee2e2;
        }

        .asm-btn.danger:hover {
            background: var(--asm-danger);
            color: #fff;
            border-color: var(--asm-danger);
        }

        .asm-btn.sm {
            padding: 8px 13px;
            font-size: 0.8rem;
            border-radius: 10px;
        }

        .asm-btn.block {
            width: 100%;
        }

        /* ---------- LISTA DE ASAMBLEAS ---------- */
        .asamblea-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            border: 1px solid var(--asm-border);
            border-radius: 14px;
            padding: 14px 16px;
            margin-bottom: 10px;
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
            background: var(--asm-surface);
        }

        .asamblea-item:hover {
            border-color: #c7d2fe;
            box-shadow: 0 4px 14px rgba(59, 130, 246, 0.08);
        }

        .asamblea-item .item-main {
            min-width: 0;
        }

        .asamblea-item .item-titulo {
            font-weight: 700;
            color: var(--asm-text-main);
            font-size: 0.95rem;
        }

        .asamblea-item .item-meta {
            font-size: 0.8rem;
            color: var(--asm-text-muted);
            margin-top: 3px;
            display: flex;
            align-items: center;
            gap: 6px;
            flex-wrap: wrap;
        }

        .asamblea-item .item-actions {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            flex-shrink: 0;
        }

        /* ---------- BADGES ---------- */
        .asm-badge {
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
            color: var(--asm-text-soft);
            font-size: 1.7rem;
        }

        .empty-state .empty-icon i {
            margin: 0 !important;
        }

        .empty-state p {
            color: var(--asm-text-muted);
            font-size: 0.9rem;
            margin: 0;
        }

        /* ---------- DETALLE / TABLERO ---------- */
        .modal-cabecera {
            background: linear-gradient(135deg, var(--asm-primary) 0%, var(--asm-primary-dark) 100%) !important;
            color: white !important;
        }

        .tablero-acciones {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            margin-bottom: 16px;
            align-items: center;
        }

        .quorum-box,
        .punto-result {
            border: 1px solid var(--asm-border);
            border-radius: 14px;
            padding: 14px 16px;
            margin-bottom: 12px;
            background: var(--asm-surface);
        }

        .quorum-head {
            display: flex;
            justify-content: space-between;
            font-weight: 700;
            font-size: 0.9rem;
            color: var(--asm-text-main);
            flex-wrap: wrap;
            gap: 6px;
        }

        .quorum-bar {
            height: 10px;
            background: #f1f5f9;
            border-radius: 20px;
            overflow: hidden;
            margin-top: 8px;
        }

        .quorum-fill {
            height: 100%;
            background: linear-gradient(90deg, var(--asm-success) 0%, #34d399 100%);
            border-radius: 20px;
            transition: width 0.3s ease;
        }

        .seccion-titulo {
            margin: 20px 0 10px;
            font-size: 0.95rem;
            font-weight: 900;
            color: var(--asm-text-main);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .seccion-titulo i {
            margin: 0 !important;
            color: var(--asm-primary);
        }

        .seccion-sub {
            font-size: 0.8rem;
            color: var(--asm-text-soft);
            margin: -4px 0 10px;
        }

        /* ---------- PASE DE LISTA ---------- */
        .pase-lista {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 8px;
        }

        .casa-chip {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 8px;
            border: 1px solid var(--asm-border);
            border-radius: 12px;
            padding: 10px 12px;
            font-size: 0.85rem;
            background: var(--asm-surface);
            transition: border-color 0.15s ease, background 0.15s ease;
        }

        .casa-chip.presente {
            border-color: var(--asm-success);
            background: #f0fdf4;
        }

        .casa-chip .chip-nombre {
            font-weight: 700;
            color: var(--asm-text-main);
            line-height: 1.25;
        }

        .casa-chip .chip-info {
            font-size: 0.72rem;
            color: var(--asm-text-muted);
            margin-top: 2px;
            line-height: 1.35;
        }

        .casa-chip .chip-poder {
            color: var(--asm-primary);
        }

        .casa-chip input[type="checkbox"] {
            width: 18px;
            height: 18px;
            accent-color: var(--asm-success);
            flex-shrink: 0;
            margin-top: 2px;
            cursor: pointer;
        }

        /* ---------- RESULTADOS ---------- */
        .punto-head-res {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 8px;
        }

        .punto-head-res strong {
            font-size: 0.92rem;
            color: var(--asm-text-main);
        }

        .res-row {
            margin-top: 8px;
        }

        .res-row .res-top {
            display: flex;
            justify-content: space-between;
            font-size: 0.82rem;
            color: var(--asm-text-muted);
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
            color: var(--asm-text-muted);
        }

        /* ---------- RESPONSIVE ---------- */
        @media (max-width: 768px) {
            .asamblea-hero {
                border-radius: 20px;
                padding: 22px;
                margin-bottom: 18px;
            }

            .hero-icon {
                width: 54px;
                height: 54px;
                border-radius: 16px;
            }

            .hero-icon i {
                font-size: 1.5rem;
            }

            .hero-bg-icon {
                display: none;
            }

            .asamblea-card {
                border-radius: 18px;
            }

            .card-header,
            .card-body {
                padding: 16px 18px;
            }

            .field-row {
                grid-template-columns: 1fr;
            }

            .asamblea-item {
                flex-direction: column;
                align-items: flex-start;
            }

            .asamblea-item .item-actions {
                width: 100%;
            }

            .asamblea-item .item-actions .asm-btn {
                flex: 1;
            }

            .pase-lista {
                grid-template-columns: 1fr 1fr;
            }
        }

        @media (max-width: 480px) {
            .pase-lista {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div class="asamblea-page">
        <!-- HÉROE -->
        <div class="asamblea-hero">
            <div class="hero-content">
                <div class="hero-icon">
                    <i class="gavel icon"></i>
                </div>
                <div>
                    <h1>Asambleas</h1>
                    <p>Convoca y gestiona las asambleas del condominio</p>
                </div>
            </div>
            <i class="gavel icon hero-bg-icon"></i>
        </div>

        <div class="asamblea-grid">
            <!-- FORMULARIO -->
            <div>
                <div class="asamblea-card">
                    <div class="card-header">
                        <i class="plus circle icon"></i>
                        <h3>Convocar asamblea</h3>
                    </div>
                    <div class="card-body">
                        <form id="form-asamblea">
                            <label class="asm-field">Título *</label>
                            <input type="text" id="asm-titulo" class="asm-input" placeholder="Asamblea ordinaria · agosto 2026" required>

                            <label class="asm-field">Descripción</label>
                            <textarea id="asm-desc" class="asm-textarea" rows="2" placeholder="Motivo o contexto de la asamblea"></textarea>

                            <div class="field-row">
                                <div>
                                    <label class="asm-field">Fecha</label>
                                    <input type="date" id="asm-fecha" class="asm-input">
                                </div>
                                <div>
                                    <label class="asm-field">Hora</label>
                                    <input type="time" id="asm-hora" class="asm-input">
                                </div>
                            </div>

                            <label class="asm-field">Lugar / enlace</label>
                            <input type="text" id="asm-lugar" class="asm-input" placeholder="Área común o enlace">

                            <label class="asm-field">Quórum requerido (%)</label>
                            <input type="number" id="asm-quorum" class="asm-input" value="50" min="1" max="100">

                            <label class="check-field">
                                <input type="checkbox" id="asm-control" checked>
                                Solo votan las casas registradas como presentes
                            </label>

                            <label class="asm-field">Orden del día *</label>
                            <div id="puntos-container"></div>
                            <button type="button" class="asm-btn ghost sm" id="btn-add-punto">
                                <i class="plus icon"></i> Agregar punto
                            </button>

                            <button type="submit" class="asm-btn primary block" style="margin-top:18px;">
                                <i class="send icon"></i> Convocar asamblea
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- LISTADO -->
            <div>
                <div class="asamblea-card">
                    <div class="card-header">
                        <i class="list ul icon"></i>
                        <h3>Asambleas</h3>
                        <span class="badge-count" id="asambleas-count">0</span>
                    </div>
                    <div class="card-body" id="lista-asambleas">
                        <div class="empty-state">
                            <div class="empty-icon"><i class="gavel icon"></i></div>
                            <p>Cargando asambleas...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL DETALLE -->
    <div class="ui modal" id="modal-asamblea">
        <i class="close icon"></i>
        <div class="header modal-cabecera" id="modal-titulo"><i class="gavel icon"></i> Asamblea</div>
        <div class="content" id="modal-content" style="max-height:70vh; overflow-y:auto;"></div>
    </div>

    <script>
        let asambleaActual = null;
        let pollTablero = null;

        $(function () {
            $('#modal-asamblea').modal({ onHide: function () { detenerPoll(); } });
            agregarPunto();
            $('#btn-add-punto').on('click', agregarPunto);
            cargarAsambleas();
            $('#form-asamblea').on('submit', crearAsamblea);
        });

        function agregarPunto() {
            const html = `
                <div class="punto-row">
                    <div class="punto-head">
                        <input type="text" class="asm-input punto-titulo" placeholder="Título del punto">
                        <button type="button" class="asm-btn danger btn-del-punto" title="Quitar punto"><i class="times icon"></i></button>
                    </div>
                    <select class="asm-select punto-tipo" style="margin-top:8px;">
                        <option value="si_no">A favor / En contra / Abstención</option>
                        <option value="opciones">Opciones (elegir una)</option>
                        <option value="ordenamiento">Ordenamiento (priorizar)</option>
                    </select>
                    <textarea class="asm-textarea punto-opciones" rows="4" style="margin-top:8px; display:none;" placeholder="Una opción por renglón (Enter para la siguiente). Ej.:&#10;Repavimentar el acceso&#10;Área de juegos&#10;Cámaras de seguridad"></textarea>
                    <small class="punto-hint">Escribe cada opción/proyecto en su propio renglón dentro de esta caja (presiona Enter). Mínimo 2. No agregues otro punto por cada opción.</small>
                </div>`;
            $('#puntos-container').append(html);
        }

        $(document).on('click', '.btn-del-punto', function () {
            if ($('.punto-row').length > 1) $(this).closest('.punto-row').remove();
        });
        $(document).on('change', '.punto-tipo', function () {
            const row = $(this).closest('.punto-row');
            const mostrar = $(this).val() !== 'si_no';
            row.find('.punto-opciones').toggle(mostrar);
            row.find('.punto-hint').toggle(mostrar);
        });

        function crearAsamblea(e) {
            e.preventDefault();
            const puntos = [];
            let valido = true;
            $('.punto-row').each(function () {
                const titulo = $(this).find('.punto-titulo').val().trim();
                const tipo = $(this).find('.punto-tipo').val();
                if (!titulo) return;
                let opciones = [];
                if (tipo !== 'si_no') {
                    opciones = $(this).find('.punto-opciones').val().split('\n').map(s => s.trim()).filter(Boolean);
                    if (opciones.length < 2) { valido = false; }
                }
                puntos.push({ titulo, tipo, opciones });
            });
            if (!puntos.length) { alertify.error('Agrega al menos un punto'); return; }
            if (!valido) { alertify.error('Los puntos de opciones/ordenamiento necesitan al menos 2 opciones'); return; }

            $.ajax({
                url: '{{ route("admin.asamblea.crear") }}', type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    titulo: $('#asm-titulo').val(), descripcion: $('#asm-desc').val(),
                    fecha: $('#asm-fecha').val(), hora: $('#asm-hora').val(), lugar: $('#asm-lugar').val(),
                    quorum_pct: $('#asm-quorum').val(), control_asistencia: $('#asm-control').is(':checked') ? 1 : 0,
                    puntos: puntos
                },
                success: function (r) {
                    if (r.success) {
                        alertify.success(r.message);
                        $('#form-asamblea')[0].reset();
                        $('#puntos-container').html(''); agregarPunto();
                        cargarAsambleas();
                    } else { alertify.error(r.message); }
                },
                error: function (x) { alertify.error(x.responseJSON?.message || 'Error al convocar'); }
            });
        }

        function cargarAsambleas() {
            $.get('{{ route("admin.asamblea.listar") }}', function (r) {
                if (!r.success) return;
                $('#asambleas-count').text(r.asambleas.length);
                if (!r.asambleas.length) {
                    $('#lista-asambleas').html(`
                        <div class="empty-state">
                            <div class="empty-icon"><i class="gavel icon"></i></div>
                            <p>Aún no hay asambleas. Convocá la primera desde el formulario.</p>
                        </div>`);
                    return;
                }
                let html = '';
                r.asambleas.forEach(function (a) {
                    html += `
                        <div class="asamblea-item">
                            <div class="item-main">
                                <div class="item-titulo">${esc(a.titulo)}</div>
                                <div class="item-meta">
                                    ${a.fecha ? '<i class="calendar alternate outline icon"></i>' + esc(a.fecha) : ''}
                                    <span>· ${a.puntos} punto(s)</span>
                                    <span class="asm-badge b-${a.estado}">${a.estado.replace('_',' ')}</span>
                                </div>
                            </div>
                            <div class="item-actions">
                                <button class="asm-btn ghost sm" onclick="verAsamblea(${a.id})"><i class="eye icon"></i> Abrir</button>
                                <button class="asm-btn danger sm" onclick="eliminarAsamblea(${a.id})" title="Eliminar"><i class="trash icon"></i></button>
                            </div>
                        </div>`;
                });
                $('#lista-asambleas').html(html);
            });
        }

        window.eliminarAsamblea = function (id) {
            alertify.confirm('Eliminar asamblea', '¿Eliminar esta asamblea y todos sus datos?', function () {
                $.ajax({ url: '{{ url("administrador/asamblea/eliminar") }}/' + id, type: 'DELETE', data: { _token: '{{ csrf_token() }}' },
                    success: function (r) { alertify.success(r.message); cargarAsambleas(); } });
            }, function () {}).set('labels', { ok: 'Sí, eliminar', cancel: 'Cancelar' });
        };

        window.verAsamblea = function (id) {
            asambleaActual = id;
            $.get('{{ url("administrador/asamblea/detalle") }}/' + id, function (r) {
                if (!r.success) return;
                renderDetalle(r.asamblea);
                $('#modal-asamblea').modal('show');
                if (r.asamblea.estado === 'en_curso') iniciarPoll(id);
            });
        };

        function renderDetalle(a) {
            const minutaActual = $('#minuta-texto').length ? $('#minuta-texto').val() : (a.minuta || '');
            $('#modal-titulo').html('<i class="gavel icon"></i> ' + esc(a.titulo));
            let html = '';

            // Controles de estado
            html += '<div class="tablero-acciones">';
            if (a.estado === 'convocada') html += `<button class="asm-btn primary sm" onclick="cambiarEstado(${a.id},'iniciar')"><i class="play icon"></i> Iniciar</button>`;
            if (a.estado === 'en_curso') html += `<button class="asm-btn danger sm" onclick="cambiarEstado(${a.id},'cerrar')"><i class="lock icon"></i> Cerrar asamblea</button>`;
            html += `<a class="asm-btn ghost sm" href="{{ url('administrador/asamblea/acta') }}/${a.id}"><i class="file pdf icon"></i> Acta (PDF)</a>`;
            html += `<span class="asm-badge b-${a.estado}">${a.estado.replace('_',' ')}</span>`;
            html += '</div>';

            // Quórum
            html += '<div id="quorum-box">' + renderQuorum(a.quorum) + '</div>';

            // Pase de lista (si control de asistencia) — agrupado por votante.
            if (a.control_asistencia) {
                html += '<div class="seccion-titulo"><i class="clipboard list icon"></i> Pase de lista</div>';
                html += '<p class="seccion-sub">Marca a quien esté presente. Si trae poder de otras casas, al marcarlo cuentan todas.</p>';
                html += '<div class="pase-lista" id="pase-lista">';
                a.pase_lista.forEach(function (v) {
                    const casasTxt = v.casas.map(c => 'Casa ' + esc(c)).join(', ');
                    const poderTxt = v.representadas.length
                        ? `<span class="chip-poder"> · con poder de ${v.representadas.map(c => 'Casa ' + esc(c)).join(', ')}</span>`
                        : '';
                    html += `
                        <div class="casa-chip ${v.presente ? 'presente' : ''}">
                            <div style="min-width:0;">
                                <div class="chip-nombre">${esc(v.nombre)}${v.es_inquilino ? ' · inquilino' : ''}</div>
                                <div class="chip-info">Vota por ${v.casas.length} casa(s): ${casasTxt}${poderTxt}</div>
                            </div>
                            <input type="checkbox" onchange="marcarGrupo(${a.id}, '${esc(v.ref)}', this)" ${v.presente ? 'checked' : ''}>
                        </div>`;
                });
                html += '</div>';
            }

            // Puntos + resultados
            html += '<div class="seccion-titulo"><i class="list ol icon"></i> Orden del día</div>';
            html += '<div id="puntos-resultados">' + renderPuntos(a.puntos) + '</div>';

            // Minuta / acuerdos
            html += '<div class="seccion-titulo"><i class="sticky note outline icon"></i> Minuta / Acuerdos</div>';
            html += '<p class="seccion-sub">Escribe aquí los acuerdos y conclusiones de la asamblea. Se incluirán en el acta PDF.</p>';
            html += '<div class="quorum-box">';
            html += '<textarea id="minuta-texto" class="asm-textarea" rows="6" placeholder="Ej.: Se acordó que..."></textarea>';
            html += `<button class="asm-btn primary" style="margin-top:10px;" onclick="guardarMinuta(${a.id})"><i class="save icon"></i> Guardar minuta</button>`;
            html += '</div>';

            $('#modal-content').html(html);
            $('#minuta-texto').val(minutaActual);
        }

        function renderQuorum(q) {
            return `
                <div class="quorum-box">
                    <div class="quorum-head">
                        <span>Quórum</span>
                        <span>${q.presentes} / ${q.total} casas (${q.pct}%) ${q.alcanzado ? '<span class="asm-badge b-en_curso">alcanzado</span>' : '<span class="asm-badge b-convocada">pendiente</span>'}</span>
                    </div>
                    <div class="quorum-bar"><div class="quorum-fill" style="width:${Math.min(q.pct,100)}%;"></div></div>
                </div>`;
        }

        function renderPuntos(puntos) {
            let html = '';
            puntos.forEach(function (p, i) {
                html += `<div class="punto-result"><div class="punto-head-res">
                    <strong>${i + 1}. ${esc(p.titulo)}</strong>
                    <button class="asm-btn ghost sm" onclick="togglePunto(${p.id})">${p.estado === 'cerrado' ? '<i class="lock icon"></i> Cerrado' : '<i class="unlock icon"></i> Abierto'}</button>
                </div>`;
                html += renderResultado(p);
                html += '</div>';
            });
            return html;
        }

        function renderResultado(p) {
            const r = p.resultados;
            if (!r) return '';
            if (r.tipo === 'si_no') {
                const t = r.total_casas || 0;
                const row = (lbl, n, color) => `<div class="res-row"><div class="res-top"><span>${lbl}</span><span>${n}</span></div><div class="res-bar"><div class="res-fill" style="width:${t ? (n/t*100) : 0}%; background:${color};"></div></div></div>`;
                return `<div style="margin-top:8px;">${row('A favor', r.conteo.a_favor, '#10b981')}${row('En contra', r.conteo.en_contra, '#ef4444')}${row('Abstención', r.conteo.abstencion, '#94a3b8')}
                    <div class="res-foot"><span class="asm-badge ${r.aprobado ? 'b-en_curso' : 'b-cerrada'}">${r.aprobado ? 'Aprobado' : 'No aprobado'}</span> · ${t} casa(s)</div></div>`;
            }
            if (r.tipo === 'opciones') {
                let h = '<div style="margin-top:8px;">';
                r.resultados.forEach(o => { h += `<div class="res-row"><div class="res-top"><span>${esc(o.opcion)}</span><span>${o.votos} (${o.pct}%)</span></div><div class="res-bar"><div class="res-fill" style="width:${o.pct}%; background:#0d9488;"></div></div></div>`; });
                h += `<div class="res-foot">Ganador: <strong>${esc(r.ganador || '—')}</strong> · ${r.total_casas} casa(s)</div></div>`;
                return h;
            }
            if (r.tipo === 'ordenamiento') {
                let h = '<div style="margin-top:8px;">';
                r.ranking.forEach((f, i) => { h += `<div class="res-row"><div class="res-top"><span>${i + 1}. ${esc(f.opcion)}</span><span>${f.puntos} pts</span></div><div class="res-bar"><div class="res-fill" style="width:${f.pct}%; background:#0d9488;"></div></div></div>`; });
                h += `<div class="res-foot">${r.total_casas} casa(s)</div></div>`;
                return h;
            }
            return '';
        }

        // Marca al votante y a todas las casas que ejerce (propia + representadas).
        window.marcarGrupo = function (id, refCasa, el) {
            const presente = $(el).is(':checked');
            $(el).closest('.casa-chip').toggleClass('presente', presente);
            $.ajax({ url: '{{ url("administrador/asamblea/marcar-grupo") }}/' + id, type: 'POST',
                data: { _token: '{{ csrf_token() }}', casa: refCasa, presente: presente ? 1 : 0 },
                success: function (r) { if (r.quorum) $('#quorum-box').html(renderQuorum(r.quorum)); },
                error: function () { alertify.error('No se pudo actualizar el pase de lista'); } });
        };

        window.cambiarEstado = function (id, accion) {
            $.ajax({ url: '{{ url("administrador/asamblea") }}/' + accion + '/' + id, type: 'POST', data: { _token: '{{ csrf_token() }}' },
                success: function (r) { alertify.success(r.message); verAsamblea(id); cargarAsambleas(); } });
        };

        window.togglePunto = function (puntoId) {
            $.ajax({ url: '{{ url("administrador/asamblea/punto") }}/' + puntoId + '/toggle', type: 'POST', data: { _token: '{{ csrf_token() }}' },
                success: function () { verAsamblea(asambleaActual); } });
        };

        window.guardarMinuta = function (id) {
            $.ajax({ url: '{{ url("administrador/asamblea/guardar-minuta") }}/' + id, type: 'POST',
                data: { _token: '{{ csrf_token() }}', minuta: $('#minuta-texto').val() },
                success: function (r) { alertify.success(r.message); },
                error: function () { alertify.error('No se pudo guardar la minuta'); } });
        };

        // Auto-actualización del tablero cada 8s mientras la asamblea está en curso.
        function iniciarPoll(id) {
            detenerPoll();
            pollTablero = setInterval(function () {
                $.get('{{ url("administrador/asamblea/detalle") }}/' + id, function (d) {
                    if (!d.success) return;
                    $('#quorum-box').html(renderQuorum(d.asamblea.quorum));
                    $('#puntos-resultados').html(renderPuntos(d.asamblea.puntos));
                    if (d.asamblea.estado !== 'en_curso') detenerPoll();
                });
            }, 8000);
        }
        function detenerPoll() { if (pollTablero) { clearInterval(pollTablero); pollTablero = null; } }

        function esc(t) { return String(t == null ? '' : t).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }
    </script>
</x-app-layout>
