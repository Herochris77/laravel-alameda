<?php

use App\Models\Asamblea;
use App\Models\AsambleaOpcion;
use App\Models\AsambleaPunto;
use App\Models\User;
use App\Services\AsambleaService;

beforeEach(function () {
    $this->svc = app(AsambleaService::class);
});

function mkUser(string $casa, string $tipo): User
{
    static $n = 0;
    $n++;

    return User::create([
        'nombre' => ucfirst($tipo).' '.$casa,
        'correo' => $tipo.'-'.$casa.'-'.$n.'@test.mx',
        'pass' => bcrypt('secret'),
        'celular' => '0000000000',
        'casa' => $casa,
        'tipo' => $tipo,
        'rol' => 'usuario',
        'pago' => 1,
        'estado' => 1,
    ]);
}

function mkAsamblea(string $estado = 'en_curso', bool $control = false, int $quorum = 50): Asamblea
{
    return Asamblea::create([
        'titulo' => 'Asamblea de prueba',
        'estado' => $estado,
        'quorum_pct' => $quorum,
        'control_asistencia' => $control,
        'created_by' => 1,
    ]);
}

function mkPunto(Asamblea $a, string $tipo, array $opciones = []): AsambleaPunto
{
    $p = AsambleaPunto::create([
        'asamblea_id' => $a->id,
        'orden' => 1,
        'titulo' => 'Punto '.$tipo,
        'tipo' => $tipo,
        'estado' => 'abierto',
    ]);
    foreach ($opciones as $i => $op) {
        AsambleaOpcion::create(['punto_id' => $p->id, 'opcion' => $op, 'orden' => $i + 1]);
    }

    return $p;
}

test('el dueño ejerce el voto de su casa por defecto', function () {
    $d = mkUser('1', 'dueño');
    $a = mkAsamblea();

    expect($this->svc->casasDeUsuario($a, $d))->toBe(['1']);
    expect($this->svc->titularCasaId($a, '1'))->toBe($d->id);
});

test('un inquilino no vota sin poder y sí con el poder de su dueño', function () {
    $dueno = mkUser('12', 'dueño');
    $inq = mkUser('12', 'inquilino');
    $a = mkAsamblea();

    expect($this->svc->casasDeUsuario($a, $inq))->toBe([]);

    $this->svc->delegar($a, '12', $dueno, $inq->id, 'inquilino');

    expect($this->svc->casasDeUsuario($a, $inq))->toBe(['12']);
    expect($this->svc->casasDeUsuario($a, $dueno))->toBe([]); // el dueño ya no vota
    expect($this->svc->titularCasaId($a, '12'))->toBe($inq->id);
});

test('un dueño no puede dar poder de inquilino a un inquilino de otra casa', function () {
    $dueno = mkUser('5', 'dueño');
    $inqOtra = mkUser('9', 'inquilino');
    $a = mkAsamblea();

    $this->svc->delegar($a, '5', $dueno, $inqOtra->id, 'inquilino');
})->throws(RuntimeException::class);

test('representación de un vecino a otro acumula votos', function () {
    $d1 = mkUser('1', 'dueño');
    $d2 = mkUser('2', 'dueño');
    $a = mkAsamblea();

    $this->svc->delegar($a, '2', $d2, $d1->id, 'vecino');

    $casas = $this->svc->casasDeUsuario($a, $d1);
    sort($casas);
    expect($casas)->toBe(['1', '2']);
    expect($this->svc->casasDeUsuario($a, $d2))->toBe([]);
});

test('solo el dueño de la casa puede delegar', function () {
    $d1 = mkUser('1', 'dueño');
    $d2 = mkUser('2', 'dueño');
    $a = mkAsamblea();

    // d1 intenta delegar la casa 2 (que no es suya)
    $this->svc->delegar($a, '2', $d1, $d2->id, 'vecino');
})->throws(RuntimeException::class);

test('un voto por casa: emitir de nuevo reemplaza el anterior', function () {
    $d = mkUser('1', 'dueño');
    $a = mkAsamblea();
    $p = mkPunto($a, 'si_no');

    $this->svc->emitirVoto($a, $p, $d, ['valor' => 'a_favor']);
    $r = $this->svc->resultadosPunto($p);
    expect($r['conteo']['a_favor'])->toBe(1);

    $this->svc->emitirVoto($a, $p, $d, ['valor' => 'en_contra']);
    $r = $this->svc->resultadosPunto($p);
    expect($r['conteo']['a_favor'])->toBe(0);
    expect($r['conteo']['en_contra'])->toBe(1);
    expect($r['total_casas'])->toBe(1);
});

test('resultado si/no calcula aprobado correctamente', function () {
    $d1 = mkUser('1', 'dueño');
    $d2 = mkUser('2', 'dueño');
    $d3 = mkUser('3', 'dueño');
    $a = mkAsamblea();
    $p = mkPunto($a, 'si_no');

    $this->svc->emitirVoto($a, $p, $d1, ['valor' => 'a_favor']);
    $this->svc->emitirVoto($a, $p, $d2, ['valor' => 'a_favor']);
    $this->svc->emitirVoto($a, $p, $d3, ['valor' => 'en_contra']);

    $r = $this->svc->resultadosPunto($p);
    expect($r['conteo'])->toBe(['a_favor' => 2, 'en_contra' => 1, 'abstencion' => 0]);
    expect($r['aprobado'])->toBeTrue();
});

test('opciones cuenta por casa y determina ganador', function () {
    $d1 = mkUser('1', 'dueño');
    $d2 = mkUser('2', 'dueño');
    $d3 = mkUser('3', 'dueño');
    $a = mkAsamblea();
    $p = mkPunto($a, 'opciones', ['Planilla A', 'Planilla B']);
    $opciones = $p->opciones()->orderBy('id')->pluck('id')->all();
    [$A, $B] = $opciones;

    $this->svc->emitirVoto($a, $p, $d1, ['opcion_id' => $A]);
    $this->svc->emitirVoto($a, $p, $d2, ['opcion_id' => $A]);
    $this->svc->emitirVoto($a, $p, $d3, ['opcion_id' => $B]);

    $r = $this->svc->resultadosPunto($p);
    expect($r['ganador'])->toBe('Planilla A');
    expect($r['resultados'][0]['votos'])->toBe(2);
});

test('ordenamiento calcula el ranking por puntos de Borda', function () {
    $d1 = mkUser('1', 'dueño');
    $d2 = mkUser('2', 'dueño');
    $a = mkAsamblea();
    $p = mkPunto($a, 'ordenamiento', ['A', 'B', 'C']);
    $ids = $p->opciones()->orderBy('id')->pluck('id')->all();
    [$A, $B, $C] = $ids;

    // d1: A,B,C  ·  d2: A,C,B  -> A=6, B=3, C=3
    $this->svc->emitirVoto($a, $p, $d1, ['orden' => [$A, $B, $C]]);
    $this->svc->emitirVoto($a, $p, $d2, ['orden' => [$A, $C, $B]]);

    $r = $this->svc->resultadosPunto($p);
    expect($r['ganador'])->toBe('A');
    expect($r['ranking'][0]['puntos'])->toBe(6);
    expect($r['total_casas'])->toBe(2);
});

test('un representante con dos casas emite dos votos', function () {
    $d1 = mkUser('1', 'dueño');
    $d2 = mkUser('2', 'dueño');
    $a = mkAsamblea();
    $p = mkPunto($a, 'si_no');

    $this->svc->delegar($a, '2', $d2, $d1->id, 'vecino');

    $emitidas = $this->svc->emitirVoto($a, $p, $d1, ['valor' => 'a_favor']);
    expect($emitidas)->toBe(2);

    $r = $this->svc->resultadosPunto($p);
    expect($r['conteo']['a_favor'])->toBe(2);
});

test('no se puede votar si la asamblea no está en curso', function () {
    $d = mkUser('1', 'dueño');
    $a = mkAsamblea('convocada');
    $p = mkPunto($a, 'si_no');

    $this->svc->emitirVoto($a, $p, $d, ['valor' => 'a_favor']);
})->throws(RuntimeException::class);

test('con control de asistencia, no se puede votar sin pase de lista', function () {
    $d = mkUser('1', 'dueño');
    $admin = mkUser('99', 'dueño');
    $a = mkAsamblea('en_curso', true);
    $p = mkPunto($a, 'si_no');

    $error = null;
    try {
        $this->svc->emitirVoto($a, $p, $d, ['valor' => 'a_favor']);
    } catch (RuntimeException $e) {
        $error = $e->getMessage();
    }
    expect($error)->not->toBeNull();

    // El administrador registra la casa como presente y ahora sí puede votar.
    $this->svc->marcarPresente($a, '1', $admin, true);
    $emitidas = $this->svc->emitirVoto($a, $p, $d, ['valor' => 'a_favor']);
    expect($emitidas)->toBe(1);
});

test('con control de asistencia, el representante vota solo por casas presentes', function () {
    $rep = mkUser('1', 'dueño');
    $otro = mkUser('2', 'dueño');
    $admin = mkUser('99', 'dueño');
    $a = mkAsamblea('en_curso', true);
    $p = mkPunto($a, 'si_no');

    $this->svc->delegar($a, '2', $otro, $rep->id, 'vecino'); // rep ejerce casas 1 y 2
    $this->svc->marcarPresente($a, '1', $admin, true);       // solo casa 1 presente

    expect($this->svc->emitirVoto($a, $p, $rep, ['valor' => 'a_favor']))->toBe(1);

    $this->svc->marcarPresente($a, '2', $admin, true);       // ahora ambas
    expect($this->svc->emitirVoto($a, $p, $rep, ['valor' => 'a_favor']))->toBe(2);
    expect($this->svc->resultadosPunto($p)['conteo']['a_favor'])->toBe(2);
});

test('el quórum cuenta las casas presentes confirmadas', function () {
    $d1 = mkUser('1', 'dueño');
    $d2 = mkUser('2', 'dueño');
    $d3 = mkUser('3', 'dueño');
    $d4 = mkUser('4', 'dueño');
    $admin = mkUser('99', 'dueño');
    $a = mkAsamblea('en_curso', true, 50);

    $this->svc->marcarPresente($a, '1', $admin, true);
    $this->svc->marcarPresente($a, '2', $admin, true);

    $q = $this->svc->quorum($a);
    expect($q['total'])->toBe(5);        // 5 dueños = 5 casas
    expect($q['presentes'])->toBe(2);
    expect($q['pct'])->toBe(40.0);
    expect($q['alcanzado'])->toBeFalse();

    $this->svc->marcarPresente($a, '3', $admin, true);
    expect($this->svc->quorum($a)['alcanzado'])->toBeTrue(); // 3/5 = 60% >= 50%
});
