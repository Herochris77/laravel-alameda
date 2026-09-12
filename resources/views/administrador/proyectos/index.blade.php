<x-app-layout>
    @php
        $badge = ['por_iniciar' => ['#eff6ff', '#1d4ed8'], 'en_proceso' => ['#fffbeb', '#92400e'], 'pausado' => ['#f1f5f9', '#475569'], 'terminado' => ['#f0fdf4', '#166534']];
    @endphp
    <style>
        .pa-page { --primary:#0d9488; --text:#0f172a; --muted:#64748b; --border:#e2e8f0; --surface:#fff; --bg:#f8fafc; padding-bottom:24px; }
        .pa-page, .pa-page * { box-sizing:border-box; }
        .pa-hero { display:flex; align-items:center; gap:12px; margin-bottom:18px; }
        .pa-hero i { color:#0d9488; }
        .pa-container { max-width:1200px; margin:0 auto; }
        .pa-card { background:var(--surface); border:1px solid var(--border); border-radius:16px; padding:18px 20px; margin-bottom:16px; box-shadow:0 6px 18px rgba(15,23,42,.05); }
        .pa-card h3 { margin:0 0 12px; font-size:1rem; }
        .pa-label { display:block; font-size:13px; color:var(--muted); margin:8px 0 5px; font-weight:600; }
        .pa-input, .pa-select, .pa-textarea { width:100%; padding:9px 12px; border:1px solid #cbd5e1; border-radius:10px; font-size:14px; }
        .pa-btn { border:none; border-radius:10px; padding:10px 16px; font-weight:700; cursor:pointer; font-size:14px; background:#0d9488; color:#fff; }
        .pa-item { display:flex; justify-content:space-between; align-items:center; gap:12px; border:1px solid var(--border); border-radius:12px; padding:12px 14px; margin-bottom:10px; }
        .pa-name { font-weight:700; color:var(--text); }
        .pa-badge { font-size:.72rem; font-weight:700; padding:3px 10px; border-radius:20px; }
        .pa-bar { height:8px; width:160px; background:var(--bg); border-radius:99px; overflow:hidden; margin-top:6px; }
        .pa-fill { height:100%; background:#0d9488; }
        .pa-link { color:#0d9488; text-decoration:none; font-weight:700; font-size:.85rem; }
        .flash { background:#f0fdf4; color:#166534; border:1px solid #a7f3d0; border-radius:10px; padding:10px 14px; margin-bottom:14px; font-size:.9rem; }
    </style>

    <div class="pa-page">
        <div class="pa-container">
            <div class="pa-hero"><i class="tasks icon big"></i><h2 style="margin:0;">Proyectos</h2></div>
            @if(session('ok'))<div class="flash"><i class="check circle icon"></i> {{ session('ok') }}</div>@endif

            <div class="pa-card">
                <h3><i class="plus circle icon"></i> Nuevo proyecto</h3>
                <form method="POST" action="{{ route('admin.proyectos.guardar') }}">
                    @csrf
                    <label class="pa-label">Nombre *</label>
                    <input type="text" name="nombre" class="pa-input" required placeholder="Ej. Impermeabilización de azoteas">
                    <label class="pa-label">Descripción</label>
                    <textarea name="descripcion" class="pa-textarea" rows="2"></textarea>
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
                        <div><label class="pa-label">Costo</label><input type="number" step="0.01" min="0" name="costo" class="pa-input" value="0"></div>
                        <div><label class="pa-label">Estado</label>
                            <select name="estado" class="pa-select">
                                @foreach($estados as $k => $v)<option value="{{ $k }}">{{ $v }}</option>@endforeach
                            </select>
                        </div>
                    </div>
                    <button type="submit" class="pa-btn" style="margin-top:14px;"><i class="save icon"></i> Crear proyecto</button>
                </form>
            </div>

            <div class="pa-card">
                <h3><i class="list icon"></i> Proyectos ({{ $proyectos->count() }})</h3>
                @forelse($proyectos as $p)
                    @php $col = $badge[$p->estado] ?? ['#f1f5f9','#475569']; @endphp
                    <div class="pa-item">
                        <div>
                            <div class="pa-name">{{ $p->nombre }} <span class="pa-badge" style="background:{{ $col[0] }}; color:{{ $col[1] }};">{{ $estados[$p->estado] ?? $p->estado }}</span></div>
                            <div style="font-size:12px; color:var(--muted); margin-top:2px;">${{ number_format($p->costo, 0) }} · {{ $p->avance }}% · {{ $p->avances_count }} avance(s)</div>
                            <div class="pa-bar"><div class="pa-fill" style="width:{{ $p->avance }}%;"></div></div>
                        </div>
                        <a href="{{ route('admin.proyectos.ver', $p->id) }}" class="pa-link">Gestionar <i class="arrow right icon"></i></a>
                    </div>
                @empty
                    <p style="color:#94a3b8;">Aún no hay proyectos.</p>
                @endforelse
            </div>
        </div>
    </div>

    <script>
        (function () {
            document.querySelectorAll('.pa-page form').forEach(function (f) {
                f.addEventListener('submit', function () {
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
