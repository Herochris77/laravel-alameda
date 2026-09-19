<?php

namespace App\Http\Controllers;

use App\Models\Detallepago;
use App\Models\Estacionamiento;
use App\Models\Reserva;
use App\Models\ServicioRecurrente;
use App\Models\User;
use App\Notifications\NotificacionGenerica;
use App\Services\MailService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class CronjobController extends Controller
{
    /**
     * @param  bool  $comoJson  false para abrirlo en el navegador y leerlo.
     */
    public function enviarNotificacionesDiarias(bool $comoJson = true)
    {
        $resultados = [
            'pagos' => ['enviados' => 0, 'errores' => 0],
            'reservaciones' => ['enviados' => 0, 'errores' => 0],
            'estacionamientos' => ['enviados' => 0, 'errores' => 0],
            'servicios' => ['enviados' => 0, 'errores' => 0],
        ];

        $manana = Carbon::tomorrow()->startOfDay();
        $mananaFin = Carbon::tomorrow()->endOfDay();

        $this->notificarPagosProximos($resultados);
        $this->notificarReservacionesProximas($manana, $mananaFin, $resultados);
        $this->notificarEstacionamientosOcupados($resultados);
        $this->notificarServiciosPorVencer($resultados);

        Log::info('Cronjob notificaciones diarias ejecutado', [
            'pagos_notificados' => $resultados['pagos']['enviados'],
            'reservaciones_notificadas' => $resultados['reservaciones']['enviados'],
            'estacionamientos_recordados' => $resultados['estacionamientos']['enviados'],
            'servicios_recordados' => $resultados['servicios']['enviados'],
        ]);

        if ($comoJson) {
            return response()->json([
                'success' => true,
                'message' => 'Notificaciones diarias procesadas',
                'resultados' => $resultados,
                'ejecutado_en' => now()->toDateTimeString(),
            ]);
        }

        return $this->resumenLegible($resultados);
    }

    /**
     * Resultado en HTML, para cuando se ejecuta a mano desde el navegador.
     *
     * Un JSON con ceros no dice si el sistema falló o si simplemente hoy no
     * había nada que mandar. Aquí se explica el porqué de cada cero, que es
     * justo la duda que deja el botón.
     */
    private function resumenLegible(array $resultados)
    {
        $etiquetas = [
            'pagos' => ['Recordatorios de pago', 'Hoy no vence ni vence mañana ningún recibo, o ya se avisó hoy.'],
            'reservaciones' => ['Reservaciones de mañana', 'Nadie tiene reservación para mañana.'],
            'estacionamientos' => ['Estacionamientos ocupados', 'No hay cajones ocupados desde ayer.'],
            'servicios' => ['Servicios por vencer', 'Ningún servicio entra hoy en su ventana de aviso.'],
        ];

        $filas = '';
        $totalEnviados = 0;
        $totalErrores = 0;

        foreach ($resultados as $clave => $r) {
            [$titulo, $porQueCero] = $etiquetas[$clave] ?? [$clave, ''];

            $totalEnviados += $r['enviados'];
            $totalErrores += $r['errores'];

            $detalle = $r['enviados'] > 0
                ? '<strong style="color:#047857;">'.$r['enviados'].' aviso(s) enviados</strong>'
                : '<span style="color:#64748b;">Nada que enviar. '.$porQueCero.'</span>';

            $err = $r['errores'] > 0
                ? '<div style="color:#b91c1c;margin-top:3px;">'.$r['errores'].' error(es). Revisa el log.</div>'
                : '';

            $filas .= '<tr><td style="padding:9px 12px;border-bottom:1px solid #eef2f7;">'
                .'<strong>'.$titulo.'</strong><div style="margin-top:2px;">'.$detalle.$err.'</div></td></tr>';
        }

        $encabezado = $totalEnviados > 0
            ? '<div style="font-size:1.05rem;color:#047857;">Se enviaron '.$totalEnviados.' aviso(s).</div>'
            : '<div style="font-size:1.05rem;color:#334155;">No había nada que enviar hoy.</div>';

        $html = '<!DOCTYPE html><html lang="es"><head><meta charset="utf-8">'
            .'<title>Notificaciones diarias</title></head>'
            .'<body style="font-family:-apple-system,Segoe UI,Roboto,sans-serif;background:#f8fafc;margin:0;padding:28px;">'
            .'<div style="max-width:560px;margin:0 auto;background:#fff;border:1px solid #e2e8f0;border-radius:14px;overflow:hidden;">'
            .'<div style="background:linear-gradient(135deg,#667eea,#764ba2);color:#fff;padding:18px 22px;">'
            .'<div style="font-weight:700;">Notificaciones diarias</div>'
            .'<div style="font-size:.84rem;opacity:.9;">'.now()->translatedFormat('j \d\e F \d\e Y, H:i').'</div></div>'
            .'<div style="padding:16px 22px 6px;">'.$encabezado.'</div>'
            .'<table style="width:100%;border-collapse:collapse;font-size:.88rem;color:#334155;">'.$filas.'</table>'
            .'<div style="padding:14px 22px;background:#f8fafc;font-size:.8rem;color:#64748b;border-top:1px solid #eef2f7;">'
            .'Los recordatorios de pago salen <strong>el día antes del vencimiento y el mismo día del vencimiento</strong>, '
            .'solo a quien no ha subido comprobante. Un concepto no se avisa dos veces el mismo día, '
            .'aunque ejecutes esto varias veces.'
            .($totalErrores > 0 ? '<div style="color:#b91c1c;margin-top:6px;">Hubo errores: revisa storage/logs/laravel.log</div>' : '')
            .'</div></div></body></html>';

        return response($html);
    }

    private function notificarPagosProximos(array &$resultados): void
    {
        $pagosSinComprobante = Detallepago::with(['pago', 'user'])
            ->where('estado', 'pendiente')
            ->whereNull('path_pago')
            ->get();

        /*
         * El correo de "tu pago vence mañana" es el mismo para todos los que
         * deben el mismo concepto: cambia solo el saludo. Se agrupan por
         * concepto y sale un mensaje con copia oculta por concepto, en vez de
         * uno por vecino. La notificación en la plataforma sí sigue siendo
         * individual, porque ahí no hay cuota que cuidar.
         */
        $porConcepto = [];

        foreach ($pagosSinComprobante as $detalle) {
            if (! $detalle->user || ! $detalle->pago) {
                continue;
            }

            $vencimiento = Carbon::parse($detalle->pago->vencimiento);

            /*
             * Se avisa DOS veces: el día antes y el día del vencimiento.
             *
             * Antes solo salía la víspera. El día límite —que es cuando la
             * gente de verdad se acuerda de pagar— no llegaba nada, y quien
             * no hubiera visto el correo del día anterior se enteraba con el
             * recargo encima.
             */
            $esVispera = $vencimiento->isSameDay(Carbon::tomorrow());
            $esElDia = $vencimiento->isSameDay(Carbon::today());

            if ($esVispera || $esElDia) {
                $usuario = $detalle->user;

                if ($usuario->emails != 1) {
                    continue;
                }

                $cuando = $esElDia ? 'hoy' : 'mañana';

                try {
                    $usuario->notify(new NotificacionGenerica(
                        $esElDia ? '⚠️ Hoy vence tu pago' : '⚠️ Tu pago vence mañana',
                        "Tu pago de <strong>{$detalle->pago->concepto}</strong> vence {$cuando} y no has subido comprobante.",
                        'Sube tu comprobante para evitar el recargo.',
                        'usuario/pago',
                        'usuario/pago',
                        null,
                        '<i class="exclamation triangle icon"></i>'
                    ));

                    // El correo se junta y sale abajo, agrupado por concepto.
                    $clave = $detalle->pago->id;

                    $porConcepto[$clave] ??= [
                        'pago' => $detalle->pago,
                        'vencimiento' => $vencimiento,
                        'es_el_dia' => $esElDia,
                        'correos' => [],
                    ];

                    $porConcepto[$clave]['correos'][] = $usuario->correo;

                    $resultados['pagos']['enviados']++;
                } catch (\Exception $e) {
                    Log::error("Error al notificar pago sin comprobante a usuario {$usuario->id}: ".$e->getMessage());
                    $resultados['pagos']['errores']++;
                }
            }
        }

        foreach ($porConcepto as $grupo) {

            /*
             * Un solo aviso por concepto y por día. El cron corre una vez,
             * pero el botón de ejecución manual se puede pulsar varias veces
             * y no hay por qué mandarle el mismo correo dos veces a los 42
             * vecinos, ni gastar la cuota del proveedor en eso.
             */
            if ($this->yaSeAvisoHoy($grupo['pago'])) {
                continue;
            }

            $esElDia = $grupo['es_el_dia'];

            $recargo = (float) ($grupo['pago']->recargo_pct ?? 0) > 0
                ? '<br><br>Si pagas después de esa fecha se aplica un recargo del '
                    .rtrim(rtrim(number_format((float) $grupo['pago']->recargo_pct, 2), '0'), '.').'%.'
                : '';

            $mensaje = ($esElDia
                    ? '<strong>Hoy es el último día</strong> para cubrir este pago y aún no has subido tu comprobante.'
                    : 'Tienes un pago pendiente que vence <strong>mañana</strong> y aún no has subido tu comprobante.')
                .'<br><br>
                   <strong>Concepto:</strong> '.e($grupo['pago']->concepto).'<br>
                   <strong>Monto:</strong> $'.number_format($grupo['pago']->cantidad, 2).'<br>
                   <strong>Fecha límite:</strong> '.$grupo['vencimiento']->translatedFormat('j \d\e F \d\e Y')
                .$recargo.'<br><br>
                   Si ya pagaste, solo falta subir el comprobante desde la plataforma.
                   Recuerda capturar la <strong>fecha real de tu transferencia</strong>: es la que
                   cuenta, no el día en que lo subes.';

            try {
                MailService::enviar(
                    $grupo['correos'],
                    subject: $esElDia
                        ? '⚠️ HOY vence tu pago - Alameda'
                        : '⚠️ Tu pago vence mañana - Alameda',
                    titulo: $esElDia
                        ? 'Hoy es el último día'
                        : 'Tu pago vence mañana',
                    mensaje: $mensaje,
                    origen: 'cronjob.pagos'
                );

                $grupo['pago']->update(['ultimo_aviso' => Carbon::today()->toDateString()]);
            } catch (\Exception $e) {
                Log::error('Error al enviar el recordatorio de pagos por vencer: '.$e->getMessage());
                $resultados['pagos']['errores']++;
            }
        }
    }

    /**
     * ¿Ya salió hoy el aviso de este concepto?
     *
     * Si la columna no existe todavía —producción antes de correr /migrar—
     * se responde que no: vale más un correo repetido que quedarse sin
     * mandar el recordatorio.
     */
    private function yaSeAvisoHoy($pago): bool
    {
        if (! Schema::hasColumn('pagos', 'ultimo_aviso')) {
            return false;
        }

        return $pago->ultimo_aviso
            && Carbon::parse($pago->ultimo_aviso)->isSameDay(Carbon::today());
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

    /**
     * Avisa a la tesorería de los servicios que están por vencer.
     *
     * Cada servicio define con cuántos días de anticipación quiere el aviso
     * (uno por omisión). Los vencidos se siguen recordando todos los días,
     * porque el olvido es justo lo que este módulo intenta evitar.
     *
     * El aviso se marca con la fecha en `ultimo_aviso`: si el cron corre dos
     * veces el mismo día, el segundo no vuelve a escribir ni a mandar correo.
     */
    private function notificarServiciosPorVencer(array &$resultados): void
    {
        if (! Schema::hasTable('servicios_recurrentes')) {
            return;
        }

        $destinatarios = $this->tesoreria();

        if ($destinatarios->isEmpty()) {
            return;
        }

        $hoy = Carbon::today();

        $servicios = ServicioRecurrente::activos()
            ->whereNotNull('proximo_vencimiento')
            ->get()
            ->filter(function ($s) use ($hoy) {
                // Ya se avisó hoy de este servicio.
                if ($s->ultimo_aviso && $s->ultimo_aviso->isSameDay($hoy)) {
                    return false;
                }

                return in_array($s->estatus(), ['vencido', 'hoy', 'por_vencer'], true);
            });

        foreach ($servicios as $servicio) {
            try {
                $this->avisarServicio($servicio, $destinatarios);

                $servicio->update(['ultimo_aviso' => $hoy->toDateString()]);

                $resultados['servicios']['enviados']++;
            } catch (\Exception $e) {
                Log::error("Error al notificar servicio {$servicio->id}: ".$e->getMessage());
                $resultados['servicios']['errores']++;
            }
        }
    }

    /**
     * A quién le toca enterarse: el tesorero.
     *
     * Si todavía no hay nadie con el cargo, el aviso va a toda la mesa, que
     * es el mismo criterio de transición que usa User::puedeGestionarPagos().
     */
    private function tesoreria()
    {
        $tesoreros = User::where('cargo', 'tesorero')
            ->where('estado', 1)
            ->whereIn('rol', ['administrador', 'super-administrador'])
            ->get();

        if ($tesoreros->isNotEmpty()) {
            return $tesoreros;
        }

        return User::whereIn('rol', ['administrador', 'super-administrador'])
            ->where('estado', 1)
            ->get();
    }

    private function avisarServicio(ServicioRecurrente $servicio, $destinatarios): void
    {
        $estatus = $servicio->estatus();
        $dias = abs((int) $servicio->diasRestantes());
        $vence = $servicio->proximo_vencimiento->translatedFormat('j \d\e F \d\e Y');
        $monto = '$'.number_format($servicio->monto_estimado, 2);

        [$icono, $encabezado, $urgencia] = match ($estatus) {
            'vencido' => [
                '🔴',
                $dias === 1 ? 'Venció ayer' : "Venció hace {$dias} días",
                'Este pago ya está vencido. Puede estar generando recargos.',
            ],
            'hoy' => [
                '⚠️',
                'Vence hoy',
                'Hoy es el último día para pagarlo sin recargo.',
            ],
            default => [
                '🗓️',
                $dias === 1 ? 'Vence mañana' : "Vence en {$dias} días",
                'Prepara el pago para no dejarlo caer en recargo.',
            ],
        };

        $referencia = $servicio->referencia
            ? "<strong>Referencia:</strong> {$servicio->referencia}<br>"
            : '';

        $mensajeCorreo = "{$urgencia}<br><br>
                          <strong>Servicio:</strong> {$servicio->nombre}<br>
                          ".($servicio->proveedor ? "<strong>Proveedor:</strong> {$servicio->proveedor}<br>" : '')."
                          {$referencia}
                          <strong>Monto estimado:</strong> {$monto}<br>
                          <strong>Vencimiento:</strong> {$vence}<br>
                          <strong>Periodicidad:</strong> {$servicio->etiquetaPeriodicidad()}<br>
                          <strong>Forma de pago:</strong> ".(ServicioRecurrente::FORMAS_PAGO[$servicio->forma_pago] ?? '—').'<br>'
                          .($servicio->notas ? "<br><strong>Notas:</strong> {$servicio->notas}" : '')."<br><br>
                          Cuando lo pagues, regístralo en el módulo de Servicios para que el
                          vencimiento avance al siguiente periodo y deje de avisarte.";

        foreach ($destinatarios as $usuario) {
            // Centro de notificaciones y webpush: siempre. Es la agenda de
            // trabajo del tesorero, no una difusión que convenga silenciar.
            $usuario->notify(new NotificacionGenerica(
                "{$icono} {$encabezado}: {$servicio->nombre}",
                "{$servicio->nombre} por <strong>{$monto}</strong> vence el {$vence}.",
                $urgencia,
                'administrador/servicio',
                'administrador/servicio',
                null,
                '<i class="calendar times icon"></i>'
            ));

            // El correo sí respeta la preferencia de cada quien.
            if ($usuario->emails == 1) {
                MailService::enviar(
                    $usuario->correo,
                    subject: "{$icono} {$encabezado}: {$servicio->nombre} - Alameda",
                    titulo: "{$encabezado}: {$servicio->nombre}",
                    mensaje: $mensajeCorreo,
                    origen: 'cronjob.servicios'
                );
            }
        }
    }
}
