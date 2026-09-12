<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Proyecto extends Model
{
    protected $table = 'proyectos';

    protected $fillable = ['nombre', 'descripcion', 'costo', 'estado', 'avance', 'created_by'];

    protected $casts = [
        'costo' => 'float',
        'avance' => 'integer',
    ];

    public function avances(): HasMany
    {
        return $this->hasMany(ProyectoAvance::class)->orderByDesc('created_at')->orderByDesc('id');
    }

    public static function estados(): array
    {
        return [
            'por_iniciar' => 'Por iniciar',
            'en_proceso' => 'En proceso',
            'pausado' => 'Pausado',
            'terminado' => 'Terminado',
        ];
    }
}
