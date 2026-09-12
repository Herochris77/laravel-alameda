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
            $path = $request->file('doc_path')->store('documentos', 'public');

            Documento::create([
                'titulo' => $request->titulo,
                'descripcion' => $request->descripcion,
                'tipo' => $request->tipo_documento,
                'pago_id' => isset($request->concepto_pago) ? $request->concepto_pago : null,
                'doc_path' => $path,
                'cantidad' => $request->cantidad ? $request->cantidad : null,
                'categoria_gasto' => $request->categoria_gasto,
                'created_by' => Auth::user()->id,
            ]);

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
        } catch (\Exception $e) {
            Log::error('Error al crear documento: '.$e->getMessage());

            return response()->json(['header' => '❌ Error', 'message' => 'Error al cargar el documento']);
        }
    }

    public function obtenerDocumentos(Request $request)
    {
        if ($request->ajax()) {
            $documento = Documento::with('user', 'pago')
                ->select(['id', 'titulo', 'descripcion', 'tipo', 'pago_id', 'doc_path', 'cantidad', 'categoria_gasto', 'created_by', 'created_at']);

            return datatables()->of($documento)
                ->addColumn('autor', fn ($c) => $c->user->nombre ?? '—')
                ->addColumn('acciones', function ($c) {
                    return '<div class="ui center aligned">
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
