{{--
    Selector de VARIAS fotos de evidencia, con reducción automática de peso.

    Uso:  <x-evidencias name="fotos" :max="8" />
    Envía el campo  fotos[]  (varios archivos).

    - Se pueden elegir varias a la vez y volver a elegir: las nuevas se SUMAN.
    - Cada foto de más de ~0.9 MB se reduce sola en el equipo antes de subirla
      (ver window.comprimirImagen en layouts/partials/comprimir-imagen).
    - Se ve una miniatura de cada una, con botón para quitarla.
--}}
@props(['name' => 'fotos', 'max' => 8, 'id' => null, 'texto' => 'Toca para tomar o elegir fotos'])
@php($campo = $id ?? 'ev-'.\Illuminate\Support\Str::random(6))

<div class="evidencias" data-evidencias data-max="{{ $max }}">
    <label class="evidencias-zona" for="{{ $campo }}">
        <i class="camera icon"></i>
        <span class="evidencias-titulo">{{ $texto }}</span>
        <small>Puedes elegir varias (hasta {{ $max }}). Se reducen solas para subir rápido.</small>
    </label>

    <input type="file" id="{{ $campo }}" name="{{ $name }}[]" accept="image/*" multiple hidden>

    <div class="evidencias-lista" aria-live="polite"></div>
</div>

@once
    <script>
        (function () {
            var OBJETIVO = 900 * 1024;   // cada foto, a menos de ~0.9 MB

            function iniciar(caja) {
                var entrada = caja.querySelector('input[type=file]');
                var lista = caja.querySelector('.evidencias-lista');
                var titulo = caja.querySelector('.evidencias-titulo');
                var tituloOriginal = titulo.textContent;
                var max = parseInt(caja.dataset.max, 10) || 8;
                var archivos = [];
                var procesando = 0;

                function sincronizar() {
                    var dt = new DataTransfer();
                    archivos.forEach(function (a) { dt.items.add(a.file); });
                    entrada.files = dt.files;
                }

                function pintar() {
                    lista.innerHTML = '';

                    archivos.forEach(function (a, i) {
                        var item = document.createElement('div');
                        item.className = 'evidencia-item';

                        var img = document.createElement('img');
                        img.alt = 'Foto ' + (i + 1);
                        img.src = a.url;
                        item.appendChild(img);

                        var kb = document.createElement('span');
                        kb.className = 'evidencia-peso';
                        kb.textContent = a.file.size >= 1048576 ? (a.file.size / 1048576).toFixed(1) + ' MB' : Math.round(a.file.size / 1024) + ' KB';
                        item.appendChild(kb);

                        var quitar = document.createElement('button');
                        quitar.type = 'button';
                        quitar.className = 'evidencia-quitar';
                        quitar.setAttribute('aria-label', 'Quitar la foto ' + (i + 1));
                        quitar.innerHTML = '&times;';
                        quitar.addEventListener('click', function () {
                            URL.revokeObjectURL(a.url);
                            archivos.splice(i, 1);
                            sincronizar();
                            pintar();
                        });
                        item.appendChild(quitar);

                        lista.appendChild(item);
                    });

                    titulo.textContent = archivos.length
                        ? archivos.length + (archivos.length === 1 ? ' foto lista' : ' fotos listas') + ' · toca para agregar más'
                        : tituloOriginal;
                    caja.classList.toggle('evidencias-con-fotos', archivos.length > 0);
                }

                function avisar(msg) { if (window.alertify) { alertify.error(msg); } }

                entrada.addEventListener('change', function () {
                    var nuevas = Array.prototype.slice.call(entrada.files);
                    sincronizar();   // vuelve a dejar en el campo solo lo ya aceptado, mientras se procesan las nuevas

                    nuevas.forEach(function (archivo) {
                        if (!/^image\//.test(archivo.type)) {
                            avisar('"' + archivo.name + '" no es una imagen.');
                            return;
                        }
                        if (archivos.length + procesando >= max) {
                            avisar('Solo puedes subir ' + max + ' fotos a la vez. Las demás agrégalas después.');
                            return;
                        }

                        procesando++;
                        var listo = function (final) {
                            procesando--;
                            if (final) {
                                archivos.push({ file: final, url: URL.createObjectURL(final) });
                                sincronizar();
                                pintar();
                            }
                        };

                        if (archivo.size <= OBJETIVO || !window.comprimirImagen) {
                            if (archivo.size > 4 * 1024 * 1024) {
                                avisar('"' + archivo.name + '" pesa demasiado y no se pudo reducir.');
                                return listo(null);
                            }
                            return listo(archivo);
                        }

                        window.comprimirImagen(archivo, OBJETIVO).then(function (reducida) {
                            if (reducida) { return listo(reducida); }
                            if (archivo.size <= 4 * 1024 * 1024) { return listo(archivo); }   // GIF, etc., que ya caben
                            avisar('No pudimos reducir "' + archivo.name + '". Prueba con otra foto.');
                            listo(null);
                        });
                    });
                });
            }

            document.addEventListener('DOMContentLoaded', function () {
                document.querySelectorAll('[data-evidencias]').forEach(iniciar);
            });
        })();
    </script>
@endonce
