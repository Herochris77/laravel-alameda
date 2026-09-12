<?php

namespace App\Http\Controllers;
use App\Models\User;

class InicioController extends Controller
{
    public function index()
    {
        $mesaDirectiva = User::select(['id', 'nombre', 'correo', 'casa', 'foto', 'celular'])->where('rol', 'administrador')->get();
       
        return view('inicio.index', compact('mesaDirectiva'));
    }
}
