<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ URL::to('/') }}/img/logo-header.png">

    <title>Verificar Correo - {{ config('app.name', 'Laravel') }}</title>
    
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
            animation: bounce 2s ease-in-out infinite;
        }
        
        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-5px); }
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
        
        .info-box {
            background: #ede9fe;
            color: #5b21b6;
            padding: 14px 16px;
            border-radius: 12px;
            margin-bottom: 20px;
            font-size: 0.85rem;
            line-height: 1.5;
            display: flex;
            align-items: flex-start;
            gap: 10px;
        }
        
        .info-box i {
            flex-shrink: 0;
            margin-top: 2px;
        }
        
        .success-box {
            background: #d1fae5;
            color: #065f46;
            padding: 14px 16px;
            border-radius: 12px;
            margin-bottom: 20px;
            font-size: 0.85rem;
            line-height: 1.5;
            display: flex;
            align-items: flex-start;
            gap: 10px;
        }
        
        .success-box i {
            flex-shrink: 0;
            margin-top: 2px;
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
        
        .btn-secondary {
            width: 100%;
            padding: 12px 24px;
            background: transparent;
            color: #6b7280;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-family: inherit;
            margin-top: 10px;
        }
        
        .btn-secondary:hover {
            background: #f9fafb;
            border-color: #667eea;
            color: #667eea;
        }
        
        .btn-secondary:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }
        
        .action-buttons {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-top: 20px;
        }
        
        .divider {
            height: 1px;
            background: #e5e7eb;
            margin: 16px 0;
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
            
            .info-box,
            .success-box {
                font-size: 0.8rem;
                padding: 12px 14px;
            }
            
            .btn-auth,
            .btn-secondary {
                padding: 12px 20px;
                font-size: 0.9rem;
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
                    <i class="envelope open icon"></i>
                </div>
                <h1 class="auth-title">Verifica tu Correo</h1>
                <p class="auth-subtitle">Último paso para activar tu cuenta</p>
            </div>
            
            <div class="auth-body">
                @if (session('status') == 'verification-link-sent')
                <div class="success-box">
                    <i class="check circle icon"></i>
                    <span>Se ha enviado un nuevo enlace de verificación a tu correo electrónico.</span>
                </div>
                @endif

                <div class="info-box">
                    <i class="info circle icon"></i>
                    <span>Gracias por registrarte. Antes de comenzar, ¿podrías verificar tu dirección de correo electrónico haciendo clic en el enlace que te enviamos? Si no recibiste el correo, con gusto te enviaremos otro.</span>
                </div>

                <div class="action-buttons">
                    <form method="POST" action="{{ route('verification.send') }}" id="resendForm">
                        @csrf
                        <button type="submit" id="btnResend" class="btn-auth">
                            <i class="paper plane icon"></i>
                            Reenviar Correo de Verificación
                        </button>
                    </form>
                    
                    <div class="divider"></div>
                    
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn-secondary">
                            <i class="sign out alternate icon"></i>
                            Cerrar Sesión
                        </button>
                    </form>
                </div>
            </div>
            
            <div class="auth-footer">
                <p>¿Ya verificaste tu correo? <a href="{{ route('dashboard') }}">Ir al inicio</a></p>
            </div>
        </div>
    </div>
</body>

<script>
    document.getElementById('resendForm').addEventListener('submit', function() {
        const btn = document.getElementById('btnResend');
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<i class="spinner loading icon"></i> Enviando...';
        }
    });
</script>

</html>
