<?php

namespace App\Http\Controllers\Administrador;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Email\EmailController;
use App\Models\Sancion;
use App\Models\User;
use App\Notifications\NotificacionGenerica;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class SancionController extends Controller
{
    protected $sendmail;

    // Inyección de dependecia al controlador de correos
    public function __construct(EmailController $sendmail)
    {
        $this->sendmail = $sendmail;
    }

    public function nuevaSancion()
    {
        $usuarios = User::where('estado', 1)->get();

        return view('administrador.sancion', compact('usuarios'));
    }

    public function crearSancion(Request $request)
    {
        try {
            $path = $request->file('evidencia')->store('sanciones', 'public');
            $data_user = User::select('id', 'nombre', 'tipo', 'casa', 'correo')->where('id', $request->user_id)->first();

            if (! $data_user) {
                return response()->json(['header' => '❌ Error', 'message' => 'Usuario a sancionar no encontrado']);
            }

            if (empty($data_user->correo)) {
                Log::warning('Usuario sancionado sin correo: '.$data_user->id);
            }

            Sancion::create([
                'user_id' => $request->user_id,
                'motivo' => $request->motivo,
                'monto' => $request->monto,
                'incidencia' => $request->incidencia,
                'comentario' => $request->comentario,
                'foto_path' => $path,
                'pago_path' => '',
                'estado' => 'pendiente',
                'created_by' => Auth::user()->id,
            ]);

            $subjectmail = '🛑 Sanción Aplicada';
            $titulomail = 'Nueva sanción aplicada';
            $mensajemail = 'Se aplicó una sanción a '.$data_user->nombre.' '.$data_user->tipo.' de la casa #'.$data_user->casa.' por el motivo: '.$request->motivo;

            // Notificación in-app Web
            $data_user->notify(new NotificacionGenerica(
                'Sanción aplicada',
                'Se te aplicó una sanción por el motivo:<br><b>'.$request->motivo.'</b>',
                'sanciones',
                'usuario/sancion',
                null,
                '<i class="gavel icon"></i>'
            ));

            $this->sendmail->enviarCorreoPersonal($data_user->correo, $subjectmail, $titulomail, $mensajemail);

            return response()->json([
                'success' => true,
                'header' => 'Sanción registrada ✅',
                'message' => 'La sanción fue registrada exitosamente.',
            ]);
        } catch (\Exception $e) {
            Log::error('Error al crear sanción: '.$e->getMessage());

            return response()->json(['header' => '❌ Error', 'message' => 'Error al sancionar']);
        }
    }

    public function obtenerSanciones(Request $request)
    {
        if ($request->ajax()) {
            $comunicados = Sancion::with('user')
                // `fecha_pago` debe ir en el select o los reportes y el cálculo
                // de puntualidad la leen como null.
                ->select(['id', 'motivo', 'monto', 'incidencia', 'comentario', 'foto_path', 'pago_path', 'fecha_pago', 'estado', 'user_id', 'created_by']);

            return datatables()->of($comunicados)
                ->addColumn('usuario', fn ($c) => $c->sancionado->nombre ?? '—')
                ->addColumn('acciones', function ($c) {
                    return '<div class="ui center aligned buttons">
                                    <button class="ui blue small icon button btn-editar" data-id="'.$c->id.'" 
                                        data-motivo="'.e($c->motivo).'" 
                                        data-monto="'.e($c->monto).'" 
                                        data-incidencia="'.e($c->incidencia).'" 
                                        data-comentario="'.e($c->comentario).'"
                                        data-estado="'.e($c->estado).'">
                                        <i class="edit icon"></i>
                                    </button>
                                    <button class="ui red small icon button btn-eliminar" data-id="'.$c->id.'">
                                        <i class="trash icon"></i>
                                    </button>
                                </div>';
                })
                ->addColumn('evidencia', function ($c) {
                    if ($c->foto_path) {
                        $url = asset('storage/'.$c->foto_path);

                        return '<button class="ui mini teal button btn-ver-evidencia" data-img="'.$url.'">
                                    Ver evidencia
                                </button>';
                    }

                    return '—';
                })
                ->addColumn('pago', function ($c) {
                    if ($c->pago_path) {
                        $url = asset('storage/'.$c->pago_path);

                        return '<button class="ui mini secondary button btn-ver-evidencia" data-img="'.$url.'">
                                    Ver pago
                                </button>';
                    }

                    return '<a class="ui grey label">Pendiente</a>';
                })
                ->addColumn('motivo', function ($c) {
                    $motivo = $c->motivo;
                    $estado = $c->estado == 'pendiente' || $c->estado == 'rechazado' ? '<a class="ui red label">'.$c->estado.'</a>' : '<a class="ui green label">'.$c->estado.'</a>';

                    return $motivo.'<br>'.$estado;
                })
                ->rawColumns(['acciones', 'evidencia', 'pago', 'motivo'])
                ->make(true);
        }
    }

    public function eliminarSancion($id)
    {
        try {
            $sancion = Sancion::findOrFail($id);

            // Eliminar imagen del almacenamiento si existe
            if ($sancion->foto_path && Storage::disk('public')->exists($sancion->foto_path)) {
                Storage::disk('public')->delete($sancion->foto_path);
            }

            // notificar a usuario de actualización
            $correo = $sancion->sancionado->correo;
            $subject = 'Sanción eliminada';
            $titulo = 'Sanción eliminada por la Administración';
            $mensaje = 'La administración eliminó la siguiente sanción que tenías aplicada: <br> <strong>Motivo:</strong> '.$sancion->motivo;

            // Notificación in-app Web
            $usuario = User::withTrashed()->find($sancion->sancionado->id);
            $usuario->notify(new NotificacionGenerica(
                'Sanción eliminada',
                'La administración eliminó la siguiente sanción que tenías aplicada: <br> <strong>Motivo:</strong> '.$sancion->motivo.'',
                'sanciones',
                'usuario/sancion',
                null,
                '<i class="gavel icon"></i>'
            ));

            // Se elimina despues de obtener el correo y el motivo
            $sancion->delete();

            $this->sendmail->enviarCorreoPersonal($correo, $subject, $titulo, $mensaje);

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

    public function actualizarSancion(Request $request, $id)
    {
        try {
            $sancion = Sancion::findOrFail($id);

            $sancion->update([
                'motivo' => $request->motivo,
                'monto' => $request->monto,
                'incidencia' => $request->incidencia,
                'comentario' => $request->comentario,
                'estado' => $request->estado,
            ]);

            // notificar a usuario de actualización
            $correo = $sancion->sancionado->correo;
            $subject = '🔃 Actualización de tu sanción';
            $titulo = 'Sanción actualizada por la Administración';
            $mensaje = 'Se actualizó tu sanción y quedó de la siguiente forma: <br>
            <strong>Motivo:</strong> '.$request->motivo.'<br>
            <strong>Monto:</strong> $'.$request->monto.'<br>
            <strong>Comentario:</strong> '.$request->comentario.'<br>
            <strong>Estado:</strong> '.$request->estado;

            $this->sendmail->enviarCorreoPersonal($correo, $subject, $titulo, $mensaje);

            // Notificación in-app Web
            $usuario = User::withTrashed()->find($sancion->sancionado->id);
            $usuario->notify(new NotificacionGenerica(
                'Actualización de tu sanción',
                'Se actualizó tu sanción y quedó de la siguiente forma:
                <strong>Motivo:</strong> '.$request->motivo.'
                <strong>Monto:</strong> $'.$request->monto.'
                <strong>Comentario:</strong> '.$request->comentario.'
                <strong>Estado:</strong> '.$request->estado.'',
                'sanciones',
                'usuario/sancion',
                null,
                '<i class="gavel icon"></i>'
            ));

            return response()->json([
                'header' => '✅ Editado correctamente',
                'message' => 'Los datos de la sanción fueron actualizados',
            ]);
        } catch (\Exception $e) {
            Log::error('Error al actualizar la sanción: '.$e->getMessage());

            return response()->json(['header' => '❌ Error', 'message' => 'Error al actualizar, contacta al desarrollador Christian Martínez']);
        }
    }
}
