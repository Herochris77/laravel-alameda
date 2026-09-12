<?php

namespace App\Http\Controllers\Usuario;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Email\EmailController;
use App\Models\SolicitudPermiso;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class PerfilController extends Controller
{
    protected $sendmail;

    // Inyección de dependecia al controlador de correos
    public function __construct(EmailController $sendmail)
    {
        $this->sendmail = $sendmail;
    }

    public function index()
    {
        $inquilino = null;
        $id_user = auth()->user()->id;
        $user = User::where('id', $id_user)->first();

        // validar si existe un inquilino
        $check = User::where('tipo', 'inquilino')->where('casa', auth()->user()->casa)->where('id', '!=', $id_user)->first();

        if ($check) {
            $inquilino = $check;
        }

        $pendientesCount = SolicitudPermiso::where('dueno_id', $id_user)
            ->where('estado', 'pendiente')
            ->count();

        return view('perfil.index', compact('user', 'inquilino', 'pendientesCount'));
    }

    public function actualizarFoto(Request $request)
    {
        $request->validate([
            'foto' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $user = auth()->user();

        // Eliminar foto anterior (si existe en el disco "public")
        if ($user->foto && Storage::disk('public')->exists('perfil/'.$user->foto)) {
            Storage::disk('public')->delete('perfil/'.$user->foto);
        }

        // Guardar foto nueva
        $file = $request->file('foto');
        $nombre = time().'_'.$file->getClientOriginalName();

        Storage::disk('public')->putFileAs('perfil', $file, $nombre);

        // Guardar en BD
        $user->foto = $nombre;
        $user->save();

        return response()->json([
            'message' => 'Fotografía actualizada correctamente',
        ]);
    }

    public function actualizarDatos(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,correo,'.$user->id,
            'telefono' => 'nullable|string|max:10',
            'emails' => 'nullable',
        ]);

        $user->nombre = $request->name;
        $user->correo = $request->email;
        $user->celular = $request->telefono;
        $user->emails = $request->has('emails') ? 1 : 0;

        $user->save();

        return response()->json([
            'header' => 'Datos guardados',
            'message' => 'Tu información se actualizó correctamente',
        ]);
    }

    public function actualizarPassword(Request $request)
    {
        $request->validate(
            [
                'actual' => 'required',
                'nueva' => 'required|min:6',
                'confirmacion' => 'required|same:nueva',
            ],
            [
                'actual.required' => 'Debes ingresar tu contraseña actual',
                'nueva.required' => 'La nueva contraseña es obligatoria',
                'nueva.min' => 'La nueva contraseña debe tener al menos 6 caracteres',
                'confirmacion.required' => 'Debes confirmar la nueva contraseña',
                'confirmacion.same' => 'La confirmación no coincide con la nueva contraseña',
            ]
        );

        $user = auth()->user();

        if (! \Hash::check($request->actual, $user->pass)) {
            return response()->json([
                'message' => 'La contraseña actual es incorrecta',
            ], 422);
        }

        $user->pass = \Hash::make($request->nueva);
        $user->save();

        return response()->json([
            'message' => 'Contraseña actualizada exitosamente',
        ]);
    }

    public function aprobarInquilino($id)
    {
        $inquilino = User::findOrFail($id);

        if ($inquilino->tipo !== 'inquilino') {
            return response()->json([
                'message' => 'El usuario no es un inquilino válido',
            ], 422);
        }

        if ($inquilino->estado == 1) {
            return response()->json([
                'message' => 'El inquilino ya se encuentra aprobado',
            ], 422);
        }

        $inquilino->estado = 1;
        $inquilino->save();

        try {
            $this->sendmail->enviarCorreoPersonal(
                $inquilino->correo,
                'Acceso a Alameda',
                'Tu acceso al sistema de condominio Alameda fue aprobado',
                'El dueño de la casa aprobó tu acceso al sistema. Ahora podrás acceder desde https://alameda-condominio.com.mx'
            );
        } catch (\Throwable $e) {
            Log::error('Error al enviar correo de aprobación', [
                'user_id' => $inquilino->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'Inquilino aprobado, pero ocurrió un error al enviar el correo',
            ], 200);
        }

        return response()->json([
            'message' => 'Inquilino aprobado correctamente',
        ]);
    }

    public function eliminarInquilino($id)
    {
        $inquilino = User::findOrFail($id);

        if ($inquilino->tipo !== 'inquilino') {
            return response()->json([
                'message' => 'El usuario no es un inquilino válido',
            ], 422);
        }

        try {
            $correo = $inquilino->correo;
            $nombre = $inquilino->nombre;
            $inquilinoPago = $inquilino->pago;

            $subject = 'Acceso eliminado - Alameda';
            $titulo = 'Tu acceso al sistema fue eliminado';
            $mensaje = "Hola $nombre, tu acceso al sistema del condominio Alameda fue eliminado por el dueño de la casa.
                    Si crees que se trata de un error, por favor comunícate con el dueño.";

            $this->sendmail->enviarCorreoPersonal(
                $correo,
                $subject,
                $titulo,
                $mensaje
            );

            $inquilino->delete();

            $dueño = auth()->user();
            if ($inquilinoPago == 1) {
                $dueño->pago = 1;
                $dueño->save();
            }
        } catch (\Throwable $e) {

            Log::error('Error al eliminar inquilino', [
                'user_id' => $inquilino->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'Ocurrió un error al eliminar al inquilino',
            ], 500);
        }

        return response()->json([
            'message' => 'Inquilino eliminado correctamente y notificado por correo',
        ]);
    }

    public function actualizarPago(Request $request)
    {
        $request->validate([
            'inquilino_id' => 'required|exists:users,id',
            'pago' => 'required|boolean',
        ]);

        $dueño = auth()->user();
        $inquilino = User::findOrFail($request->inquilino_id);

        if ($request->pago) {
            // Inquilino paga
            $inquilino->pago = 1;
            $dueño->pago = 0;
        } else {
            // Dueño paga
            $inquilino->pago = 0;
            $dueño->pago = 1;
        }

        $inquilino->save();
        $dueño->save();

        return response()->json([
            'message' => 'Configuración de pagos actualizada correctamente',
        ]);
    }
}
