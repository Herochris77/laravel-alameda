<?php

namespace App\Http\Controllers\Administrador;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Email\EmailController;
use App\Models\Comunicado;
use App\Models\User;
use App\Notifications\NotificacionGenerica;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ComunicadoController extends Controller
{
    protected $sendmail;

    // Inyección de dependecia al controlador de correos
    public function __construct(EmailController $sendmail)
    {
        $this->sendmail = $sendmail;
    }

    public function nuevoComunicado()
    {
        return view('administrador.comunicado');
    }

    public function crearComunicado(Request $request)
    {
        try {
            Comunicado::create([
                'titulo' => $request->titulo,
                'comunicado' => $request->contenido,
                'vencimiento' => $request->fecha_vencimiento,
                'created_by' => Auth::user()->id,
            ]);

            $subjectmail = '📢 Comunicado publicado';
            $titulomail = 'Nuevo comunicado disponible';
            $mensajemail = $request->contenido;

            $usuarios = User::get();
            foreach ($usuarios as $usuario) {
                /** @var \App\Models\User $usuario */
                // Notificación in-app Web
                $usuario->notify(new NotificacionGenerica(
                    'Nuevo Comunicado',
                    '<b>Título: </b>'.$request->titulo.'',
                    'comunicado',
                    'usuario/comunicados',
                    null,
                    '<i class="bullhorn icon"></i>'
                ));
            }

            $this->sendmail->enviarCorreoGeneral($subjectmail, $titulomail, $mensajemail);

            return response()->json([
                'success' => true,
                'header' => 'Comunicado publicado ✅',
                'message' => 'Tu comunicado se publicó correctamente',
            ]);
        } catch (\Exception $e) {
            Log::error('Ocurrió un error al generar un comunicado: '.$e);

            return response()->json([
                'success' => false,
                'header' => '❌ Ocurrió un error',
                'message' => 'Por favor intentalo más tarde y reportalo al desarrollador de la aplicación Christian Martínez',
            ]);
        }
    }

    public function obtenerComunicados(Request $request)
    {
        if ($request->ajax()) {
            $comunicados = Comunicado::with('user')
                ->select(['id', 'titulo', 'comunicado', 'vencimiento', 'created_by']);

            return datatables()->of($comunicados)
                ->addColumn('autor', fn ($c) => $c->user->nombre ?? '—')
                ->addColumn('acciones', function ($c) {
                    return '<div class="ui center aligned">
                                    <button class="ui red small icon button btn-eliminar" data-id="'.$c->id.'">
                                        <i class="trash icon"></i>
                                    </button>
                                </div>';
                })
                ->rawColumns(['acciones'])
                ->make(true);
        }
    }

    public function eliminarComunicados($id)
    {
        try {
            $comunicado = Comunicado::findOrFail($id);
            $comunicado->delete();

            return response()->json(
                [
                    'header' => 'Comunicado eliminado ✅',
                    'success' => true,
                    'message' => 'Se eliminó correctamente tu comunicado',
                ]
            );
        } catch (\Exception $e) {
            Log::error('Error al eliminar comunicado: '.$e->getMessage());

            return response()->json(
                [
                    'header' => '❌ Ups... 🛑',
                    'success' => false,
                    'message' => 'Por favor intentalo más tarde y reportalo al desarrollador de la aplicación Christian Martínez',
                ]
            );
        }
    }
}
