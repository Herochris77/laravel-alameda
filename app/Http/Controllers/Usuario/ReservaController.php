<?php

namespace App\Http\Controllers\Usuario;

use App\Http\Controllers\Controller;
use App\Models\Reserva;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReservaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('usuario.reserva.index');
    }

    /**
     * Get reservations for a specific date and space.
     */
    public function obtenerReservas(Request $request)
    {
        $request->validate([
            'espacio' => 'required|string',
            'fecha' => 'required|date',
        ]);

        $reservas = Reserva::where('espacio', $request->espacio)
            ->where('fecha_reserva', $request->fecha)
            ->orderBy('hora_inicio', 'asc')
            ->get(['id', 'hora_inicio', 'hora_fin', 'user_id', 'created_at']);

        $reservasFormateadas = $reservas->map(function ($reserva) {
            return [
                'id' => $reserva->id,
                'hora_inicio' => $reserva->hora_inicio,
                'hora_fin' => $reserva->hora_fin,
                'es_propia' => $reserva->user_id === Auth::user()->id,
                'nombre_usuario' => $reserva->user->nombre ?? 'Usuario',
                'casa_usuario' => $reserva->user->casa ?? 'N/A',
                'creado_en' => Carbon::parse($reserva->created_at)->format('d/m/Y H:i'),
            ];
        });

        return response()->json([
            'success' => true,
            'reservas' => $reservasFormateadas,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function crear()
    {
        return view('usuario.reserva.crear');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function guardar(Request $request)
    {
        Log::info('ReservaController@guardar called');
        Log::info('Request data: '.json_encode($request->all()));

        // Validar los datos
        $request->validate([
            'espacio' => 'required|string|max:255',
            'fecha_reserva' => 'required|date',
            'hora_inicio' => 'required|date_format:H:i',
            'hora_fin' => 'required|date_format:H:i|after:hora_inicio',
        ]);

        // Convertir las horas a objetos Carbon para comparar
        $horaInicioStr = $request->hora_inicio;
        $horaFinStr = $request->hora_fin;

        // Asegurar que las horas tienen el formato correcto (agregar :s si es necesario)
        if (strlen($horaInicioStr) === 5) {
            $horaInicioStr .= ':00';
        }
        if (strlen($horaFinStr) === 5) {
            $horaFinStr .= ':00';
        }

        $horaInicio = Carbon::createFromFormat('H:i:s', $horaInicioStr);
        $horaFin = Carbon::createFromFormat('H:i:s', $horaFinStr);

        // Validar que la hora de fin sea después de la hora de inicio
        if ($horaFin->lte($horaInicio)) {
            return response()->json([
                'success' => false,
                'header' => '❌ Error de validación',
                'message' => 'La hora de fin debe ser posterior a la hora de inicio.',
            ], 400);
        }

        try {
            // Usar transacción para evitar condiciones de carrera
            DB::beginTransaction();

            // Verificar si hay algún conflicto de reserva para el mismo espacio y fecha
            $conflicto = Reserva::where('espacio', $request->espacio)
                ->where('fecha_reserva', $request->fecha_reserva)
                ->where(function ($query) use ($horaInicio, $horaFin) {
                    // Condición de solapamiento:
                    // Una reserva existente overlap con la nueva si:
                    //  (existing_start < new_end) AND (existing_end > new_start)
                    $query->where('hora_inicio', '<', $horaFin)
                        ->where('hora_fin', '>', $horaInicio);
                })
                ->exists();

            if ($conflicto) {
                DB::rollBack();

                return response()->json([
                    'success' => false,
                    'header' => '❌ Conflicto de reserva',
                    'message' => 'El espacio ya está reservado para el horario seleccionado. Por favor elija otro horario.',
                ], 409);
            }

            // Crear la reserva
            $reserva = Reserva::create([
                'user_id' => Auth::user()->id,
                'espacio' => $request->espacio,
                'fecha_reserva' => $request->fecha_reserva,
                'hora_inicio' => $request->hora_inicio,
                'hora_fin' => $request->hora_fin,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'header' => '✅ Reserva creada',
                'message' => 'Tu reserva ha sido creada exitosamente.',
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error al crear reserva: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'header' => '❌ Error al crear reserva',
                'message' => 'Ocurrió un error al procesar su reserva. Por favor intente de nuevo.'.$e,
            ], 500);
        }
    }

    /**
     * Display all reservations with optional date filters.
     */
    public function misReservas(Request $request)
    {
        $query = Reserva::with('user');

        if ($request->filled('filtro')) {
            $filtro = $request->filtro;
            $today = Carbon::today();

            if ($filtro === 'hoy') {
                $query->whereDate('fecha_reserva', $today);
            } elseif ($filtro === 'proximos') {
                $query->whereDate('fecha_reserva', '>=', $today)
                    ->orderBy('fecha_reserva', 'asc')
                    ->orderBy('hora_inicio', 'asc');
            } elseif ($filtro === 'pasados') {
                $query->whereDate('fecha_reserva', '<', $today)
                    ->orderBy('fecha_reserva', 'desc')
                    ->orderBy('hora_inicio', 'asc');
            } else {
                $query->orderBy('fecha_reserva', 'desc')
                    ->orderBy('hora_inicio', 'asc');
            }
        } else {
            $query->orderBy('fecha_reserva', 'desc')
                ->orderBy('hora_inicio', 'asc');
        }

        $reservas = $query->get();

        return view('usuario.reserva.mis-reservas', compact('reservas'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function editar($id)
    {
        $reserva = Reserva::findOrFail($id);

        // Verificar que el usuario actual sea el propietario de la reserva
        if ($reserva->user_id !== Auth::user()->id) {
            abort(403, 'No tienes permiso para editar esta reserva.');
        }

        return view('usuario.reserva.editar', compact('reserva'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function actualizar(Request $request, $id)
    {
        $reserva = Reserva::findOrFail($id);

        // Verificar que el usuario actual sea el propietario de la reserva
        if ($reserva->user_id !== Auth::user()->id) {
            return response()->json([
                'success' => false,
                'header' => '❌ Error de autorización',
                'message' => 'No tienes permiso para editar esta reserva.',
            ], 403);
        }

        // Convertir fecha de d/m/yyyy o dd/mm/yyyy a yyyy-mm-dd si es necesario
        $fechaReserva = $request->fecha_reserva;
        if (preg_match('/^\d{1,2}\/\d{1,2}\/\d{4}$/', $fechaReserva)) {
            $fechaReserva = Carbon::createFromFormat('d/m/Y', $fechaReserva)->format('Y-m-d');
        }

        // Convertir las horas a objetos Carbon para comparar
        $horaInicioStr = $request->hora_inicio;
        $horaFinStr = $request->hora_fin;

        // Asegurar que las horas tienen el formato correcto (agregar :s si es necesario)
        if (strlen($horaInicioStr) === 5) {
            $horaInicioStr .= ':00';
        }
        if (strlen($horaFinStr) === 5) {
            $horaFinStr .= ':00';
        }

        $horaInicio = Carbon::createFromFormat('H:i:s', $horaInicioStr);
        $horaFin = Carbon::createFromFormat('H:i:s', $horaFinStr);

        // Validar que la hora de fin sea después de la hora de inicio
        if ($horaFin->lte($horaInicio)) {
            return response()->json([
                'success' => false,
                'header' => '❌ Error de validación',
                'message' => 'La hora de fin debe ser posterior a la hora de inicio.',
            ], 400);
        }

        try {
            DB::beginTransaction();

            // Verificar si hay algún conflicto de reserva para el mismo espacio y fecha, excluyendo la reserva actual
            $conflicto = Reserva::where('id', '!=', $id)
                ->where('espacio', $request->espacio)
                ->where('fecha_reserva', $fechaReserva)
                ->where(function ($query) use ($horaInicio, $horaFin) {
                    $query->where('hora_inicio', '<', $horaFin)
                        ->where('hora_fin', '>', $horaInicio);
                })
                ->exists();

            if ($conflicto) {
                DB::rollBack();

                return response()->json([
                    'success' => false,
                    'header' => '❌ Conflicto de reserva',
                    'message' => 'El espacio ya está reservado para el horario seleccionado por otra reserva. Por favor elija otro horario.',
                ], 409);
            }

            // Actualizar la reserva
            $reserva->update([
                'espacio' => $request->espacio,
                'fecha_reserva' => $fechaReserva,
                'hora_inicio' => $request->hora_inicio,
                'hora_fin' => $request->hora_fin,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'header' => '✅ Reserva actualizada',
                'message' => 'Tu reserva ha sido actualizada exitosamente.',
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error al actualizar reserva: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'header' => '❌ Error al actualizar reserva'.$e,
                'message' => 'Ocurrió un error al procesar su solicitud. Por favor intente de nuevo.',
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function eliminar($id)
    {
        $reserva = Reserva::findOrFail($id);

        // Verificar que el usuario actual sea el propietario de la reserva
        if ($reserva->user_id !== Auth::user()->id) {
            return response()->json([
                'success' => false,
                'header' => '❌ Error de autorización',
                'message' => 'No tienes permiso para eliminar esta reserva.',
            ], 403);
        }

        try {
            $reserva->delete();

            return response()->json([
                'success' => true,
                'header' => '✅ Reserva eliminada',
                'message' => 'Tu reserva ha sido eliminada exitosamente.',
            ]);
        } catch (Exception $e) {
            Log::error('Error al eliminar reserva: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'header' => '❌ Error al eliminar reserva'.$e,
                'message' => 'Ocurrió un error al eliminar su reserva. Por favor intente de nuevo.',
            ], 500);
        }
    }
}
