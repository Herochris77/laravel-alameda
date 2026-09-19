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
            'puntos' => 'required|array|min:1',
            'puntos.*.titulo' => 'required|string|max:255',
            'puntos.*.tipo' => 'required|in:si_no,opciones,ordenamiento',
            'puntos.*.opciones' => 'nullable|array',
        ]);

        try {
            $asamblea = Asamblea::create([
                'titulo' => $request->titulo,
                'descripcion' => $request->descripcion,
                'fecha' => $request->fecha,
                'hora' => $request->hora,
                'lugar' => $request->lugar,
                'quorum_pct' => $request->quorum_pct ?? 50,
                'control_asistencia' => $request->boolean('control_asistencia'),
                'estado' => 'convocada',
                'created_by' => Auth::user()->id,
            ]);

            foreach ($request->puntos as $i => $punto) {
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

            return response()->json(['success' => false, 'message' => 'No se pudo crear la asamblea.'. $e], 500);
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
        $casas = $this->svc->casas();
        $presentes = $asamblea->asistencias()->where('confirmada', true)->pluck('casa')->all();

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
            'quorum' => $this->svc->quorum($asamblea),
            // Pase de lista AGRUPADO por quién ejerce el voto. Quien delegó su
            // voto no aparece como fila propia: se integra en la fila de quien
            // votará por él (con el poder visible en pantalla).
            'pase_lista' => $this->paseListaAgrupado($asamblea, $casas, $presentes),
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
                'resultados' => $this->svc->resultadosPunto($p),
            ]),
        ];
    }

    /**
     * Agrupa las casas por quién las vota. Devuelve una fila por votante con
     * todas las casas que ejerce (propia + representadas).
     */
    private function paseListaAgrupado(Asamblea $asamblea, array $casas, array $presentes)
    {
        $grupos = [];
        foreach ($casas as $c) {
            $tid = $this->svc->titularCasaId($asamblea, $c);
            if (! $tid) {
                continue;
            }
            $grupos[$tid][] = $c;
        }

        $filas = [];
        foreach ($grupos as $tid => $casasGrupo) {
            $u = User::find($tid);
            natsort($casasGrupo);
            $casasGrupo = array_values($casasGrupo);
            $baseCasa = $u?->casa;
            $representadas = array_values(array_diff($casasGrupo, [$baseCasa]));
            $todasPresentes = count(array_diff($casasGrupo, $presentes)) === 0;

            $filas[] = [
                'voter_id' => (int) $tid,
                'nombre' => $u?->nombre ?? 'Vecino',
                'es_inquilino' => $u?->tipo === 'inquilino',
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
        $request->validate(['casa' => 'required|string', 'presente' => 'required|boolean']);
        $asamblea = Asamblea::findOrFail($id);

        $this->svc->marcarPresente($asamblea, $request->casa, Auth::user(), $request->boolean('presente'));

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
            'resultados' => $this->svc->resultadosPunto($p),
        ]);

        $quorum = $this->svc->quorum($asamblea);

        $dompdf = \App\Services\PdfService::crear('A4', 'portrait');
        $dompdf->loadHtml(view('asamblea.acta', compact('asamblea', 'puntos', 'quorum'))->render());
        $dompdf->render();

        return response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="acta-asamblea-'.$asamblea->id.'.pdf"',
        ]);
    }
}
