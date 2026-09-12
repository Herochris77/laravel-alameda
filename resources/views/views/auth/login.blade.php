<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ URL::to('/') }}/img/logo-header.png">
    <title>{{ config('app.name', 'Laravel') }}</title>

    <script>
        (function(){try{var t=localStorage.getItem('alameda-theme');if(t==='dark'||t==='light'){document.documentElement.setAttribute('data-theme',t);}}catch(e){}})();
    </script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fomantic-ui@2.9.2/dist/semantic.min.css">
    <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/alertify.min.css" />
    <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/themes/default.min.css" />
    <script src="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/alertify.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fomantic-ui@2.9.2/dist/semantic.min.js"></script>

    <link rel="stylesheet" href="{{ asset('css/theme.css') }}?v={{ @filemtime(public_path('css/theme.css')) }}">
</head>

<body class="auth-page">
    <button type="button" class="auth-theme" id="themeToggle" title="Cambiar tema" aria-label="Cambiar tema"><i class="moon icon"></i></button>

    <div class="auth-wrap">
        <div class="auth-head">
            <div class="auth-emblem"><img src="{{ URL::to('/') }}/img/logo.jpg" alt="Logo Alameda"></div>
            <h1 class="auth-title">Iniciar sesión</h1>
            <p class="auth-sub">Tu condominio Alameda, en una sola app</p>
        </div>

        @if (session('registro_exitoso'))
            <div class="auth-alert success"><i class="check circle icon"></i> Registro exitoso. Revisa tu correo para verificar tu cuenta.</div>
        @endif
        @if (session('registro_exitoso_inquilino'))
            <div class="auth-alert success"><i class="check circle icon"></i> Registro exitoso. Contacta al dueño para la aprobación.</div>
        @endif
        @if (session('usuario_bloqueado'))
            <div class="auth-alert error"><i class="exclamation circle icon"></i> Tu usuario está bloqueado. Contacta a la administración.</div>
        @endif
        @if (session('usuario_pendiente'))
            <div class="auth-alert error"><i class="clock icon"></i> Tu acceso está pendiente de aprobación.</div>
        @endif
        @if (session('usuario_novalidado'))
            <div class="auth-alert error"><i class="mail icon"></i> Verifica tu correo para activar tu cuenta.</div>
        @endif
        @if (session('status'))
            <div class="auth-alert success"><i class="check circle icon"></i> {{ session('status') }}</div>
        @endif

        <form method="POST" action="{{ route('login') }}" id="loginForm">
            @csrf

            <div class="auth-group" id="authGroup">
                <div class="auth-field @error('email') has-error @enderror">
                    <i class="envelope outline icon"></i>
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="Correo electrónico" required autofocus autocomplete="email">
                </div>
                <div class="auth-field @error('password') has-error @enderror">
                    <i class="lock icon"></i>
                    <input type="password" name="password" id="passwordInput" placeholder="Contraseña" required autocomplete="current-password">
                    <button type="button" class="auth-eye" id="togglePassword" aria-label="Mostrar contraseña"><i class="eye slash icon"></i></button>
                </div>
            </div>

            @error('email')<div class="auth-err"><i class="exclamation circle icon"></i> {{ $message }}</div>@enderror
            @error('password')<div class="auth-err"><i class="exclamation circle icon"></i> {{ $message }}</div>@enderror

            <div class="auth-row">
                <label class="auth-chk"><input type="checkbox" name="remember" id="remember"> Recordar sesión</label>
                @if (Route::has('password.request'))
                    <a href="{{ route('restart.password.index') }}" class="auth-link">¿Olvidaste tu contraseña?</a>
                @endif
            </div>

            <button type="submit" id="btnSubmit" class="auth-btn"><i class="sign-in alternate icon"></i> Iniciar sesión</button>
        </form>

        <div class="auth-foot">¿No tienes cuenta? <a href="{{ route('register') }}">Regístrate aquí</a></div>
    </div>

    <script>
        // Tema
        (function(){
            var btn=document.getElementById('themeToggle');
            function currentDark(){var t=document.documentElement.getAttribute('data-theme');if(t)return t==='dark';return window.matchMedia&&window.matchMedia('(prefers-color-scheme: dark)').matches;}
            function paint(){btn.querySelector('i').className=currentDark()?'sun icon':'moon icon';}
            paint();
            btn.addEventListener('click',function(){var dark=!currentDark();var v=dark?'dark':'light';document.documentElement.setAttribute('data-theme',v);try{localStorage.setItem('alameda-theme',v);}catch(e){}paint();});
        })();

        // Foco del grupo (anillo iOS)
        var g=document.getElementById('authGroup');
        document.querySelectorAll('#authGroup input').forEach(function(i){
            i.addEventListener('focus',function(){g.classList.add('focus');});
            i.addEventListener('blur',function(){setTimeout(function(){if(!g.contains(document.activeElement))g.classList.remove('focus');},0);});
        });

        // Mostrar/ocultar contraseña
        var togglePassword=document.getElementById('togglePassword');
        var passwordInput=document.getElementById('passwordInput');
        if(togglePassword&&passwordInput){
            togglePassword.addEventListener('click',function(){
                var t=passwordInput.getAttribute('type')==='password'?'text':'password';
                passwordInput.setAttribute('type',t);
                this.querySelector('i').className=t==='password'?'eye slash icon':'eye icon';
            });
        }

        // Loader anti-doble-clic
        document.getElementById('loginForm').addEventListener('submit',function(){
            var b=document.getElementById('btnSubmit');
            if(b){b.disabled=true;b.innerHTML='<span class="auth-spin"></span> Ingresando…';}
        });

        // Alertify (defaults)
        alertify.defaults.transition="zoom";
        alertify.defaults.theme.ok="btn btn-primary";
        alertify.defaults.theme.cancel="btn btn-secondary";
        alertify.defaults.glossary.ok='Aceptar';
        alertify.defaults.glossary.cancel='Cancelar';
    </script>
</body>
</html>
