<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Documento extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'id',
        'titulo',
        'descripcion',
        'tipo',
        'pago_id',
        'doc_path',
        'cantidad',
        'categoria_gasto',
        'fecha_gasto',
        'proveedor',
        'forma_pago',
        'servicio_id',
        'created_by',
        'created_at',
        'updated_at',
        'deleted_by',
    ];

    protected $casts = [
        'fecha_gasto' => 'date',
    ];

    /**
     * Un documento con monto ES un egreso; así se registran desde siempre.
     */
    public function scopeGastos($query)
    {
        return $query->whereNotNull('cantidad');
    }

    /**
     * Fecha con la que el gasto entra al reporte.
     *
     * Se prefiere la fecha real del movimiento bancario. Si no se capturó
     * —los 34 gastos anteriores a esta función— se cae a la de subida, que
     * es lo único que hay, y `fechaEsReal()` permite advertirlo en pantalla
     * en vez de presentarla como si fuera exacta.
     */
    public function fechaEfectiva(): ?\Carbon\Carbon
    {
        if ($this->fecha_gasto) {
            return \Carbon\Carbon::parse($this->fecha_gasto);
        }

        return $this->created_at ? \Carbon\Carbon::parse($this->created_at)->startOfDay() : null;
    }

    public function fechaEsReal(): bool
    {
        return $this->fecha_gasto !== null;
    }

    public function servicio()
    {
        return $this->belongsTo(ServicioRecurrente::class, 'servicio_id');
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

    public function pago()
    {
        return $this->belongsTo(Pago::class, 'pago_id');
    }
}
