<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#0b6b45">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ URL::to('/') }}/img/logo-header.png">

    <title>Nueva contraseña · Alameda</title>
    
    {{-- Fuente, jQuery, Fomantic y Alertify: locales, con respaldo a CDN. --}}
    @include('layouts.partials.vendor-core')
    {{-- Sistema de diseño "Casa Alameda" (incluye los estilos de acceso). --}}
    {!! \App\Support\Vendor::estilosSistema() !!}
</head>

<body class="acceso">
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
