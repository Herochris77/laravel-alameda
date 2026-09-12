<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SolicitudPermiso extends Model
{
    use SoftDeletes;

    protected $table = 'solicitudes_permisos';

    protected $fillable = [
        'inquilino_id',
        'dueno_id',
        'titulo',
        'mensaje',
        'estado',
        'fecha_respuesta',
    ];

    protected function casts(): array
    {
        return [
            'fecha_respuesta' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    public function inquilino()
    {
        return $this->belongsTo(User::class, 'inquilino_id')->withTrashed();
    }

    public function dueno()
    {
        return $this->belongsTo(User::class, 'dueno_id')->withTrashed();
    }
}
