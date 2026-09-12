<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ URL::to('/') }}/img/logo-header.png">

    <title>Nueva Contraseña - {{ config('app.name', 'Laravel') }}</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fomantic-ui@2.9.2/dist/semantic.min.css">
    
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
        
        .auth-icon {
            position: relative;
            z-index: 1;
            width: 70px;
            height: 70px;
            margin: 0 auto 16px;
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .auth-icon i {
            font-size: 2rem;
            color: white;
        }
        
        .auth-title {
            position: relative;
            z-index: 1;
            color: white;
            font-size: 1.4rem;
            font-weight: 700;
            text-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .auth-subtitle {
            position: relative;
            z-index: 1;
            color: rgba(255,255,255,0.9);
            font-size: 0.85rem;
            margin-top: 8px;
            line-height: 1.4;
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
        
        .password-requirements {
            background: #f3f4f6;
            border-radius: 8px;
            padding: 12px 14px;
            margin-bottom: 18px;
            font-size: 0.8rem;
            color: #6b7280;
        }
        
        .password-requirements p {
            margin-bottom: 6px;
            font-weight: 600;
            color: #374151;
        }
        
        .password-requirements ul {
            margin: 0;
            padding-left: 18px;
        }
        
        .password-requirements li {
            margin-bottom: 2px;
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
            
            .auth-icon {
                width: 60px;
                height: 60px;
            }
            
            .auth-icon i {
                font-size: 1.7rem;
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
            
            .btn-auth {
                padding: 14px 20px;
            }
            
            .password-requirements {
                font-size: 0.75rem;
                padding: 10px 12px;
            }
            
            .auth-footer {
                padding: 16px;
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
                padding: 14px 12px;
            }
        }
    </style>
</head>

<body>
    <div class="auth-wrapper">
        <div class="auth-card">
            <div class="auth-header">
                <div class="auth-icon">
                    <i class="lock icon"></i>
                </div>
                <h1 class="auth-title">Nueva Contraseña</h1>
                <p class="auth-subtitle">Crea una nueva contraseña segura</p>
            </div>
            
            <div class="auth-body">
                @if (session('status'))
                <div class="alert-box success">
                    <i class="check circle icon"></i>
                    {{ session('status') }}
                </div>
                @endif
                
                @if ($errors->any())
                <div class="alert-box error">
                    <i class="exclamation circle icon"></i>
                    {{ $errors->first() }}
                </div>
                @endif

                <div class="password-requirements">
                    <p><i class="info circle icon"></i> Requisitos de contraseña:</p>
                    <ul>
                        <li>Mínimo 8 caracteres</li>
                        <li>Al menos una letra mayúscula</li>
                        <li>Al menos un número</li>
                    </ul>
                </div>

                <form method="POST" action="{{ route('restart.password.updatePassword') }}" id="resetForm">
                    @csrf
                    
                    <input type="hidden" name="id" value="{{ $id }}">
                    <input type="hidden" name="token" value="{{ $token }}">
                    
                    <div class="form-group">
                        <label class="form-label">Nueva Contraseña</label>
                        <div class="input-wrapper">
                            <input type="password" 
                                   name="pass" 
                                   id="passwordInput"
                                   class="form-input @error('pass') form-error @enderror" 
                                   placeholder="Mínimo 8 caracteres"
                                   required 
                                   autocomplete="new-password"
                                   autofocus>
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
                        <label class="form-label">Confirmar Contraseña</label>
                        <div class="input-wrapper">
                            <input type="password" 
                                   name="passconfirm" 
                                   id="passwordConfirmInput"
                                   class="form-input @error('passconfirm') form-error @enderror" 
                                   placeholder="Repite la contraseña"
                                   required 
                                   autocomplete="new-password">
                            <i class="lock icon input-icon"></i>
                            <button type="button" class="password-toggle" id="togglePasswordConfirm">
                                <i class="eye slash icon"></i>
                            </button>
                        </div>
                        @error('passconfirm')
                        <div class="error-message">
                            <i class="exclamation circle icon"></i>
                            {{ $message }}
                        </div>
                        @enderror
                    </div>

                    <button type="submit" id="btnSubmit" class="btn-auth">
                        <i class="save icon"></i>
                        Actualizar Contraseña
                    </button>
                </form>
            </div>
            
            <div class="auth-footer">
                <p><a href="{{ route('login') }}"><i class="arrow left icon"></i> Volver al inicio de sesión</a></p>
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
    
    const togglePasswordConfirm = document.getElementById('togglePasswordConfirm');
    const passwordConfirmInput = document.getElementById('passwordConfirmInput');
    
    if (togglePasswordConfirm && passwordConfirmInput) {
        togglePasswordConfirm.addEventListener('click', function() {
            const type = passwordConfirmInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordConfirmInput.setAttribute('type', type);
            this.querySelector('i').classList.toggle('eye');
            this.querySelector('i').classList.toggle('eye slash');
        });
    }
    
    document.getElementById('resetForm').addEventListener('submit', function() {
        const btn = document.getElementById('btnSubmit');
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<i class="spinner loading icon"></i> Actualizando...';
        }
    });
</script>

</html>
