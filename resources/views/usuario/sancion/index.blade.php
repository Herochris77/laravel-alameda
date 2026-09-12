<x-app-layout>
    <style>
        .filter-bar {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }
        
        .filter-btn {
            padding: 10px 18px;
            border-radius: 50px;
            border: 2px solid #e2e8f0;
            background: white;
            color: #64748b;
            font-weight: 600;
            font-size: 0.85rem;
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .filter-btn:hover {
            border-color: #667eea;
            color: #667eea;
        }
        
        .filter-btn.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-color: transparent;
            color: white;
        }
        
        .sancion-card {
            background: white;
            border-radius: 16px;
            padding: 24px;
            margin-bottom: 16px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.06);
            position: relative;
            overflow: hidden;
        }
        
        .sancion-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
        }
        
        .sancion-card.pendiente::before { background: #ef4444; }
        .sancion-card.pagado::before { background: #10b981; }
        .sancion-card.rechazado::before { background: #f59e0b; }
        
        .sancion-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 16px;
        }
        
        .sancion-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: #1e293b;
            margin: 0;
        }
        
        .sancion-monto {
            font-size: 1.5rem;
            font-weight: 800;
            color: #1e293b;
        }
        
        .sancion-monto span {
            font-size: 0.9rem;
            font-weight: 500;
            color: #94a3b8;
        }
        
        .sancion-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 16px;
            margin-bottom: 16px;
            padding-bottom: 16px;
            border-bottom: 1px solid #f1f5f9;
        }
        
        .sancion-meta-item {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 0.85rem;
            color: #64748b;
        }
        
        .sancion-meta-item i {
            color: #94a3b8;
        }
        
        .sancion-desc {
            margin-bottom: 16px;
        }
        
        .sancion-desc-item {
            display: flex;
            gap: 10px;
            padding: 8px 0;
            border-bottom: 1px solid #f8fafc;
            font-size: 0.9rem;
        }
        
        .sancion-desc-item:last-child {
            border-bottom: none;
        }
        
        .sancion-desc-item .label {
            color: #94a3b8;
            min-width: 100px;
        }
        
        .sancion-desc-item .value {
            color: #475569;
            font-weight: 500;
        }
        
        .sancion-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            border-radius: 50px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        
        .status-badge.pendiente {
            background: #fee2e2;
            color: #dc2626;
        }
        
        .status-badge.pagado {
            background: #d1fae5;
            color: #059669;
        }
        
        .status-badge.rechazado {
            background: #fef3c7;
            color: #d97706;
        }
        
        .evidence-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            border-radius: 10px;
            background: #f1f5f9;
            color: #475569;
            font-size: 0.85rem;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .evidence-btn:hover {
            background: #e2e8f0;
        }
    </style>
    
    <div class="page-header">
        <div class="page-header-left">
            <div class="page-icon">
                <i class="gavel icon" style="margin: 0;"></i>
            </div>
            <div>
                <h1 class="page-title">Mis Sanciones</h1>
                <p class="page-subtitle">Consulta tus sanciones y realiza tu pago</p>
            </div>
        </div>
    </div>
    
    <div class="filter-bar">
        <a href="{{ route('usuario.sancion.index', ['filtro' => 'todos']) }}" 
           class="filter-btn {{ !request('filtro') || request('filtro') == 'todos' ? 'active' : '' }}">
            Todas
        </a>
        <a href="{{ route('usuario.sancion.index', ['filtro' => 'pendiente']) }}" 
           class="filter-btn {{ request('filtro') == 'pendiente' ? 'active' : '' }}">
            Pendientes
        </a>
        <a href="{{ route('usuario.sancion.index', ['filtro' => 'pagado']) }}" 
           class="filter-btn {{ request('filtro') == 'pagado' ? 'active' : '' }}">
            Pagadas
        </a>
        <a href="{{ route('usuario.sancion.index', ['filtro' => 'rechazado']) }}" 
           class="filter-btn {{ request('filtro') == 'rechazado' ? 'active' : '' }}">
            Rechazadas
        </a>
    </div>
    
    @if($sanciones->isEmpty())
        <div class="card">
            <div class="empty-state">
                <div class="empty-state-icon">
                    <i class="check circle icon" style="margin: 0;"></i>
                </div>
                <h3>Sin sanciones</h3>
                <p>No tienes sanciones registradas.</p>
            </div>
        </div>
    @else
        @foreach($sanciones as $sancion)
            <div class="sancion-card {{ $sancion->estado }}">
                <div class="sancion-header">
                    <div>
                        <h3 class="sancion-title">
                            <i class="warning icon" style="color: #ef4444;"></i>
                            {{ $sancion->motivo }}
                        </h3>
                        <div class="sancion-meta">
                            <div class="sancion-meta-item">
                                <i class="calendar icon"></i>
                                {{ \Carbon\Carbon::parse($sancion->created_at)->translatedFormat('d M Y') }}
                            </div>
                            <div class="sancion-meta-item">
                                <i class="info circle icon"></i>
                                {{ $sancion->incidencia }}
                            </div>
                        </div>
                    </div>
                    <div class="sancion-monto">
                        ${{ number_format($sancion->monto, 2) }}
                        <span>MXN</span>
                    </div>
                </div>
                
                <div class="sancion-desc">
                    @if($sancion->comentario)
                        <div class="sancion-desc-item">
                            <span class="label"><i class="comment icon"></i> Comentario:</span>
                            <span class="value">{{ $sancion->comentario }}</span>
                        </div>
                    @endif
                </div>
                
                <div class="sancion-actions">
                    <span class="status-badge {{ $sancion->estado }}">
                        @if($sancion->estado === 'pendiente')
                            <i class="clock icon"></i> Pendiente
                        @elseif($sancion->estado === 'pagado')
                            <i class="check icon"></i> Pagado
                        @else
                            <i class="times icon"></i> Rechazado
                        @endif
                    </span>
                    
                    @if($sancion->estado === 'pendiente')
                        <button class="btn btn-primary" onclick="abrirModalPago({{ $sancion->id }}, {{ $sancion->monto }})">
                            <i class="upload icon"></i> Subir pago
                        </button>
                    @elseif($sancion->estado === 'rechazado')
                        <button class="btn btn-warning" onclick="abrirModalPago({{ $sancion->id }}, {{ $sancion->monto }})">
                            <i class="upload icon"></i> Reintentar
                        </button>
                    @endif
                    
                    @if($sancion->foto_path)
                        <button class="evidence-btn" onclick="verEvidencia('{{ asset('storage/' . $sancion->foto_path) }}')">
                            <i class="image icon"></i> Evidencia
                        </button>
                    @endif
                    
                    @if($sancion->estado === 'pagado' && $sancion->pago_path)
                        <button class="evidence-btn" onclick="verEvidencia('{{ asset('storage/' . $sancion->pago_path) }}')">
                            <i class="receipt icon"></i> Comprobante
                        </button>
                    @endif
                </div>
            </div>
        @endforeach
    @endif
</x-app-layout>

<!-- Modal Pago -->
<div class="ui modal" id="modal-pago">
    <div class="header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
        <i class="dollar icon"></i> Subir Comprobante de Pago
    </div>
    <div class="content">
        <form id="pago-form" class="ui form" enctype="multipart/form-data">
            @csrf
            <input type="hidden" id="sancion-id" name="sancion_id">
            
            <div class="field">
                <label>Cantidad a pagar</label>
                <div class="ui left icon input">
                    <i class="dollar sign icon"></i>
                    <input type="number" id="cantidad-pago" name="cantidad" step="0.01" min="0" placeholder="0.00" required>
                </div>
            </div>

            <div class="upload-area" id="uploadAreaSancion">
                <input type="file" name="comprobante" accept="image/jpeg,image/png" id="comprobante-input-sancion" style="display: none;">
                
                <label for="comprobante-input-sancion" id="uploadLabelSancion" style="cursor: pointer; width: 100%;">
                    <div class="upload-placeholder" id="uploadPlaceholderSancion">
                        <div class="upload-icon">
                            <i class="cloud upload alternate icon"></i>
                        </div>
                        <div class="upload-text">
                            <span class="primary">Arrastra tu imagen aquí</span>
                            <span class="secondary">o presiona para seleccionar</span>
                        </div>
                        <div class="upload-formats">
                            JPG, PNG (máx. 5MB)
                        </div>
                    </div>
                </label>
                
                <div class="upload-preview" id="uploadPreviewSancion" style="display: none;">
                    <img id="previewImageSancion" src="" alt="Preview" style="max-height: 200px; border-radius: 8px;">
                    <button type="button" class="remove-btn" id="removeImageSancion" style="position: absolute; top: 10px; right: 10px;">
                        <i class="times icon"></i>
                    </button>
                </div>
            </div>
        </form>
    </div>
    <div class="actions">
        <button class="ui black deny button" onclick="cerrarModalPago()">Cancelar</button>
        <button class="ui green button" id="btn-subir-pago-sancion" onclick="subirPago()">
            <i class="upload icon"></i> Subir
        </button>
    </div>
</div>

<!-- Modal Evidencia -->
<div class="ui modal" id="modal-evidencia">
    <div class="header">
        <i class="image icon"></i> Evidencia
    </div>
    <div class="content" style="text-align: center;">
        <img id="evidencia-img" src="" alt="Evidencia" style="max-width: 100%; border-radius: 8px;">
    </div>
    <div class="actions">
        <button class="ui black deny button">Cerrar</button>
    </div>
</div>

<script>
    function abrirModalPago(id, monto) {
        $('#sancion-id').val(id);
        $('#cantidad-pago').val(monto);
        $('#comprobante-input-sancion').val('');
        $('#uploadPlaceholderSancion').show();
        $('#uploadPreviewSancion').hide();
        $('#modal-pago').modal('show');
    }

    function cerrarModalPago() {
        $('#modal-pago').modal('hide');
    }

    function verEvidencia(url) {
        $('#evidencia-img').attr('src', url);
        $('#modal-evidencia').modal('show');
    }

    // File input handling
    const fileInputSancion = document.getElementById('comprobante-input-sancion');
    const previewSancion = document.getElementById('uploadPreviewSancion');
    const placeholderSancion = document.getElementById('uploadPlaceholderSancion');
    const previewImgSancion = document.getElementById('previewImageSancion');
    const removeBtnSancion = document.getElementById('removeImageSancion');

    fileInputSancion.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            if (file.size > 5 * 1024 * 1024) {
                alertify.error('La imagen no puede superar los 5MB');
                this.value = '';
                return;
            }
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImgSancion.src = e.target.result;
                placeholderSancion.style.display = 'none';
                previewSancion.style.display = 'block';
            };
            reader.readAsDataURL(file);
        }
    });

    removeBtnSancion.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        fileInputSancion.value = '';
        previewImgSancion.src = '';
        previewSancion.style.display = 'none';
        placeholderSancion.style.display = 'block';
    });

    // Drag and drop
    const uploadAreaSancion = document.getElementById('uploadAreaSancion');
    uploadAreaSancion.addEventListener('dragover', function(e) {
        e.preventDefault();
        this.classList.add('dragover');
    });
    uploadAreaSancion.addEventListener('dragleave', function(e) {
        e.preventDefault();
        this.classList.remove('dragover');
    });
    uploadAreaSancion.addEventListener('drop', function(e) {
        e.preventDefault();
        this.classList.remove('dragover');
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            fileInputSancion.files = files;
            fileInputSancion.dispatchEvent(new Event('change'));
        }
    });

    let isSubmittingSancion = false;
    
    function subirPago() {
        if (isSubmittingSancion) return false;
        
        const id = $('#sancion-id').val();
        const cantidad = $('#cantidad-pago').val();
        const comprobante = $('#comprobante-input-sancion')[0].files[0];

        if (!cantidad || !comprobante) {
            alertify.error('Completa todos los campos');
            return;
        }

        const formData = new FormData();
        formData.append('cantidad', cantidad);
        formData.append('comprobante', comprobante);

        isSubmittingSancion = true;
        const btn = $('#btn-subir-pago-sancion');
        if (btn.length) {
            btn.addClass('loading disabled');
            btn.html('<i class="spinner loading icon"></i> Subiendo...');
        }

        $.ajax({
            url: '{{ route("usuario.sancion.subirPago", ["id" => ":id"]) }}'.replace(':id', id),
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function(xhr) {
                const token = $('meta[name="csrf-token"]').attr('content');
                if (token) xhr.setRequestHeader('X-CSRF-TOKEN', token);
            },
            success: function(response) {
                if (response.success) {
                    alertify.success(response.message);
                    cerrarModalPago();
                    setTimeout(function() { location.reload(); }, 1500);
                } else {
                    alertify.error(response.message);
                    isSubmittingSancion = false;
                    if (btn.length) {
                        btn.removeClass('loading disabled');
                        btn.html('<i class="upload icon"></i> Subir');
                    }
                }
            },
            error: function(xhr) {
                alertify.error(xhr.responseJSON?.message || 'Error al subir');
                isSubmittingSancion = false;
                if (btn.length) {
                    btn.removeClass('loading disabled');
                    btn.html('<i class="upload icon"></i> Subir');
                }
            }
        });
    }
</script>