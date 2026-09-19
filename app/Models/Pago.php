<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Pago extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'id',
        'concepto',
        'cantidad',
        'vencimiento',
        'recargo_pct',
        'aplica_saldo',
        'ultimo_aviso',
        'created_at',
        'updated_at',
        'created_by',
        'deleted_by',
    ];

    protected $casts = [
        'aplica_saldo' => 'boolean',
    ];

    /**
     * ¿Este concepto se puede liquidar con el saldo a favor del vecino?
     *
     * Los conceptos anteriores a esta función no tienen el dato, y ahí la
     * respuesta es que sí: es como se venían comportando.
     */
    public function aplicaSaldo(): bool
    {
        return $this->aplica_saldo === null ? true : (bool) $this->aplica_saldo;
    }

    // Sobreescribir el método delete para guardar quien eliminó
    public function delete()
    {
        // Solo si aún no está eliminado
        if (is_null($this->deleted_at)) {
            $this->deleted_by = Auth::user()->id;
            $this->save();
        }

        // Llama al delete original de Eloquent
        return parent::delete();
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by')->withTrashed();
    }

    public function detalles()
    {
        return $this->hasMany(Detallepago::class, 'pago_id');
    }
}
