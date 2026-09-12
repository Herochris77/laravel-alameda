<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProyectoAvance extends Model
{
    protected $table = 'proyecto_avances';

    protected $fillable = ['proyecto_id', 'porcentaje', 'comentario', 'foto', 'autor'];

    public function proyecto(): BelongsTo
    {
        return $this->belongsTo(Proyecto::class);
    }
}
