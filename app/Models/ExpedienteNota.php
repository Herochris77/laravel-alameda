<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExpedienteNota extends Model
{
    protected $table = 'expediente_notas';

    protected $fillable = ['casa', 'nota', 'autor'];
}
