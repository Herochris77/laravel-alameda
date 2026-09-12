<?php

namespace App\Http\Controllers\Usuario;

use App\Http\Controllers\Controller;
use App\Models\Estacionamiento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class EstacionamientoController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Nos aseguramos de que exista la escalera comunitaria (recurso compartido).
        // Así aparece en la vista sin necesidad de terminal ni comandos.
        $this->asegurarEscalera();

        return view('usuario.estacionamiento.index', compact('user'));
    }

    /**
     * Garantiza que exista el registro de la escalera comunitaria.
     */
    private function asegurarEscalera(): void
    {
        Estacionamiento::firstOrCreate(
            ['ubicacion' => 'escalera'],
            ['nombre' => 'Escalera comunitaria', 'estado' => 'disponible']
        );
    }

    public function inicializarCajones()
    {
        $entradaCount = Estacionamiento::where('ubicacion', 'entrada')->count();
        $centralCount = Estacionamiento::where('ubicacion', 'central')->count();

        for ($i = 1; $i <= max(4 - $entradaCount, 0); $i++) {
            Estacionamiento::create([
                'nombre' => "Cajón " . $i,
                'ubicacion' => 'entrada',
                'estado' => 'disponible',
            ]);
        }

        for ($i = 1; $i <= max(2 - $centralCount, 0); $i++) {
            Estacionamiento::create([
                'nombre' => "Cajón Central " . chr(64 + $i),
                'ubicacion' => 'central',
                'estado' => 'disponible',
            ]);
        }

        $this->asegurarEscalera();

        return response()->json([
            'success' => true,
            'message' => 'Cajones inicializados correctamente',
        ]);
    }

    public function obtenerDisponibilidad(Request $request)
    {
        if ($request->ajax()) {
            $user = auth()->user();
            $esSuperAdmin = $user->rol === 'super-administrador';

            $estacionamientos = Estacionamiento::with('usuario')
                ->orderBy('ubicacion')
                ->orderBy('nombre')
                ->get()
                ->map(function ($e) use ($user) {
                    $estaOcupado = $e->estado === 'ocupado';

                    return [
                        'id' => $e->id,
                        'nombre' => $e->nombre,
                        'ubicacion' => $e->ubicacion,
                        'estado' => $e->estado,
                        'esta_ocupado' => $estaOcupado,
                        'usuario' => $estaOcupado && $e->user_id && $e->usuario ? [
                            'id' => $e->usuario->id,
                            'nombre' => $e->usuario->nombre,
                            'casa' => $e->usuario->casa,
                        ] : null,
                        'es_mio' => $e->user_id === $user->id,
                    ];
                });

            return response()->json([
                'success' => true,
                'es_super_admin' => $esSuperAdmin,
                'estacionamientos' => $estacionamientos,
            ]);
        }
    }

    public function ocuparCajon(Request $request)
    {
        try {
            $request->validate([
                'estacionamiento_id' => 'required|exists:estacionamientos,id',
            ]);

            $user = auth()->user();
            $estacionamiento = Estacionamiento::findOrFail($request->estacionamiento_id);

            $esEscalera = $estacionamiento->ubicacion === 'escalera';
            $recurso = $esEscalera ? 'La '.strtolower($estacionamiento->nombre) : 'El cajón';

            if ($estacionamiento->estado === 'ocupado' && $estacionamiento->user_id !== $user->id) {
                $otroUsuario = $estacionamiento->usuario;
                return response()->json([
                    'success' => false,
                    'header' => $esEscalera ? '❌ Escalera ocupada' : '❌ Cajón ocupado',
                    'message' => "{$recurso} ya está en uso por {$otroUsuario->nombre} #{$otroUsuario->casa}",
                ]);
            }

            if ($estacionamiento->estado === 'ocupado' && $estacionamiento->user_id === $user->id) {
                return response()->json([
                    'success' => false,
                    'header' => '❌ Ya ocupado',
                    'message' => $esEscalera
                        ? 'Ya tienes la escalera marcada como ocupada. Libérala primero.'
                        : 'Ya tienes este cajón ocupado. Libéralo primero.',
                ]);
            }

            $estacionamiento->update([
                'estado' => 'ocupado',
                'user_id' => $user->id,
                'fecha_inicio' => now()->toDateString(),
                'hora_inicio' => now()->format('H:i:s'),
                'fecha_fin' => null,
                'hora_fin' => null,
            ]);

            // Ocupar ya NO genera notificaciones (ni al usuario ni a los demás).
            // El único aviso es el recordatorio del día siguiente (ver CronjobController).

            Log::info("Usuario {$user->id} ocupó {$estacionamiento->ubicacion} {$estacionamiento->id}");

            return response()->json([
                'success' => true,
                'header' => '✅ ' . ($estacionamiento->ubicacion === 'escalera' ? 'Escalera ocupada' : 'Cajón ocupado'),
                'message' => $estacionamiento->ubicacion === 'escalera'
                    ? "Has marcado la {$estacionamiento->nombre} como ocupada."
                    : "Has ocupado el cajón {$estacionamiento->nombre}",
            ]);
        } catch (\Exception $e) {
            Log::error('Error al ocupar cajón: ' . $e->getMessage());
            return response()->json([
                'header' => '❌ Error',
                'message' => 'Error al ocupar el cajón.',
            ]);
        }
    }

    public function liberarCajon(Request $request)
    {
        try {
            $request->validate([
                'estacionamiento_id' => 'required|exists:estacionamientos,id',
            ]);

            $user = auth()->user();
            $estacionamiento = Estacionamiento::findOrFail($request->estacionamiento_id);

            $esEscalera = $estacionamiento->ubicacion === 'escalera';
            $esSuperAdmin = $user->rol === 'super-administrador';
            $esDeOtro = $estacionamiento->user_id !== $user->id;

            // El dueño del lugar lo libera; el super-administrador puede liberar
            // cualquier lugar (cuando un usuario lo desocupó físicamente pero no
            // lo marcó como libre en el sistema).
            if ($esDeOtro && ! $esSuperAdmin) {
                return response()->json([
                    'success' => false,
                    'header' => '❌ Sin permiso',
                    'message' => $esEscalera
                        ? 'No puedes liberar la escalera porque no la ocupaste tú.'
                        : 'No puedes liberar un cajón que no has ocupado.',
                ]);
            }

            $ocupante = $estacionamiento->usuario;

            $estacionamiento->update([
                'estado' => 'disponible',
                'user_id' => null,
                'fecha_inicio' => null,
                'hora_inicio' => null,
                'fecha_fin' => null,
                'hora_fin' => null,
            ]);

            // Liberar ya NO genera notificaciones (ni al usuario ni a los demás).

            if ($esDeOtro && $esSuperAdmin) {
                Log::info("Super-admin {$user->id} liberó {$estacionamiento->ubicacion} {$estacionamiento->id} que ocupaba el usuario ".($ocupante->id ?? 'N/A'));

                return response()->json([
                    'success' => true,
                    'header' => '✅ Lugar desocupado',
                    'message' => $ocupante
                        ? "Liberaste {$estacionamiento->nombre} (lo ocupaba {$ocupante->nombre} #{$ocupante->casa})."
                        : "{$estacionamiento->nombre} ha sido marcado como disponible.",
                ]);
            }

            Log::info("Usuario {$user->id} liberó {$estacionamiento->ubicacion} {$estacionamiento->id}");

            return response()->json([
                'success' => true,
                'header' => $esEscalera ? '✅ Escalera liberada' : '✅ Cajón liberado',
                'message' => $esEscalera
                    ? 'La escalera comunitaria ha sido marcada como disponible.'
                    : 'El cajón ha sido marcado como disponible.',
            ]);
        } catch (\Exception $e) {
            Log::error('Error al liberar cajón: ' . $e->getMessage());
            return response()->json([
                'header' => '❌ Error',
                'message' => 'Error al liberar el cajón.',
            ]);
        }
    }
}