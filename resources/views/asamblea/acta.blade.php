<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #1e293b; font-size: 12px; }
        h1 { font-size: 18px; margin: 0 0 4px; color: #0f766e; }
        h2 { font-size: 14px; margin: 18px 0 6px; border-bottom: 1px solid #cbd5e1; padding-bottom: 4px; }
        .muted { color: #64748b; }
        table { width: 100%; border-collapse: collapse; margin-top: 6px; }
        th, td { border: 1px solid #cbd5e1; padding: 6px 8px; text-align: left; }
        th { background: #f1f5f9; }
        .badge { padding: 2px 8px; border-radius: 4px; font-size: 11px; }
        .aprobado { background: #dcfce7; color: #166534; }
        .rechazado { background: #fee2e2; color: #991b1b; }
    </style>
</head>
<body>
    <h1>Acta de asamblea</h1>
    <div class="muted">{{ $asamblea->titulo }}</div>
    <div class="muted">
        Fecha: {{ $asamblea->fecha ? $asamblea->fecha->format('d/m/Y') : '—' }}
        @if($asamblea->hora) · {{ $asamblea->hora }} @endif
        @if($asamblea->lugar) · {{ $asamblea->lugar }} @endif
    </div>

    <h2>Quórum</h2>
    <p>
        Casas presentes o representadas: <strong>{{ $quorum['presentes'] }} de {{ $quorum['total'] }}</strong>
        ({{ $quorum['pct'] }}%) — quórum requerido {{ $asamblea->quorum_pct }}%.
        <span class="badge {{ $quorum['alcanzado'] ? 'aprobado' : 'rechazado' }}">
            {{ $quorum['alcanzado'] ? 'Quórum alcanzado' : 'Sin quórum' }}
        </span>
    </p>

    <h2>Orden del día y resultados</h2>
    @foreach($puntos as $i => $p)
        <p><strong>{{ $i + 1 }}. {{ $p['titulo'] }}</strong></p>
        @php $r = $p['resultados']; @endphp

        @if($p['tipo'] === 'si_no')
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
    @endforeach

    <h2>Minuta / Acuerdos</h2>
    @if($asamblea->minuta)
        <p style="white-space: pre-wrap;">{{ $asamblea->minuta }}</p>
    @else
        <p class="muted">No se registró minuta para esta asamblea.</p>
    @endif

    <p class="muted" style="margin-top:24px;">
        Generada el {{ now()->format('d/m/Y H:i') }} · Sistema Alameda.
    </p>
</body>
</html>
