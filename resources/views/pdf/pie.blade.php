{{--
    Pie común de todos los PDF del sistema.

    Deja constancia de dónde salió el documento y cuándo. Importa para los que
    circulan fuera de la plataforma —una constancia de no adeudo, un estado de
    cuenta, el reporte que se entrega en asamblea—: quien lo recibe sabe que lo
    emitió el sistema del condominio y no una hoja armada a mano.

    Parámetros:
      fecha  Carbon con el momento de generación. Si no se manda, se usa ahora.
      fijo   true (por omisión) lo repite al pie de TODAS las hojas, que es lo
             que se quiere en un documento de varias páginas. false lo deja
             como un renglón más al final del contenido, para plantillas cuyo
             margen inferior no da espacio.
      nota   Texto extra opcional, antes de la frase.

    El estilo va en línea a propósito: cada plantilla trae su propio CSS y así
    el pie se ve igual en todas sin depender de que exista una clase.
--}}
@php
    $pdfPieFecha = ($fecha ?? null) instanceof \Carbon\Carbon
        ? $fecha
        : (($fecha ?? null) ? \Carbon\Carbon::parse($fecha) : \Carbon\Carbon::now());

    $pdfPieFijo = $fijo ?? true;

    $pdfPieEstilo = $pdfPieFijo
        ? 'position: fixed; bottom: 10px; left: 0; right: 0;'
        : 'margin-top: 16px;';
@endphp

<div style="{{ $pdfPieEstilo }} text-align: center; font-size: 7.5px; color: #777; font-family: DejaVu Sans, sans-serif; line-height: 1.4;">
    @if($nota ?? null)
        {{ $nota }}<br>
    @endif
    Documento generado por la plataforma del Condominio Alameda
    el {{ $pdfPieFecha->translatedFormat('j \d\e F \d\e Y \a \l\a\s H:i') }}.
</div>
