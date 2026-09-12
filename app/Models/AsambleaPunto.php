<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AsambleaPunto extends Model
{
    protected $table = 'asamblea_puntos';

    protected $fillable = [
        'asamblea_id', 'orden', 'titulo', 'descripcion', 'tipo', 'estado',
    ];

    public function asamblea(): BelongsTo
    {
        return $this->belongsTo(Asamblea::class);
    }

    public function opciones(): HasMany
    {
        return $this->hasMany(AsambleaOpcion::class, 'punto_id')->orderBy('orden');
    }

    public function votos(): HasMany
    {
        return $this->hasMany(AsambleaVoto::class, 'punto_id');
    }
}
