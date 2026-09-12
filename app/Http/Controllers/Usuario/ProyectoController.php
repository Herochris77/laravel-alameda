<?php

namespace App\Http\Controllers\Usuario;

use App\Http\Controllers\Controller;
use App\Models\Proyecto;

class ProyectoController extends Controller
{
    public function index()
    {
        $proyectos = Proyecto::orderByDesc('created_at')->get();
        $estados = Proyecto::estados();

        return view('usuario.proyectos.index', compact('proyectos', 'estados'));
    }

    public function ver($id)
    {
        $proyecto = Proyecto::with('avances')->findOrFail($id);
        $estados = Proyecto::estados();

        return view('usuario.proyectos.ver', compact('proyecto', 'estados'));
    }
}
