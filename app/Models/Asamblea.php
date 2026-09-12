<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Asamblea extends Model
{
    protected $table = 'asambleas';

    protected $fillable = [
        'titulo', 'descripcion', 'fecha', 'hora', 'lugar', 'minuta',
        'quorum_pct', 'control_asistencia', 'estado', 'created_by',
    ];

    protected $casts = [
        'fecha' => 'date',
        'quorum_pct' => 'integer',
        'control_asistencia' => 'boolean',
    ];

    public function creador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by')->withTrashed();
    }

    public function puntos(): HasMany
    {
        return $this->hasMany(AsambleaPunto::class)->orderBy('orden');
    }

    public function poderes(): HasMany
    {
        return $this->hasMany(AsambleaPoder::class);
    }

    public function votos(): HasMany
    {
        return $this->hasMany(AsambleaVoto::class);
    }

    public function asistencias(): HasMany
    {
        return $this->hasMany(AsambleaAsistencia::class);
    }
}
