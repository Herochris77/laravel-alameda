<?php

namespace App\Http\Controllers\Usuario;

use App\Http\Controllers\Controller;
use App\Models\Mascota;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class MascotaController extends Controller
{
    public function index()
    {
        return view('usuario.mascota.crear');
    }

    public function crear(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'tipo' => 'required|string|in:perro,gato,otro',
            'edad' => 'required|integer|min:0|max:30',
            'genero' => 'required|string|in:macho,hembra',
            'caracteristicas' => 'nullable|string|max:500',
            'vacunas' => 'nullable|boolean',
            'esterilizado' => 'nullable|boolean',
            'amistoso' => 'nullable|boolean',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
        ]);

        try {
            $fotoPath = null;

            if ($request->hasFile('foto')) {
                $foto = $request->file('foto');

                $nombreArchivo = time().'_'.uniqid().'.'.$foto->getClientOriginalExtension();
                $directorio = storage_path('app/public/mascotas');

                if (! file_exists($directorio)) {
                    mkdir($directorio, 0755, true);
                }

                $foto->move($directorio, $nombreArchivo);
                $fotoPath = 'mascotas/'.$nombreArchivo;
            } else {
                Log::info('No se recibió archivo de foto');
            }

            Mascota::create([
                'user_id' => Auth::user()->id,
                'nombre' => $request->nombre,
                'tipo' => $request->tipo,
                'edad' => $request->edad,
                'genero' => $request->genero,
                'caracteristicas' => $request->caracteristicas,
                'vacunas' => $request->boolean('vacunas'),
                'esterilizado' => $request->boolean('esterilizado'),
                'amistoso' => $request->boolean('amistoso'),
                'foto' => $fotoPath,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Mascota registrada exitosamente.',
            ]);
        } catch (Exception $e) {
            Log::error('Error al registrar mascota: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al registrar la mascota. Por favor intente de nuevo.',
            ], 500);
        }
    }

    public function misMascotas(Request $request)
    {
        $query = Mascota::with('user');

        if ($request->filled('filtro')) {
            $filtro = $request->filtro;

            if ($filtro === 'mis-mascotas') {
                $query->where('user_id', Auth::user()->id);
            } elseif ($filtro === 'perro') {
                $query->where('tipo', 'perro');
            } elseif ($filtro === 'gato') {
                $query->where('tipo', 'gato');
            } elseif ($filtro === 'otro') {
                $query->where('tipo', 'otro');
            }
        }

        $mascotas = $query->orderBy('created_at', 'desc')->get();

        return view('usuario.mascota.index', compact('mascotas'));
    }

    public function editar($id)
    {
        $mascota = Mascota::findOrFail($id);

        if ($mascota->user_id !== Auth::user()->id) {
            abort(403, 'No tienes permiso para editar esta mascota.');
        }

        return view('usuario.mascota.editar', compact('mascota'));
    }

    public function actualizar(Request $request, $id)
    {
        $mascota = Mascota::findOrFail($id);

        if ($mascota->user_id !== Auth::user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permiso para editar esta mascota.',
            ], 403);
        }

        $request->validate([
            'nombre' => 'required|string|max:255',
            'tipo' => 'required|string|in:perro,gato,otro',
            'edad' => 'required|integer|min:0|max:30',
            'genero' => 'required|string|in:macho,hembra',
            'caracteristicas' => 'nullable|string|max:500',
            'vacunas' => 'nullable|boolean',
            'esterilizado' => 'nullable|boolean',
            'amistoso' => 'nullable|boolean',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
        ]);

        try {
            $fotoPath = $mascota->foto;

            if ($request->hasFile('foto')) {
                if ($mascota->foto) {
                    $rutaAnterior = storage_path('app/public/'.$mascota->foto);
                    if (file_exists($rutaAnterior)) {
                        unlink($rutaAnterior);
                    }
                }

                $foto = $request->file('foto');
                $nombreArchivo = time().'_'.uniqid().'.'.$foto->getClientOriginalExtension();
                $directorio = storage_path('app/public/mascotas');

                if (! file_exists($directorio)) {
                    mkdir($directorio, 0755, true);
                }

                $foto->move($directorio, $nombreArchivo);
                $fotoPath = 'mascotas/'.$nombreArchivo;
            }

            $mascota->update([
                'nombre' => $request->nombre,
                'tipo' => $request->tipo,
                'edad' => $request->edad,
                'genero' => $request->genero,
                'caracteristicas' => $request->caracteristicas,
                'vacunas' => $request->boolean('vacunas'),
                'esterilizado' => $request->boolean('esterilizado'),
                'amistoso' => $request->boolean('amistoso'),
                'foto' => $fotoPath,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Mascota actualizada exitosamente.',
            ]);
        } catch (Exception $e) {
            Log::error('Error al actualizar mascota: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al actualizar la mascota. Por favor intente de nuevo.',
            ], 500);
        }
    }

    public function eliminar($id)
    {
        $mascota = Mascota::findOrFail($id);

        if ($mascota->user_id !== Auth::user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permiso para eliminar esta mascota.',
            ], 403);
        }

        try {
            if ($mascota->foto && Storage::exists('public/'.$mascota->foto)) {
                Storage::delete('public/'.$mascota->foto);
            }

            $mascota->delete();

            return response()->json([
                'success' => true,
                'message' => 'Mascota eliminada exitosamente.',
            ]);
        } catch (Exception $e) {
            Log::error('Error al eliminar mascota: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al eliminar la mascota. Por favor intente de nuevo.',
            ], 500);
        }
    }
}
