<?php

namespace Tests\Unit;

use App\Models\Detallepago;
use App\Models\Pago;
use Illuminate\Database\Capsule\Manager as Capsule;
use PHPUnit\Framework\TestCase;

/**
 * Prueba pura de la regla: un recibo exonerado no cobra recargo, y el resto
 * del cálculo (monto esperado, excedente, faltante) lo respeta.
 */
class RecargoExoneradoTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Los modelos necesitan una conexión para resolver formatos de fecha; no se usa la base real.
        $capsula = new Capsule();
        $capsula->addConnection(['driver' => 'sqlite', 'database' => ':memory:']);
        $capsula->setAsGlobal();
        $capsula->bootEloquent();
    }

    private function recibo(bool $exonerado, string $fechaPago, float $pagado = 0.0): Detallepago
    {
        $concepto = new Pago(['concepto' => 'Mantenimiento', 'cantidad' => 650, 'vencimiento' => '2026-11-18', 'recargo_pct' => 10]);

        $d = new Detallepago([
            'fecha_pago' => $fechaPago,
            'cantidad_pago' => $pagado,
            'recargo_exonerado' => $exonerado,
        ]);
        $d->setRelation('pago', $concepto);

        return $d;
    }

    public function test_pago_tardio_sin_acuerdo_cobra_recargo(): void
    {
        $d = $this->recibo(false, '2026-11-25');

        $this->assertSame(65.0, $d->recargoAplicable());
        $this->assertSame(715.0, $d->montoEsperado());
        $this->assertSame(0.0, $d->recargoCondonado());
    }

    public function test_pago_tardio_con_acuerdo_no_cobra_recargo(): void
    {
        $d = $this->recibo(true, '2026-11-25', 650);

        $this->assertSame(0.0, $d->recargoAplicable());
        $this->assertSame(650.0, $d->montoEsperado());
        $this->assertSame(65.0, $d->recargoCondonado());   // lo que se habría cobrado
        $this->assertSame(0.0, $d->desglosePago()['faltante']);
        $this->assertSame(0.0, $d->desglosePago()['excedente']);
    }

    public function test_sin_acuerdo_pagar_solo_la_cuota_deja_faltante(): void
    {
        $d = $this->recibo(false, '2026-11-25', 650);

        $this->assertSame(65.0, $d->desglosePago()['faltante']);
    }

    public function test_si_ya_pago_con_recargo_y_luego_se_exonera_lo_demas_es_excedente(): void
    {
        $d = $this->recibo(true, '2026-11-25', 715);

        $this->assertSame(65.0, $d->desglosePago()['excedente']);   // pasa a saldo a favor
    }

    public function test_pago_puntual_no_tiene_nada_que_condonar(): void
    {
        $d = $this->recibo(true, '2026-11-10', 650);

        $this->assertSame(0.0, $d->recargoAplicable());
        $this->assertSame(0.0, $d->recargoCondonado());
    }

    public function test_recibo_sin_la_columna_se_comporta_como_no_exonerado(): void
    {
        $concepto = new Pago(['cantidad' => 650, 'vencimiento' => '2026-11-18', 'recargo_pct' => 10]);
        $d = new Detallepago(['fecha_pago' => '2026-11-25']);
        $d->setRelation('pago', $concepto);

        $this->assertFalse($d->recargoExonerado());
        $this->assertSame(65.0, $d->recargoAplicable());
    }
}
