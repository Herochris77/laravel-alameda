<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Contacto extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'id',
        'nombre_contacto',
        'numero_contacto',
        'tipo_contacto',
        'pagina_web',
        'created_by',
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
        return $this->belongsTo(User::class, 'created_by')->withTrashed();
    }
}
