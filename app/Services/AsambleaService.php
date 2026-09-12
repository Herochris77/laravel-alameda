<?php

namespace App\Services;

use App\Models\Asamblea;
use App\Models\AsambleaAsistencia;
use App\Models\AsambleaOpcion;
use App\Models\AsambleaPoder;
use App\Models\AsambleaPunto;
use App\Models\AsambleaVoto;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AsambleaService
{
    /**
     * Número total de casas con derecho a voto (una por dueño).
     */
    public function totalCasas(): int
    {
        return User::where('tipo', 'dueño')
            ->whereNotNull('casa')
            ->distinct()
            ->count('casa');
    }

    /**
     * Lista de casas del condominio (las que tienen dueño).
     *
     * @return array<int, string>
     */
    public function casas(): array
    {
        return User::where('tipo', 'dueño')
            ->whereNotNull('casa')
            ->orderBy('casa')
            ->pluck('casa')
            ->unique()
            ->values()
            ->all();
    }

    public function duenoDeCasa(string $casa): ?User
    {
        return User::where('tipo', 'dueño')->where('casa', $casa)->first();
    }

    public function poderActivo(Asamblea $asamblea, string $casa): ?AsambleaPoder
    {
        return AsambleaPoder::where('asamblea_id', $asamblea->id)
            ->where('casa', $casa)
            ->where('estado', 'activo')
            ->latest('id')
            ->first();
    }

    /**
     * Quién ejerce el voto de una casa en esta asamblea:
     * el representante si hay poder activo, o el dueño en caso contrario.
     */
    public function titularCasaId(Asamblea $asamblea, string $casa): ?int
    {
        $poder = $this->poderActivo($asamblea, $casa);
        if ($poder) {
            return (int) $poder->representante_id;
        }

        return $this->duenoDeCasa($casa)?->id;
    }

    /**
     * Casas cuyo voto puede ejercer este usuario en la asamblea.
     *
     * @return array<int, string>
     */
    public function casasDeUsuario(Asamblea $asamblea, User $user): array
    {
        $casas = [];

        // Su propia casa, si es dueño y no la ha delegado.
        if ($user->tipo === 'dueño' && $user->casa) {
            if (! $this->poderActivo($asamblea, $user->casa)) {
                $casas[] = $user->casa;
            }
        }

        // Casas que le fueron delegadas (como inquilino o como representante vecino).
        $delegadas = AsambleaPoder::where('asamblea_id', $asamblea->id)
            ->where('estado', 'activo')
            ->where('representante_id', $user->id)
            ->pluck('casa')
            ->all();

        return array_values(array_unique(array_merge($casas, $delegadas)));
    }

    /**
     * Otorga un poder. Valida las reglas de negocio y desactiva poderes previos.
     *
     * @throws \RuntimeException
     */
    public function delegar(Asamblea $asamblea, string $casa, User $otorgante, int $representanteId, string $tipo): AsambleaPoder
    {
        if ($asamblea->estado === 'cerrada') {
            throw new \RuntimeException('La asamblea ya está cerrada.');
        }

        $dueno = $this->duenoDeCasa($casa);
        if (! $dueno || $dueno->id !== $otorgante->id) {
            throw new \RuntimeException('Solo el dueño de la casa puede delegar su voto.');
        }

        $representante = User::find($representanteId);
        if (! $representante) {
            throw new \RuntimeException('El representante no existe.');
        }

        if ($representante->id === $otorgante->id) {
            throw new \RuntimeException('No puedes delegarte el voto a ti mismo.');
        }

        if ($tipo === 'inquilino') {
            if ($representante->tipo !== 'inquilino' || $representante->casa !== $casa) {
                throw new \RuntimeException('El poder de inquilino solo puede otorgarse a un inquilino de tu casa.');
            }
        } elseif ($tipo === 'vecino') {
            // Representación a otro vecino: cualquier usuario activo distinto al otorgante.
        } else {
            throw new \RuntimeException('Tipo de poder inválido.');
        }

        return DB::transaction(function () use ($asamblea, $casa, $otorgante, $representante, $tipo) {
            AsambleaPoder::where('asamblea_id', $asamblea->id)
                ->where('casa', $casa)
                ->where('estado', 'activo')
                ->update(['estado' => 'revocado']);

            return AsambleaPoder::create([
                'asamblea_id' => $asamblea->id,
                'casa' => $casa,
                'otorgante_id' => $otorgante->id,
                'representante_id' => $representante->id,
                'tipo' => $tipo,
                'estado' => 'activo',
            ]);
        });
    }

    public function revocar(Asamblea $asamblea, string $casa, User $solicitante): void
    {
        $dueno = $this->duenoDeCasa($casa);
        if (! $dueno || $dueno->id !== $solicitante->id) {
            throw new \RuntimeException('Solo el dueño de la casa puede revocar el poder.');
        }

        AsambleaPoder::where('asamblea_id', $asamblea->id)
            ->where('casa', $casa)
            ->where('estado', 'activo')
            ->update(['estado' => 'revocado']);
    }

    /**
     * Pase de lista del administrador: marca (o quita) la presencia de una casa.
     * Esta es la única forma de habilitar el voto cuando control_asistencia está activo.
     */
    public function marcarPresente(Asamblea $asamblea, string $casa, User $admin, bool $presente = true): void
    {
        if ($presente) {
            $titular = $this->titularCasaId($asamblea, $casa);
            AsambleaAsistencia::updateOrCreate(
                ['asamblea_id' => $asamblea->id, 'casa' => $casa],
                ['user_id' => $titular ?? $admin->id, 'confirmada' => true, 'registrada_por' => $admin->id]
            );
        } else {
            AsambleaAsistencia::where('asamblea_id', $asamblea->id)->where('casa', $casa)->delete();
        }
    }

    public function casaPresente(Asamblea $asamblea, string $casa): bool
    {
        return AsambleaAsistencia::where('asamblea_id', $asamblea->id)
            ->where('casa', $casa)
            ->where('confirmada', true)
            ->exists();
    }

    /**
     * Emite el voto de un usuario en un punto, para todas las casas que ejerce.
     *
     * $data puede traer:
     *  - 'valor'    (si_no): a_favor|en_contra|abstencion
     *  - 'opcion_id'(opciones)
     *  - 'orden'    (ordenamiento): array de opcion_id en orden
     *  - 'por_casa' (opcional, si_no/opciones): [casa => valor|opcion_id] para votar distinto
     *
     * @throws \RuntimeException
     */
    public function emitirVoto(Asamblea $asamblea, AsambleaPunto $punto, User $user, array $data): int
    {
        if ($asamblea->estado !== 'en_curso') {
            throw new \RuntimeException('La asamblea no está en curso.');
        }
        if ($punto->estado === 'cerrado') {
            throw new \RuntimeException('Este punto ya está cerrado.');
        }
        if ($punto->asamblea_id !== $asamblea->id) {
            throw new \RuntimeException('El punto no pertenece a esta asamblea.');
        }

        $casas = $this->casasDeUsuario($asamblea, $user);
        if (empty($casas)) {
            throw new \RuntimeException('No tienes voto en esta asamblea.');
        }

        // Candado de asistencia: si está activo, solo se vota por las casas que
        // el administrador registró como presentes (pase de lista). Si un
        // representante trae varias casas, vota por las presentes; las que no
        // estén registradas no cuentan hasta que el admin las marque.
        if ($asamblea->control_asistencia) {
            $casas = array_values(array_filter($casas, fn ($casa) => $this->casaPresente($asamblea, $casa)));
            if (empty($casas)) {
                throw new \RuntimeException('Tu casa aún no ha sido registrada como presente por el administrador.');
            }
        }

        $opcionesValidas = $punto->opciones()->pluck('id')->map(fn ($i) => (int) $i)->all();
        $porCasa = $data['por_casa'] ?? [];

        DB::transaction(function () use ($asamblea, $punto, $user, $casas, $data, $porCasa, $opcionesValidas) {
            foreach ($casas as $casa) {
                AsambleaVoto::where('punto_id', $punto->id)->where('casa', $casa)->delete();

                if ($punto->tipo === 'si_no') {
                    $valor = $porCasa[$casa] ?? $data['valor'] ?? null;
                    if (! in_array($valor, ['a_favor', 'en_contra', 'abstencion'], true)) {
                        throw new \RuntimeException('Valor de voto inválido.');
                    }
                    AsambleaVoto::create([
                        'asamblea_id' => $asamblea->id,
                        'punto_id' => $punto->id,
                        'casa' => $casa,
                        'valor' => $valor,
                        'emitido_por' => $user->id,
                    ]);
                } elseif ($punto->tipo === 'opciones') {
                    $opcionId = (int) ($porCasa[$casa] ?? $data['opcion_id'] ?? 0);
                    if (! in_array($opcionId, $opcionesValidas, true)) {
                        throw new \RuntimeException('Opción inválida.');
                    }
                    AsambleaVoto::create([
                        'asamblea_id' => $asamblea->id,
                        'punto_id' => $punto->id,
                        'casa' => $casa,
                        'opcion_id' => $opcionId,
                        'emitido_por' => $user->id,
                    ]);
                } elseif ($punto->tipo === 'ordenamiento') {
                    $orden = array_map('intval', $data['orden'] ?? []);
                    $ok = count($orden) === count($opcionesValidas)
                        && empty(array_diff($orden, $opcionesValidas))
                        && empty(array_diff($opcionesValidas, $orden));
                    if (! $ok) {
                        throw new \RuntimeException('Debes ordenar todas las opciones.');
                    }
                    foreach ($orden as $i => $opcionId) {
                        AsambleaVoto::create([
                            'asamblea_id' => $asamblea->id,
                            'punto_id' => $punto->id,
                            'casa' => $casa,
                            'opcion_id' => $opcionId,
                            'posicion' => $i + 1,
                            'emitido_por' => $user->id,
                        ]);
                    }
                } else {
                    throw new \RuntimeException('Tipo de punto no soportado.');
                }

                // Si no hay control de asistencia, votar cuenta como presencia.
                if (! $asamblea->control_asistencia) {
                    AsambleaAsistencia::updateOrCreate(
                        ['asamblea_id' => $asamblea->id, 'casa' => $casa],
                        ['user_id' => $user->id, 'confirmada' => true]
                    );
                }
            }
        });

        return count($casas);
    }

    /**
     * Resultado de un punto según su tipo.
     */
    public function resultadosPunto(AsambleaPunto $punto): array
    {
        $punto->loadMissing('opciones');

        if ($punto->tipo === 'si_no') {
            $conteo = ['a_favor' => 0, 'en_contra' => 0, 'abstencion' => 0];
            $votos = AsambleaVoto::where('punto_id', $punto->id)->get();
            foreach ($votos as $v) {
                if (isset($conteo[$v->valor])) {
                    $conteo[$v->valor]++;
                }
            }
            $totalCasas = array_sum($conteo);

            return [
                'tipo' => 'si_no',
                'total_casas' => $totalCasas,
                'conteo' => $conteo,
                'aprobado' => $conteo['a_favor'] > $conteo['en_contra'],
            ];
        }

        if ($punto->tipo === 'opciones') {
            $votos = AsambleaVoto::where('punto_id', $punto->id)->get();
            $porOpcion = [];
            foreach ($punto->opciones as $op) {
                $porOpcion[$op->id] = 0;
            }
            foreach ($votos as $v) {
                if (isset($porOpcion[$v->opcion_id])) {
                    $porOpcion[$v->opcion_id]++;
                }
            }
            $total = array_sum($porOpcion);

            $resultados = $punto->opciones->map(function ($op) use ($porOpcion, $total) {
                $c = $porOpcion[$op->id] ?? 0;

                return [
                    'id' => $op->id,
                    'opcion' => $op->opcion,
                    'votos' => $c,
                    'pct' => $total > 0 ? round(($c / $total) * 100, 1) : 0,
                ];
            })->sortByDesc('votos')->values()->all();

            return [
                'tipo' => 'opciones',
                'total_casas' => $total,
                'resultados' => $resultados,
                'ganador' => $resultados[0]['opcion'] ?? null,
            ];
        }

        if ($punto->tipo === 'ordenamiento') {
            $n = $punto->opciones->count();
            $votos = AsambleaVoto::where('punto_id', $punto->id)->get();
            $votantes = $votos->pluck('casa')->unique()->count();

            $puntosPorOpcion = [];
            $conteoPos = [];
            foreach ($punto->opciones as $op) {
                $puntosPorOpcion[$op->id] = 0;
                $conteoPos[$op->id] = array_fill(1, max($n, 1), 0);
            }
            foreach ($votos as $v) {
                $pos = (int) $v->posicion;
                if ($v->opcion_id && $pos >= 1 && $pos <= $n && isset($puntosPorOpcion[$v->opcion_id])) {
                    $puntosPorOpcion[$v->opcion_id] += ($n - $pos + 1);
                    $conteoPos[$v->opcion_id][$pos]++;
                }
            }
            $maxPuntos = $n * max($votantes, 1);

            $ranking = $punto->opciones->map(function ($op) use ($puntosPorOpcion, $conteoPos, $maxPuntos) {
                $pts = $puntosPorOpcion[$op->id] ?? 0;

                return [
                    'id' => $op->id,
                    'opcion' => $op->opcion,
                    'puntos' => $pts,
                    'pct' => $maxPuntos > 0 ? round(($pts / $maxPuntos) * 100, 1) : 0,
                    'conteo_pos' => $conteoPos[$op->id] ?? [],
                    'primeros' => $conteoPos[$op->id][1] ?? 0,
                ];
            })->sortByDesc('puntos')->values()->all();

            return [
                'tipo' => 'ordenamiento',
                'total_casas' => $votantes,
                'num_opciones' => $n,
                'ranking' => $ranking,
                'ganador' => $ranking[0]['opcion'] ?? null,
            ];
        }

        return ['tipo' => $punto->tipo, 'total_casas' => 0];
    }

    public function quorum(Asamblea $asamblea): array
    {
        $total = $this->totalCasas();
        $presentes = AsambleaAsistencia::where('asamblea_id', $asamblea->id)
            ->where('confirmada', true)
            ->distinct()
            ->count('casa');
        $pct = $total > 0 ? round(($presentes / $total) * 100, 1) : 0;

        return [
            'presentes' => $presentes,
            'total' => $total,
            'pct' => $pct,
            'alcanzado' => $pct >= $asamblea->quorum_pct,
        ];
    }
}
