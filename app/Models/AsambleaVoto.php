<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AsambleaVoto extends Model
{
    protected $table = 'asamblea_votos';

    protected $fillable = [
        'asamblea_id', 'punto_id', 'casa', 'valor', 'opcion_id', 'posicion', 'emitido_por',
    ];

    public function punto(): BelongsTo
    {
        return $this->belongsTo(AsambleaPunto::class, 'punto_id');
    }

    public function opcion(): BelongsTo
    {
        return $this->belongsTo(AsambleaOpcion::class, 'opcion_id');
    }

    public function emisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'emitido_por')->withTrashed();
    }
}
