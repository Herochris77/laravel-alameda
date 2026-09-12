<x-app-layout>
    <div class="mascotas-page">
        <div class="page-header mascotas-header">
            <div class="page-header-left">
                <div class="page-icon mascotas-page-icon">
                    <i class="paw icon"></i>
                </div>
                <div>
                    <h1 class="page-title">Mascotas</h1>
                    <p class="page-subtitle">Todas las mascotas registradas en el condominio</p>
                </div>
            </div>

            <div class="page-header-actions mascotas-actions">
                <div class="filter-wrapper">
                    <label class="filter-label">Filtrar mascotas</label>
                    <select class="ui fluid dropdown filtro-tipo-dropdown" id="filtro-tipo" onchange="aplicarFiltro()">
                        <option value="todos" {{ request('filtro') == 'todos' || !request('filtro') ? 'selected' : '' }}>Todas</option>
                        <option value="mis-mascotas" {{ request('filtro') == 'mis-mascotas' ? 'selected' : '' }}>Mis mascotas</option>
                        <option value="perro" {{ request('filtro') == 'perro' ? 'selected' : '' }}>Perros</option>
                        <option value="gato" {{ request('filtro') == 'gato' ? 'selected' : '' }}>Gatos</option>
                        <option value="otro" {{ request('filtro') == 'otro' ? 'selected' : '' }}>Otros</option>
                    </select>
                </div>

                <a href="{{ route('usuario.mascota.crear') }}" class="btn btn-success nueva-mascota-btn">
                    <i class="plus icon"></i>
                    Nueva Mascota
                </a>
            </div>
        </div>

        <div class="mascotas-summary-card">
            <div class="summary-item">
                <div class="summary-icon">
                    <i class="paw icon"></i>
                </div>
                <div>
                    <span class="summary-label">Mascotas encontradas</span>
                    <strong class="summary-value">{{ $mascotas->count() }}</strong>
                </div>
            </div>

            <div class="summary-helper">
                <i class="info circle icon"></i>
                <span>Solo puedes editar o eliminar las mascotas registradas por ti.</span>
            </div>
        </div>

        @if($mascotas->isEmpty())
            <div class="card empty-mascotas-card">
                <div class="empty-state mascotas-empty-state">
                    <div class="empty-state-icon">
                        <i class="paw icon"></i>
                    </div>
                    <h3>No hay mascotas registradas</h3>
                    <p>No se encontraron mascotas con el filtro seleccionado.</p>

                    <div class="empty-actions">
                        <a href="{{ route('usuario.mascota.crear') }}" class="btn btn-primary">
                            <i class="plus icon"></i>
                            Registrar primera mascota
                        </a>

                        @if(request('filtro'))
                            <a href="{{ route('usuario.mascota.index') }}" class="btn btn-secondary">
                                <i class="undo icon"></i>
                                Ver todas
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        @else
            <div class="mascotas-grid">
                @foreach($mascotas as $mascota)
                    @php
                        $esMia = Auth::user()->id === $mascota->user_id;

                        $tipoClase = match($mascota->tipo) {
                            'perro' => 'perro',
                            'gato' => 'gato',
                            default => 'otro',
                        };

                        $tipoIcono = match($mascota->tipo) {
                            'perro' => 'dog',
                            'gato' => 'cat',
                            default => 'paw',
                        };

                        $generoIcono = $mascota->genero === 'macho' ? 'mars' : 'venus';
                        $generoClase = $mascota->genero === 'macho' ? 'macho' : 'hembra';
                        $generoTexto = $mascota->genero === 'macho' ? 'Macho' : 'Hembra';
                    @endphp

                    <div class="mascota-card">
                        <div class="mascota-image-wrap">
                            @if($mascota->foto && file_exists(storage_path('app/public/' . $mascota->foto)))
                                <img
                                    src="{{ asset('storage/' . $mascota->foto) }}"
                                    alt="Foto de {{ $mascota->nombre }}"
                                    class="mascota-image"
                                    onclick="abrirModalMascota('{{ asset('storage/' . $mascota->foto) }}', '{{ addslashes($mascota->nombre) }}')"
                                >

                                <button
                                    type="button"
                                    class="zoom-photo-btn"
                                    onclick="abrirModalMascota('{{ asset('storage/' . $mascota->foto) }}', '{{ addslashes($mascota->nombre) }}')"
                                    title="Ver foto"
                                >
                                    <i class="search plus icon"></i>
                                </button>
                            @else
                                <div class="mascota-placeholder">
                                    <i class="paw icon"></i>
                                </div>
                            @endif

                            <div class="mascota-top-badges">
                                @if($esMia)
                                    <span class="owner-badge">
                                        <i class="user check icon"></i>
                                        Mi mascota
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="mascota-card-body">
                            <div class="mascota-title-row">
                                <div>
                                    <h3 class="mascota-nombre">{{ $mascota->nombre }}</h3>
                                    <p class="mascota-owner">
                                        <i class="user icon"></i>
                                        {{ $mascota->user->nombre ?? 'Dueño desconocido' }}
                                    </p>
                                </div>
                            </div>

                            <div class="mascota-location">
                                <i class="home icon"></i>
                                <span>Casa {{ $mascota->user->casa ?? 'N/A' }}</span>
                            </div>

                            <div class="mascota-meta-grid">
                                <span class="pet-type-badge {{ $tipoClase }}">
                                    <i class="{{ $tipoIcono }} icon"></i>
                                    {{ ucfirst($mascota->tipo) }}
                                </span>

                                <span class="pet-age-badge">
                                    <i class="calendar icon"></i>
                                    {{ $mascota->edad }} años
                                </span>

                                <span class="pet-gender-badge {{ $generoClase }}">
                                    <i class="{{ $generoIcono }} icon"></i>
                                    {{ $generoTexto }}
                                </span>
                            </div>

                            <div class="pet-status-list">
                                <span class="pet-status {{ $mascota->vacunas ? 'ok' : 'bad' }}">
                                    <i class="{{ $mascota->vacunas ? 'check circle' : 'times circle' }} icon"></i>
                                    Vacunas
                                </span>

                                <span class="pet-status {{ $mascota->esterilizado ? 'ok' : 'bad' }}">
                                    <i class="{{ $mascota->esterilizado ? 'check circle' : 'times circle' }} icon"></i>
                                    Esterilizado
                                </span>

                                <span class="pet-status {{ $mascota->amistoso ? 'friendly' : 'neutral' }}">
                                    <i class="heart icon"></i>
                                    {{ $mascota->amistoso ? 'Amigable' : 'Cauteloso' }}
                                </span>
                            </div>

                            @if($mascota->caracteristicas)
                                <div class="pet-description">
                                    <i class="info circle icon"></i>
                                    <span>{{ Str::limit($mascota->caracteristicas, 95) }}</span>
                                </div>
                            @else
                                <div class="pet-description muted">
                                    <i class="info circle icon"></i>
                                    <span>Sin características adicionales.</span>
                                </div>
                            @endif
                        </div>

                        @if($esMia)
                            <div class="mascota-card-footer">
                                <a href="{{ route('usuario.mascota.editar', $mascota->id) }}" class="btn btn-secondary btn-sm mascota-action-btn">
                                    <i class="edit icon"></i>
                                    Editar
                                </a>

                                <button type="button" class="btn btn-danger btn-sm mascota-delete-btn" onclick="eliminarMascota({{ $mascota->id }})">
                                    <i class="trash icon"></i>
                                    <span>Eliminar</span>
                                </button>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif

        <div id="modalMascotaImagen" class="mascota-modal" style="display: none;">
            <div class="mascota-modal-dialog">
                <button
                    type="button"
                    onclick="cerrarModalMascota()"
                    class="mascota-modal-close"
                    aria-label="Cerrar"
                >
                    <i class="times icon"></i>
                </button>

                <div class="mascota-modal-content">
                    <img
                        id="modalMascotaImagenSrc"
                        src=""
                        alt=""
                        class="mascota-modal-image"
                    >

                    <div class="mascota-modal-footer">
                        <div>
                            <span class="modal-label">Foto de mascota</span>
                            <h3 id="modalMascotaTitulo"></h3>
                        </div>

                        <button type="button" class="btn btn-secondary btn-sm" onclick="cerrarModalMascota()">
                            <i class="times icon"></i>
                            Cerrar
                        </button>
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

    .mascotas-page {
        width: 100%;
    }

    .mascotas-header {
        gap: 20px;
    }

    .mascotas-page-icon {
        background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
    }

    .mascotas-actions {
        display: flex;
        align-items: flex-end;
        gap: 12px;
        flex-wrap: wrap;
    }

    .filter-wrapper {
        min-width: 190px;
    }

    .filter-label {
        display: block;
        font-size: 0.78rem;
        font-weight: 800;
        color: var(--text-muted);
        margin-bottom: 6px;
    }

    .filtro-tipo-dropdown {
        min-height: 42px;
        border-radius: 12px !important;
    }

    .nueva-mascota-btn {
        min-height: 42px;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        white-space: nowrap;
    }

    .mascotas-summary-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 20px;
        padding: 18px 20px;
        margin-bottom: 22px;
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.06);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
    }

    .summary-item {
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .summary-icon {
        width: 48px;
        height: 48px;
        border-radius: 16px;
        background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        box-shadow: 0 8px 18px rgba(102, 126, 234, 0.25);
    }

    .summary-icon i {
        margin: 0;
        font-size: 1.2rem;
    }

    .summary-label {
        display: block;
        color: var(--text-muted);
        font-size: 0.85rem;
        margin-bottom: 2px;
    }

    .summary-value {
        color: var(--text-main);
        font-size: 1.6rem;
        line-height: 1;
    }

    .summary-helper {
        display: flex;
        align-items: center;
        gap: 8px;
        color: var(--text-muted);
        font-size: 0.9rem;
        background: var(--soft-bg);
        border-radius: 999px;
        padding: 10px 14px;
    }

    .summary-helper i {
        color: var(--primary);
        margin: 0;
    }

    .mascotas-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 20px;
    }

    .mascota-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 22px;
        overflow: hidden;
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.06);
        display: flex;
        flex-direction: column;
        min-height: 100%;
        transition: transform 0.25s ease, box-shadow 0.25s ease, border-color 0.25s ease;
    }

    .mascota-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 16px 34px rgba(15, 23, 42, 0.12);
        border-color: rgba(102, 126, 234, 0.35);
    }

    .mascota-image-wrap {
        position: relative;
        height: 210px;
        background: var(--soft-bg);
        overflow: hidden;
    }

    .mascota-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        cursor: zoom-in;
        display: block;
        transition: transform 0.3s ease;
    }

    .mascota-card:hover .mascota-image {
        transform: scale(1.04);
    }

    .mascota-placeholder {
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .mascota-placeholder i {
        font-size: 4rem;
        color: white;
        opacity: 0.55;
        margin: 0;
    }

    .zoom-photo-btn {
        position: absolute;
        left: 12px;
        bottom: 12px;
        width: 38px;
        height: 38px;
        border: none;
        border-radius: 999px;
        background: rgba(15, 23, 42, 0.72);
        color: white;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        backdrop-filter: blur(6px);
        transition: transform 0.2s ease, background 0.2s ease;
    }

    .zoom-photo-btn:hover {
        transform: scale(1.06);
        background: rgba(15, 23, 42, 0.9);
    }

    .zoom-photo-btn i {
        margin: 0;
    }

    .mascota-top-badges {
        position: absolute;
        top: 12px;
        right: 12px;
        display: flex;
        align-items: center;
        gap: 8px;
        z-index: 2;
    }

    .owner-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #dbeafe;
        color: #1d4ed8;
        border-radius: 999px;
        padding: 7px 11px;
        font-size: 0.75rem;
        font-weight: 800;
        box-shadow: 0 8px 18px rgba(15, 23, 42, 0.12);
    }

    .owner-badge i {
        margin: 0;
    }

    .mascota-card-body {
        padding: 18px;
        flex: 1;
    }

    .mascota-title-row {
        display: flex;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 12px;
    }

    .mascota-nombre {
        margin: 0;
        font-size: 1.18rem;
        color: var(--text-main);
        font-weight: 800;
        line-height: 1.25;
        overflow-wrap: anywhere;
    }

    .mascota-owner {
        margin: 6px 0 0 0;
        color: var(--text-muted);
        font-size: 0.9rem;
        display: flex;
        align-items: center;
        gap: 7px;
        overflow-wrap: anywhere;
    }

    .mascota-owner i,
    .mascota-location i {
        color: var(--primary);
        margin: 0;
        flex-shrink: 0;
    }

    .mascota-location {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        color: var(--text-muted);
        font-size: 0.88rem;
        background: var(--soft-bg);
        border: 1px solid #eef2f7;
        border-radius: 999px;
        padding: 7px 11px;
        margin-bottom: 14px;
    }

    .mascota-meta-grid {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-bottom: 14px;
    }

    .pet-type-badge,
    .pet-age-badge,
    .pet-gender-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        border-radius: 999px;
        padding: 7px 11px;
        font-size: 0.78rem;
        font-weight: 800;
        line-height: 1;
    }

    .pet-type-badge i,
    .pet-age-badge i,
    .pet-gender-badge i {
        margin: 0;
    }

    .pet-type-badge.perro {
        background: #fef3c7;
        color: #92400e;
    }

    .pet-type-badge.gato {
        background: #ffedd5;
        color: #9a3412;
    }

    .pet-type-badge.otro {
        background: #e2e8f0;
        color: #475569;
    }

    .pet-age-badge {
        background: #ede9fe;
        color: #5b21b6;
    }

    .pet-gender-badge.macho {
        background: #dbeafe;
        color: #1d4ed8;
    }

    .pet-gender-badge.hembra {
        background: #fce7f3;
        color: #be185d;
    }

    .pet-status-list {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-bottom: 14px;
    }

    .pet-status {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 0.78rem;
        font-weight: 800;
        border-radius: 999px;
        padding: 7px 10px;
    }

    .pet-status i {
        margin: 0;
    }

    .pet-status.ok {
        background: #dcfce7;
        color: #166534;
    }

    .pet-status.bad {
        background: #fee2e2;
        color: #991b1b;
    }

    .pet-status.friendly {
        background: #fce7f3;
        color: #be185d;
    }

    .pet-status.neutral {
        background: #f1f5f9;
        color: #64748b;
    }

    .pet-description {
        display: flex;
        align-items: flex-start;
        gap: 8px;
        color: var(--text-muted);
        font-size: 0.87rem;
        line-height: 1.45;
        background: var(--soft-bg);
        border: 1px solid #eef2f7;
        border-radius: 14px;
        padding: 11px 12px;
    }

    .pet-description i {
        color: var(--primary);
        margin: 2px 0 0 0;
        flex-shrink: 0;
    }

    .pet-description.muted {
        color: #94a3b8;
    }

    .mascota-card-footer {
        padding: 16px 18px 18px;
        border-top: 1px solid #f1f5f9;
        display: grid;
        grid-template-columns: 1fr auto;
        gap: 10px;
    }

    .mascota-action-btn,
    .mascota-delete-btn {
        min-height: 42px;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        white-space: nowrap;
    }

    .mascota-delete-btn {
        padding-left: 14px;
        padding-right: 14px;
    }

    .empty-mascotas-card {
        border-radius: 22px;
        border: 1px solid var(--border);
    }

    .mascotas-empty-state {
        padding: 48px 20px;
    }

    .empty-actions {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        flex-wrap: wrap;
        margin-top: 18px;
    }

    .empty-actions .btn {
        min-height: 42px;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .mascota-modal {
        position: fixed;
        z-index: 9999;
        inset: 0;
        background: rgba(15, 23, 42, 0.88);
        align-items: center;
        justify-content: center;
        padding: 24px;
        backdrop-filter: blur(4px);
    }

    .mascota-modal-dialog {
        position: relative;
        max-width: 920px;
        width: 100%;
    }

    .mascota-modal-close {
        position: absolute;
        top: -46px;
        right: 0;
        width: 40px;
        height: 40px;
        border: none;
        border-radius: 999px;
        background: var(--danger);
        color: white;
        cursor: pointer;
        font-size: 16px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 12px 24px rgba(239, 68, 68, 0.25);
    }

    .mascota-modal-close:hover {
        background: var(--danger-dark);
    }

    .mascota-modal-close i {
        margin: 0;
    }

    .mascota-modal-content {
        background: white;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 24px 70px rgba(0, 0, 0, 0.38);
    }

    .mascota-modal-image {
        width: 100%;
        max-height: 78vh;
        object-fit: contain;
        display: block;
        background: #0f172a;
    }

    .mascota-modal-footer {
        padding: 16px 18px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        border-top: 1px solid #f1f5f9;
    }

    .modal-label {
        display: block;
        color: var(--text-muted);
        font-size: 0.78rem;
        font-weight: 800;
        margin-bottom: 3px;
    }

    .mascota-modal-footer h3 {
        margin: 0;
        color: var(--text-main);
        font-size: 1.15rem;
        font-weight: 800;
    }

    @media (max-width: 1280px) {
        .mascotas-grid {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }
    }

    @media (max-width: 992px) {
        .mascotas-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .mascotas-summary-card {
            align-items: flex-start;
            flex-direction: column;
        }

        .summary-helper {
            width: 100%;
            border-radius: 14px;
            align-items: flex-start;
        }
    }

    @media (max-width: 768px) {
        .mascotas-header {
            align-items: flex-start;
        }

        .page-header-left {
            align-items: flex-start;
        }

        .page-title {
            font-size: 1.45rem;
            line-height: 1.2;
        }

        .page-subtitle {
            font-size: 0.9rem;
        }

        .mascotas-actions {
            width: 100%;
            display: grid;
            grid-template-columns: 1fr;
            align-items: stretch;
        }

        .filter-wrapper {
            min-width: 0;
        }

        .nueva-mascota-btn {
            width: 100%;
        }

        .mascotas-summary-card {
            border-radius: 18px;
            padding: 16px;
            margin-bottom: 18px;
        }

        .summary-icon {
            width: 44px;
            height: 44px;
            border-radius: 14px;
        }

        .summary-value {
            font-size: 1.35rem;
        }
    }

    @media (max-width: 640px) {
        .mascotas-grid {
            grid-template-columns: 1fr;
            gap: 16px;
        }

        .mascota-card {
            border-radius: 18px;
        }

        .mascota-image-wrap {
            height: 220px;
        }

        .mascota-card-body {
            padding: 16px;
        }

        .mascota-card-footer {
            padding: 14px 16px 16px;
            grid-template-columns: 1fr;
        }

        .mascota-action-btn,
        .mascota-delete-btn {
            width: 100%;
        }

        .mascota-delete-btn span {
            display: inline;
        }

        .empty-actions {
            flex-direction: column;
        }

        .empty-actions .btn {
            width: 100%;
        }

        .mascota-modal {
            padding: 14px;
        }

        .mascota-modal-close {
            top: 10px;
            right: 10px;
            z-index: 2;
        }

        .mascota-modal-content {
            border-radius: 18px;
        }

        .mascota-modal-footer {
            align-items: stretch;
            flex-direction: column;
        }

        .mascota-modal-footer .btn {
            width: 100%;
        }
    }

    @media (hover: none) {
        .mascota-card:hover {
            transform: none;
        }

        .mascota-card:hover .mascota-image {
            transform: none;
        }
    }
</style>

<script>
    function aplicarFiltro() {
        var filtro = document.getElementById('filtro-tipo').value;
        var url = '{{ route("usuario.mascota.index") }}';

        if (filtro === 'todos' || filtro === '') {
            window.location.href = url;
        } else {
            window.location.href = url + '?filtro=' + filtro;
        }
    }

    function abrirModalMascota(imagenUrl, nombreMascota) {
        var modal = document.getElementById('modalMascotaImagen');
        var imagen = document.getElementById('modalMascotaImagenSrc');
        var titulo = document.getElementById('modalMascotaTitulo');

        imagen.src = imagenUrl;
        imagen.alt = 'Foto de ' + nombreMascota;
        titulo.textContent = nombreMascota;

        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }

    function cerrarModalMascota() {
        var modal = document.getElementById('modalMascotaImagen');
        var imagen = document.getElementById('modalMascotaImagenSrc');

        modal.style.display = 'none';
        imagen.src = '';
        document.body.style.overflow = '';
    }

    function eliminarMascota(id) {
        alertify.confirm(
            'Eliminar Mascota',
            '¿Está seguro de que desea eliminar esta mascota?',
            function() {
                mostrarLoaderPantalla();

                setTimeout(() => {
                    $.ajax({
                        url: "{{ route('usuario.mascota.eliminar', ['id' => ':id']) }}".replace(':id', id),
                        type: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            if (response.success) {
                                alertify.success(response.message);

                                setTimeout(function() {
                                    location.reload();
                                }, 1200);
                            } else {
                                alertify.error(response.message || 'No se pudo eliminar la mascota.');
                            }
                        },
                        error: function() {
                            alertify.error('Ocurrió un error al eliminar la mascota.');
                        },
                        complete: function() {
                            ocultarLoaderPantalla();
                        }
                    });
                }, 100);
            },
            function() {
                alertify.message('Eliminación cancelada');
            }
        ).set('labels', {
            ok: 'Sí, eliminar',
            cancel: 'Cancelar'
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        $('#filtro-tipo').dropdown();

        var modal = document.getElementById('modalMascotaImagen');

        if (modal) {
            modal.addEventListener('click', function (event) {
                if (event.target === modal) {
                    cerrarModalMascota();
                }
            });
        }

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                cerrarModalMascota();
            }
        });
    });
</script>