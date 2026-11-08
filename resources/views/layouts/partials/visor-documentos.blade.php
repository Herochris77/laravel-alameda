{{--
    Visor de documentos del sistema: una sola ventana para ver imágenes y PDFs.

    Por qué existe: dentro de un <iframe> o una pestaña nueva, muchos celulares
    (Android sobre todo) no muestran los PDF de varias hojas: ponen "contenido
    bloqueado" o piden descargarlos. Aquí el PDF se dibuja con pdf.js, que va
    incluido en /vendor, así que se ve igual en cualquier dispositivo.

    Uso: cualquier elemento con data-visor="URL" (y opcional data-titulo) abre
    el documento al tocarlo. También se puede llamar a abrirDocumento(url, titulo).

    pdf.js se baja solo la primera vez que alguien abre un PDF: las pantallas
    que no lo usan no cargan nada extra.
--}}
<div class="ui modal visor-doc" id="modal-visor-doc" role="dialog" aria-labelledby="visor-doc-titulo">
    <div class="header modal-cabecera">
        <i class="file alternate icon"></i>
        <span id="visor-doc-titulo">Documento</span>
    </div>

    <div class="content visor-cuerpo">
        <div id="visor-doc-imagen" hidden>
            <img id="visor-doc-img" alt="Vista del documento">
        </div>

        <div id="visor-doc-pdf" hidden>
            <div class="visor-barra" role="toolbar" aria-label="Controles del documento">
                <span class="visor-pagina-actual" id="visor-pagina-actual" aria-live="polite">Cargando…</span>
                <span class="visor-zoom">
                    <button type="button" class="ui icon button" id="visor-alejar" aria-label="Alejar" title="Alejar"><i class="minus icon"></i></button>
                    <button type="button" class="ui button" id="visor-ajustar" title="Ajustar al ancho">Ajustar</button>
                    <button type="button" class="ui icon button" id="visor-acercar" aria-label="Acercar" title="Acercar"><i class="plus icon"></i></button>
                </span>
            </div>
            <div class="visor-paginas" id="visor-paginas" tabindex="0"></div>
        </div>

        <div id="visor-doc-otro" class="visor-otro" hidden>
            <div class="visor-otro-icono"><i class="file alternate outline icon"></i></div>
            <h3 id="visor-otro-titulo">Vista previa no disponible</h3>
            <p id="visor-otro-texto">Puedes descargar el archivo para consultarlo.</p>
        </div>
    </div>

    <div class="actions">
        <a class="ui button" id="visor-descargar" href="#" download target="_blank" rel="noopener"><i class="download icon"></i> Descargar</a>
        <div class="ui button deny">Cerrar</div>
    </div>
</div>

<script>
(function () {
    var URL_LIBRERIA = @json(\App\Support\Vendor::url('pdfjs.js'));
    var URL_WORKER = @json(\App\Support\Vendor::url('pdfjs-worker.js'));

    var cargaLibreria = null;
    var pdf = null;          // documento abierto
    var paginas = [];        // [{ numero, pagina, caja, vista, canvas, tarea }]
    var observador = null;
    var escala = 1;          // 1 = ajustado al ancho
    var token = 0;           // para ignorar respuestas de un documento que ya se cerró

    function el(id) { return document.getElementById(id); }

    // Siempre pedimos el archivo a este mismo sitio y con el mismo https que la página:
    // una dirección con http:// o con otro dominio quedaba "bloqueada" en el celular.
    function mismoOrigen(url) {
        try {
            var u = new URL(url, location.href);
            return location.origin + u.pathname + u.search;
        } catch (e) { return url; }
    }

    function extension(url) {
        try { return (new URL(url, location.href).pathname.split('.').pop() || '').toLowerCase(); }
        catch (e) { return ''; }
    }

    function mostrar(cual) {
        ['visor-doc-imagen', 'visor-doc-pdf', 'visor-doc-otro'].forEach(function (id) {
            el(id).hidden = (id !== cual);
        });
    }

    function sinVisor(titulo, texto) {
        el('visor-otro-titulo').textContent = titulo;
        el('visor-otro-texto').textContent = texto;
        mostrar('visor-doc-otro');
    }

    function cargarLibreria() {
        if (window.pdfjsLib) { return Promise.resolve(window.pdfjsLib); }
        if (!URL_LIBRERIA || !URL_WORKER) { return Promise.reject(new Error('sin librería')); }
        if (cargaLibreria) { return cargaLibreria; }

        cargaLibreria = new Promise(function (ok, fallo) {
            var s = document.createElement('script');
            s.src = URL_LIBRERIA;
            s.onload = function () {
                window.pdfjsLib.GlobalWorkerOptions.workerSrc = URL_WORKER;
                ok(window.pdfjsLib);
            };
            s.onerror = function () { cargaLibreria = null; fallo(new Error('no cargó')); };
            document.head.appendChild(s);
        });

        return cargaLibreria;
    }

    function limpiarPdf() {
        token++;
        if (observador) { observador.disconnect(); observador = null; }
        paginas.forEach(function (p) { if (p.tarea) { try { p.tarea.cancel(); } catch (e) {} } });
        paginas = [];
        el('visor-paginas').innerHTML = '';
        if (pdf) { try { pdf.destroy(); } catch (e) {} pdf = null; }
        escala = 1;
        el('visor-pagina-actual').textContent = 'Cargando…';
    }

    function anchoDisponible() {
        var c = el('visor-paginas');
        return Math.max(200, c.clientWidth - 16);
    }

    function dimensionar() {
        var ancho = anchoDisponible() * escala;

        paginas.forEach(function (p) {
            var factor = ancho / p.vista.width;
            p.caja.style.width = ancho + 'px';
            p.caja.style.height = (p.vista.height * factor) + 'px';

            if (p.canvas) { p.canvas.remove(); p.canvas = null; }
            if (p.tarea) { try { p.tarea.cancel(); } catch (e) {} p.tarea = null; }
            p.dibujada = false;

            if (observador) { observador.unobserve(p.caja); observador.observe(p.caja); }
        });
    }

    function dibujar(p, miToken) {
        if (p.dibujada || miToken !== token) { return; }
        p.dibujada = true;

        var ancho = anchoDisponible() * escala;
        var dpr = Math.min(window.devicePixelRatio || 1, 2);
        var vista = p.pagina.getViewport({ scale: (ancho / p.vista.width) * dpr });
        var canvas = document.createElement('canvas');
        canvas.width = Math.floor(vista.width);
        canvas.height = Math.floor(vista.height);
        canvas.setAttribute('aria-label', 'Página ' + p.numero);
        p.caja.appendChild(canvas);
        p.canvas = canvas;

        p.tarea = p.pagina.render({ canvasContext: canvas.getContext('2d'), viewport: vista });
        p.tarea.promise.catch(function () { /* cancelada al cambiar el zoom */ });
    }

    function actualizarIndicador() {
        if (!paginas.length) { return; }
        var c = el('visor-paginas');
        var centro = c.getBoundingClientRect().top + Math.min(120, c.clientHeight / 3);
        var actual = 1;

        for (var i = 0; i < paginas.length; i++) {
            var r = paginas[i].caja.getBoundingClientRect();
            if (r.top <= centro) { actual = paginas[i].numero; } else { break; }
        }

        el('visor-pagina-actual').textContent = 'Página ' + actual + ' de ' + paginas.length;
    }

    function mostrarPdf(url) {
        var miToken = ++token;
        mostrar('visor-doc-pdf');
        el('visor-pagina-actual').textContent = 'Cargando documento…';

        cargarLibreria().then(function (lib) {
            return lib.getDocument({ url: url, withCredentials: true, isEvalSupported: false }).promise;
        }).then(function (doc) {
            if (miToken !== token) { try { doc.destroy(); } catch (e) {} return null; }
            pdf = doc;

            var lista = [];
            for (var n = 1; n <= doc.numPages; n++) { lista.push(doc.getPage(n)); }
            return Promise.all(lista);
        }).then(function (hojas) {
            if (!hojas || miToken !== token) { return; }

            var contenedor = el('visor-paginas');
            observador = ('IntersectionObserver' in window)
                ? new IntersectionObserver(function (entradas) {
                    entradas.forEach(function (e) {
                        if (!e.isIntersecting) { return; }
                        var p = paginas.find(function (x) { return x.caja === e.target; });
                        if (p) { dibujar(p, miToken); }
                    });
                }, { root: contenedor, rootMargin: '600px 0px' })
                : null;

            hojas.forEach(function (hoja, i) {
                var caja = document.createElement('div');
                caja.className = 'visor-hoja';
                contenedor.appendChild(caja);
                paginas.push({ numero: i + 1, pagina: hoja, caja: caja, vista: hoja.getViewport({ scale: 1 }), canvas: null, tarea: null, dibujada: false });
            });

            dimensionar();
            if (!observador) { paginas.forEach(function (p) { dibujar(p, miToken); }); }
            actualizarIndicador();
        }).catch(function () {
            if (miToken !== token) { return; }
            sinVisor('No pudimos mostrar el documento aquí', 'Puedes descargarlo con el botón "Descargar" y abrirlo en tu dispositivo.');
        });
    }

    window.abrirDocumento = function (url, titulo) {
        if (!url) { return; }

        var segura = mismoOrigen(url);
        var ext = extension(segura);

        limpiarPdf();
        el('visor-doc-titulo').textContent = titulo || 'Documento';
        el('visor-descargar').setAttribute('href', segura);
        el('visor-doc-img').removeAttribute('src');

        if (['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'svg'].indexOf(ext) !== -1) {
            el('visor-doc-img').setAttribute('src', segura);
            mostrar('visor-doc-imagen');
        } else if (ext === 'pdf') {
            mostrar('visor-doc-pdf');
        } else {
            sinVisor('Vista previa no disponible', 'Este tipo de archivo se puede descargar para consultarlo.');
        }

        $('#modal-visor-doc').modal({
            autofocus: false,
            allowMultiple: true,
            onHidden: function () {
                limpiarPdf();
                el('visor-doc-img').removeAttribute('src');
            }
        }).modal('show');

        // Hasta que la ventana es visible no se conoce el ancho real para dibujar.
        if (ext === 'pdf') { setTimeout(function () { mostrarPdf(segura); }, 60); }
    };

    $(document).on('click', '[data-visor]', function (e) {
        e.preventDefault();
        abrirDocumento(this.getAttribute('data-visor') || this.getAttribute('href'), this.getAttribute('data-titulo'));
    });

    $('#visor-paginas').on('scroll', actualizarIndicador);

    function cambiarZoom(nuevo) {
        escala = Math.min(3, Math.max(0.5, nuevo));
        dimensionar();
        actualizarIndicador();
    }
    $('#visor-acercar').on('click', function () { cambiarZoom(escala + 0.25); });
    $('#visor-alejar').on('click', function () { cambiarZoom(escala - 0.25); });
    $('#visor-ajustar').on('click', function () { cambiarZoom(1); });

    // Al girar el celular o cambiar el tamaño de la ventana, volver a ajustar al ancho.
    var temporizador = null;
    $(window).on('resize orientationchange', function () {
        if (!paginas.length) { return; }
        clearTimeout(temporizador);
        temporizador = setTimeout(function () { dimensionar(); actualizarIndicador(); }, 200);
    });
})();
</script>
