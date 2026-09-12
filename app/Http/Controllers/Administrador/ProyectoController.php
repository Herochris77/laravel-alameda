<?php

namespace App\Http\Controllers\Administrador;

use App\Http\Controllers\Controller;
use App\Models\Proyecto;
use App\Models\ProyectoAvance;
use App\Models\User;
use App\Notifications\NotificacionGenerica;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;

class ProyectoController extends Controller
{
    public function index()
    {
        $proyectos = Proyecto::withCount('avances')->orderByDesc('created_at')->get();
        $estados = Proyecto::estados();

        return view('administrador.proyectos.index', compact('proyectos', 'estados'));
    }

    public function guardar(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'costo' => 'nullable|numeric|min:0',
            'estado' => 'required|in:por_iniciar,en_proceso,pausado,terminado',
        ]);

        $proyecto = Proyecto::create([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'costo' => $request->filled('costo') ? $request->costo : null,
            'estado' => $request->estado,
            'avance' => $request->estado === 'terminado' ? 100 : 0,
            'created_by' => auth()->user()->id,
        ]);

        // Si nace "en proceso", se avisa a los vecinos.
        if ($proyecto->estado === 'en_proceso') {
            $this->notificarEnProceso($proyecto);
        }

        return redirect()->route('admin.proyectos.index')->with('ok', 'Proyecto creado.');
    }

    public function ver($id)
    {
        $proyecto = Proyecto::with('avances')->findOrFail($id);
        $estados = Proyecto::estados();

        return view('administrador.proyectos.ver', compact('proyecto', 'estados'));
    }

    public function actualizar(Request $request, $id)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'costo' => 'nullable|numeric|min:0',
            'estado' => 'required|in:por_iniciar,en_proceso,pausado,terminado',
        ]);

        $proyecto = Proyecto::findOrFail($id);
        $estadoAnterior = $proyecto->estado;
        $proyecto->update([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'costo' => $request->filled('costo') ? $request->costo : null,
            'estado' => $request->estado,
            'avance' => $request->estado === 'terminado' ? 100 : $proyecto->avance,
        ]);

        // Avisar solo cuando ENTRA a "en proceso" (no en cada guardado).
        if ($estadoAnterior !== 'en_proceso' && $proyecto->estado === 'en_proceso') {
            $this->notificarEnProceso($proyecto);
        }

        return back()->with('ok', 'Proyecto actualizado.');
    }

    public function registrarAvance(Request $request, $id)
    {
        $request->validate([
            'porcentaje' => 'required|integer|min:0|max:100',
            'comentario' => 'required|string|max:1000',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
        ]);

        $proyecto = Proyecto::findOrFail($id);
        $porcentaje = (int) $request->porcentaje;

        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('proyectos/avances', 'public');
        }

        ProyectoAvance::create([
            'proyecto_id' => $proyecto->id,
            'porcentaje' => $porcentaje,
            'comentario' => $request->comentario,
            'foto' => $fotoPath,
            'autor' => auth()->user()->nombre ?? 'Comité',
        ]);

        // Actualiza el avance y ajusta el estado (respetando "pausado").
        $nuevoEstado = $proyecto->estado;
        if ($proyecto->estado !== 'pausado') {
            if ($porcentaje >= 100) {
                $nuevoEstado = 'terminado';
            } elseif ($porcentaje > 0 && $proyecto->estado === 'por_iniciar') {
                $nuevoEstado = 'en_proceso';
            }
        }
        $proyecto->update(['avance' => $porcentaje, 'estado' => $nuevoEstado]);

        $this->notificarAvance($proyecto, $porcentaje, $request->comentario);

        return back()->with('ok', 'Avance publicado y notificado a los vecinos.');
    }

    public function editarAvance(Request $request, $id, $avanceId)
    {
        $request->validate([
            'porcentaje' => 'required|integer|min:0|max:100',
            'comentario' => 'required|string|max:1000',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
        ]);

        $proyecto = Proyecto::findOrFail($id);
        $avance = ProyectoAvance::where('proyecto_id', $proyecto->id)->findOrFail($avanceId);

        $data = [
            'porcentaje' => (int) $request->porcentaje,
            'comentario' => $request->comentario,
        ];

        // Quitar la foto actual (si se pidió) o reemplazarla por una nueva.
        if ($request->boolean('quitar_foto') && $avance->foto) {
            if (Storage::disk('public')->exists($avance->foto)) {
                Storage::disk('public')->delete($avance->foto);
            }
            $data['foto'] = null;
        }
        if ($request->hasFile('foto')) {
            if ($avance->foto && Storage::disk('public')->exists($avance->foto)) {
                Storage::disk('public')->delete($avance->foto);
            }
            $data['foto'] = $request->file('foto')->store('proyectos/avances', 'public');
        }

        $avance->update($data);
        $this->recalcularAvance($proyecto);

        return back()->with('ok', 'Avance actualizado.');
    }

    public function eliminarAvance($id, $avanceId)
    {
        $proyecto = Proyecto::findOrFail($id);
        $avance = ProyectoAvance::where('proyecto_id', $proyecto->id)->findOrFail($avanceId);

        if ($avance->foto) {
            Storage::disk('public')->delete($avance->foto);
        }
        $avance->delete();
        $this->recalcularAvance($proyecto);

        return back()->with('ok', 'Avance eliminado.');
    }

    /**
     * Recalcula el % actual del proyecto según el avance más reciente que quede.
     */
    private function recalcularAvance(Proyecto $proyecto): void
    {
        $ultimo = $proyecto->avances()->first(); // ordenado por fecha desc
        $proyecto->update(['avance' => $ultimo ? (int) $ultimo->porcentaje : 0]);
    }

    private function notificarEnProceso(Proyecto $proyecto): void
    {
        try {
            $usuarios = User::where('estado', 1)->get();
            if ($usuarios->isNotEmpty()) {
                Notification::send($usuarios, new NotificacionGenerica(
                    '🏗️ Proyecto en proceso',
                    "El proyecto <strong>{$proyecto->nombre}</strong> ya está en proceso.",
                    'info',
                    'usuario/proyectos',
                    'usuario/proyectos',
                    '<i class="tasks icon"></i>'
                ));
            }
        } catch (\Throwable $e) {
            Log::error('Error notificando proyecto en proceso: '.$e->getMessage());
        }
    }

    private function notificarAvance(Proyecto $proyecto, int $porcentaje, string $comentario): void
    {
        try {
            $usuarios = User::where('estado', 1)->get();
            if ($usuarios->isNotEmpty()) {
                Notification::send($usuarios, new NotificacionGenerica(
                    '🏗️ Avance: '.$proyecto->nombre,
                    "El proyecto <strong>{$proyecto->nombre}</strong> avanzó a {$porcentaje}%. ".e($comentario),
                    'info',
                    'usuario/proyectos',
                    'usuario/proyectos',
                    '<i class="tasks icon"></i>'
                ));
            }
        } catch (\Throwable $e) {
            Log::error('Error notificando avance de proyecto: '.$e->getMessage());
        }
    }

    public function eliminar($id)
    {
        $proyecto = Proyecto::findOrFail($id);
        $proyecto->avances()->delete();
        $proyecto->delete();

        return redirect()->route('admin.proyectos.index')->with('ok', 'Proyecto eliminado.');
    }
}
