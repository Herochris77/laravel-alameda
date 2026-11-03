{{--
    Reduce una foto pesada en el propio teléfono o computadora, antes de subirla.

    Antes se mandaba a la gente a un sitio externo ("comprime tu imagen"), algo
    que a quien usa poca tecnología le cuesta trabajo y además saca al vecino
    del sistema. Ahora la reducción es automática y no sale nada del equipo.

    window.comprimirImagen(file, maxBytes) devuelve una Promesa con un File JPEG
    que pesa menos de maxBytes, o null si no fue posible. No toca GIF ni SVG.
--}}
<script>
    window.comprimirImagen = function (archivo, maxBytes) {
        return new Promise(function (resolver) {
            if (!archivo || !/^image\//.test(archivo.type) || /gif|svg/i.test(archivo.type)) {
                return resolver(null);
            }

            var url = URL.createObjectURL(archivo);
            var imagen = new Image();

            imagen.onerror = function () { URL.revokeObjectURL(url); resolver(null); };
            imagen.onload = function () {
                URL.revokeObjectURL(url);

                var lado = 2200;
                var intentos = 0;

                (function intentar() {
                    var escala = Math.min(1, lado / Math.max(imagen.width, imagen.height));
                    var lienzo = document.createElement('canvas');
                    lienzo.width = Math.round(imagen.width * escala);
                    lienzo.height = Math.round(imagen.height * escala);

                    var ctx = lienzo.getContext('2d');
                    ctx.fillStyle = '#ffffff';            // un PNG con transparencia no debe quedar negro
                    ctx.fillRect(0, 0, lienzo.width, lienzo.height);
                    ctx.drawImage(imagen, 0, 0, lienzo.width, lienzo.height);

                    var calidad = Math.max(0.5, 0.88 - intentos * 0.1);

                    lienzo.toBlob(function (blob) {
                        if (!blob) { return resolver(null); }

                        if (blob.size <= maxBytes || intentos >= 6) {
                            if (blob.size > maxBytes) { return resolver(null); }
                            var nombre = (archivo.name || 'comprobante').replace(/\.[^.]+$/, '') + '.jpg';
                            return resolver(new File([blob], nombre, { type: 'image/jpeg', lastModified: Date.now() }));
                        }

                        intentos++;
                        lado = Math.round(lado * 0.8);
                        intentar();
                    }, 'image/jpeg', calidad);
                })();
            };
            imagen.src = url;
        });
    };
</script>
