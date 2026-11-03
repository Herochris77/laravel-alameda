<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Schema;

class ProyectoAvance extends Model
{
    protected $table = 'proyecto_avances';

    protected $fillable = ['proyecto_id', 'porcentaje', 'comentario', 'foto', 'autor'];

    public function proyecto(): BelongsTo
    {
        return $this->belongsTo(Proyecto::class);
    }

    public function fotos(): HasMany
    {
        return $this->hasMany(ProyectoAvanceFoto::class, 'proyecto_avance_id')->orderBy('id');
    }

    /** ¿Ya se corrió la migración de las fotos? (se consulta una sola vez por petición) */
    public static function tablaDeFotos(): bool
    {
        static $existe = null;

        return $existe ??= Schema::hasTable('proyecto_avance_fotos');
    }

    /**
     * Todas las fotos de evidencia del avance: la de siempre (columna `foto`)
     * y las que se subieron después. Cada una con su id (null en la antigua)
     * para poder quitarla individualmente.
     *
     * @return array<int, array{id:?int, ruta:string}>
     */
    public function evidencias(): array
    {
        $lista = [];

        if ($this->foto) {
            $lista[] = ['id' => null, 'ruta' => $this->foto];
        }

        // Si la tabla aún no existe (código subido antes de migrar) no se cae la pantalla.
        if (self::tablaDeFotos()) {
            foreach ($this->fotos as $f) {
                $lista[] = ['id' => $f->id, 'ruta' => $f->ruta];
            }
        }

        return $lista;
    }
}
