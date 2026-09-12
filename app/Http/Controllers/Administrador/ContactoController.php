<?php

namespace App\Http\Controllers\Administrador;

use App\Http\Controllers\Controller;
use App\Models\Contacto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ContactoController extends Controller
{
    public function nuevoContacto()
    {
        return view('administrador.contacto');
    }

    public function crearContacto(Request $request)
    {
        try {
            Contacto::create([
                'nombre_contacto' => $request->nombre_contacto,
                'numero_contacto' => $request->numero_contacto,
                'tipo_contacto' => $request->tipo_contacto,
                'pagina_web' => isset($request->pagina_web) ? $request->pagina_web : '',
                'created_by' => Auth::user()->id,
            ]);

            return response()->json([
                'success' => true,
                'header' => 'Contacto guardado ✅',
                'message' => 'Tu contacto se guardó correctamente',
            ]);
        } catch (\Exception $e) {
            Log::error('Ocurrió un error al generar un contacto: '.$e);

            return response()->json([
                'success' => false,
                'header' => '❌ Ocurrió un error',
                'message' => 'Por favor intentalo más tarde y reportalo al desarrollador de la aplicación Christian Martínez',
            ]);
        }
    }

    public function obtenerContactos(Request $request)
    {
        if ($request->ajax()) {
            $comunicados = Contacto::with('user')
                ->select(['id', 'nombre_contacto', 'numero_contacto', 'tipo_contacto', 'pagina_web', 'created_by']);

            return datatables()->of($comunicados)
                ->addColumn('autor', fn ($c) => $c->user->nombre ?? '—')
                ->addColumn('acciones', function ($c) {
                    // Solo muestra el botón si el usuario actual lo creó
                    if (Auth::user()->id == $c->created_by) {
                        return '<div class="ui center aligned buttons">
                                    <button class="ui blue small icon button btn-editar" data-id="'.$c->id.'" 
                                        data-nombre="'.e($c->nombre_contacto).'" 
                                        data-numero="'.e($c->numero_contacto).'" 
                                        data-tipo="'.e($c->tipo_contacto).'" 
                                        data-pagina="'.e($c->pagina_web).'">
                                        <i class="edit icon"></i>
                                    </button>
                                    <button class="ui red small icon button btn-eliminar" data-id="'.$c->id.'">
                                        <i class="trash icon"></i>
                                    </button>
                                </div>';
                    }

                    return ' <div class="ui center aligned"><i data-content="Creado por: '.$c->user->nombre.'" class="exclamation circle icon"></i></div>';
                })
                ->rawColumns(['acciones'])
                ->make(true);
        }
    }

    public function eliminarContacto($id)
    {
        try {
            $contacto = Contacto::findOrFail($id);

            // Solo permite eliminar si el usuario actual lo creó
            if ($contacto->created_by !== Auth::user()->id) {
                return response()->json(
                    [
                        'header' => '❌ Ups... 🛑',
                        'success' => false,
                        'message' => 'No tienes autorización para eliminar el contacto',
                    ]
                );
            }

            $contacto->delete();

            return response()->json(
                [
                    'header' => 'Contacto eliminado ✅',
                    'success' => true,
                    'message' => 'Se eliminó correctamente tu contacto',
                ]
            );
        } catch (\Exception $e) {
            Log::error('Error al eliminar contacto: '.$e->getMessage());

            return response()->json(
                [
                    'header' => '❌ Ups... 🛑',
                    'success' => false,
                    'message' => 'Por favor intentalo más tarde y reportalo al desarrollador de la aplicación Christian Martínez',
                ]
            );
        }
    }

    public function actualizarContacto(Request $request, $id)
    {
        try {
            $contacto = Contacto::findOrFail($id);

            if (Auth::user()->id !== $contacto->created_by) {
                return response()->json(['header' => '❌ Error', 'message' => 'No autorizado']);
            }

            $contacto->update([
                'nombre_contacto' => $request->nombre_contacto,
                'numero_contacto' => $request->numero_contacto,
                'tipo_contacto' => $request->tipo_contacto,
                'pagina_web' => $request->pagina_web,
            ]);

            return response()->json([
                'header' => '✅ Editado correctamente',
                'message' => 'Los datos del contacto fueron actualizados',
            ]);
        } catch (\Exception $e) {
            Log::error('Error al actualizar contacto: '.$e->getMessage());

            return response()->json(['header' => '❌ Error', 'message' => 'Error al actualizar']);
        }
    }
}
