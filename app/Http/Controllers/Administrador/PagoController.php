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
                'vencimiento' => 'required|date|after_or_equal:hoy',
            ]);

            // 2. Crear el encabezado del pago
            $pagoHeader = Pago::create([
                'concepto' => $request->concepto,
                'cantidad' => $request->cantidad,
                'vencimiento' => $request->vencimiento,
                'created_by' => Auth::user()->id,
            ]);

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
            foreach ($usuarios as $usuario) {
                $pago = Detallepago::create([
                    'pago_id' => $pagoHeader->id,
                    'user_id' => $usuario->id,
                ]);

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

                try {
                    $this->sendmail->enviarCorreoPersonal($usuario->correo, $subjectmail, $titulomail, $mensajemail);
                } catch (\Exception $mailEx) {
                    Log::warning("❌ Error al enviar correo a {$usuario->correo}: ".$mailEx->getMessage());
                }
            }

            return response()->json([
                'success' => true,
                'header' => 'Recibo registrado ✅',
                'message' => 'El recibo fue registrado exitosamente y se notificó a los usuarios.',
            ]);
        } catch (\Exception $e) {
            Log::error('❌ Error al crear pago: '.$e->getMessage());

            return response()->json([
                'header' => '❌ Error',
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function obtenerPagos(Request $request)
    {
        if ($request->ajax()) {
            $pagos = Pago::with('user')
                ->select(['id', 'concepto', 'cantidad', 'vencimiento', 'created_by']);

            return datatables()->of($pagos)
                ->addColumn('autor', fn ($c) => $c->user->nombre ?? '—')
                ->addColumn('progreso', function ($c) {
                    $total = $c->detalles()->count();
                    $conComprobante = $c->detalles()->whereNotNull('path_pago')->count();

                    if ($total === 0) {
                        return '<div class="progress-container" style="min-width: 120px;">
                                    <div style="font-size: 0.75rem; color: #94a3b8;">Sin usuarios</div>
                                </div>';
                    }

                    $porcentaje = round(($conComprobante / $total) * 100);
                    $color = $porcentaje >= 100 ? '#10b981' : ($porcentaje >= 50 ? '#f59e0b' : '#ef4444');

                    return '<div class="progress-container" style="min-width: 120px;">
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px;">
                                    <span style="font-size: 0.75rem; color: #64748b;">'.$conComprobante.'/'.$total.'</span>
                                    <span style="font-size: 0.75rem; font-weight: 600; color: '.$color.';">'.$porcentaje.'%</span>
                                </div>
                                <div style="background: #e5e7eb; border-radius: 4px; height: 6px; overflow: hidden;">
                                    <div style="background: '.$color.'; height: 100%; width: '.$porcentaje.'%; border-radius: 4px; transition: width 0.3s ease;"></div>
                                </div>
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
        $info = Pago::where('id', $id_pago)->first();

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
                    $vencimiento = \Carbon\Carbon::parse($c->pago->vencimiento);
                    $fechaPago = \Carbon\Carbon::parse($c->updated_at);
                    $esTarde = $fechaPago->isAfter($vencimiento->endOfDay());

                    $badge = '';
                    if ($esTarde) {
                        $badge = ' <span class="ui orange mini label" title="Pago realizado después del vencimiento"><i class="clock icon"></i> Tarde</span>';
                    }

                    return '<button class="ui mini secondary button btn-ver-evidencia" data-img="'.$url.'">
                                Ver pago
                            </button>'.$badge;
                }

                return '<a class="ui grey label">Pendiente</a>';
            })
            ->addColumn('estado', function ($c) {
                $vencimiento = \Carbon\Carbon::parse($c->pago->vencimiento);
                $fechaPago = \Carbon\Carbon::parse($c->updated_at);
                $esTarde = $fechaPago->isAfter($vencimiento->endOfDay());

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
                    $color = 'gray';
                    $text = 'pendiente';
                } else {
                    $color = 'green';
                    $text = '$'.number_format($c->cantidad_pago, 2);
                }

                return '<a class="ui '.$color.' label">'.$text.'</a>';
            })
            ->addColumn('acciones', function ($c) {
                return '<div class="ui center aligned buttons">
                                <button class="ui blue small icon button btn-editar" data-id="'.$c->id.'" 
                                    data-estado="'.e($c->estado).'"
                                    data-cantidad-pago="'.$c->cantidad_pago.'">
                                    <i class="edit icon"></i>
                                </button>
                                <button class="ui red small icon button btn-eliminar" data-id="'.$c->id.'">
                                    <i class="trash icon"></i>
                                </button>
                            </div>';
            })
            ->rawColumns(['acciones', 'pago', 'estado', 'cantidad_pago'])
            ->make(true);
    }

    public function actualizarPago($id, Request $request)
    {
        try {
            $pago = Detallepago::findOrFail($id);

            $data = ['estado' => $request->estado];

            if ($request->filled('cantidad_pago')) {
                $data['cantidad_pago'] = $request->cantidad_pago;
            }

            $pago->update($data);

            // notificar a usuario de actualización
            $correo = $pago->user->correo;
            $subject = '🔃 Actualización de tu pago';
            $titulo = 'Pago actualizado';
            $montoMostrar = $pago->cantidad_pago ?? $pago->pago->cantidad;
            $mensaje = 'Se actualizó el estado de tu pago. <br><br>
            <strong>Concepto:</strong> '.$pago->pago->concepto.'<br>
            <strong>Monto:</strong> $'.number_format($montoMostrar, 2).'<br>
            <strong>Estado:</strong> '.$pago->estado;

            $this->sendmail->enviarCorreoPersonal($correo, $subject, $titulo, $mensaje);

            /** @var \App\Models\User $usuario */
            // Notificación in-app Web
            $usuario = User::withTrashed()->find($pago->user->id);
            $usuario->notify(new NotificacionGenerica(
                'Actualización de pago',
                'Se actualizó el estado de tu pago.
                <strong>Concepto:</strong> '.$pago->pago->concepto.'
                <strong>Monto:</strong> $'.number_format($montoMostrar, 2).'
                <strong>Estado:</strong> '.$pago->estado,
                'pagos',
                'usuario/pago',
                null,
                '<i class="money bill wave icon"></i>'
            ));

            return response()->json([
                'header' => '✅ Editado correctamente',
                'message' => 'El estado fue actualizado',
            ]);
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
