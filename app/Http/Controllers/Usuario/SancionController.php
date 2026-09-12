<?php

namespace App\Http\Controllers\Usuario;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Email\EmailController;
use App\Models\Sancion;
use App\Models\User;
use App\Notifications\NotificacionGenerica;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SancionController extends Controller
{
    public function index(Request $request)
    {
        $query = Sancion::where('user_id', Auth::user()->id);

        if ($request->filled('filtro')) {
            $filtro = $request->filtro;

            if ($filtro === 'pendiente') {
                $query->where('estado', 'pendiente');
            } elseif ($filtro === 'pagado') {
                $query->where('estado', 'pagado');
            } elseif ($filtro === 'rechazado') {
                $query->where('estado', 'rechazado');
            }
        }

        $sanciones = $query->orderBy('created_at', 'desc')->get();

        return view('usuario.sancion.index', compact('sanciones'));
    }

    public function subirPago(Request $request, $id)
    {
        $sancion = Sancion::findOrFail($id);

        if ($sancion->user_id !== Auth::user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permiso para realizar esta acción.',
            ], 403);
        }

        if ($sancion->estado === 'pagado') {
            return response()->json([
                'success' => false,
                'message' => 'Esta sanción ya ha sido pagada.',
            ], 400);
        }

        $request->validate([
            'cantidad' => 'required|numeric|min:0',
            'comprobante' => 'required|image|mimes:jpeg,png,jpg|max:5120',
        ]);

        try {
            $pagoPath = null;

            if ($request->hasFile('comprobante')) {
                $comprobante = $request->file('comprobante');
                $nombreArchivo = time().'_'.uniqid().'.'.$comprobante->getClientOriginalExtension();
                $directorio = storage_path('app/public/sanciones');

                if (! file_exists($directorio)) {
                    mkdir($directorio, 0755, true);
                }

                $comprobante->move($directorio, $nombreArchivo);
                $pagoPath = 'sanciones/'.$nombreArchivo;
            }

            $usuario = Auth::user();
            $montoAnterior = $sancion->monto;
            $montoNuevo = $request->cantidad;

            $sancion->update([
                'pago_path' => $pagoPath,
                'monto' => $montoNuevo,
                'estado' => 'pendiente',
            ]);

            $emailController = new EmailController;
            $administradores = User::whereIn('rol', ['administrador', 'super-administrador'])
                ->where('estado', 1)
                ->where('emails', 1)
                ->get();

            foreach ($administradores as $admin) {
                $subject = '💰 Nuevo pago de sanción';
                $titulo = 'Pago de sanción recibido';
                $mensaje = 'El usuario <strong>'.$usuario->nombre.'</strong> (Casa #'.$usuario->casa.') ha realizado el pago de su sanción.<br><br>
                    <strong>Motivo:</strong> '.$sancion->motivo.'<br>
                    <strong>Monto aplicado:</strong> $'.number_format($montoAnterior, 2).'<br>
                    <strong>Monto pagado:</strong> $'.number_format($montoNuevo, 2).'<br><br>
                    Por favor verifica el comprobante en el panel de administración.';

                $emailController->enviarCorreoPersonal($admin->correo, $subject, $titulo, $mensaje);

                $admin->notify(new NotificacionGenerica(
                    '💰 Pago de sanción',
                    $usuario->nombre.' (Casa #'.$usuario->casa.') ha subido el pago de su sanción.<br>
                    <strong>Motivo:</strong> '.$sancion->motivo.'<br>
                    <strong>Monto:</strong> $'.number_format($montoNuevo, 2),
                    'sanciones',
                    'administrador/sancion/nueva-sancion',
                    null,
                    '<i class="dollar icon"></i>'
                ));
            }

            return response()->json([
                'success' => true,
                'message' => 'Pago registrado exitosamente. La administración verificará tu comprobante.',
            ]);
        } catch (\Exception $e) {
            Log::error('Error al subir pago: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error al procesar el pago. Por favor intente de nuevo.',
            ], 500);
        }
    }
}
