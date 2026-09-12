<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AsambleaPoder extends Model
{
    protected $table = 'asamblea_poderes';

    protected $fillable = [
        'asamblea_id', 'casa', 'otorgante_id', 'representante_id', 'tipo', 'estado',
    ];

    public function asamblea(): BelongsTo
    {
        return $this->belongsTo(Asamblea::class);
    }

    public function otorgante(): BelongsTo
    {
        return $this->belongsTo(User::class, 'otorgante_id')->withTrashed();
    }

    public function representante(): BelongsTo
    {
        return $this->belongsTo(User::class, 'representante_id')->withTrashed();
    }
}
