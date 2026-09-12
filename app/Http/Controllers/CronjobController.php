<?php

namespace App\Http\Controllers;

use App\Models\Detallepago;
use App\Models\Estacionamiento;
use App\Models\Reserva;
use App\Notifications\NotificacionGenerica;
use App\Services\MailService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class CronjobController extends Controller
{
    public function enviarNotificacionesDiarias(): JsonResponse
    {
        $resultados = [
            'pagos' => ['enviados' => 0, 'errores' => 0],
            'reservaciones' => ['enviados' => 0, 'errores' => 0],
            'estacionamientos' => ['enviados' => 0, 'errores' => 0],
        ];

        $manana = Carbon::tomorrow()->startOfDay();
        $mananaFin = Carbon::tomorrow()->endOfDay();

        $this->notificarPagosProximos($resultados);
        $this->notificarReservacionesProximas($manana, $mananaFin, $resultados);
        $this->notificarEstacionamientosOcupados($resultados);

        Log::info('Cronjob notificaciones diarias ejecutado', [
            'pagos_notificados' => $resultados['pagos']['enviados'],
            'reservaciones_notificadas' => $resultados['reservaciones']['enviados'],
            'estacionamientos_recordados' => $resultados['estacionamientos']['enviados'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Notificaciones diarias procesadas',
            'resultados' => $resultados,
            'ejecutado_en' => now()->toDateTimeString(),
        ]);
    }

    private function notificarPagosProximos(array &$resultados): void
    {
        $pagosSinComprobante = Detallepago::with(['pago', 'user'])
            ->where('estado', 'pendiente')
            ->whereNull('path_pago')
            ->get();

        foreach ($pagosSinComprobante as $detalle) {
            if (! $detalle->user || ! $detalle->pago) {
                continue;
            }

            $vencimiento = Carbon::parse($detalle->pago->vencimiento);
            $manana = Carbon::tomorrow()->startOfDay();

            if ($vencimiento->isSameDay($manana)) {
                $usuario = $detalle->user;

                if ($usuario->emails != 1) {
                    continue;
                }

                $correo = $usuario->correo;
                $asunto = '⚠️ URGENTE: Tu pago vence mañana - Alameda';
                $titulo = '¡Pago pendiente sin comprobante!';
                $mensaje = "Hola {$usuario->nombre}, te informamos que tienes un pago pendiente que vence <strong>mañana</strong> y aún no has subido tu comprobante de pago.<br><br>
                           <strong>Concepto:</strong> {$detalle->pago->concepto}<br>
                           <strong>Monto:</strong> $".number_format($detalle->pago->cantidad, 2)."<br>
                           <strong>Fecha de vencimiento:</strong> {$vencimiento->translatedFormat('j \\d\\e F \\d\\e Y')}<br><br>
                           <strong>⚠️ IMPORTANTE:</strong> Es necesario que subas tu comprobante de pago lo antes posible.";

                try {
                    $usuario->notify(new NotificacionGenerica(
                        '⚠️ Pago sin comprobante',
                        "Tu pago de <strong>{$detalle->pago->concepto}</strong> vence mañana y no has subido comprobante.",
                        'Sube tu comprobante de pago para evitar sanciones.',
                        'usuario/pago',
                        'usuario/pago',
                        null,
                        '<i class="exclamation triangle icon"></i>'
                    ));

                    MailService::enviar(
                        $correo,
                        subject: $asunto,
                        titulo: $titulo,
                        mensaje: $mensaje,
                        origen: 'cronjob.pagos'
                    );

                    $resultados['pagos']['enviados']++;
                } catch (\Exception $e) {
                    Log::error("Error al notificar pago sin comprobante a usuario {$usuario->id}: ".$e->getMessage());
                    $resultados['pagos']['errores']++;
                }
            }
        }
    }

    private function notificarReservacionesProximas(Carbon $fechaInicio, Carbon $fechaFin, array &$resultados): void
    {
        $reservacionesProximas = Reserva::with('user')
            ->whereBetween('fecha_reserva', [$fechaInicio->toDateString(), $fechaFin->toDateString()])
            ->get();

        foreach ($reservacionesProximas as $reserva) {
            if (! $reserva->user) {
                continue;
            }

            $usuario = $reserva->user;

            if ($usuario->emails != 1) {
                continue;
            }

            $fechaReserva = Carbon::parse($reserva->fecha_reserva);

            $correo = $usuario->correo;
            $asunto = '🏖️ Recordatorio: Tu reservación de '.ucfirst($reserva->espacio).' es mañana - Alameda';
            $titulo = '¡Recordatorio de reservación!';
            $mensaje = "Hola {$usuario->nombre}, te informamos que tienes una reservación confirmada para <strong>mañana</strong>.<br><br>
                       <strong>Espacio:</strong> ".ucfirst($reserva->espacio)."<br>
                       <strong>Fecha:</strong> {$fechaReserva->translatedFormat('j \\d\\e F \\d\\e Y')}<br>
                       <strong>Horario:</strong> {$reserva->hora_inicio} - {$reserva->hora_fin}<br><br>
                       ¡Te esperamos!";

            try {
                $usuario->notify(new NotificacionGenerica(
                    '🏖️ Reservación mañana',
                    'Tu reservación de <strong>'.ucfirst($reserva->espacio).'</strong> es mañana.',
                    "Horario: {$reserva->hora_inicio} - {$reserva->hora_fin}",
                    'usuario/reserva/mis-reservas',
                    'usuario/reserva/mis-reservas',
                    null,
                    '<i class="calendar check icon"></i>'
                ));

                MailService::enviar(
                    $correo,
                    subject: $asunto,
                    titulo: $titulo,
                    mensaje: $mensaje,
                    origen: 'cronjob.reservaciones'
                );

                $resultados['reservaciones']['enviados']++;
            } catch (\Exception $e) {
                Log::error("Error al notificar reservación a usuario {$usuario->id}: ".$e->getMessage());
                $resultados['reservaciones']['errores']++;
            }
        }
    }

    private function notificarEstacionamientosOcupados(array &$resultados): void
    {
        $ayer = Carbon::yesterday()->startOfDay();

        $estacionamientosOcupados = Estacionamiento::with('usuario')
            ->where('estado', 'ocupado')
            ->where('fecha_inicio', '<=', $ayer->toDateString())
            ->get();

        foreach ($estacionamientosOcupados as $estacionamiento) {
            if (! $estacionamiento->usuario) {
                continue;
            }

            $usuario = $estacionamiento->usuario;

            if ($usuario->emails != 1) {
                continue;
            }

            $esEscalera = $estacionamiento->ubicacion === 'escalera';
            $fechaOcupado = Carbon::parse($estacionamiento->fecha_inicio)->translatedFormat('j \\d\\e F \\d\\e Y');

            $correo = $usuario->correo;

            if ($esEscalera) {
                $asunto = '🪜 Recordatorio: Libera la escalera comunitaria - Alameda';
                $titulo = '¿Ya devolviste la escalera comunitaria?';
                $mensaje = "Hola {$usuario->nombre}, el <strong>{$fechaOcupado}</strong> marcaste como ocupada la <strong>{$estacionamiento->nombre}</strong> y desde entonces no la has liberado.<br><br>
                           Si ya terminaste de usarla, por favor <strong>libérala</strong> desde la aplicación para que otros vecinos puedan usarla.<br><br>
                           <a href=\"".url('usuario/estacionamiento')."\" style=\"display: inline-block; padding: 12px 28px; background: linear-gradient(135deg, #667eea, #764ba2); color: #fff; text-decoration: none; border-radius: 8px; font-weight: 600;\">Ir al módulo</a>";
                $notiTitulo = '🪜 Libera la escalera';
                $notiMensaje = "Marcaste como ocupada <strong>{$estacionamiento->nombre}</strong> el {$fechaOcupado} y aún no la liberas.";
                $notiIcono = '<i class="toolbox icon"></i>';
            } else {
                $asunto = '🅿️ Recordatorio: Libera tu cajón de estacionamiento - Alameda';
                $titulo = '¿Ya liberaste tu cajón?';
                $mensaje = "Hola {$usuario->nombre}, el <strong>{$fechaOcupado}</strong> marcaste como ocupado el cajón <strong>{$estacionamiento->nombre}</strong> y desde entonces no lo has liberado.<br><br>
                           Si ya desocupaste el cajón, por favor <strong>líbralo</strong> desde la aplicación para que otros vecinos puedan usarlo.<br><br>
                           <a href=\"".url('usuario/estacionamiento')."\" style=\"display: inline-block; padding: 12px 28px; background: linear-gradient(135deg, #667eea, #764ba2); color: #fff; text-decoration: none; border-radius: 8px; font-weight: 600;\">Ir a estacionamiento</a>";
                $notiTitulo = '🅿️ Libera tu cajón';
                $notiMensaje = "Marcaste como ocupado <strong>{$estacionamiento->nombre}</strong> el {$fechaOcupado} y aún no lo liberas.";
                $notiIcono = '<i class="car icon"></i>';
            }

            try {
                $usuario->notify(new NotificacionGenerica(
                    $notiTitulo,
                    $notiMensaje,
                    'Si ya lo desocupaste, libéralo para que otros puedan usarlo.',
                    'usuario/estacionamiento',
                    'usuario/estacionamiento',
                    $notiIcono
                ));

                MailService::enviar(
                    $correo,
                    subject: $asunto,
                    titulo: $titulo,
                    mensaje: $mensaje,
                    origen: 'cronjob.estacionamientos'
                );

                $resultados['estacionamientos']['enviados']++;
            } catch (\Exception $e) {
                Log::error("Error al notificar estacionamiento ocupado a usuario {$usuario->id}: ".$e->getMessage());
                $resultados['estacionamientos']['errores']++;
            }
        }
    }
}
