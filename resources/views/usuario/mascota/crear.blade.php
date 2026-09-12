<x-app-layout>
    <div class="mascota-page">
        <div class="page-header mascota-header">
            <div class="page-header-left mascota-header-left">
                <div class="page-icon mascota-page-icon">
                    <i class="paw icon"></i>
                </div>

                <div>
                    <h1 class="page-title">Registrar Mascota</h1>
                    <p class="page-subtitle">Ingresa los datos de tu mascota</p>
                </div>
            </div>

            <div class="page-header-actions mascota-header-actions">
                <a href="{{ route('usuario.mascota.index') }}" class="btn btn-secondary btn-volver">
                    <i class="arrow left icon"></i>
                    Volver
                </a>
            </div>
        </div>

        <div class="mascota-layout">
            <div class="mascota-form-card">
                <div class="mascota-card-header">
                    <div class="mascota-card-title-wrap">
                        <div class="mascota-card-icon">
                            <i class="paw icon"></i>
                        </div>

                        <div>
                            <h3 class="mascota-card-title">Datos de la mascota</h3>
                            <p class="mascota-card-subtitle">
                                Completa la información para registrar a tu mascota en el condominio.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="mascota-card-body">
                    <form id="mascota-form" method="POST" action="{{ route('usuario.mascota.guardar') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="form-group">
                            <label class="form-label">Nombre de la mascota *</label>
                            <div class="input-icon-wrapper">
                                <i class="paw icon"></i>
                                <input
                                    type="text"
                                    name="nombre"
                                    class="form-input mascota-input input-with-icon"
                                    placeholder="Ej: Max, Luna, Buddy"
                                    value="{{ old('nombre') }}"
                                    required
                                    autocomplete="off"
                                >
                            </div>
                        </div>

                        <div class="responsive-grid-2">
                            <div class="form-group">
                                <label class="form-label">Tipo de mascota *</label>
                                <select name="tipo" id="tipo-mascota" class="ui fluid dropdown mascota-input" required>
                                    <option value="">Seleccionar tipo</option>
                                    <option value="perro" {{ old('tipo') == 'perro' ? 'selected' : '' }}>Perro</option>
                                    <option value="gato" {{ old('tipo') == 'gato' ? 'selected' : '' }}>Gato</option>
                                    <option value="otro" {{ old('tipo') == 'otro' ? 'selected' : '' }}>Otro</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Edad (años) *</label>
                                <div class="input-icon-wrapper">
                                    <i class="calendar icon"></i>
                                    <input
                                        type="number"
                                        name="edad"
                                        class="form-input mascota-input input-with-icon"
                                        min="0"
                                        max="30"
                                        placeholder="Ej: 3"
                                        value="{{ old('edad') }}"
                                        required
                                    >
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Género *</label>
                            <select name="genero" id="genero-mascota" class="ui fluid dropdown mascota-input" required>
                                <option value="">Seleccionar género</option>
                                <option value="macho" {{ old('genero') == 'macho' ? 'selected' : '' }}>Macho</option>
                                <option value="hembra" {{ old('genero') == 'hembra' ? 'selected' : '' }}>Hembra</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Características</label>
                            <textarea
                                name="caracteristicas"
                                class="form-input mascota-input mascota-textarea"
                                rows="3"
                                placeholder="Color, raza, marcas distintivas..."
                            >{{ old('caracteristicas') }}</textarea>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Estado de la mascota</label>

                            <div class="checkbox-grid">
                                <label class="checkbox-card {{ old('vacunas') ? 'checked' : '' }}">
                                    <input type="checkbox" name="vacunas" value="1" {{ old('vacunas') ? 'checked' : '' }}>

                                    <div class="checkbox-content">
                                        <div class="checkbox-icon vacunas">
                                            <i class="syringe icon"></i>
                                        </div>

                                        <div>
                                            <strong>Vacunas al día</strong>
                                            <span>Esquema actualizado</span>
                                        </div>
                                    </div>
                                </label>

                                <label class="checkbox-card {{ old('esterilizado') ? 'checked' : '' }}">
                                    <input type="checkbox" name="esterilizado" value="1" {{ old('esterilizado') ? 'checked' : '' }}>

                                    <div class="checkbox-content">
                                        <div class="checkbox-icon esterilizado">
                                            <i class="check icon"></i>
                                        </div>

                                        <div>
                                            <strong>Esterilizado/a</strong>
                                            <span>Control responsable</span>
                                        </div>
                                    </div>
                                </label>

                                <label class="checkbox-card {{ old('amistoso') === '0' ? '' : 'checked' }}">
                                    <input type="checkbox" name="amistoso" value="1" {{ old('amistoso') !== '0' ? 'checked' : '' }}>

                                    <div class="checkbox-content">
                                        <div class="checkbox-icon amistoso">
                                            <i class="heart icon"></i>
                                        </div>

                                        <div>
                                            <strong>Amistoso/a</strong>
                                            <span>Convive con vecinos</span>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Foto de la mascota</label>

                            <div class="upload-area responsive-upload-area" id="upload-area-foto">
                                <input type="file" name="foto" accept="image/jpeg,image/png" id="foto-input" hidden>

                                <label for="foto-input" class="upload-label">
                                    <div class="upload-placeholder" id="upload-placeholder">
                                        <div class="upload-icon">
                                            <i class="upload icon"></i>
                                        </div>

                                        <div class="upload-text">
                                            <span class="primary">Arrastra la imagen aquí</span>
                                            <span class="secondary">o haz clic para seleccionar</span>
                                        </div>

                                        <div class="upload-formats">JPG o PNG, máximo 5MB</div>
                                    </div>

                                    <div id="preview-upload" class="preview-upload hidden">
                                        <button type="button" class="remove-preview-btn" id="remove-preview-btn" title="Quitar imagen">
                                            <i class="times icon"></i>
                                        </button>

                                        <img
                                            id="foto-preview"
                                            class="foto-preview"
                                            alt="Vista previa de la mascota"
                                            style="display: none;"
                                        >

                                        <div class="preview-badge">
                                            <i class="check icon"></i>
                                            Imagen seleccionada
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <div class="form-actions">
                            <button class="btn btn-primary btn-lg btn-submit-mascota" type="submit" id="btn-guardar">
                                <i class="paw icon"></i>
                                Registrar Mascota
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="mascota-info-card">
                <div class="mascota-card-header">
                    <div class="mascota-card-title-wrap">
                        <div class="mascota-card-icon secondary">
                            <i class="info circle icon"></i>
                        </div>

                        <div>
                            <h3 class="mascota-card-title">Información importante</h3>
                            <p class="mascota-card-subtitle">Recomendaciones para un registro correcto.</p>
                        </div>
                    </div>
                </div>

                <div class="mascota-card-body">
                    <div class="info-list">
                        <div class="info-item">
                            <div class="info-icon info-icon-purple">
                                <i class="syringe icon"></i>
                            </div>

                            <div class="info-content">
                                <h4>Vacunas al día</h4>
                                <p>Asegúrate de tener las vacunas actualizadas para la seguridad de todos.</p>
                            </div>
                        </div>

                        <div class="info-item">
                            <div class="info-icon info-icon-pink">
                                <i class="camera icon"></i>
                            </div>

                            <div class="info-content">
                                <h4>Foto clara</h4>
                                <p>Sube una foto reciente donde se pueda identificar bien a tu mascota.</p>
                            </div>
                        </div>

                        <div class="info-item">
                            <div class="info-icon info-icon-blue">
                                <i class="check circle icon"></i>
                            </div>

                            <div class="info-content">
                                <h4>Datos precisos</h4>
                                <p>Proporciona información veraz sobre edad, género y características.</p>
                            </div>
                        </div>
                    </div>

                    <div class="responsive-alert">
                        <i class="info circle icon"></i>
                        <span>Las mascotas deben estar esterilizadas según el reglamento del condominio.</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<style>
    :root {
        --primary: #667eea;
        --primary-dark: #4f46e5;
        --secondary: #764ba2;
        --success: #10b981;
        --success-dark: #059669;
        --danger: #ef4444;
        --danger-dark: #dc2626;
        --warning: #f59e0b;
        --text-main: #0f172a;
        --text-muted: #64748b;
        --border: #e2e8f0;
        --surface: #ffffff;
        --soft-bg: #f8fafc;
    }

    .mascota-page {
        width: 100%;
        max-width: 100%;
    }

    .mascota-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        flex-wrap: wrap;
        margin-bottom: 22px;
    }

    .mascota-header-left {
        display: flex;
        align-items: center;
        gap: 16px;
        min-width: 0;
    }

    .mascota-header-left > div:last-child {
        min-width: 0;
    }

    .mascota-page-icon {
        background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
    }

    .mascota-header .page-title,
    .mascota-header .page-subtitle {
        word-break: break-word;
    }

    .mascota-header-actions {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }

    .btn-volver {
        min-height: 42px;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        white-space: nowrap;
    }

    .mascota-layout {
        display: grid;
        grid-template-columns: minmax(0, 1fr) minmax(320px, 0.48fr);
        gap: 24px;
        align-items: start;
        width: 100%;
    }

    .mascota-form-card,
    .mascota-info-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 22px;
        overflow: hidden;
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.06);
        min-width: 0;
    }

    .mascota-info-card {
        position: sticky;
        top: 18px;
    }

    .mascota-card-header {
        padding: 18px 20px;
        border-bottom: 1px solid #f1f5f9;
        background: linear-gradient(180deg, #ffffff 0%, #fbfdff 100%);
    }

    .mascota-card-title-wrap {
        display: flex;
        align-items: center;
        gap: 14px;
        min-width: 0;
    }

    .mascota-card-icon {
        width: 52px;
        height: 52px;
        border-radius: 16px;
        background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        box-shadow: 0 8px 18px rgba(102, 126, 234, 0.24);
    }

    .mascota-card-icon.secondary {
        background: linear-gradient(135deg, #0ea5e9 0%, #2563eb 100%);
    }

    .mascota-card-icon i {
        margin: 0;
        font-size: 1.25rem;
    }

    .mascota-card-title {
        margin: 0;
        font-size: 1.12rem;
        font-weight: 800;
        color: var(--text-main);
        line-height: 1.25;
    }

    .mascota-card-subtitle {
        margin: 4px 0 0 0;
        color: var(--text-muted);
        font-size: 0.9rem;
        line-height: 1.35;
    }

    .mascota-card-body {
        padding: 20px;
        min-width: 0;
    }

    .mascota-input {
        min-height: 46px;
        border-radius: 14px !important;
        width: 100%;
        max-width: 100%;
        box-sizing: border-box;
    }

    .mascota-textarea {
        resize: vertical;
        min-height: 96px;
    }

    .input-icon-wrapper {
        position: relative;
        display: flex;
        align-items: center;
        width: 100%;
    }

    .input-icon-wrapper > i {
        position: absolute;
        left: 14px;
        color: var(--primary);
        font-size: 1.05rem;
        pointer-events: none;
        z-index: 1;
        margin: 0;
    }

    .input-with-icon {
        padding-left: 42px !important;
    }

    .responsive-grid-2 {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 18px;
    }

    .checkbox-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 14px;
        margin-bottom: 22px;
    }

    .checkbox-card {
        display: block;
        cursor: pointer;
        position: relative;
        min-width: 0;
    }

    .checkbox-card input {
        position: absolute;
        opacity: 0;
        pointer-events: none;
    }

    .checkbox-content {
        min-height: 112px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 10px;
        padding: 16px;
        border: 2px solid var(--border);
        border-radius: 16px;
        transition: all 0.2s ease;
        text-align: center;
        background: #ffffff;
    }

    .checkbox-icon {
        width: 42px;
        height: 42px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: var(--soft-bg);
        color: #94a3b8;
        transition: all 0.2s ease;
        flex-shrink: 0;
    }

    .checkbox-icon i {
        margin: 0;
        font-size: 1.15rem;
    }

    .checkbox-content strong {
        display: block;
        color: var(--text-main);
        font-size: 0.86rem;
        font-weight: 800;
        line-height: 1.2;
        margin-bottom: 3px;
    }

    .checkbox-content span {
        display: block;
        color: var(--text-muted);
        font-size: 0.76rem;
        line-height: 1.2;
    }

    .checkbox-card input:checked + .checkbox-content {
        border-color: rgba(102, 126, 234, 0.65);
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.10) 0%, rgba(118, 75, 162, 0.10) 100%);
        box-shadow: 0 8px 20px rgba(102, 126, 234, 0.12);
    }

    .checkbox-card input:checked + .checkbox-content .checkbox-icon {
        background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        color: #ffffff;
    }

    .checkbox-card:hover .checkbox-content {
        border-color: rgba(102, 126, 234, 0.65);
        transform: translateY(-2px);
    }

    .responsive-upload-area {
        width: 100%;
        min-height: 230px;
        border-radius: 18px;
        border: 2px dashed #cbd5e1;
        background: var(--soft-bg);
        transition: all 0.2s ease;
        overflow: hidden;
    }

    .responsive-upload-area:hover,
    .responsive-upload-area.dragover {
        border-color: var(--primary);
        background: #f0f4ff;
    }

    .upload-label {
        display: block;
        width: 100%;
        cursor: pointer;
        margin: 0;
    }

    .upload-placeholder {
        min-height: 230px;
        padding: 26px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }

    .upload-icon {
        width: 68px;
        height: 68px;
        border-radius: 20px;
        background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 14px;
        box-shadow: 0 8px 18px rgba(102, 126, 234, 0.24);
    }

    .upload-icon i {
        margin: 0;
        font-size: 1.55rem;
    }

    .upload-text {
        display: flex;
        flex-direction: column;
        text-align: center;
        gap: 4px;
    }

    .upload-text .primary {
        color: var(--text-main);
        font-weight: 800;
        font-size: 1rem;
    }

    .upload-text .secondary {
        color: var(--text-muted);
        font-size: 0.9rem;
    }

    .upload-formats {
        color: #94a3b8;
        font-size: 0.82rem;
        margin-top: 8px;
        text-align: center;
    }

    .hidden {
        display: none !important;
    }

    .preview-upload {
        position: relative;
        padding: 18px;
        text-align: center;
        min-height: 230px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        gap: 12px;
        background: #ffffff;
    }

    .foto-preview {
        max-width: 100%;
        width: auto;
        max-height: 250px;
        object-fit: contain;
        border-radius: 16px;
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.12);
    }

    .remove-preview-btn {
        position: absolute;
        top: 12px;
        right: 12px;
        width: 34px;
        height: 34px;
        border: none;
        border-radius: 999px;
        background: var(--danger);
        color: #ffffff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        box-shadow: 0 8px 18px rgba(239, 68, 68, 0.24);
        z-index: 2;
    }

    .remove-preview-btn:hover {
        background: var(--danger-dark);
    }

    .remove-preview-btn i {
        margin: 0;
    }

    .preview-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 7px 12px;
        background: #dcfce7;
        color: #166534;
        border-radius: 999px;
        font-size: 0.78rem;
        font-weight: 800;
    }

    .preview-badge i {
        margin: 0;
    }

    .form-actions {
        display: flex;
        gap: 12px;
        margin-top: 24px;
    }

    .btn-submit-mascota {
        flex: 1;
        min-height: 48px;
        border-radius: 14px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .info-list {
        display: grid;
        gap: 18px;
    }

    .info-item {
        display: flex;
        gap: 14px;
        align-items: flex-start;
        min-width: 0;
        background: var(--soft-bg);
        border: 1px solid #eef2f7;
        border-radius: 16px;
        padding: 14px;
    }

    .info-icon {
        width: 46px;
        height: 46px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        color: white;
    }

    .info-icon i {
        margin: 0;
    }

    .info-icon-purple {
        background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
    }

    .info-icon-pink {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    }

    .info-icon-blue {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    }

    .info-content {
        min-width: 0;
    }

    .info-content h4 {
        margin: 0 0 4px 0;
        color: var(--text-main);
        font-size: 0.98rem;
        line-height: 1.3;
        font-weight: 800;
        word-break: break-word;
    }

    .info-content p {
        margin: 0;
        color: var(--text-muted);
        font-size: 0.88rem;
        line-height: 1.5;
        word-break: break-word;
    }

    .responsive-alert {
        margin-top: 20px;
        display: flex;
        align-items: flex-start;
        gap: 9px;
        line-height: 1.5;
        background: #fef3c7;
        border: 1px solid #fde68a;
        color: #92400e;
        border-radius: 16px;
        padding: 13px 14px;
        font-size: 0.9rem;
        font-weight: 600;
    }

    .responsive-alert i {
        flex-shrink: 0;
        margin: 2px 0 0 0;
    }

    @media (max-width: 1180px) {
        .mascota-layout {
            grid-template-columns: 1fr;
        }

        .mascota-info-card {
            position: static;
        }
    }

    @media (max-width: 768px) {
        .mascota-header {
            align-items: flex-start;
        }

        .mascota-header-left {
            width: 100%;
            align-items: flex-start;
        }

        .mascota-header-actions {
            width: 100%;
        }

        .btn-volver {
            width: 100%;
        }

        .page-title {
            font-size: 1.45rem;
            line-height: 1.2;
        }

        .page-subtitle {
            font-size: 0.9rem;
        }

        .mascota-form-card,
        .mascota-info-card {
            border-radius: 18px;
        }

        .mascota-card-header,
        .mascota-card-body {
            padding: 16px;
        }

        .mascota-card-icon {
            width: 46px;
            height: 46px;
            border-radius: 14px;
        }

        .mascota-card-title {
            font-size: 1.04rem;
        }

        .mascota-card-subtitle {
            font-size: 0.85rem;
        }

        .responsive-grid-2 {
            grid-template-columns: 1fr;
            gap: 0;
        }

        .checkbox-grid {
            grid-template-columns: 1fr;
            gap: 12px;
        }

        .checkbox-content {
            min-height: auto;
            flex-direction: row;
            justify-content: flex-start;
            text-align: left;
            padding: 14px 16px;
        }

        .checkbox-content strong {
            font-size: 0.9rem;
        }

        .checkbox-icon {
            width: 40px;
            height: 40px;
            border-radius: 13px;
        }

        .form-actions {
            flex-direction: column;
        }

        .btn-submit-mascota {
            width: 100%;
        }

        .upload-placeholder {
            min-height: 200px;
            padding: 20px 16px;
        }

        .responsive-upload-area,
        .preview-upload {
            min-height: 200px;
        }

        .foto-preview {
            max-height: 220px;
        }
    }

    @media (max-width: 576px) {
        .mascota-header-left {
            gap: 12px;
        }

        .mascota-header-left .page-icon {
            width: 44px;
            height: 44px;
            flex-shrink: 0;
        }

        .mascota-header .page-title {
            font-size: 1.35rem;
        }

        .info-item {
            gap: 12px;
        }

        .info-icon {
            width: 42px;
            height: 42px;
            border-radius: 12px;
        }

        .upload-icon {
            width: 58px;
            height: 58px;
            border-radius: 18px;
        }

        .upload-text .primary {
            font-size: 0.95rem;
        }

        .upload-text .secondary,
        .upload-formats {
            font-size: 0.85rem;
        }

        .preview-upload {
            padding: 14px;
        }

        .foto-preview {
            max-height: 190px;
        }
    }

    @media (max-width: 380px) {
        .mascota-header-left {
            flex-direction: column;
        }

        .mascota-card-title-wrap,
        .info-item {
            align-items: flex-start;
        }

        .info-item {
            flex-direction: column;
        }
    }

    @media (hover: none) {
        .checkbox-card:hover .checkbox-content {
            transform: none;
        }
    }
</style>

<script>
    const uploadArea = document.getElementById('upload-area-foto');
    const fileInput = document.getElementById('foto-input');
    const uploadPlaceholder = document.getElementById('upload-placeholder');
    const previewUpload = document.getElementById('preview-upload');
    const fotoPreview = document.getElementById('foto-preview');
    const removePreviewBtn = document.getElementById('remove-preview-btn');

    if (uploadArea && fileInput && uploadPlaceholder && previewUpload && fotoPreview) {
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            uploadArea.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        ['dragenter', 'dragover'].forEach(eventName => {
            uploadArea.addEventListener(eventName, () => {
                uploadArea.classList.add('dragover');
            }, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            uploadArea.addEventListener(eventName, () => {
                uploadArea.classList.remove('dragover');
            }, false);
        });

        uploadArea.addEventListener('drop', (e) => {
            const dt = e.dataTransfer;
            const files = dt.files;

            if (files.length > 0) {
                fileInput.files = files;
                handleFileSelect(files[0]);
            }
        });

        fileInput.addEventListener('change', function() {
            if (this.files.length > 0) {
                handleFileSelect(this.files[0]);
            }
        });
    }

    if (removePreviewBtn) {
        removePreviewBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();

            fileInput.value = '';
            limpiarVistaPrevia();
        });
    }

    function limpiarVistaPrevia() {
        if (fotoPreview) {
            fotoPreview.removeAttribute('src');
            fotoPreview.style.display = 'none';
        }

        if (uploadPlaceholder) {
            uploadPlaceholder.classList.remove('hidden');
        }

        if (previewUpload) {
            previewUpload.classList.add('hidden');
        }
    }

    function handleFileSelect(file) {
        const maxSize = 5 * 1024 * 1024;
        const allowedTypes = ['image/jpeg', 'image/png'];

        if (!allowedTypes.includes(file.type)) {
            alertify.error('Solo se permiten imágenes JPG o PNG');
            fileInput.value = '';
            limpiarVistaPrevia();
            return;
        }

        if (file.size > maxSize) {
            alertify.error('La imagen no puede superar los 5MB');
            fileInput.value = '';
            limpiarVistaPrevia();
            return;
        }

        const reader = new FileReader();

        reader.onload = function(e) {
            fotoPreview.src = e.target.result;
            fotoPreview.style.display = 'inline-block';

            uploadPlaceholder.classList.add('hidden');
            previewUpload.classList.remove('hidden');
        };

        reader.readAsDataURL(file);
    }

    $('.checkbox-card input').on('change', function () {
        const card = $(this).closest('.checkbox-card');

        if ($(this).is(':checked')) {
            card.addClass('checked');
        } else {
            card.removeClass('checked');
        }
    });

    $('#mascota-form').on('submit', function(e) {
        e.preventDefault();

        var form = $(this);
        var button = $('#btn-guardar');
        var formData = new FormData(form[0]);

        button.addClass('loading disabled');

        $.ajax({
            url: form.attr('action'),
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            beforeSend: function(xhr) {
                var token = $('meta[name="csrf-token"]').attr('content');

                if (token) {
                    xhr.setRequestHeader('X-CSRF-TOKEN', token);
                }
            },
            success: function(response) {
                if (response.success) {
                    alertify.success(response.message);

                    form[0].reset();

                    $('#tipo-mascota').dropdown('clear');
                    $('#genero-mascota').dropdown('clear');

                    limpiarVistaPrevia();

                    $('.checkbox-card').removeClass('checked');
                    $('input[name="amistoso"]').prop('checked', true).closest('.checkbox-card').addClass('checked');
                } else {
                    alertify.error(response.message || 'No se pudo registrar la mascota.');
                }
            },
            error: function(xhr) {
                var message = 'Error al procesar la solicitud.';

                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }

                alertify.error(message);
            },
            complete: function() {
                button.removeClass('loading disabled');
            }
        });
    });

    $(document).ready(function() {
        $('#tipo-mascota').dropdown();
        $('#genero-mascota').dropdown();
    });
</script>