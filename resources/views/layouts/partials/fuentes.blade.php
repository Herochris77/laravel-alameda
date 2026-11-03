{{-- Fuentes de la marca: Inter (texto) y Fraunces (títulos y cifras), locales. --}}
@php($inter = \App\Support\Vendor::url('inter.woff2'))
@if($inter)
    <link rel="preload" href="{{ $inter }}" as="font" type="font/woff2" crossorigin>
    <style>
        /* Inter variable, subconjunto latino: cubre español completo (á é í ó ú ñ ¿ ¡ —). */
        @font-face {
            font-family: 'Inter';
            font-style: normal;
            font-weight: 100 900;
            font-display: swap;
            src: url('{{ $inter }}') format('woff2');
            unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+0304, U+0308, U+0329, U+2000-206F, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD;
        }
    </style>
@endif

{{-- Fraunces: la voz de la marca en títulos y cifras (Georgia si el archivo no está). --}}
@php($fraunces = \App\Support\Vendor::url('fraunces.woff2'))
@php($frauncesCursiva = \App\Support\Vendor::url('fraunces-italic.woff2'))
@if($fraunces)
    <link rel="preload" href="{{ $fraunces }}" as="font" type="font/woff2" crossorigin>
    <style>
        @font-face { font-family: 'Fraunces'; font-style: normal; font-weight: 100 900; font-display: swap; src: url('{{ $fraunces }}') format('woff2'); }
        @if($frauncesCursiva)
        @font-face { font-family: 'Fraunces'; font-style: italic; font-weight: 100 900; font-display: swap; src: url('{{ $frauncesCursiva }}') format('woff2'); }
        @endif
    </style>
@endif

