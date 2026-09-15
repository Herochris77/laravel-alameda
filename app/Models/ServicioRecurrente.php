<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Servicio o pago recurrente a cargo de la tesorería.
 *
 * Es el catálogo de obligaciones del condominio: el agua, la basura, el
 * internet, la jardinería. Guarda con qué referencia se paga, cuánto suele
 * costar y cada cuándo vence, para que el cronjob avise antes y para que
 * quien reciba la tesorería no tenga que reconstruirlo preguntando.
 *
 * El vencimiento NO se recalcula solo con el paso del tiempo: avanza cuando
 * se registra el pago. Así un recibo olvidado se queda vencido a la vista en
 * vez de saltar al mes siguiente como si nada.
 */
class ServicioRecurrente extends Model
{
    use SoftDeletes;

    protected $table = 'servicios_recurrentes';

    protected $fillable = [
        'nombre',
        'proveedor',
        'referencia',
        'categoria',
        'monto_estimado',
        'periodicidad',
        'proximo_vencimiento',
        'dias_aviso',
        'forma_pago',
        'contacto',
        'notas',
        'activo',
        'ultimo_aviso',
        'created_by',
    ];

    protected $casts = [
        'monto_estimado' => 'float',
        'dias_aviso' => 'integer',
        'activo' => 'boolean',
        'proximo_vencimiento' => 'date',
        'ultimo_aviso' => 'date',
    ];

    /**
     * Cada cuándo vuelve a tocar, y cuántos meses hay que sumarle al vencer.
     */
    public const PERIODICIDADES = [
        'mensual' => ['etiqueta' => 'Mensual', 'meses' => 1],
        'bimestral' => ['etiqueta' => 'Bimestral', 'meses' => 2],
        'trimestral' => ['etiqueta' => 'Trimestral', 'meses' => 3],
        'semestral' => ['etiqueta' => 'Semestral', 'meses' => 6],
        'anual' => ['etiqueta' => 'Anual', 'meses' => 12],
        'unico' => ['etiqueta' => 'Pago único', 'meses' => 0],
    ];

    /**
     * Las mismas categorías que usan los egresos, para que el gasto del
     * servicio y su registro en el reporte mensual hablen el mismo idioma.
     */
    public const CATEGORIAS = [
        'agua' => 'Agua',
        'luz' => 'Luz',
        'mantenimiento' => 'Mantenimiento',
        'jardineria' => 'Jardinería',
        'limpieza' => 'Limpieza',
        'seguridad' => 'Seguridad',
        'internet' => 'Internet',
        'administrativo' => 'Administrativo',
        'otro' => 'Otro',
    ];

    public const FORMAS_PAGO = [
        'transferencia' => 'Transferencia',
        'ventanilla' => 'Ventanilla / OXXO',
        'domiciliado' => 'Cargo domiciliado',
        'efectivo' => 'Efectivo',
    ];

    public function pagos()
    {
        return $this->hasMany(ServicioPago::class, 'servicio_id')->orderByDesc('fecha_pago');
    }

    public function autor()
    {
        return $this->belongsTo(User::class, 'created_by')->withTrashed();
    }

    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    public function etiquetaPeriodicidad(): string
    {
        return self::PERIODICIDADES[$this->periodicidad]['etiqueta'] ?? $this->periodicidad;
    }

    public function etiquetaCategoria(): string
    {
        return self::CATEGORIAS[$this->categoria] ?? '—';
    }

    /**
     * Días que faltan para el vencimiento. Negativo si ya pasó.
     */
    public function diasRestantes(): ?int
    {
        if (! $this->proximo_vencimiento) {
            return null;
        }

        return Carbon::today()->diffInDays($this->proximo_vencimiento, false);
    }

    /**
     * Semáforo del servicio, que es lo que ordena la lista y pinta la tabla.
     *
     * "por_vencer" empieza en la ventana de aviso que configuró el tesorero,
     * para que lo que ve en pantalla coincida con lo que le va a llegar por
     * correo.
     */
    public function estatus(): string
    {
        if (! $this->activo) {
            return 'inactivo';
        }

        $dias = $this->diasRestantes();

        if ($dias === null) {
            return 'sin_fecha';
        }

        if ($dias < 0) {
            return 'vencido';
        }

        if ($dias === 0) {
            return 'hoy';
        }

        if ($dias <= max(1, (int) $this->dias_aviso)) {
            return 'por_vencer';
        }

        return 'al_corriente';
    }

    /**
     * Cuánto representa este servicio al mes, prorrateado.
     *
     * Sirve para estimar la carga fija mensual sin que un pago anual la
     * distorsione. Los pagos únicos no cuentan: no son carga recurrente.
     */
    public function costoMensualizado(): float
    {
        $meses = self::PERIODICIDADES[$this->periodicidad]['meses'] ?? 0;

        if ($meses < 1) {
            return 0.0;
        }

        return round($this->monto_estimado / $meses, 2);
    }

    /**
     * Fecha del siguiente vencimiento después de pagar el actual.
     *
     * Se calcula desde el vencimiento, no desde el día en que se pagó: un
     * pago tardío no debe recorrer el calendario del servicio.
     */
    public function siguienteVencimiento(): ?Carbon
    {
        $meses = self::PERIODICIDADES[$this->periodicidad]['meses'] ?? 0;

        if ($meses < 1 || ! $this->proximo_vencimiento) {
            return null;
        }

        return Carbon::parse($this->proximo_vencimiento)->addMonthsNoOverflow($meses);
    }

    /**
     * Promedio de lo realmente pagado, para contrastarlo con el estimado.
     */
    public function promedioPagado(): ?float
    {
        $pagos = $this->pagos()->get();

        if ($pagos->isEmpty()) {
            return null;
        }

        return round($pagos->avg('monto_pagado'), 2);
    }
}
