<?php

namespace App\Http\Controllers\Usuario;

use App\Http\Controllers\Controller;
use App\Models\Vehiculo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class VehiculoController extends Controller
{
    public function index()
    {
        $vehiculos = Vehiculo::get();

        return view('usuario.vehiculo.index', compact('vehiculos'));
    }

    public function guardar(Request $request)
    {
        try {
            $validated = $request->validate([
                'marca' => 'required|string|max:50',
                'modelo' => 'required|string|max:50',
                'anio' => 'nullable|string|max:10',
                'color' => 'nullable|string|max:30',
                'placas' => 'required|string|max:15|unique:vehiculos,placas',
                'tipo' => 'required|in:automovil,motocicleta,camioneta,otro',
                'foto' => 'nullable|image|max:5120',
                'observaciones' => 'nullable|string|max:500',
            ]);

            $fotoPath = null;
            if ($request->hasFile('foto')) {
                $fotoPath = $request->file('foto')->store('vehiculos', 'public');
            }

            Vehiculo::create([
                'user_id' => Auth::user()->id,
                'marca' => $validated['marca'],
                'modelo' => $validated['modelo'],
                'anio' => $validated['anio'] ?? null,
                'color' => $validated['color'] ?? null,
                'placas' => $validated['placas'],
                'tipo' => $validated['tipo'],
                'foto' => $fotoPath,
                'observaciones' => $validated['observaciones'] ?? null,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Vehículo registrado correctamente.',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->validator->errors()->first(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error al guardar vehículo: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error al registrar el vehículo.',
            ]);
        }
    }

    public function actualizar(Request $request, $id)
    {
        try {
            $vehiculo = Vehiculo::where('user_id', Auth::user()->id)->findOrFail($id);

            $validated = $request->validate([
                'marca' => 'required|string|max:50',
                'modelo' => 'required|string|max:50',
                'anio' => 'nullable|string|max:10',
                'color' => 'nullable|string|max:30',
                'placas' => 'required|string|max:15|unique:vehiculos,placas,'.$id,
                'tipo' => 'required|in:automovil,motocicleta,camioneta,otro',
                'foto' => 'nullable|image|max:5120',
                'observaciones' => 'nullable|string|max:500',
            ]);

            if ($request->hasFile('foto')) {
                if ($vehiculo->foto && Storage::disk('public')->exists($vehiculo->foto)) {
                    Storage::disk('public')->delete($vehiculo->foto);
                }
                $vehiculo->foto = $request->file('foto')->store('vehiculos', 'public');
            }

            $vehiculo->update([
                'marca' => $validated['marca'],
                'modelo' => $validated['modelo'],
                'anio' => $validated['anio'] ?? null,
                'color' => $validated['color'] ?? null,
                'placas' => $validated['placas'],
                'tipo' => $validated['tipo'],
                'observaciones' => $validated['observaciones'] ?? null,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Vehículo actualizado correctamente.',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->validator->errors()->first(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error al actualizar vehículo: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el vehículo.',
            ]);
        }
    }

    public function eliminar($id)
    {
        try {
            $vehiculo = Vehiculo::where('user_id', Auth::user()->id)->findOrFail($id);

            if ($vehiculo->foto && Storage::disk('public')->exists($vehiculo->foto)) {
                Storage::disk('public')->delete($vehiculo->foto);
            }

            $vehiculo->delete();

            return response()->json([
                'success' => true,
                'message' => 'Vehículo eliminado correctamente.',
            ]);
        } catch (\Exception $e) {
            Log::error('Error al eliminar vehículo: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el vehículo.',
            ]);
        }
    }
}
