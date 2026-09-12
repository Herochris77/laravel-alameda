<?php

namespace App\Http\Controllers\Administrador;

use App\Http\Controllers\Controller;
use App\Models\Encuesta;
use App\Models\EncuestaOpcion;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class EncuestaController extends Controller
{
    public function index()
    {
        return view('administrador.encuesta');
    }

    public function obtenerEncuestas(Request $request)
    {
        if ($request->ajax()) {
            $encuestas = Encuesta::with(['creador', 'opciones'])
                ->withCount('respuestas')
                ->orderBy('created_at', 'desc')
                ->get();

            return datatables()->of($encuestas)
                ->addColumn('estado_html', function ($e) {
                    $estaActiva = $e->estaActiva();

                    $color = $estaActiva ? 'green' : 'red';
                    $texto = $estaActiva ? 'Abierta' : 'Cerrada';

                    return "<span class='ui $color label'>$texto</span>";
                })
                ->addColumn('tipo_html', function ($e) {
                    $tipos = [
                        'dueño' => 'Dueños',
                        'inquilino' => 'Inquilinos',
                        'todos' => 'Todos',
                    ];
                    return $tipos[$e->tipo_usuario] ?? $e->tipo_usuario;
                })
                ->addColumn('multiple_html', function ($e) {
                    if ($e->tipo === 'ordenamiento') {
                        return '<span class="ui purple label">Ordenamiento</span>';
                    }
                    return $e->multiple
                        ? '<span class="ui teal label">Múltiple</span>'
                        : '<span class="ui grey label">Única</span>';
                })
                ->addColumn('votos', function ($e) {
                    return $e->respuestas_count;
                })
                ->addColumn('fecha_cierre', function ($e) {
                    $cierre = $e->fechaHoraCierre();

                    return $cierre
                        ? $cierre->format('d/m/Y H:i')
                        : 'Sin fecha límite';
                })
                ->addColumn('acciones', function ($e) {
                    $btnVer = '<button class="ui blue small icon button btn-ver" data-id="'.$e->id.'" title="Ver resultados">
                        <i class="chart pie icon"></i>
                    </button>';
                    
                    $btnCerrar = $e->estaActiva()
                        ? '<button class="ui orange small icon button btn-cerrar" data-id="'.$e->id.'" title="Cerrar encuesta">
                            <i class="lock icon"></i>
                           </button>'
                        : '<button class="ui green small icon button btn-abrir" data-id="'.$e->id.'" title="Abrir encuesta">
                            <i class="unlock icon"></i>
                           </button>';
                    
                    $btnEliminar = '<button class="ui red small icon button btn-eliminar" data-id="'.$e->id.'" title="Eliminar">
                        <i class="trash icon"></i>
                    </button>';

                    return "<div class='ui center aligned buttons'>$btnVer $btnCerrar $btnEliminar</div>";
                })
                ->rawColumns(['estado_html', 'tipo_html', 'multiple_html', 'acciones', 'fecha_cierre'])
                ->make(true);
        }
    }

    public function crearEncuesta(Request $request)
    {
        try {
            $request->validate([
                'titulo' => 'required|string|max:255',
                'descripcion' => 'nullable|string',
                'tipo_usuario' => 'required|in:dueño,inquilino,todos',
                'tipo' => 'nullable|in:opcion,ordenamiento',
                'multiple' => 'boolean',
                'fecha_fin' => [
                    'nullable',
                    'date_format:Y-m-d',
                    'required_with:hora_fin',
                ],
                'hora_fin' => [
                    'nullable',
                    'date_format:H:i',
                    'required_with:fecha_fin',
                ],
                'opciones' => 'required|array|min:2',
                'opciones.*' => 'required|string|max:255',
            ], [
                'fecha_fin.required_with' => 'Debes seleccionar la fecha de cierre.',
                'hora_fin.required_with' => 'Debes seleccionar la hora de cierre.',
                'hora_fin.date_format' => 'La hora de cierre no tiene un formato válido.',
            ]);

            $tipo = $request->tipo === 'ordenamiento' ? 'ordenamiento' : 'opcion';

            if ($request->filled('fecha_fin') && $request->filled('hora_fin')) {
                $fechaHoraCierre = Carbon::createFromFormat(
                    'Y-m-d H:i',
                    $request->fecha_fin.' '.$request->hora_fin,
                    config('app.timezone')
                );

                if ($fechaHoraCierre->lessThanOrEqualTo(now())) {
                    return response()->json([
                        'success' => false,
                        'header' => 'Fecha de cierre inválida',
                        'message' => 'La fecha y hora de cierre deben ser posteriores al momento actual.',
                    ], 422);
                }
            }

            $encuesta = Encuesta::create([
                'titulo' => $request->titulo,
                'descripcion' => $request->descripcion,
                'tipo_usuario' => $request->tipo_usuario,
                'tipo' => $tipo,
                // En ordenamiento no aplica "opción múltiple".
                'multiple' => $tipo === 'ordenamiento' ? false : $request->boolean('multiple'),
                'estado' => 'abierta',
                'fecha_fin' => $request->fecha_fin,
                'hora_fin' => $request->hora_fin,
                'created_by' => Auth::user()->id,
            ]);

            foreach ($request->opciones as $opcionTexto) {
                EncuestaOpcion::create([
                    'encuesta_id' => $encuesta->id,
                    'opcion' => trim($opcionTexto),
                    'votos_count' => 0,
                ]);
            }

            Log::info('Encuesta creada', ['id' => $encuesta->id, 'titulo' => $encuesta->titulo]);

            return response()->json([
                'success' => true,
                'header' => '✅ Encuesta creada',
                'message' => 'La encuesta se creó exitosamente.',
            ]);
        } catch (\Exception $e) {
            Log::error('Error al crear encuesta: '.$e->getMessage());
            return response()->json([
                'header' => '❌ Error',
                'message' => 'Error al crear la encuesta.',
            ]);
        }
    }

    public function obtenerEncuesta($id)
    {
        $encuesta = Encuesta::with(['opciones', 'creador'])->findOrFail($id);
        
        return response()->json([
            'success' => true,
            'encuesta' => $encuesta,
        ]);
    }

    public function obtenerResultados($id)
    {
        $encuesta = Encuesta::with(['opciones.respuestas.usuario'])->findOrFail($id);

        if ($encuesta->esOrdenamiento()) {
            return response()->json([
                'success' => true,
                'encuesta' => array_merge([
                    'id' => $encuesta->id,
                    'titulo' => $encuesta->titulo,
                    'descripcion' => $encuesta->descripcion,
                    'estado' => $encuesta->estado,
                    'tipo' => 'ordenamiento',
                ], $encuesta->resultadosOrdenamiento(true)),
            ]);
        }

        $totalVotos = $encuesta->respuestas()->count();

        $opciones = $encuesta->opciones->map(function ($opcion) use ($totalVotos) {
            $votosReales = $opcion->respuestas->count();
            $porcentaje = $totalVotos > 0 ? round(($votosReales / $totalVotos) * 100, 1) : 0;
            return [
                'id' => $opcion->id,
                'opcion' => $opcion->opcion,
                'votos' => $votosReales,
                'porcentaje' => $porcentaje,
                'respondieron' => $opcion->respuestas->map(function ($r) {
                    return [
                        'nombre' => optional($r->usuario)->nombre ?? 'Vecino',
                        'casa' => optional($r->usuario)->casa ?? '—',
                    ];
                }),
            ];
        });

        return response()->json([
            'success' => true,
            'encuesta' => [
                'id' => $encuesta->id,
                'titulo' => $encuesta->titulo,
                'descripcion' => $encuesta->descripcion,
                'estado' => $encuesta->estado,
                'total_votos' => $totalVotos,
                'opciones' => $opciones,
            ],
        ]);
    }

    public function cerrarEncuesta($id)
    {
        try {
            $encuesta = Encuesta::findOrFail($id);
            $encuesta->estado = 'cerrada';
            $encuesta->save();

            return response()->json([
                'success' => true,
                'header' => '🔒 Encuesta cerrada',
                'message' => 'La encuesta ha sido cerrada exitosamente.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'header' => '❌ Error',
                'message' => 'Error al cerrar la encuesta.',
            ]);
        }
    }

    public function abrirEncuesta($id)
    {
        try {
            $encuesta = Encuesta::findOrFail($id);
            $encuesta->estado = 'abierta';
            $encuesta->save();

            return response()->json([
                'success' => true,
                'header' => '🔓 Encuesta abierta',
                'message' => 'La encuesta ha sido abierta nuevamente.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'header' => '❌ Error',
                'message' => 'Error al abrir la encuesta.',
            ]);
        }
    }

    public function eliminarEncuesta($id)
    {
        try {
            $encuesta = Encuesta::findOrFail($id);
            $encuesta->delete();

            return response()->json([
                'success' => true,
                'header' => '🗑️ Eliminada',
                'message' => 'La encuesta ha sido eliminada.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'header' => '❌ Error',
                'message' => 'Error al eliminar la encuesta.',
            ]);
        }
    }
}
