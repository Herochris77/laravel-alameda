<x-app-layout>
    <style>
        :root {
            --primary: #14b8a6;
            --primary-dark: #0f766e;
            --success: #10b981;
            --success-dark: #059669;
            --danger: #ef4444;
            --warning: #f59e0b;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --text-soft: #94a3b8;
            --border: #e2e8f0;
            --surface: #ffffff;
            --soft-bg: #f8fafc;
        }

        .solicitudes-page {
            width: 100%;
            padding-bottom: 24px;
        }

        .solicitudes-hero {
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

        .solicitudes-hero::before {
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

        .solicitudes-hero::after {
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

        .solicitudes-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 22px;
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.06);
            overflow: hidden;
        }

        .solicitudes-card-header {
            padding: 18px 22px;
            border-bottom: 1px solid #f1f5f9;
            background: linear-gradient(180deg, #ffffff 0%, #fbfdff 100%);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
        }

        .solicitudes-card-header h3 {
            margin: 0;
            color: var(--text-main);
            font-size: 1.05rem;
            font-weight: 900;
        }

        .solicitudes-card-body {
            padding: 0;
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
            vertical-align: top;
        }

        .solicitudes-table tbody tr:last-child td {
            border-bottom: none;
        }

        .solicitud-titulo {
            font-weight: 700;
            color: var(--text-main);
            margin-bottom: 4px;
        }

        .solicitud-accordion {
            margin-top: 6px;
        }

        .solicitud-accordion .title {
            color: var(--primary);
            font-size: 0.82rem;
            font-weight: 700;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 4px 0;
            transition: color 0.15s ease;
        }

        .solicitud-accordion .title:hover {
            color: var(--primary-dark);
        }

        .solicitud-accordion .title i {
            transition: transform 0.2s ease;
        }

        .solicitud-accordion .title.active i {
            transform: rotate(90deg);
        }

        .solicitud-accordion .content {
            padding: 12px 16px;
            background: var(--soft-bg);
            border-radius: 12px;
            margin-top: 8px;
            color: var(--text-muted);
            font-size: 0.9rem;
            line-height: 1.55;
            white-space: pre-wrap;
            display: none;
        }

        .solicitud-accordion .content.active {
            display: block;
        }

        .estado-label {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            border-radius: 999px;
            font-size: 0.78rem;
            font-weight: 800;
            white-space: nowrap;
        }

        .estado-label.pendiente {
            background: #fef3c7;
            color: #92400e;
        }

        .estado-label.aprobado {
            background: #d1fae5;
            color: #065f46;
        }

        .estado-label.rechazado {
            background: #fee2e2;
            color: #991b1b;
        }

        .estado-label i {
            margin: 0 !important;
            font-size: 0.8rem;
        }

        .fecha-cell {
            color: var(--text-muted);
            font-size: 0.88rem;
            white-space: nowrap;
        }

        .fecha-cell .subtle {
            color: var(--text-soft);
            font-size: 0.82rem;
        }

        .empty-state-solicitudes {
            text-align: center;
            padding: 60px 20px;
        }

        .empty-state-solicitudes .empty-icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 18px;
            font-size: 2rem;
            color: var(--text-soft);
        }

        .empty-state-solicitudes .empty-icon i {
            margin: 0 !important;
        }

        .empty-state-solicitudes h3 {
            color: var(--text-main);
            font-size: 1.1rem;
            margin: 0 0 6px;
            font-weight: 800;
        }

        .empty-state-solicitudes p {
            color: var(--text-muted);
            font-size: 0.92rem;
            margin: 0 0 20px;
        }

        .badge-count {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 24px;
            height: 24px;
            padding: 0 7px;
            border-radius: 999px;
            background: var(--primary);
            color: white;
            font-size: 0.72rem;
            font-weight: 800;
        }

        .btn-crear {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 24px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-weight: 700;
            font-size: 0.9rem;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s ease;
            box-shadow: 0 8px 20px rgba(20, 184, 166, 0.24);
        }

        .btn-crear:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 28px rgba(20, 184, 166, 0.30);
            color: white;
        }

        .btn-crear i {
            margin: 0 !important;
        }

        .section-header-wrap {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            flex-wrap: wrap;
        }

        .section-header-wrap .left {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        @media (max-width: 768px) {
            .solicitudes-hero {
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

            .solicitudes-card {
                border-radius: 18px;
            }

            .solicitudes-card-header {
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

            .section-header-wrap {
                flex-direction: column;
                align-items: flex-start;
            }

            .btn-crear {
                width: 100%;
                justify-content: center;
            }
        }
    </style>

    <div class="solicitudes-page">
        <div class="solicitudes-hero">
            <div class="hero-content">
                <div class="hero-icon">
                    <i class="file alternate outline icon"></i>
                </div>
                <div>
                    <h1>Mis Solicitudes</h1>
                    <p>Gestiona tus solicitudes de permiso enviadas al dueño</p>
                </div>
            </div>
            <i class="file alternate outline icon hero-bg-icon"></i>
        </div>

        @if(session('error'))
            <div class="ui warning message" style="margin-bottom:20px;border-radius:14px;">
                <i class="close icon"></i>
                {{ session('error') }}
            </div>
        @endif

        <div class="solicitudes-card">
            <div class="solicitudes-card-header">
                <div class="section-header-wrap">
                    <div class="left">
                        <h3>Historial de solicitudes</h3>
                        <span class="badge-count">{{ $solicitudes->count() }}</span>
                    </div>
                    <a href="{{ route('usuario.solicitudes.create') }}" class="btn-crear">
                        <i class="plus icon"></i> Nueva solicitud
                    </a>
                </div>
            </div>

            <div class="solicitudes-card-body">
                @if($solicitudes->isEmpty())
                    <div class="empty-state-solicitudes">
                        <div class="empty-icon">
                            <i class="file alternate outline icon"></i>
                        </div>
                        <h3>No hay solicitudes aún</h3>
                        <p>Envía tu primera solicitud de permiso al dueño de tu departamento.</p>
                        <a href="{{ route('usuario.solicitudes.create') }}" class="btn-crear">
                            <i class="plus icon"></i> Crear primera solicitud
                        </a>
                    </div>
                @else
                    <table class="solicitudes-table">
                        <thead>
                            <tr>
                                <th>Solicitud</th>
                                <th>Estado</th>
                                <th>Enviada</th>
                                <th>Respuesta</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($solicitudes as $s)
                                <tr>
                                    <td data-label="Solicitud">
                                        <div class="solicitud-titulo">{{ $s->titulo }}</div>
                                        <div class="solicitud-accordion">
                                            <div class="title" onclick="toggleMensaje(this)">
                                                <i class="chevron right icon"></i> Ver mensaje
                                            </div>
                                            <div class="content">{{ $s->mensaje }}</div>
                                        </div>
                                    </td>
                                    <td data-label="Estado">
                                        @if($s->estado === 'pendiente')
                                            <span class="estado-label pendiente"><i class="clock icon"></i> Pendiente</span>
                                        @elseif($s->estado === 'aprobado')
                                            <span class="estado-label aprobado"><i class="check circle icon"></i> Aprobado</span>
                                        @else
                                            <span class="estado-label rechazado"><i class="times circle icon"></i> Rechazado</span>
                                        @endif
                                    </td>
                                    <td data-label="Enviada" class="fecha-cell">{{ $s->created_at->format('d/m/Y') }} <span class="subtle">{{ $s->created_at->format('H:i') }}</span></td>
                                    <td data-label="Respuesta" class="fecha-cell">
                                        @if($s->fecha_respuesta)
                                            {{ $s->fecha_respuesta->format('d/m/Y') }} <span class="subtle">{{ $s->fecha_respuesta->format('H:i') }}</span>
                                        @else
                                            <span class="subtle">—</span>
                                        @endif
                                    </td>
                                    <td data-label="Acción">
                                        @if($s->estado === 'pendiente')
                                            <button class="ui mini circular icon button btn-eliminar-solicitud" data-id="{{ $s->id }}" title="Eliminar solicitud" style="background:transparent;color:#ef4444;border:1px solid #fee2e2;">
                                                <i class="trash alternate icon"></i>
                                            </button>
                                        @else
                                            <span class="subtle" style="color:var(--text-soft);font-size:0.82rem;">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div>

    <script>
        function toggleMensaje(el) {
            el.classList.toggle('active');
            const content = el.nextElementSibling;
            if (content) {
                content.classList.toggle('active');
            }
        }

        $('.message .close').on('click', function() {
            $(this).closest('.message').transition('fade');
        });

        var baseURLEliminar = '{{ route("usuario.solicitudes.eliminar", ["id" => ":id"]) }}';

        $(document).on('click', '.btn-eliminar-solicitud', function() {
            var id = $(this).data('id');
            var $btn = $(this);

            alertify.confirm(
                'Eliminar solicitud',
                'El dueño será notificado de que eliminaste esta solicitud.',
                function() {
                    $btn.prop('disabled', true);
                    mostrarLoaderPantalla();

                    $.ajax({
                        url: baseURLEliminar.replace(':id', id),
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(res) {
                            ocultarLoaderPantalla();
                            alertify.success(res.message || 'Solicitud eliminada.');
                            setTimeout(function() {
                                location.reload();
                            }, 1200);
                        },
                        error: function(xhr) {
                            ocultarLoaderPantalla();
                            $btn.prop('disabled', false);
                            var msg = 'Error al eliminar la solicitud.';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                msg = xhr.responseJSON.message;
                            }
                            alertify.error(msg);
                        }
                    });
                },
                function() {
                    $btn.prop('disabled', false);
                }
            ).set('labels', {
                ok: 'Sí, eliminar',
                cancel: 'Cancelar'
            });
        });
    </script>
</x-app-layout>
