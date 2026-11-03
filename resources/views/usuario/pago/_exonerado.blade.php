{{--
    Aviso para el vecino: la mesa directiva acordó no cobrarle el recargo por mora
    de este recibo. Se usa dentro de cada tarjeta de pago ($pago = el recibo).
--}}
@if($pago->recargoExonerado())
    <div class="aviso-acuerdo" role="note">
        <i class="handshake outline icon"></i>
        <span>
            <strong>Recargo por mora exonerado.</strong>
            Por acuerdo con la mesa directiva no se te cobrará recargo en este recibo; paga solo la cuota.
            @if($pago->recargo_exonerado_motivo)
                <em>{{ $pago->recargo_exonerado_motivo }}</em>
            @endif
        </span>
    </div>
@endif
