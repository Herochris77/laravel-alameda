<?php

namespace App\Services;

use App\Models\Detallepago;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

/**
 * Genera el recibo de pago en PDF.
 *
 * Existe un solo documento, no dos: el que descarga el vecino y el que
 * imprime la tesorería son idénticos salvo por la firma. Tenerlos en un solo
 * lugar evita que con el tiempo digan cosas distintas del mismo pago.
 *
 *   firmar: true  -> lleva la firma digitalizada de quien validó el pago.
 *                    Es el que el vecino descarga desde su sesión.
 *   firmar: false -> deja la línea en blanco para firmar de puño.
 *                    Es el que la tesorería entrega en mano.
 */
class ReciboService
{
    private const CARPETA_FIRMAS = 'firmas';

    public function generar(Detallepago $detalle, bool $firmar = true): string
    {
        Carbon::setLocale('es');

        // PdfService es lo que permite incrustar el logo y la firma: define
        // el chroot desde el que dompdf puede leer imágenes del disco.
        $dompdf = PdfService::crear('A4', 'portrait');
        $dompdf->loadHtml($this->html($detalle, $firmar));
        $dompdf->render();

        return $dompdf->output();
    }

    private function html(Detallepago $detalle, bool $firmar): string
    {
        $firmante = $this->firmante($detalle);

        return view('usuario.pago.recibo-pdf', [
            'pago' => $detalle,
            'nombre' => $detalle->user->nombre ?? 'Usuario',
            'casa' => $detalle->user->casa ?? 'N/A',
            'tipo' => $detalle->user->tipo ?? 'residente',
            'concepto' => $detalle->pago->concepto,
            'cantidad' => number_format($detalle->cantidad_pago, 2),
            'vencimiento' => Carbon::parse($detalle->pago->vencimiento)->format('d/m/Y'),

            // Fecha real del movimiento, no la de validación: el recibo debe
            // decir cuándo pagó el vecino, no cuándo se enteró el sistema.
            'fechaPago' => optional($detalle->fechaEfectivaPago())
                ->translatedFormat('d \d\e F \d\e Y') ?? 'No registrada',

            'fechaValidacion' => Carbon::parse($detalle->updated_at)
                ->translatedFormat('d \d\e F \d\e Y'),

            'fechaActual' => now()->translatedFormat('d \d\e F \d\e Y \a \l\a\s H:i'),

            // --- Bloque de firma ---
            'formaPago' => $detalle->formaPagoTexto(),
            'esEfectivo' => $detalle->esEfectivo(),
            'folioRecibo' => $detalle->folio_recibo,
            'firmanteNombre' => $firmante?->nombre,
            'firmanteCargo' => $this->cargoDe($firmante),
            'firmaImagen' => $firmar ? $this->rutaFirma($firmante) : null,

            // Solo se fija el alto: el ancho queda libre para que la firma
            // conserve su proporción, sea apaisada o casi cuadrada.
            'firmaAlto' => (int) config('privacidad.firma_alto', 62),
        ])->render();
    }

    /**
     * Quién firma: el que validó el pago.
     *
     * Si el pago se validó antes de que existiera este registro, se cae al
     * tesorero en funciones. No es exacto para un recibo antiguo, pero es
     * mejor que dejar el documento sin responsable identificado.
     */
    private function firmante(Detallepago $detalle): ?User
    {
        if ($detalle->validado_por && $detalle->validador) {
            return $detalle->validador;
        }

        return User::where('cargo', 'tesorero')
            ->where('estado', 1)
            ->first();
    }

    private function cargoDe(?User $u): string
    {
        if (! $u) {
            return 'Tesorería · Mesa Directiva';
        }

        $cargo = User::CARGOS[$u->cargo] ?? 'Mesa Directiva';

        return $cargo.' · Mesa Directiva';
    }

    /**
     * Ruta ABSOLUTA en disco de la firma.
     *
     * dompdf lee el archivo del sistema de archivos; una URL obligaría a que
     * el servidor se pida la imagen a sí mismo, que es justo lo que falla en
     * hosting compartido.
     */
    private function rutaFirma(?User $u): ?string
    {
        if (! $u || ! $u->firma) {
            return null;
        }

        $ruta = Storage::disk('public')->path(self::CARPETA_FIRMAS.'/'.$u->firma);

        return is_file($ruta) ? $ruta : null;
    }
}
