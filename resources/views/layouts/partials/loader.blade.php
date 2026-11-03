{{--
    Loader de pantalla completa: ÚNICAMENTE la animación Lottie de temporada.

        loader.json    el resto del año
        fantasma.json  del 1 de octubre al 2 de noviembre
        santa.json     del 1 de diciembre al 10 de enero

    Reglas
    - Es el único indicador de carga del sistema. Los aros de Fomantic, los
      íconos giratorios de los botones y los "shimmer" quedan ocultos por la
      hoja de estilos; esta animación los reemplaza.
    - Se prepara DESPUÉS de que la página cargó, sin bloquearla: la librería y
      la animación ya están listas cuando hace falta, así que aparece al
      instante y no hay un momento en blanco.
    - Aparece solo si la acción tarda más de 180 ms (evita el parpadeo en
      respuestas rápidas) y se enciende sola con cualquier petición jQuery.
      Una petición de fondo (sondeos) se excluye con { global: false }.
    - Quien usa lector de pantalla recibe "Cargando…"; quien pidió menos
      movimiento la ve detenida en un fotograma.
--}}
<div id="fullscreen-loader" class="loader-overlay" role="status" aria-live="polite" aria-hidden="true">
    <div id="lottie-loader" aria-hidden="true"></div>
    <span class="sr-only" id="loader-texto"></span>
</div>

<script>
    (function () {
        var overlay = document.getElementById('fullscreen-loader');
        var contenedor = document.getElementById('lottie-loader');
        var texto = document.getElementById('loader-texto');
        var animacion = null;
        var lista = false;
        var preparando = false;
        var espera = null;
        var manual = false;
        var peticiones = 0;
        var menosMovimiento = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

        function rutaAnimacion() {
            var hoy = new Date();
            var anio = hoy.getFullYear();

            if (hoy >= new Date(anio, 11, 1) && hoy <= new Date(anio + 1, 0, 10)) {
                return '{{ asset("lottie/santa.json") }}';
            }
            if (hoy >= new Date(anio, 9, 1) && hoy <= new Date(anio, 10, 2)) {
                return '{{ asset("lottie/fantasma.json") }}';
            }
            return '{{ asset("lottie/loader.json") }}';
        }

        // Carga la librería y deja la animación montada, en pausa.
        function preparar() {
            if (preparando || animacion) { return; }
            preparando = true;

            function montar() {
                if (typeof lottie === 'undefined') { preparando = false; return; }
                animacion = lottie.loadAnimation({
                    container: contenedor,
                    renderer: 'svg',
                    loop: true,
                    autoplay: false,
                    path: rutaAnimacion()
                });
                animacion.addEventListener('DOMLoaded', function () {
                    lista = true;
                    if (overlay.classList.contains('active')) { reproducir(); }
                });
            }

            if (typeof lottie !== 'undefined') { montar(); return; }

            var s = document.createElement('script');
            s.src = '{{ \App\Support\Vendor::url("lottie.js") }}';
            s.onload = montar;
            s.onerror = function () { preparando = false; };
            document.head.appendChild(s);
        }

        function reproducir() {
            if (!animacion || !lista) { return; }
            if (menosMovimiento) { animacion.goToAndStop(20, true); } else { animacion.play(); }
        }

        function actualizar() {
            var debeVerse = manual || peticiones > 0;
            clearTimeout(espera);
            espera = null;

            if (debeVerse) {
                if (overlay.classList.contains('active')) { return; }
                espera = setTimeout(function () {
                    espera = null;
                    overlay.classList.add('active');
                    overlay.setAttribute('aria-hidden', 'false');
                    texto.textContent = 'Cargando…';
                    preparar();
                    reproducir();
                }, 180);
            } else {
                overlay.classList.remove('active');
                overlay.setAttribute('aria-hidden', 'true');
                texto.textContent = '';
                if (animacion && lista) { animacion.pause(); }
            }
        }

        // API que ya usan las pantallas.
        window.mostrarLoaderPantalla = function () { manual = true; actualizar(); };
        window.ocultarLoaderPantalla = function () { manual = false; actualizar(); };

        // Toda petición jQuery enciende el loader; { global: false } la excluye.
        if (window.jQuery) {
            jQuery(document)
                .ajaxSend(function () { peticiones++; actualizar(); })
                .ajaxComplete(function () { peticiones = Math.max(0, peticiones - 1); actualizar(); });
        }

        // Listo antes de que se necesite, sin estorbar la carga de la página.
        window.addEventListener('load', function () {
            if ('requestIdleCallback' in window) { requestIdleCallback(preparar, { timeout: 2500 }); }
            else { setTimeout(preparar, 800); }
        });

        // Al volver con el botón "atrás" el navegador puede restaurar la página con el loader encendido.
        window.addEventListener('pageshow', function (e) {
            if (e.persisted) { manual = false; peticiones = 0; actualizar(); }
        });
    })();
</script>
