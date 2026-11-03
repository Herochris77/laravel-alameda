<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProyectoAvanceFoto extends Model
{
    protected $table = 'proyecto_avance_fotos';

    protected $fillable = ['proyecto_avance_id', 'ruta'];

    public function avance(): BelongsTo
    {
        return $this->belongsTo(ProyectoAvance::class, 'proyecto_avance_id');
    }
}
