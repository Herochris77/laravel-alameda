<?php

namespace App\Http\Controllers\Usuario;

use App\Http\Controllers\Controller;
use App\Models\Contacto;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ContactoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('usuario.contacto.index');
    }

    /**
     * Obtener contactos para usuarios (solo lectura)
     */
    public function obtenerContactos(Request $request)
    {
        if ($request->ajax()) {
            try {
                // Obtener todos los contactos activos (no eliminados)
                $contactos = Contacto::whereNull('deleted_at')
                    ->select(['id', 'nombre_contacto', 'numero_contacto', 'tipo_contacto', 'pagina_web'])
                    ->orderBy('nombre_contacto');

                // Si queremos filtrar por tipo (por ejemplo, solo recomendados o ambos)
                // Por ahora mostramos todos los contactos activos

                return datatables()->of($contactos)
                    ->addColumn('tipo_contacto', function ($contacto) {
                        // Devolver el tipo tal cual para que el JavaScript lo procese
                        return $contacto->tipo_contacto;
                    })
                    ->rawColumns(['tipo_contacto'])
                    ->make(true);
            } catch (Exception $e) {
                Log::error('Error al obtener contactos para usuarios: '.$e->getMessage());

                return response()->json([
                    'error' => 'Error al cargar los contactos',
                ], 500);
            }
        }

        return response()->json(['error' => 'No autorizado'], 401);
    }
}
