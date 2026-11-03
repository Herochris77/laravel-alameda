<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class AppLayout extends Component
{
    /**
     * Get the view / contents that represents the component.
     */
    /** Título de la pestaña. Si no se indica, se deduce de la ruta (App\Support\Navegacion::tituloActual). */
    public function __construct(public ?string $titulo = null) {}

    public function render(): View
    {
        return view('layouts.app');
    }
}
