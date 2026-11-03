<x-app-layout>
    <style>
        .ex-page { --primary:var(--brand-600); --text:var(--ink); --muted:var(--ink-3); --soft:var(--ink-4); --border:var(--arena); --surface:#fff; --bg:var(--crema-50); padding-bottom:24px; }
        .ex-hero { background: radial-gradient(circle at top right, rgba(255,255,255,.22), transparent 34%), linear-gradient(135deg,var(--brand-600),var(--brand-500)); border-radius:24px; padding:24px; color:#fff; margin-bottom:20px; box-shadow:0 12px 30px rgba(11,107,69,.18); }
        .ex-hero-top { display:flex; align-items:center; gap:14px; }
        .ex-hero-icon { width:58px; height:58px; border-radius:18px; background:rgba(255,255,255,.16); display:flex; align-items:center; justify-content:center; }
        .ex-hero-icon i { font-size:1.6rem; margin:0 !important; }
        .ex-title { margin:0; font-size:clamp(1.4rem,3vw,2rem); font-weight:900; letter-spacing:-.03em; }
        .ex-subtitle { margin:2px 0 0; opacity:.92; font-size:.95rem; }
        .ex-container { max-width:1200px; margin:0 auto; }
        .ex-search { margin-bottom:16px; }
        .ex-search input { width:100%; max-width:420px; border:1px solid var(--border); border-radius:12px; padding:10px 14px; font-size:.95rem; }
        .ex-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(220px,1fr)); gap:12px; }
        .ex-casa { display:block; text-decoration:none; background:var(--surface); border:1px solid var(--border); border-radius:16px; padding:16px; box-shadow:0 6px 18px rgba(20,32,26,.05); transition:.15s; }
        .ex-casa:hover {  box-shadow:0 10px 24px rgba(20,32,26,.09); border-color:var(--brand-100); }
        .ex-casa-top { display:flex; justify-content:space-between; align-items:center; }
        .ex-casa-num { font-size:1.05rem; font-weight:800; color:var(--text); }
        .ex-badge { font-size:.72rem; font-weight:700; padding:3px 9px; border-radius:20px; }
        .b-ok { background:var(--brand-50); color:var(--brand-800); }
        .b-adeudo { background:var(--danger-bg); color:var(--danger); }
        .b-sancion { background:var(--warn-bg); color:var(--warn); }
        .ex-casa-info { font-size:.82rem; color:var(--muted); margin-top:8px; }
        .ex-casa-link { font-size:.82rem; color:var(--primary); margin-top:10px; font-weight:700; }
    </style>

    <div class="ex-page">
        <div class="ex-hero">
            <div class="ex-hero-top">
                <div class="ex-hero-icon"><i class="folder open icon"></i></div>
                <div>
                    <h1 class="ex-title">Expediente por casa</h1>
                    <p class="ex-subtitle">Toda la información de cada casa en un solo lugar</p>
                </div>
            </div>
        </div>

        <div class="ex-container">
            <div class="ex-search">
                <input type="text" id="ex-buscar" placeholder="Buscar por número de casa o dueño...">
            </div>

            <div class="ex-grid" id="ex-grid">
                @forelse($casas as $c)
                    <a href="{{ route('admin.expediente.ficha', $c['casa']) }}" class="ex-casa" data-buscar="{{ strtolower($c['casa'].' '.$c['dueno']) }}">
                        <div class="ex-casa-top">
                            <span class="ex-casa-num">Casa {{ $c['casa'] }}</span>
                            @if($c['estado'] === 'adeudo')
                                <span class="ex-badge b-adeudo">con adeudo</span>
                            @elseif($c['estado'] === 'sancion')
                                <span class="ex-badge b-sancion">sanción activa</span>
                            @else
                                <span class="ex-badge b-ok">al corriente</span>
                            @endif
                        </div>
                        <div class="ex-casa-info">{{ $c['dueno'] }} · {{ $c['personas'] }} persona(s)</div>
                        <div class="ex-casa-link">Ver expediente <i class="arrow right icon"></i></div>
                    </a>
                @empty
                    <p style="color:var(--ink-4);">Aún no hay casas registradas.</p>
                @endforelse
            </div>
        </div>
    </div>

    <script>
        $('#ex-buscar').on('keyup input', function () {
            const q = $(this).val().toLowerCase().trim();
            $('#ex-grid .ex-casa').each(function () {
                $(this).toggle($(this).data('buscar').toString().includes(q));
            });
        });
    </script>
</x-app-layout>
