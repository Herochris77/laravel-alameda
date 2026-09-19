<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Detallepago extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'id',
        'pago_id',
        'user_id',
        'path_pago',
        'cantidad_pago',
        'fecha_pago',
        'comentario_rechazo',
        'estado',
        'forma_pago',
        'validado_por',
        'folio_recibo',
        'updated_at',
        'deleted_by',
    ];

    /**
     * Formas en que puede entrar el dinero.
     *
     * El efectivo se separa porque no deja comprobante que subir: el respaldo
     * es el recibo firmado que la tesorería entrega en mano.
     */
    public const FORMAS_PAGO = [
        'transferencia' => 'Transferencia',
        'efectivo' => 'Efectivo',
        'deposito' => 'Depósito en ventanilla',
    ];

    public function esEfectivo(): bool
    {
        return $this->forma_pago === 'efectivo';
    }

    public function formaPagoTexto(): string
    {
        // Los pagos anteriores a esta columna no la tienen; todos fueron
        // por transferencia, que era la única vía disponible.
        return self::FORMAS_PAGO[$this->forma_pago ?? 'transferencia'] ?? 'Transferencia';
    }

    /**
     * Quién validó el pago. Es de quien lleva la firma el recibo.
     */
    public function validador()
    {
        return $this->belongsTo(User::class, 'validado_por')->withTrashed();
    }

    protected $casts = [
        'fecha_pago' => 'date',
    ];

    /**
     * Fecha real del movimiento, o null si no se capturó.
     *
     * No hay respaldo a otra columna a propósito:
     *  - `updated_at` es la fecha de la última edición, justo el error que esta
     *    columna vino a corregir.
     *  - `created_at` es la fecha en que la administración creó el cargo, la
     *    misma para todos los vecinos del concepto; usarla daría por puntual
     *    cualquier pago.
     *
     * En los registros anteriores a esta columna el dato no existe, y es
     * preferible decirlo que inventarlo.
     */
    public function fechaEfectivaPago(): ?\Carbon\Carbon
    {
        return $this->fecha_pago
            ? \Carbon\Carbon::parse($this->fecha_pago)
            : null;
    }

    /**
     * Un pago solo es tardío si hay fecha real y vencimiento, y aquélla es
     * posterior. Sin comprobante todavía no hay pago que juzgar.
     */
    public function esPagoTardio(): bool
    {
        $fecha = $this->fechaEfectivaPago();

        if (! $fecha || ! $this->pago || ! $this->pago->vencimiento) {
            return false;
        }

        return $fecha->isAfter(
            \Carbon\Carbon::parse($this->pago->vencimiento)->endOfDay()
        );
    }

    /**
     * Excluye los recibos que se liquidaron con saldo a favor.
     *
     * Pagar un recibo con saldo NO es una entrada de dinero: el ingreso se
     * registró cuando el vecino hizo la transferencia que generó ese saldo.
     * Contarlo otra vez infla la recaudación y el fondo del condominio.
     *
     * Se usa en todo lo que mida flujo de efectivo. Para el devengado (qué se
     * cobró de lo que vencía) estos recibos sí cuentan: la cuota está cubierta.
     */
    public function scopeSinLiquidacionesConSaldo($query)
    {
        return $query->whereNotIn('id', function ($sub) {
            $sub->select('detallepago_id')
                ->from('saldo_movimientos')
                ->where('tipo', 'aplicacion')
                ->whereNull('deleted_at')
                ->whereNotNull('detallepago_id');
        });
    }

    /**
     * ¿Este recibo se pagó con saldo a favor en vez de con una transferencia?
     */
    public function fueLiquidadoConSaldo(): bool
    {
        return SaldoMovimiento::where('detallepago_id', $this->id)
            ->where('tipo', SaldoMovimiento::TIPO_APLICACION)
            ->exists();
    }

    /**
     * Recargo por mora que corresponde a este recibo.
     *
     * Se calcula contra la FECHA REAL de la transferencia, no contra la fecha
     * en que se sube el comprobante ni contra hoy. Es la diferencia que importa:
     * quien transfirió a tiempo y capturó su comprobante dos días después no
     * debe recargo, aunque el vencimiento ya haya pasado cuando lo sube.
     *
     * `$fechaSupuesta` permite preguntar "¿cuánto sería si pagara tal día?",
     * que es lo que necesita el formulario del vecino antes de guardar nada.
     */
    public function recargoAplicable($fechaSupuesta = null): float
    {
        if (! $this->pago || ! $this->pago->vencimiento) {
            return 0.0;
        }

        $pct = (float) ($this->pago->recargo_pct ?? 0);

        if ($pct <= 0) {
            return 0.0;
        }

        $fecha = $fechaSupuesta
            ? \Carbon\Carbon::parse($fechaSupuesta)
            : $this->fechaEfectivaPago();

        // Sin fecha todavía no se puede juzgar: no se asume mora.
        if (! $fecha) {
            return 0.0;
        }

        $vence = \Carbon\Carbon::parse($this->pago->vencimiento)->endOfDay();

        if (! $fecha->isAfter($vence)) {
            return 0.0;
        }

        return round((float) $this->pago->cantidad * $pct / 100, 2);
    }

    /**
     * Lo que debe pagarse: cuota más el recargo que corresponda a esa fecha.
     */
    public function montoEsperado($fechaSupuesta = null): float
    {
        if (! $this->pago) {
            return 0.0;
        }

        return round((float) $this->pago->cantidad + $this->recargoAplicable($fechaSupuesta), 2);
    }

    /**
     * Descompone lo que el vecino pagó en cuota, recargo y excedente.
     *
     * El excedente es lo único que se convierte en saldo a favor; el recargo
     * es ingreso del condominio y no debe acreditarse al vecino.
     */
    public function desglosePago(): array
    {
        $cuota = $this->pago ? (float) $this->pago->cantidad : 0.0;
        $recargo = $this->recargoAplicable();
        $pagado = (float) ($this->cantidad_pago ?: 0);
        $esperado = round($cuota + $recargo, 2);

        return [
            'cuota' => $cuota,
            'recargo' => $recargo,
            'esperado' => $esperado,
            'pagado' => $pagado,
            'excedente' => round(max($pagado - $esperado, 0), 2),
            'faltante' => round(max($esperado - $pagado, 0), 2),
        ];
    }

    // Sobreescribir el método delete para guardar quien eliminó
    public function delete()
    {
        // Solo si aún no está eliminado
        if (is_null($this->deleted_at)) {
            $this->deleted_by = Auth::user()->id;
            $this->save();
        }

        // Llama al delete original de Eloquent
        return parent::delete();
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id')->withTrashed();
    }

    public function pago()
    {
        return $this->belongsTo(Pago::class, 'pago_id');
    }
}
