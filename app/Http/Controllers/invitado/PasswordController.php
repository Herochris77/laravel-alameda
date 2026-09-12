<?php

namespace App\Http\Controllers\invitado;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Email\EmailController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PasswordController extends Controller
{
    protected $sendmail;

    // Inyección de dependecia al controlador de correos
    public function __construct(EmailController $sendmail)
    {
        $this->sendmail = $sendmail;
    }

    public function verify($id, $token)
    {
        $user = User::where('id', $id)->first();
    
        if (!$user) {
            return redirect()->route('login')
                ->with('status', '❌ No se pudo verificar tu cuenta. El enlace no es válido.');
        }
    
        if ($user->estado == 1) {
            return redirect()->route('login')
                ->with('status', '✔ Tu cuenta ya fue verificada. Ahora puedes iniciar sesión.');
        }
    
        if (!hash_equals((string) $user->token_auth, (string) $token)) {
            return redirect()->route('login')
                ->with('status', '❌ No se pudo verificar tu cuenta. El enlace no es válido o ya expiró.');
        }
    
        $user->estado = 1;
        $user->token_auth = null;
        $user->save();
    
        return redirect()->route('login')
            ->with('status', '✔ Tu cuenta fue verificada. Ahora puedes iniciar sesión.');
    }

    public function index()
    {
        return view('invitado.index');
    }

    public function restart(Request $request)
    {
        $validarmail = User::where('correo', $request->email)->where('estado', 1);

        if (! $validarmail->exists()) {
            return back()->withErrors([
                'email' => 'No existe el correo '.$request->email.' en la plataforma o la cuenta no ha sido verificada, por favor intenta con un correo verificado/registrado ',
            ])->withInput();
        } else {
            $user = $validarmail->first();
            $newToken = Str::random(60);
            $user->token_auth = $newToken;
            $user->save();
            $nombre = $user->nombre;
            $id = $user->id;
            $url = url('/restablecer/contraseña/actualizar/'.$id.'/'.$newToken);
            // Enviar correo con token para restablecer
            $correo = $request->email;
            $subject = '🔃 Recuperación de contraseña';
            $titulo = '¡Hola '.$nombre.'!';
            $mensaje = 'para poder restablecer tu contraseña de acceso a la plataforma de Alameda Condominio, por favor haz clic 
            <a href="'.$url.'"><strong>aquí</strong></a>
            <br><br><br><br>
            <strong>Si no fuiste tú, por favor ignora este correo</strong>';
            $this->sendmail->enviarCorreoPersonal($correo, $subject, $titulo, $mensaje);

            // notificar al usuario el front
            return redirect()->route('restart.password.index')->with('correo_enviado', true);
        }
    }

    public function update($id, $token)
    {
        return view('invitado.changepass', compact('id', 'token'));
    }

    public function updatePassword(Request $request)
    {
        // 1. Validación de los campos
        $request->validate([
            'id' => 'required|integer|exists:users,id',
            'token' => 'required|string',
            'pass' => ['required', 'min:8'],
            'passconfirm' => ['required', 'same:pass'],
        ], [
            'pass.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'passconfirm.same' => 'Las contraseñas no coinciden.',
        ]);

        // 2. Buscar al usuario por id y token
        $user = User::where('id', $request->id)
            ->where('token_auth', $request->token)
            ->first();

        if (! $user) {
            // Token inválido o ya usado/expirado
            return redirect()->route('restart.password.index')
                ->with('invalid_token', true);
        }

        // 3. Actualiza la contraseña y limpia el token
        $user->pass = Hash::make($request->pass); // Si tu campo se llama "pass"
        $user->token_auth = null; // O genera uno nuevo si prefieres
        $user->save();

        // 4. Notifica al usuario y redirige
        return redirect()->route('login')
            ->with('status', '✔ Tu contraseña fue actualizada correctamente. Ahora puedes iniciar sesión.');
    }
}
