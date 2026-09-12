@if($vencidos->isEmpty())
    <div class="empty-state">
        <i class="thumbs up outline icon"></i>
        <h3 class="ui header">Sin pagos vencidos</h3>
        <p>No tienes pagos vencidos. ¡Excelente!</p>
    </div>
@else
    <div class="ui warning message" style="margin-bottom: 15px;">
        <i class="exclamation triangle icon"></i>
        <strong>Atención:</strong> Tienes {{ $vencidos->count() }} pago(s) vencido(s). Regulariza tu situación a la brevedad.
    </div>
    
    <div class="desktop-table-layout">
        <table class="ui unstackable celled responsive table" style="width: 100%;">
            <thead>
                <tr>
                    <th>Concepto</th>
                    <th>Monto</th>
                    <th>Vencimiento</th>
                    <th>Días</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                @foreach($vencidos as $pago)
                    @php
                        $vencido = Carbon\Carbon::parse($pago->pago->vencimiento);
                        $diasVencido = $vencido->diffInDays(Carbon\Carbon::now(), false);
                    @endphp
                    <tr>
                        <td data-label="Concepto">
                            <i class="file alternate icon"></i>
                            {{ $pago->pago->concepto }}
                        </td>
                        <td data-label="Monto">
                            <div class="ui red label">${{ number_format($pago->pago->cantidad, 2) }}</div>
                        </td>
                        <td data-label="Vencimiento">
                            <i class="calendar icon"></i>
                            {{ $vencido->translatedFormat('j \\d\\e F \\d\\e Y') }}
                        </td>
                        <td data-label="Días">
                            <div class="ui red horizontal label">{{ $diasVencido }} días</div>
                        </td>
                        <td data-label="Acción" class="center aligned">
                            @if(!$pago->path_pago)
                                <button class="ui blue button btn-subir-comprobante" 
                                        data-id="{{ $pago->id }}"
                                        data-cantidad="{{ $pago->pago->cantidad }}">
                                    <i class="upload icon"></i> Subir
                                </button>
                            @else
                                <button class="ui green button btn-ver-comprobante" 
                                        data-id="{{ $pago->id }}" 
                                        data-imagen="{{ asset('storage/'.$pago->path_pago) }}">
                                    <i class="eye icon"></i> Ver
                                </button>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    
    <div class="mobile-card-layout">
        @foreach($vencidos as $pago)
            @php
                $vencido = Carbon\Carbon::parse($pago->pago->vencimiento);
                $diasVencido = $vencido->diffInDays(Carbon\Carbon::now(), false);
            @endphp
            <div class="mobile-card-item vencido">
                <div class="header-item">
                    <i class="file alternate icon"></i>
                    {{ $pago->pago->concepto }}
                </div>
                <div class="meta-item">
                    <span class="label-tag">Monto</span>
                    <span class="value-tag ui red text">${{ number_format($pago->pago->cantidad, 2) }}</span>
                </div>
                <div class="meta-item">
                    <span class="label-tag">Vencimiento</span>
                    <span class="value-tag">{{ $vencido->translatedFormat('j \\d\\e F \\d\\e Y') }}</span>
                </div>
                <div class="meta-item">
                    <span class="label-tag">Estado</span>
                    <span class="value-tag"><span class="ui small red label">Vencido</span></span>
                </div>
                <div class="actions-item">
                    @if(!$pago->path_pago)
                        <button class="ui blue button btn-subir-comprobante" 
                                data-id="{{ $pago->id }}"
                                data-cantidad="{{ $pago->pago->cantidad }}">
                            <i class="upload icon"></i> Subir
                        </button>
                    @else
                        <button class="ui green button btn-ver-comprobante" 
                                data-id="{{ $pago->id }}" 
                                data-imagen="{{ asset('storage/'.$pago->path_pago) }}">
                            <i class="eye icon"></i> Ver
                        </button>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
@endif