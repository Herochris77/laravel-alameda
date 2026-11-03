{{--
    DataTables (interfaz de tabla). Solo lo usan administrador/correos y
    usuario/vecino; incluirlo en el resto de pantallas era cargar ~190 KB para nada.

    Va ANTES del script de la vista, que llama a $('#tabla').DataTable().
    Sin los botones de exportar (nadie los usa).
--}}
{!! \App\Support\Vendor::css('datatables-se.css') !!}
{!! \App\Support\Vendor::css('datatables-responsive-se.css') !!}
{!! \App\Support\Vendor::js('datatables.js') !!}
{!! \App\Support\Vendor::js('datatables-se.js') !!}
{!! \App\Support\Vendor::js('datatables-responsive.js') !!}
{!! \App\Support\Vendor::js('datatables-responsive-se.js') !!}
