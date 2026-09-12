<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AsambleaAsistencia extends Model
{
    protected $table = 'asamblea_asistencias';

    protected $fillable = ['asamblea_id', 'casa', 'user_id', 'confirmada', 'registrada_por'];

    protected $casts = ['confirmada' => 'boolean'];
}
