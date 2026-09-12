<x-app-layout>
    <style>
        * { box-sizing: border-box; }
        
        .detalle-container {
            max-width: 100%;
            padding: 16px;
            background: #f5f7fa;
            min-height: 100vh;
        }
        
        .back-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
            margin-bottom: 16px;
            font-size: 0.9rem;
        }
        
        .back-btn:hover { color: #5a67d8; }
        
        .page-header {
            background: white;
            border-radius: 20px;
            padding: 24px;
            margin-bottom: 16px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.06);
            text-align: center;
        }
        
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 20px;
            border-radius: 50px;
            font-size: 0.9rem;
            font-weight: 700;
            margin-bottom: 16px;
        }
        
        .status-badge.pendiente { background: #fef3c7; color: #d97706; }
        .status-badge.aprobado { background: #d1fae5; color: #059669; }
        .status-badge.rechazado { background: #fee2e2; color: #dc2626; }
        
        .page-header h1 {
            font-size: 1.3rem;
            font-weight: 700;
            color: #1e293b;
            margin: 0 0 8px 0;
        }
        
        .amount-display {
            font-size: 2.5rem;
            font-weight: 800;
            color: #1e293b;
            margin: 16px 0;
        }
        
        .amount-display span {
            font-size: 1rem;
            font-weight: 500;
            opacity: 0.6;
        }
        
        .info-card {
            background: white;
            border-radius: 16px;
            padding: 20px;
            margin-bottom: 16px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.06);
        }
        
        .info-card h3 {
            font-size: 0.85rem;
            text-transform: uppercase;
            color: #94a3b8;
            margin: 0 0 16px 0;
            letter-spacing: 0.5px;
        }
        
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #f1f5f9;
        }
        
        .info-row:last-child { border-bottom: none; }
        
        .info-row .label {
            color: #64748b;
            font-size: 0.9rem;
        }
        
        .info-row .value {
            font-weight: 600;
            color: #1e293b;
            font-size: 0.9rem;
        }
        
        .comprobante-preview {
            background: white;
            border-radius: 16px;
            padding: 20px;
            margin-bottom: 16px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.06);
            text-align: center;
        }
        
        .comprobante-preview img {
            max-width: 100%;
            max-height: 400px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        
        .btn {
            display: block;
            width: 100%;
            padding: 14px 20px;
            border-radius: 14px;
            border: none;
            font-weight: 700;
            font-size: 1rem;
            cursor: pointer;
            text-align: center;
            margin-bottom: 12px;
            transition: all 0.2s;
        }
        
        .btn:active { transform: scale(0.98); }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .btn-success {
            background: #10b981;
            color: white;
        }
        
        .btn-warning {
            background: #f59e0b;
            color: white;
        }
        
        .btn-outline {
            background: transparent;
            border: 2px solid #e2e8f0;
            color: #64748b;
        }
        
        .btn.loading {
            pointer-events: none;
            opacity: 0.7;
        }
        
        .upload-form {
            background: white;
            border-radius: 16px;
            padding: 20px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.06);
        }
        
        .upload-form h3 {
            font-size: 1rem;
            font-weight: 700;
            color: #1e293b;
            margin: 0 0 16px 0;
        }
        
        .file-input-wrapper {
            position: relative;
            margin-bottom: 16px;
        }
        
        .file-input-label {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 30px;
            border: 2px dashed #cbd5e1;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .file-input-label:hover {
            border-color: #667eea;
            background: #f8fafc;
        }
        
        .file-input-label i {
            font-size: 2rem;
            color: #94a3b8;
            margin-bottom: 8px;
        }
        
        .file-input-label span {
            color: #64748b;
            font-size: 0.9rem;
        }
        
        .file-input-label small {
            color: #94a3b8;
            font-size: 0.75rem;
        }
        
        #comprobante-preview-detalle {
            margin-top: 12px;
            text-align: center;
        }
        
        #comprobante-preview-detalle img {
            max-width: 150px;
            border-radius: 8px;
        }
        
        @media (min-width: 768px) {
            .detalle-container {
                max-width: 600px;
                margin: 0 auto;
                padding: 24px;
            }
        }
    </style>
    
    <div class="detalle-container">
        <a href="{{ route('usuario.pago.index') }}" class="back-btn">
            <i class="chevron left icon"></i>
            Volver a pagos
        </a>
        
        <div class="page-header">
            @if($pago->estado == 'pagado')
                <div class="status-badge aprobado">
                    <i class="check circle icon"></i> PAGADO
                </div>
            @elseif($pago->estado == 'pendiente')
                <div class="status-badge pendiente">
                    <i class="clock icon"></i> PENDIENTE
                </div>
            @else
                <div class="status-badge rechazado">
                    <i class="times circle icon"></i> RECHAZADO
                </div>
            @endif
            
            <h1>{{ $pago->pago->concepto }}</h1>
            
            @if($pago->cantidad_pago)
                <div class="amount-display">
                    ${{ number_format($pago->cantidad_pago, 2) }}
                </div>
            @else
                <div class="amount-display">
                    ${{ number_format($pago->pago->cantidad, 2) }}
                </div>
            @endif
        </div>
        
        <div class="info-card">
            <h3>Detalles del pago</h3>
            <div class="info-row">
                <span class="label">Monto esperado</span>
                <span class="value">${{ number_format($pago->pago->cantidad, 2) }}</span>
            </div>
            <div class="info-row">
                <span class="label">Fecha de vencimiento</span>
                <span class="value">{{ \Carbon\Carbon::parse($pago->pago->vencimiento)->translatedFormat('d M Y') }}</span>
            </div>
            @if($pago->cantidad_pago)
            <div class="info-row">
                <span class="label">Monto pagado</span>
                <span class="value" style="color: #059669;">${{ number_format($pago->cantidad_pago, 2) }}</span>
            </div>
            @endif
            @if($pago->estado == 'pagado')
            <div class="info-row">
                <span class="label">Fecha de pago</span>
                <span class="value">{{ \Carbon\Carbon::parse($pago->updated_at)->translatedFormat('d M Y') }}</span>
            </div>
            @endif
        </div>
        
        @if($pago->path_pago)
            <div class="comprobante-preview">
                <h3 style="font-size: 0.85rem; text-transform: uppercase; color: #94a3b8; margin: 0 0 16px 0; letter-spacing: 0.5px;">Comprobante</h3>
                <img src="{{ asset('storage/'.$pago->path_pago) }}" alt="Comprobante">
            </div>
            
            @if($pago->estado == 'pagado')
                <button class="btn btn-success btn-descargar-recibo" data-id="{{ $pago->id }}">
                    <i class="download icon"></i>
                    Descargar Recibo PDF
                </button>
            @endif
            
            @if($pago->estado == 'rechazado')
                <div class="upload-form">
                    <h3>Subir nuevo comprobante</h3>
                    <form id="form-subir-comprobante-detalle" enctype="multipart/form-data">
                        @csrf
                        <div class="file-input-wrapper">
                            <input type="file" name="comprobante" id="comprobante-input-detalle" accept="image/*" style="display: none;" required>
                            <label for="comprobante-input-detalle" class="file-input-label">
                                <i class="cloud upload alternate icon"></i>
                                <span>Seleccionar imagen</span>
                                <small>PNG, JPG o GIF</small>
                            </label>
                            <div id="comprobante-preview-detalle"></div>
                        </div>
                        
                        <div class="ui input" style="width: 100%; margin-bottom: 16px;">
                            <input type="number" name="cantidad_pago" id="cantidad-pago-detalle" 
                                   value="{{ $pago->pago->cantidad }}" 
                                   placeholder="Cantidad pagada" 
                                   min="0" step="0.01" 
                                   style="width: 100%;" required>
                        </div>
                        
                        <button type="submit" class="btn btn-primary" id="btn-subir-detalle">
                            <i class="upload icon"></i>
                            Subir Comprobante
                        </button>
                    </form>
                </div>
            @endif
        @else
            @if($pago->estado != 'pagado')
                <div class="upload-form">
                    <h3>Subir comprobante</h3>
                    <form id="form-subir-comprobante-detalle" enctype="multipart/form-data">
                        @csrf
                        <div class="file-input-wrapper">
                            <input type="file" name="comprobante" id="comprobante-input-detalle" accept="image/*" style="display: none;" required>
                            <label for="comprobante-input-detalle" class="file-input-label">
                                <i class="cloud upload alternate icon"></i>
                                <span>Seleccionar imagen</span>
                                <small>PNG, JPG o GIF</small>
                            </label>
                            <div id="comprobante-preview-detalle"></div>
                        </div>
                        
                        <div class="ui input" style="width: 100%; margin-bottom: 16px;">
                            <input type="number" name="cantidad_pago" id="cantidad-pago-detalle" 
                                   value="{{ $pago->pago->cantidad }}" 
                                   placeholder="Cantidad pagada" 
                                   min="0" step="0.01" 
                                   style="width: 100%;" required>
                        </div>
                        
                        <button type="submit" class="btn btn-primary" id="btn-subir-detalle">
                            <i class="upload icon"></i>
                            Subir Comprobante
                        </button>
                    </form>
                </div>
            @endif
        @endif
    </div>
</x-app-layout>

<script>
$(document).ready(function() {
    const BASE_URL = "{{ url('/') }}";
    let isSubmitting = false;
    
    $('#comprobante-input-detalle').on('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#comprobante-preview-detalle').html(
                    '<img src="' + e.target.result + '" alt="Preview">'
                );
            };
            reader.readAsDataURL(file);
        }
    });
    
    $('#form-subir-comprobante-detalle').submit(function(e) {
        e.preventDefault();
        
        if (isSubmitting) return false;
        
        const btn = $('#btn-subir-detalle');
        const formData = new FormData(this);
        const id = {{ $pago->id }};
        
        if (!$('#comprobante-input-detalle')[0].files[0]) {
            alertify.error('Selecciona un comprobante');
            return false;
        }
        
        isSubmitting = true;
        btn.addClass('loading').html('<i class="spinner loading icon"></i> Subiendo...');
        
        $.ajax({
            url: BASE_URL + '/usuario/pago/subir-comprobante/' + id,
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(res) {
                alertify.success('Comprobante subido');
                setTimeout(() => location.reload(), 1500);
            },
            error: function(xhr) {
                alertify.error(xhr.responseJSON?.message || 'Error al subir');
                isSubmitting = false;
                btn.removeClass('loading').html('<i class="upload icon"></i> Subir');
            }
        });
        return false;
    });
    
    $('.btn-descargar-recibo').on('click', function() {
        const btn = $(this);
        if (btn.hasClass('loading')) return false;
        
        btn.addClass('loading').html('<i class="spinner loading icon"></i> Generando...');
        
        const id = btn.data('id');
        window.location.href = BASE_URL + '/usuario/pago/descargar/' + id;
        
        setTimeout(() => {
            btn.removeClass('loading').html('<i class="download icon"></i> Descargar Recibo PDF');
        }, 2500);
    });
});
</script>