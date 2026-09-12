<x-app-layout>
    <style>
        :root {
            --primary: #667eea;
            --primary-dark: #764ba2;
            --success: #10b981;
            --success-dark: #059669;
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

        .dueno-solicitudes-page {
            width: 100%;
            padding-bottom: 24px;
        }

        .page-hero {
            background:
                radial-gradient(circle at top right, rgba(255,255,255,0.24), transparent 34%),
                radial-gradient(circle at bottom left, rgba(255,255,255,0.14), transparent 30%),
                linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            border-radius: 26px;
            padding: 26px;
            color: white;
            margin-bottom: 24px;
            position: relative;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.08);
        }

        .page-hero::before {
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

        .page-hero::after {
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

        .hero-text h1 {
            margin: 0;
            font-size: clamp(1.4rem, 2.5vw, 1.9rem);
            font-weight: 800;
            letter-spacing: -0.02em;
        }

        .hero-text p {
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

        .section-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 22px;
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.06);
            overflow: hidden;
            margin-bottom: 22px;
        }

        .section-card:last-child {
            margin-bottom: 0;
        }

        .section-card-header {
            padding: 18px 22px;
            border-bottom: 1px solid #f1f5f9;
            background: linear-gradient(180deg, #ffffff 0%, #fbfdff 100%);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
        }

        .section-card-header .left {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .section-card-header .left i {
            margin: 0 !important;
            color: var(--primary);
            font-size: 1.15rem;
        }

        .section-card-header h3 {
            margin: 0;
            color: var(--text-main);
            font-size: 1.05rem;
            font-weight: 900;
        }

        .section-card-body {
            padding: 0;
        }

        .badge-count {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 26px;
            height: 26px;
            padding: 0 8px;
            border-radius: 999px;
            font-size: 0.75rem;
            font-weight: 800;
        }

        .badge-count.warning {
            background: #fef3c7;
            color: #92400e;
        }

        .badge-count.success {
            background: #d1fae5;
            color: #065f46;
        }

        .solicitudes-table {
            width: 100%;
            border-collapse: collapse;
        }

        .solicitudes-table thead th {
            padding: 14px 22px;
            font-size: 0.76rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: var(--text-soft);
            background: var(--soft-bg);
            border-bottom: 1px solid #eef2f7;
            text-align: left;
            white-space: nowrap;
        }

        .solicitudes-table tbody tr {
            transition: background 0.15s ease;
        }

        .solicitudes-table tbody tr:hover {
            background: #fafbff;
        }

        .solicitudes-table tbody td {
            padding: 16px 22px;
            border-bottom: 1px solid #f1f5f9;
            color: var(--text-main);
            font-size: 0.92rem;
            vertical-align: middle;
        }

        .solicitudes-table tbody tr:last-child td {
            border-bottom: none;
        }

        .inquilino-cell {
            display: flex;
            flex-direction: column;
        }

        .inquilino-cell strong {
            font-size: 0.95rem;
        }

        .inquilino-cell small {
            color: var(--text-soft);
            font-size: 0.8rem;
            margin-top: 1px;
        }

        .mensaje-preview {
            max-width: 320px;
            white-space: pre-wrap;
            color: var(--text-muted);
            font-size: 0.88rem;
            line-height: 1.45;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .acciones-cell {
            display: flex;
            gap: 8px;
            flex-wrap: nowrap;
        }

        .btn-aprobar, .btn-rechazar, .btn-ver-mensaje {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 9px 16px;
            border: none;
            border-radius: 10px;
            font-weight: 700;
            font-size: 0.82rem;
            cursor: pointer;
            transition: all 0.2s ease;
            white-space: nowrap;
        }

        .btn-aprobar {
            background: #d1fae5;
            color: #065f46;
        }

        .btn-aprobar:hover {
            background: #a7f3d0;
            transform: translateY(-2px);
        }

        .btn-rechazar {
            background: #fee2e2;
            color: #991b1b;
        }

        .btn-rechazar:hover {
            background: #fecaca;
            transform: translateY(-2px);
        }

        .btn-ver-mensaje {
            background: var(--soft-bg);
            color: var(--text-muted);
            border: 1px solid var(--border);
        }

        .btn-ver-mensaje:hover {
            background: #eef2f7;
            color: var(--text-main);
        }

        .btn-aprobar i, .btn-rechazar i, .btn-ver-mensaje i {
            margin: 0 !important;
        }

        .estado-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 5px 11px;
            border-radius: 999px;
            font-size: 0.78rem;
            font-weight: 800;
        }

        .estado-badge.aprobado {
            background: #d1fae5;
            color: #065f46;
        }

        .estado-badge.rechazado {
            background: #fee2e2;
            color: #991b1b;
        }

        .estado-badge i {
            margin: 0 !important;
            font-size: 0.75rem;
        }

        .empty-state {
            text-align: center;
            padding: 50px 20px;
        }

        .empty-state .empty-icon {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            background: #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 16px;
            font-size: 1.8rem;
            color: var(--text-soft);
        }

        .empty-state .empty-icon i {
            margin: 0 !important;
        }

        .empty-state h3 {
            color: var(--text-main);
            font-size: 1rem;
            margin: 0 0 4px;
            font-weight: 800;
        }

        .empty-state p {
            color: var(--text-muted);
            font-size: 0.9rem;
            margin: 0;
        }

        .modal-mensaje-content {
            background: var(--soft-bg);
            padding: 20px;
            border-radius: 14px;
            white-space: pre-wrap;
            line-height: 1.6;
            font-size: 0.95rem;
            color: var(--text-main);
        }

        .modal-mensaje-meta {
            margin-bottom: 16px;
            color: var(--text-muted);
            font-size: 0.88rem;
        }

        .btn-volver {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            background: var(--soft-bg);
            color: var(--text-muted);
            border: 2px solid var(--border);
            border-radius: 12px;
            font-weight: 700;
            font-size: 0.88rem;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .btn-volver:hover {
            background: #eef2f7;
            border-color: #cbd5e1;
            color: var(--text-main);
        }

        .btn-volver i {
            margin: 0 !important;
        }

        @media (max-width: 968px) {
            .acciones-cell {
                flex-direction: column;
            }

            .mensaje-preview {
                max-width: 200px;
            }
        }

        @media (max-width: 768px) {
            .page-hero {
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

            .section-card {
                border-radius: 18px;
            }

            .section-card-header {
                padding: 16px 18px;
            }

            .solicitudes-table thead {
                display: none;
            }

            .solicitudes-table tbody td {
                display: block;
                padding: 12px 18px;
                border-bottom: none;
            }

            .solicitudes-table tbody tr {
                display: block;
                padding: 0;
                border-bottom: 2px solid #f1f5f9;
            }

            .solicitudes-table tbody tr:last-child {
                border-bottom: none;
            }

            .solicitudes-table tbody td::before {
                content: attr(data-label);
                display: block;
                font-size: 0.72rem;
                font-weight: 800;
                text-transform: uppercase;
                letter-spacing: 0.04em;
                color: var(--text-soft);
                margin-bottom: 4px;
            }

            .mensaje-preview {
                max-width: none;
            }

            .acciones-cell {
                flex-direction: row;
                flex-wrap: wrap;
            }
        }
    </style>

    <div class="dueno-solicitudes-page">
        <div class="page-hero">
            <div class="hero-content">
                <div class="hero-icon">
                    <i class="file alternate outline icon"></i>
                </div>
                <div class="hero-text">
                    <h1>Solicitudes de Inquilinos</h1>
                    <p>Revisa y responde las solicitudes de permiso de tus inquilinos</p>
                </div>
            </div>
            <i class="file alternate outline icon hero-bg-icon"></i>
        </div>

        <div class="section-card">
            <div class="section-card-header">
                <div class="left">
                    <i class="clock outline icon"></i>
                    <h3>Solicitudes pendientes</h3>
                    <span class="badge-count warning">{{ $pendientes->count() }}</span>
                </div>
                <a href="{{ route('usuario.perfil.index') }}" class="btn-volver">
                    <i class="arrow left icon"></i> Volver al perfil
                </a>
            </div>

            <div class="section-card-body">
                @if($pendientes->isEmpty())
                    <div class="empty-state">
                        <div class="empty-icon">
                            <i class="check circle outline icon"></i>
                        </div>
                        <h3>No hay solicitudes pendientes</h3>
                        <p>Cuando un inquilino envíe una solicitud, aparecerá aquí.</p>
                    </div>
                @else
                    <table class="solicitudes-table">
                        <thead>
                            <tr>
                                <th>Inquilino</th>
                                <th>Título</th>
                                <th>Mensaje</th>
                                <th>Recibida</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pendientes as $s)
                                <tr>
                                    <td data-label="Inquilino">
                                        <div class="inquilino-cell">
                                            <strong>{{ $s->inquilino->nombre }}</strong>
                                            <small>Casa {{ $s->inquilino->casa }}</small>
                                        </div>
                                    </td>
                                    <td data-label="Título"><strong>{{ $s->titulo }}</strong></td>
                                    <td data-label="Mensaje">
                                        <div class="mensaje-preview">{{ $s->mensaje }}</div>
                                        <button class="btn-ver-mensaje" style="margin-top:6px" onclick="verMensaje('{{ addslashes($s->inquilino->nombre) }}', '{{ addslashes($s->titulo) }}', `{{ addslashes($s->mensaje) }}`, '{{ $s->created_at->format('d/m/Y H:i') }}')">
                                            <i class="eye icon"></i> Leer completo
                                        </button>
                                    </td>
                                    <td data-label="Recibida" class="fecha-cell" style="color:var(--text-muted);font-size:0.88rem;white-space:nowrap">
                                        {{ $s->created_at->format('d/m/Y') }}<br><span style="color:var(--text-soft);font-size:0.82rem">{{ $s->created_at->format('H:i') }}</span>
                                    </td>
                                    <td data-label="Acciones">
                                        <div class="acciones-cell">
                                            <button class="btn-aprobar btn-responder" data-id="{{ $s->id }}" data-estado="aprobado">
                                                <i class="check icon"></i> Aprobar
                                            </button>
                                            <button class="btn-rechazar btn-responder" data-id="{{ $s->id }}" data-estado="rechazado">
                                                <i class="times icon"></i> Rechazar
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>

        @if($historial->isNotEmpty())
            <div class="section-card">
                <div class="section-card-header">
                    <div class="left">
                        <i class="history icon"></i>
                        <h3>Historial</h3>
                        <span class="badge-count success">{{ $historial->count() }}</span>
                    </div>
                </div>

                <div class="section-card-body">
                    <table class="solicitudes-table">
                        <thead>
                            <tr>
                                <th>Inquilino</th>
                                <th>Título</th>
                                <th>Estado</th>
                                <th>Enviada</th>
                                <th>Respondida</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($historial as $s)
                                <tr>
                                    <td data-label="Inquilino">
                                        <div class="inquilino-cell">
                                            <strong>{{ $s->inquilino->nombre }}</strong>
                                            <small>Casa {{ $s->inquilino->casa }}</small>
                                        </div>
                                    </td>
                                    <td data-label="Título">{{ $s->titulo }}</td>
                                    <td data-label="Estado">
                                        @if($s->estado === 'aprobado')
                                            <span class="estado-badge aprobado"><i class="check circle icon"></i> Aprobado</span>
                                        @else
                                            <span class="estado-badge rechazado"><i class="times circle icon"></i> Rechazado</span>
                                        @endif
                                    </td>
                                    <td data-label="Enviada" style="color:var(--text-muted);font-size:0.88rem;white-space:nowrap">
                                        {{ $s->created_at->format('d/m/Y') }}<br><span style="color:var(--text-soft);font-size:0.82rem">{{ $s->created_at->format('H:i') }}</span>
                                    </td>
                                    <td data-label="Respondida" style="color:var(--text-muted);font-size:0.88rem;white-space:nowrap">
                                        @if($s->fecha_respuesta)
                                            {{ $s->fecha_respuesta->format('d/m/Y') }}<br><span style="color:var(--text-soft);font-size:0.82rem">{{ $s->fecha_respuesta->format('H:i') }}</span>
                                        @else
                                            <span style="color:var(--text-soft)">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>

    <div class="ui modal" id="modal-ver-mensaje">
        <div class="header" style="background:linear-gradient(135deg,#667eea,#764ba2);color:white">
            <i class="file alternate outline icon"></i> Mensaje de solicitud
        </div>
        <div class="content">
            <div class="modal-mensaje-meta" id="modal-meta"></div>
            <div class="modal-mensaje-content" id="modal-mensaje-texto"></div>
        </div>
        <div class="actions">
            <button class="ui button" onclick="$('#modal-ver-mensaje').modal('hide')">Cerrar</button>
        </div>
    </div>

    <script>
        function verMensaje(inquilino, titulo, mensaje, fecha) {
            $('#modal-meta').html('<strong>De:</strong> ' + inquilino + ' &middot; <strong>Título:</strong> ' + titulo + ' &middot; <strong>Recibida:</strong> ' + fecha);
            $('#modal-mensaje-texto').text(mensaje);
            $('#modal-ver-mensaje').modal('show');
        }

        $(document).on('click', '.btn-responder', function() {
            const btn = $(this);
            const id = btn.data('id');
            const estado = btn.data('estado');
            const texto = estado === 'aprobado' ? 'aprobar' : 'rechazar';
            const textoP = estado === 'aprobado' ? 'aprobada' : 'rechazada';

            alertify.confirm(
                textoP.charAt(0).toUpperCase() + textoP.slice(1),
                '¿Estás seguro de ' + texto + ' esta solicitud?',
                function() {
                    mostrarLoaderPantalla();
                    setTimeout(() => {
                        $.ajax({
                            url: "{{ route('usuario.solicitudes.responder', ['id' => ':id']) }}".replace(':id', id),
                            method: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                                estado: estado
                            },
                            success: function(res) {
                                alertify.success(res.message);
                                setTimeout(function() {
                                    location.reload();
                                }, 1500);
                            },
                            error: function() {
                                alertify.error('Error al responder la solicitud');
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
    </script>
</x-app-layout>
