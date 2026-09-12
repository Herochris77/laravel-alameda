<?php

namespace App\Http\Controllers\Usuario;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class VecinoController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->filled('filtro')) {
            $filtro = $request->filtro;

            if ($filtro === 'activos') {
                $query->where('estado', 1);
            } elseif ($filtro === 'inactivos') {
                $query->where('estado', 0);
            } elseif ($filtro === 'inquilinos') {
                $query->where('tipo', 'inquilino');
            } elseif ($filtro === 'dueños') {
                $query->where('tipo', 'dueño');
            } elseif ($filtro === 'casa') {
                $query->whereNotNull('casa')->where('casa', '!=', '')->orderBy('casa', 'asc');
            }
        }

        $vecinos = $query->orderBy('nombre', 'asc')->get();

        return view('usuario.vecino.index', compact('vecinos'));
    }
}
