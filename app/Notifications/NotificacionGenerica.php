<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;

class NotificacionGenerica extends Notification
{
    use Queueable;

    protected $titulo;

    protected $mensaje;

    protected $tipo;

    protected $url;

    protected $parametros;

    protected $icono;

    public function __construct($titulo, $mensaje, $tipo, $url = null, $parametros = null, $icono = null)
    {
        $this->titulo = $titulo;
        $this->mensaje = $mensaje;
        $this->tipo = $tipo;
        $this->url = $url;
        $this->parametros = $parametros;
        $this->icono = $icono;
    }

    public function via($notifiable)
    {
        $canales = ['database'];

        // Solo intentamos Web Push si el usuario puede tener suscripciones,
        // si tiene al menos un dispositivo suscrito y si las llaves VAPID
        // están configuradas. Así nunca rompemos la notificación de base de datos.
        if (
            config('webpush.vapid.public_key')
            && method_exists($notifiable, 'pushSubscriptions')
            && $notifiable->pushSubscriptions()->exists()
        ) {
            $canales[] = WebPushChannel::class;
        }

        return $canales;
    }

    public function toDatabase($notifiable)
    {
        return [
            'titulo' => $this->titulo,
            'mensaje' => $this->mensaje,
            'tipo' => $this->tipo,
            'url' => $this->url,
            'parametros' => $this->parametros,
            'icono' => $this->icono,
        ];
    }

    public function toWebPush($notifiable, $notification)
    {
        // El cuerpo puede traer HTML (<strong>, <br>, etc.); lo limpiamos
        // para mostrarlo como texto plano en la notificación del navegador.
        $cuerpo = trim(html_entity_decode(strip_tags(
            str_ireplace(['<br>', '<br/>', '<br />'], ' ', (string) $this->mensaje)
        )));

        $destino = $this->url ? url($this->url) : url('/inicio');

        return (new WebPushMessage)
            ->title($this->limpiar($this->titulo))
            ->icon(url('/img/logo-header.png'))
            ->badge(url('/img/logo-header.png'))
            ->body($cuerpo !== '' ? $cuerpo : $this->limpiar($this->titulo))
            ->tag('alameda-notif')
            ->data(['url' => $destino])
            ->options(['TTL' => 86400]);
    }

    private function limpiar($texto): string
    {
        return trim(html_entity_decode(strip_tags((string) $texto)));
    }
}
