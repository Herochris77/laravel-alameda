<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AsambleaOpcion extends Model
{
    protected $table = 'asamblea_opciones';

    protected $fillable = ['punto_id', 'opcion', 'orden'];

    public function punto(): BelongsTo
    {
        return $this->belongsTo(AsambleaPunto::class, 'punto_id');
    }
}
