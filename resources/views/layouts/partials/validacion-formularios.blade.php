{{--
    Validación visible de formularios, igual en TODO el sistema.

    El problema que resuelve: cuando un campo obligatorio está oculto (el
    <select> que Fomantic esconde detrás de su lista desplegable, el <input
    type="file"> que se cambia por una zona de arrastrar y soltar), el navegador
    no puede mostrarle el globo "completa este campo" y simplemente NO envía el
    formulario, sin decir nada. La persona pulsa el botón y no pasa nada.

    Qué hace:
      - Intercepta el aviso nativo de cualquier campo inválido (evento "invalid").
      - Marca en rojo el campo VISIBLE (el desplegable, la zona de archivos...).
      - Pone debajo un texto: "Este dato es obligatorio".
      - Lleva la pantalla al primer campo que falta y avisa con un mensaje que
        dice cuáles son.
    El mensaje se va solo en cuanto la persona corrige el campo.

    No cambia las reglas: solo comunica lo que el navegador ya detectaba.
--}}
<script>
(function () {
    var pendientes = [];
    var programado = false;

    function visible(el) {
        return !!el && el.getClientRects().length > 0 && getComputedStyle(el).visibility !== 'hidden';
    }

    // El elemento que la persona SÍ ve para este campo.
    function anfitrion(campo) {
        if (visible(campo) && campo.type !== 'hidden') { return campo; }

        var cont = campo.closest('.upload-area, .ui.dropdown, .ui.checkbox, .field, .form-group, .campo');
        if (cont && visible(cont)) { return cont; }

        var padre = campo.parentElement;
        while (padre && padre !== document.body) {
            var vecino = padre.querySelector('.ui.dropdown, .upload-area, .upload-zone, label');
            if (vecino && visible(vecino)) { return vecino.closest('.ui.dropdown, .upload-area, .upload-zone') || vecino; }
            if (visible(padre)) { return padre; }
            padre = padre.parentElement;
        }

        return null;
    }

    function nombre(campo) {
        var txt = '';
        if (campo.id) {
            var l = document.querySelector('label[for="' + (window.CSS && CSS.escape ? CSS.escape(campo.id) : campo.id) + '"]');
            if (l) { txt = l.textContent; }
        }
        if (!txt) {
            var g = campo.closest('.field, .form-group, .campo, .upload-group, form > div');
            var l2 = g && g.querySelector('label, .form-label, legend');
            if (l2) { txt = l2.textContent; }
        }
        if (!txt) { txt = campo.getAttribute('aria-label') || campo.getAttribute('placeholder') || ''; }
        if (!txt && campo.type === 'file') { txt = 'Archivo'; }
        if (!txt) { txt = campo.name || 'campo'; }

        return txt.replace(/\*/g, '').replace(/\s+/g, ' ').trim().slice(0, 60);
    }

    function textoError(campo) {
        var v = campo.validity || {};
        if (v.valueMissing) { return campo.type === 'file' ? 'Selecciona un archivo' : (campo.tagName === 'SELECT' || campo.closest('.ui.dropdown') ? 'Elige una opción' : 'Este dato es obligatorio'); }
        if (v.typeMismatch) { return campo.type === 'email' ? 'Escribe un correo válido, por ejemplo nombre@correo.com' : 'El formato no es válido'; }
        if (v.tooShort) { return 'Debe tener al menos ' + campo.minLength + ' caracteres'; }
        if (v.patternMismatch) { return campo.title || 'El formato no es válido'; }
        if (v.rangeUnderflow) { return 'El valor mínimo es ' + campo.min; }
        if (v.rangeOverflow) { return 'El valor máximo es ' + campo.max; }
        return campo.validationMessage || 'Revisa este dato';
    }

    function limpiar(host, aviso) {
        host.classList.remove('campo-invalido');
        if (aviso && aviso.parentNode) { aviso.parentNode.removeChild(aviso); }
    }

    function marcar(campo, host, texto) {
        if (host.classList.contains('campo-invalido')) { return; }
        host.classList.add('campo-invalido');

        var aviso = document.createElement('div');
        aviso.className = 'campo-error';
        aviso.setAttribute('role', 'alert');
        aviso.innerHTML = '<i class="exclamation circle icon"></i> ';
        aviso.appendChild(document.createTextNode(texto || textoError(campo)));
        // El texto va DEBAJO del campo: si el campo vive en una fila (flex/grid), debajo de la fila.
        var ancla = host;
        while (ancla.parentElement && ancla.parentElement !== document.body) {
            // Envoltorios que solo sirven para colocar un icono sobre el campo: el texto va fuera de ellos.
            if (ancla.parentElement.matches('.input-wrapper, .input-group, .ui.input, .ui.icon.input, [class*="input-wrap"]')) {
                ancla = ancla.parentElement;
                continue;
            }
            var estilo = getComputedStyle(ancla.parentElement);
            var enFila = (estilo.display === 'flex' || estilo.display === 'inline-flex') && estilo.flexDirection.indexOf('row') === 0;
            var enRejilla = estilo.display === 'grid' && estilo.gridTemplateColumns.split(' ').length > 1 && ancla.parentElement.children.length > 1 && !ancla.parentElement.classList.contains('two-col');
            if (!enFila && !(estilo.display === 'inline-block')) { break; }
            if (ancla.parentElement.matches('form, .form-group, .field')) { break; }
            ancla = ancla.parentElement;
        }
        ancla.insertAdjacentElement('afterend', aviso);

        var quitar = function () { limpiar(host, aviso); };
        campo.addEventListener('input', quitar, { once: true });
        campo.addEventListener('change', quitar, { once: true });
        host.addEventListener('click', quitar, { once: true });
    }

    function escapar(t) {
        return String(t).replace(/[&<>"']/g, function (c) { return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]; });
    }

    function procesar() {
        programado = false;
        var lista = pendientes;
        pendientes = [];
        if (!lista.length) { return; }

        var nombres = [];
        var primero = null;

        lista.forEach(function (campo) {
            var host = anfitrion(campo);
            if (host) {
                marcar(campo, host);
                if (!primero) { primero = { campo: campo, host: host }; }
            }
            var n = nombre(campo);
            if (nombres.indexOf(n) === -1) { nombres.push(n); }
        });

        if (primero) {
            try { primero.host.scrollIntoView({ block: 'center', behavior: 'auto' }); } catch (e) {}
            var enfocable = visible(primero.campo) ? primero.campo : primero.host.querySelector('input:not([type=hidden]), select, textarea');
            if (enfocable && enfocable.focus) { try { enfocable.focus({ preventScroll: true }); } catch (e) {} }
        }

        if (window.alertify) {
            var mostrados = nombres.slice(0, 4).map(escapar).join(', ') + (nombres.length > 4 ? ' y ' + (nombres.length - 4) + ' más' : '');
            alertify.error((nombres.length === 1 ? 'Falta completar: ' : 'Faltan datos por completar: ') + '<b>' + mostrados + '</b>');
        }
    }

    /*
     * Listas desplegables (Fomantic).
     *
     * Fomantic convierte el <select> en una lista bonita y, al hacerlo, le quita
     * el atributo "required": desde ese momento el navegador ya no sabe que el
     * dato es obligatorio y el formulario viaja vacío. Se recuerda cuáles eran
     * obligatorios (los que tenían "required" o cuya etiqueta lleva asterisco) y
     * se revisan al enviar.
     */
    document.querySelectorAll('select[required]').forEach(function (s) { s.setAttribute('data-requerido', '1'); });

    function esObligatorio(sel) {
        if (sel.getAttribute('data-requerido') === '1') { return true; }
        var g = sel.closest('.field, .form-group, .campo');
        var l = g && g.querySelector('label, .form-label');
        return !!(l && /\*\s*$/.test(l.textContent.trim()));
    }

    function vacio(sel) {
        if (sel.multiple) { return sel.selectedOptions.length === 0; }
        return !sel.value;
    }

    document.addEventListener('submit', function (e) {
        var form = e.target;
        if (!form || form.noValidate || form.tagName !== 'FORM') { return; }

        var faltan = [].slice.call(form.querySelectorAll('select')).filter(function (s) {
            return !s.disabled && esObligatorio(s) && vacio(s) && anfitrion(s);
        });
        if (!faltan.length) { return; }

        e.preventDefault();
        e.stopImmediatePropagation();

        var nombres = [];
        var primero = null;
        faltan.forEach(function (s) {
            var host = anfitrion(s);
            marcar(s, host, 'Elige una opción');
            if (!primero) { primero = host; }
            nombres.push(nombre(s));
        });

        try { primero.scrollIntoView({ block: 'center', behavior: 'auto' }); } catch (x) {}
        if (window.alertify) {
            alertify.error((nombres.length === 1 ? 'Falta elegir: ' : 'Faltan por elegir: ') + '<b>' + nombres.slice(0, 4).map(escapar).join(', ') + '</b>');
        }
    }, true);

    // "invalid" no burbujea: se escucha en la fase de captura.
    document.addEventListener('invalid', function (e) {
        var campo = e.target;
        if (!campo || !campo.form) { return; }

        e.preventDefault();   // sin el globo nativo: el aviso es el mismo en todas partes

        if (pendientes.indexOf(campo) === -1) { pendientes.push(campo); }
        if (!programado) {
            programado = true;
            setTimeout(procesar, 0);
        }
    }, true);

/*
 * Respuestas del servidor a envíos por AJAX.
 *
 * - 422: la validación del servidor dice qué falta; se muestra completo y
 *   reemplaza el aviso genérico que cada pantalla ponía ("Error al publicar").
 * - 419: la sesión venció mientras la persona llenaba el formulario.
 */
if (window.jQuery) {
    jQuery(document).ajaxError(function (evento, xhr, ajustes) {
        if (!window.alertify) { return; }

        if (xhr.status === 422 && xhr.responseJSON) {
            var errores = xhr.responseJSON.errors || {};
            var lista = [];
            Object.keys(errores).forEach(function (k) { lista = lista.concat(errores[k]); });
            if (!lista.length && xhr.responseJSON.message) { lista = [xhr.responseJSON.message]; }
            if (!lista.length) { return; }

            alertify.dismissAll();
            alertify.error(lista.slice(0, 4).map(function (m) { return escapar(m); }).join('<br>'));
        } else if (xhr.status === 419) {
            alertify.dismissAll();
            alertify.alert('Tu sesión se venció', 'Pasó mucho tiempo sin actividad. Actualiza la página para continuar; lo que ya estaba guardado sigue seguro.', function () { window.location.reload(); }).set({ label: 'Actualizar página' });
        }
    });
}
})();
</script>
