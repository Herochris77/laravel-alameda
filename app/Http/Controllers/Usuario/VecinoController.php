<?php

namespace App\Http\Controllers\Usuario;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class VecinoController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        /*
         * Solo aparece quien no se haya salido del directorio.
         *
         * El vecino lo controla desde su perfil. Nace en visible para que
         * nadie desaparezca sin haberlo pedido, pero salirse es de un clic y
         * surte efecto de inmediato.
         *
         * Quien pidió que se retiraran sus datos tampoco aparece: la
         * desvinculación apaga esta bandera.
         */
        if (Schema::hasColumn('users', 'visible_directorio')) {
            $query->where('visible_directorio', 1);
        }

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
