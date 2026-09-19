{{--
    Hueco para una captura de pantalla real.

    Si el archivo existe en public/img/guia/ se imprime; si no, la guía sigue
    completa con sus ilustraciones y no queda un recuadro vacío diciendo
    "falta imagen". Así se pueden ir agregando capturas sin tocar código.

    Para agregar una: guarda un JPG en public/img/guia/ con el nombre que
    espera la plantilla. JPG y no PNG, porque dompdf necesita la extensión GD
    para los PNG y puede no estar en el servidor.

    Uso: @include('guias.captura', ['archivo' => 'pagos.jpg', 'pie' => '...'])
--}}
@php
    $rutaCaptura = null;

    foreach ([public_path('img/guia/'.$archivo), base_path('../public_html/img/guia/'.$archivo)] as $candidata) {
        if (is_file($candidata) && \App\Services\PdfService::puedeIncrustar($candidata)) {
            $rutaCaptura = $candidata;
            break;
        }
    }
@endphp

@if($rutaCaptura)
    <div class="captura">
        <img src="{{ $rutaCaptura }}" alt="">
        @if(!empty($pie))
            <div class="captura-pie">{{ $pie }}</div>
        @endif
    </div>
@endif
