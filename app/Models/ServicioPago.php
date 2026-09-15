<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Un pago concreto de un servicio recurrente.
 *
 * Es el historial que responde "¿ya se pagó el agua de este bimestre y de
 * cuánto salió?". Guarda el monto REAL, que casi nunca es el estimado, y el
 * folio del comprobante para poder rastrearlo en el estado de cuenta.
 */
class ServicioPago extends Model
{
    protected $table = 'servicio_pagos';

    protected $fillable = [
        'servicio_id',
        'periodo',
        'fecha_pago',
        'monto_pagado',
        'folio',
        'notas',
        'created_by',
    ];

    protected $casts = [
        'monto_pagado' => 'float',
        'fecha_pago' => 'date',
    ];

    public function servicio()
    {
        return $this->belongsTo(ServicioRecurrente::class, 'servicio_id');
    }

    public function autor()
    {
        return $this->belongsTo(User::class, 'created_by')->withTrashed();
    }
}
