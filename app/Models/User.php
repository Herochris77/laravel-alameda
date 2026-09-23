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
        'cargo',
        'pago',
        'estado',
        'token_auth',
        'emails',
        'foto',
        'firma',
        'acepto_aviso_en',
        'acepto_aviso_version',
        'visible_directorio',
        'desvinculado_en',
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
            'desvinculado_en' => 'datetime',
            'visible_directorio' => 'boolean',
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

    /*
     * =====================================================
     * CARGOS DE LA MESA DIRECTIVA
     * =====================================================
     *
     * El `rol` decide a qué módulos entra alguien; el `cargo` decide qué le
     * toca hacer dentro del módulo de pagos. Se separan porque toda la mesa
     * necesita ver la cobranza, pero el dinero lo mueve quien es tesorero.
     */
    public const CARGOS = [
        'tesorero' => 'Tesorero',
        'presidente' => 'Presidente',
        'secretario' => 'Secretario',
        'vocal' => 'Vocal',
    ];

    /**
     * ¿Este usuario ya aceptó la versión vigente del aviso de privacidad?
     *
     * Se compara contra la versión, no contra la fecha: si el aviso cambia de
     * fondo se sube la versión en config y a todos les vuelve a aparecer la
     * ventana. Corregir una coma no debería obligar a 45 vecinos a aceptar
     * otra vez, por eso la versión se sube a mano.
     */
    public function haAceptadoAviso(): bool
    {
        if (! \Illuminate\Support\Facades\Schema::hasColumn('users', 'acepto_aviso_en')) {
            // Todavía no se corre /migrar: no se puede exigir lo que no se
            // puede registrar, y bloquear el sistema entero sería peor.
            return true;
        }

        if (! $this->acepto_aviso_en) {
            return false;
        }

        return (string) $this->acepto_aviso_version === (string) config('privacidad.version_aviso', '1.0');
    }

    /**
     * ¿Hay alguien nombrado tesorero?
     *
     * Mientras no lo haya, el módulo de pagos sigue abierto a toda la mesa,
     * igual que antes. Así activar esta función no le quita el acceso a nadie
     * de un día para otro: la restricción empieza cuando se asigna el cargo.
     */
    public static function hayTesoreroAsignado(): bool
    {
        return static::where('cargo', 'tesorero')
            ->where('estado', 1)
            ->exists();
    }

    /**
     * ¿Este usuario puede mover dinero? (crear recibos, validar pagos,
     * eliminarlos, ajustar saldos)
     */
    public function puedeGestionarPagos(): bool
    {
        // Quien administra el sistema siempre puede, para no quedarse fuera
        // de su propia plataforma por una mala asignación de cargos.
        if ($this->rol === 'super-administrador') {
            return true;
        }

        if ($this->rol !== 'administrador') {
            return false;
        }

        if ($this->cargo === 'tesorero') {
            return true;
        }

        // Periodo de transición: sin tesorero nombrado, todo sigue como antes.
        return ! static::hayTesoreroAsignado();
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
