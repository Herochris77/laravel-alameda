<?php

namespace App\Services;

use App\Mail\CorreoGenerico;
use App\Models\CorreoEnviado;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class MailService
{
    public static function enviar($destinatarios, string $subject, string $titulo, string $mensaje, bool $ignorarPreferencia = false, ?string $origen = null)
    {
        Log::info('=== MailService::enviar() ===');
        Log::info('ignorarPreferencia: '.($ignorarPreferencia ? 'true' : 'false'));

        try {
            $mailable = new CorreoGenerico($subject, $titulo, $mensaje);
            $emails = is_array($destinatarios) ? $destinatarios : [$destinatarios];

            Log::info('Destinatarios originales: '.implode(', ', $emails));

            if ($ignorarPreferencia) {
                $emailsFiltrados = $emails;
                Log::info('Ignorando preferencia de emails - enviando a todos');
            } else {
                $usuariosConEmailHabilitado = \App\Models\User::whereIn('correo', $emails)
                    ->where('emails', 1)
                    ->pluck('correo')
                    ->toArray();

                $emailsFiltrados = array_intersect($emails, $usuariosConEmailHabilitado);
                Log::info('Solo enviando a usuarios con emails=1');
            }

            $emailsFiltrados = array_values($emailsFiltrados);
            Log::info('Emails a enviar: '.implode(', ', $emailsFiltrados));

            if (empty($emailsFiltrados)) {
                Log::warning('No hay destinatarios para enviar el correo.');
            } elseif (count($emailsFiltrados) === 1) {
                try {
                    Mail::to($emailsFiltrados[0])->send($mailable);
                    Log::info("✅ Correo enviado exitosamente a: {$emailsFiltrados[0]}");
                    self::guardarRegistro($emailsFiltrados, $subject, $titulo, $mensaje, $origen, 'enviado');
                } catch (\Exception $ex) {
                    Log::error("❌ Error al enviar correo a {$emailsFiltrados[0]}: ".$ex->getMessage());
                    self::guardarRegistro($emailsFiltrados, $subject, $titulo, $mensaje, $origen, 'error');
                }
            } else {
                try {
                    Mail::bcc($emailsFiltrados)->send($mailable);
                    Log::info('✅ Correo enviado exitosamente a TODOS (BCC): '.implode(', ', $emailsFiltrados));
                    self::guardarRegistro($emailsFiltrados, $subject, $titulo, $mensaje, $origen, 'enviado');
                } catch (\Exception $ex) {
                    Log::error('❌ Error al enviar correo masivo: '.$ex->getMessage());
                    self::guardarRegistro($emailsFiltrados, $subject, $titulo, $mensaje, $origen, 'error');
                }
            }

            if (! $ignorarPreferencia) {
                $emailsNoEnviados = array_diff($emails, $emailsFiltrados);
                if (! empty($emailsNoEnviados)) {
                    Log::info('📧 Correos NO enviados (emails = 0): '.implode(', ', $emailsNoEnviados));
                }
            }
        } catch (\Exception $e) {
            Log::error('❌ Error general en MailService: '.$e->getMessage());
        }
    }

    private static function guardarRegistro(array $emails, string $subject, string $titulo, string $mensaje, ?string $origen, string $estado): void
    {
        try {
            CorreoEnviado::create([
                'destinatarios' => implode(', ', $emails),
                'asunto' => $subject,
                'titulo' => $titulo,
                'mensaje' => $mensaje,
                'origen' => $origen,
                'estado' => $estado,
            ]);
        } catch (\Exception $e) {
            Log::error('Error al guardar registro de correo enviado: '.$e->getMessage());
        }
    }
}
