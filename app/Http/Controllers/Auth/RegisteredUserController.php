<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Email\EmailController;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    protected $sendmail;

    // Inyección de dependecia al controlador de correos
    public function __construct(EmailController $sendmail)
    {
        $this->sendmail = $sendmail;
    }

    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'correo' => ['required', 'email:rfc,dns', 'max:255'],
            'pass' => ['required', 'confirmed', 'min:8'],
            'celular' => ['required', 'string', 'max:20'],
            'casa' => ['required', 'integer', 'between:1,42'],
            'tipo' => ['required', 'string'],
        ]);

        $validarmail = User::where('correo', $request->correo)->exists();

        if ($validarmail) {
            return back()->withErrors([
                'correo' => 'Ya existe un usuario con el correo '.$request->correo,
            ])->withInput();
        }

        $existeTipo = User::where('tipo', $request->tipo)->where('casa', $request->casa)->exists();

        if ($existeTipo) {
            if ($request->tipo === 'inquilino') {
                return back()->withErrors([
                    'tipo' => 'Ya existe un inquilino registrado para la casa #'.$request->casa.'. Solo se permite un inquilino por casa.',
                ])->withInput();
            }

            return back()->withErrors([
                'tipo' => 'Ya existe un propietario registrado para la casa #'.$request->casa,
            ])->withInput();
        }

        $token_auth = Str::random(60);
        $esInquilino = $request->tipo == 'inquilino';

        Log::info('=== INICIO DE REGISTRO ===');

        $user = User::create([
            'nombre' => $request->nombre,
            'correo' => $request->correo,
            'pass' => Hash::make($request->pass),
            'celular' => $request->celular,
            'casa' => $request->casa,
            'tipo' => $request->tipo,
            'rol' => 'usuario',
            'token_auth' => $token_auth,
            'pago' => $esInquilino ? 0 : 1,
            'estado' => $esInquilino ? 3 : 0,
        ]);

        Log::info("Usuario creado con ID: {$user->id}, tipo: {$user->tipo}, estado: {$user->estado}");

        if ($esInquilino) {
            Log::info('Redirigiendo como INQUILINO a '. $request->nombre . ' de la casa '. $request->casa . ' con correo: ' . $request->correo);

            return redirect()->route('login')->with('registro_exitoso_inquilino', true);
        }

        Log::info('Enviando correo de verificacion...');
        Log::info("Correo a: {$request->correo}");
        Log::info("Nombre: {$request->nombre}");

        $correo = $request->correo;
        $subject = '✔ Verificación de acceso';
        $titulo = '¡Hola '.$request->nombre.'!';
        $url = url('/verificación/'.$user->id.'/'.$token_auth);
        $mensaje = 'Solo falta un paso para que puedas acceder a la plataforma de Alameda Condominio. <br><br>
            Para <strong>verificar</strong> tu cuenta, por favor haz clic 
            <a href="'.$url.'"><strong>aquí</strong></a>
            <br><br><br><br>
            <strong>Si no fuiste tú, por favor ignora este correo</strong>';

        Log::info("URL de verificacion: {$url}");

        try {
            $this->sendmail->enviarCorreoPersonal($correo, $subject, $titulo, $mensaje, true);
            Log::info('Correo enviado exitosamente');
        } catch (\Exception $e) {
            Log::error('ERROR al enviar correo: '.$e->getMessage());
        }

        event(new Registered($user));
        Log::info('Redirigiendo como PROPIETARIO (con verificacion)');

        return redirect()->route('login')->with('registro_exitoso', true);
    }
}
