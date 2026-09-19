<?php

namespace App\Http\Controllers\Usuario;

use App\Http\Controllers\Controller;
use App\Models\Detallepago;
use App\Models\Pago;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Dompdf\Options;

class PagoController extends Controller
{
    /**
     * Muestra la página de pagos del usuario.
     *
     * IMPORTANTE:
     * Se conserva la lógica original de clasificación
     * porque ya está funcionando correctamente en producción.
     */
    public function index(Request $request)
    {
        $userId = Auth::user()->id;

        $porPagina = 9;

        /*
         * =====================================================
         * TODOS LOS PAGOS DEL USUARIO
         * =====================================================
         */

        $pagos = Detallepago::with('pago')
            ->where('user_id', $userId)
            ->get();

        /*
         * =====================================================
         * VENCIDOS
         * =====================================================
         *
         * - estado pendiente
         * - no tiene comprobante
         * - la fecha de vencimiento ya pasó
         */

        $vencidos = $pagos
            ->filter(function ($pago) {
                return $pago->estado === 'pendiente'
                    && is_null($pago->path_pago)
                    && $pago->pago
                    && $pago->pago->vencimiento
                    && Carbon::parse(
                        $pago->pago->vencimiento
                    )->endOfDay()->isPast();
            })
            ->sortByDesc('created_at')
            ->values();

        /*
         * =====================================================
         * PENDIENTES
         * =====================================================
         *
         * Se conserva EXACTAMENTE el concepto de tu sistema:
         *
         * - sigue pendiente
         *
         * Y además:
         *
         * - tiene comprobante subido
         *
         * O
         *
         * - todavía no vence.
         */

        $pendientes = $pagos
            ->filter(function ($pago) {
                if ($pago->estado !== 'pendiente') {
                    return false;
                }

                /*
                 * Si tiene comprobante:
                 * permanece en pendientes mientras se revisa.
                 */
                if (! is_null($pago->path_pago)) {
                    return true;
                }

                /*
                 * Si por algún motivo no existe
                 * el pago relacionado o vencimiento,
                 * lo mantenemos como pendiente
                 * para no ocultarlo de la interfaz.
                 */
                if (
                    ! $pago->pago
                    || ! $pago->pago->vencimiento
                ) {
                    return true;
                }

                /*
                 * Todavía no venció.
                 */
                return ! Carbon::parse(
                    $pago->pago->vencimiento
                )
                    ->endOfDay()
                    ->isPast();
            })
            ->sortByDesc('created_at')
            ->values();

        /*
         * =====================================================
         * APROBADOS
         * =====================================================
         *
         * El estado correcto en tu sistema es "pagado".
         */

        $aprobados = $pagos
            ->where('estado', 'pagado')
            ->sortByDesc('updated_at')
            ->values();

        /*
         * =====================================================
         * RECHAZADOS
         * =====================================================
         */

        $rechazados = $pagos
            ->where('estado', 'rechazado')
            ->sortByDesc('updated_at')
            ->values();

        /*
         * =====================================================
         * PESTAÑA ACTIVA
         * =====================================================
         */

        $tabsPermitidas = [
            'pendientes',
            'vencidos',
            'aprobados',
            'rechazados',
        ];

        $tabActivo = $request->query(
            'tab',
            'pendientes'
        );

        if (
            ! in_array(
                $tabActivo,
                $tabsPermitidas,
                true
            )
        ) {
            $tabActivo = 'pendientes';
        }

        /*
         * =====================================================
         * COLECCIÓN ACTIVA
         * =====================================================
         *
         * Solo se pagina la pestaña que el usuario está viendo.
         *
         * Las colecciones completas se conservan
         * para mostrar los contadores.
         */

        switch ($tabActivo) {
            case 'vencidos':
                $coleccionActiva = $vencidos;
                break;

            case 'aprobados':
                $coleccionActiva = $aprobados;
                break;

            case 'rechazados':
                $coleccionActiva = $rechazados;
                break;

            case 'pendientes':
            default:
                $coleccionActiva = $pendientes;
                break;
        }

        /*
         * =====================================================
         * PAGINACIÓN
         * =====================================================
         */

        $pagosPaginados = $this->paginarColeccion(
            $coleccionActiva,
            $porPagina,
            $tabActivo
        );

        /*
         * =====================================================
         * SALDO A FAVOR
         * =====================================================
         *
         * Dinero que el vecino pagó de más y que se aplicará solo
         * a su próxima cuota.
         */
        $saldos = app(\App\Services\SaldoService::class);

        $saldoAFavor = $saldos->saldo($userId);

        $movimientosSaldo = $saldoAFavor != 0.0
            ? $saldos->movimientos($userId)
            : collect();

        return view(
            'usuario.pago.index',
            compact(
                'pendientes',
                'vencidos',
                'aprobados',
                'rechazados',
                'pagosPaginados',
                'tabActivo',
                'saldoAFavor',
                'movimientosSaldo'
            )
        );
    }

    /**
     * Pagina una Collection de Laravel sin modificar
     * las colecciones originales utilizadas para los contadores.
     */
    private function paginarColeccion(
        $coleccion,
        int $porPagina,
        string $tabActivo
    ): LengthAwarePaginator {
        $total = $coleccion->count();

        $ultimaPagina = max(
            1,
            (int) ceil(
                $total / $porPagina
            )
        );

        $paginaSolicitada = max(
            1,
            (int) request()->query(
                'page',
                1
            )
        );

        $paginaActual = min(
            $paginaSolicitada,
            $ultimaPagina
        );

        $elementos = $coleccion
            ->slice(
                ($paginaActual - 1) * $porPagina,
                $porPagina
            )
            ->values();

        return new LengthAwarePaginator(
            $elementos,
            $total,
            $porPagina,
            $paginaActual,
            [
                'path' => request()->url(),

                'pageName' => 'page',

                'query' => [
                    'tab' => $tabActivo,
                ],
            ]
        );
    }

    /**
     * Muestra el detalle de un pago específico.
     */
    public function detalle($id)
    {
        $pago = Detallepago::with('pago')
            ->where('id', $id)
            ->where(
                'user_id',
                Auth::user()->id
            )
            ->firstOrFail();

        return view(
            'usuario.pago.detalle',
            compact('pago')
        );
    }

    /**
     * Permite al usuario subir un comprobante de pago.
     */
    public function subirComprobante(
        Request $request,
        $id
    ) {
        /*
         * Se conserva la validación de tu backend productivo.
         */
        $request->validate([
            'comprobante' =>
                'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',

            'cantidad_pago' =>
                'required|numeric|min:0',

            /*
             * Fecha real en que el vecino hizo el movimiento.
             * No puede ser futura. Tesorería puede corregirla al validar.
             */
            'fecha_pago' =>
                'required|date|before_or_equal:today',
        ]);

        $detallePago = Detallepago::where(
            'id',
            $id
        )
            ->where(
                'user_id',
                Auth::user()->id
            )
            ->firstOrFail();

        /*
         * Si ya existe un comprobante,
         * se elimina antes de guardar el nuevo.
         */
        if (
            $detallePago->path_pago
            &&
            Storage::disk('public')
                ->exists(
                    $detallePago->path_pago
                )
        ) {
            Storage::disk('public')
                ->delete(
                    $detallePago->path_pago
                );
        }

        /*
         * Guardar nuevo archivo.
         */
        $path = $request
            ->file('comprobante')
            ->store(
                'pagos/comprobantes',
                'public'
            );

        /*
         * Después de subir o sustituir un comprobante,
         * vuelve a estado pendiente para revisión.
         */
        $detallePago->update([
            'path_pago' => $path,

            'cantidad_pago' =>
                $request->cantidad_pago,

            'fecha_pago' =>
                $request->fecha_pago,

            /*
             * El motivo del rechazo anterior deja de aplicar en cuanto el
             * vecino sube un comprobante nuevo: ya corrigió lo que se le
             * pidió y el recibo vuelve a revisión. Dejarlo colgado le haría
             * seguir viendo un reclamo que ya atendió.
             */
            'comentario_rechazo' => null,

            'estado' =>
                'pendiente',
        ]);

        return redirect()
            ->route(
                'usuario.pago.index'
            )
            ->with(
                'success',
                'Comprobante subido correctamente. Queda pendiente de aprobación.'
            );
    }

    /**
     * Descarga el recibo en PDF.
     *
     * Solo disponible para pagos aprobados.
     */
    /**
     * Estado de cuenta del propio vecino.
     *
     * Deliberadamente no recibe un id: siempre se arma con el usuario de la
     * sesión. Así no hay forma de pedir el de otra vivienda cambiando un
     * número en la barra de direcciones.
     */
    public function estadoCuenta()
    {
        Carbon::setLocale('es');

        $datos = app(\App\Services\EstadoCuentaService::class)->datos(Auth::user()->id);

        $dompdf = \App\Services\PdfService::crear('LETTER', 'portrait');
        $dompdf->loadHtml(
            view('administrador.estado-cuenta-pdf', compact('datos'))->render()
        );
        $dompdf->render();

        $casa = preg_replace('/[^A-Za-z0-9]/', '', (string) Auth::user()->casa) ?: 'vivienda';

        return response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="estado-de-cuenta-casa-'.$casa.'.pdf"',
        ]);
    }

    public function descargarRecibo($id)
    {
        /*
         * El recibo se arma en ReciboService, que es el mismo que usa
         * tesorería. La diferencia es esta: aquí va firmado, porque el vecino
         * lo descarga y nadie se lo va a firmar de puño.
         */
        $detallePago = Detallepago::with(['pago', 'user', 'validador'])
            ->where('id', $id)
            ->where('user_id', Auth::user()->id)
            ->where('estado', 'pagado')
            ->firstOrFail();

        $pdf = app(\App\Services\ReciboService::class)->generar($detallePago, firmar: true);

        return response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="recibo-pago-'
                .$detallePago->id.'-'.date('Ymd').'.pdf"',
        ]);
    }
}
