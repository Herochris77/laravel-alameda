<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CorreoEnviado extends Model
{
    protected $table = 'correos_enviados';

    protected $fillable = [
        'destinatarios',
        'asunto',
        'titulo',
        'mensaje',
        'origen',
        'estado',
    ];
}
