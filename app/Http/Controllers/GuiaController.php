<?php

namespace App\Http\Controllers;

use App\Services\PdfService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

/**
 * Guías de uso en PDF.
 *
 * Se generan desde el sistema y no se guardan como archivo suelto para que
 * no se queden atrás: si mañana cambia un módulo, se corrige la plantilla y
 * la siguiente descarga ya sale bien. Un PDF pegado en un correo envejece sin
 * que nadie se entere.
 */
class GuiaController extends Controller
{
    public function vecinos()
    {
        return $this->generar('guias.vecinos', 'guia-vecinos-alameda');
    }

    public function mesa()
    {
        return $this->generar('guias.mesa', 'guia-mesa-directiva-alameda');
    }

    private function generar(string $vista, string $nombre)
    {
        try {
            Carbon::setLocale('es');

            $dompdf = PdfService::crear('LETTER', 'portrait');
            $dompdf->loadHtml(view($vista, ['generado' => Carbon::now()])->render());
            $dompdf->render();

            return response($dompdf->output(), 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="'.$nombre.'.pdf"',
            ]);
        } catch (\Exception $e) {
            Log::error('Error al generar la guía: '.$e->getMessage());

            abort(500, 'No se pudo generar la guía.');
        }
    }
}
