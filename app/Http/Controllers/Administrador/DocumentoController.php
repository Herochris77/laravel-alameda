<?php

namespace App\Http\Controllers\Administrador;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Email\EmailController;
use App\Models\Documento;
use App\Models\User;
use App\Notifications\NotificacionGenerica;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class DocumentoController extends Controller
{
    protected $sendmail;

    // Inyección de dependecia al controlador de correos
    public function __construct(EmailController $sendmail)
    {
        $this->sendmail = $sendmail;
    }

    public function nuevoDocumento()
    {
        return view('administrador.documento');
    }

    public function crearDocumento(Request $request)
    {
        try {
            $esGasto = $request->tipo_documento === 'referencia';

            if ($esGasto) {
                $request->validate([
                    'cantidad' => 'required|numeric|min:0',
                    'categoria_gasto' => 'required|string|max:50',
                    // Sin la fecha del movimiento el reporte mensual no puede
                    // cuadrar contra el estado de cuenta.
                    'fecha_gasto' => 'required|date|before_or_equal:today',
                    'proveedor' => 'nullable|string|max:150',
                    'forma_pago' => 'nullable|string|max:20',
                ], [
                    'fecha_gasto.required' => 'Captura el día en que salió el dinero de la cuenta.',
                    'fecha_gasto.before_or_equal' => 'La fecha del movimiento no puede ser futura.',
                ]);
            }

            $path = $request->file('doc_path')->store('documentos', 'public');

            $datos = [
                'titulo' => $request->titulo,
                'descripcion' => $request->descripcion,
                'tipo' => $request->tipo_documento,
                'pago_id' => isset($request->concepto_pago) ? $request->concepto_pago : null,
                'doc_path' => $path,
                'cantidad' => $request->cantidad ? $request->cantidad : null,
                'categoria_gasto' => $request->categoria_gasto,
                'created_by' => Auth::user()->id,
            ];

            // Las columnas llegan con /migrar, y en producción los archivos se
            // suben antes de correrlo. En esa ventana el gasto se guarda como
            // se guardaba antes en vez de reventar con un error de SQL.
            if ($esGasto && Schema::hasColumn('documentos', 'fecha_gasto')) {
                $datos['fecha_gasto'] = $request->fecha_gasto;
                $datos['proveedor'] = $request->proveedor;
                $datos['forma_pago'] = $request->forma_pago;
            }

            Documento::create($datos);

            $subjectmail = '🧾 Nuevo documento cargado';
            $titulomail = 'Se agregó un nuevo documento a la plataforma';
            $mensajemail = 'El documento  <strong>'.e($request->titulo).' </strong> <br>
            fue agregado a la plataforma, puedes revisarlo en el módulo de Documentos.';

            // Notificación in-app Web
            $usuarios = User::get();
            foreach ($usuarios as $usuario) {
                /** @var \App\Models\User $usuario */
                // Notificación in-app Web
                $usuario->notify(new NotificacionGenerica(
                    'Nuevo Documento',
                    '<b>Título: </b>'.e($request->titulo).'',
                    'documento',
                    'usuario/documentos',
                    null,
                    '<i class="file alternate icon"></i>'
                ));
            }

            $this->sendmail->enviarCorreoGeneral($subjectmail, $titulomail, $mensajemail);

            return response()->json([
                'success' => true,
                'header' => 'Documento cargado ✅',
                'message' => 'El documento se cargó correctamente.',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Sin este catch, el genérico de abajo convertía "falta la fecha"
            // en "error al cargar el documento" y no había forma de saberlo.
            return response()->json([
                'success' => false,
                'header' => '⚠️ Faltan datos',
                'message' => implode(' ', $e->validator->errors()->all()),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error al crear documento: '.$e->getMessage());

            return response()->json(['header' => '❌ Error', 'message' => 'Error al cargar el documento']);
        }
    }

    public function obtenerDocumentos(Request $request)
    {
        if ($request->ajax()) {
            $columnas = ['id', 'titulo', 'descripcion', 'tipo', 'pago_id', 'doc_path', 'cantidad', 'categoria_gasto', 'created_by', 'created_at'];

            // Condicional porque la columna puede no existir todavía si aún
            // no se corre /migrar.
            $hayFechaGasto = Schema::hasColumn('documentos', 'fecha_gasto');

            if ($hayFechaGasto) {
                $columnas = array_merge($columnas, ['fecha_gasto', 'proveedor', 'forma_pago']);
            }

            $documento = Documento::with('user', 'pago')->select($columnas);

            return datatables()->of($documento)
                ->addColumn('autor', fn ($c) => $c->user->nombre ?? '—')
                ->addColumn('fecha_movimiento', function ($c) use ($hayFechaGasto) {
                    if (! $c->cantidad) {
                        return null;
                    }

                    return [
                        'texto' => $hayFechaGasto && $c->fecha_gasto
                            ? \Carbon\Carbon::parse($c->fecha_gasto)->format('d/m/Y')
                            : 'Sin registrar',
                        'real' => $hayFechaGasto && $c->fecha_gasto !== null,
                    ];
                })
                ->addColumn('acciones', function ($c) use ($hayFechaGasto) {
                    // Los 34 gastos anteriores a esta función no traen fecha
                    // bancaria; este botón es la forma de completarla.
                    $editar = ($c->cantidad && $hayFechaGasto)
                        ? '<button class="ui blue small icon button btn-editar-gasto" data-id="'.$c->id.'"
                                   data-fecha="'.($c->fecha_gasto ? \Carbon\Carbon::parse($c->fecha_gasto)->format('Y-m-d') : '').'"
                                   data-proveedor="'.e($c->proveedor ?? '').'"
                                   data-forma="'.e($c->forma_pago ?? '').'"
                                   data-titulo="'.e($c->titulo).'"
                                   title="Editar datos del gasto">
                                <i class="edit icon"></i>
                            </button>'
                        : '';

                    return '<div class="ui center aligned">
                                    '.$editar.'
                                    <button class="ui red small icon button btn-eliminar" data-id="'.$c->id.'">
                                        <i class="trash icon"></i>
                                    </button>
                                </div>';
                })
                ->addColumn('documento', function ($c) {
                    if ($c->doc_path) {
                        $url = asset('storage/'.$c->doc_path);

                        return '<button class="ui mini teal button btn-ver-archivo" data-img="'.$url.'">
                                    Ver documento
                                </button>';
                    }

                    return '—';
                })
                ->addColumn('concepto', function ($c) {
                    return optional($c->pago)->concepto ?? 'NA';
                })
                ->addColumn('cantidad_formatted', function ($c) {
                    return $c->cantidad ? '$'.number_format($c->cantidad, 2) : '—';
                })
                ->addColumn('categoria', function ($c) {
                    $categorias = [
                        'luz' => ['icon' => 'bolt', 'color' => '#f59e0b', 'label' => 'Luz'],
                        'agua' => ['icon' => 'tint', 'color' => '#3b82f6', 'label' => 'Agua'],
                        'gas' => ['icon' => 'fire', 'color' => '#ef4444', 'label' => 'Gas'],
                        'mantenimiento' => ['icon' => 'wrench', 'color' => '#10b981', 'label' => 'Mantenimiento'],
                        'seguridad' => ['icon' => 'shield alternate', 'color' => '#8b5cf6', 'label' => 'Seguridad'],
                        'limpieza' => ['icon' => 'broom', 'color' => '#06b6d4', 'label' => 'Limpieza'],
                        'jardineria' => ['icon' => 'leaf', 'color' => '#22c55e', 'label' => 'Jardinería'],
                        'otro' => ['icon' => 'file', 'color' => '#64748b', 'label' => 'Otro'],
                    ];
                    $cat = $categorias[$c->categoria_gasto] ?? $categorias['otro'];

                    return '<span class="ui basic label" style="border-left: 3px solid '.$cat['color'].';"><i class="'.$cat['icon'].' icon"></i> '.$cat['label'].'</span>';
                })
                ->rawColumns(['acciones', 'documento', 'concepto', 'categoria'])
                ->make(true);
        }
    }

    /**
     * Completa o corrige los datos bancarios de un gasto ya cargado.
     *
     * Los 34 gastos anteriores a esta función no tienen fecha de movimiento,
     * y el reporte los está fechando por el día en que se subió el PDF. Esto
     * es lo que permite irlos corrigiendo sin volver a subir el comprobante.
     */
    public function actualizarGasto(Request $request, $id)
    {
        try {
            if (! Schema::hasColumn('documentos', 'fecha_gasto')) {
                return response()->json([
                    'success' => false,
                    'header' => '⚠️ Falta migrar',
                    'message' => 'Corre /migrar para habilitar la fecha de los gastos.',
                ], 409);
            }

            $request->validate([
                'fecha_gasto' => 'required|date|before_or_equal:today',
                'proveedor' => 'nullable|string|max:150',
                'forma_pago' => 'nullable|string|max:20',
            ], [
                'fecha_gasto.required' => 'Captura el día en que salió el dinero de la cuenta.',
                'fecha_gasto.before_or_equal' => 'La fecha del movimiento no puede ser futura.',
            ]);

            $documento = Documento::findOrFail($id);

            if (! $documento->cantidad) {
                return response()->json([
                    'success' => false,
                    'header' => '❌ No es un gasto',
                    'message' => 'Solo los comprobantes de gasto llevan fecha de movimiento.',
                ], 422);
            }

            // Se tocan SOLO estos tres campos: el archivo, el monto y la
            // categoría se quedan exactamente como estaban.
            $documento->update([
                'fecha_gasto' => $request->fecha_gasto,
                'proveedor' => $request->proveedor,
                'forma_pago' => $request->forma_pago,
            ]);

            return response()->json([
                'success' => true,
                'header' => 'Gasto actualizado ✅',
                'message' => 'El reporte mensual ya lo cuenta en el mes en que salió el dinero.',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'header' => '⚠️ Revisa los datos',
                'message' => implode(' ', $e->validator->errors()->all()),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error al actualizar gasto: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'header' => '❌ Error',
                'message' => 'No se pudo actualizar el gasto.',
            ], 500);
        }
    }

    public function EliminarDocumento($id)
    {
        try {
            $documento = Documento::findOrFail($id);

            // Eliminar imagen del almacenamiento si existe
            if ($documento->doc_path && Storage::disk('public')->exists($documento->doc_path)) {
                Storage::disk('public')->delete($documento->doc_path);
            }
            $subjectmail = 'Documento Eliminado';
            $titulomail = 'Documento Eliminado en Alameda';
            $mensajemail = 'La administración eliminó el documento '.$documento->titulo;

            $usuarios = User::get();
            foreach ($usuarios as $usuario) {
                // Notificación in-app Web
                $usuario->notify(new NotificacionGenerica(
                    'Documento Eliminado',
                    $mensajemail,
                    'documento',
                    'usuario/documentos',
                    null,
                    '<i class="bullhorn icon"></i>'
                ));
            }

            // Envio de correo masivo
            $this->sendmail->enviarCorreoGeneral($subjectmail, $titulomail, $mensajemail);

            // Se elimina
            $documento->delete();

            // Respuesta
            return response()->json(
                [
                    'header' => 'Sanción eliminada ✅',
                    'success' => true,
                    'message' => 'Se eliminó correctamente la sanción',
                ]
            );
        } catch (\Exception $e) {
            Log::error('Error al eliminar sanción: '.$e->getMessage());

            return response()->json(
                [
                    'header' => '❌ Ups... 🛑',
                    'success' => false,
                    'message' => 'Por favor intentalo más tarde y reportalo al desarrollador de la aplicación Christian Martínez',
                ]
            );
        }
    }

    public function resumenGastos()
    {
        $documentos = Documento::where('tipo', 'referencia')
            ->whereNotNull('cantidad')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->get();

        $resumen = [
            'luz' => 0,
            'agua' => 0,
            'gas' => 0,
            'mantenimiento' => 0,
            'seguridad' => 0,
            'limpieza' => 0,
            'jardineria' => 0,
            'otro' => 0,
        ];

        foreach ($documentos as $doc) {
            $categoria = $doc->categoria_gasto ?? 'otro';
            if (isset($resumen[$categoria])) {
                $resumen[$categoria] += floatval($doc->cantidad);
            } else {
                $resumen['otro'] += floatval($doc->cantidad);
            }
        }

        return response()->json($resumen);
    }
}
