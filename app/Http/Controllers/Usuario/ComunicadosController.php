<?php

namespace App\Http\Controllers\Usuario;

use App\Http\Controllers\Controller;
use App\Models\Comunicado;
use Carbon\Carbon;

Carbon::setLocale('es');

class ComunicadosController extends Controller
{
    public function index()
    {
        $hoy = date('Y-m-d');
        $arr_comu = [];
        $comunicados = Comunicado::where('vencimiento', '>=', $hoy)->get();

        foreach ($comunicados as $comunicado) {
            $arr_comu[] = [
                'Titulo' => $comunicado->titulo,
                'Comunicado' => $comunicado->comunicado,
                'Vencimiento' => Carbon::parse($comunicado->vencimiento)->translatedFormat('j \\d\\e F \\d\\e Y'),
            ];
        }

        return view('usuario.comunicados.index', compact('arr_comu'));
    }
}
