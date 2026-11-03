<x-app-layout>
    <style>
        .ex-page { --primary:var(--brand-600); --success:var(--brand-500); --warning:var(--oro-700); --text:var(--ink); --muted:var(--ink-3); --soft:var(--ink-4); --border:var(--arena); --surface:#fff; --bg:var(--crema-50); padding-bottom:24px; }
        .ex-hero { background: radial-gradient(circle at top right, rgba(255,255,255,.22), transparent 34%), linear-gradient(135deg,var(--brand-600),var(--brand-500)); border-radius:24px; padding:22px 24px; color:#fff; margin-bottom:20px; box-shadow:0 12px 30px rgba(11,107,69,.18); }
        .ex-hero-row { display:flex; align-items:center; justify-content:space-between; gap:14px; flex-wrap:wrap; }
        .ex-hero-left { display:flex; align-items:center; gap:14px; }
        .ex-hero-num { width:56px; height:56px; border-radius:16px; background:rgba(255,255,255,.16); display:flex; align-items:center; justify-content:center; font-size:1.4rem; font-weight:900; }
        .ex-title { margin:0; font-size:1.6rem; font-weight:900; letter-spacing:-.02em; }
        .ex-sub { margin:2px 0 0; opacity:.92; font-size:.9rem; }
        .ex-badges { display:flex; gap:8px; flex-wrap:wrap; }
        .ex-badge { font-size:.78rem; font-weight:700; padding:5px 12px; border-radius:20px; background:rgba(255,255,255,.18); }
        .ex-back { color:#fff; opacity:.9; text-decoration:none; font-size:.85rem; display:inline-flex; align-items:center; gap:4px; margin-bottom:12px; }
        .ex-container { max-width:1100px; margin:0 auto; }
        .ex-cards { display:grid; grid-template-columns:repeat(auto-fit,minmax(300px,1fr)); gap:16px; }
        .ex-card { background:var(--surface); border:1px solid var(--border); border-radius:18px; padding:18px 20px; box-shadow:0 6px 18px rgba(20,32,26,.05); }
        .ex-card.full { grid-column:1/-1; }
        .ex-card h3 { margin:0 0 12px; font-size:1rem; color:var(--text); display:flex; align-items:center; gap:8px; }
        .ex-card h3 i { color:var(--primary); }
        .row { display:flex; justify-content:space-between; align-items:center; gap:10px; padding:9px 0; border-bottom:1px solid var(--bg); font-size:.9rem; }
        .row:last-child { border-bottom:none; }
        .muted { color:var(--muted); font-size:.8rem; }
        .pill { font-size:.72rem; font-weight:700; padding:2px 9px; border-radius:20px; }
        .p-ok { background:var(--brand-50); color:var(--brand-800); }
        .p-pend { background:var(--danger-bg); color:var(--danger); }
        .p-grey { background:var(--crema-100); color:var(--ink-2); }
        .contact-btn { display:inline-flex; align-items:center; gap:5px; font-size:.78rem; font-weight:700; text-decoration:none; padding:5px 10px; border-radius:8px; }
        .c-call { background:var(--info-bg); color:var(--info); }
        .c-wa { background:var(--brand-50); color:var(--brand-800); }
        .tl { display:flex; gap:12px; }
        .tl-dot { display:flex; flex-direction:column; align-items:center; }
        .tl-dot span.d { width:10px; height:10px; border-radius:50%; margin-top:5px; }
        .tl-dot span.l { flex:1; width:1px; background:var(--border); }
        .tl-body { padding-bottom:14px; }
        .nota-item { background:var(--bg); border-radius:10px; padding:10px 12px; margin-bottom:8px; font-size:.86rem; }
        .nota-form textarea { width:100%; border:1px solid var(--border); border-radius:10px; padding:10px; font-size:.9rem; resize:vertical; }
        .nota-form button { margin-top:8px; border:none; background:var(--primary); color:#fff; font-weight:700; padding:9px 16px; border-radius:10px; cursor:pointer; }
        .flash { background:var(--brand-50); color:var(--brand-800); border:1px solid var(--brand-100); border-radius:10px; padding:10px 14px; margin-bottom:14px; font-size:.9rem; }
    </style>

    <div class="ex-page">
        <div class="ex-container">
            <a href="{{ route('admin.expediente.index') }}" class="ex-back"><i class="arrow left icon"></i> Volver a expedientes</a>
        </div>

        <div class="ex-hero">
            <div class="ex-hero-row">
                <div class="ex-hero-left">
                    <div class="ex-hero-num">{{ $casa }}</div>
                    <div>
                        <h1 class="ex-title">Casa {{ $casa }}</h1>
                        <p class="ex-sub">{{ $personas->count() }} persona(s) · dueño: {{ $dueno->nombre ?? '—' }}</p>
                    </div>
                </div>
                <div class="ex-badges">
                    @if($adeudo > 0)<span class="ex-badge">Adeudo ${{ number_format($adeudo, 2) }}</span>@endif
                    @php $sancActivas = $sanciones->where('estado','pendiente')->count(); @endphp
                    @if($sancActivas > 0)<span class="ex-badge">{{ $sancActivas }} sanción(es) activa(s)</span>@endif
                    @if($adeudo == 0 && $sancActivas == 0)<span class="ex-badge">Al corriente</span>@endif
                </div>
            </div>
        </div>

        <div class="ex-container">
            @if(session('ok'))<div class="flash"><i class="check circle icon"></i> {{ session('ok') }}</div>@endif

            <div class="ex-cards">
                <div class="ex-card">
                    <h3><i class="users icon"></i> Personas</h3>
                    @foreach($personas as $p)
                        <div class="row">
                            <div>
                                <div>{{ $p->nombre }} @if($p->pago == 1)<span class="pill p-ok">trae el pago</span>@endif</div>
                                <div class="muted">{{ ucfirst($p->tipo) }} · {{ $p->celular ?: 'sin teléfono' }}</div>
                            </div>
                            @if($p->celular)
                                @php $tel = preg_replace('/\D/', '', (string) $p->celular); @endphp
                                <div style="display:flex; gap:6px;">
                                    <a class="contact-btn c-call" href="tel:{{ $tel }}"><i class="phone icon"></i></a>
                                    <a class="contact-btn c-wa" href="https://wa.me/52{{ $tel }}" target="_blank"><i class="whatsapp icon"></i></a>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>

                <div class="ex-card">
                    <h3><i class="dollar sign icon"></i> Finanzas</h3>
                    @forelse($recibos as $r)
                        <div class="row">
                            <div><div>{{ $r['concepto'] }}</div><div class="muted">${{ number_format($r['monto'], 2) }} · {{ $r['fecha'] }}</div></div>
                            @if($r['estado'] === 'pagado')<span class="pill p-ok">Pagado</span>
                            @elseif($r['estado'] === 'pendiente')<span class="pill p-pend">Pendiente</span>
                            @else<span class="pill p-grey">{{ ucfirst($r['estado']) }}</span>@endif
                        </div>
                    @empty
                        <p class="muted">Sin recibos registrados.</p>
                    @endforelse
                </div>

                {{-- Los vehículos ya no se listan: el módulo se retiró. --}}
                <div class="ex-card">
                    <h3><i class="paw icon"></i> Mascotas</h3>
                    @forelse($mascotas as $m)
                        <div class="row"><div><i class="paw icon" style="color:var(--ink-3);"></i> {{ $m->nombre }} · {{ $m->tipo }}</div></div>
                    @empty
                        <p class="muted">Sin mascotas registradas.</p>
                    @endforelse
                </div>

                <div class="ex-card">
                    <h3><i class="warning circle icon"></i> Sanciones y estacionamiento</h3>
                    @forelse($sanciones as $s)
                        <div class="row"><div><div>{{ $s->motivo }}</div><div class="muted">${{ number_format($s->monto, 2) }} · {{ \Carbon\Carbon::parse($s->created_at)->format('d/m/Y') }}</div></div>
                        @if($s->estado === 'pendiente')<span class="pill p-pend">Activa</span>@else<span class="pill p-ok">Pagada</span>@endif</div>
                    @empty
                        <p class="muted">Sin sanciones.</p>
                    @endforelse
                    @foreach($cajones as $cj)
                        <div class="row"><div><i class="parking icon" style="color:var(--ink-3);"></i> Ocupa {{ $cj->nombre }}</div><span class="pill p-grey">ahora</span></div>
                    @endforeach
                </div>

                <div class="ex-card">
                    <h3><i class="history icon"></i> Actividad reciente</h3>
                    @forelse($actividad as $a)
                        <div class="tl">
                            <div class="tl-dot">
                                <span class="d" style="background:{{ $a['tipo']==='pago' ? '#17895c' : ($a['tipo']==='adeudo' ? '#c0432c' : ($a['tipo']==='sancion' ? '#8a6d1a' : '#6f6a58')) }};"></span>
                                @if(!$loop->last)<span class="l"></span>@endif
                            </div>
                            <div class="tl-body"><div style="font-size:.88rem;">{{ $a['texto'] }}</div><div class="muted">{{ $a['fecha']->format('d/m/Y') }}</div></div>
                        </div>
                    @empty
                        <p class="muted">Sin actividad reciente.</p>
                    @endforelse
                </div>

                <div class="ex-card full">
                    <h3><i class="sticky note icon"></i> Notas del comité</h3>
                    @forelse($notas as $n)
                        <div class="nota-item">{{ $n->nota }}<div class="muted" style="margin-top:4px;">{{ \Carbon\Carbon::parse($n->created_at)->format('d/m/Y') }} · {{ $n->autor }}</div></div>
                    @empty
                        <p class="muted">Sin notas todavía.</p>
                    @endforelse

                    <form class="nota-form" method="POST" action="{{ route('admin.expediente.nota', $casa) }}" style="margin-top:10px;">
                        @csrf
                        <textarea name="nota" rows="2" placeholder="Agregar una nota (acuerdos, observaciones, plan de pago...)" required></textarea>
                        <button type="submit"><i class="plus icon"></i> Agregar nota</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
