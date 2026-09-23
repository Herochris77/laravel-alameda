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
                    <h2 id="aviso-titulo">Actualizamos el Aviso de Privacidad</h2>
                    <p>Cambió lo que se guarda y lo que puedes decidir sobre tus datos</p>
                </div>
            </div>

            <div class="aviso-cuerpo">
                <p>
                    Hola <strong>{{ auth()->user()->nombre }}</strong>. Se actualizó el
                    Aviso de Privacidad: <strong>cambiaron tres cosas</strong> sobre la
                    información que la plataforma guarda y muestra. Te pedimos leerlas
                    y decidir.
                </p>

                <div class="aviso-cambios">
                    <div class="aviso-cambio">
                        <i class="car icon"></i>
                        <span>
                            <strong>Se retiró el módulo de vehículos.</strong>
                            Las placas y fotografías de los autos se eliminaron del sistema.
                        </span>
                    </div>

                    <div class="aviso-cambio">
                        <i class="address book outline icon"></i>
                        <span>
                            <strong>Puedes salir del directorio vecinal.</strong>
                            Hoy tu nombre, casa, teléfono y foto son visibles para los demás
                            vecinos. Desde <em>Mi Perfil</em> puedes desactivarlo cuando
                            quieras, y dejas de aparecer de inmediato.
                        </span>
                    </div>

                    <div class="aviso-cambio">
                        <i class="users icon"></i>
                        <span>
                            <strong>La mesa se nombra como provisional.</strong>
                            Mientras no esté legalmente constituida, así aparece en todos los
                            documentos.
                        </span>
                    </div>
                </div>

                <p>
                    Te pedimos leer el aviso y aceptarlo. Es un requisito legal, sobre todo
                    por los <strong>comprobantes de pago</strong> que subes, que son datos
                    financieros y necesitan tu autorización expresa.
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
                        Decidir después, cerrar sesión
                    </button>

                    {{--
                        La tercera opción: no aceptar y pedir que se retiren los
                        datos. Va separada y en rojo porque no tiene vuelta
                        atrás, y con su propia confirmación.
                    --}}
                    <button type="button" class="aviso-baja" id="aviso-btn-baja">
                        No acepto seguir usando la plataforma y deseo desvincular
                        mis datos personales
                    </button>
                </div>

                <p class="aviso-pie">
                    ¿Dudas antes de decidir? Escríbenos a
                    <strong>{{ config('privacidad.correo') }}</strong>. Ten en cuenta que los
                    recibos y sus montos se conservan aunque retires tus datos, porque
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

        .aviso-cambios {
            display: flex;
            flex-direction: column;
            gap: 9px;
            margin: 14px 0 16px;
        }

        .aviso-cambio {
            display: flex;
            gap: 11px;
            align-items: flex-start;
            padding: 11px 13px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            font-size: .86rem;
            line-height: 1.5;
            color: #475569;
        }

        .aviso-cambio > i {
            font-size: 1.05rem;
            color: #667eea;
            margin: 0;
            flex-shrink: 0;
            margin-top: 1px;
        }

        .aviso-cambio strong { color: #0f172a; }

        .aviso-baja {
            background: none;
            border: none;
            color: #b91c1c;
            font-size: .8rem;
            cursor: pointer;
            text-decoration: underline;
            padding: 6px 4px;
            line-height: 1.4;
            text-align: center;
        }

        .aviso-baja:hover { color: #7f1d1d; }

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

            /*
             * No acepto y quiero que retiren mis datos.
             *
             * Dos confirmaciones y escribir una palabra: no tiene vuelta atrás
             * y borra archivos. Vale más un paso de más que un arrepentimiento
             * sin remedio.
             */
            document.getElementById('aviso-btn-baja').addEventListener('click', function () {
                const paso1 = confirm(
                    'Vas a pedir que se retiren tus datos personales.\n\n' +
                    'SE ELIMINAN de forma permanente:\n' +
                    '  · Tu nombre, correo y teléfono\n' +
                    '  · Tu fotografía de perfil\n' +
                    '  · Tus mascotas y vehículos registrados\n' +
                    '  · Los comprobantes de pago que subiste\n' +
                    '  · Tus notificaciones\n\n' +
                    'SE CONSERVAN, porque respaldan las cuentas del condominio:\n' +
                    '  · Tus recibos con su monto, fecha y estado,\n' +
                    '    identificados solo como "Casa {{ auth()->user()->casa }}"\n\n' +
                    'Tu cuenta quedará sin acceso a la plataforma.\n\n' +
                    '¿Continuar?'
                );

                if (!paso1) return;

                const palabra = prompt(
                    'Esta acción NO se puede deshacer.\n\n' +
                    'Escribe DESVINCULAR para confirmar:'
                );

                if (!palabra || palabra.trim().toUpperCase() !== 'DESVINCULAR') {
                    alert('No se hizo ningún cambio.');
                    return;
                }

                this.disabled = true;
                this.textContent = 'Retirando tus datos...';

                fetch("{{ route('legal.desvincular') }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                })
                    .then(r => r.json())
                    .then(function (res) {
                        alert(res.message);

                        if (res.success) {
                            document.body.style.overflow = '';
                            window.location = "{{ url('/') }}";
                        }
                    })
                    .catch(function () {
                        alert('No se pudieron retirar tus datos. No se hizo ningún cambio. ' +
                              'Escríbenos a {{ config('privacidad.correo') }}.');
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
