<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ URL::to('/') }}/img/logo-header.png">

    <title>{{ config('app.name', 'Laravel') }} - Registro</title>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fomantic-ui@2.9.2/dist/semantic.min.css">
    
    <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/alertify.min.css" />
    <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/themes/default.min.css" />
    <script src="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/alertify.min.js"></script>
    
    <script src="https://cdn.jsdelivr.net/npm/fomantic-ui@2.9.2/dist/semantic.min.js"></script>
    
    <style>
        *, *::before, *::after {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        html, body {
            height: 100%;
            overflow-x: hidden;
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            min-height: 120vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #8b5cf6 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 16px;
        }
        
        .auth-wrapper {
            width: 100%;
            max-width: 480px;
            animation: slideUp 0.5s ease-out;
        }
        
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .auth-card {
            background: white;
            border-radius: 24px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            overflow: hidden;
            width: 100%;
        }
        
        .auth-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 32px 24px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .auth-header::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 60%);
            animation: pulse 4s ease-in-out infinite;
        }
        
        @keyframes pulse {
            0%, 100% { transform: scale(1); opacity: 0.5; }
            50% { transform: scale(1.1); opacity: 0.8; }
        }
        
        .auth-logo {
            position: relative;
            z-index: 1;
            display: flex;
            justify-content: center;
        }
        
        .auth-logo img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid rgba(255,255,255,0.3);
            box-shadow: 0 8px 32px rgba(0,0,0,0.2);
        }
        
        .auth-title {
            position: relative;
            z-index: 1;
            color: white;
            font-size: 1.4rem;
            font-weight: 700;
            margin-top: 16px;
            text-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .auth-subtitle {
            position: relative;
            z-index: 1;
            color: rgba(255,255,255,0.9);
            font-size: 0.85rem;
            margin-top: 4px;
        }
        
        .auth-body {
            padding: 24px 24px;
        }
        
        .form-group {
            margin-bottom: 16px;
        }
        
        .form-label {
            display: block;
            font-weight: 600;
            color: #374151;
            margin-bottom: 6px;
            font-size: 0.85rem;
        }
        
        .form-input {
            width: 100%;
            padding: 12px 14px;
            border: 2px solid #e5e7eb;
            border-radius: 10px;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            background: #f9fafb;
            font-family: inherit;
        }
        
        .form-input:focus {
            outline: none;
            border-color: #667eea;
            background: white;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        .form-input::placeholder {
            color: #9ca3af;
        }
        
        .form-error {
            border-color: #ef4444 !important;
            background: #fef2f2 !important;
        }
        
        .error-message {
            color: #ef4444;
            font-size: 0.75rem;
            margin-top: 4px;
            display: flex;
            align-items: center;
            gap: 4px;
        }
        
        .input-wrapper {
            position: relative;
        }
        
        .input-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            pointer-events: none;
            font-size: 1rem;
        }
        
        .input-wrapper .form-input {
            padding-left: 42px;
        }
        
        .input-wrapper .form-input:focus ~ .input-icon,
        .input-wrapper .form-input:focus + .input-icon {
            color: #667eea;
        }
        
        .input-wrapper-select .form-input {
            padding-left: 42px;
            cursor: pointer;
        }
        
        .password-toggle {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            cursor: pointer;
            padding: 4px;
            transition: color 0.2s;
            background: none;
            border: none;
            font-size: 1rem;
        }
        
        .password-toggle:hover {
            color: #667eea;
        }
        
        .btn-auth {
            width: 100%;
            padding: 14px 24px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-family: inherit;
            margin-top: 8px;
        }
        
        .btn-auth:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
        }
        
        .btn-auth:active {
            transform: translateY(0);
        }
        
        .btn-auth:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
        }
        
        .auth-footer {
            text-align: center;
            padding: 20px 24px;
            background: #f9fafb;
            border-top: 1px solid #e5e7eb;
        }
        
        .auth-footer p {
            color: #6b7280;
            font-size: 0.9rem;
            margin: 0;
        }
        
        .auth-footer a {
            color: #667eea;
            font-weight: 600;
            text-decoration: none;
            transition: color 0.2s;
        }
        
        .auth-footer a:hover {
            color: #764ba2;
            text-decoration: underline;
        }
        
        .two-col {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }
        
        @media (max-width: 520px) {
            body {
                padding: 12px;
                align-items: flex-start;
                padding-top: 30px;
            }
            
            .auth-wrapper {
                max-width: 100%;
            }
            
            .auth-header {
                padding: 24px 16px;
            }
            
            .auth-logo img {
                width: 70px;
                height: 70px;
            }
            
            .auth-title {
                font-size: 1.25rem;
            }
            
            .auth-body {
                padding: 20px 16px;
            }
            
            .form-group {
                margin-bottom: 14px;
            }
            
            .form-input {
                padding: 12px;
                font-size: 16px;
            }
            
            .input-wrapper .form-input {
                padding-left: 40px;
            }
            
            .two-col {
                grid-template-columns: 1fr;
                gap: 14px;
            }
            
            .btn-auth {
                padding: 14px 20px;
            }
        }
        
        @media (max-width: 360px) {
            .auth-header {
                padding: 20px 12px;
            }
            
            .auth-body {
                padding: 16px 12px;
            }
            
            .form-label {
                font-size: 0.8rem;
            }
            
            .auth-footer {
                padding: 16px 12px;
            }
        }
    </style>
</head>

<body>
    <div class="auth-wrapper">
        <div class="auth-card">
            <div class="auth-header">
                <div class="auth-logo">
                    <img src="{{ URL::to('/') }}/img/logo.jpg" alt="Logo Alameda">
                </div>
                <h1 class="auth-title">Crear Cuenta</h1>
                <p class="auth-subtitle">Regístrate en la plataforma Alameda</p>
            </div>
            
            <div class="auth-body">
                <form method="POST" action="{{ route('register') }}" id="registerForm">
                    @csrf
                    
                    <div class="form-group">
                        <label class="form-label">Nombre Completo</label>
                        <div class="input-wrapper">
                            <input type="text" 
                                   name="nombre" 
                                   class="form-input @error('nombre') form-error @enderror" 
                                   value="{{ old('nombre') }}" 
                                   placeholder="Tu nombre completo"
                                   required 
                                   autofocus>
                            <i class="user icon input-icon"></i>
                        </div>
                        @error('nombre')
                        <div class="error-message">
                            <i class="exclamation circle icon"></i>
                            {{ $message }}
                        </div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Correo Electrónico</label>
                        <div class="input-wrapper">
                            <input type="email" 
                                   name="correo"
                                   id="correoInput"
                                   class="form-input @error('correo') form-error @enderror" 
                                   value="{{ old('correo') }}" 
                                   placeholder="correo@ejemplo.com"
                                   autocomplete="off"
                                   autocorrect="off"
                                   autocapitalize="off"
                                   spellcheck="false"
                                   onpaste="return false"
                                   oncopy="return false"
                                   oncut="return false"
                                   ondrop="return false"
                                   oncontextmenu="return false"
                                   required>
                            <i class="envelope icon input-icon"></i>
                        </div>
                        @error('correo')
                        <div class="error-message">
                            <i class="exclamation circle icon"></i>
                            {{ $message }}
                        </div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Confirmar Correo Electrónico</label>
                        <div class="input-wrapper">
                            <input type="email" 
                                   name="correo_confirmation"
                                   id="correoConfirmInput"
                                   class="form-input @error('correo_confirmation') form-error @enderror" 
                                   value="" 
                                   placeholder="Repite tu correo electrónico"
                                   autocomplete="off"
                                   autocorrect="off"
                                   autocapitalize="off"
                                   spellcheck="false"
                                   onpaste="return false"
                                   oncopy="return false"
                                   oncut="return false"
                                   ondrop="return false"
                                   oncontextmenu="return false"
                                   required>
                            <i class="envelope outline icon input-icon"></i>
                        </div>
                        <div class="error-message" id="correoConfirmError" style="display: none;">
                            <i class="exclamation circle icon"></i>
                            Los correos electrónicos no coinciden.
                        </div>
                        @error('correo_confirmation')
                        <div class="error-message">
                            <i class="exclamation circle icon"></i>
                            {{ $message }}
                        </div>
                        @enderror
                    </div>

                    <div class="two-col">
                        <div class="form-group">
                            <label class="form-label">Contraseña</label>
                            <div class="input-wrapper">
                                <input type="password" 
                                       name="pass" 
                                       id="passwordInput"
                                       class="form-input @error('pass') form-error @enderror" 
                                       placeholder="Mín. 8 caracteres"
                                       required>
                                <i class="lock icon input-icon"></i>
                                <button type="button" class="password-toggle" id="togglePassword">
                                    <i class="eye slash icon"></i>
                                </button>
                            </div>
                            @error('pass')
                            <div class="error-message">
                                <i class="exclamation circle icon"></i>
                                {{ $message }}
                            </div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label">Confirmar</label>
                            <div class="input-wrapper">
                                <input type="password" 
                                       name="pass_confirmation" 
                                       id="passwordConfirm"
                                       class="form-input @error('pass_confirmation') form-error @enderror" 
                                       placeholder="Repite tu contraseña"
                                       required>
                                <i class="lock icon input-icon"></i>
                                <button type="button" class="password-toggle" id="togglePasswordConfirm">
                                    <i class="eye slash icon"></i>
                                </button>
                            </div>
                            @error('pass_confirmation')
                            <div class="error-message">
                                <i class="exclamation circle icon"></i>
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                    </div>

                    <div class="two-col">
                        <div class="form-group">
                            <label class="form-label">Celular</label>
                            <div class="input-wrapper">
                                <input type="tel" 
                                       name="celular" 
                                       class="form-input @error('celular') form-error @enderror" 
                                       value="{{ old('celular') }}" 
                                       placeholder="10 dígitos"
                                       pattern="\d{10}"
                                       maxlength="10"
                                       inputmode="numeric"
                                       required>
                                <i class="phone icon input-icon"></i>
                            </div>
                            @error('celular')
                            <div class="error-message">
                                <i class="exclamation circle icon"></i>
                                {{ $message }}
                            </div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label"># de Casa</label>
                            <div class="input-wrapper">
                                <input type="text" 
                                       name="casa" 
                                       class="form-input @error('casa') form-error @enderror" 
                                       value="{{ old('casa') }}" 
                                       placeholder="Ej. 5"
                                       required>
                                <i class="home icon input-icon"></i>
                            </div>
                            @error('casa')
                            <div class="error-message">
                                <i class="exclamation circle icon"></i>
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Tipo de Usuario</label>
                        <div class="input-wrapper input-wrapper-select">
                            <select name="tipo" 
                                    class="form-input @error('tipo') form-error @enderror" 
                                    required>
                                <option value="">Selecciona una opción</option>
                                <option value="dueño" {{ old('tipo') == 'dueño' ? 'selected' : '' }}>Soy propietario (Dueño)</option>
                                <option value="inquilino" {{ old('tipo') == 'inquilino' ? 'selected' : '' }}>Soy inquilino</option>
                            </select>
                            <i class="users icon input-icon"></i>
                        </div>
                        @error('tipo')
                        <div class="error-message">
                            <i class="exclamation circle icon"></i>
                            {{ $message }}
                        </div>
                        @enderror
                    </div>

                    <button type="submit" id="btnSubmit" class="btn-auth">
                        <i class="user plus icon"></i>
                        Crear Mi Cuenta
                    </button>
                </form>
            </div>
            
            <div class="auth-footer">
                <p>¿Ya tienes cuenta? <a href="{{ route('login') }}">Inicia sesión</a></p>
            </div>
        </div>
    </div>
</body>

<script>
    const togglePassword = document.getElementById('togglePassword');
    const togglePasswordConfirm = document.getElementById('togglePasswordConfirm');
    const passwordInput = document.getElementById('passwordInput');
    const passwordConfirm = document.getElementById('passwordConfirm');

    const correoInput = document.getElementById('correoInput');
    const correoConfirmInput = document.getElementById('correoConfirmInput');
    const correoConfirmError = document.getElementById('correoConfirmError');
    
    if (togglePassword && passwordInput) {
        togglePassword.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            this.querySelector('i').classList.toggle('eye');
            this.querySelector('i').classList.toggle('eye slash');
        });
    }
    
    if (togglePasswordConfirm && passwordConfirm) {
        togglePasswordConfirm.addEventListener('click', function() {
            const type = passwordConfirm.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordConfirm.setAttribute('type', type);
            this.querySelector('i').classList.toggle('eye');
            this.querySelector('i').classList.toggle('eye slash');
        });
    }

    function bloquearPegadoYCopiar(input) {
        if (!input) return;

        input.setAttribute('autocomplete', 'off');
        input.setAttribute('autocorrect', 'off');
        input.setAttribute('autocapitalize', 'off');
        input.setAttribute('spellcheck', 'false');

        input.addEventListener('paste', function(event) {
            event.preventDefault();
            alertify.warning('Por seguridad, escribe el correo manualmente.');
        });

        input.addEventListener('copy', function(event) {
            event.preventDefault();
        });

        input.addEventListener('cut', function(event) {
            event.preventDefault();
        });

        input.addEventListener('drop', function(event) {
            event.preventDefault();
            alertify.warning('Por seguridad, escribe el correo manualmente.');
        });

        input.addEventListener('contextmenu', function(event) {
            event.preventDefault();
        });

        input.addEventListener('keydown', function(event) {
            const tecla = event.key.toLowerCase();

            if ((event.ctrlKey || event.metaKey) && (tecla === 'v' || tecla === 'c' || tecla === 'x')) {
                event.preventDefault();

                if (tecla === 'v') {
                    alertify.warning('Por seguridad, escribe el correo manualmente.');
                }
            }
        });
    }

    bloquearPegadoYCopiar(correoInput);
    bloquearPegadoYCopiar(correoConfirmInput);

    function validarCorreos() {
        if (!correoInput || !correoConfirmInput || !correoConfirmError) {
            return true;
        }

        const correo = correoInput.value.trim().toLowerCase();
        const correoConfirmacion = correoConfirmInput.value.trim().toLowerCase();

        if (correoConfirmacion !== '' && correo !== correoConfirmacion) {
            correoInput.classList.add('form-error');
            correoConfirmInput.classList.add('form-error');
            correoConfirmError.style.display = 'flex';
            return false;
        }

        correoInput.classList.remove('form-error');
        correoConfirmInput.classList.remove('form-error');
        correoConfirmError.style.display = 'none';
        return true;
    }

    if (correoInput && correoConfirmInput) {
        correoInput.addEventListener('input', validarCorreos);
        correoConfirmInput.addEventListener('input', validarCorreos);
    }
    
    document.getElementById('registerForm').addEventListener('submit', function(event) {
        const btn = document.getElementById('btnSubmit');

        if (!validarCorreos()) {
            event.preventDefault();

            if (btn) {
                btn.disabled = false;
                btn.innerHTML = '<i class="user plus icon"></i> Crear Mi Cuenta';
            }

            alertify.error('Los correos electrónicos no coinciden. Verifica que estén escritos correctamente.');
            correoConfirmInput.focus();
            return false;
        }

        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<i class="spinner loading icon"></i> Registrando...';
        }
    });
    
    alertify.defaults.transition = "zoom";
    alertify.defaults.theme.ok = "btn btn-primary";
    alertify.defaults.theme.cancel = "btn btn-secondary";
    alertify.defaults.glossary.ok = 'Aceptar';
    alertify.defaults.glossary.cancel = 'Cancelar';
</script>

</html>