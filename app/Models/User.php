<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use NotificationChannels\WebPush\HasPushSubscriptions;

class User extends Authenticatable
{
    use HasFactory, HasPushSubscriptions, Notifiable, SoftDeletes;

    protected $fillable = [
        'id',
        'nombre',
        'correo',
        'pass',
        'celular',
        'casa',
        'tipo',
        'rol',
        'pago',
        'estado',
        'token_auth',
        'emails',
        'foto',
    ];

    protected $hidden = [
        'pass',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    public function solicitudesEnviadas()
    {
        return $this->hasMany(\App\Models\SolicitudPermiso::class, 'inquilino_id');
    }

    public function solicitudesRecibidas()
    {
        return $this->hasMany(\App\Models\SolicitudPermiso::class, 'dueno_id');
    }

    public function getAuthIdentifierName()
    {
        return 'correo';
    }

    public function getAuthPassword()
    {
        return $this->pass;
    }
}
