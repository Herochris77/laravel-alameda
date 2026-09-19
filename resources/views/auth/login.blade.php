<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ URL::to('/') }}/img/logo-header.png">

    <title>{{ config('app.name', 'Laravel') }}</title>
    
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
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #8b5cf6 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 16px;
        }
        
        .auth-wrapper {
            width: 100%;
            max-width: 420px;
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
            padding: 28px 24px;
        }
        
        .form-group {
            margin-bottom: 18px;
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
        
        .checkbox-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 10px;
        }
        
        .checkbox-label {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            font-size: 0.85rem;
            color: #6b7280;
        }
        
        .checkbox-label input {
            width: 16px;
            height: 16px;
            cursor: pointer;
            accent-color: #667eea;
        }
        
        .forgot-link {
            font-size: 0.85rem;
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.2s;
        }
        
        .forgot-link:hover {
            color: #764ba2;
            text-decoration: underline;
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

        .auth-legal {
            margin-top: 10px !important;
            font-size: 0.8rem !important;
        }

        .auth-legal a {
            color: #6b7280;
            font-weight: 500;
        }

        .auth-legal span {
            color: #d1d5db;
            margin: 0 6px;
        }
        
        .alert-box {
            padding: 12px 14px;
            border-radius: 10px;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 0.85rem;
        }
        
        .alert-box.success {
            background: #d1fae5;
            color: #065f46;
        }
        
        .alert-box.error {
            background: #fee2e2;
            color: #991b1b;
        }
        
        .two-col {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }
        
        @media (max-width: 480px) {
            body {
                padding: 12px;
                align-items: flex-start;
                padding-top: 40px;
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
            
            .checkbox-row {
                flex-direction: column;
                align-items: flex-start;
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
                <h1 class="auth-title">Iniciar Sesión</h1>
                <p class="auth-subtitle">Accede a tu cuenta del condominio</p>
            </div>
            
            <div class="auth-body">
                @if (session('registro_exitoso'))
                <div class="alert-box success">
                    <i class="check circle icon"></i>
                    Registro exitoso. Revisa tu correo para verificar tu cuenta.
                </div>
                @endif
                
                @if (session('registro_exitoso_inquilino'))
                <div class="alert-box success">
                    <i class="check circle icon"></i>
                    Registro exitoso. Contacta al al dueño para la aprobación.
                </div>
                @endif
                
                @if (session('usuario_bloqueado'))
                <div class="alert-box error">
                    <i class="exclamation circle icon"></i>
                    Tu usuario está bloqueado. Contacta a la administración.
                </div>
                @endif
                
                @if (session('usuario_pendiente'))
                <div class="alert-box error">
                    <i class="clock icon"></i>
                    Tu acceso está pendiente de aprobación.
                </div>
                @endif
                
                @if (session('usuario_novalidado'))
                <div class="alert-box error">
                    <i class="mail icon"></i>
                    Verifica tu correo para activar tu cuenta.
                </div>
                @endif
                
                @if (session('status'))
                <div class="alert-box success">
                    <i class="check circle icon"></i>
                    {{ session('status') }}
                </div>
                @endif

                <form method="POST" action="{{ route('login') }}" id="loginForm">
                    @csrf
                    
                    <div class="form-group">
                        <label class="form-label">Correo Electrónico</label>
                        <div class="input-wrapper">
                            <input type="email" 
                                   name="email" 
                                   class="form-input @error('email') form-error @enderror" 
                                   value="{{ old('email') }}" 
                                   placeholder="correo@ejemplo.com"
                                   required 
                                   autofocus>
                            <i class="envelope icon input-icon"></i>
                        </div>
                        @error('email')
                        <div class="error-message">
                            <i class="exclamation circle icon"></i>
                            {{ $message }}
                        </div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Contraseña</label>
                        <div class="input-wrapper">
                            <input type="password" 
                                   name="password" 
                                   id="passwordInput"
                                   class="form-input @error('password') form-error @enderror" 
                                   placeholder="Tu contraseña"
                                   required>
                            <i class="lock icon input-icon"></i>
                            <button type="button" class="password-toggle" id="togglePassword">
                                <i class="eye slash icon"></i>
                            </button>
                        </div>
                        @error('password')
                        <div class="error-message">
                            <i class="exclamation circle icon"></i>
                            {{ $message }}
                        </div>
                        @enderror
                    </div>

                    <div class="checkbox-row">
                        <label class="checkbox-label">
                            <input type="checkbox" name="remember" id="remember">
                            Recordar sesión
                        </label>
                        
                        @if (Route::has('password.request'))
                        <a href="{{ route('restart.password.index') }}" class="forgot-link">
                            ¿Olvidaste tu contraseña?
                        </a>
                        @endif
                    </div>

                    <button type="submit" id="btnSubmit" class="btn-auth">
                        <i class="sign-in alt icon"></i>
                        Iniciar Sesión
                    </button>
                </form>
            </div>
            
            <div class="auth-footer">
                <p>¿No tienes cuenta? <a href="{{ route('register') }}">Regístrate aquí</a></p>

                {{--
                    El aviso tiene que estar ANTES de entrar, no escondido
                    dentro del sistema: quien va a registrarse necesita poder
                    leer qué se hace con sus datos sin tener que dar de alta
                    una cuenta primero.
                --}}
                <p class="auth-legal">
                    <a href="{{ route('legal.privacidad') }}">Aviso de Privacidad</a>
                    <span>·</span>
                    <a href="{{ route('legal.terminos') }}">Términos de Uso</a>
                </p>
            </div>
        </div>
    </div>
</body>

<script>
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('passwordInput');
    
    if (togglePassword && passwordInput) {
        togglePassword.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            this.querySelector('i').classList.toggle('eye');
            this.querySelector('i').classList.toggle('eye slash');
        });
    }
    
    document.getElementById('loginForm').addEventListener('submit', function() {
        const btn = document.getElementById('btnSubmit');
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<i class="spinner loading icon"></i> Ingresando...';
        }
    });
    
    alertify.defaults.transition = "zoom";
    alertify.defaults.theme.ok = "btn btn-primary";
    alertify.defaults.theme.cancel = "btn btn-secondary";
    alertify.defaults.glossary.ok = 'Aceptar';
    alertify.defaults.glossary.cancel = 'Cancelar';
</script>

</html>
