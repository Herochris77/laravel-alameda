<?php

namespace App\Http\Controllers\Administrador;

use App\Http\Controllers\Controller;
use App\Models\Proyecto;
use App\Models\ProyectoAvance;
use App\Models\ProyectoAvanceFoto;
use App\Models\User;
use App\Notifications\NotificacionGenerica;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;

class ProyectoController extends Controller
{
    /** Fotos por envío. Cada una llega ya reducida desde el navegador, así el total cabe en el límite de subida del hosting. */
    private const MAX_FOTOS = 8;

    private function reglasFotos(): array
    {
        return [
            'fotos' => 'nullable|array|max:'.self::MAX_FOTOS,
            'fotos.*' => 'image|mimes:jpg,jpeg,png,webp|max:4096',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',   // formulario anterior, una sola foto
        ];
    }

    /** Guarda las fotos recibidas (campo `fotos[]` y el antiguo `foto`) como evidencia del avance. */
    private function guardarFotos(Request $request, ProyectoAvance $avance): int
    {
        $archivos = [];

        foreach ((array) $request->file('fotos', []) as $archivo) {
            if ($archivo && $archivo->isValid()) {
                $archivos[] = $archivo;
            }
        }
        if ($request->hasFile('foto') && $request->file('foto')->isValid()) {
            $archivos[] = $request->file('foto');
        }

        foreach ($archivos as $archivo) {
            ProyectoAvanceFoto::create([
                'proyecto_avance_id' => $avance->id,
                'ruta' => $archivo->store('proyectos/avances', 'public'),
            ]);
        }

        return count($archivos);
    }

    /** Borra del disco y de la base las fotos de un avance (la antigua y las nuevas). */
    private function borrarFotosDe(ProyectoAvance $avance): void
    {
        if ($avance->foto) {
            Storage::disk('public')->delete($avance->foto);
        }

        if (ProyectoAvance::tablaDeFotos()) {
            foreach ($avance->fotos as $foto) {
                Storage::disk('public')->delete($foto->ruta);
                $foto->delete();
            }
        }
    }

    /** Ajusta porcentaje y estado del proyecto tras publicar un avance (respeta "pausado"). */
    private function aplicarAvance(Proyecto $proyecto, int $porcentaje): void
    {
        $nuevoEstado = $proyecto->estado;
        if ($proyecto->estado !== 'pausado') {
            if ($porcentaje >= 100) {
                $nuevoEstado = 'terminado';
            } elseif ($porcentaje > 0 && $proyecto->estado === 'por_iniciar') {
                $nuevoEstado = 'en_proceso';
            }
        }
        $proyecto->update(['avance' => $porcentaje, 'estado' => $nuevoEstado]);
    }
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
            'comentario_avance' => 'nullable|string|max:1000',
        ] + $this->reglasFotos());

        $proyecto = Proyecto::create([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'costo' => $request->filled('costo') ? $request->costo : null,
            'estado' => $request->estado,
            'avance' => $request->estado === 'terminado' ? 100 : 0,
            'created_by' => auth()->user()->id,
        ]);

        // Un proyecto que se ejecutó de una sola vez llega con su evidencia: se guarda
        // como el primer avance (100% si ya está terminado) para que los vecinos la vean.
        $trajoFotos = $request->hasFile('fotos') || $request->hasFile('foto');
        if ($trajoFotos || $request->filled('comentario_avance')) {
            $porcentaje = $proyecto->estado === 'terminado' ? 100 : 0;
            $avance = ProyectoAvance::create([
                'proyecto_id' => $proyecto->id,
                'porcentaje' => $porcentaje,
                'comentario' => $request->filled('comentario_avance')
                    ? $request->comentario_avance
                    : ($porcentaje === 100 ? 'Proyecto concluido.' : 'Evidencia del proyecto.'),
                'autor' => auth()->user()->nombre ?? 'Comité',
            ]);
            $this->guardarFotos($request, $avance);
        }

        // Si nace "en proceso", se avisa a los vecinos.
        if ($proyecto->estado === 'en_proceso') {
            $this->notificarEnProceso($proyecto);
        } elseif ($proyecto->estado === 'terminado') {
            $this->notificarAvance($proyecto, 100, $request->filled('comentario_avance') ? $request->comentario_avance : 'Proyecto concluido.');
        }

        return redirect()->route('admin.proyectos.index')->with('ok', 'Proyecto creado.');
    }

    public function ver($id)
    {
        $proyecto = Proyecto::with(ProyectoAvance::tablaDeFotos() ? ['avances.fotos'] : ['avances'])->findOrFail($id);
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
        ] + $this->reglasFotos());

        $proyecto = Proyecto::findOrFail($id);
        $porcentaje = (int) $request->porcentaje;

        $avance = ProyectoAvance::create([
            'proyecto_id' => $proyecto->id,
            'porcentaje' => $porcentaje,
            'comentario' => $request->comentario,
            'autor' => auth()->user()->nombre ?? 'Comité',
        ]);
        $this->guardarFotos($request, $avance);

        // Actualiza el avance y ajusta el estado (respetando "pausado").
        $this->aplicarAvance($proyecto, $porcentaje);

        $this->notificarAvance($proyecto, $porcentaje, $request->comentario);

        return back()->with('ok', 'Avance publicado y notificado a los vecinos.');
    }

    public function editarAvance(Request $request, $id, $avanceId)
    {
        $request->validate([
            'porcentaje' => 'required|integer|min:0|max:100',
            'comentario' => 'required|string|max:1000',
            'quitar_fotos' => 'nullable|array',
            'quitar_fotos.*' => 'integer',
        ] + $this->reglasFotos());

        $proyecto = Proyecto::findOrFail($id);
        $avance = ProyectoAvance::where('proyecto_id', $proyecto->id)->findOrFail($avanceId);

        $data = [
            'porcentaje' => (int) $request->porcentaje,
            'comentario' => $request->comentario,
        ];

        // Foto de siempre (columna `foto`): se puede quitar.
        if ($request->boolean('quitar_foto') && $avance->foto) {
            Storage::disk('public')->delete($avance->foto);
            $data['foto'] = null;
        }

        // Fotos individuales marcadas para quitar (solo de ESTE avance).
        if (ProyectoAvance::tablaDeFotos() && $request->filled('quitar_fotos')) {
            $avance->fotos()->whereIn('id', $request->quitar_fotos)->get()->each(function ($foto) {
                Storage::disk('public')->delete($foto->ruta);
                $foto->delete();
            });
        }

        // Fotos nuevas: se SUMAN a las que ya tiene.
        $this->guardarFotos($request, $avance);

        $avance->update($data);
        $this->recalcularAvance($proyecto);

        return back()->with('ok', 'Avance actualizado.');
    }

    public function eliminarAvance($id, $avanceId)
    {
        $proyecto = Proyecto::findOrFail($id);
        $avance = ProyectoAvance::where('proyecto_id', $proyecto->id)->findOrFail($avanceId);

        $this->borrarFotosDe($avance);
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
        $proyecto->avances()->get()->each(function (ProyectoAvance $avance) {
            $this->borrarFotosDe($avance);
            $avance->delete();
        });
        $proyecto->delete();

        return redirect()->route('admin.proyectos.index')->with('ok', 'Proyecto eliminado.');
    }
}
