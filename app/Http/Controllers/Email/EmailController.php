<?php

namespace App\Http\Controllers\Email;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\MailService;

class EmailController extends Controller
{
    public function enviarCorreoGeneral($subject, $titulo, $mensaje)
    {
        $correos = User::select('estado', 'correo', 'emails')->where('estado', 1)->where('emails', 1)->pluck('correo')->toArray();

        MailService::enviar(
            $correos,
            subject: $subject,
            titulo: $titulo,
            mensaje: $mensaje,
            origen: 'general'
        );
    }

    public function enviarCorreoPersonal($correo, $subject, $titulo, $mensaje, $ignorarPreferencia = false)
    {
        MailService::enviar(
            destinatarios: $correo,
            subject: $subject,
            titulo: $titulo,
            mensaje: $mensaje,
            ignorarPreferencia: $ignorarPreferencia,
            origen: 'personal'
        );
    }
}
