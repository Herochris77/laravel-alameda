<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#0b6b45">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ URL::to('/') }}/img/logo-header.png">

    <title>Recuperar contraseña · Alameda</title>
    
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
                <h1 class="auth-title">Recuperar Contraseña</h1>
                <p class="auth-subtitle">Restablece el acceso a tu cuenta</p>
            </div>
            
            <div class="auth-body">
                @if (session('correo_enviado'))
                <script>
                    alertify.alert('Revisa tu correo', 'Si ese correo está registrado y verificado, te enviamos las instrucciones. Revisa tu bandeja de entrada y la carpeta de spam; el enlace funciona durante 60 minutos. Si no llega, confirma que escribiste el correo con el que te registraste.', function() {
                        window.location.href = "{{ route('login') }}";
                    });
                </script>
                @endif
                
                @if (session('invalid_token'))
                <script>
                    alertify.alert('Ups...', 'Al parecer tu enlace para restablecer tu contraseña ya expiró. Por favor, vuelve a enviar una nueva solicitud.', function() {});
                </script>
                @endif
                
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

                <div class="info-box">
                    <i class="info circle icon"></i>
                    <span>Ingresa el correo electrónico con el que te registraste y te enviaremos un enlace para restablecer tu contraseña.</span>
                </div>

                <form method="POST" action="{{ route('restart.password.restart') }}" id="forgotForm">
                    @csrf
                    
                    <div class="form-group">
                        <label class="form-label">Correo Electrónico</label>
                        <div class="input-wrapper">
                            <input type="email" 
                                   name="email" 
                                   id="emailInput"
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

                    <button type="submit" id="btnSubmit" class="btn-auth">
                        <i class="paper plane icon"></i>
                        Enviar Enlace de Recuperación
                    </button>
                    
                    <div class="note-box">
                        <i class="info circle icon"></i>
                        Recibirás un correo para restablecer tu contraseña
                    </div>
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
    alertify.defaults.transition = "zoom";
    alertify.defaults.theme.ok = "btn btn-primary";
    alertify.defaults.theme.cancel = "btn btn-secondary";
    alertify.defaults.glossary.ok = 'Aceptar';
    alertify.defaults.glossary.cancel = 'Cancelar';
    
    document.getElementById('forgotForm').addEventListener('submit', function() {
        const btn = document.getElementById('btnSubmit');
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<i class="spinner loading icon"></i> Enviando...';
        }
    });
</script>

</html>
