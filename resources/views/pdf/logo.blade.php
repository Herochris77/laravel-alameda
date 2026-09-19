{{--
    Logo del condominio para los PDF.

    Se incluye con @include('pdf.logo', ['alto' => 52]). Si el archivo no
    aparece, no imprime nada: el documento sale sin membrete pero completo.

    El alto se pasa en píxeles y el ancho se deja libre para que la imagen
    no se deforme, sea cuadrada o apaisada.
--}}
@php
    $rutaLogo = app(\App\Services\LogoService::class)->ruta();
@endphp

@if($rutaLogo)
    <img src="{{ $rutaLogo }}"
         alt="Condominio Alameda"
         style="height: {{ $alto ?? 52 }}px; width: auto;">
@endif
