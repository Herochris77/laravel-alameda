<?php

namespace App\Http\Controllers\Usuario;

use App\Http\Controllers\Controller;
use App\Models\Asamblea;
use App\Models\AsambleaPunto;
use App\Models\AsambleaVoto;
use App\Models\User;
use App\Services\AsambleaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AsambleaController extends Controller
{
    public function __construct(protected AsambleaService $svc) {}

    public function index()
    {
        $user = auth()->user();

        return view('usuario.asamblea.index', compact('user'));
    }

    public function listar()
    {
        $user = auth()->user();

        $asambleas = Asamblea::whereIn('estado', ['convocada', 'en_curso', 'cerrada'])
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($a) use ($user) {
                $misCasas = $this->svc->casasDeUsuario($a, $user);

                return [
                    'id' => $a->id,
                    'titulo' => $a->titulo,
                    'descripcion' => $a->descripcion,
                    'fecha' => $a->fecha?->format('d/m/Y'),
                    'fecha_iso' => $a->fecha?->format('Y-m-d'),
                    'hora' => $a->hora,
                    'lugar' => $a->lugar,
                    'estado' => $a->estado,
                    'mis_casas' => $misCasas,
                    'num_votos' => count($misCasas),
                ];
            });

        return response()->json(['success' => true, 'asambleas' => $asambleas]);
    }

    public function detalle($id)
    {
        $user = auth()->user();
        $asamblea = Asamblea::with('puntos.opciones')->findOrFail($id);

        $misCasas = $this->svc->casasDeUsuario($asamblea, $user);

        // Delegación de mi propia casa (si soy dueño).
        $delegacion = null;
        $puedeDelegar = false;
        $elegiblesInquilino = [];
        $elegiblesVecinos = [];

        if ($user->tipo === 'dueño' && $user->casa) {
            $puedeDelegar = $asamblea->estado !== 'cerrada';
            $poder = $this->svc->poderActivo($asamblea, $user->casa);
            if ($poder) {
                $delegacion = [
                    'tipo' => $poder->tipo,
                    'representante' => optional($poder->representante)->nombre,
                    'casa' => $poder->casa,
                ];
            }

            $elegiblesInquilino = User::where('casa', $user->casa)
                ->where('tipo', 'inquilino')->where('estado', 1)
                ->get(['id', 'nombre'])->all();

            $elegiblesVecinos = User::where('tipo', 'dueño')
                ->where('id', '!=', $user->id)
                ->where('estado', 1)
                ->orderBy('casa')
                ->get(['id', 'nombre', 'casa'])->all();
        }

        // Mis votos por punto y por casa.
        $misVotos = [];
        if (! empty($misCasas)) {
            $votos = AsambleaVoto::where('asamblea_id', $asamblea->id)
                ->whereIn('casa', $misCasas)
                ->get();
            foreach ($votos as $v) {
                $misVotos[$v->punto_id][$v->casa][] = [
                    'valor' => $v->valor,
                    'opcion_id' => $v->opcion_id,
                    'posicion' => $v->posicion,
                ];
            }
        }

        $presenteMiCasa = $user->casa ? $this->svc->casaPresente($asamblea, $user->casa) : false;

        return response()->json([
            'success' => true,
            'asamblea' => [
                'id' => $asamblea->id,
                'titulo' => $asamblea->titulo,
                'descripcion' => $asamblea->descripcion,
                'fecha' => $asamblea->fecha?->format('d/m/Y'),
                'hora' => $asamblea->hora,
                'lugar' => $asamblea->lugar,
                'estado' => $asamblea->estado,
                'control_asistencia' => (bool) $asamblea->control_asistencia,
                'quorum' => $this->svc->quorum($asamblea),
                'mis_casas' => $misCasas,
                'mi_casa' => $user->casa,
                'soy_dueno' => $user->tipo === 'dueño',
                'presente_mi_casa' => $presenteMiCasa,
                'puede_delegar' => $puedeDelegar,
                'delegacion' => $delegacion,
                'elegibles_inquilino' => $elegiblesInquilino,
                'elegibles_vecinos' => $elegiblesVecinos,
                'puntos' => $asamblea->puntos->map(function ($p) use ($asamblea, $misVotos) {
                    $data = [
                        'id' => $p->id,
                        'orden' => $p->orden,
                        'titulo' => $p->titulo,
                        'descripcion' => $p->descripcion,
                        'tipo' => $p->tipo,
                        'estado' => $p->estado,
                        'opciones' => $p->opciones->map(fn ($o) => ['id' => $o->id, 'opcion' => $o->opcion]),
                        'mis_votos' => $misVotos[$p->id] ?? [],
                    ];
                    // Resultados visibles solo cuando la asamblea está cerrada.
                    if ($asamblea->estado === 'cerrada') {
                        $data['resultados'] = $this->svc->resultadosPunto($p);
                    }

                    return $data;
                }),
            ],
        ]);
    }

    public function delegar(Request $request, $id)
    {
        $request->validate([
            'representante_id' => 'required|integer',
            'tipo' => 'required|in:inquilino,vecino',
        ]);

        $user = auth()->user();
        $asamblea = Asamblea::findOrFail($id);

        try {
            $this->svc->delegar($asamblea, $user->casa, $user, (int) $request->representante_id, $request->tipo);

            return response()->json(['success' => true, 'message' => 'Poder otorgado correctamente.']);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    public function revocar($id)
    {
        $user = auth()->user();
        $asamblea = Asamblea::findOrFail($id);

        try {
            $this->svc->revocar($asamblea, $user->casa, $user);

            return response()->json(['success' => true, 'message' => 'Poder revocado. Recuperaste tu voto.']);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    public function votar(Request $request, $id)
    {
        $request->validate(['punto_id' => 'required|integer']);

        $user = auth()->user();
        $asamblea = Asamblea::findOrFail($id);
        $punto = AsambleaPunto::where('asamblea_id', $asamblea->id)->findOrFail($request->punto_id);

        try {
            $n = $this->svc->emitirVoto($asamblea, $punto, $user, [
                'valor' => $request->input('valor'),
                'opcion_id' => $request->input('opcion_id'),
                'orden' => $request->input('orden', []),
                'por_casa' => $request->input('por_casa', []),
            ]);

            return response()->json([
                'success' => true,
                'message' => $n > 1 ? "Voto emitido por {$n} casas." : 'Voto emitido.',
            ]);
        } catch (\Throwable $e) {
            Log::info('Voto rechazado: '.$e->getMessage());

            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }
}
