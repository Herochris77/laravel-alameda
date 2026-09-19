<?php

namespace App\Http\Controllers\Administrador;

use App\Http\Controllers\Controller;
use App\Models\Detallepago;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AdministradorController extends Controller
{
    public function index()
    {
        return view('administrador.index', [
            'porRevisar' => $this->porRevisar(),
        ]);
    }

    /**
     * Comprobantes subidos que siguen esperando validación.
     *
     * Es el único pendiente del sistema que tiene a alguien esperando del otro
     * lado: el vecino ya pagó, ya subió su comprobante, y hasta que la
     * tesorería lo valide su recibo sigue diciendo "pendiente". Antes había
     * que acordarse de entrar concepto por concepto a buscarlos.
     *
     * Se agrupa por concepto porque así es como se revisan: se abre uno y se
     * validan todos los de esa tanda.
     */
    private function porRevisar(): array
    {
        try {
            $pendientes = Detallepago::with(['pago', 'user'])
                ->where('estado', 'pendiente')
                ->whereNotNull('path_pago')
                ->get()
                ->filter(fn ($d) => $d->pago);

            if ($pendientes->isEmpty()) {
                return ['total' => 0, 'conceptos' => [], 'mas_antiguo' => null];
            }

            $hoy = Carbon::today();

            $conceptos = $pendientes
                ->groupBy('pago_id')
                ->map(function ($grupo) use ($hoy) {
                    $pago = $grupo->first()->pago;

                    // Días que lleva esperando el comprobante más viejo del
                    // grupo: es lo que distingue una tanda recién subida de
                    // una que se quedó olvidada.
                    $espera = $grupo->max(
                        fn ($d) => Carbon::parse($d->updated_at)->startOfDay()->diffInDays($hoy)
                    );

                    return [
                        'pago_id' => $pago->id,
                        'concepto' => $pago->concepto,
                        'cantidad' => (float) $pago->cantidad,
                        'cuantos' => $grupo->count(),
                        'monto' => round($grupo->count() * (float) $pago->cantidad, 2),
                        'espera' => (int) $espera,
                        'vencimiento' => $pago->vencimiento
                            ? Carbon::parse($pago->vencimiento)->format('d/m/Y')
                            : null,
                    ];
                })
                ->sortByDesc('espera')
                ->values();

            return [
                'total' => $pendientes->count(),
                'conceptos' => $conceptos->all(),
                'mas_antiguo' => $conceptos->max('espera'),
            ];
        } catch (\Throwable $e) {
            Log::error('inicio.porRevisar: '.$e->getMessage());

            return ['total' => 0, 'conceptos' => [], 'mas_antiguo' => null];
        }
    }
}
