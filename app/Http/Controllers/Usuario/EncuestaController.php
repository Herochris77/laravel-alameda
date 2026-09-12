<?php

namespace App\Http\Controllers\Usuario;

use App\Http\Controllers\Controller;
use App\Models\Encuesta;
use App\Models\EncuestaOpcion;
use App\Models\EncuestaRespuesta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EncuestaController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        return view('usuario.encuesta.index', compact('user'));
    }

    public function obtenerEncuestas(Request $request)
    {
        if ($request->ajax()) {
            $user = auth()->user();
            
            $encuestas = Encuesta::with(['opciones.respuestas'])
                ->paraUsuario($user->tipo)
                ->orderBy('created_at', 'desc')
                ->get()
                ->filter(function ($encuesta) {
                    return $encuesta->estaActiva();
                })
                ->values()
                ->map(function ($encuesta) use ($user) {
                    $misResp = $encuesta->respuestas->where('user_id', $user->id);
                    $yaVoto = $misResp->isNotEmpty();

                    if ($encuesta->tipo === 'ordenamiento') {
                        // Mi orden guardado, ordenado por la posición que elegí.
                        $misRespuestas = $yaVoto
                            ? $misResp->sortBy('posicion')->pluck('opcion_id')->values()->toArray()
                            : [];
                    } else {
                        $misRespuestas = $yaVoto ? $misResp->pluck('opcion_id')->toArray() : [];
                    }

                    // En ordenamiento mostramos "votantes"; en opción, el total de votos como antes.
                    $totalVotos = $encuesta->tipo === 'ordenamiento'
                        ? $encuesta->totalVotantes()
                        : $encuesta->respuestas->count();

                    return [
                        'id' => $encuesta->id,
                        'titulo' => $encuesta->titulo,
                        'descripcion' => $encuesta->descripcion,
                        'tipo' => $encuesta->tipo,
                        'multiple' => $encuesta->multiple,
                        'tipo_usuario' => $encuesta->tipo_usuario,
                        'fecha_fin' => $encuesta->fecha_fin?->format('d/m/Y'),
                        'hora_fin' => $encuesta->hora_fin,
                        'fecha_cierre' => $encuesta->fechaHoraCierre()?->format('d/m/Y H:i'),
                        'ya_voto' => $yaVoto,
                        'mis_respuestas' => $misRespuestas,
                        'total_votos' => $totalVotos,
                        'opciones' => $encuesta->opciones->map(function ($opcion) {
                            return [
                                'id' => $opcion->id,
                                'opcion' => $opcion->opcion,
                                'votos' => $opcion->respuestas->count(),
                            ];
                        }),
                    ];
                });

            return response()->json(['success' => true, 'encuestas' => $encuestas]);
        }
    }

    public function votar(Request $request)
    {
        try {
            $user = auth()->user();
            $encuesta = Encuesta::with('opciones')->findOrFail($request->encuesta_id);
            
            if (! $encuesta->estaActiva()) {
                return response()->json([
                    'success' => false,
                    'header' => '❌ Encuesta no disponible',
                    'message' => $encuesta->estaVencida()
                        ? 'La fecha y hora límite de esta encuesta ya vencieron.'
                        : 'Esta encuesta está cerrada.',
                ], 422);
            }

            if ($encuesta->tipo_usuario !== 'todos' && $encuesta->tipo_usuario !== $user->tipo) {
                return response()->json([
                    'success' => false,
                    'header' => '❌ Sin acceso',
                    'message' => 'No tienes permiso para votar en esta encuesta.',
                ]);
            }

            // Encuesta de ordenamiento: guardamos el orden completo del vecino.
            if ($encuesta->tipo === 'ordenamiento') {
                return $this->votarOrdenamiento($request, $encuesta, $user);
            }

            $opcionesValidas = $encuesta->opciones->pluck('id')->toArray();

            if ($encuesta->multiple) {
                $opcionesSeleccionadas = $request->opciones;
            } else {
                $opcionesSeleccionadas = [$request->opcion_id];
            }

            if (empty($opcionesSeleccionadas)) {
                return response()->json([
                    'success' => false,
                    'header' => '❌ Error',
                    'message' => 'Selecciona al menos una opción.',
                ]);
            }

            foreach ($opcionesSeleccionadas as $opcionId) {
                if (!in_array($opcionId, $opcionesValidas)) {
                    return response()->json([
                        'success' => false,
                        'header' => '❌ Error',
                        'message' => 'Opción inválida.',
                    ]);
                }
            }

            DB::transaction(function () use ($encuesta, $opcionesSeleccionadas, $user) {
                EncuestaRespuesta::where('encuesta_id', $encuesta->id)
                    ->where('user_id', $user->id)
                    ->delete();

                EncuestaOpcion::where('encuesta_id', $encuesta->id)
                    ->update(['votos_count' => 0]);

                foreach ($opcionesSeleccionadas as $opcionId) {
                    EncuestaRespuesta::create([
                        'encuesta_id' => $encuesta->id,
                        'opcion_id' => $opcionId,
                        'user_id' => $user->id,
                    ]);
                }

                EncuestaOpcion::where('encuesta_id', $encuesta->id)
                    ->whereIn('id', $opcionesSeleccionadas)
                    ->increment('votos_count');
            });

            Log::info('Usuario votó en encuesta', [
                'user_id' => $user->id,
                'encuesta_id' => $encuesta->id,
                'opciones' => $opcionesSeleccionadas,
            ]);

            return response()->json([
                'success' => true,
                'header' => '✅ Voto registrado',
                'message' => 'Tu voto ha sido registrado exitosamente.',
            ]);
        } catch (\Exception $e) {
            Log::error('Error al votar: '.$e->getMessage());
            return response()->json([
                'success' => false,
                'header' => '❌ Error',
                'message' => 'Error al registrar tu voto.',
            ]);
        }
    }

    /**
     * Registra el orden (ranking) que el vecino asignó a los proyectos.
     * Se guarda una fila por opción con su posición (1 = mayor prioridad).
     */
    private function votarOrdenamiento(Request $request, Encuesta $encuesta, $user)
    {
        $orden = $request->input('orden', []);

        $opcionesValidas = $encuesta->opciones->pluck('id')->map(fn ($id) => (int) $id)->sort()->values()->toArray();
        $ordenNormalizado = collect($orden)->map(fn ($id) => (int) $id)->values();

        // El orden debe incluir TODAS las opciones exactamente una vez.
        $mismasOpciones = $ordenNormalizado->sort()->values()->toArray() === $opcionesValidas;

        if ($ordenNormalizado->isEmpty() || $ordenNormalizado->count() !== count($opcionesValidas) || ! $mismasOpciones) {
            return response()->json([
                'success' => false,
                'header' => '❌ Orden inválido',
                'message' => 'Debes ordenar todos los proyectos antes de guardar.',
            ]);
        }

        DB::transaction(function () use ($encuesta, $ordenNormalizado, $user) {
            EncuestaRespuesta::where('encuesta_id', $encuesta->id)
                ->where('user_id', $user->id)
                ->delete();

            foreach ($ordenNormalizado as $index => $opcionId) {
                EncuestaRespuesta::create([
                    'encuesta_id' => $encuesta->id,
                    'opcion_id' => $opcionId,
                    'user_id' => $user->id,
                    'posicion' => $index + 1,
                ]);
            }
        });

        Log::info('Usuario ordenó encuesta', [
            'user_id' => $user->id,
            'encuesta_id' => $encuesta->id,
            'orden' => $ordenNormalizado->toArray(),
        ]);

        return response()->json([
            'success' => true,
            'header' => '✅ Orden guardado',
            'message' => 'Tu orden de prioridad ha sido registrado.',
        ]);
    }

    public function obtenerHistorial(Request $request)
    {
        if ($request->ajax()) {
            $user = auth()->user();
            
            $encuestas = Encuesta::with(['opciones.respuestas.usuario'])
                ->paraUsuario($user->tipo)
                ->orderBy('created_at', 'desc')
                ->get()
                ->filter(function ($encuesta) {
                    return ! $encuesta->estaActiva();
                })
                ->values()
                ->map(function ($encuesta) use ($user) {
                    $misResp = $encuesta->respuestas->where('user_id', $user->id);
                    $yaVoto = $misResp->isNotEmpty();
                    $totalVotos = $encuesta->tipo === 'ordenamiento'
                        ? $encuesta->totalVotantes()
                        : $encuesta->respuestas->count();

                    if ($encuesta->tipo === 'ordenamiento') {
                        $misRespuestas = $yaVoto
                            ? $misResp->sortBy('posicion')->pluck('opcion_id')->values()->toArray()
                            : [];

                        return [
                            'id' => $encuesta->id,
                            'titulo' => $encuesta->titulo,
                            'descripcion' => $encuesta->descripcion,
                            'estado' => $encuesta->estaVencida() ? 'vencida' : 'cerrada',
                            'tipo' => 'ordenamiento',
                            'fecha_fin' => $encuesta->fecha_fin?->format('d/m/Y'),
                            'hora_fin' => $encuesta->hora_fin,
                            'fecha_cierre' => $encuesta->fechaHoraCierre()?->format('d/m/Y H:i'),
                            'ya_voto' => $yaVoto,
                            'mis_respuestas' => $misRespuestas,
                            'total_votos' => $totalVotos,
                            // Orden ganador + matriz (sin nombres, solo agregado).
                            'resultado_orden' => $encuesta->resultadosOrdenamiento(false),
                            'opciones' => $encuesta->opciones->map(fn ($o) => [
                                'id' => $o->id,
                                'opcion' => $o->opcion,
                            ]),
                        ];
                    }

                    $misRespuestas = $yaVoto ? $misResp->pluck('opcion_id')->toArray() : [];

                    return [
                        'id' => $encuesta->id,
                        'titulo' => $encuesta->titulo,
                        'descripcion' => $encuesta->descripcion,
                        'estado' => $encuesta->estaVencida() ? 'vencida' : 'cerrada',
                        'tipo' => $encuesta->tipo,
                        'fecha_fin' => $encuesta->fecha_fin?->format('d/m/Y'),
                        'hora_fin' => $encuesta->hora_fin,
                        'fecha_cierre' => $encuesta->fechaHoraCierre()?->format('d/m/Y H:i'),
                        'ya_voto' => $yaVoto,
                        'mis_respuestas' => $misRespuestas,
                        'total_votos' => $totalVotos,
                        'opciones' => $encuesta->opciones->map(function ($opcion) use ($totalVotos, $user) {
                            $votosReales = $opcion->respuestas->count();
                            $porcentaje = $totalVotos > 0 ? round(($votosReales / $totalVotos) * 100, 1) : 0;
                            $participantes = $opcion->respuestas->map(function ($respuesta) {
                                return [
                                    'nombre' => $respuesta->usuario->nombre,
                                    'casa' => $respuesta->usuario->casa,
                                ];
                            });
                            
                            return [
                                'id' => $opcion->id,
                                'opcion' => $opcion->opcion,
                                'votos' => $votosReales,
                                'porcentaje' => $porcentaje,
                                'participantes' => $participantes,
                            ];
                        }),
                    ];
                });

            return response()->json(['success' => true, 'encuestas' => $encuestas]);
        }
    }
}