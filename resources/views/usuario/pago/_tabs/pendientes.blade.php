@if($pendientes->isEmpty())
    <div class="empty-state">
        <i class="check circle outline icon"></i>
        <h3 class="ui header">Sin pagos pendientes</h3>
        <p>No tienes pagos pendientes por el momento.</p>
    </div>
@else
    <div class="desktop-table-layout">
        <table class="ui unstackable celled responsive table" style="width: 100%;">
            <thead>
                <tr>
                    <th>Concepto</th>
                    <th>Monto</th>
                    <th>Vencimiento</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pendientes as $pago)
                    <tr>
                        <td data-label="Concepto">
                            <i class="file alternate icon"></i>
                            {{ $pago->pago->concepto }}
                        </td>
                        <td data-label="Monto">
                            <div class="ui yellow label">${{ number_format($pago->pago->cantidad, 2) }}</div>
                        </td>
                        <td data-label="Vencimiento">
                            <i class="calendar icon"></i>
                            {{ Carbon\Carbon::parse($pago->pago->vencimiento)->translatedFormat('j \\d\\e F \\d\\e Y') }}
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
                                <span class="ui small orange label">Pendiente</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    
    <div class="mobile-card-layout">
        @foreach($pendientes as $pago)
            <div class="mobile-card-item">
                <div class="header-item">
                    <i class="file alternate icon"></i>
                    {{ $pago->pago->concepto }}
                </div>
                <div class="meta-item">
                    <span class="label-tag">Monto</span>
                    <span class="value-tag">${{ number_format($pago->pago->cantidad, 2) }}</span>
                </div>
                <div class="meta-item">
                    <span class="label-tag">Vencimiento</span>
                    <span class="value-tag">{{ Carbon\Carbon::parse($pago->pago->vencimiento)->translatedFormat('j \\d\\e F \\d\\e Y') }}</span>
                </div>
                <div class="meta-item">
                    <span class="label-tag">Estado</span>
                    <span class="value-tag">
                        @if(!$pago->path_pago)
                            <span class="ui small orange label">Pendiente</span>
                        @else
                            <span class="ui small yellow label">Subido</span>
                        @endif
                    </span>
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