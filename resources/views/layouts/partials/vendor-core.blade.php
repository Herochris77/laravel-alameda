{{--
    Librerías base de TODA pantalla: fuente, jQuery, Fomantic UI y AlertifyJS.

    Salen de /vendor (ver App\Support\Vendor). Si un archivo local no existe se
    usa el CDN de respaldo, así que olvidar subir la carpeta nunca deja una
    pantalla sin estilos.

    Quedan fuera a propósito, porque ninguna pantalla las usa:
      - DataTables Buttons, JSZip y pdfmake (~1 MB comprimido, exportación
        que ninguna vista llama).
    DataTables completo solo se carga donde se usa: layouts.partials.vendor-datatables.
--}}
@include('layouts.partials.fuentes')

{!! \App\Support\Vendor::js('jquery') !!}
{!! \App\Support\Vendor::css('fomantic.css') !!}
{!! \App\Support\Vendor::css('alertify.css') !!}
{!! \App\Support\Vendor::css('alertify-tema.css') !!}
{!! \App\Support\Vendor::js('alertify.js') !!}
{!! \App\Support\Vendor::js('fomantic.js') !!}
