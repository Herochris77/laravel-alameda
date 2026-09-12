<?php

namespace App\Http\Controllers\Usuario;

use App\Http\Controllers\Controller;
use App\Models\Documento;
use Illuminate\Http\Request;

class DocumentosController extends Controller
{
    public function index()
    {
        return view('usuario.documentos.index');
    }

    public function obtenerDocumentos(Request $request)
    {
        if ($request->ajax()) {
            $documentos = Documento::with('user')
                ->where(function ($query) {
                    $query->where('tipo', 'general')
                        ->orWhereNotNull('cantidad');
                })
                ->orderBy('created_at', 'desc');

            return datatables()->of($documentos)
                ->addColumn('fecha', function ($d) {
                    return $d->created_at->format('d/m/Y');
                })
                ->addColumn('categoria', function ($d) {
                    $categorias = [
                        'luz' => ['icon' => 'bolt', 'color' => '#f59e0b', 'label' => 'Luz'],
                        'agua' => ['icon' => 'tint', 'color' => '#3b82f6', 'label' => 'Agua'],
                        'gas' => ['icon' => 'fire', 'color' => '#ef4444', 'label' => 'Gas'],
                        'mantenimiento' => ['icon' => 'wrench', 'color' => '#10b981', 'label' => 'Mantenimiento'],
                        'seguridad' => ['icon' => 'shield alternate', 'color' => '#8b5cf6', 'label' => 'Seguridad'],
                        'limpieza' => ['icon' => 'broom', 'color' => '#06b6d4', 'label' => 'Limpieza'],
                        'jardineria' => ['icon' => 'leaf', 'color' => '#22c55e', 'label' => 'Jardinería'],
                        'otro' => ['icon' => 'file', 'color' => '#64748b', 'label' => 'Otro'],
                    ];
                    $cat = $categorias[$d->categoria_gasto] ?? $categorias['otro'];
                    if ($d->tipo === 'general') {
                        return '<span class="ui purple basic label"><i class="file alternate icon"></i> General</span>';
                    }

                    return '<span class="ui basic label" style="border-left: 3px solid '.$cat['color'].';"><i class="'.$cat['icon'].' icon"></i> '.$cat['label'].'</span>';
                })
                ->addColumn('cantidad', function ($d) {
                    return $d->cantidad ? '$'.number_format($d->cantidad, 2) : '—';
                })
                ->addColumn('documento', function ($d) {
                    if ($d->doc_path) {
                        $url = asset('storage/'.$d->doc_path);

                        return '<a href="'.$url.'" target="_blank" class="ui mini teal button">
                            <i class="eye icon"></i> Ver
                        </a>';
                    }

                    return '—';
                })
                ->rawColumns(['categoria', 'documento'])
                ->make(true);
        }
    }
}
