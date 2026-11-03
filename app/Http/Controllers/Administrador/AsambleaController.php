<?php

namespace App\Http\Controllers\Administrador;

use App\Http\Controllers\Controller;
use App\Models\Asamblea;
use App\Models\AsambleaOpcion;
use App\Models\AsambleaPunto;
use App\Models\User;
use App\Notifications\NotificacionGenerica;
use App\Services\AsambleaService;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class AsambleaController extends Controller
{
    public function __construct(protected AsambleaService $svc) {}

    public function index()
    {
        return view('administrador.asamblea');
    }

    public function listar()
    {
        $asambleas = Asamblea::withCount('puntos')
            ->orderByDesc('created_at')
            ->get()
            ->map(fn ($a) => [
                'id' => $a->id,
                'titulo' => $a->titulo,
                'fecha' => $a->fecha?->format('d/m/Y'),
                'hora' => $a->hora,
                'estado' => $a->estado,
                'puntos' => $a->puntos_count,
                'control_asistencia' => (bool) $a->control_asistencia,
                'modalidad' => $this->svc->modalidad($a),
            ]);

        return response()->json(['success' => true, 'asambleas' => $asambleas]);
    }

    public function crear(Request $request)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'fecha' => 'nullable|date',
            'hora' => 'nullable',
            'lugar' => 'nullable|string|max:255',
            'quorum_pct' => 'nullable|integer|min:1|max:100',
            'control_asistencia' => 'nullable',
            'modalidad' => 'nullable|in:'.implode(',', array_keys(AsambleaService::MODALIDADES)),
            // El orden del día puede quedar vacío: hay asambleas que solo llevan
            // asistencia, quórum y minuta, o cuyos puntos se agregan en la sala.
            'puntos' => 'nullable|array',
            'puntos.*.titulo' => 'required|string|max:255',
            'puntos.*.tipo' => 'required|in:si_no,opciones,ordenamiento,informativo',
            'puntos.*.opciones' => 'nullable|array',
        ]);

        $modalidad = $request->input('modalidad', 'plataforma');

        try {
            $asamblea = Asamblea::create([
                'titulo' => $request->titulo,
                'descripcion' => $request->descripcion,
                'fecha' => $request->fecha,
                'hora' => $request->hora,
                'lugar' => $request->lugar,
                'quorum_pct' => $request->quorum_pct ?? 50,
                // En una asamblea presencial siempre hay pase de lista: es la única forma de saber quién está.
                'control_asistencia' => $modalidad === 'presencial' ? true : $request->boolean('control_asistencia'),
                'estado' => 'convocada',
                'created_by' => Auth::user()->id,
                'modalidad' => $modalidad,
            ]);

            foreach ((array) $request->puntos as $i => $punto) {
                $p = AsambleaPunto::create([
                    'asamblea_id' => $asamblea->id,
                    'orden' => $i + 1,
                    'titulo' => $punto['titulo'],
                    'descripcion' => $punto['descripcion'] ?? null,
                    'tipo' => $punto['tipo'],
                    'estado' => 'pendiente',
                ]);

                if (in_array($punto['tipo'], ['opciones', 'ordenamiento'])) {
                    foreach (($punto['opciones'] ?? []) as $j => $op) {
                        $op = trim((string) $op);
                        if ($op !== '') {
                            AsambleaOpcion::create(['punto_id' => $p->id, 'opcion' => $op, 'orden' => $j + 1]);
                        }
                    }
                }
            }

            $this->notificarConvocatoria($asamblea);

            return response()->json(['success' => true, 'message' => 'Asamblea convocada correctamente.']);
        } catch (\Throwable $e) {
            Log::error('Error al crear asamblea: '.$e->getMessage());

            return response()->json(['success' => false, 'message' => 'No se pudo crear la asamblea.'], 500);
        }
    }

    private function notificarConvocatoria(Asamblea $a): void
    {
        try {
            $fecha = $a->fecha ? $a->fecha->translatedFormat('j \\d\\e F') : 'próximamente';
            $usuarios = User::where('estado', 1)->get();
            if ($usuarios->isNotEmpty()) {
                Notification::send($usuarios, new NotificacionGenerica(
                    '🗳️ Nueva asamblea convocada',
                    "Se convocó la asamblea \"{$a->titulo}\" para el {$fecha}. Revisa el orden del día.",
                    'info',
                    'usuario/asamblea',
                    'usuario/asamblea',
                    '<i class="gavel icon"></i>'
                ));
            }
        } catch (\Throwable $e) {
            Log::error('Error notificando convocatoria: '.$e->getMessage());
        }
    }

    public function detalle($id)
    {
        $asamblea = Asamblea::with('puntos.opciones')->findOrFail($id);

        return response()->json([
            'success' => true,
            'asamblea' => $this->serializar($asamblea),
        ]);
    }

    /**
     * Payload liviano para el sondeo (auto-actualización) del tablero.
     */
    public function tablero($id)
    {
        $asamblea = Asamblea::with('puntos.opciones')->findOrFail($id);

        return response()->json([
            'success' => true,
            'estado' => $asamblea->estado,
            'quorum' => $this->svc->quorum($asamblea),
            'puntos' => $asamblea->puntos->map(fn ($p) => [
                'id' => $p->id,
                'estado' => $p->estado,
                'resultados' => $this->svc->resultadosPunto($p),
            ]),
        ]);
    }

    private function serializar(Asamblea $asamblea): array
    {
        $casasEstado = $this->svc->casasEstado($asamblea);

        return [
            'id' => $asamblea->id,
            'titulo' => $asamblea->titulo,
            'descripcion' => $asamblea->descripcion,
            'fecha' => $asamblea->fecha?->format('d/m/Y'),
            'hora' => $asamblea->hora,
            'lugar' => $asamblea->lugar,
            'minuta' => $asamblea->minuta,
            'estado' => $asamblea->estado,
            'quorum_pct' => $asamblea->quorum_pct,
            'control_asistencia' => (bool) $asamblea->control_asistencia,
            'modalidad' => $this->svc->modalidad($asamblea),
            'quorum' => $this->svc->quorum($asamblea),
            // Estado de cada casa: quién la vota, si puede entrar a la plataforma y si está presente.
            'casas' => $casasEstado,
            // Pase de lista AGRUPADO por quién ejerce el voto. Quien delegó su
            // voto no aparece como fila propia: se integra en la fila de quien
            // votará por él (con el poder visible en pantalla).
            'pase_lista' => $this->paseListaAgrupado($casasEstado),
            'poderes' => $asamblea->poderes()->where('estado', 'activo')->get()->map(fn ($p) => [
                'casa' => $p->casa,
                'tipo' => $p->tipo,
                'representante' => optional($p->representante)->nombre,
            ]),
            'puntos' => $asamblea->puntos->map(fn ($p) => [
                'id' => $p->id,
                'orden' => $p->orden,
                'titulo' => $p->titulo,
                'descripcion' => $p->descripcion,
                'tipo' => $p->tipo,
                'estado' => $p->estado,
                'opciones' => $p->opciones->map(fn ($o) => ['id' => $o->id, 'opcion' => $o->opcion]),
                'resolucion' => $p->resolucion ?? null,
                'votos_casas' => in_array($p->tipo, AsambleaService::TIPOS_VOTABLES, true) ? $this->svc->votosPorCasa($p) : (object) [],
                'resultados' => $this->svc->resultadosPunto($p),
            ]),
        ];
    }

    /**
     * Agrupa las casas por quién las vota. Devuelve una fila por votante con
     * todas las casas que ejerce (propia + representadas).
     *
     * Parte del estado de casas ya calculado (casasEstado), así no vuelve a
     * consultar la base casa por casa.
     *
     * @param  array<int, array<string, mixed>>  $casasEstado
     */
    private function paseListaAgrupado(array $casasEstado)
    {
        $grupos = [];
        foreach ($casasEstado as $c) {
            if (! $c['titular_id']) {
                continue;
            }
            $grupos[$c['titular_id']][] = $c;
        }

        $filas = [];
        foreach ($grupos as $tid => $filasGrupo) {
            $casasGrupo = array_column($filasGrupo, 'casa');
            natsort($casasGrupo);
            $casasGrupo = array_values($casasGrupo);
            $primera = $filasGrupo[0];
            $baseCasa = $primera['titular_casa'];
            $representadas = array_values(array_diff($casasGrupo, [$baseCasa]));
            $todasPresentes = ! in_array(false, array_column($filasGrupo, 'presente'), true);

            $filas[] = [
                'voter_id' => (int) $tid,
                'nombre' => $primera['nombre'] ?? 'Vecino',
                'es_inquilino' => $primera['titular_tipo'] === 'inquilino',
                'base_casa' => $baseCasa,
                'casas' => $casasGrupo,
                'representadas' => $representadas,
                'presente' => $todasPresentes,
                'ref' => $casasGrupo[0] ?? $baseCasa,   // casa de referencia para marcar el grupo
            ];
        }

        usort($filas, fn ($a, $b) => strnatcmp((string) ($a['base_casa'] ?? $a['ref']), (string) ($b['base_casa'] ?? $b['ref'])));

        return $filas;
    }
    public function marcarPresente(Request $request, $id)
    {
        $request->validate(['casa' => 'required|string', 'presente' => 'required|boolean', 'nota' => 'nullable|string|max:255']);
        $asamblea = Asamblea::findOrFail($id);

        $this->svc->marcarPresente($asamblea, $request->casa, Auth::user(), $request->boolean('presente'), $request->nota);

        return response()->json(['success' => true, 'quorum' => $this->svc->quorum($asamblea)]);
    }

    /**
     * Marca (o quita) la presencia de un votante y de TODAS las casas que ejerce.
     */
    public function marcarGrupo(Request $request, $id)
    {
        $request->validate(['casa' => 'required|string', 'presente' => 'required|boolean']);
        $asamblea = Asamblea::findOrFail($id);
        $presente = $request->boolean('presente');

        $titularId = $this->svc->titularCasaId($asamblea, $request->casa);
        $titular = $titularId ? User::find($titularId) : null;
        $casas = $titular ? $this->svc->casasDeUsuario($asamblea, $titular) : [$request->casa];

        foreach ($casas as $casa) {
            $this->svc->marcarPresente($asamblea, $casa, Auth::user(), $presente);
        }

        return response()->json(['success' => true, 'quorum' => $this->svc->quorum($asamblea), 'casas' => $casas]);
    }

    /**
     * Cambia cómo se lleva la asamblea (plataforma, presencial o mixta) mientras
     * no esté cerrada. Útil cuando en la sala se decide votar a mano.
     */
    public function configurar(Request $request, $id)
    {
        $request->validate([
            'modalidad' => 'required|in:'.implode(',', array_keys(AsambleaService::MODALIDADES)),
            'quorum_pct' => 'nullable|integer|min:1|max:100',
        ]);

        $asamblea = Asamblea::findOrFail($id);
        if ($asamblea->estado === 'cerrada') {
            return response()->json(['success' => false, 'message' => 'La asamblea ya está cerrada.'], 422);
        }

        $datos = ['modalidad' => $request->modalidad];
        if ($request->modalidad === 'presencial') {
            $datos['control_asistencia'] = true;
        }
        if ($request->filled('quorum_pct')) {
            $datos['quorum_pct'] = (int) $request->quorum_pct;
        }
        $asamblea->update($datos);

        return response()->json(['success' => true, 'message' => 'Modalidad actualizada.']);
    }

    /**
     * Agrega un punto al orden del día, también con la asamblea en curso: así se
     * puede someter a votación (o dejar constancia de) algo que surge en la sala.
     */
    public function agregarPunto(Request $request, $id)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'tipo' => 'required|in:si_no,opciones,ordenamiento,informativo',
            'opciones' => 'nullable|array',
        ]);

        $asamblea = Asamblea::findOrFail($id);
        if ($asamblea->estado === 'cerrada') {
            return response()->json(['success' => false, 'message' => 'La asamblea ya está cerrada.'], 422);
        }

        $opciones = array_values(array_filter(array_map(fn ($o) => trim((string) $o), (array) $request->opciones)));
        if (in_array($request->tipo, ['opciones', 'ordenamiento'], true) && count($opciones) < 2) {
            return response()->json(['success' => false, 'message' => 'Un punto de opciones necesita al menos 2 opciones.'], 422);
        }

        $punto = AsambleaPunto::create([
            'asamblea_id' => $asamblea->id,
            'orden' => (int) $asamblea->puntos()->max('orden') + 1,
            'titulo' => $request->titulo,
            'descripcion' => $request->descripcion,
            'tipo' => $request->tipo,
            // Si la asamblea ya está en curso, el punto nace abierto.
            'estado' => $asamblea->estado === 'en_curso' ? 'abierto' : 'pendiente',
        ]);

        if (in_array($request->tipo, ['opciones', 'ordenamiento'], true)) {
            foreach ($opciones as $j => $op) {
                AsambleaOpcion::create(['punto_id' => $punto->id, 'opcion' => $op, 'orden' => $j + 1]);
            }
        }

        return response()->json(['success' => true, 'message' => 'Punto agregado al orden del día.']);
    }

    /** Quita un punto, solo si todavía nadie votó en él (no se pierde ningún voto). */
    public function eliminarPunto($puntoId)
    {
        $punto = AsambleaPunto::findOrFail($puntoId);

        if ($punto->votos()->exists()) {
            return response()->json(['success' => false, 'message' => 'Este punto ya tiene votos; no se puede quitar.'], 422);
        }

        AsambleaOpcion::where('punto_id', $punto->id)->delete();
        $punto->delete();

        return response()->json(['success' => true, 'message' => 'Punto eliminado.']);
    }

    /** Acuerdo escrito de un punto informativo (o conclusión de cualquier punto). */
    public function guardarResolucion(Request $request, $puntoId)
    {
        $request->validate(['resolucion' => 'nullable|string|max:3000']);

        $punto = AsambleaPunto::findOrFail($puntoId);
        $punto->update(['resolucion' => trim((string) $request->resolucion) ?: null]);

        return response()->json(['success' => true, 'message' => 'Acuerdo guardado.']);
    }

    /**
     * La mesa registra los votos de las casas que votaron en la sala o que no
     * pueden entrar a la plataforma. Recibe varias casas a la vez.
     *
     * votos: [ ['casa' => '12', 'valor' => 'a_favor'] , ['casa' => '14', 'opcion_id' => 7], ... ]
     */
    public function registrarVotosMesa(Request $request, $id)
    {
        $request->validate([
            'punto_id' => 'required|integer',
            'votos' => 'required|array|min:1',
            'votos.*.casa' => 'required|string',
        ]);

        $asamblea = Asamblea::findOrFail($id);
        $punto = AsambleaPunto::where('asamblea_id', $asamblea->id)->findOrFail($request->punto_id);

        $guardados = 0;
        $errores = [];
        foreach ($request->votos as $v) {
            try {
                $this->svc->registrarVotoMesa($asamblea, $punto, (string) $v['casa'], [
                    'valor' => $v['valor'] ?? null,
                    'opcion_id' => $v['opcion_id'] ?? null,
                    'orden' => $v['orden'] ?? [],
                ], Auth::user());
                $guardados++;
            } catch (\RuntimeException $e) {
                $errores[] = "Casa {$v['casa']}: ".$e->getMessage();
            }
        }

        Log::info("Votos registrados por la mesa | asamblea {$asamblea->id} | punto {$punto->id} | por ".Auth::user()->id." | {$guardados} casa(s)");

        return response()->json([
            'success' => $guardados > 0 || $errores === [],
            'message' => $guardados.' voto(s) registrado(s).'.($errores ? ' '.implode(' ', $errores) : ''),
            'guardados' => $guardados,
            'errores' => $errores,
        ], $guardados === 0 && $errores !== [] ? 422 : 200);
    }

    /** Mismo voto para todas las casas presentes que aún no votaron en el punto. */
    public function unanimidad(Request $request, $id)
    {
        $request->validate(['punto_id' => 'required|integer', 'valor' => 'required|in:a_favor,en_contra,abstencion']);

        $asamblea = Asamblea::findOrFail($id);
        $punto = AsambleaPunto::where('asamblea_id', $asamblea->id)->findOrFail($request->punto_id);

        try {
            $n = $this->svc->registrarUnanimidad($asamblea, $punto, $request->valor, Auth::user());
            Log::info("Votación unánime registrada por la mesa | asamblea {$asamblea->id} | punto {$punto->id} | {$request->valor} | por ".Auth::user()->id." | {$n} casa(s)");

            return response()->json(['success' => true, 'message' => "Se registró el voto de {$n} casa(s) presentes."]);
        } catch (\RuntimeException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    /** Corrige un error de captura: quita el voto que la mesa registró para una casa. */
    public function quitarVotoMesa(Request $request, $id)
    {
        $request->validate(['punto_id' => 'required|integer', 'casa' => 'required|string']);

        $asamblea = Asamblea::findOrFail($id);
        $punto = AsambleaPunto::where('asamblea_id', $asamblea->id)->findOrFail($request->punto_id);

        try {
            $n = $this->svc->quitarVotoMesa($asamblea, $punto, (string) $request->casa);

            return response()->json(['success' => true, 'message' => $n ? 'Voto quitado.' : 'Esa casa no tenía un voto registrado por la mesa.']);
        } catch (\RuntimeException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    public function iniciar($id)
    {
        $asamblea = Asamblea::findOrFail($id);
        $asamblea->update(['estado' => 'en_curso']);
        AsambleaPunto::where('asamblea_id', $id)->where('estado', 'pendiente')->update(['estado' => 'abierto']);

        return response()->json(['success' => true, 'message' => 'Asamblea iniciada.']);
    }

    public function cerrar($id)
    {
        $asamblea = Asamblea::findOrFail($id);
        $asamblea->update(['estado' => 'cerrada']);
        AsambleaPunto::where('asamblea_id', $id)->update(['estado' => 'cerrado']);

        return response()->json(['success' => true, 'message' => 'Asamblea cerrada.']);
    }

    public function togglePunto(Request $request, $puntoId)
    {
        $punto = AsambleaPunto::findOrFail($puntoId);
        $punto->estado = $punto->estado === 'cerrado' ? 'abierto' : 'cerrado';
        $punto->save();

        return response()->json(['success' => true, 'estado' => $punto->estado]);
    }

    public function guardarMinuta(Request $request, $id)
    {
        $request->validate(['minuta' => 'nullable|string']);

        $asamblea = Asamblea::findOrFail($id);
        $asamblea->update(['minuta' => trim($request->minuta ?? '') ?: null]);

        return response()->json(['success' => true, 'message' => 'Minuta guardada.']);
    }

    public function eliminar($id)
    {
        $asamblea = Asamblea::findOrFail($id);
        // Borrado en cascada manual (sin FKs estrictas en cPanel).
        AsambleaPunto::where('asamblea_id', $id)->get()->each(function ($p) {
            AsambleaOpcion::where('punto_id', $p->id)->delete();
        });
        $asamblea->puntos()->delete();
        $asamblea->votos()->delete();
        $asamblea->poderes()->delete();
        $asamblea->asistencias()->delete();
        $asamblea->delete();

        return response()->json(['success' => true, 'message' => 'Asamblea eliminada.']);
    }

    public function acta($id)
    {
        $asamblea = Asamblea::with('puntos.opciones', 'creador')->findOrFail($id);

        $puntos = $asamblea->puntos->map(fn ($p) => [
            'titulo' => $p->titulo,
            'tipo' => $p->tipo,
            'resolucion' => $p->resolucion ?? null,
            'resultados' => $this->svc->resultadosPunto($p),
        ]);

        $quorum = $this->svc->quorum($asamblea);
        $modalidad = $this->svc->modalidad($asamblea);
        $modalidades = AsambleaService::MODALIDADES;
        $casas = $this->svc->casasEstado($asamblea);

        $dompdf = \App\Services\PdfService::crear('A4', 'portrait');
        $dompdf->loadHtml(view('asamblea.acta', compact('asamblea', 'puntos', 'quorum', 'modalidad', 'modalidades', 'casas'))->render());
        $dompdf->render();

        return response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="acta-asamblea-'.$asamblea->id.'.pdf"',
        ]);
    }
}
