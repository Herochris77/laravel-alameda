<?php

namespace App\Services;

use App\Models\Detallepago;
use App\Models\Sancion;
use App\Models\SaldoMovimiento;
use App\Models\User;
use Carbon\Carbon;

/**
 * Estado de cuenta de una vivienda.
 *
 * Reúne en un solo lugar lo que hoy vive repartido: los recibos, lo que se
 * pagó de cada uno, los recargos por mora, el saldo a favor y las multas.
 * Sirve para responder un "yo ya pagué" sin armar la cuenta a mano, y para
 * extender la constancia de no adeudo cuando alguien vende o renta.
 *
 * Criterio: FLUJO DE EFECTIVO, el mismo del reporte mensual, para que las dos
 * cosas no se contradigan. Un recibo cubierto con saldo a favor cuenta como
 * pagado, pero se distingue, porque ahí no entró dinero nuevo a la cuenta.
 */
class EstadoCuentaService
{
    public function __construct(private SaldoService $saldos) {}

    public function datos(int $userId): array
    {
        $usuario = User::withTrashed()->findOrFail($userId);

        $cargos = $this->cargos($usuario);
        $multas = $this->multas($usuario);

        $totales = $this->totales($cargos, $multas);
        $saldoFavor = round($this->saldos->saldo($usuario->id), 2);

        return [
            'usuario' => $usuario,
            'generado' => Carbon::now(),

            'cargos' => $cargos,
            'multas' => $multas,
            'movimientos_saldo' => $this->movimientosSaldo($usuario),

            'totales' => $totales,
            'saldo_favor' => $saldoFavor,
            'firmas' => $this->firmas(),

            // El neto es lo que de verdad importa: positivo significa que la
            // vivienda debe; negativo, que tiene dinero a su favor.
            'neto' => round($totales['pendiente'] - $saldoFavor, 2),
            'al_corriente' => $totales['pendiente'] <= 0.009,
        ];
    }

    /**
     * Quiénes firman la constancia, tomado de los cargos de la mesa.
     *
     * Si un cargo no está asignado, el renglón sale en blanco para firmarse a
     * mano: es preferible a imprimir un nombre equivocado en un documento que
     * se entrega a terceros.
     */
    private function firmas(): array
    {
        $porCargo = function (string $cargo) {
            $u = User::whereIn('rol', ['administrador', 'super-administrador'])
                ->where('cargo', $cargo)
                ->where('estado', 1)
                ->first();

            return $u ? $u->nombre : '';
        };

        return [
            'tesorero' => $porCargo('tesorero'),
            'presidente' => $porCargo('presidente'),
        ];
    }

    /**
     * Un renglón por recibo, con lo que se esperaba y lo que se pagó.
     */
    private function cargos(User $usuario): array
    {
        return Detallepago::with('pago')
            ->where('user_id', $usuario->id)
            ->get()
            ->filter(fn ($d) => $d->pago)
            ->sortBy(fn ($d) => $d->pago->vencimiento ?? '')
            ->map(function ($d) {
                $desglose = $d->desglosePago();
                $vence = $d->pago->vencimiento ? Carbon::parse($d->pago->vencimiento) : null;
                $fechaPago = $d->fechaEfectivaPago();

                return [
                    'concepto' => $d->pago->concepto,
                    'vencimiento' => $vence,
                    'cuota' => $desglose['cuota'],
                    'recargo' => $desglose['recargo'],
                    'esperado' => $desglose['esperado'],
                    'pagado' => $d->estado === 'pagado' ? $desglose['esperado'] : 0.0,
                    'estado' => $d->estado,
                    'fecha_pago' => $fechaPago,
                    'con_saldo' => $this->liquidadoConSaldo($d->id),
                    'tarde' => $this->fuePagoTardio($d, $vence, $fechaPago),
                ];
            })
            ->values()
            ->all();
    }

    /**
     * ¿Este recibo se liquidó con saldo a favor en vez de con dinero nuevo?
     */
    private function liquidadoConSaldo(int $detallepagoId): bool
    {
        return SaldoMovimiento::where('detallepago_id', $detallepagoId)
            ->where('tipo', SaldoMovimiento::TIPO_APLICACION)
            ->exists();
    }

    /**
     * Tarde se mide contra la fecha REAL de la transferencia.
     *
     * Quien transfirió a tiempo y subió el comprobante días después no pagó
     * tarde, aunque el sistema se haya enterado después.
     */
    private function fuePagoTardio($detalle, ?Carbon $vence, ?Carbon $fechaPago): bool
    {
        if (! $vence || $detalle->estado !== 'pagado' || ! $fechaPago) {
            return false;
        }

        return $fechaPago->gt($vence);
    }

    private function multas(User $usuario): array
    {
        return Sancion::where('user_id', $usuario->id)
            ->orderBy('created_at')
            ->get()
            ->map(fn ($s) => [
                'motivo' => $s->motivo,
                'monto' => round((float) $s->monto, 2),
                'estado' => $s->estado,
                'fecha' => $s->created_at ? Carbon::parse($s->created_at) : null,
                'fecha_pago' => $s->fecha_pago ? Carbon::parse($s->fecha_pago) : null,
                'pagada' => $s->estado === 'pagado',
            ])
            ->all();
    }

    private function movimientosSaldo(User $usuario): array
    {
        return SaldoMovimiento::where('user_id', $usuario->id)
            ->orderBy('created_at')
            ->get()
            ->map(fn ($m) => [
                'fecha' => Carbon::parse($m->created_at),
                'tipo' => $m->tipo,
                'monto' => round((float) $m->monto, 2),
                'descripcion' => $m->descripcion,
                // Un abono suma al saldo del vecino; una aplicación lo gasta.
                'suma' => $m->tipo !== SaldoMovimiento::TIPO_APLICACION,
            ])
            ->all();
    }

    private function totales(array $cargos, array $multas): array
    {
        $col = collect($cargos);
        $pagados = $col->where('estado', 'pagado');

        $conSaldo = $pagados->where('con_saldo', true)->sum('esperado');

        $multasPendientes = collect($multas)->where('pagada', false)->sum('monto');

        return [
            'cargado' => round($col->sum('esperado'), 2),
            'recargos' => round($col->sum('recargo'), 2),
            'pagado' => round($pagados->sum('esperado'), 2),
            // De lo pagado, cuánto fue dinero nuevo y cuánto adelanto previo.
            'con_saldo' => round($conSaldo, 2),
            'en_efectivo' => round($pagados->sum('esperado') - $conSaldo, 2),
            'multas' => round(collect($multas)->sum('monto'), 2),
            'multas_pendientes' => round($multasPendientes, 2),
            'pendiente' => round(
                $col->where('estado', '!=', 'pagado')->sum('esperado') + $multasPendientes,
                2
            ),
            'pagos_tardios' => $pagados->where('tarde', true)->count(),
        ];
    }

    /**
     * Resumen de todas las viviendas, para la pantalla de tesorería.
     */
    public function resumenGeneral(): array
    {
        return User::where('pago', 1)
            ->where('estado', 1)
            ->orderByRaw('CAST(casa AS UNSIGNED)')
            ->get()
            ->map(function ($u) {
                $d = $this->datos($u->id);

                return [
                    'id' => $u->id,
                    'casa' => $u->casa,
                    'nombre' => $u->nombre,
                    'pendiente' => $d['totales']['pendiente'],
                    'saldo_favor' => $d['saldo_favor'],
                    'neto' => $d['neto'],
                    'al_corriente' => $d['al_corriente'],
                    'pagos_tardios' => $d['totales']['pagos_tardios'],
                ];
            })
            ->all();
    }
}
