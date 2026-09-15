<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Cierre mensual de caja.
 *
 * Guarda lo que la plataforma no puede saber: cuánto quedó en el banco y
 * cuánto en efectivo al cerrar el mes, según el estado de cuenta. Los
 * ingresos y egresos sí se calculan de los datos del sistema.
 *
 * El saldo inicial de un mes no se captura: se arrastra del cierre del mes
 * anterior, que es como se encadena un corte de caja.
 */
class CorteMensual extends Model
{
    protected $table = 'cortes_mensuales';

    protected $fillable = [
        'periodo',
        'saldo_banco',
        'saldo_efectivo',
        'notas',
        'created_by',
    ];

    protected $casts = [
        'saldo_banco' => 'float',
        'saldo_efectivo' => 'float',
    ];

    public function autor()
    {
        return $this->belongsTo(User::class, 'created_by')->withTrashed();
    }

    /**
     * Saldo total al cierre: banco más efectivo.
     */
    public function total(): float
    {
        return round($this->saldo_banco + $this->saldo_efectivo, 2);
    }

    /**
     * Cierre del mes anterior a un periodo dado (formato Y-m).
     *
     * Es el saldo inicial del periodo: así el reporte de cada mes arranca
     * donde terminó el anterior, sin capturarlo a mano.
     */
    public static function anteriorA(string $periodo): ?self
    {
        return static::where('periodo', '<', $periodo)
            ->orderByDesc('periodo')
            ->first();
    }
}
