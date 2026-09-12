<?php

namespace App\Http\Controllers\Administrador;

use App\Http\Controllers\Controller;
use App\Models\SolicitudPermiso;

class SolicitudController extends Controller
{
    public function index()
    {
        return view('administrador.solicitudes.index');
    }

    public function listar()
    {
        $solicitudes = SolicitudPermiso::with(['inquilino', 'dueno'])
            ->withTrashed()
            ->orderBy('created_at', 'desc')
            ->get();

        return datatables()->of($solicitudes)
            ->editColumn('inquilino', function ($s) {
                return $s->inquilino->nombre.' <small class="text-muted">(casa '.$s->inquilino->casa.')</small>';
            })
            ->editColumn('dueno', function ($s) {
                return $s->dueno->nombre;
            })
            ->editColumn('estado', function ($s) {
                if ($s->trashed()) {
                    return '<div class="ui gray horizontal label"><i class="trash icon"></i> Eliminada</div>';
                } elseif ($s->estado === 'pendiente') {
                    return '<div class="ui yellow horizontal label">Pendiente</div>';
                } elseif ($s->estado === 'aprobado') {
                    return '<div class="ui green horizontal label">Aprobado</div>';
                }
                return '<div class="ui red horizontal label">Rechazado</div>';
            })
            ->editColumn('fecha_respuesta', function ($s) {
                return $s->fecha_respuesta ? $s->fecha_respuesta->format('d/m/Y H:i') : '—';
            })
            ->editColumn('created_at', function ($s) {
                return $s->created_at->format('d/m/Y H:i');
            })
            ->addColumn('acciones', function ($s) {
                return '<button class="ui blue icon button btn-ver-solicitud" data-id="'.$s->id.'">
                            <i class="eye icon"></i> Ver
                        </button>';
            })
            ->rawColumns(['inquilino', 'estado', 'acciones'])
            ->make(true);
    }

    public function ver($id)
    {
        $solicitud = SolicitudPermiso::with(['inquilino', 'dueno'])->withTrashed()->findOrFail($id);

        return response()->json([
            'inquilino' => $solicitud->inquilino->nombre,
            'casa' => $solicitud->inquilino->casa,
            'dueno' => $solicitud->dueno->nombre,
            'titulo' => $solicitud->titulo,
            'mensaje' => nl2br(e($solicitud->mensaje)),
            'estado' => $solicitud->estado,
            'trashed' => $solicitud->trashed(),
            'deleted_at' => $solicitud->deleted_at ? $solicitud->deleted_at->format('d/m/Y H:i') : null,
            'created_at' => $solicitud->created_at->format('d/m/Y H:i'),
            'fecha_respuesta' => $solicitud->fecha_respuesta ? $solicitud->fecha_respuesta->format('d/m/Y H:i') : '—',
        ]);
    }
}
