<?php

namespace App\Http\Controllers\invitado;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Email\EmailController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class PasswordController extends Controller
{
    /** Minutos que dura un enlace de recuperación. */
    private const VIGENCIA_ENLACE = 60;

    protected $sendmail;

    // Inyección de dependecia al controlador de correos
    public function __construct(EmailController $sendmail)
    {
        $this->sendmail = $sendmail;
    }

    public function verify($id, $token)
    {
        $user = User::where('id', $id)->first();

        if (! $user) {
            return redirect()->route('login')
                ->with('status', '❌ No se pudo verificar tu cuenta. El enlace no es válido.');
        }

        if ($user->estado == 1) {
            return redirect()->route('login')
                ->with('status', '✔ Tu cuenta ya fue verificada. Ahora puedes iniciar sesión.');
        }

        if (! hash_equals((string) $user->token_auth, (string) $token)) {
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

    /**
     * Envía el enlace de recuperación.
     *
     * La respuesta es la MISMA exista o no el correo. Si dijera "ese correo no
     * está registrado", cualquiera podría averiguar quién usa la plataforma
     * probando direcciones una por una.
     */
    public function restart(Request $request)
    {
        $request->validate(['email' => ['required', 'string', 'email', 'max:255']]);

        $correo = Str::lower(trim($request->email));

        // Tope por correo: sin él, alguien puede mandar cien correos de
        // recuperación a un vecino (o agotar la cuota de envío del hosting).
        $clave = 'pwreset-correo:'.sha1($correo);

        if (RateLimiter::tooManyAttempts($clave, 3)) {
            return redirect()->route('restart.password.index')->with('correo_enviado', true);
        }

        RateLimiter::hit($clave, 15 * 60);

        $user = User::where('correo', $correo)->where('estado', 1)->first();

        if ($user) {
            $token = Str::random(60);

            // El token NO se guarda: solo su huella, y con caducidad. Si se
            // filtrara la base, los enlaces pendientes no servirían de nada.
            Cache::put('pwreset:'.$user->id, hash('sha256', $token), now()->addMinutes(self::VIGENCIA_ENLACE));

            $url = url('/restablecer/contraseña/actualizar/'.$user->id.'/'.$token);
            $subject = '🔃 Recuperación de contraseña';
            $titulo = '¡Hola '.$user->nombre.'!';
            $mensaje = 'para poder restablecer tu contraseña de acceso a la plataforma de Alameda Condominio, por favor haz clic
            <a href="'.$url.'"><strong>aquí</strong></a>
            <br><br>
            El enlace funciona durante '.self::VIGENCIA_ENLACE.' minutos y una sola vez.
            <br><br><br><br>
            <strong>Si no fuiste tú, por favor ignora este correo</strong>';

            $this->sendmail->enviarCorreoPersonal($user->correo, $subject, $titulo, $mensaje);
        }

        return redirect()->route('restart.password.index')->with('correo_enviado', true);
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

        // 2. El enlace debe estar vigente y coincidir. hash_equals evita que el
        //    tiempo de respuesta delate cuántos caracteres del token son correctos.
        $clave = 'pwreset:'.$request->id;
        $esperado = Cache::get($clave);

        if (! $esperado || ! hash_equals($esperado, hash('sha256', (string) $request->token))) {
            return redirect()->route('restart.password.index')
                ->with('invalid_token', true);
        }

        $user = User::where('id', $request->id)->where('estado', 1)->first();

        if (! $user) {
            return redirect()->route('restart.password.index')
                ->with('invalid_token', true);
        }

        // 3. Actualiza la contraseña. El enlace se consume: sirve una sola vez.
        $user->pass = Hash::make($request->pass);
        // Cambiar el token de "recordarme" cierra las sesiones que quedaron abiertas
        // en otros dispositivos con la contraseña anterior.
        $user->setRememberToken(Str::random(60));
        $user->save();

        Cache::forget($clave);

        // 4. Notifica al usuario y redirige
        return redirect()->route('login')
            ->with('status', '✔ Tu contraseña fue actualizada correctamente. Ahora puedes iniciar sesión.');
    }
}
