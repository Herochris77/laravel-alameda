<?php

namespace App\Http\Controllers\Administrador;

use App\Http\Controllers\Controller;
use App\Models\CorreoEnviado;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CorreoController extends Controller
{
    public function index()
    {
        return view('administrador.correos', ['consumo' => $this->consumo()]);
    }

    /**
     * Cuántos mensajes SMTP han salido, para vigilar la cuota del hosting.
     *
     * Cada renglón de `correos_enviados` es UN mensaje, aunque lleve 44
     * destinatarios en copia oculta: es así como lo cuenta el proveedor de
     * correo, y por eso es el número que importa vigilar.
     */
    private function consumo(): array
    {
        $porDia = CorreoEnviado::selectRaw('DATE(created_at) dia, COUNT(*) total')
            ->where('created_at', '>=', now()->subDays(14)->startOfDay())
            ->groupByRaw('DATE(created_at)')
            ->orderByRaw('DATE(created_at)')
            ->get();

        return [
            'hoy' => CorreoEnviado::whereDate('created_at', today())->count(),
            'mes' => CorreoEnviado::where('created_at', '>=', now()->startOfMonth())->count(),
            'destinatarios_hoy' => CorreoEnviado::whereDate('created_at', today())->get()
                ->sum(fn ($c) => count(array_filter(explode(',', (string) $c->destinatarios)))),
            'errores_hoy' => CorreoEnviado::whereDate('created_at', today())
                ->where('estado', 'error')->count(),
            'pico' => (int) $porDia->max('total'),
            'serie' => $porDia->map(fn ($d) => [
                'dia' => \Carbon\Carbon::parse($d->dia)->format('d/m'),
                'total' => (int) $d->total,
            ])->all(),
        ];
    }

    public function listar(Request $request)
    {
        if ($request->ajax()) {
            $correos = CorreoEnviado::select(['id', 'destinatarios', 'asunto', 'origen', 'estado', 'created_at']);

            return datatables()->of($correos)
                ->editColumn('created_at', function ($c) {
                    return $c->created_at->format('d/m/Y H:i');
                })
                ->editColumn('estado', function ($c) {
                    if ($c->estado == 'enviado') {
                        return '<div class="ui green horizontal label">Enviado</div>';
                    }

                    return '<div class="ui red horizontal label">Error</div>';
                })
                ->addColumn('checkbox', function ($c) {
                    return '<div class="ui center aligned">
                                <div class="ui checkbox">
                                    <input type="checkbox" class="checkbox-correo" value="'.$c->id.'">
                                    <label></label>
                                </div>
                            </div>';
                })
                ->addColumn('acciones', function ($c) {
                    return '<div class="ui center aligned">
                                <button class="ui blue icon button btn-ver" data-id="'.$c->id.'">
                                    <i class="eye icon"></i> Ver
                                </button>
                                <button class="ui red icon button btn-eliminar" data-id="'.$c->id.'">
                                    <i class="trash icon"></i> Eliminar
                                </button>
                            </div>';
                })
                ->rawColumns(['checkbox', 'estado', 'acciones'])
                ->make(true);
        }
    }

    public function ver($id)
    {
        try {
            $correo = CorreoEnviado::findOrFail($id);

            return response()->json([
                'success' => true,
                'destinatarios' => $correo->destinatarios,
                'asunto' => $correo->asunto,
                'titulo' => $correo->titulo,
                'mensaje' => $correo->mensaje,
                'origen' => $correo->origen,
                'estado' => $correo->estado,
                'created_at' => $correo->created_at->format('d/m/Y H:i'),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener el correo: '.$e->getMessage(),
            ]);
        }
    }

    public function eliminar($id)
    {
        try {
            $correo = CorreoEnviado::findOrFail($id);
            $correo->delete();

            return response()->json([
                'header' => '✅ Correo eliminado',
                'success' => true,
                'message' => 'El registro de correo ha sido eliminado correctamente.',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'header' => '❌ Error',
                'success' => false,
                'message' => 'Error al eliminar el correo: '.$e->getMessage(),
            ]);
        }
    }

    public function eliminarSeleccionados(Request $request): JsonResponse
    {
        $ids = $request->input('ids', []);

        if (empty($ids)) {
            return response()->json([
                'header' => '❌ Sin selección',
                'success' => false,
                'message' => 'No seleccionaste ningún registro para eliminar.',
            ]);
        }

        try {
            $eliminados = CorreoEnviado::whereIn('id', $ids)->delete();

            return response()->json([
                'header' => '✅ Eliminados',
                'success' => true,
                'message' => "Se eliminaron {$eliminados} registro(s) correctamente.",
            ]);
        } catch (Exception $e) {
            return response()->json([
                'header' => '❌ Error',
                'success' => false,
                'message' => 'Error al eliminar los registros: '.$e->getMessage(),
            ]);
        }
    }
}
