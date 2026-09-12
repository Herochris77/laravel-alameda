<x-app-layout>
    <style>
        :root {
            --primary: #14b8a6;
            --primary-dark: #0f766e;
            --success: #10b981;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --text-soft: #94a3b8;
            --border: #e2e8f0;
            --surface: #ffffff;
            --soft-bg: #f8fafc;
        }

        .solicitud-create-page {
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
            width: 300px;
            height: 300px;
            border-radius: 999px;
            background: rgba(255,255,255,0.10);
            right: -120px;
            top: -140px;
            pointer-events: none;
        }

        .page-hero::after {
            content: '';
            position: absolute;
            width: 120px;
            height: 120px;
            border-radius: 999px;
            background: rgba(255,255,255,0.08);
            right: 50px;
            bottom: -60px;
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

        .hero-bg-icon i {
            margin: 0 !important;
        }

        .form-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 22px;
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.06);
            overflow: hidden;
        }

        .form-card-header {
            padding: 18px 22px;
            border-bottom: 1px solid #f1f5f9;
            background: linear-gradient(180deg, #ffffff 0%, #fbfdff 100%);
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .form-card-header-icon {
            width: 44px;
            height: 44px;
            border-radius: 14px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            box-shadow: 0 8px 18px rgba(20, 184, 166, 0.20);
        }

        .form-card-header-icon i {
            margin: 0 !important;
            color: white;
            font-size: 1.15rem;
            line-height: 1 !important;
        }

        .form-card-header h3 {
            margin: 0;
            color: var(--text-main);
            font-size: 1.05rem;
            font-weight: 900;
        }

        .form-card-body {
            padding: 24px 22px;
        }

        .form-field {
            margin-bottom: 22px;
        }

        .form-field:last-of-type {
            margin-bottom: 0;
        }

        .form-field-label {
            display: block;
            font-weight: 800;
            color: var(--text-main);
            margin-bottom: 8px;
            font-size: 0.88rem;
        }

        .form-field-label .required {
            color: var(--danger);
        }

        .form-input-wrap {
            position: relative;
            display: flex;
            align-items: center;
        }

        .form-input-wrap i {
            position: absolute;
            left: 14px;
            color: var(--primary);
            z-index: 1;
            pointer-events: none;
            margin: 0 !important;
        }

        .form-input-custom {
            width: 100%;
            padding: 12px 16px 12px 42px;
            border: 2px solid var(--border);
            border-radius: 14px;
            font-size: 0.95rem;
            transition: all 0.2s ease;
            background: white;
            font-family: inherit;
            box-sizing: border-box;
        }

        .form-input-custom:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(20, 184, 166, 0.10);
        }

        .form-input-custom.readonly {
            background: var(--soft-bg);
            color: var(--text-muted);
            cursor: not-allowed;
        }

        .form-textarea {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid var(--border);
            border-radius: 14px;
            font-size: 0.95rem;
            transition: all 0.2s ease;
            background: white;
            font-family: inherit;
            resize: vertical;
            min-height: 140px;
            box-sizing: border-box;
        }

        .form-textarea:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(20, 184, 166, 0.10);
        }

        .form-hint {
            display: block;
            margin-top: 6px;
            font-size: 0.78rem;
            color: var(--text-soft);
            font-weight: 600;
        }

        .form-actions {
            display: flex;
            gap: 12px;
            margin-top: 26px;
            padding-top: 22px;
            border-top: 1px solid #f1f5f9;
        }

        .btn-submit {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 13px 28px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-weight: 700;
            font-size: 0.92rem;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s ease;
            box-shadow: 0 8px 20px rgba(20, 184, 166, 0.24);
            min-height: 48px;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 28px rgba(20, 184, 166, 0.30);
        }

        .btn-submit.loading {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
        }

        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 13px 22px;
            background: var(--soft-bg);
            color: var(--text-muted);
            border: 2px solid var(--border);
            border-radius: 12px;
            font-weight: 700;
            font-size: 0.92rem;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s ease;
            min-height: 48px;
        }

        .btn-back:hover {
            background: #eef2f7;
            border-color: #cbd5e1;
            color: var(--text-main);
        }

        .btn-back i, .btn-submit i {
            margin: 0 !important;
        }

        .dueno-info {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 16px;
            background: rgba(20, 184, 166, 0.06);
            border: 1px solid rgba(20, 184, 166, 0.18);
            border-radius: 14px;
            margin-bottom: 22px;
        }

        .dueno-info i {
            color: var(--primary);
            margin: 0 !important;
            font-size: 1.2rem;
        }

        .dueno-info-text {
            font-size: 0.92rem;
            color: var(--text-muted);
        }

        .dueno-info-text strong {
            color: var(--text-main);
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

            .form-card {
                border-radius: 18px;
            }

            .form-card-header,
            .form-card-body {
                padding: 16px 18px;
            }

            .form-actions {
                flex-direction: column;
            }

            .btn-submit,
            .btn-back {
                width: 100%;
                justify-content: center;
            }
        }
    </style>

    <div class="solicitud-create-page">
        <div class="page-hero">
            <div class="hero-content">
                <div class="hero-icon">
                    <i class="plus icon"></i>
                </div>
                <div class="hero-text">
                    <h1>Nueva Solicitud</h1>
                    <p>Envía una solicitud de permiso al dueño de tu departamento</p>
                </div>
            </div>
            <i class="plus icon hero-bg-icon"></i>
        </div>

        <div class="form-card">
            <div class="form-card-header">
                <div class="form-card-header-icon">
                    <i class="edit outline icon"></i>
                </div>
                <h3>Detalles de la solicitud</h3>
            </div>

            <div class="form-card-body">
                <form id="form-solicitud">
                    @csrf

                    <div class="dueno-info">
                        <i class="user icon"></i>
                        <div class="dueno-info-text">
                            Solicitud para <strong>{{ $dueno->nombre }}</strong> &middot; Casa {{ $dueno->casa }}
                        </div>
                    </div>

                    <div class="form-field">
                        <label class="form-field-label">Título <span class="required">*</span></label>
                        <div class="form-input-wrap">
                            <i class="tag icon"></i>
                            <input type="text" name="titulo" class="form-input-custom" placeholder="Ej: Permiso para mascota" required maxlength="255">
                        </div>
                        <span class="form-hint">Describe brevemente el motivo de tu solicitud</span>
                    </div>

                    <div class="form-field">
                        <label class="form-field-label">Mensaje <span class="required">*</span></label>
                        <textarea name="mensaje" class="form-textarea" placeholder="Explica tu solicitud en detalle..." required maxlength="5000"></textarea>
                        <span class="form-hint">Incluye toda la información relevante para que el dueño pueda tomar una decisión</span>
                    </div>

                    <div class="form-actions">
                        <a href="{{ route('usuario.solicitudes.index') }}" class="btn-back">
                            <i class="arrow left icon"></i> Volver
                        </a>
                        <button type="submit" class="btn-submit" id="btn-enviar">
                            <i class="send icon"></i> Enviar solicitud
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        $('#form-solicitud').submit(function(e) {
            e.preventDefault();
            let btn = $('#btn-enviar');
            btn.addClass('loading disabled');

            $.ajax({
                url: '{{ route("usuario.solicitudes.store") }}',
                method: 'POST',
                data: $(this).serialize(),
                success: function(res) {
                    alertify.success(res.message);
                    setTimeout(() => {
                        window.location.href = '{{ route("usuario.solicitudes.index") }}';
                    }, 1500);
                },
                error: function(xhr) {
                    if (xhr.responseJSON?.message) {
                        alertify.error(xhr.responseJSON.message);
                    } else {
                        alertify.error('Error al enviar la solicitud');
                    }
                },
                complete: function() {
                    btn.removeClass('loading disabled');
                }
            });
        });
    </script>
</x-app-layout>
