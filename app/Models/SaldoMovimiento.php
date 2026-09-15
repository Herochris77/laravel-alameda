<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Movimientos del saldo a favor de un vecino.
 *
 * El saldo no se guarda como un número en `users`: se calcula sumando estos
 * movimientos. Así queda siempre el rastro de de dónde salió cada peso y a qué
 * recibo se aplicó, que es lo que tesorería necesita poder explicar en una
 * asamblea.
 *
 *   abono      -> entra dinero a favor (pagó de más, o pagó sin cargo previo)
 *   aplicacion -> el saldo se usa para liquidar un recibo
 *   ajuste     -> corrección manual de tesorería (puede ser negativa)
 */
class SaldoMovimiento extends Model
{
    use SoftDeletes;

    protected $table = 'saldo_movimientos';

    public const TIPO_ABONO = 'abono';

    public const TIPO_APLICACION = 'aplicacion';

    public const TIPO_AJUSTE = 'ajuste';

    protected $fillable = [
        'user_id',
        'tipo',
        'monto',
        'detallepago_id',
        'descripcion',
        'created_by',
    ];

    protected $casts = [
        'monto' => 'float',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id')->withTrashed();
    }

    public function detallepago()
    {
        return $this->belongsTo(Detallepago::class, 'detallepago_id');
    }

    public function autor()
    {
        return $this->belongsTo(User::class, 'created_by')->withTrashed();
    }

    /**
     * Efecto del movimiento sobre el saldo: los abonos suman, las
     * aplicaciones restan y los ajustes conservan su signo.
     */
    public function efecto(): float
    {
        return match ($this->tipo) {
            self::TIPO_ABONO => abs($this->monto),
            self::TIPO_APLICACION => -abs($this->monto),
            default => (float) $this->monto,
        };
    }

    /**
     * Saldo disponible de un vecino.
     */
    public static function saldoDe(int $userId): float
    {
        $movimientos = static::where('user_id', $userId)->get();

        $total = 0.0;

        foreach ($movimientos as $m) {
            $total += $m->efecto();
        }

        return round($total, 2);
    }
}
