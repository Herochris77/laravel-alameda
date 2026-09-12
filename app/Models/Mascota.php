<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Mascota extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nombre',
        'tipo',
        'edad',
        'genero',
        'caracteristicas',
        'vacunas',
        'esterilizado',
        'amistoso',
        'foto',
    ];

    protected $casts = [
        'vacunas' => 'boolean',
        'esterilizado' => 'boolean',
        'amistoso' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function getFotoUrlAttribute(): string
    {
        if ($this->foto && file_exists(public_path('storage/'.$this->foto))) {
            return asset('storage/'.$this->foto);
        }

        return asset('img/mascotas/default.png');
    }
}
