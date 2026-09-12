<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CorreoGenerico extends Mailable
{
    use Queueable, SerializesModels;

    public $titulo;

    public $mensaje;

    public $subjectLine;

    public function __construct($subject, $titulo, $mensaje)
    {
        $this->subjectLine = $subject;
        $this->titulo = $titulo;
        $this->mensaje = $mensaje;
    }

    public function build()
    {
        return $this->view('emails.generico')
            ->subject($this->subjectLine)
            ->with([
                'titulo' => $this->titulo,
                'mensaje' => $this->mensaje,
            ]);
    }
}
