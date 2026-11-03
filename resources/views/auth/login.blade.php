<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#0b6b45">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ URL::to('/') }}/img/logo-header.png">

    <title>Iniciar sesión · Alameda</title>
    
    {{-- Fuente, jQuery, Fomantic y Alertify: locales, con respaldo a CDN. --}}
    @include('layouts.partials.vendor-core')
    {{-- Sistema de diseño "Casa Alameda" (incluye los estilos de acceso). --}}
    {!! \App\Support\Vendor::estilosSistema() !!}
</head>

<body class="acceso">
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
                        
                        @if (Route::has('restart.password.index'))
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
    @include('layouts.partials.validacion-formularios')
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
