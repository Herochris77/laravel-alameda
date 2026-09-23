<?php

namespace App\Http\Controllers\Administrador;

use App\Http\Controllers\Controller;
use App\Models\Detallepago;
use App\Models\Estacionamiento;
use App\Models\ExpedienteNota;
use App\Models\Mascota;
use App\Models\Sancion;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ExpedienteController extends Controller
{
    /**
     * Cuadrícula de casas con su estado (al corriente / adeudo / sanción).
     */
    public function index()
    {
        $casaPorUser = User::withTrashed()->pluck('casa', 'id')->toArray();
        $hoy = Carbon::now()->startOfDay();

        $casasAdeudo = [];
        foreach (Detallepago::with('pago')->where('estado', 'pendiente')->get() as $d) {
            if ($d->pago && $d->pago->vencimiento && Carbon::parse($d->pago->vencimiento)->lt($hoy)) {
                $casa = $casaPorUser[$d->user_id] ?? null;
                if ($casa) {
                    $casasAdeudo[$casa] = true;
                }
            }
        }

        $casasSancion = [];
        foreach (Sancion::where('estado', 'pendiente')->get() as $s) {
            $casa = $casaPorUser[$s->user_id] ?? null;
            if ($casa) {
                $casasSancion[$casa] = true;
            }
        }

        $personasPorCasa = User::whereNotNull('casa')->get()->groupBy('casa');

        $casas = User::where('tipo', 'dueño')->whereNotNull('casa')->orderBy('casa')->get()
            ->map(function ($d) use ($casasAdeudo, $casasSancion, $personasPorCasa) {
                $casa = $d->casa;
                $estado = isset($casasAdeudo[$casa]) ? 'adeudo' : (isset($casasSancion[$casa]) ? 'sancion' : 'al_corriente');

                return [
                    'casa' => $casa,
                    'dueno' => $d->nombre,
                    'personas' => isset($personasPorCasa[$casa]) ? $personasPorCasa[$casa]->count() : 1,
                    'estado' => $estado,
                ];
            })
            ->sortBy(fn ($c) => (int) $c['casa'])
            ->values();

        return view('administrador.expediente.index', compact('casas'));
    }

    /**
     * Ficha 360 de una casa.
     */
    public function ficha($casa)
    {
        $personas = User::where('casa', $casa)->get();
        $ids = $personas->pluck('id')->all();

        if (empty($ids)) {
            abort(404, 'Casa no encontrada.');
        }

        $dueno = $personas->firstWhere('tipo', 'dueño');

        // Finanzas
        $detalles = Detallepago::with('pago')->whereIn('user_id', $ids)->orderByDesc('created_at')->get();
        $adeudo = 0;
        $recibos = $detalles->map(function ($d) use (&$adeudo) {
            $monto = floatval($d->cantidad_pago ?: ($d->pago ? $d->pago->cantidad : 0));
            if ($d->estado === 'pendiente') {
                $adeudo += floatval($d->pago ? $d->pago->cantidad : $monto);
            }

            return [
                'concepto' => $d->pago->concepto ?? 'Recibo',
                'estado' => $d->estado,
                'monto' => $monto,
                'fecha' => Carbon::parse($d->updated_at)->format('d/m/Y'),
            ];
        });

        $mascotas = Mascota::whereIn('user_id', $ids)->get();
        $sanciones = Sancion::whereIn('user_id', $ids)->orderByDesc('created_at')->get();
        $cajones = Estacionamiento::whereIn('user_id', $ids)->where('estado', 'ocupado')->get();
        $notas = ExpedienteNota::where('casa', $casa)->orderByDesc('created_at')->get();

        // Actividad (línea de tiempo simple)
        $actividad = collect();
        foreach ($detalles->take(10) as $d) {
            $actividad->push([
                'fecha' => Carbon::parse($d->updated_at),
                'tipo' => $d->estado === 'pagado' ? 'pago' : 'adeudo',
                'texto' => ($d->estado === 'pagado' ? 'Pagó ' : 'Recibo pendiente: ').($d->pago->concepto ?? 'recibo'),
            ]);
        }
        foreach ($sanciones->take(10) as $s) {
            $actividad->push([
                'fecha' => Carbon::parse($s->created_at),
                'tipo' => 'sancion',
                'texto' => 'Sanción: '.$s->motivo.' ('.$s->estado.')',
            ]);
        }
        foreach ($notas as $n) {
            $actividad->push([
                'fecha' => Carbon::parse($n->created_at),
                'tipo' => 'nota',
                'texto' => 'Nota: '.$n->nota,
            ]);
        }
        $actividad = $actividad->sortByDesc('fecha')->take(12)->values();

        return view('administrador.expediente.ficha', compact(
            'casa', 'personas', 'dueno', 'recibos', 'adeudo',
            'mascotas', 'sanciones', 'cajones', 'notas', 'actividad'
        ));
    }

    public function guardarNota(Request $request, $casa)
    {
        $request->validate(['nota' => 'required|string|max:1000']);

        ExpedienteNota::create([
            'casa' => $casa,
            'nota' => $request->nota,
            'autor' => auth()->user()->nombre ?? 'Comité',
        ]);

        return back()->with('ok', 'Nota agregada al expediente.');
    }
}
