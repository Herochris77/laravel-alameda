<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Encuesta extends Model
{
    use HasFactory;

    protected $table = 'encuestas';

    protected $fillable = [
        'titulo',
        'descripcion',
        'tipo_usuario',
        'tipo',
        'multiple',
        'estado',
        'fecha_fin',
        'hora_fin',
        'created_by',
    ];

    protected $casts = [
        'multiple' => 'boolean',
        'fecha_fin' => 'date',
    ];
    
    public function fechaHoraCierre(): ?Carbon
    {
        if (! $this->fecha_fin) {
            return null;
        }
    
        $hora = $this->hora_fin ?: '23:59:59';
    
        return Carbon::parse(
            $this->fecha_fin->format('Y-m-d').' '.$hora,
            config('app.timezone')
        );
    }
    
    public function estaVencida(): bool
    {
        $fechaHoraCierre = $this->fechaHoraCierre();
    
        return $fechaHoraCierre !== null
            && now()->greaterThanOrEqualTo($fechaHoraCierre);
    }
    
    public function estaActiva(): bool
    {
        return $this->estado === 'abierta'
            && ! $this->estaVencida();
    }

    public function creador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by')->withTrashed();
    }

    public function opciones(): HasMany
    {
        return $this->hasMany(EncuestaOpcion::class);
    }

    public function respuestas(): HasMany
    {
        return $this->hasMany(EncuestaRespuesta::class);
    }

    public function scopeAbierta($query)
    {
        return $query
            ->where('estado', 'abierta')
            ->where(function ($q) {
                $q->whereNull('fecha_fin')
                  ->orWhereDate('fecha_fin', '>', today())
                  ->orWhere(function ($q2) {
                      $q2->whereDate('fecha_fin', today())
                         ->where(function ($q3) {
                             $q3->whereNull('hora_fin')
                                ->orWhereTime('hora_fin', '>', now()->format('H:i:s'));
                         });
                  });
            });
    }

    public function scopeParaUsuario($query, $tipo)
    {
        return $query->where(function ($q) use ($tipo) {
            $q->where('tipo_usuario', 'todos')
              ->orWhere('tipo_usuario', $tipo);
        });
    }

    public function usuarioYaVoto($userId): bool
    {
        return $this->respuestas()->where('user_id', $userId)->exists();
    }

    public function esOrdenamiento(): bool
    {
        return $this->tipo === 'ordenamiento';
    }

    public function totalVotantes(): int
    {
        return $this->respuestas()->distinct('user_id')->count('user_id');
    }

    public function totalVotos(): int
    {
        return $this->respuestas()->count();
    }

    /**
     * Calcula el resultado de una encuesta de ordenamiento por el método de
     * puntos de Borda: en una encuesta con N proyectos, la posición #1 vale N
     * puntos, la #2 vale N-1, ... y la #N vale 1. Se suman los puntos de todos
     * los votantes para obtener el orden ganador.
     *
     * @param  bool  $incluirVotantes  Incluir el detalle voto por voto (solo admin).
     */
    public function resultadosOrdenamiento(bool $incluirVotantes = false): array
    {
        $this->loadMissing(['opciones.respuestas.usuario', 'respuestas.usuario']);

        $n = $this->opciones->count();
        $votantes = $this->totalVotantes();
        $puntosMaximos = $n * max($votantes, 1);

        $ranking = $this->opciones->map(function ($opcion) use ($n) {
            $conteoPos = array_fill(1, max($n, 1), 0);
            $puntos = 0;

            foreach ($opcion->respuestas as $r) {
                $pos = (int) $r->posicion;
                if ($pos >= 1 && $pos <= $n) {
                    $conteoPos[$pos]++;
                    $puntos += ($n - $pos + 1);
                }
            }

            return [
                'id' => $opcion->id,
                'opcion' => $opcion->opcion,
                'puntos' => $puntos,
                'conteo_pos' => $conteoPos,
                'primeros' => $conteoPos[1] ?? 0,
            ];
        })
        ->sortByDesc('puntos')
        ->values();

        // Ancho de barra relativo a los puntos máximos posibles.
        $ranking = $ranking->map(function ($fila) use ($puntosMaximos) {
            $fila['pct'] = $puntosMaximos > 0 ? round(($fila['puntos'] / $puntosMaximos) * 100, 1) : 0;
            return $fila;
        });

        $resultado = [
            'total_votos' => $votantes,
            'num_opciones' => $n,
            'ranking' => $ranking,
        ];

        if ($incluirVotantes) {
            // Reconstruye el orden completo que eligió cada vecino.
            $porUsuario = [];
            foreach ($this->respuestas as $r) {
                if (! $r->relationLoaded('usuario')) {
                    $r->load('usuario');
                }
                $uid = $r->user_id;
                $porUsuario[$uid]['nombre'] = $r->usuario->nombre ?? 'Vecino';
                $porUsuario[$uid]['casa'] = $r->usuario->casa ?? '—';
                $porUsuario[$uid]['items'][] = [
                    'pos' => (int) $r->posicion,
                    'opcion' => optional($this->opciones->firstWhere('id', $r->opcion_id))->opcion,
                ];
            }

            $resultado['votantes'] = collect($porUsuario)->map(function ($v) {
                $orden = collect($v['items'])->sortBy('pos')->pluck('opcion')->values()->all();
                return [
                    'nombre' => $v['nombre'],
                    'casa' => $v['casa'],
                    'orden' => $orden,
                ];
            })->values()->all();
        }

        return $resultado;
    }
}
