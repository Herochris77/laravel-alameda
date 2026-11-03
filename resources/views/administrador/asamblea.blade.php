<x-app-layout>
    <style>
        :root {
            --asm-primary: var(--brand-500);
            --asm-primary-dark: var(--brand-600);
            --asm-success: var(--brand-500);
            --asm-danger: var(--danger);
            --asm-warning: var(--oro-700);
            --asm-text-main: var(--ink);
            --asm-text-muted: var(--ink-3);
            --asm-text-soft: var(--ink-4);
            --asm-border: var(--arena);
            --asm-surface: #ffffff;
            --asm-soft-bg: var(--crema-50);
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
            box-shadow: 0 10px 30px rgba(20,32,26, 0.08);
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
            box-shadow: 0 12px 28px rgba(20,32,26, 0.12);
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
            box-shadow: 0 8px 24px rgba(20,32,26, 0.06);
            overflow: hidden;
            margin-bottom: 20px;
        }

        .card-header {
            padding: 18px 22px;
            border-bottom: 1px solid var(--crema-100);
            background: linear-gradient(180deg, #ffffff 0%, var(--crema-50) 100%);
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
            box-shadow: 0 0 0 3px rgba(23,137,92, 0.12);
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
            border: 1.5px dashed var(--arena-2);
            border-radius: 14px;
            padding: 14px;
            margin-bottom: 12px;
            background: var(--crema-50);
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
            box-shadow: 0 8px 20px rgba(23,137,92, 0.22);
        }

        .asm-btn.primary:hover {
            
            box-shadow: 0 12px 28px rgba(23,137,92, 0.30);
        }

        .asm-btn.ghost {
            background: var(--asm-soft-bg);
            color: var(--asm-text-main);
            border: 1px solid var(--crema-100);
        }

        .asm-btn.ghost:hover {
            background: var(--crema-100);
        }

        .asm-btn.danger {
            background: #fff;
            color: var(--asm-danger);
            border: 1px solid var(--danger-bg);
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
            border-color: var(--brand-100);
            box-shadow: 0 4px 14px rgba(23,137,92, 0.08);
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

        .b-convocada { background: var(--warn-bg); color: var(--warn); }
        .b-en_curso { background: var(--ok-bg); color: var(--brand-800); }
        .b-cerrada { background: var(--arena); color: var(--ink-2); }

        /* ---------- ESTADO VACÍO ---------- */
        .empty-state {
            text-align: center;
            padding: 40px 16px;
        }

        .empty-state .empty-icon {
            width: 68px;
            height: 68px;
            border-radius: 50%;
            background: var(--crema-100);
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
            background: var(--crema-100);
            border-radius: 20px;
            overflow: hidden;
            margin-top: 8px;
        }

        .quorum-fill {
            height: 100%;
            background: linear-gradient(90deg, var(--asm-success) 0%, var(--brand-500) 100%);
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
            background: var(--brand-50);
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
            background: var(--crema-100);
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

                            <label class="asm-field" for="asm-modalidad">¿Cómo se llevará a cabo?</label>
                            <select id="asm-modalidad" class="asm-select">
                                <option value="mixta">Mixta: plataforma y sala (recomendada)</option>
                                <option value="presencial">Presencial: la mesa registra asistencia y votos</option>
                                <option value="plataforma">Solo por la plataforma</option>
                            </select>
                            <small class="punto-hint" id="asm-modalidad-ayuda" style="display:block;"></small>

                            <label class="check-field">
                                <input type="checkbox" id="asm-control" checked>
                                Solo votan las casas registradas como presentes
                            </label>

                            <label class="asm-field">Orden del día <span style="font-weight:400; color:var(--ink-3);">(opcional)</span></label>
                            <small class="punto-hint" style="display:block; margin-bottom:6px;">Puedes dejarlo vacío (asamblea sin votación) y agregar puntos después, incluso durante la asamblea.</small>
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
        let asambleaDatos = null;
        let pollTablero = null;

        const MODALIDADES = {
            mixta: { titulo: 'Mixta: plataforma y sala', ayuda: 'Quien puede entrar vota desde su cuenta; a quien vota en la sala o no puede entrar a la plataforma (por ejemplo, no aceptó el aviso de privacidad) la mesa le registra asistencia y voto.' },
            presencial: { titulo: 'Presencial: la mesa registra los votos', ayuda: 'Todo se vota en la sala. La mesa pasa lista y registra el voto de cada casa; los vecinos solo consultan.' },
            plataforma: { titulo: 'Solo por la plataforma', ayuda: 'Cada casa vota desde su cuenta. La mesa solo puede registrar a las casas que no pueden entrar a la plataforma.' }
        };
        const TIPOS = { si_no: 'A favor / En contra / Abstención', opciones: 'Opciones (elegir una)', ordenamiento: 'Ordenamiento (priorizar)', informativo: 'Informativo (sin votación)' };

        $(function () {
            $('#modal-asamblea').modal({ onHide: function () { detenerPoll(); } });
            $('#btn-add-punto').on('click', agregarPunto);
            cargarAsambleas();
            $('#form-asamblea').on('submit', crearAsamblea);
            $('#asm-modalidad').on('change', actualizarAyudaModalidad).trigger('change');
        });

        function actualizarAyudaModalidad() {
            const m = $('#asm-modalidad').val();
            $('#asm-modalidad-ayuda').text(MODALIDADES[m].ayuda);
            // En una asamblea presencial siempre hay pase de lista.
            $('#asm-control').prop('checked', m === 'presencial' ? true : $('#asm-control').prop('checked')).prop('disabled', m === 'presencial');
        }

        function agregarPunto() {
            const html = `
                <div class="punto-row">
                    <div class="punto-head">
                        <input type="text" class="asm-input punto-titulo" placeholder="Título del punto">
                        <button type="button" class="asm-btn danger btn-del-punto" title="Quitar punto"><i class="times icon"></i></button>
                    </div>
                    <select class="asm-select punto-tipo" style="margin-top:8px;">
                        ${Object.keys(TIPOS).map(k => `<option value="${k}">${TIPOS[k]}</option>`).join('')}
                    </select>
                    <textarea class="asm-textarea punto-opciones" rows="4" style="margin-top:8px; display:none;" placeholder="Una opción por renglón (Enter para la siguiente). Ej.:&#10;Repavimentar el acceso&#10;Área de juegos&#10;Cámaras de seguridad"></textarea>
                    <small class="punto-hint" style="display:none;">Escribe cada opción/proyecto en su propio renglón dentro de esta caja (presiona Enter). Mínimo 2. No agregues otro punto por cada opción.</small>
                </div>`;
            $('#puntos-container').append(html);
        }

        $(document).on('click', '.btn-del-punto', function () {
            $(this).closest('.punto-row').remove();
        });
        $(document).on('change', '.punto-tipo', function () {
            const row = $(this).closest('.punto-row');
            const mostrar = ['opciones', 'ordenamiento'].includes($(this).val());
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
                if (['opciones', 'ordenamiento'].includes(tipo)) {
                    opciones = $(this).find('.punto-opciones').val().split('\n').map(s => s.trim()).filter(Boolean);
                    if (opciones.length < 2) { valido = false; }
                }
                puntos.push({ titulo, tipo, opciones });
            });
            if (!valido) { alertify.error('Los puntos de opciones/ordenamiento necesitan al menos 2 opciones'); return; }

            const datos = {
                _token: '{{ csrf_token() }}',
                titulo: $('#asm-titulo').val(), descripcion: $('#asm-desc').val(),
                fecha: $('#asm-fecha').val(), hora: $('#asm-hora').val(), lugar: $('#asm-lugar').val(),
                quorum_pct: $('#asm-quorum').val(), control_asistencia: $('#asm-control').is(':checked') ? 1 : 0,
                modalidad: $('#asm-modalidad').val()
            };
            if (puntos.length) { datos.puntos = puntos; }

            $.ajax({
                url: '{{ route("admin.asamblea.crear") }}', type: 'POST', data: datos,
                success: function (r) {
                    if (r.success) {
                        alertify.success(r.message);
                        $('#form-asamblea')[0].reset();
                        $('#puntos-container').html('');
                        actualizarAyudaModalidad();
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
                            <p>Aún no hay asambleas. Convoca la primera desde el formulario.</p>
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
                                    <span>· ${esc((MODALIDADES[a.modalidad] || MODALIDADES.plataforma).titulo.split(':')[0])}</span>
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
                asambleaDatos = r.asamblea;
                renderDetalle(r.asamblea);
                $('#modal-asamblea').modal('show');
                if (r.asamblea.estado === 'en_curso') iniciarPoll(id);
            });
        };

        function renderDetalle(a) {
            const minutaActual = $('#minuta-texto').length ? $('#minuta-texto').val() : (a.minuta || '');
            const modalidad = MODALIDADES[a.modalidad] || MODALIDADES.plataforma;
            const sinAcceso = (a.casas || []).filter(c => !c.acceso);
            $('#modal-titulo').html('<i class="gavel icon"></i> ' + esc(a.titulo));
            let html = '';

            // Controles de estado
            html += '<div class="tablero-acciones">';
            if (a.estado === 'convocada') html += `<button class="asm-btn primary sm" onclick="cambiarEstado(${a.id},'iniciar')"><i class="play icon"></i> Iniciar</button>`;
            if (a.estado === 'en_curso') html += `<button class="asm-btn danger sm" onclick="cambiarEstado(${a.id},'cerrar')"><i class="lock icon"></i> Cerrar asamblea</button>`;
            html += `<a class="asm-btn ghost sm" href="{{ url('administrador/asamblea/acta') }}/${a.id}"><i class="file pdf icon"></i> Acta (PDF)</a>`;
            html += `<span class="asm-badge b-${a.estado}">${a.estado.replace('_',' ')}</span>`;
            html += '</div>';

            // Modalidad (se puede cambiar mientras no esté cerrada: p. ej. si en la sala deciden votar a mano)
            html += '<div class="quorum-box"><div class="quorum-head"><span><i class="users icon"></i> Cómo se lleva</span></div>';
            if (a.estado !== 'cerrada') {
                html += `<div style="display:flex; gap:8px; flex-wrap:wrap; align-items:center; margin-top:6px;">
                    <select id="cfg-modalidad" class="asm-select" style="flex:1; min-width:220px;">
                        ${Object.keys(MODALIDADES).map(k => `<option value="${k}" ${a.modalidad === k ? 'selected' : ''}>${esc(MODALIDADES[k].titulo)}</option>`).join('')}
                    </select>
                    <button class="asm-btn ghost sm" onclick="guardarModalidad(${a.id})"><i class="save icon"></i> Guardar</button>
                </div>`;
            } else {
                html += `<div style="margin-top:6px;"><strong>${esc(modalidad.titulo)}</strong></div>`;
            }
            html += `<p class="seccion-sub" style="margin:6px 0 0;">${esc(modalidad.ayuda)}</p></div>`;

            // Casas que no pueden entrar a la plataforma
            if (sinAcceso.length) {
                html += `<div class="aviso-sin-acceso"><i class="lock icon"></i> <span><strong>${sinAcceso.length} casa(s) no pueden entrar a la plataforma</strong> (no han aceptado el aviso de privacidad y los términos, o su cuenta está bloqueada): ${sinAcceso.map(c => 'Casa ' + esc(c.casa)).join(', ')}. La mesa registra su asistencia y su voto en la sala.</span></div>`;
            }

            // Quórum
            html += '<div id="quorum-box">' + renderQuorum(a.quorum) + '</div>';

            // Pase de lista — agrupado por votante. Siempre disponible: en una asamblea presencial es la base de todo.
            html += '<div class="seccion-titulo"><i class="clipboard list icon"></i> Pase de lista</div>';
            html += a.control_asistencia
                ? '<p class="seccion-sub">Marca a quien esté presente. Si trae poder de otras casas, al marcarlo cuentan todas.</p>'
                : '<p class="seccion-sub">En esta asamblea no es obligatorio, pero sirve para llevar el quórum. Marca a quien esté presente.</p>';
            html += '<div class="pase-lista" id="pase-lista">';
            a.pase_lista.forEach(function (v) {
                const casasTxt = v.casas.map(c => 'Casa ' + esc(c)).join(', ');
                const poderTxt = v.representadas.length
                    ? `<span class="chip-poder"> · con poder de ${v.representadas.map(c => 'Casa ' + esc(c)).join(', ')}</span>`
                    : '';
                const info = (a.casas || []).find(c => c.casa === String(v.ref));
                const sinAccesoChip = info && !info.acceso
                    ? '<span class="chip-sin-acceso" title="No puede entrar a la plataforma: la mesa registra su asistencia y su voto."><i class="lock icon"></i> sin acceso</span>'
                    : '';
                html += `
                    <div class="casa-chip ${v.presente ? 'presente' : ''}">
                        <div style="min-width:0;">
                            <div class="chip-nombre">${esc(v.nombre)}${v.es_inquilino ? ' · inquilino' : ''} ${sinAccesoChip}</div>
                            <div class="chip-info">Vota por ${v.casas.length} casa(s): ${casasTxt}${poderTxt}</div>
                        </div>
                        <input type="checkbox" aria-label="Presente" onchange="marcarGrupo(${a.id}, '${esc(v.ref)}', this)" ${v.presente ? 'checked' : ''}>
                    </div>`;
            });
            html += '</div>';

            // Puntos + resultados
            html += '<div class="seccion-titulo"><i class="list ol icon"></i> Orden del día</div>';
            if (!a.puntos.length) {
                html += '<p class="seccion-sub">Esta asamblea no tiene puntos todavía. Puedes agregar uno en cualquier momento, también durante la asamblea.</p>';
            }
            html += '<div id="puntos-resultados">' + renderPuntos(a) + '</div>';

            if (a.estado !== 'cerrada') {
                html += `<button class="asm-btn ghost sm" id="btn-nuevo-punto" onclick="$('#form-nuevo-punto').slideToggle(120)"><i class="plus icon"></i> Agregar un punto</button>
                    <div id="form-nuevo-punto" class="quorum-box" style="display:none; margin-top:10px;">
                        <input type="text" id="np-titulo" class="asm-input" placeholder="Título del punto">
                        <select id="np-tipo" class="asm-select" style="margin-top:8px;">${Object.keys(TIPOS).map(k => `<option value="${k}">${TIPOS[k]}</option>`).join('')}</select>
                        <textarea id="np-opciones" class="asm-textarea" rows="3" style="margin-top:8px; display:none;" placeholder="Una opción por renglón (mínimo 2)"></textarea>
                        <button class="asm-btn primary sm" style="margin-top:10px;" onclick="guardarNuevoPunto(${a.id})"><i class="check icon"></i> Agregar al orden del día</button>
                    </div>`;
            }

            // Minuta / acuerdos
            html += '<div class="seccion-titulo"><i class="sticky note outline icon"></i> Minuta / Acuerdos</div>';
            html += '<p class="seccion-sub">Escribe aquí los acuerdos y conclusiones de la asamblea. Se incluirán en el acta PDF.</p>';
            html += '<div class="quorum-box">';
            html += '<textarea id="minuta-texto" class="asm-textarea" rows="6" placeholder="Ej.: Se acordó que..."></textarea>';
            html += `<button class="asm-btn primary" style="margin-top:10px;" onclick="guardarMinuta(${a.id})"><i class="save icon"></i> Guardar minuta</button>`;
            html += '</div>';

            $('#modal-content').html(html);
            $('#minuta-texto').val(minutaActual);
            $('#np-tipo').on('change', function () { $('#np-opciones').toggle(['opciones', 'ordenamiento'].includes($(this).val())); });
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

        function renderPuntos(a) {
            const puntos = a.puntos;
            const hayCasasSinAcceso = (a.casas || []).some(c => !c.acceso);
            let html = '';
            puntos.forEach(function (p, i) {
                const votable = p.tipo !== 'informativo';
                const puedeRegistrar = votable && p.tipo !== 'ordenamiento' && a.estado === 'en_curso' && p.estado !== 'cerrado'
                    && (a.modalidad !== 'plataforma' || hayCasasSinAcceso);
                const sinVotos = votable && p.resultados && !p.resultados.total_casas;

                html += `<div class="punto-result"><div class="punto-head-res">
                    <strong>${i + 1}. ${esc(p.titulo)}${p.tipo === 'informativo' ? ' <span class="asm-badge b-convocada">informativo</span>' : ''}</strong>
                    <span style="display:flex; gap:6px; flex-wrap:wrap;">
                        ${votable ? `<button class="asm-btn ghost sm" onclick="togglePunto(${p.id})">${p.estado === 'cerrado' ? '<i class="lock icon"></i> Cerrado' : '<i class="unlock icon"></i> Abierto'}</button>` : ''}
                        ${a.estado !== 'cerrada' && (sinVotos || !votable) ? `<button class="asm-btn danger sm" title="Quitar este punto" onclick="eliminarPunto(${p.id})"><i class="trash icon"></i></button>` : ''}
                    </span>
                </div>`;
                if (p.descripcion) html += `<p class="seccion-sub" style="margin:4px 0 0;">${esc(p.descripcion)}</p>`;

                if (p.tipo === 'informativo') {
                    html += `<div style="margin-top:8px;">
                        <label class="asm-field" for="res-${p.id}">Acuerdo o resumen de este punto</label>
                        <textarea id="res-${p.id}" class="asm-textarea" rows="3" placeholder="Ej.: Se informó a la asamblea que...">${esc(p.resolucion || '')}</textarea>
                        <button class="asm-btn primary sm" style="margin-top:8px;" onclick="guardarResolucion(${p.id})"><i class="save icon"></i> Guardar acuerdo</button>
                    </div>`;
                } else {
                    html += renderResultado(p);
                    if (puedeRegistrar) {
                        html += `<button class="asm-btn primary sm" style="margin-top:10px;" onclick="abrirCapturaMesa(${p.id})"><i class="users icon"></i> Registrar votos en sala</button>`;
                    } else if (p.tipo === 'ordenamiento' && a.estado === 'en_curso' && a.modalidad !== 'plataforma') {
                        html += '<p class="seccion-sub" style="margin-top:8px;">Los puntos de priorización se votan desde la plataforma.</p>';
                    }
                }
                html += '</div>';
            });
            return html;
        }

        function pieOrigen(r) {
            if (!r.origen || !r.origen.mesa) return '';
            return `<div class="res-foot">${r.origen.plataforma} por la plataforma · <strong>${r.origen.mesa} registrados por la mesa en sala</strong></div>`;
        }

        function renderResultado(p) {
            const r = p.resultados;
            if (!r) return '';
            if (r.tipo === 'si_no') {
                const t = r.total_casas || 0;
                const row = (lbl, n, color) => `<div class="res-row"><div class="res-top"><span>${lbl}</span><span>${n}</span></div><div class="res-bar"><div class="res-fill" style="width:${t ? (n/t*100) : 0}%; background:${color};"></div></div></div>`;
                return `<div style="margin-top:8px;">${row('A favor', r.conteo.a_favor, '#17895c')}${row('En contra', r.conteo.en_contra, '#c0432c')}${row('Abstención', r.conteo.abstencion, '#6f6a58')}
                    <div class="res-foot"><span class="asm-badge ${r.aprobado ? 'b-en_curso' : 'b-cerrada'}">${r.aprobado ? 'Aprobado' : 'No aprobado'}</span> · ${t} casa(s)</div>${pieOrigen(r)}</div>`;
            }
            if (r.tipo === 'opciones') {
                let h = '<div style="margin-top:8px;">';
                r.resultados.forEach(o => { h += `<div class="res-row"><div class="res-top"><span>${esc(o.opcion)}</span><span>${o.votos} (${o.pct}%)</span></div><div class="res-bar"><div class="res-fill" style="width:${o.pct}%; background:#0b6b45;"></div></div></div>`; });
                h += `<div class="res-foot">Ganador: <strong>${esc(r.ganador || '—')}</strong> · ${r.total_casas} casa(s)</div>${pieOrigen(r)}</div>`;
                return h;
            }
            if (r.tipo === 'ordenamiento') {
                let h = '<div style="margin-top:8px;">';
                r.ranking.forEach((f, i) => { h += `<div class="res-row"><div class="res-top"><span>${i + 1}. ${esc(f.opcion)}</span><span>${f.puntos} pts</span></div><div class="res-bar"><div class="res-fill" style="width:${f.pct}%; background:#0b6b45;"></div></div></div>`; });
                h += `<div class="res-foot">${r.total_casas} casa(s)</div>${pieOrigen(r)}</div>`;
                return h;
            }
            return '';
        }

        // ---- Registro de votos de la mesa, casa por casa ------------------------------
        window.abrirCapturaMesa = function (puntoId) {
            detenerPoll();
            $.get('{{ url("administrador/asamblea/detalle") }}/' + asambleaActual, function (r) {
                if (!r.success) return;
                asambleaDatos = r.asamblea;
                renderCaptura(r.asamblea, puntoId);
            });
        };

        window.volverTablero = function () {
            verAsamblea(asambleaActual);
        };

        const ETIQ_VOTO = { a_favor: 'A favor', en_contra: 'En contra', abstencion: 'Abstención' };

        function renderCaptura(a, puntoId) {
            const p = a.puntos.find(x => x.id === puntoId);
            if (!p) { volverTablero(); return; }
            const modalidadPlataforma = a.modalidad === 'plataforma';

            let h = `<div class="tablero-acciones">
                <button class="asm-btn ghost sm" onclick="volverTablero()"><i class="arrow left icon"></i> Volver al tablero</button>
                <span class="asm-badge b-${a.estado}">${a.estado.replace('_',' ')}</span>
            </div>`;
            h += '<div class="seccion-titulo"><i class="users icon"></i> Registrar votos en sala</div>';
            h += `<p class="seccion-sub"><strong>${esc(p.titulo)}</strong><br>Marca el voto de cada casa tal como lo expresó en la asamblea. Si una casa no estaba marcada como presente, se marca al guardar su voto. Los votos que ya se emitieron desde la plataforma no se modifican.</p>`;

            if (p.tipo === 'si_no') {
                h += `<div class="quorum-box"><div class="quorum-head"><span>Por unanimidad</span></div>
                    <p class="seccion-sub" style="margin:4px 0 8px;">Le pone el mismo voto a todas las casas <strong>presentes</strong> que aún no votaron en este punto.</p>
                    <div class="tablero-acciones">
                        <button class="asm-btn primary sm" onclick="unanimidad(${a.id}, ${p.id}, 'a_favor')"><i class="thumbs up outline icon"></i> Todos a favor</button>
                        <button class="asm-btn ghost sm" onclick="unanimidad(${a.id}, ${p.id}, 'en_contra')">Todos en contra</button>
                        <button class="asm-btn ghost sm" onclick="unanimidad(${a.id}, ${p.id}, 'abstencion')">Todos se abstienen</button>
                    </div></div>`;
            }

            h += '<input type="search" id="buscar-casa" class="asm-input" placeholder="Buscar casa o nombre..." style="margin:10px 0;">';
            h += '<div id="lista-captura">';
            (a.casas || []).forEach(function (c) {
                const v = (p.votos_casas || {})[c.casa];
                const deMesa = v && v.origen === 'mesa';
                const deLaPlataforma = v && v.origen !== 'mesa';
                const bloqueada = modalidadPlataforma && c.acceso;   // en "solo plataforma" esa casa vota ella misma
                const nombreCasa = 'v-' + c.casa;

                let control = '';
                if (deLaPlataforma) {
                    const txt = v.valor ? ETIQ_VOTO[v.valor] : ((p.opciones.find(o => o.id == v.opcion_id) || {}).opcion || 'votó');
                    control = `<span class="asm-badge b-en_curso">Votó en la plataforma: ${esc(txt)}</span>`;
                } else if (bloqueada) {
                    control = '<span class="seccion-sub" style="margin:0;">Puede votar desde su cuenta.</span>';
                } else if (p.tipo === 'si_no') {
                    control = ['a_favor', 'en_contra', 'abstencion'].map(val => `<label class="opt-voto"><input type="radio" name="${nombreCasa}" value="${val}" ${deMesa && v.valor === val ? 'checked' : ''}> ${ETIQ_VOTO[val]}</label>`).join('');
                } else {
                    control = `<select class="asm-select sel-voto" data-casa="${esc(c.casa)}"><option value="">— sin registrar —</option>${p.opciones.map(o => `<option value="${o.id}" ${deMesa && v.opcion_id == o.id ? 'selected' : ''}>${esc(o.opcion)}</option>`).join('')}</select>`;
                }
                if (deMesa) {
                    control += ` <button class="asm-btn ghost sm" title="Quitar el voto registrado" onclick="quitarVotoMesa(${a.id}, ${p.id}, '${esc(c.casa)}')"><i class="times icon"></i></button>`;
                }

                h += `<div class="casa-voto ${c.presente ? 'presente' : ''}" data-casa="${esc(c.casa)}" data-buscar="${esc((c.casa + ' ' + c.nombre).toLowerCase())}">
                    <div class="cv-info">
                        <strong>Casa ${esc(c.casa)}</strong> · ${esc(c.nombre)}
                        ${c.acceso ? '' : '<span class="chip-sin-acceso" title="No puede entrar a la plataforma"><i class="lock icon"></i> sin acceso</span>'}
                        ${c.presente ? '<span class="chip-presente"><i class="check icon"></i> presente</span>' : ''}
                    </div>
                    <div class="cv-voto">${control}</div>
                </div>`;
            });
            h += '</div>';
            h += `<button class="asm-btn primary block" style="margin-top:14px;" onclick="guardarVotosMesa(${a.id}, ${p.id}, '${p.tipo}')"><i class="save icon"></i> Guardar los votos marcados</button>`;

            $('#modal-content').html(h);
            $('#buscar-casa').on('input', function () {
                const q = $(this).val().toLowerCase().trim();
                $('#lista-captura .casa-voto').each(function () { $(this).toggle(!q || String($(this).data('buscar')).includes(q)); });
            });
        }

        window.guardarVotosMesa = function (id, puntoId, tipo) {
            const votos = [];
            $('#lista-captura .casa-voto').each(function () {
                const casa = String($(this).data('casa'));
                if (tipo === 'si_no') {
                    const val = $(this).find('input[type=radio]:checked').val();
                    if (val) votos.push({ casa, valor: val });
                } else {
                    const op = $(this).find('select.sel-voto').val();
                    if (op) votos.push({ casa, opcion_id: op });
                }
            });
            if (!votos.length) { alertify.error('Marca el voto de al menos una casa.'); return; }

            $.ajax({ url: '{{ url("administrador/asamblea") }}/' + id + '/votos-mesa', type: 'POST',
                data: { _token: '{{ csrf_token() }}', punto_id: puntoId, votos: votos },
                success: function (r) {
                    r.errores && r.errores.length ? alertify.alert('Votos registrados', esc(r.message)) : alertify.success(r.message);
                    abrirCapturaMesa(puntoId);
                },
                error: function (x) { alertify.error(x.responseJSON?.message || 'No se pudieron registrar los votos'); } });
        };

        window.unanimidad = function (id, puntoId, valor) {
            alertify.confirm('Registrar por unanimidad', 'Se registrará «' + ETIQ_VOTO[valor] + '» para todas las casas presentes que aún no han votado en este punto. ¿Continuar?', function () {
                $.ajax({ url: '{{ url("administrador/asamblea") }}/' + id + '/unanimidad', type: 'POST',
                    data: { _token: '{{ csrf_token() }}', punto_id: puntoId, valor: valor },
                    success: function (r) { alertify.success(r.message); abrirCapturaMesa(puntoId); },
                    error: function (x) { alertify.error(x.responseJSON?.message || 'No se pudo registrar'); } });
            }, function () {}).set('labels', { ok: 'Sí, registrar', cancel: 'Cancelar' });
        };

        window.quitarVotoMesa = function (id, puntoId, casa) {
            $.ajax({ url: '{{ url("administrador/asamblea") }}/' + id + '/voto-mesa', type: 'DELETE',
                data: { _token: '{{ csrf_token() }}', punto_id: puntoId, casa: casa },
                success: function (r) { alertify.success(r.message); abrirCapturaMesa(puntoId); },
                error: function (x) { alertify.error(x.responseJSON?.message || 'No se pudo quitar'); } });
        };

        // ---- Puntos, modalidad y acuerdos -------------------------------------------------
        window.guardarNuevoPunto = function (id) {
            const tipo = $('#np-tipo').val();
            const opciones = $('#np-opciones').val().split('\n').map(s => s.trim()).filter(Boolean);
            if (!$('#np-titulo').val().trim()) { alertify.error('Escribe el título del punto.'); return; }
            if (['opciones', 'ordenamiento'].includes(tipo) && opciones.length < 2) { alertify.error('Un punto de opciones necesita al menos 2 opciones.'); return; }
            $.ajax({ url: '{{ url("administrador/asamblea") }}/' + id + '/puntos', type: 'POST',
                data: { _token: '{{ csrf_token() }}', titulo: $('#np-titulo').val().trim(), tipo: tipo, opciones: opciones },
                success: function (r) { alertify.success(r.message); verAsamblea(id); cargarAsambleas(); },
                error: function (x) { alertify.error(x.responseJSON?.message || 'No se pudo agregar el punto'); } });
        };

        window.eliminarPunto = function (puntoId) {
            alertify.confirm('Quitar punto', '¿Quitar este punto del orden del día?', function () {
                $.ajax({ url: '{{ url("administrador/asamblea/punto") }}/' + puntoId, type: 'DELETE', data: { _token: '{{ csrf_token() }}' },
                    success: function (r) { alertify.success(r.message); verAsamblea(asambleaActual); cargarAsambleas(); },
                    error: function (x) { alertify.error(x.responseJSON?.message || 'No se pudo quitar'); } });
            }, function () {}).set('labels', { ok: 'Sí, quitar', cancel: 'Cancelar' });
        };

        window.guardarResolucion = function (puntoId) {
            $.ajax({ url: '{{ url("administrador/asamblea/punto") }}/' + puntoId + '/resolucion', type: 'POST',
                data: { _token: '{{ csrf_token() }}', resolucion: $('#res-' + puntoId).val() },
                success: function (r) { alertify.success(r.message); },
                error: function (x) { alertify.error(x.responseJSON?.message || 'No se pudo guardar'); } });
        };

        window.guardarModalidad = function (id) {
            $.ajax({ url: '{{ url("administrador/asamblea/configurar") }}/' + id, type: 'POST',
                data: { _token: '{{ csrf_token() }}', modalidad: $('#cfg-modalidad').val() },
                success: function (r) { alertify.success(r.message); verAsamblea(id); cargarAsambleas(); },
                error: function (x) { alertify.error(x.responseJSON?.message || 'No se pudo cambiar'); } });
        };

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
                // No se pisa lo que la mesa está escribiendo (acuerdo de un punto, nuevo punto, minuta).
                const escribiendo = document.activeElement && $(document.activeElement).is('textarea, input, select') && $('#modal-content').has(document.activeElement).length;
                $.ajax({ url: '{{ url("administrador/asamblea/detalle") }}/' + id, global: false, success: function (d) {
                    if (!d.success) return;
                    asambleaDatos = d.asamblea;
                    $('#quorum-box').html(renderQuorum(d.asamblea.quorum));
                    if (!escribiendo) $('#puntos-resultados').html(renderPuntos(d.asamblea));
                    if (d.asamblea.estado !== 'en_curso') detenerPoll();
                } });
            }, 8000);
        }
        function detenerPoll() { if (pollTablero) { clearInterval(pollTablero); pollTablero = null; } }

        function esc(t) { return String(t == null ? '' : t).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }
    </script>
</x-app-layout>
