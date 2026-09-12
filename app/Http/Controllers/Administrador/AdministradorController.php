<?php

namespace App\Http\Controllers\Administrador;

use App\Http\Controllers\Controller;

class AdministradorController extends Controller
{
    public function index()
    {
        return view('administrador.index');
    }
}
