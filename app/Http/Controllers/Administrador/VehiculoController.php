<?php

namespace App\Http\Controllers\Administrador;

use App\Http\Controllers\Controller;
use App\Models\Vehiculo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class VehiculoController extends Controller
{
    public function index()
    {
        return view('administrador.vehiculo');
    }

    public function obtenerVehiculos(Request $request)
    {
        if ($request->ajax()) {
            $vehiculos = Vehiculo::with('user')
                ->select(['id', 'user_id', 'marca', 'modelo', 'anio', 'color', 'placas', 'tipo', 'foto', 'created_at']);

            return datatables()->of($vehiculos)
                ->addColumn('usuario', function ($v) {
                    if ($v->user) {
                        if ($v->user->trashed()) {
                            return '<span class="ui red text">Usuario eliminado</span>';
                        }

                        return $v->user->nombre;
                    }

                    return '—';
                })
                ->addColumn('casa', function ($v) {
                    if ($v->user && ! $v->user->trashed()) {
                        return $v->user->casa ?? '—';
                    }

                    return '—';
                })
                ->addColumn('foto_preview', function ($v) {
                    if ($v->foto && Storage::disk('public')->exists($v->foto)) {
                        $url = asset('storage/'.$v->foto);

                        return '<button class="ui mini teal button btn-ver-foto" data-img="'.$url.'">
                                    <i class="image icon"></i> Ver
                                </button>';
                    }

                    return '<span class="ui gray text">Sin foto</span>';
                })
                ->addColumn('tipo_badge', function ($v) {
                    $tipos = [
                        'automovil' => ['color' => '#3b82f6', 'icon' => 'car', 'label' => 'Automóvil'],
                        'motocicleta' => ['color' => '#f59e0b', 'icon' => 'motorcycle', 'label' => 'Motocicleta'],
                        'camioneta' => ['color' => '#10b981', 'icon' => 'truck', 'label' => 'Camioneta'],
                        'otro' => ['color' => '#6b7280', 'icon' => 'question circle', 'label' => 'Otro'],
                    ];
                    $tipo = $tipos[$v->tipo] ?? $tipos['otro'];

                    return '<span class="ui label" style="background: '.$tipo['color'].'; color: white;">
                                <i class="'.$tipo['icon'].' icon"></i> '.$tipo['label'].'
                            </span>';
                })
                ->addColumn('acciones', function ($v) {
                    return '<div class="ui center aligned">
                                <button class="ui red small icon button btn-eliminar" data-id="'.$v->id.'">
                                    <i class="trash icon"></i>
                                </button>
                            </div>';
                })
                ->rawColumns(['foto_preview', 'tipo_badge', 'acciones'])
                ->make(true);
        }
    }

    public function eliminarVehiculo($id)
    {
        try {
            $vehiculo = Vehiculo::findOrFail($id);

            if ($vehiculo->foto && Storage::disk('public')->exists($vehiculo->foto)) {
                Storage::disk('public')->delete($vehiculo->foto);
            }

            $vehiculo->delete();

            return response()->json([
                'header' => 'Vehículo eliminado',
                'success' => true,
                'message' => 'El vehículo se eliminó correctamente.',
            ]);
        } catch (\Exception $e) {
            Log::error('Error al eliminar vehículo: '.$e->getMessage());

            return response()->json([
                'header' => 'Error',
                'success' => false,
                'message' => 'Error al eliminar el vehículo.',
            ]);
        }
    }
}
