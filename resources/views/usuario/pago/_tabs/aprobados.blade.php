@if($aprobados->isEmpty())
    <div class="empty-state">
        <i class="check circle outline icon"></i>
        <h3 class="ui header">Sin pagos aprobados</h3>
        <p>No tienes pagos aprobados hasta el momento.</p>
    </div>
@else
    <div class="desktop-table-layout">
        <table class="ui unstackable celled responsive table" style="width: 100%;">
            <thead>
                <tr>
                    <th>Concepto</th>
                    <th>Monto Pagado</th>
                    <th>Fecha de Pago</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                @foreach($aprobados as $pago)
                    <tr>
                        <td data-label="Concepto">
                            <i class="file check icon"></i>
                            {{ $pago->pago->concepto }}
                        </td>
                        <td data-label="Monto">
                            <div class="ui green label">${{ number_format($pago->cantidad_pago, 2) }}</div>
                        </td>
                        <td data-label="Fecha">
                            <i class="calendar check icon"></i>
                            {{ Carbon\Carbon::parse($pago->updated_at)->translatedFormat('j \\d\\e F \\d\\e Y') }}
                        </td>
                        <td data-label="Acción" class="center aligned">
                            <button class="ui blue button btn-descargar-recibo" data-id="{{ $pago->id }}">
                                <i class="download icon"></i> Descargar
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
        @foreach($aprobados as $pago)
            <div class="mobile-card-item aprobado">
                <div class="header-item">
                    <i class="file check icon"></i>
                    {{ $pago->pago->concepto }}
                </div>
                <div class="meta-item">
                    <span class="label-tag">Monto</span>
                    <span class="value-tag ui green text">${{ number_format($pago->cantidad_pago, 2) }}</span>
                </div>
                <div class="meta-item">
                    <span class="label-tag">Fecha</span>
                    <span class="value-tag">{{ Carbon\Carbon::parse($pago->updated_at)->translatedFormat('j \\d\\e F \\d\\e Y') }}</span>
                </div>
                <div class="meta-item">
                    <span class="label-tag">Estado</span>
                    <span class="value-tag"><span class="ui small green label">Pagado</span></span>
                </div>
                <div class="actions-item">
                    <button class="ui blue button btn-descargar-recibo" data-id="{{ $pago->id }}">
                        <i class="download icon"></i> Descargar
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