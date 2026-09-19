<?php

namespace App\Http\Controllers\Administrador;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Email\EmailController;
use App\Models\Detallepago;
use App\Models\Pago;
use App\Models\User;
use App\Notifications\NotificacionGenerica;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

Carbon::setLocale('es');

class PagoController extends Controller
{
    protected $sendmail;

    // Inyección de dependecia al controlador de correos
    public function __construct(EmailController $sendmail)
    {
        $this->sendmail = $sendmail;
    }

    public function nuevoPago()
    {
        $usuarios = User::where('estado', 1)->orderBy('nombre')->get(['id', 'nombre', 'tipo', 'casa', 'pago']);

        return view('administrador.pago', compact('usuarios'));
    }

    public function crearPago(Request $request)
    {
        try {
            // 1. Validar los datos de entrada
            $request->validate([
                'concepto' => 'required|string|max:255',
                'cantidad' => 'required|numeric|min:0',
                'vencimiento' => 'required|date|after_or_equal:today',
                // Vacío significa que este concepto no lleva recargo por mora.
                'recargo_pct' => 'nullable|numeric|min:0|max:100',
                'aplica_saldo' => 'nullable|boolean',
            ]);

            // 2. Crear el encabezado del pago
            $datosPago = [
                'concepto' => $request->concepto,
                'cantidad' => $request->cantidad,
                'vencimiento' => $request->vencimiento,
                'recargo_pct' => $request->filled('recargo_pct') ? $request->recargo_pct : null,
                'created_by' => Auth::user()->id,
            ];

            // La columna se agrega con /migrar, y en producción los archivos
            // se suben antes de correrlo. En esa ventana el concepto se crea
            // sin la bandera y se comporta como siempre, en vez de reventar
            // con un error de SQL en la cara del tesorero.
            if (Schema::hasColumn('pagos', 'aplica_saldo')) {
                // Sin el dato explícito se asume que sí, que es como se venía
                // comportando el sistema antes de que existiera la opción.
                $datosPago['aplica_saldo'] = $request->has('aplica_saldo')
                    ? $request->boolean('aplica_saldo')
                    : true;
            }

            $pagoHeader = Pago::create($datosPago);

            // 3. Obtener todos los usuarios en una sola consulta
            if (in_array('todos', $request->usuarios)) {
                // Solo los usuarios autorizados a recibir pagos
                $usuarios = User::where('pago', 1)
                    ->whereNotNull('correo')
                    ->get(['id', 'correo']);
            } else {
                $usuarios = User::whereIn('id', $request->usuarios)
                    ->whereNotNull('correo')
                    ->get(['id', 'correo']);
            }

            // 4. Preparar los datos para Detallepago y envío de correo
            $subjectmail = '🧾 Nuevo recibo de pago';
            $titulomail = 'Nuevo recibo de pago cargado en la plataforma';
            $mensajemail = 'Se cargó un nuevo recibo de pago: <br>
            <strong>Concepto:</strong> '.e($request->concepto).'<br>
            <strong>Cantidad a pagar:</strong> $'.number_format($request->cantidad, 2).'<br>
            <strong>Fecha de Límite de pago:</strong> '.Carbon::parse($request->vencimiento)->translatedFormat('j \\d\\e F \\d\\e Y');

            // 5. Crear detalles de pago y enviar correo por usuario
            $saldos = app(\App\Services\SaldoService::class);
            $liquidadosConSaldo = 0;

            /*
             * El aviso de recibo nuevo es IDÉNTICO para todos: mismo concepto,
             * mismo monto, misma fecha. Antes salía un mensaje SMTP por vecino
             * —44 por recibo—, que en un plan de correo básico se come la
             * cuota diaria de una sola publicación. Se acumulan aquí y al
             * final salen en un solo envío con copia oculta.
             *
             * Los que se cubren con saldo a favor sí llevan correo aparte:
             * ese mensaje incluye el saldo restante de cada quien.
             */
            $destinatariosGenerales = [];

            foreach ($usuarios as $usuario) {
                $pago = Detallepago::create([
                    'pago_id' => $pagoHeader->id,
                    'user_id' => $usuario->id,
                ]);

                // Si el vecino trae saldo a favor suficiente (pagó cuotas por
                // adelantado), el recibo se liquida solo. Antes tenía que
                // volver a subir un comprobante por algo que ya había pagado.
                $pago->setRelation('pago', $pagoHeader);
                $cubiertoConSaldo = $saldos->aplicarA($pago);

                if ($cubiertoConSaldo) {
                    $liquidadosConSaldo++;

                    $usuario->notify(new NotificacionGenerica(
                        'Recibo cubierto con tu saldo a favor',
                        'Se cargó el recibo <strong>'.e($request->concepto).'</strong> por $'
                        .number_format($request->cantidad, 2).
                        ' y se liquidó automáticamente con tu saldo a favor. No tienes que hacer nada.
                        <strong>Saldo restante:</strong> $'.number_format($saldos->saldo($usuario->id), 2),
                        'pagos',
                        'usuario/pago',
                        null,
                        '<i class="check circle icon"></i>'
                    ));

                    try {
                        $this->sendmail->enviarCorreoPersonal(
                            $usuario->correo,
                            '✅ Tu recibo se cubrió con tu saldo a favor',
                            'Recibo liquidado con tu saldo a favor',
                            'Se cargó el recibo <strong>'.e($request->concepto).'</strong> por $'
                            .number_format($request->cantidad, 2).
                            ' y se liquidó automáticamente con el saldo que tenías a favor. No necesitas hacer nada.<br><br>
                            <strong>Saldo restante:</strong> $'.number_format($saldos->saldo($usuario->id), 2)
                        );
                    } catch (\Exception $mailEx) {
                        Log::warning("❌ Error al enviar correo a {$usuario->correo}: ".$mailEx->getMessage());
                    }

                    continue;
                }

                // Notificación in-app Web
                $usuario->notify(new NotificacionGenerica(
                    'Nuevo recibo de pago',
                    'Se cargó un nuevo recibo de pago:
                    <strong>Concepto:</strong> '.e($request->concepto).'
                    <strong>Cantidad a pagar:</strong> $'.number_format($request->cantidad, 2).'
                    <strong>Fecha de Límite de pago:</strong> '.Carbon::parse($request->vencimiento)->translatedFormat('j \\d\\e F \\d\\e Y'),
                    'pagos',
                    'usuario/pago',
                    null,
                    '<i class="money bill wave icon"></i>'
                ));

                $destinatariosGenerales[] = $usuario->correo;
            }

            // Un solo mensaje con copia oculta para todos los demás.
            if (! empty($destinatariosGenerales)) {
                try {
                    \App\Services\MailService::enviar(
                        $destinatariosGenerales,
                        subject: $subjectmail,
                        titulo: $titulomail,
                        mensaje: $mensajemail,
                        origen: 'pago.nuevo-recibo'
                    );
                } catch (\Exception $mailEx) {
                    Log::warning('❌ Error al enviar el aviso de recibo nuevo: '.$mailEx->getMessage());
                }
            }

            $extra = $liquidadosConSaldo > 0
                ? ' '.$liquidadosConSaldo.' recibo(s) se liquidaron automáticamente con saldo a favor.'
                : '';

            return response()->json([
                'success' => true,
                'header' => 'Recibo registrado ✅',
                'message' => 'El recibo fue registrado exitosamente y se notificó a los usuarios.'.$extra,
            ]);
        } catch (\Exception $e) {
            Log::error('❌ Error al crear pago: '.$e->getMessage());

            return response()->json([
                'header' => '❌ Error',
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Reporte consolidado de cobranza, una fila por vecino y concepto.
     *
     * Se entrega como CSV y no como .xlsx a propósito: Excel lo abre igual y
     * permite filtrar y hacer tablas dinámicas, pero no exige instalar una
     * librería nueva, que en el hosting actual (sin acceso a terminal) hay que
     * subir a mano con todo su vendor.
     *
     * Lleva BOM UTF-8 para que Excel respete acentos y eñes al abrirlo.
     */
    public function exportarConsolidado(Request $request)
    {
        $detalles = Detallepago::with(['pago', 'user'])
            ->get()
            ->filter(fn ($d) => $d->pago)
            ->sortBy([
                fn ($a, $b) => strcmp($a->pago->vencimiento ?? '', $b->pago->vencimiento ?? ''),
                fn ($a, $b) => (int) ($a->user->casa ?? 0) <=> (int) ($b->user->casa ?? 0),
            ]);

        return $this->csvDeCobranza($detalles, 'cobranza-alameda-'.now()->format('Y-m-d').'.csv');
    }

    /**
     * Reporte de un solo concepto, desde su pantalla de detalle.
     *
     * Es el mismo formato que el consolidado para que ambos se puedan pegar en
     * la misma hoja de Excel; solo cambia el filtro y el nombre del archivo.
     */
    public function exportarConcepto($id)
    {
        $pago = Pago::findOrFail($id);

        $detalles = Detallepago::with(['pago', 'user'])
            ->where('pago_id', $pago->id)
            ->get()
            ->filter(fn ($d) => $d->pago)
            ->sortBy(fn ($d) => (int) ($d->user->casa ?? 0));

        // Nombre de archivo legible a partir del concepto.
        $slug = mb_strtolower(trim(preg_replace('/[^A-Za-z0-9]+/', '-', $pago->concepto), '-'));

        return $this->csvDeCobranza($detalles, 'cobranza-'.($slug ?: 'concepto').'-'.now()->format('Y-m-d').'.csv');
    }

    /**
     * Arma el CSV de cobranza a partir de una colección de recibos.
     *
     * Se entrega como CSV y no como .xlsx a propósito: Excel lo abre igual y
     * permite filtrar y hacer tablas dinámicas, pero no exige instalar una
     * librería nueva, que en el hosting actual (sin acceso a terminal) hay que
     * subir a mano con todo su vendor.
     */
    private function csvDeCobranza($detalles, string $nombre)
    {
        $saldos = app(\App\Services\SaldoService::class);

        // El saldo es por vecino, no por recibo: se calcula una sola vez.
        $saldoPorUsuario = [];

        $totales = [
            'recibos' => 0,
            'esperado' => 0.0,
            'entro' => 0.0,
            'conSaldo' => 0.0,
            'recargo' => 0.0,
            'excedente' => 0.0,
            'faltante' => 0.0,
        ];

        return response()->streamDownload(function () use ($detalles, $saldos, &$saldoPorUsuario, &$totales) {
            $out = fopen('php://output', 'w');

            // BOM para que Excel no rompa los acentos.
            fwrite($out, "\xEF\xBB\xBF");

            fputcsv($out, [
                'Casa',
                'Vecino',
                'Tipo',
                'Correo',
                'Concepto',
                'Vencimiento',
                'Cuota',
                'Recargo %',
                'Estado',
                'Tiene comprobante',
                'Fecha de pago',
                'Puntualidad',
                'Dias de atraso',
                'Monto pagado',
                'Recargo aplicado',
                'Total esperado',
                'Excedente (a favor)',
                'Faltante',
                // Un recibo cubierto con saldo NO es dinero que entró a la
                // cuenta este mes: entró cuando el vecino adelantó.
                'Cubierto con saldo',
                'Entro a la cuenta',
                'Motivo de rechazo',
                'Saldo a favor del vecino',
            ], ';');

            foreach ($detalles as $d) {
                $x = $d->desglosePago();
                $fecha = $d->fechaEfectivaPago();

                $dias = 0;

                if ($fecha && $d->pago->vencimiento && $d->esPagoTardio()) {
                    $dias = \Carbon\Carbon::parse($d->pago->vencimiento)
                        ->startOfDay()
                        ->diffInDays($fecha->startOfDay());
                }

                $uid = $d->user_id;

                if (! array_key_exists($uid, $saldoPorUsuario)) {
                    $saldoPorUsuario[$uid] = $saldos->saldo($uid);
                }

                // Dinero que de verdad llegó a la cuenta por este recibo.
                $conSaldo = $d->fueLiquidadoConSaldo();
                $entroACuenta = ($d->estado === 'pagado' && ! $conSaldo)
                    ? (float) ($d->cantidad_pago ?: 0)
                    : 0.0;

                $totales['recibos']++;
                $totales['esperado'] += $x['esperado'];
                $totales['entro'] += $entroACuenta;
                $totales['faltante'] += $x['faltante'];

                if ($conSaldo) {
                    $totales['conSaldo'] += (float) ($d->cantidad_pago ?: 0);
                } else {
                    $totales['recargo'] += $x['recargo'];
                    $totales['excedente'] += $x['excedente'];
                }

                fputcsv($out, [
                    $d->user->casa ?? '',
                    $d->user->nombre ?? 'Usuario eliminado',
                    $d->user->tipo ?? '',
                    $d->user->correo ?? '',
                    $d->pago->concepto,
                    $d->pago->vencimiento,
                    number_format((float) $d->pago->cantidad, 2, '.', ''),
                    $d->pago->recargo_pct !== null ? (float) $d->pago->recargo_pct : '',
                    $d->estado,
                    $d->path_pago ? 'Si' : 'No',
                    $fecha ? $fecha->format('Y-m-d') : '',
                    $fecha ? ($d->esPagoTardio() ? 'Tardio' : 'Puntual') : 'Sin registrar',
                    $dias,
                    $d->cantidad_pago !== null ? number_format((float) $d->cantidad_pago, 2, '.', '') : '',
                    number_format($x['recargo'], 2, '.', ''),
                    number_format($x['esperado'], 2, '.', ''),
                    number_format($x['excedente'], 2, '.', ''),
                    number_format($x['faltante'], 2, '.', ''),
                    $conSaldo ? 'Si' : 'No',
                    number_format($entroACuenta, 2, '.', ''),
                    $d->comentario_rechazo ?? '',
                    number_format($saldoPorUsuario[$uid], 2, '.', ''),
                ], ';');
            }

            /*
             * Totales. Es lo que responde "cuánto debí recibir en la cuenta":
             * el dinero que de verdad entró, separado de lo que se cubrió con
             * saldo a favor (que ya había entrado antes) y de lo que falta
             * por cobrar.
             */
            fputcsv($out, [], ';');
            fputcsv($out, ['TOTALES'], ';');
            fputcsv($out, ['Recibos', $totales['recibos']], ';');
            fputcsv($out, ['Esperado (cuotas + recargos)', number_format($totales['esperado'], 2, '.', '')], ';');
            fputcsv($out, ['ENTRO A LA CUENTA', number_format($totales['entro'], 2, '.', '')], ';');
            fputcsv($out, ['   de eso, recargos por mora', number_format($totales['recargo'], 2, '.', '')], ';');
            fputcsv($out, ['   de eso, excedente a favor de vecinos', number_format($totales['excedente'], 2, '.', '')], ';');
            fputcsv($out, ['Cubierto con saldo (no entro dinero)', number_format($totales['conSaldo'], 2, '.', '')], ';');
            fputcsv($out, ['Pendiente de cobro', number_format($totales['faltante'], 2, '.', '')], ';');

            fclose($out);
        }, $nombre, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /**
     * Siguiente folio de recibo en efectivo, del año en curso.
     *
     * Formato EF-2026-0001. Se reinicia cada año, que es como se numeran los
     * talonarios en papel y facilita el archivo muerto.
     */
    private function siguienteFolioEfectivo(): string
    {
        $anio = now()->format('Y');
        $prefijo = "EF-{$anio}-";

        $ultimo = Detallepago::withTrashed()
            ->where('folio_recibo', 'like', $prefijo.'%')
            ->orderByDesc('folio_recibo')
            ->value('folio_recibo');

        $consecutivo = $ultimo
            ? ((int) substr($ultimo, strlen($prefijo))) + 1
            : 1;

        return $prefijo.str_pad((string) $consecutivo, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Recibo en PDF desde tesorería, SIN firma.
     *
     * Es el que se imprime para entregar en mano, sobre todo en los pagos en
     * efectivo. Sale con la línea en blanco a propósito: lo firma el tesorero
     * de su puño, que es lo que le da valor al papel que recibe el vecino.
     *
     * El recibo que el vecino descarga desde su sesión es el otro, y ese sí
     * lleva la firma digitalizada.
     */
    public function reciboPdf($id)
    {
        try {
            $detalle = Detallepago::with(['pago', 'user', 'validador'])
                ->where('estado', 'pagado')
                ->findOrFail($id);

            Carbon::setLocale('es');

            $pdf = app(\App\Services\ReciboService::class)->generar($detalle, firmar: false);

            return response($pdf, 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="recibo-'
                    .($detalle->folio_recibo ?: $detalle->id).'.pdf"',
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            abort(404, 'Solo se puede generar el recibo de un pago ya validado.');
        } catch (\Exception $e) {
            Log::error('Error al generar el recibo desde tesorería: '.$e->getMessage());

            abort(500, 'No se pudo generar el recibo.');
        }
    }

    public function obtenerPagos(Request $request)
    {
        if ($request->ajax()) {
            // `recargo_pct` debe ir en el select o llega null al datatable y el
            // concepto se vería como si no cobrara mora. Lo mismo con
            // `aplica_saldo`, que se consulta condicionalmente porque la
            // columna puede no existir todavía si aún no se corre /migrar.
            $columnas = ['id', 'concepto', 'cantidad', 'vencimiento', 'recargo_pct', 'created_by'];

            if (Schema::hasColumn('pagos', 'aplica_saldo')) {
                $columnas[] = 'aplica_saldo';
            }

            $pagos = Pago::with('user')->select($columnas);

            return datatables()->of($pagos)
                ->addColumn('autor', fn ($c) => $c->user->nombre ?? '—')
                ->addColumn('progreso', function ($c) {
                    $total = $c->detalles()->count();

                    if ($total === 0) {
                        return '<div class="progress-container" style="min-width: 120px;">
                                    <div style="font-size: 0.75rem; color: #94a3b8;">Sin usuarios</div>
                                </div>';
                    }

                    // El avance real es lo validado por tesorería, no lo subido.
                    $aprobados = $c->detalles()->where('estado', 'pagado')->count();

                    // Comprobantes subidos que siguen esperando revisión.
                    $porRevisar = $c->detalles()
                        ->where('estado', 'pendiente')
                        ->whereNotNull('path_pago')
                        ->count();

                    $porcentaje = round(($aprobados / $total) * 100);
                    $color = $porcentaje >= 100 ? '#10b981' : ($porcentaje >= 50 ? '#f59e0b' : '#ef4444');

                    $avisoRevision = $porRevisar > 0
                        ? '<div style="font-size: 0.7rem; color: #f59e0b; margin-top: 3px;" title="Comprobantes subidos en espera de validación">
                               <i class="clock outline icon"></i>'.$porRevisar.' por revisar
                           </div>'
                        : '';

                    return '<div class="progress-container" style="min-width: 120px;">
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px;">
                                    <span style="font-size: 0.75rem; color: #64748b;" title="Pagos validados sobre el total asignado">'.$aprobados.'/'.$total.'</span>
                                    <span style="font-size: 0.75rem; font-weight: 600; color: '.$color.';">'.$porcentaje.'%</span>
                                </div>
                                <div style="background: #e5e7eb; border-radius: 4px; height: 6px; overflow: hidden;">
                                    <div style="background: '.$color.'; height: 100%; width: '.$porcentaje.'%; border-radius: 4px; transition: width 0.3s ease;"></div>
                                </div>
                                '.$avisoRevision.'
                            </div>';
                })
                ->addColumn('acciones', function ($c) {
                    return '<div class="ui center aligned buttons">
                                    <a class="ui green small icon button" href="'.url('/administrador/pago/detalle-pagos/').'/'.$c->id.'">
                                        <i class="eye icon"></i>
                                    </a>
                                    <button class="ui red small icon button btn-eliminar" data-id="'.$c->id.'">
                                        <i class="trash icon"></i>
                                    </button>
                                </div>';
                })
                ->rawColumns(['acciones', 'progreso'])
                ->make(true);
        }
    }

    public function DetallePagos($id)
    {
        $id_pago = $id;

        // Antes usaba first(): un concepto inexistente o eliminado llegaba
        // como null a la vista y reventaba en 500 al leer ->concepto.
        $info = Pago::findOrFail($id_pago);

        $usuariosAsignados = Detallepago::where('pago_id', $id)->pluck('user_id');
        $usuariosDisponibles = User::where('estado', 1)
            ->whereNotIn('id', $usuariosAsignados)
            ->orderBy('nombre')
            ->get(['id', 'nombre', 'casa']);

        return view('administrador.detallepagos', compact('id_pago', 'info', 'usuariosDisponibles'));
    }

    public function obtenerDetallepagos(Request $request)
    {
        $idPago = $request->input('id');

        $pagos = Detallepago::with(['pago'])
            ->where('pago_id', $idPago)
            ->get();

        return datatables()->of($pagos)
            ->addColumn('usuario', fn ($c) => $c->user->nombre ."(Casa #". $c->user->casa .")" ?? '—')
            ->addColumn('concepto', fn ($row) => $row->pago->concepto)
            ->addColumn('pago', function ($c) {
                if ($c->path_pago) {
                    $url = asset('storage/'.$c->path_pago);
                    $esTarde = $c->esPagoTardio();
                    $fechaPago = $c->fechaEfectivaPago();

                    $badge = '';
                    if ($esTarde) {
                        $titulo = 'Pagado el '.$fechaPago->format('d/m/Y')
                            .', después del vencimiento ('
                            .\Carbon\Carbon::parse($c->pago->vencimiento)->format('d/m/Y').')';
                        $badge = ' <span class="ui orange mini label" title="'.e($titulo).'"><i class="clock icon"></i> Tarde</span>';
                    }

                    return '<button class="ui mini secondary button btn-ver-evidencia" data-img="'.$url.'">
                                Ver pago
                            </button>'.$badge;
                }

                return '<a class="ui grey label">Pendiente</a>';
            })
            ->addColumn('estado', function ($c) {
                $esTarde = $c->esPagoTardio();

                switch ($c->estado) {
                    case 'pendiente':
                        $color = $esTarde ? 'orange' : 'gray';
                        break;
                    case 'rechazado':
                        $color = 'red';
                        break;
                    case 'pagado':
                        $color = $esTarde ? 'orange' : 'green';
                        break;
                    default:
                        $color = 'gray';
                }

                $badgeTarde = $esTarde ? ' <i class="clock icon" title="Pago vencido"></i>' : '';

                return '<a class="ui '.$color.' label">'.ucfirst($c->estado).$badgeTarde.'</a>';
            })
            ->addColumn('cantidad_pago', function ($c) {
                if ($c->cantidad_pago == '' || $c->cantidad_pago == null) {
                    return '<a class="ui gray label">pendiente</a>';
                }

                $d = $c->desglosePago();

                $etiqueta = '<a class="ui green label">$'.number_format($c->cantidad_pago, 2).'</a>';

                /*
                 * El desglose va ENTRE PARÉNTESIS y nombrando cada parte.
                 *
                 * Antes decía "$715.00 +$65.00 recargo", que se lee como una
                 * suma: parecía que el vecino debía $780. El monto grande ya
                 * incluye el recargo; lo que va después lo explica, no lo
                 * agrega.
                 *
                 * El total tiene que seguir siendo el primer número del
                 * texto: de ahí lo toma el modal de edición.
                 */
                if ($d['recargo'] > 0) {
                    $etiqueta .= ' <span class="ui orange mini label" title="El monto ya incluye el recargo por mora">(cuota $'
                        .number_format($d['cuota'], 2).' + recargo $'
                        .number_format($d['recargo'], 2).')</span>';
                }

                if ($d['excedente'] > 0) {
                    $etiqueta .= ' <span class="ui teal mini label" title="Pagó de más; la diferencia se abona a su saldo a favor">(incluye $'
                        .number_format($d['excedente'], 2).' que pasa a su saldo)</span>';
                }

                if ($d['faltante'] > 0) {
                    $etiqueta .= ' <span class="ui red mini label" title="Le falta respecto a lo que debía pagar">(faltan $'
                        .number_format($d['faltante'], 2).' de $'
                        .number_format($d['esperado'], 2).')</span>';
                }

                return $etiqueta;
            })
            // Dato explícito para el frontend. Antes el JS deducía la
            // puntualidad buscando la palabra "tarde" dentro del HTML, lo que
            // se rompía en silencio al cambiar cualquier etiqueta.
            ->addColumn('es_tarde', fn ($c) => $c->esPagoTardio())
            // Saldo a favor del vecino, para que tesorería sepa que ese recibo
            // puede liquidarse con dinero que el vecino ya entregó.
            ->addColumn('saldo_favor', fn ($c) => app(\App\Services\SaldoService::class)->saldo($c->user_id))
            ->addColumn('fecha_pago', function ($c) {
                $fecha = $c->fechaEfectivaPago();

                if ($fecha) {
                    $color = $c->esPagoTardio() ? 'orange' : 'green';

                    return '<a class="ui '.$color.' label">'.$fecha->format('d/m/Y').'</a>';
                }

                // Sin comprobante todavía no hay pago del cual hablar.
                if (! $c->path_pago) {
                    return '<a class="ui grey label">Sin pago</a>';
                }

                // Tiene comprobante pero es anterior a la captura de fecha: el
                // dato no existe y no se inventa. Tesorería puede capturarlo.
                return '<a class="ui basic grey label" title="Pago anterior a la captura de fecha. Puedes registrarla al editar.">Sin registrar</a>';
            })
            ->addColumn('acciones', function ($c) {
                // Sin permiso para mover dinero no se muestran los botones:
                // más vale no ofrecer una acción que el backend va a negar.
                if (! auth()->user()->puedeGestionarPagos()) {
                    return '<div class="ui center aligned">
                                <span class="ui basic label" title="Solo el Tesorero registra y valida pagos">
                                    <i class="lock icon"></i> Solo consulta
                                </span>
                            </div>';
                }

                return '<div class="ui center aligned buttons">
                                <button class="ui blue small icon button btn-editar" data-id="'.$c->id.'"
                                    data-estado="'.e($c->estado).'"
                                    data-cantidad-pago="'.$c->cantidad_pago.'"
                                    data-fecha-pago="'.($c->fecha_pago ? $c->fecha_pago->format('Y-m-d') : '').'"
                                    data-comentario-rechazo="'.e($c->comentario_rechazo ?? '').'">
                                    <i class="edit icon"></i>
                                </button>
                                <button class="ui red small icon button btn-eliminar" data-id="'.$c->id.'">
                                    <i class="trash icon"></i>
                                </button>
                            </div>';
            })
            ->rawColumns(['acciones', 'pago', 'estado', 'cantidad_pago', 'fecha_pago'])
            ->make(true);
    }

    public function actualizarPago($id, Request $request)
    {
        try {
            /*
             * El motivo es obligatorio SOLO al rechazar. Se arma la regla
             * según el estado en vez de usar `required_if`, porque esa regla
             * combinada con `nullable` se anula (dejaba pasar rechazos sin
             * explicación) y sin `nullable` rompe el caso contrario: al
             * aprobar, el textarea vacío llega como null —Laravel convierte
             * las cadenas vacías— y la regla `string` lo rechazaba.
             */
            $esRechazoSolicitado = $request->estado === 'rechazado';

            $request->validate([
                'estado' => 'required|in:pendiente,pagado,rechazado',
                'cantidad_pago' => 'nullable|numeric|min:0',
                'fecha_pago' => 'nullable|date',
                'forma_pago' => 'nullable|in:'.implode(',', array_keys(Detallepago::FORMAS_PAGO)),
                'comentario_rechazo' => $esRechazoSolicitado
                    ? 'required|string|max:500'
                    : 'nullable|string|max:500',
            ], [
                'comentario_rechazo.required' => 'Explica al vecino por qué se rechaza su comprobante.',
            ]);

            $pago = Detallepago::findOrFail($id);

            $data = ['estado' => $request->estado];

            // El motivo solo tiene sentido mientras el pago esté rechazado.
            // Al aprobarlo o regresarlo a pendiente se limpia, para que no
            // quede colgado un reclamo que ya se resolvió.
            $data['comentario_rechazo'] = $request->estado === 'rechazado'
                ? $request->comentario_rechazo
                : null;

            if ($request->filled('cantidad_pago')) {
                $data['cantidad_pago'] = $request->cantidad_pago;
            }

            // El tesorero puede corregir la fecha real del movimiento.
            if ($request->filled('fecha_pago')) {
                $data['fecha_pago'] = $request->fecha_pago;
            }

            // Columnas que llegan con /migrar. Se escriben solo si existen,
            // para que subir los archivos antes de migrar no rompa la
            // validación de pagos, que es la operación más usada del sistema.
            if (Schema::hasColumn('detallepagos', 'forma_pago')) {
                if ($request->filled('forma_pago')) {
                    $data['forma_pago'] = $request->forma_pago;
                }

                if ($request->estado === 'pagado') {
                    // Queda registrado quién validó: es de quien llevará la
                    // firma el recibo que descargue el vecino.
                    $data['validado_por'] = Auth::user()->id;

                    // El efectivo necesita folio porque su único respaldo es
                    // el papel firmado que se entrega en mano.
                    if ($request->forma_pago === 'efectivo' && ! $pago->folio_recibo) {
                        $data['folio_recibo'] = $this->siguienteFolioEfectivo();
                    }
                }
            }

            $pago->update($data);

            // Lo que se pagó por encima de la cuota queda como saldo a favor
            // del vecino, en vez de perderse dentro del recibo.
            $saldos = app(\App\Services\SaldoService::class);
            $movimiento = $saldos->registrarExcedente($pago->fresh('pago'));

            // notificar a usuario de actualización
            $correo = $pago->user->correo;
            $subject = '🔃 Actualización de tu pago';
            $titulo = 'Pago actualizado';
            $montoMostrar = $pago->cantidad_pago ?? $pago->pago->cantidad;
            // Si el pago dejó saldo a favor, se le avisa: es dinero suyo y
            // debe saber que quedó disponible para su próxima cuota.
            $avisoSaldo = '';

            if ($movimiento && $movimiento->tipo === \App\Models\SaldoMovimiento::TIPO_ABONO) {
                $avisoSaldo = '<br><br><strong>Saldo a favor:</strong> pagaste $'
                    .number_format($movimiento->monto, 2)
                    .' de más. Ese monto quedó a tu favor y se aplicará automáticamente a tu próxima cuota. Tu saldo disponible es de $'
                    .number_format($saldos->saldo($pago->user_id), 2).'.';
            }

            // El motivo del rechazo es lo único que el vecino necesita leer,
            // así que va destacado y antes de cualquier otro detalle.
            $avisoRechazo = '';

            if ($pago->estado === 'rechazado' && $pago->comentario_rechazo) {
                $subject = '⚠️ Tu comprobante fue rechazado';
                $titulo = 'Comprobante rechazado';

                $avisoRechazo = '<br><br><strong>Motivo del rechazo:</strong><br>'
                    .e($pago->comentario_rechazo)
                    .'<br><br>Puedes subir un nuevo comprobante desde la plataforma.';
            }

            $mensaje = 'Se actualizó el estado de tu pago. <br><br>
            <strong>Concepto:</strong> '.$pago->pago->concepto.'<br>
            <strong>Monto:</strong> $'.number_format($montoMostrar, 2).'<br>
            <strong>Estado:</strong> '.$pago->estado.$avisoRechazo.$avisoSaldo;

            $this->sendmail->enviarCorreoPersonal($correo, $subject, $titulo, $mensaje);

            /** @var \App\Models\User $usuario */
            // Notificación in-app Web
            $usuario = User::withTrashed()->find($pago->user->id);
            $esRechazo = $pago->estado === 'rechazado' && $pago->comentario_rechazo;

            $usuario->notify(new NotificacionGenerica(
                $esRechazo ? 'Tu comprobante fue rechazado' : 'Actualización de pago',
                $esRechazo
                    ? '<strong>Concepto:</strong> '.$pago->pago->concepto.'
                       <strong>Motivo:</strong> '.e($pago->comentario_rechazo).'
                       Sube un nuevo comprobante desde la plataforma.'
                    : 'Se actualizó el estado de tu pago.
                <strong>Concepto:</strong> '.$pago->pago->concepto.'
                <strong>Monto:</strong> $'.number_format($montoMostrar, 2).'
                <strong>Estado:</strong> '.$pago->estado,
                'pagos',
                $esRechazo ? 'usuario/pago?tab=rechazados' : 'usuario/pago',
                null,
                $esRechazo ? '<i class="times circle icon"></i>' : '<i class="money bill wave icon"></i>'
            ));

            return response()->json([
                'header' => '✅ Editado correctamente',
                'message' => 'El estado fue actualizado',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Sin este catch, el genérico de abajo convertía un error de
            // captura ("falta el motivo del rechazo") en "contacta al
            // desarrollador", y el tesorero no sabía qué corregir.
            return response()->json([
                'header' => '⚠️ Faltan datos',
                'message' => implode(' ', $e->validator->errors()->all()),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error al actualizar el estado: '.$e->getMessage());

            return response()->json(['header' => '❌ Error', 'message' => 'Error al actualizar, contacta al desarrollador Christian Martínez']);
        }
    }

    public function eliminarPago($id)
    {
        try {
            $pago = Detallepago::findOrFail($id);

            // Eliminar imagen del almacenamiento si existe
            if ($pago->path_pago && Storage::disk('public')->exists($pago->path_pago)) {
                Storage::disk('public')->delete($pago->path_pago);
            }

            // notificar a usuario de actualización
            $correo = $pago->user->correo;
            $subject = 'Pago eliminado';
            $titulo = 'Pago eliminado por la Administración';
            $mensaje = 'La administración eliminó tu recibo de pago. <br><br>
            <strong>Concepto:</strong> '.$pago->pago->concepto;

            // Se elimina despues de obtener el correo
            $pago->delete();

            // Notificación in-app Web
            $usuario = User::withTrashed()->find($pago->user->id);
            $usuario->notify(new NotificacionGenerica(
                'Pago eliminado',
                'La administración eliminó tu recibo de pago.
                <strong>Concepto:</strong> '.$pago->pago->concepto,
                'pagos',
                'usuario/pago',
                null,
                '<i class="money bill wave icon"></i>'
            ));

            $this->sendmail->enviarCorreoPersonal($correo, $subject, $titulo, $mensaje);

            return response()->json(
                [
                    'header' => 'Pago eliminado ✅',
                    'success' => true,
                    'message' => 'Se eliminó correctamente el pago',
                ]
            );
        } catch (\Exception $e) {
            Log::error('Error al eliminar un pago: '.$e->getMessage());

            return response()->json(
                [
                    'header' => '❌ Ups... 🛑',
                    'success' => false,
                    'message' => 'Por favor intentalo más tarde y reportalo al desarrollador de la aplicación Christian Martínez',
                ]
            );
        }
    }

    public function agregarUsuario(Request $request)
    {
        try {
            $request->validate([
                'pago_id' => 'required|exists:pagos,id',
                'user_id' => 'required|exists:users,id',
            ]);

            $pagoHeader = Pago::findOrFail($request->pago_id);

            $yaExiste = Detallepago::where('pago_id', $request->pago_id)
                ->where('user_id', $request->user_id)
                ->exists();

            if ($yaExiste) {
                return response()->json([
                    'header' => '❌ Ya asignado',
                    'success' => false,
                    'message' => 'Este usuario ya tiene asignado este concepto de pago.',
                ]);
            }

            $detalle = Detallepago::create([
                'pago_id' => $request->pago_id,
                'user_id' => $request->user_id,
            ]);

            $usuario = User::withTrashed()->find($request->user_id);

            // Mismo trato que al publicar el concepto: si el vecino trae saldo
            // suficiente y el concepto lo admite, el recibo se liquida solo.
            // Sin esto, a quien se agrega después le llegaba como pendiente
            // algo que ya tenía pagado por adelantado.
            $detalle->setRelation('pago', $pagoHeader);
            $saldos = app(\App\Services\SaldoService::class);

            if ($saldos->aplicarA($detalle)) {
                $usuario->notify(new NotificacionGenerica(
                    'Recibo cubierto con tu saldo a favor',
                    'Se te asignó el recibo <strong>'.e($pagoHeader->concepto).'</strong> por $'
                    .number_format($pagoHeader->cantidad, 2).
                    ' y se liquidó automáticamente con tu saldo a favor. No tienes que hacer nada.
                    <strong>Saldo restante:</strong> $'.number_format($saldos->saldo($usuario->id), 2),
                    'pagos',
                    'usuario/pago',
                    null,
                    '<i class="check circle icon"></i>'
                ));

                $this->sendmail->enviarCorreoPersonal(
                    $usuario->correo,
                    '✅ Tu recibo se cubrió con tu saldo a favor',
                    'Recibo liquidado con tu saldo a favor',
                    'Se te asignó el recibo <strong>'.e($pagoHeader->concepto).'</strong> por $'
                    .number_format($pagoHeader->cantidad, 2).
                    ' y se liquidó automáticamente con el saldo que tenías a favor.<br><br>
                    <strong>Saldo restante:</strong> $'.number_format($saldos->saldo($usuario->id), 2)
                );

                return response()->json([
                    'header' => '✅ Usuario agregado',
                    'success' => true,
                    'message' => 'El usuario fue agregado y su recibo quedó cubierto con el saldo a favor que tenía.',
                ]);
            }

            $usuario->notify(new NotificacionGenerica(
                '🧾 Nuevo recibo de pago',
                'Se te asignó un nuevo recibo de pago:
                <strong>Concepto:</strong> '.$pagoHeader->concepto.'
                <strong>Cantidad a pagar:</strong> $'.number_format($pagoHeader->cantidad, 2),
                'pagos',
                'usuario/pago',
                null,
                '<i class="money bill wave icon"></i>'
            ));

            $subject = '🧾 Nuevo recibo de pago';
            $titulo = 'Nuevo recibo de pago cargado en la plataforma';
            $mensaje = 'Se te asignó un nuevo recibo de pago: <br>
            <strong>Concepto:</strong> '.e($pagoHeader->concepto).'<br>
            <strong>Cantidad a pagar:</strong> $'.number_format($pagoHeader->cantidad, 2).'<br>
            <strong>Fecha de Límite de pago:</strong> '.Carbon::parse($pagoHeader->vencimiento)->translatedFormat('j \\d\\e F \\d\\e Y');

            $this->sendmail->enviarCorreoPersonal($usuario->correo, $subject, $titulo, $mensaje);

            return response()->json([
                'header' => '✅ Usuario agregado',
                'success' => true,
                'message' => 'El usuario fue agregado al concepto de pago y notificado.',
            ]);
        } catch (\Exception $e) {
            Log::error('Error al agregar usuario al pago: '.$e->getMessage());

            return response()->json([
                'header' => '❌ Error',
                'success' => false,
                'message' => 'Error al agregar usuario: '.$e->getMessage(),
            ]);
        }
    }

    public function eliminarMainPago($id)
    {
        try {
            $validar = Detallepago::select('pago_id')->where('pago_id', $id)->exists();
            if ($validar) {
                return response()->json([
                    'header' => '❌ Ups... 🛑',
                    'success' => false,
                    'message' => 'Existen recibos de pago activos para este concepto',
                ]);
            }

            $pago = Pago::find($id);
            if ($pago) {
                $pago->delete();
            }

            return response()->json([
                'header' => 'Recibo de pago eliminado ✅',
                'success' => true,
                'message' => 'Se eliminó correctamente el recibo de pago',
            ]);
        } catch (\Exception $e) {
            Log::error('Error al eliminar un recibo de pago: '.$e->getMessage());

            return response()->json([
                'header' => '❌ Ups... 🛑',
                'success' => false,
                'message' => 'Por favor intentalo más tarde y reportalo al desarrollador de la aplicación Christian Martínez',
            ]);
        }
    }
}
