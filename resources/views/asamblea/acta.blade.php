<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #1d2a23; font-size: 12px; }
        h1 { font-size: 18px; margin: 0 0 4px; color: #095a3a; }
        h2 { font-size: 14px; margin: 18px 0 6px; border-bottom: 1px solid #d6cba8; padding-bottom: 4px; }
        .muted { color: #5d695f; }
        table { width: 100%; border-collapse: collapse; margin-top: 6px; }
        th, td { border: 1px solid #d6cba8; padding: 6px 8px; text-align: left; }
        th { background: #f3eee0; }
        .badge { padding: 2px 8px; border-radius: 4px; font-size: 11px; }
        .aprobado { background: #e1eee5; color: #0a4a31; }
        .rechazado { background: #f8e3dc; color: #a9402a; }

        .membrete { text-align: center; margin-bottom: 14px; }
        .membrete .condominio { font-size: 13px; font-weight: bold; letter-spacing: .04em; color: #1d2a23; }
        .membrete .direccion { font-size: 8.5px; color: #5d695f; line-height: 1.4; }
    </style>
</head>
<body>
    <div class="membrete">
        @include('pdf.logo', ['alto' => 44])
        <div class="condominio">CONDOMINIO ALAMEDA</div>
        <div class="direccion">
            Av. Los Arados No. 1, Fracc. Hacienda del Bosque<br>
            Los Ángeles, Qro. C.P. 76902
        </div>
    </div>

    <h1>Acta de asamblea</h1>
    <div class="muted">{{ $asamblea->titulo }}</div>
    <div class="muted">
        Fecha: {{ $asamblea->fecha ? $asamblea->fecha->format('d/m/Y') : '—' }}
        @if($asamblea->hora) · {{ $asamblea->hora }} @endif
        @if($asamblea->lugar) · {{ $asamblea->lugar }} @endif
    </div>
    <div class="muted">Modalidad: {{ $modalidades[$modalidad] ?? $modalidad }}</div>

    <h2>Quórum</h2>
    <p>
        Casas presentes o representadas: <strong>{{ $quorum['presentes'] }} de {{ $quorum['total'] }}</strong>
        ({{ $quorum['pct'] }}%) — quórum requerido {{ $asamblea->quorum_pct }}%.
        <span class="badge {{ $quorum['alcanzado'] ? 'aprobado' : 'rechazado' }}">
            {{ $quorum['alcanzado'] ? 'Quórum alcanzado' : 'Sin quórum' }}
        </span>
    </p>

    @php
        $presentes = collect($casas)->where('presente', true);
        $enSala = $presentes->where('modo', 'mesa')->count();
        $sinAcceso = collect($casas)->where('acceso', false)->count();
    @endphp
    <p>
        <strong>Casas presentes o representadas:</strong>
        {{ $presentes->pluck('casa')->implode(', ') ?: 'ninguna registrada' }}.
        @if($enSala > 0)
            <br><span class="muted">{{ $enSala }} casa(s) con asistencia registrada por la mesa directiva en la sala.</span>
        @endif
        @if($sinAcceso > 0)
            <br><span class="muted">{{ $sinAcceso }} casa(s) no tienen acceso a la plataforma; su asistencia y votos, cuando los hubo, los registró la mesa directiva.</span>
        @endif
    </p>

    <h2>Orden del día y resultados</h2>
    @if(count($puntos) === 0)
        <p class="muted">Esta asamblea no llevó puntos a votación.</p>
    @endif    @foreach($puntos as $i => $p)
        <p><strong>{{ $i + 1 }}. {{ $p['titulo'] }}</strong></p>
        @php $r = $p['resultados']; @endphp

        @if($p['tipo'] === 'informativo')
            <p style="white-space: pre-wrap;">{{ $p['resolucion'] ?: 'Punto informativo, sin votación.' }}</p>
        @elseif($p['tipo'] === 'si_no')
            <table>
                <tr><th>A favor</th><th>En contra</th><th>Abstención</th><th>Resultado</th></tr>
                <tr>
                    <td>{{ $r['conteo']['a_favor'] }}</td>
                    <td>{{ $r['conteo']['en_contra'] }}</td>
                    <td>{{ $r['conteo']['abstencion'] }}</td>
                    <td><span class="badge {{ $r['aprobado'] ? 'aprobado' : 'rechazado' }}">{{ $r['aprobado'] ? 'Aprobado' : 'No aprobado' }}</span></td>
                </tr>
            </table>
        @elseif($p['tipo'] === 'opciones')
            <table>
                <tr><th>Opción</th><th>Votos (casas)</th><th>%</th></tr>
                @foreach($r['resultados'] as $op)
                    <tr><td>{{ $op['opcion'] }}</td><td>{{ $op['votos'] }}</td><td>{{ $op['pct'] }}%</td></tr>
                @endforeach
            </table>
            <p class="muted">Ganador: <strong>{{ $r['ganador'] }}</strong></p>
        @elseif($p['tipo'] === 'ordenamiento')
            <table>
                <tr><th>#</th><th>Proyecto</th><th>Puntos</th></tr>
                @foreach($r['ranking'] as $k => $fila)
                    <tr><td>{{ $k + 1 }}</td><td>{{ $fila['opcion'] }}</td><td>{{ $fila['puntos'] }}</td></tr>
                @endforeach
            </table>
        @endif
        @if(isset($r['origen']) && ($r['origen']['mesa'] ?? 0) > 0)
            <p class="muted">Votos por la plataforma: {{ $r['origen']['plataforma'] }} · registrados por la mesa directiva en la sala: {{ $r['origen']['mesa'] }}.</p>
        @endif
        @if($p['tipo'] !== 'informativo' && !empty($p['resolucion']))
            <p style="white-space: pre-wrap;"><strong>Acuerdo:</strong> {{ $p['resolucion'] }}</p>
        @endif
    @endforeach

    <h2>Minuta / Acuerdos</h2>
    @if($asamblea->minuta)
        <p style="white-space: pre-wrap;">{{ $asamblea->minuta }}</p>
    @else
        <p class="muted">No se registró minuta para esta asamblea.</p>
    @endif

    @include('pdf.pie')
</body>
</html>
