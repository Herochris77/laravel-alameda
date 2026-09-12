<?php

namespace App\Http\Controllers;

use App\Models\SolicitudPermiso;
use App\Models\User;
use App\Notifications\NotificacionGenerica;
use App\Services\MailService;
use Illuminate\Http\Request;

class SolicitudController extends Controller
{
    public function index()
    {
        $solicitudes = SolicitudPermiso::where('inquilino_id', auth()->user()->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('usuario.solicitudes.index', compact('solicitudes'));
    }

    public function create()
    {
        $dueno = User::where('tipo', 'dueño')
            ->where('casa', auth()->user()->casa)
            ->where('estado', 1)
            ->first();

        if (! $dueno) {
            return redirect()->route('usuario.solicitudes.index')
                ->with('error', 'No hay un dueño asignado a tu departamento.');
        }

        return view('usuario.solicitudes.create', compact('dueno'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'mensaje' => 'required|string|max:5000',
        ]);

        $dueno = User::where('tipo', 'dueño')
            ->where('casa', auth()->user()->casa)
            ->where('estado', 1)
            ->first();

        if (! $dueno) {
            return response()->json(['success' => false, 'message' => 'No hay un dueño asignado.'], 422);
        }

        $solicitud = SolicitudPermiso::create([
            'inquilino_id' => auth()->user()->id,
            'dueno_id' => $dueno->id,
            'titulo' => $request->titulo,
            'mensaje' => $request->mensaje,
        ]);

        $inquilino = auth()->user();

        MailService::enviar(
            $dueno->correo,
            subject: 'Nueva solicitud de '.$inquilino->nombre,
            titulo: 'Solicitud de permiso',
            mensaje: 'El inquilino <strong>'.$inquilino->nombre.'</strong> (casa '.$inquilino->casa.') ha realizado una solicitud:<br><br>
                <strong>'.$solicitud->titulo.'</strong><br><br>
                '.nl2br(e($solicitud->mensaje)).'<br><br>
                <a href="'.url('/perfil/solicitudes').'" style="display:inline-block;padding:12px 28px;background:#667eea;color:#fff;text-decoration:none;border-radius:8px;">Ver solicitud</a>',
            origen: 'solicitud'
        );

        $dueno->notify(new NotificacionGenerica(
            titulo: 'Nueva solicitud de '.$inquilino->nombre,
            mensaje: 'Tienes una nueva solicitud: "'.$solicitud->titulo.'"',
            tipo: 'solicitud_permiso',
            url: 'usuario/perfil/solicitudes',
            icono: '<i class="file alternate outline icon"></i>'
        ));

        return response()->json(['success' => true, 'message' => 'Solicitud enviada correctamente.']);
    }

    public function indexDueno()
    {
        $userId = auth()->user()->id;

        $pendientes = SolicitudPermiso::where('dueno_id', $userId)
            ->where('estado', 'pendiente')
            ->orderBy('created_at', 'desc')
            ->get();

        $historial = SolicitudPermiso::where('dueno_id', $userId)
            ->whereIn('estado', ['aprobado', 'rechazado'])
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('perfil.solicitudes', compact('pendientes', 'historial'));
    }

    public function responder(Request $request, $id)
    {
        $request->validate([
            'estado' => 'required|in:aprobado,rechazado',
        ]);

        $solicitud = SolicitudPermiso::where('dueno_id', auth()->user()->id)
            ->findOrFail($id);

        $solicitud->update([
            'estado' => $request->estado,
            'fecha_respuesta' => now(),
        ]);

        $dueno = auth()->user();
        $estadoTexto = $request->estado === 'aprobado' ? 'aprobada' : 'rechazada';

        MailService::enviar(
            $solicitud->inquilino->correo,
            subject: 'Tu solicitud fue '.$estadoTexto,
            titulo: 'Respuesta a tu solicitud',
            mensaje: 'El dueño <strong>'.$dueno->nombre.'</strong> ha '.$estadoTexto.' tu solicitud "<strong>'.$solicitud->titulo.'</strong>".<br><br>
                <a href="'.url('/usuario/solicitudes').'" style="display:inline-block;padding:12px 28px;background:#667eea;color:#fff;text-decoration:none;border-radius:8px;">Ver mis solicitudes</a>',
            origen: 'solicitud'
        );

        $solicitud->inquilino->notify(new NotificacionGenerica(
            titulo: 'Solicitud '.$estadoTexto,
            mensaje: 'Tu solicitud "'.$solicitud->titulo.'" fue '.$estadoTexto.' por el dueño.',
            tipo: 'respuesta_solicitud',
            url: '/usuario/solicitudes',
            icono: '<i class="'.($request->estado === 'aprobado' ? 'check circle' : 'times circle').' outline icon"></i>'
        ));

        return response()->json(['success' => true, 'message' => 'Solicitud '.$estadoTexto.' correctamente.']);
    }

    public function eliminar($id)
    {
        $solicitud = SolicitudPermiso::where('inquilino_id', auth()->user()->id)
            ->where('estado', 'pendiente')
            ->findOrFail($id);

        $solicitud->delete();

        $inquilino = auth()->user();
        $dueno = $solicitud->dueno;

        MailService::enviar(
            $dueno->correo,
            subject: 'Solicitud eliminada por '.$inquilino->nombre,
            titulo: 'Solicitud de permiso eliminada',
            mensaje: 'El inquilino <strong>'.$inquilino->nombre.'</strong> (casa '.$inquilino->casa.') ha eliminado la solicitud "<strong>'.$solicitud->titulo.'</strong>".<br><br>
                <a href="'.url('/perfil/solicitudes').'" style="display:inline-block;padding:12px 28px;background:#667eea;color:#fff;text-decoration:none;border-radius:8px;">Ver solicitudes</a>',
            origen: 'solicitud'
        );

        $dueno->notify(new NotificacionGenerica(
            titulo: 'Solicitud eliminada',
            mensaje: 'El inquilino '.$inquilino->nombre.' eliminó la solicitud "'.$solicitud->titulo.'".',
            tipo: 'solicitud_permiso',
            url: '/perfil/solicitudes',
            icono: '<i class="trash alternate outline icon"></i>'
        ));

        return response()->json(['success' => true, 'message' => 'Solicitud eliminada correctamente.']);
    }
}
