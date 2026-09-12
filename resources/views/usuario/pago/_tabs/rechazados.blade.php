@if($rechazados->isEmpty())
    <div class="empty-state">
        <i class="times circle outline icon"></i>
        <h3 class="ui header">Sin pagos rechazados</h3>
        <p>No tienes pagos rechazados.</p>
    </div>
@else
    <div class="ui negative message" style="margin-bottom: 15px;">
        <i class="times circle icon"></i>
        <strong>Pagos rechazados:</strong> {{ $rechazados->count() }} pago(s) fue(fueron) rechazado(s). Revisa la información y vuelve a subir tu comprobante.
    </div>
    
    <div class="desktop-table-layout">
        <table class="ui unstackable celled responsive table" style="width: 100%;">
            <thead>
                <tr>
                    <th>Concepto</th>
                    <th>Monto</th>
                    <th>Fecha</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                @foreach($rechazados as $pago)
                    <tr>
                        <td data-label="Concepto">
                            <i class="file alternate icon"></i>
                            {{ $pago->pago->concepto }}
                        </td>
                        <td data-label="Monto">
                            <div class="ui red label">${{ number_format($pago->cantidad_pago ?? $pago->pago->cantidad, 2) }}</div>
                        </td>
                        <td data-label="Fecha">
                            <i class="calendar icon"></i>
                            {{ Carbon\Carbon::parse($pago->updated_at)->translatedFormat('j \\d\\e F \\d\\e Y') }}
                        </td>
                        <td data-label="Acción" class="center aligned">
                            <button class="ui blue button btn-subir-comprobante" 
                                    data-id="{{ $pago->id }}"
                                    data-cantidad="{{ $pago->pago->cantidad }}">
                                <i class="upload icon"></i> Re-subir
                            </button>
                            @if($pago->path_pago)
                                <button class="ui green button btn-ver-comprobante" 
                                        data-id="{{ $pago->id }}" 
                                        data-imagen="{{ asset('storage/'.$pago->path_pago) }}">
                                    <i class="eye icon"></i>
                                </button>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    
    <div class="mobile-card-layout">
        @foreach($rechazados as $pago)
            <div class="mobile-card-item rechazado">
                <div class="header-item">
                    <i class="file alternate icon"></i>
                    {{ $pago->pago->concepto }}
                </div>
                <div class="meta-item">
                    <span class="label-tag">Monto</span>
                    <span class="value-tag ui red text">${{ number_format($pago->cantidad_pago ?? $pago->pago->cantidad, 2) }}</span>
                </div>
                <div class="meta-item">
                    <span class="label-tag">Fecha</span>
                    <span class="value-tag">{{ Carbon\Carbon::parse($pago->updated_at)->translatedFormat('j \\d\\e F \\d\\e Y') }}</span>
                </div>
                <div class="meta-item">
                    <span class="label-tag">Estado</span>
                    <span class="value-tag"><span class="ui small red label">Rechazado</span></span>
                </div>
                <div class="actions-item">
                    <button class="ui blue button btn-subir-comprobante" 
                            data-id="{{ $pago->id }}"
                            data-cantidad="{{ $pago->pago->cantidad }}">
                        <i class="upload icon"></i> Re-subir
                    </button>
                    @if($pago->path_pago)
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