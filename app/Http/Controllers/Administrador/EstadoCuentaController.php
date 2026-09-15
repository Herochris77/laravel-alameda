<?php

namespace App\Http\Controllers\Administrador;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\EstadoCuentaService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Estado de cuenta por vivienda.
 *
 * Responde de un vistazo quién debe y quién trae saldo a favor, y saca el
 * PDF que se le entrega al vecino. La constancia de no adeudo es el mismo
 * dato en formato de documento formal, que es lo que piden al vender o rentar.
 */
class EstadoCuentaController extends Controller
{
    public function __construct(private EstadoCuentaService $servicio) {}

    public function index()
    {
        $viviendas = $this->servicio->resumenGeneral();

        return view('administrador.estado-cuenta', [
            'viviendas' => $viviendas,
            'resumen' => [
                'total' => count($viviendas),
                'al_corriente' => collect($viviendas)->where('al_corriente', true)->count(),
                'con_adeudo' => collect($viviendas)->where('al_corriente', false)->count(),
                'por_cobrar' => round(collect($viviendas)->sum('pendiente'), 2),
                'saldo_favor' => round(collect($viviendas)->sum('saldo_favor'), 2),
            ],
        ]);
    }

    public function ver($id)
    {
        return response()->json([
            'success' => true,
            'datos' => $this->presentar($this->servicio->datos((int) $id)),
        ]);
    }

    public function pdf($id)
    {
        return $this->generarPdf((int) $id, 'administrador.estado-cuenta-pdf', 'estado-de-cuenta');
    }

    /**
     * Constancia de no adeudo.
     *
     * Solo se extiende si la vivienda está realmente al corriente: firmar una
     * constancia falsa es justo lo que no debe poder hacerse por descuido.
     */
    public function constancia($id)
    {
        $datos = $this->servicio->datos((int) $id);

        if (! $datos['al_corriente']) {
            return response()->json([
                'success' => false,
                'header' => '🚫 No se puede extender',
                'message' => 'La casa '.$datos['usuario']->casa.' tiene $'
                    .number_format($datos['totales']['pendiente'], 2)
                    .' pendiente. La constancia de no adeudo solo se extiende a viviendas al corriente.',
            ], 422);
        }

        return $this->generarPdf((int) $id, 'administrador.constancia-pdf', 'constancia-no-adeudo');
    }

    private function generarPdf(int $id, string $vista, string $prefijo)
    {
        try {
            Carbon::setLocale('es');

            $datos = $this->servicio->datos($id);

            $options = new \Dompdf\Options;
            $options->set('isRemoteEnabled', true);
            $options->set('isHtml5ParserEnabled', true);

            $dompdf = new \Dompdf\Dompdf($options);
            $dompdf->setPaper('LETTER', 'portrait');
            $dompdf->loadHtml(view($vista, compact('datos'))->render());
            $dompdf->render();

            $casa = preg_replace('/[^A-Za-z0-9]/', '', (string) $datos['usuario']->casa) ?: $id;

            return response($dompdf->output(), 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="'.$prefijo.'-casa-'.$casa.'.pdf"',
            ]);
        } catch (\Exception $e) {
            Log::error('Error al generar el estado de cuenta: '.$e->getMessage());

            abort(500, 'No se pudo generar el documento.');
        }
    }

    /**
     * Da forma a los datos para la vista previa en pantalla.
     */
    private function presentar(array $d): array
    {
        return [
            'casa' => $d['usuario']->casa,
            'nombre' => $d['usuario']->nombre,
            'al_corriente' => $d['al_corriente'],
            'saldo_favor' => $d['saldo_favor'],
            'neto' => $d['neto'],
            'totales' => $d['totales'],
            'cargos' => collect($d['cargos'])->map(fn ($c) => [
                'concepto' => $c['concepto'],
                'vencimiento' => optional($c['vencimiento'])->format('d/m/Y'),
                'esperado' => $c['esperado'],
                'recargo' => $c['recargo'],
                'estado' => $c['estado'],
                'con_saldo' => $c['con_saldo'],
                'tarde' => $c['tarde'],
                'fecha_pago' => optional($c['fecha_pago'])->format('d/m/Y'),
            ])->all(),
            'multas' => collect($d['multas'])->map(fn ($m) => [
                'motivo' => $m['motivo'],
                'monto' => $m['monto'],
                'pagada' => $m['pagada'],
                'fecha' => optional($m['fecha'])->format('d/m/Y'),
            ])->all(),
        ];
    }
}
