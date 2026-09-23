<?php

namespace App\Services;

use App\Models\Detallepago;
use App\Models\SaldoMovimiento;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Saldo a favor de los vecinos.
 *
 * Existe porque en el condominio es común pagar varias cuotas de golpe. Antes
 * ese dinero quedaba como un `cantidad_pago` mayor que la cuota, sin rastro de
 * a qué meses correspondía: el recibo del mes siguiente seguía apareciendo
 * pendiente aunque el vecino ya lo hubiera cubierto.
 *
 * Toda la lógica vive aquí para que los controladores no la dupliquen y para
 * que haya un único lugar donde auditar cómo se mueve el dinero a favor.
 */
class SaldoService
{
    /**
     * Saldo disponible de un vecino.
     */
    public function saldo(int $userId): float
    {
        return SaldoMovimiento::saldoDe($userId);
    }

    /**
     * Registra como saldo a favor lo que un pago excede de su cuota.
     *
     * Se llama al validar un pago. Es idempotente por recibo: si ese mismo
     * detallepago ya generó un abono, ajusta la diferencia en vez de duplicar,
     * de modo que corregir el monto dos veces no infle el saldo.
     */
    /**
     * Fecha a partir de la cual este módulo se hace cargo del dinero.
     *
     * Todo lo cobrado antes pertenece a la gestión anterior y ya está
     * entregado y cuadrado: reclasificarlo como saldo a favor cambiaría
     * cifras que la mesa directiva provisional ya dio por buenas.
     *
     * Se define con SALDOS_DESDE en el .env (formato Y-m-d). Sin esa
     * variable el módulo opera sobre todo el histórico.
     */
    public function fechaCorte(): ?\Carbon\Carbon
    {
        $valor = env('SALDOS_DESDE');

        return $valor ? \Carbon\Carbon::parse($valor)->startOfDay() : null;
    }

    /**
     * ¿Este recibo pertenece a la gestión anterior?
     *
     * Se juzga por el vencimiento del concepto y no por la fecha de pago:
     * lo que define de qué gestión es un recibo es el periodo que cubre,
     * no cuándo se capturó.
     */
    private function esDeGestionAnterior(Detallepago $detalle): bool
    {
        $corte = $this->fechaCorte();

        if (! $corte || ! $detalle->pago || ! $detalle->pago->vencimiento) {
            return false;
        }

        return \Carbon\Carbon::parse($detalle->pago->vencimiento)->startOfDay()->lt($corte);
    }

    public function registrarExcedente(Detallepago $detalle): ?SaldoMovimiento
    {
        if ($detalle->estado !== 'pagado' || ! $detalle->pago) {
            return null;
        }

        // Revalidar un recibo viejo no debe crear saldo a favor: en esa época
        // el excedente solía ser el recargo por mora, que es ingreso del
        // condominio y no crédito del vecino.
        if ($this->esDeGestionAnterior($detalle)) {
            return null;
        }

        // El excedente se mide contra lo que REALMENTE debía pagar, cuota más
        // el recargo por mora que le corresponda según la fecha de su
        // transferencia. Sin esto, el recargo del 10 % se le acreditaría al
        // vecino como saldo a favor siendo que es ingreso del condominio.
        $desglose = $detalle->desglosePago();
        $excedente = $desglose['excedente'];

        // Lo ya reconocido antes por este mismo recibo.
        $yaAbonado = round(
            (float) SaldoMovimiento::where('detallepago_id', $detalle->id)
                ->where('tipo', SaldoMovimiento::TIPO_ABONO)
                ->sum('monto'),
            2
        );

        $diferencia = round($excedente - $yaAbonado, 2);

        if ($diferencia == 0.0) {
            return null;
        }

        // Si el excedente bajó (tesorería corrigió el monto a la baja) se
        // registra un ajuste negativo en lugar de borrar el abono original,
        // para no perder el historial.
        if ($diferencia < 0) {
            return SaldoMovimiento::create([
                'user_id' => $detalle->user_id,
                'tipo' => SaldoMovimiento::TIPO_AJUSTE,
                'monto' => $diferencia,
                'detallepago_id' => $detalle->id,
                'descripcion' => 'Corrección del excedente de "'.$detalle->pago->concepto.'"',
                'created_by' => optional(Auth::user())->id,
            ]);
        }

        return SaldoMovimiento::create([
            'user_id' => $detalle->user_id,
            'tipo' => SaldoMovimiento::TIPO_ABONO,
            'monto' => $diferencia,
            'detallepago_id' => $detalle->id,
            'descripcion' => 'Pago de más en "'.$detalle->pago->concepto.'"',
            'created_by' => optional(Auth::user())->id,
        ]);
    }

    /**
     * Aplica el saldo disponible a un recibo pendiente.
     *
     * Solo liquida cuando el saldo alcanza para la cuota COMPLETA. Un abono
     * parcial dejaría el recibo en un estado a medias que el resto del sistema
     * no sabe representar (un recibo está pendiente, pagado o rechazado), y
     * confundiría tanto al vecino como al corte de caja. El remanente se queda
     * disponible para el siguiente recibo.
     *
     * Devuelve true si el recibo quedó liquidado con saldo.
     */
    public function aplicarA(Detallepago $detalle): bool
    {
        if ($detalle->estado === 'pagado' || ! $detalle->pago) {
            return false;
        }

        // Hay conceptos que NO deben consumir el adelanto del vecino: una
        // derrama, un proyecto, un gasto extraordinario. Lo que adelantó fue
        // su cuota, y tomarlo para otra cosa le cobraría dos veces.
        if (! $detalle->pago->aplicaSaldo()) {
            return false;
        }

        $cuota = round((float) $detalle->pago->cantidad, 2);

        if ($cuota <= 0) {
            return false;
        }

        $disponible = $this->saldo($detalle->user_id);

        if ($disponible + 0.001 < $cuota) {
            return false;
        }

        DB::transaction(function () use ($detalle, $cuota) {
            SaldoMovimiento::create([
                'user_id' => $detalle->user_id,
                'tipo' => SaldoMovimiento::TIPO_APLICACION,
                'monto' => $cuota,
                'detallepago_id' => $detalle->id,
                'descripcion' => 'Aplicado a "'.$detalle->pago->concepto.'"',
                'created_by' => optional(Auth::user())->id,
            ]);

            $detalle->update([
                'estado' => 'pagado',
                'cantidad_pago' => $cuota,
                // El dinero entró cuando el vecino adelantó; la fecha del
                // movimiento es la de hoy, que es cuando se aplicó el crédito.
                'fecha_pago' => now()->toDateString(),
            ]);
        });

        Log::info('Saldo a favor aplicado', [
            'detallepago_id' => $detalle->id,
            'user_id' => $detalle->user_id,
            'monto' => $cuota,
        ]);

        return true;
    }

    /**
     * Movimientos de un vecino, del más reciente al más antiguo.
     */
    public function movimientos(int $userId)
    {
        return SaldoMovimiento::where('user_id', $userId)
            ->with('detallepago.pago')
            ->orderByDesc('created_at')
            ->get();
    }

    /**
     * Ajuste manual de tesorería. El monto puede ser negativo.
     */
    public function ajustar(int $userId, float $monto, string $motivo): SaldoMovimiento
    {
        return SaldoMovimiento::create([
            'user_id' => $userId,
            'tipo' => SaldoMovimiento::TIPO_AJUSTE,
            'monto' => round($monto, 2),
            'descripcion' => $motivo,
            'created_by' => optional(Auth::user())->id,
        ]);
    }
}
