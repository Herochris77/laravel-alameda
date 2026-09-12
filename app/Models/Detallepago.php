<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Detallepago extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'id',
        'pago_id',
        'user_id',
        'path_pago',
        'cantidad_pago',
        'estado',
        'updated_at',
        'deleted_by',
    ];

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
        return $this->belongsTo(User::class, 'user_id')->withTrashed();
    }

    public function pago()
    {
        return $this->belongsTo(Pago::class, 'pago_id');
    }
}
