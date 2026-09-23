<?php

namespace App\Http\Controllers\Administrador;

use App\Http\Controllers\Controller;
use App\Models\Asamblea;
use App\Models\Detallepago;
use App\Models\Encuesta;
use App\Models\Estacionamiento;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function index()
    {
        return view('administrador.dashboard');
    }

    /**
     * KPIs del comité. Todo va protegido para que nunca rompa la vista.
     */
    public function datos()
    {
        return response()->json([
            'success' => true,
            'cobranza' => $this->cobranza(),
            'casas' => $this->casas(),
            'deudores' => $this->deudores(),
            'participacion' => $this->participacion(),
            'recaudacion_6m' => $this->recaudacion6meses(),
            'pendientes' => $this->pendientes(),
            'actualizado' => now()->format('H:i'),
        ]);
    }

    private function casaPorUsuario(): array
    {
        return User::withTrashed()->pluck('casa', 'id')->toArray();
    }

    private function totalCasas(): int
    {
        return (int) User::where('tipo', 'dueño')->whereNotNull('casa')->distinct()->count('casa');
    }

    /**
     * Cobranza del mes: criterio DEVENGADO.
     *
     * Mide qué tanto de lo que VENCE este mes ya está cobrado, así que aquí
     * el filtro por `vencimiento` es el correcto y no debe cambiarse por
     * `fecha_pago`. Es la contraparte de recaudacion6meses(), que sí mide
     * flujo de efectivo. Los dos números responden preguntas distintas y no
     * tienen por qué coincidir.
     */
    private function cobranza(): array
    {
        try {
            $ini = Carbon::now()->startOfMonth();
            $fin = Carbon::now()->endOfMonth();
            $detalles = Detallepago::with('pago')->get();

            $esperado = 0; $recaudado = 0;
            foreach ($detalles as $d) {
                if (! $d->pago || ! $d->pago->vencimiento) {
                    continue;
                }
                $venc = Carbon::parse($d->pago->vencimiento);
                if ($venc->between($ini, $fin)) {
                    $monto = (float) $d->pago->cantidad;
                    $esperado += $monto;
                    if ($d->estado === 'pagado') {
                        $recaudado += $d->cantidad_pago ? (float) $d->cantidad_pago : $monto;
                    }
                }
            }

            return [
                'recaudado' => round($recaudado, 2),
                'esperado' => round($esperado, 2),
                'pct' => $esperado > 0 ? round(($recaudado / $esperado) * 100) : 0,
            ];
        } catch (\Throwable $e) {
            Log::error('dashboard.cobranza: '.$e->getMessage());

            return ['recaudado' => 0, 'esperado' => 0, 'pct' => 0];
        }
    }

    /**
     * Quién debe y cuánto, con nombre y casa.
     *
     * Va SOLO en el tablero de la mesa directiva provisional. En el módulo de
     * transparencia, que ven todos los vecinos, se queda el agregado sin
     * nombres: el Aviso de Privacidad dice que los adeudos por vivienda los
     * ve únicamente la mesa, y publicar la lista lo incumpliría.
     */
    private function deudores(): array
    {
        try {
            $servicio = app(\App\Services\EstadoCuentaService::class);
            $hoy = Carbon::now()->startOfDay();

            $conAdeudo = collect($servicio->resumenGeneral())
                ->where('al_corriente', false)
                ->sortByDesc('pendiente')
                ->values();

            // Días de atraso del recibo vencido más antiguo de cada vivienda:
            // es lo que distingue un despiste de este mes de un rezago serio.
            $atrasoPorUsuario = [];

            foreach (Detallepago::with('pago')->where('estado', '!=', 'pagado')->get() as $d) {
                if (! $d->pago || ! $d->pago->vencimiento) {
                    continue;
                }

                $vence = Carbon::parse($d->pago->vencimiento)->startOfDay();

                if ($vence->gte($hoy)) {
                    continue;
                }

                $dias = $vence->diffInDays($hoy);

                $atrasoPorUsuario[$d->user_id] = max($atrasoPorUsuario[$d->user_id] ?? 0, $dias);
            }

            return [
                'total' => $conAdeudo->count(),
                'monto' => round($conAdeudo->sum('pendiente'), 2),
                'lista' => $conAdeudo->take(8)->map(fn ($v) => [
                    'id' => $v['id'],
                    'casa' => $v['casa'],
                    'nombre' => $v['nombre'],
                    'pendiente' => $v['pendiente'],
                    'dias' => $atrasoPorUsuario[$v['id']] ?? 0,
                ])->all(),
            ];
        } catch (\Throwable $e) {
            Log::error('dashboard.deudores: '.$e->getMessage());

            return ['total' => 0, 'monto' => 0, 'lista' => []];
        }
    }

    private function casas(): array
    {
        try {
            $total = $this->totalCasas();
            $mapa = $this->casaPorUsuario();
            $hoy = Carbon::now()->startOfDay();

            $atrasadas = [];
            $carteraVencida = 0;

            $pendientes = Detallepago::with('pago')->where('estado', 'pendiente')->get();
            foreach ($pendientes as $d) {
                if (! $d->pago || ! $d->pago->vencimiento) {
                    continue;
                }
                if (Carbon::parse($d->pago->vencimiento)->lt($hoy)) {
                    $casa = $mapa[$d->user_id] ?? null;
                    if ($casa) {
                        $atrasadas[$casa] = true;
                    }
                    $carteraVencida += (float) $d->pago->cantidad;
                }
            }

            $numAtrasadas = count($atrasadas);

            return [
                'total' => $total,
                'atrasadas' => $numAtrasadas,
                'al_corriente' => max($total - $numAtrasadas, 0),
                'cartera_vencida' => round($carteraVencida, 2),
            ];
        } catch (\Throwable $e) {
            Log::error('dashboard.casas: '.$e->getMessage());

            return ['total' => 0, 'atrasadas' => 0, 'al_corriente' => 0, 'cartera_vencida' => 0];
        }
    }

    private function participacion(): array
    {
        try {
            $encuesta = Encuesta::withCount(['respuestas'])->latest()->first();
            $totalUsuarios = User::where('estado', 1)->count();

            if (! $encuesta) {
                return ['titulo' => null, 'votantes' => 0, 'total' => $totalUsuarios, 'pct' => 0];
            }

            $votantes = $encuesta->respuestas()->distinct('user_id')->count('user_id');

            return [
                'titulo' => $encuesta->titulo,
                'votantes' => $votantes,
                'total' => $totalUsuarios,
                'pct' => $totalUsuarios > 0 ? round(($votantes / $totalUsuarios) * 100) : 0,
            ];
        } catch (\Throwable $e) {
            Log::error('dashboard.participacion: '.$e->getMessage());

            return ['titulo' => null, 'votantes' => 0, 'total' => 0, 'pct' => 0];
        }
    }

    private function recaudacion6meses(): array
    {
        try {
            // Recaudación es dinero que entró: un recibo liquidado con saldo a
            // favor no lo es, el ingreso se registró cuando llegó ese dinero.
            $detalles = Detallepago::with('pago')
                ->where('estado', 'pagado')
                ->sinLiquidacionesConSaldo()
                ->get();
            $meses = [];
            for ($i = 5; $i >= 0; $i--) {
                $m = Carbon::now()->subMonths($i);
                $meses[$m->format('Y-m')] = ['label' => $m->translatedFormat('M'), 'total' => 0];
            }
            foreach ($detalles as $d) {
                if (! $d->pago) {
                    continue;
                }

                // Recaudación es FLUJO DE EFECTIVO: el mes en que entró el
                // dinero, no el mes al que corresponde la cuota. Antes se
                // agrupaba por vencimiento, así que un pago atrasado (o
                // adelantado) se contaba en un mes en el que no hubo ingreso.
                $fecha = $d->fecha_pago
                    ? Carbon::parse($d->fecha_pago)
                    : Carbon::parse($d->updated_at);

                $key = $fecha->format('Y-m');

                if (isset($meses[$key])) {
                    $meses[$key]['total'] += $d->cantidad_pago ? (float) $d->cantidad_pago : (float) $d->pago->cantidad;
                }
            }

            return array_values($meses);
        } catch (\Throwable $e) {
            Log::error('dashboard.recaudacion6m: '.$e->getMessage());

            return [];
        }
    }

    private function pendientes(): array
    {
        $out = [];
        try {
            $comprobantes = Detallepago::where('estado', 'pendiente')->whereNotNull('path_pago')->count();
            if ($comprobantes > 0) {
                $out[] = ['icono' => 'money bill wave', 'texto' => "{$comprobantes} comprobante(s) de pago por revisar", 'nota' => 'pendiente'];
            }

            $asamblea = Asamblea::whereIn('estado', ['convocada', 'en_curso'])
                ->orderBy('fecha')->first();
            if ($asamblea) {
                $cuando = $asamblea->fecha ? Carbon::parse($asamblea->fecha)->diffForHumans() : 'programada';
                $out[] = ['icono' => 'gavel', 'texto' => 'Asamblea: '.$asamblea->titulo, 'nota' => $cuando];
            }

            $escalera = Estacionamiento::where('ubicacion', 'escalera')->where('estado', 'ocupado')->first();
            if ($escalera) {
                $out[] = ['icono' => 'sitemap', 'texto' => 'Escalera comunitaria en uso', 'nota' => 'sin liberar'];
            }

            $ocupados = Estacionamiento::where('ubicacion', '!=', 'escalera')->where('estado', 'ocupado')->count();
            $out[] = ['icono' => 'car', 'texto' => "{$ocupados} cajón(es) de estacionamiento en uso", 'nota' => 'ahora'];
        } catch (\Throwable $e) {
            Log::error('dashboard.pendientes: '.$e->getMessage());
        }

        return $out;
    }
}
