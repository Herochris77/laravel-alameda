{{--
    Ventana de aceptación del aviso de privacidad.

    Se incluye en el layout, así que aparece en CUALQUIER pantalla del sistema
    mientras el usuario no haya aceptado. Muchos vecinos tienen la sesión
    guardada y nunca vuelven a ver el login, así que ahí no serviría de nada.

    No se puede cerrar con Escape ni haciendo clic fuera, a propósito. Pero sí
    ofrece "Cerrar sesión": un consentimiento sin alternativa real no es
    consentimiento, y dejar a alguien atrapado en una pantalla tampoco está
    bien.
--}}
@php
    $requiereAviso = auth()->check() && ! auth()->user()->haAceptadoAviso();
@endphp

@if($requiereAviso)
    <div id="aviso-bloqueo" class="aviso-bloqueo">
        <div class="aviso-caja" role="dialog" aria-modal="true" aria-labelledby="aviso-titulo">

            <div class="aviso-cabecera">
                <div class="aviso-icono"><i class="shield alternate icon"></i></div>
                <div>
                    <h2 id="aviso-titulo">Antes de continuar</h2>
                    <p>Necesitamos tu visto bueno para seguir usando la plataforma</p>
                </div>
            </div>

            <div class="aviso-cuerpo">
                <p>
                    Hola <strong>{{ auth()->user()->nombre }}</strong>. La plataforma del
                    condominio ahora cuenta con un <strong>Aviso de Privacidad</strong> que
                    explica qué datos tuyos se guardan, para qué se usan y quién puede verlos.
                </p>

                <p>
                    Te pedimos leerlo y aceptarlo. Es un requisito legal, sobre todo por los
                    <strong>comprobantes de pago</strong> que subes, que son datos financieros
                    y necesitan tu autorización expresa.
                </p>

                <div class="aviso-enlaces">
                    <a href="{{ route('legal.privacidad') }}" target="_blank" rel="noopener">
                        <i class="file alternate outline icon"></i>
                        <span>
                            <strong>Aviso de Privacidad</strong>
                            <small>Qué datos se guardan y tus derechos</small>
                        </span>
                        <i class="external alternate icon aviso-flecha"></i>
                    </a>

                    <a href="{{ route('legal.terminos') }}" target="_blank" rel="noopener">
                        <i class="clipboard list icon"></i>
                        <span>
                            <strong>Términos de Uso</strong>
                            <small>Cómo se usa la plataforma</small>
                        </span>
                        <i class="external alternate icon aviso-flecha"></i>
                    </a>
                </div>

                <label class="aviso-check" for="aviso-acepto">
                    <input type="checkbox" id="aviso-acepto">
                    <span>
                        He leído y acepto el Aviso de Privacidad y los Términos de Uso del
                        Condominio Alameda.
                    </span>
                </label>

                <div class="aviso-acciones">
                    <button type="button" class="btn btn-primary" id="aviso-btn-aceptar" disabled>
                        <i class="check icon"></i>
                        Acepto y continúo
                    </button>

                    <button type="button" class="aviso-salir" id="aviso-btn-salir">
                        Prefiero no aceptar ahora, cerrar sesión
                    </button>
                </div>

                <p class="aviso-pie">
                    Si no estás de acuerdo, escríbenos a
                    <strong>{{ config('privacidad.correo') }}</strong> y vemos tu caso. Ten en
                    cuenta que los recibos y comprobantes ya registrados se conservan porque
                    respaldan las cuentas del condominio ante la asamblea.
                </p>
            </div>

        </div>
    </div>

    <style>
        .aviso-bloqueo {
            position: fixed;
            inset: 0;
            z-index: 100000;              /* por encima del loader de pantalla */
            background: rgba(15, 23, 42, .72);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 18px;
            overflow-y: auto;
        }

        .aviso-caja {
            background: #fff;
            border-radius: 18px;
            max-width: 560px;
            width: 100%;
            margin: auto;
            box-shadow: 0 24px 60px rgba(15, 23, 42, .3);
            overflow: hidden;
        }

        .aviso-cabecera {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #fff;
            padding: 22px 26px;
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .aviso-icono {
            width: 46px;
            height: 46px;
            border-radius: 12px;
            background: rgba(255, 255, 255, .2);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .aviso-icono i { font-size: 1.4rem; margin: 0; }

        .aviso-cabecera h2 { margin: 0; font-size: 1.2rem; font-weight: 700; }

        .aviso-cabecera p { margin: 2px 0 0; font-size: .85rem; opacity: .9; }

        .aviso-cuerpo { padding: 22px 26px 24px; }

        .aviso-cuerpo > p {
            margin: 0 0 12px;
            color: #475569;
            font-size: .92rem;
            line-height: 1.6;
        }

        .aviso-enlaces {
            display: flex;
            flex-direction: column;
            gap: 9px;
            margin: 18px 0;
        }

        .aviso-enlaces a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 14px;
            border: 1px solid #e2e8f0;
            border-radius: 11px;
            text-decoration: none;
            color: #0f172a;
            transition: border-color .15s, background .15s;
        }

        .aviso-enlaces a:hover { border-color: #667eea; background: #f8fafc; }

        .aviso-enlaces a > i:first-child { font-size: 1.2rem; color: #667eea; margin: 0; }

        .aviso-enlaces span { display: flex; flex-direction: column; flex: 1; }

        .aviso-enlaces strong { font-size: .9rem; }

        .aviso-enlaces small { font-size: .77rem; color: #64748b; }

        .aviso-flecha { color: #cbd5e1; font-size: .85rem !important; }

        .aviso-check {
            display: flex;
            align-items: flex-start;
            gap: 11px;
            padding: 13px 15px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 11px;
            cursor: pointer;
            font-size: .89rem;
            line-height: 1.5;
            color: #334155;
        }

        .aviso-check input { margin-top: 3px; flex-shrink: 0; width: 17px; height: 17px; cursor: pointer; }

        .aviso-acciones {
            margin-top: 18px;
            display: flex;
            flex-direction: column;
            gap: 10px;
            align-items: center;
        }

        .aviso-acciones .btn { width: 100%; }

        .aviso-acciones .btn:disabled { opacity: .5; cursor: not-allowed; }

        .aviso-salir {
            background: none;
            border: none;
            color: #64748b;
            font-size: .82rem;
            cursor: pointer;
            text-decoration: underline;
            padding: 4px;
        }

        .aviso-salir:hover { color: #ef4444; }

        .aviso-pie {
            margin: 16px 0 0;
            padding-top: 14px;
            border-top: 1px solid #f1f5f9;
            font-size: .79rem;
            color: #94a3b8;
            line-height: 1.55;
        }

        @media (max-width: 560px) {
            .aviso-caja { border-radius: 14px; }
            .aviso-cabecera { padding: 18px; }
            .aviso-cuerpo { padding: 18px; }
            .aviso-cabecera h2 { font-size: 1.05rem; }
        }
    </style>

    <script>
        (function () {
            const bloqueo = document.getElementById('aviso-bloqueo');
            const check = document.getElementById('aviso-acepto');
            const btnAceptar = document.getElementById('aviso-btn-aceptar');
            const btnSalir = document.getElementById('aviso-btn-salir');

            // El botón se habilita solo al marcar la casilla: obliga a un acto
            // afirmativo, que es justo lo que pide el consentimiento expreso.
            check.addEventListener('change', function () {
                btnAceptar.disabled = !this.checked;
            });

            // Mientras la ventana esté abierta no se navega con el teclado por
            // detrás ni se hace scroll en la página de atrás.
            document.body.style.overflow = 'hidden';

            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape' && document.getElementById('aviso-bloqueo')) {
                    e.preventDefault();
                    e.stopPropagation();
                }
            }, true);

            btnAceptar.addEventListener('click', function () {
                btnAceptar.disabled = true;
                btnAceptar.innerHTML = '<i class="spinner loading icon"></i> Registrando...';

                fetch("{{ route('legal.aceptar') }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                })
                    .then(r => r.json())
                    .then(function (res) {
                        if (res.success) {
                            document.body.style.overflow = '';
                            bloqueo.remove();
                            location.reload();
                            return;
                        }

                        throw new Error(res.message || 'No se pudo registrar.');
                    })
                    .catch(function (e) {
                        btnAceptar.disabled = false;
                        btnAceptar.innerHTML = '<i class="check icon"></i> Acepto y continúo';
                        alert(e.message || 'No se pudo registrar tu aceptación. Inténtalo de nuevo.');
                    });
            });

            btnSalir.addEventListener('click', function () {
                const form = document.getElementById('logout-form');

                if (form) {
                    form.submit();
                    return;
                }

                window.location = "{{ url('/') }}";
            });
        })();
    </script>
@endif
