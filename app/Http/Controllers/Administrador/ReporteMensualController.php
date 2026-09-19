<?php

namespace App\Http\Controllers\Administrador;

use App\Http\Controllers\Controller;
use App\Models\CorteMensual;
use App\Services\ReporteMensualService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 * Reporte mensual de ingresos y egresos.
 *
 * Reproduce el documento que la mesa directiva entrega cada mes. Los ingresos
 * y egresos salen del sistema; lo único que se captura es el saldo del banco y,
 * cuando lo hay, el efectivo al cierre, porque eso no vive en la plataforma.
 *
 * El saldo inicial no se pide: se arrastra del cierre del mes anterior.
 */
class ReporteMensualController extends Controller
{
    public function __construct(private ReporteMensualService $servicio) {}

    public function index(Request $request)
    {
        $periodos = $this->servicio->periodosDisponibles();

        $periodo = $request->input('periodo', $periodos[0] ?? now()->format('Y-m'));

        $datos = $this->servicio->datos($periodo);

        return view('administrador.reporte-mensual', compact('datos', 'periodos', 'periodo'));
    }

    /**
     * Guarda el cierre del mes: lo que dice el estado de cuenta.
     */
    public function guardarCierre(Request $request)
    {
        try {
            $request->validate([
                'periodo' => 'required|date_format:Y-m',
                'saldo_banco' => 'required|numeric|min:0',
                'saldo_efectivo' => 'nullable|numeric|min:0',
                'notas' => 'nullable|string|max:500',
            ], [
                'periodo.date_format' => 'El periodo debe tener el formato AAAA-MM.',
            ]);

            CorteMensual::updateOrCreate(
                ['periodo' => $request->periodo],
                [
                    'saldo_banco' => $request->saldo_banco,
                    // Vacío quiere decir que no se maneja efectivo, no un dato
                    // pendiente de capturar.
                    'saldo_efectivo' => $request->saldo_efectivo ?: 0,
                    'notas' => $request->notas,
                    'created_by' => Auth::user()->id,
                ]
            );

            return response()->json([
                'header' => 'Cierre guardado ✅',
                'success' => true,
                'message' => 'El saldo del mes quedó registrado. El mes siguiente arrancará desde aquí.',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'header' => '⚠️ Faltan datos',
                'success' => false,
                'message' => implode(' ', $e->validator->errors()->all()),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error al guardar el cierre mensual: '.$e->getMessage());

            return response()->json([
                'header' => '❌ Error',
                'success' => false,
                'message' => 'No se pudo guardar el cierre.',
            ], 500);
        }
    }

    /**
     * Genera el PDF con el formato del reporte de papel.
     */
    public function pdf(Request $request)
    {
        $periodo = $request->input('periodo', now()->format('Y-m'));

        Carbon::setLocale('es');

        $datos = $this->servicio->datos($periodo);

        $dompdf = \App\Services\PdfService::crear('LETTER', 'portrait');

        $dompdf->loadHtml(
            view('administrador.reporte-mensual-pdf', compact('datos'))->render()
        );

        $dompdf->render();

        return response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="reporte-ingresos-egresos-'.$periodo.'.pdf"',
        ]);
    }
}
