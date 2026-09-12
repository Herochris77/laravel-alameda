<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EncuestaOpcion extends Model
{
    use HasFactory;

    protected $table = 'encuesta_opciones';

    protected $fillable = [
        'encuesta_id',
        'opcion',
        'votos_count',
    ];

    public function encuesta(): BelongsTo
    {
        return $this->belongsTo(Encuesta::class);
    }

    public function respuestas(): HasMany
    {
        return $this->hasMany(EncuestaRespuesta::class, 'opcion_id');
    }
}
