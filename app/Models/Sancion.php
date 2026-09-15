<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Sancion extends Model
{
    use SoftDeletes;

    protected $table = 'sanciones';

    protected $fillable = [
        'id',
        'user_id',
        'motivo',
        'monto',
        'incidencia',
        'comentario',
        'foto_path',
        'pago_path',
        'fecha_pago',
        'estado',
        'created_by',
        'deleted_by',
    ];

    protected $casts = [
        'fecha_pago' => 'date',
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
        return $this->belongsTo(User::class, 'created_by')->withTrashed();
    }

    public function sancionado()
    {
        return $this->belongsTo(User::class, 'user_id')->withTrashed();
    }
}
