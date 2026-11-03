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
use Illuminate\Support\Facades\Schema;

class AsambleaService
{
    /**
     * Cómo se lleva a cabo una asamblea.
     *
     *  - plataforma: cada casa vota desde su cuenta (como siempre).
     *  - presencial: se vota en la sala; la mesa directiva registra asistencia y votos.
     *  - mixta: las dos cosas. Quien puede entrar vota desde su cuenta; la mesa
     *    registra a quien vota en sala o no puede entrar a la plataforma.
     */
    public const MODALIDADES = [
        'plataforma' => 'Por la plataforma',
        'presencial' => 'Presencial (la mesa registra los votos)',
        'mixta' => 'Mixta (plataforma y sala)',
    ];

    /** Tipos de punto que se votan (el informativo solo deja constancia del acuerdo). */
    public const TIPOS_VOTABLES = ['si_no', 'opciones', 'ordenamiento'];

    public function modalidad(Asamblea $asamblea): string
    {
        $m = (string) ($asamblea->modalidad ?? 'plataforma');

        return array_key_exists($m, self::MODALIDADES) ? $m : 'plataforma';
    }

    /**
     * ¿Ya existen las columnas de la migración de asambleas flexibles?
     * Permite subir el código antes de correr /migrar sin tumbar las asambleas.
     */
    private function columnasNuevas(): bool
    {
        static $listo = null;

        return $listo ??= Schema::hasColumn('asamblea_votos', 'origen')
            && Schema::hasColumn('asamblea_asistencias', 'modo');
    }

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
     * ¿Quien vota por esta casa puede entrar a la plataforma?
     *
     * No puede si bloquearon su cuenta o si no aceptó el aviso de privacidad y
     * los términos (sin eso el sistema no le deja registrar nada). A esas casas
     * la mesa directiva les registra asistencia y voto en la asamblea.
     */
    public function casaTieneAcceso(Asamblea $asamblea, string $casa): bool
    {
        $id = $this->titularCasaId($asamblea, $casa);
        $usuario = $id ? User::find($id) : null;

        return $usuario !== null
            && (string) $usuario->estado === '1'
            && $usuario->haAceptadoAviso();
    }

    /**
     * Estado de cada casa en la asamblea: quién la vota, si puede entrar a la
     * plataforma, si está presente y cómo se registró su presencia.
     *
     * @return array<int, array<string, mixed>>
     */
    public function casasEstado(Asamblea $asamblea): array
    {
        // Cuatro consultas en total (no una por casa): este estado se pide en cada
        // actualización del tablero, y con 45 casas el método anterior se notaba.
        $asistencias = AsambleaAsistencia::where('asamblea_id', $asamblea->id)
            ->where('confirmada', true)
            ->get()
            ->keyBy('casa');

        $poderes = AsambleaPoder::where('asamblea_id', $asamblea->id)
            ->where('estado', 'activo')
            ->orderBy('id')
            ->get()
            ->keyBy('casa');   // si hubiera dos activos, gana el más reciente

        $duenos = User::where('tipo', 'dueño')
            ->whereNotNull('casa')
            ->orderBy('id')
            ->get()
            ->groupBy('casa')
            ->map(fn ($grupo) => $grupo->first());

        $usuarios = User::whereIn('id', $duenos->pluck('id')->merge($poderes->pluck('representante_id'))->unique())
            ->get()
            ->keyBy('id');

        $filas = [];
        foreach ($duenos as $casa => $dueno) {
            $poder = $poderes->get($casa);
            $titular = $poder ? $usuarios->get($poder->representante_id) : $usuarios->get($dueno->id);
            $asistencia = $asistencias->get($casa);

            $filas[] = [
                'casa' => (string) $casa,
                'nombre' => $titular?->nombre ?? $dueno->nombre ?? 'Vecino',
                'con_poder' => $poder !== null,
                'titular_id' => $titular?->id,
                'titular_tipo' => $titular?->tipo,
                'titular_casa' => $titular?->casa,
                'acceso' => $titular !== null
                    && (string) $titular->estado === '1'
                    && $titular->haAceptadoAviso(),
                'presente' => $asistencia !== null,
                'modo' => $asistencia?->modo,
                'nota' => $asistencia?->nota,
            ];
        }

        usort($filas, fn ($a, $b) => strnatcmp($a['casa'], $b['casa']));

        return $filas;
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
    public function marcarPresente(Asamblea $asamblea, string $casa, User $admin, bool $presente = true, ?string $nota = null): void
    {
        if ($presente) {
            $titular = $this->titularCasaId($asamblea, $casa);
            $datos = ['user_id' => $titular ?? $admin->id, 'confirmada' => true, 'registrada_por' => $admin->id];

            if ($this->columnasNuevas()) {
                $datos['modo'] = 'mesa';
                if ($nota !== null) {
                    $datos['nota'] = mb_substr($nota, 0, 255);
                }
            }

            AsambleaAsistencia::updateOrCreate(
                ['asamblea_id' => $asamblea->id, 'casa' => $casa],
                $datos
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
        if ($this->modalidad($asamblea) === 'presencial') {
            throw new \RuntimeException('Esta asamblea se vota en la sala: la mesa directiva registra los votos.');
        }
        if (! in_array($punto->tipo, self::TIPOS_VOTABLES, true)) {
            throw new \RuntimeException('Este punto es informativo: no se vota.');
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
                $deEstaCasa = $data;
                if (isset($porCasa[$casa])) {
                    $punto->tipo === 'si_no'
                        ? $deEstaCasa['valor'] = $porCasa[$casa]
                        : $deEstaCasa['opcion_id'] = $porCasa[$casa];
                }

                $this->escribirVoto($asamblea, $punto, (string) $casa, $deEstaCasa, $user->id, 'plataforma', $opcionesValidas);

                // Si no hay control de asistencia, votar cuenta como presencia.
                if (! $asamblea->control_asistencia) {
                    $datos = ['user_id' => $user->id, 'confirmada' => true];
                    if ($this->columnasNuevas()) {
                        $datos['modo'] = 'plataforma';
                    }
                    AsambleaAsistencia::updateOrCreate(
                        ['asamblea_id' => $asamblea->id, 'casa' => $casa],
                        $datos
                    );
                }
            }
        });

        return count($casas);
    }

    /**
     * Guarda el voto de UNA casa en un punto (reemplaza el anterior si lo había).
     * Lo comparten el voto desde la plataforma y el registro de la mesa en sala.
     *
     * @param  array<int, int>  $opcionesValidas
     *
     * @throws \RuntimeException
     */
    private function escribirVoto(Asamblea $asamblea, AsambleaPunto $punto, string $casa, array $data, int $emitidoPor, string $origen, array $opcionesValidas): void
    {
        AsambleaVoto::where('punto_id', $punto->id)->where('casa', $casa)->delete();

        $base = [
            'asamblea_id' => $asamblea->id,
            'punto_id' => $punto->id,
            'casa' => $casa,
            'emitido_por' => $emitidoPor,
        ];
        if ($this->columnasNuevas()) {
            $base['origen'] = $origen;
        }

        if ($punto->tipo === 'si_no') {
            $valor = $data['valor'] ?? null;
            if (! in_array($valor, ['a_favor', 'en_contra', 'abstencion'], true)) {
                throw new \RuntimeException('Valor de voto inválido.');
            }
            AsambleaVoto::create($base + ['valor' => $valor]);
        } elseif ($punto->tipo === 'opciones') {
            $opcionId = (int) ($data['opcion_id'] ?? 0);
            if (! in_array($opcionId, $opcionesValidas, true)) {
                throw new \RuntimeException('Opción inválida.');
            }
            AsambleaVoto::create($base + ['opcion_id' => $opcionId]);
        } elseif ($punto->tipo === 'ordenamiento') {
            $orden = array_map('intval', $data['orden'] ?? []);
            $ok = count($orden) === count($opcionesValidas)
                && empty(array_diff($orden, $opcionesValidas))
                && empty(array_diff($opcionesValidas, $orden));
            if (! $ok) {
                throw new \RuntimeException('Debes ordenar todas las opciones.');
            }
            foreach ($orden as $i => $opcionId) {
                AsambleaVoto::create($base + ['opcion_id' => $opcionId, 'posicion' => $i + 1]);
            }
        } else {
            throw new \RuntimeException('Tipo de punto no soportado.');
        }
    }

    /**
     * La mesa directiva registra el voto de una casa que votó en la sala o que
     * no puede entrar a la plataforma (por ejemplo, no aceptó el aviso de
     * privacidad). Queda marcado como "mesa", con quién lo registró, y la casa
     * cuenta como presente.
     *
     * @param  array<string, mixed>  $data  valor | opcion_id | orden
     *
     * @throws \RuntimeException
     */
    public function registrarVotoMesa(Asamblea $asamblea, AsambleaPunto $punto, string $casa, array $data, User $registra): void
    {
        $this->validarRegistroMesa($asamblea, $punto, $casa);

        $opcionesValidas = $punto->opciones()->pluck('id')->map(fn ($i) => (int) $i)->all();

        DB::transaction(function () use ($asamblea, $punto, $casa, $data, $registra, $opcionesValidas) {
            $this->escribirVoto($asamblea, $punto, $casa, $data, $registra->id, 'mesa', $opcionesValidas);
            $this->asegurarPresenciaEnSala($asamblea, $casa, $registra);
        });
    }

    /**
     * "Por unanimidad" (o mayoría abrumadora a mano alzada): le pone el mismo
     * voto a todas las casas PRESENTES que aún no votaron en este punto. No
     * pisa lo que alguien ya votó desde la plataforma.
     *
     * @return int casas a las que se les registró el voto
     */
    public function registrarUnanimidad(Asamblea $asamblea, AsambleaPunto $punto, string $valor, User $registra): int
    {
        if ($punto->tipo !== 'si_no') {
            throw new \RuntimeException('La unanimidad solo aplica a puntos de a favor / en contra.');
        }
        $this->validarRegistroMesa($asamblea, $punto, null);

        $yaVotaron = AsambleaVoto::where('punto_id', $punto->id)->pluck('casa')->all();
        $pendientes = array_values(array_filter(
            $this->casas(),
            fn ($casa) => $this->casaPresente($asamblea, $casa) && ! in_array($casa, $yaVotaron, true)
        ));

        if (! in_array($valor, ['a_favor', 'en_contra', 'abstencion'], true)) {
            throw new \RuntimeException('Valor de voto inválido.');
        }
        if ($pendientes === []) {
            throw new \RuntimeException('No hay casas presentes pendientes de votar. Marca primero la asistencia.');
        }

        DB::transaction(function () use ($asamblea, $punto, $pendientes, $valor, $registra) {
            foreach ($pendientes as $casa) {
                $this->escribirVoto($asamblea, $punto, (string) $casa, ['valor' => $valor], $registra->id, 'mesa', []);
            }
        });

        return count($pendientes);
    }

    /**
     * Corrige un error de captura: quita el voto que la mesa registró para una
     * casa. Los votos emitidos desde la plataforma no se pueden quitar aquí.
     */
    public function quitarVotoMesa(Asamblea $asamblea, AsambleaPunto $punto, string $casa): int
    {
        if ($asamblea->estado === 'cerrada') {
            throw new \RuntimeException('La asamblea ya está cerrada.');
        }
        if (! $this->columnasNuevas()) {
            throw new \RuntimeException('Falta ejecutar /migrar para habilitar esta función.');
        }

        return AsambleaVoto::where('punto_id', $punto->id)
            ->where('casa', $casa)
            ->where('origen', 'mesa')
            ->delete();
    }

    /**
     * Reglas comunes del registro de la mesa.
     *
     * @throws \RuntimeException
     */
    private function validarRegistroMesa(Asamblea $asamblea, AsambleaPunto $punto, ?string $casa): void
    {
        if (! $this->columnasNuevas()) {
            throw new \RuntimeException('Falta ejecutar /migrar para habilitar el registro de votos en sala.');
        }
        if ($asamblea->estado !== 'en_curso') {
            throw new \RuntimeException('La asamblea debe estar en curso para registrar votos.');
        }
        if ($punto->asamblea_id !== $asamblea->id) {
            throw new \RuntimeException('El punto no pertenece a esta asamblea.');
        }
        if (! in_array($punto->tipo, self::TIPOS_VOTABLES, true)) {
            throw new \RuntimeException('Este punto es informativo: no se vota.');
        }
        if ($punto->estado === 'cerrado') {
            throw new \RuntimeException('Este punto ya está cerrado.');
        }

        if ($casa !== null) {
            if (! in_array($casa, $this->casas(), true)) {
                throw new \RuntimeException("La casa {$casa} no existe.");
            }

            // En una asamblea solo por plataforma la mesa registra únicamente a
            // quien no puede entrar; si la casa sí puede, que vote ella misma.
            if ($this->modalidad($asamblea) === 'plataforma' && $this->casaTieneAcceso($asamblea, $casa)) {
                throw new \RuntimeException("La casa {$casa} puede votar desde la plataforma. Cambia la modalidad a «mixta» si se votará en sala.");
            }
        }
    }

    /** Votar en sala implica estar presente: se registra la asistencia si faltaba. */
    private function asegurarPresenciaEnSala(Asamblea $asamblea, string $casa, User $registra): void
    {
        if ($this->casaPresente($asamblea, $casa)) {
            return;
        }

        $titular = $this->titularCasaId($asamblea, $casa);
        AsambleaAsistencia::updateOrCreate(
            ['asamblea_id' => $asamblea->id, 'casa' => $casa],
            [
                'user_id' => $titular ?? $registra->id,
                'confirmada' => true,
                'registrada_por' => $registra->id,
                'modo' => 'mesa',
                'nota' => 'Registrada al votar en sala',
            ]
        );
    }

    /**
     * Casas que votaron en un punto, separadas por cómo se registró el voto.
     *
     * @return array{plataforma:int, mesa:int}
     */
    public function votosPorOrigen(AsambleaPunto $punto): array
    {
        $cuenta = ['plataforma' => 0, 'mesa' => 0];

        $filas = AsambleaVoto::where('punto_id', $punto->id)->get(['casa', 'origen']);
        foreach ($filas->groupBy('casa') as $votosDeCasa) {
            $origen = (string) ($votosDeCasa->first()->origen ?? 'plataforma');
            $cuenta[$origen === 'mesa' ? 'mesa' : 'plataforma']++;
        }

        return $cuenta;
    }

    /**
     * Voto que hay registrado por casa en un punto (para que la mesa lo vea y
     * lo corrija).
     *
     * @return array<string, array<string, mixed>>
     */
    public function votosPorCasa(AsambleaPunto $punto): array
    {
        $mapa = [];
        foreach (AsambleaVoto::where('punto_id', $punto->id)->orderBy('posicion')->get() as $v) {
            if (! isset($mapa[$v->casa])) {
                $mapa[$v->casa] = [
                    'valor' => $v->valor,
                    'opcion_id' => $v->opcion_id,
                    'origen' => $v->origen ?? 'plataforma',
                ];
            }
        }

        return $mapa;
    }

    /**
     * Resultado de un punto según su tipo.
     */
    public function resultadosPunto(AsambleaPunto $punto): array
    {
        $punto->loadMissing('opciones');

        if ($punto->tipo === 'informativo') {
            return [
                'tipo' => 'informativo',
                'total_casas' => 0,
                'resolucion' => $punto->resolucion ?? null,
            ];
        }

        $origen = $this->votosPorOrigen($punto);

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
                'origen' => $origen,
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
                'origen' => $origen,
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
                'origen' => $origen,
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
