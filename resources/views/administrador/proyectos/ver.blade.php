<x-app-layout>
    <style>
        .pa-page { --primary:#0d9488; --danger:#ef4444; --text:#0f172a; --muted:#64748b; --border:#e2e8f0; --surface:#fff; --bg:#f8fafc; padding-bottom:24px; }
        .pa-page, .pa-page * { box-sizing:border-box; }
        .pa-lightbox { display:none; position:fixed; inset:0; background:rgba(15,23,42,.88); z-index:9999; align-items:center; justify-content:center; padding:20px; cursor:zoom-out; }
        .pa-lightbox img { max-width:95%; max-height:95%; border-radius:12px; box-shadow:0 12px 44px rgba(0,0,0,.5); }
        .pa-lightbox .close { position:absolute; top:16px; right:20px; color:#fff; font-size:2rem; cursor:pointer; line-height:1; }
        .pa-container { max-width:1200px; margin:0 auto; }
        .pa-back { color:var(--muted); text-decoration:none; font-size:.9rem; display:inline-flex; align-items:center; gap:4px; margin-bottom:12px; }
        .pa-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(320px,1fr)); gap:16px; }
        .pa-card { background:var(--surface); border:1px solid var(--border); border-radius:16px; padding:18px 20px; box-shadow:0 6px 18px rgba(15,23,42,.05); }
        .pa-card h3 { margin:0 0 12px; font-size:1rem; display:flex; align-items:center; gap:8px; }
        .pa-card h3 i { color:#0d9488; }
        .pa-label { display:block; font-size:13px; color:var(--muted); margin:8px 0 5px; font-weight:600; }
        .pa-input, .pa-select, .pa-textarea { width:100%; padding:9px 12px; border:1px solid #cbd5e1; border-radius:10px; font-size:14px; }
        .pa-btn { border:none; border-radius:10px; padding:10px 16px; font-weight:700; cursor:pointer; font-size:14px; background:#0d9488; color:#fff; }
        .pa-btn.ghost { background:#f1f5f9; color:#0f172a; }
        .pa-btn.danger { background:#fef2f2; color:#991b1b; }
        .slider-row { display:flex; align-items:center; gap:12px; }
        .slider-row input[type=range] { flex:1; }
        .slider-val { font-size:1.3rem; font-weight:900; min-width:56px; text-align:right; }
        .av-item { padding:10px 0; border-bottom:1px solid var(--bg); font-size:.9rem; }
        .av-item:last-child { border-bottom:none; }
        .flash { background:#f0fdf4; color:#166534; border:1px solid #a7f3d0; border-radius:10px; padding:10px 14px; margin-bottom:14px; font-size:.9rem; }
        .pa-upload { display:flex; flex-direction:column; align-items:center; gap:6px; border:1.5px dashed #cbd5e1; border-radius:12px; padding:16px; text-align:center; color:#64748b; cursor:pointer; font-size:.88rem; transition:.15s; }
        .pa-upload:hover { border-color:#0d9488; background:#f0fdfa; color:#0f766e; }
        .pa-upload i { font-size:1.5rem; margin:0 !important; }
        .pa-upload .fname { font-weight:600; color:#0f766e; word-break:break-all; }
    </style>

    <div class="pa-page">
        <div class="pa-container">
            <a href="{{ route('admin.proyectos.index') }}" class="pa-back"><i class="arrow left icon"></i> Volver a proyectos</a>
            @if(session('ok'))<div class="flash"><i class="check circle icon"></i> {{ session('ok') }}</div>@endif

            <div class="pa-grid">
                <div class="pa-card">
                    <h3><i class="edit icon"></i> Datos del proyecto</h3>
                    <form method="POST" action="{{ route('admin.proyectos.actualizar', $proyecto->id) }}">
                        @csrf
                        <label class="pa-label">Nombre *</label>
                        <input type="text" name="nombre" class="pa-input" value="{{ $proyecto->nombre }}" required>
                        <label class="pa-label">Descripción</label>
                        <textarea name="descripcion" class="pa-textarea" rows="2">{{ $proyecto->descripcion }}</textarea>
                        <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
                            <div><label class="pa-label">Costo <span style="color:#94a3b8;">(opcional)</span></label><input type="number" step="0.01" min="0" name="costo" class="pa-input" value="{{ $proyecto->costo }}" placeholder="Dejar en blanco si no aplica"></div>
                            <div><label class="pa-label">Estado</label>
                                <select name="estado" class="pa-select">
                                    @foreach($estados as $k => $v)<option value="{{ $k }}" @selected($proyecto->estado === $k)>{{ $v }}</option>@endforeach
                                </select>
                            </div>
                        </div>
                        <button type="submit" class="pa-btn" style="margin-top:14px;"><i class="save icon"></i> Guardar</button>
                    </form>

                    <form method="POST" action="{{ route('admin.proyectos.eliminar', $proyecto->id) }}" style="margin-top:12px;" data-confirm="¿Eliminar este proyecto y todos sus avances? Esta acción no se puede deshacer.">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="pa-btn danger"><i class="trash icon"></i> Eliminar proyecto</button>
                    </form>
                </div>

                <div class="pa-card">
                    <h3><i class="chart line icon"></i> Registrar avance</h3>
                    <form method="POST" action="{{ route('admin.proyectos.avance', $proyecto->id) }}" enctype="multipart/form-data">
                        @csrf
                        <label class="pa-label">Porcentaje de avance</label>
                        <div class="slider-row">
                            <input type="range" id="pa-range" name="porcentaje" min="0" max="100" value="{{ $proyecto->avance }}" oninput="document.getElementById('pa-val').textContent=this.value+'%';document.getElementById('pa-fill').style.width=this.value+'%';">
                            <span class="slider-val" id="pa-val">{{ $proyecto->avance }}%</span>
                        </div>
                        <div style="height:8px; background:var(--bg); border-radius:99px; overflow:hidden; margin:6px 0 14px;"><div id="pa-fill" style="width:{{ $proyecto->avance }}%; height:100%; background:#0d9488;"></div></div>

                        <label class="pa-label">Comentario para los vecinos *</label>
                        <textarea name="comentario" class="pa-textarea" rows="3" required placeholder="Ej.: Se terminó la azotea del edificio A, va la B esta semana."></textarea>

                        <label class="pa-label">Evidencia (foto) <span style="color:#94a3b8;">— opcional</span></label>
                        <label class="pa-upload" for="foto-avance">
                            <i class="camera icon"></i>
                            <span id="foto-avance-text">Toca para tomar o subir una foto</span>
                            <input type="file" id="foto-avance" name="foto" accept="image/*" hidden
                                onchange="document.getElementById('foto-avance-text').innerHTML = this.files[0] ? '<span class=\'fname\'>'+this.files[0].name+'</span>' : 'Toca para tomar o subir una foto';">
                        </label>

                        <button type="submit" class="pa-btn" style="margin-top:12px;"><i class="send icon"></i> Publicar avance</button>
                    </form>

                    <div style="margin-top:16px;">
                        <div style="font-size:13px; color:var(--muted); margin-bottom:6px;">Últimos avances</div>
                        @forelse($proyecto->avances as $a)
                            <div class="av-item">
                                <div style="display:flex; gap:10px; align-items:flex-start;">
                                    @if($a->foto)
                                        <img src="{{ asset('storage/'.$a->foto) }}" alt="evidencia" onclick="abrirEvidencia('{{ asset('storage/'.$a->foto) }}')" style="flex:0 0 auto; width:52px; height:52px; object-fit:cover; border-radius:8px; border:1px solid var(--border); cursor:zoom-in;">
                                    @endif
                                    <div style="flex:1; min-width:0;">
                                        <strong>{{ $a->porcentaje }}% · {{ \Carbon\Carbon::parse($a->created_at)->format('d/m/Y') }}</strong> — {{ $a->comentario }}
                                        <span style="color:var(--muted); font-size:.78rem;">({{ $a->autor }})</span>
                                    </div>
                                    <form method="POST" action="{{ route('admin.proyectos.avance.eliminar', [$proyecto->id, $a->id]) }}" data-confirm="¿Eliminar este avance?" style="flex:0 0 auto;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="pa-btn danger" style="padding:7px 10px;" title="Eliminar avance"><i class="trash icon"></i></button>
                                    </form>
                                </div>
                                <div style="margin-top:6px;">
                                    <details>
                                        <summary style="cursor:pointer; color:#0d9488; font-size:.82rem; font-weight:700;">Editar</summary>
                                        <form method="POST" action="{{ route('admin.proyectos.avance.editar', [$proyecto->id, $a->id]) }}" enctype="multipart/form-data" style="margin-top:8px;">
                                            @csrf
                                            <label class="pa-label">Porcentaje</label>
                                            <input type="number" name="porcentaje" min="0" max="100" class="pa-input" value="{{ $a->porcentaje }}">
                                            <label class="pa-label">Comentario</label>
                                            <textarea name="comentario" class="pa-textarea" rows="2" required>{{ $a->comentario }}</textarea>
                                            <label class="pa-label">Reemplazar evidencia (foto)</label>
                                            <label class="pa-upload" for="foto-edit-{{ $a->id }}">
                                                <i class="camera icon"></i>
                                                <span id="foto-edit-text-{{ $a->id }}">Toca para subir una foto</span>
                                                <input type="file" id="foto-edit-{{ $a->id }}" name="foto" accept="image/*" hidden
                                                    onchange="document.getElementById('foto-edit-text-{{ $a->id }}').innerHTML = this.files[0] ? '<span class=\'fname\'>'+this.files[0].name+'</span>' : 'Toca para subir una foto';">
                                            </label>
                                            @if($a->foto)
                                                <label style="display:flex; align-items:center; gap:6px; font-size:.82rem; color:var(--muted); margin-top:6px;">
                                                    <input type="checkbox" name="quitar_foto" value="1" style="width:auto;"> Quitar la foto actual
                                                </label>
                                            @endif
                                            <button type="submit" class="pa-btn" style="margin-top:10px;"><i class="save icon"></i> Guardar avance</button>
                                        </form>
                                    </details>
                                </div>
                            </div>
                        @empty
                            <p style="color:#94a3b8; font-size:.88rem;">Sin avances todavía.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="pa-lightbox" id="pa-lightbox">
        <span class="close">&times;</span>
        <img id="pa-lightbox-img" src="" alt="Evidencia">
    </div>

    <script>
        window.abrirEvidencia = function (src) {
            var lb = document.getElementById('pa-lightbox');
            document.getElementById('pa-lightbox-img').src = src;
            lb.style.display = 'flex';
        };
        (function () {
            var lb = document.getElementById('pa-lightbox');
            if (lb) {
                lb.addEventListener('click', function () {
                    lb.style.display = 'none';
                    document.getElementById('pa-lightbox-img').src = '';
                });
                document.addEventListener('keydown', function (e) {
                    if (e.key === 'Escape') { lb.style.display = 'none'; }
                });
            }
        })();
        (function () {
            document.querySelectorAll('.pa-page form').forEach(function (f) {
                f.addEventListener('submit', function (e) {
                    // Confirmación con la alerta del sistema (alertify)
                    if (f.dataset.confirm && !f.dataset.confirmed) {
                        e.preventDefault();
                        alertify.confirm('Confirmar', f.dataset.confirm, function () {
                            f.dataset.confirmed = '1';
                            if (typeof f.requestSubmit === 'function') { f.requestSubmit(); } else { f.submit(); }
                        }, function () {}).set('labels', { ok: 'Sí, continuar', cancel: 'Cancelar' });
                        return;
                    }
                    // Deshabilitar botón + loader para evitar doble clic
                    const btn = f.querySelector('button[type="submit"], button:not([type])');
                    if (btn && !btn.disabled) {
                        btn.dataset.orig = btn.innerHTML;
                        btn.disabled = true;
                        btn.innerHTML = '<i class="notched circle loading icon"></i> Procesando...';
                    }
                    if (window.mostrarLoaderPantalla) { window.mostrarLoaderPantalla(); }
                });
            });
        })();
    </script>
</x-app-layout>
