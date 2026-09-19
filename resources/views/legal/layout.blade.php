{{--
    Envoltura de las páginas legales.

    Es una página suelta, sin el layout de la aplicación, porque el aviso de
    privacidad tiene que poder leerse ANTES de iniciar sesión: quien todavía
    no acepta no debería tener que entrar para saber qué se hace con sus datos.
--}}
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('titulo') · Condominio Alameda</title>

    <link rel="icon" href="{{ asset('favicon.ico') }}">

    <style>
        :root {
            --lg-tinta: #0f172a;
            --lg-texto: #334155;
            --lg-suave: #64748b;
            --lg-borde: #e2e8f0;
            --lg-acento: #667eea;
            --lg-fondo: #f8fafc;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            background: var(--lg-fondo);
            color: var(--lg-texto);
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            font-size: 15px;
            line-height: 1.65;
        }

        .lg-barra {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #fff;
            padding: 26px 20px;
        }

        .lg-barra-interior {
            max-width: 860px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
        }

        .lg-marca { font-size: 1.15rem; font-weight: 700; letter-spacing: .01em; }

        .lg-marca span { display: block; font-size: .8rem; font-weight: 400; opacity: .85; }

        .lg-volver {
            color: #fff;
            text-decoration: none;
            font-size: .88rem;
            padding: 7px 14px;
            border: 1px solid rgba(255, 255, 255, .45);
            border-radius: 8px;
            white-space: nowrap;
        }

        .lg-volver:hover { background: rgba(255, 255, 255, .16); }

        .lg-hoja {
            max-width: 860px;
            margin: 26px auto 60px;
            background: #fff;
            border: 1px solid var(--lg-borde);
            border-radius: 14px;
            padding: 38px 42px;
        }

        .lg-titulo {
            margin: 0 0 4px;
            font-size: 1.7rem;
            color: var(--lg-tinta);
            line-height: 1.25;
        }

        .lg-fecha { color: var(--lg-suave); font-size: .85rem; margin-bottom: 26px; }

        .lg-hoja h2 {
            margin: 32px 0 10px;
            font-size: 1.1rem;
            color: var(--lg-tinta);
            padding-bottom: 6px;
            border-bottom: 2px solid var(--lg-borde);
        }

        .lg-hoja h3 {
            margin: 22px 0 8px;
            font-size: .97rem;
            color: var(--lg-tinta);
        }

        .lg-hoja p { margin: 0 0 13px; }

        .lg-hoja ul { margin: 0 0 14px; padding-left: 22px; }

        .lg-hoja li { margin-bottom: 7px; }

        .lg-hoja strong { color: var(--lg-tinta); }

        .lg-hoja a { color: var(--lg-acento); }

        .lg-tabla-wrap {
            overflow-x: auto;
            margin-bottom: 16px;
            -webkit-overflow-scrolling: touch;
        }

        /*
         * En celular la tabla se desplaza de lado en vez de aplastar las
         * columnas. Una palabra por renglón vuelve ilegible un documento
         * que la gente va a leer justamente desde el teléfono.
         */
        .lg-tabla {
            width: 100%;
            min-width: 560px;
            border-collapse: collapse;
            font-size: .89rem;
        }

        .lg-tabla th {
            text-align: left;
            background: var(--lg-fondo);
            border: 1px solid var(--lg-borde);
            padding: 9px 11px;
            color: var(--lg-tinta);
            font-size: .8rem;
            text-transform: uppercase;
            letter-spacing: .03em;
        }

        .lg-tabla td {
            border: 1px solid var(--lg-borde);
            padding: 9px 11px;
            vertical-align: top;
        }

        .lg-nota {
            background: #f0f9ff;
            border: 1px solid #bae6fd;
            border-radius: 10px;
            padding: 13px 16px;
            margin: 18px 0;
            font-size: .91rem;
            color: #075985;
        }

        .lg-ojo {
            background: #fffbeb;
            border: 1px solid #fcd34d;
            border-radius: 10px;
            padding: 13px 16px;
            margin: 18px 0;
            font-size: .91rem;
            color: #92400e;
        }

        .lg-indice {
            background: var(--lg-fondo);
            border: 1px solid var(--lg-borde);
            border-radius: 10px;
            padding: 16px 20px;
            margin-bottom: 26px;
        }

        .lg-indice-titulo {
            font-size: .78rem;
            text-transform: uppercase;
            letter-spacing: .05em;
            color: var(--lg-suave);
            font-weight: 700;
            margin-bottom: 8px;
        }

        .lg-indice ol { margin: 0; padding-left: 20px; font-size: .9rem; }

        .lg-indice li { margin-bottom: 4px; }

        .lg-indice a { color: var(--lg-texto); text-decoration: none; }

        .lg-indice a:hover { color: var(--lg-acento); text-decoration: underline; }

        .lg-pie {
            max-width: 860px;
            margin: 0 auto 40px;
            text-align: center;
            font-size: .82rem;
            color: var(--lg-suave);
        }

        .lg-pie a { color: var(--lg-acento); }

        @media (max-width: 640px) {
            .lg-hoja { padding: 24px 20px; margin: 16px 12px 40px; border-radius: 12px; }
            .lg-titulo { font-size: 1.35rem; }

            .lg-tabla-wrap {
                border: 1px solid var(--lg-borde);
                border-radius: 10px;
            }

            .lg-tabla-wrap::before {
                content: "Desliza la tabla para ver todo →";
                display: block;
                padding: 7px 11px;
                background: var(--lg-fondo);
                border-bottom: 1px solid var(--lg-borde);
                font-size: .74rem;
                color: var(--lg-suave);
            }
        }

        @media print {
            body { background: #fff; }
            .lg-barra, .lg-volver, .lg-indice { display: none; }
            .lg-hoja { border: none; max-width: none; padding: 0; margin: 0; }
        }
    </style>
</head>
<body>

    <header class="lg-barra">
        <div class="lg-barra-interior">
            <div class="lg-marca">
                Condominio Alameda
                <span>{{ config('privacidad.domicilio') }}</span>
            </div>

            <a href="{{ url('/') }}" class="lg-volver">Volver al inicio</a>
        </div>
    </header>

    <main class="lg-hoja">
        @yield('contenido')
    </main>

    <footer class="lg-pie">
        <a href="{{ route('legal.privacidad') }}">Aviso de Privacidad</a>
        &nbsp;·&nbsp;
        <a href="{{ route('legal.terminos') }}">Términos de Uso</a>
    </footer>

</body>
</html>
