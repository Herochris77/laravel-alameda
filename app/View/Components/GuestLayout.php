<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class GuestLayout extends Component
{
    public string $title;

    public string $subtitle;

    public function __construct(string $title = 'Bienvenido', string $subtitle = '')
    {
        $this->title = $title;
        $this->subtitle = $subtitle;
    }

    public function render(): View
    {
        return view('layouts.guest');
    }
}
