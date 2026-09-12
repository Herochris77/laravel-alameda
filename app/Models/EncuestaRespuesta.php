<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EncuestaRespuesta extends Model
{
    use HasFactory;

    protected $table = 'encuesta_respuestas';

    protected $fillable = [
        'encuesta_id',
        'opcion_id',
        'user_id',
        'posicion',
    ];

    public function encuesta(): BelongsTo
    {
        return $this->belongsTo(Encuesta::class);
    }

    public function opcion(): BelongsTo
    {
        return $this->belongsTo(EncuestaOpcion::class);
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id')->withTrashed();
    }
}
