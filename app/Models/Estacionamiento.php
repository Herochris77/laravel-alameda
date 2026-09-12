<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Estacionamiento extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'ubicacion',
        'estado',
        'user_id',
        'fecha_inicio',
        'fecha_fin',
        'hora_inicio',
        'hora_fin',
    ];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id')->withTrashed();
    }

    public function scopeDisponible($query)
    {
        return $query->where('estado', 'disponible');
    }

    public function scopeOcupado($query)
    {
        return $query->where('estado', 'ocupado');
    }

    public function scopeReservado($query)
    {
        return $query->where('estado', 'reservado');
    }

    public function scopeEntrada($query)
    {
        return $query->where('ubicacion', 'entrada');
    }

    public function scopeCentral($query)
    {
        return $query->where('ubicacion', 'central');
    }

    public function estaDisponibleParaFechaHora($fecha, $hora = null)
    {
        if ($this->estado === 'disponible') {
            return true;
        }

        if ($this->estado === 'reservado') {
            $fechaInicio = \Carbon\Carbon::parse($this->fecha_inicio);
            $fechaFin = \Carbon\Carbon::parse($this->fecha_fin);
            
            $fechaObj = $hora 
                ? \Carbon\Carbon::parse($fecha . ' ' . $hora)
                : \Carbon\Carbon::parse($fecha);
            
            if ($this->hora_inicio && $this->hora_fin) {
                $inicio = \Carbon\Carbon::parse($this->fecha_inicio . ' ' . $this->hora_inicio);
                $fin = \Carbon\Carbon::parse($this->fecha_fin . ' ' . $this->hora_fin);
                return $fechaObj->lt($inicio) || $fechaObj->gt($fin);
            }
            
            return $fechaObj->lt($fechaInicio) || $fechaObj->gt($fechaFin);
        }

        return false;
    }

    public function marcarDisponible()
    {
        $this->update([
            'estado' => 'disponible',
            'user_id' => null,
            'fecha_inicio' => null,
            'fecha_fin' => null,
            'hora_inicio' => null,
            'hora_fin' => null,
        ]);
    }
}