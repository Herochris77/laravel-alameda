<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#0b6b45">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ URL::to('/') }}/img/logo-header.png">

    <title>Crear cuenta · Alameda</title>
    
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
    @include('layouts.partials.validacion-formularios')
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