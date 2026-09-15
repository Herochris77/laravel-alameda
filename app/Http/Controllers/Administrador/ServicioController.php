<?php

namespace App\Http\Controllers\Administrador;

use App\Http\Controllers\Controller;
use App\Models\ServicioPago;
use App\Models\ServicioRecurrente;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Servicios y pagos recurrentes de la tesorería.
 *
 * Consultarlos queda abierto a toda la mesa directiva; darlos de alta,
 * editarlos y registrar sus pagos es de tesorería, igual que el resto de las
 * operaciones con dinero.
 */
class ServicioController extends Controller
{
    private const ORDEN_ESTATUS = [
        'vencido' => 0,
        'hoy' => 1,
        'por_vencer' => 2,
        'al_corriente' => 3,
        'sin_fecha' => 4,
        'inactivo' => 5,
    ];

    public function index()
    {
        return view('administrador.servicios', [
            'periodicidades' => ServicioRecurrente::PERIODICIDADES,
            'categorias' => ServicioRecurrente::CATEGORIAS,
            'formasPago' => ServicioRecurrente::FORMAS_PAGO,
            'resumen' => $this->resumen(),
            'puedeEditar' => Auth::user()->puedeGestionarPagos(),
        ]);
    }

    /**
     * Cifras de cabecera: la carga fija del mes y lo que urge.
     */
    private function resumen(): array
    {
        $servicios = ServicioRecurrente::activos()->get();

        $porVencer = $servicios->filter(
            fn ($s) => in_array($s->estatus(), ['hoy', 'por_vencer'], true)
        );

        $vencidos = $servicios->filter(fn ($s) => $s->estatus() === 'vencido');

        // Comprometido en los próximos 30 días: lo que hay que tener listo en
        // la cuenta, que no es lo mismo que la carga mensual prorrateada.
        $limite = Carbon::today()->addDays(30);

        $proximos30 = $servicios->filter(
            fn ($s) => $s->proximo_vencimiento && $s->proximo_vencimiento->lte($limite)
        );

        return [
            'total_activos' => $servicios->count(),
            'carga_mensual' => round($servicios->sum(fn ($s) => $s->costoMensualizado()), 2),
            'proximos_30' => round($proximos30->sum('monto_estimado'), 2),
            'por_vencer' => $porVencer->count(),
            'vencidos' => $vencidos->count(),
            'monto_vencido' => round($vencidos->sum('monto_estimado'), 2),
        ];
    }

    public function obtenerServicios(Request $request)
    {
        if (! $request->ajax()) {
            return response()->json([]);
        }

        $puedeEditar = Auth::user()->puedeGestionarPagos();

        $servicios = ServicioRecurrente::withCount('pagos')->get()
            ->map(function ($s) use ($puedeEditar) {
                $estatus = $s->estatus();

                return [
                    'id' => $s->id,
                    'nombre' => $s->nombre,
                    'proveedor' => $s->proveedor,
                    'referencia' => $s->referencia,
                    'categoria' => $s->categoria,
                    'categoria_texto' => $s->etiquetaCategoria(),
                    'monto_estimado' => $s->monto_estimado,
                    'periodicidad' => $s->periodicidad,
                    'periodicidad_texto' => $s->etiquetaPeriodicidad(),
                    'proximo_vencimiento' => optional($s->proximo_vencimiento)->format('Y-m-d'),
                    'vencimiento_texto' => $s->proximo_vencimiento
                        ? $s->proximo_vencimiento->translatedFormat('j M Y')
                        : 'Sin fecha',
                    'dias_restantes' => $s->diasRestantes(),
                    'dias_aviso' => $s->dias_aviso,
                    'forma_pago' => $s->forma_pago,
                    'contacto' => $s->contacto,
                    'notas' => $s->notas,
                    'activo' => $s->activo,
                    'estatus' => $estatus,
                    'orden' => self::ORDEN_ESTATUS[$estatus] ?? 9,
                    'pagos_count' => $s->pagos_count,
                    'promedio' => $s->promedioPagado(),
                    'puede_editar' => $puedeEditar,
                ];
            })
            // Primero lo que urge; dentro de cada grupo, lo que vence antes.
            ->sortBy([['orden', 'asc'], ['proximo_vencimiento', 'asc']])
            ->values();

        return response()->json(['data' => $servicios]);
    }

    public function crearServicio(Request $request)
    {
        try {
            $datos = $this->validar($request);

            $datos['created_by'] = Auth::user()->id;

            ServicioRecurrente::create($datos);

            return response()->json([
                'success' => true,
                'header' => 'Servicio registrado ✅',
                'message' => 'Quedó en el catálogo. Se te avisará antes de que venza.',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->errorValidacion($e);
        } catch (\Exception $e) {
            Log::error('Error al crear servicio recurrente: '.$e->getMessage());

            return $this->errorGenerico();
        }
    }

    public function actualizarServicio(Request $request, $id)
    {
        try {
            $servicio = ServicioRecurrente::findOrFail($id);

            $servicio->update($this->validar($request));

            return response()->json([
                'success' => true,
                'header' => 'Servicio actualizado ✅',
                'message' => 'Los cambios quedaron guardados.',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->errorValidacion($e);
        } catch (\Exception $e) {
            Log::error('Error al actualizar servicio recurrente: '.$e->getMessage());

            return $this->errorGenerico();
        }
    }

    /**
     * Registra el pago y recorre el vencimiento al siguiente periodo.
     *
     * Las dos cosas van juntas en una transacción: si el servicio no avanza,
     * el pago no debe quedar registrado, porque el tesorero vería el recibo
     * como pagado y la alerta seguiría encendida.
     */
    public function registrarPago(Request $request, $id)
    {
        try {
            $servicio = ServicioRecurrente::findOrFail($id);

            $request->validate([
                'monto_pagado' => 'required|numeric|min:0',
                'fecha_pago' => 'required|date|before_or_equal:today',
                'folio' => 'nullable|string|max:100',
                'notas' => 'nullable|string|max:500',
            ], [
                'fecha_pago.before_or_equal' => 'La fecha de pago no puede ser futura.',
            ]);

            $siguiente = $servicio->siguienteVencimiento();

            DB::transaction(function () use ($request, $servicio, $siguiente) {
                ServicioPago::create([
                    'servicio_id' => $servicio->id,
                    // El periodo se etiqueta por el vencimiento cubierto, no
                    // por el día en que se pagó: así un pago tardío se archiva
                    // en el mes al que corresponde.
                    'periodo' => optional($servicio->proximo_vencimiento)->format('Y-m')
                        ?? Carbon::parse($request->fecha_pago)->format('Y-m'),
                    'fecha_pago' => $request->fecha_pago,
                    'monto_pagado' => $request->monto_pagado,
                    'folio' => $request->folio,
                    'notas' => $request->notas,
                    'created_by' => Auth::user()->id,
                ]);

                $servicio->update([
                    'proximo_vencimiento' => $siguiente?->toDateString() ?? $servicio->proximo_vencimiento,
                    // Un pago único ya cumplió su propósito.
                    'activo' => $servicio->periodicidad === 'unico' ? false : $servicio->activo,
                    // Se limpia para que el siguiente periodo vuelva a avisar.
                    'ultimo_aviso' => null,
                ]);
            });

            $mensaje = $siguiente
                ? 'Quedó registrado. El siguiente vencimiento es el '
                    .$siguiente->translatedFormat('j \d\e F \d\e Y').'.'
                : 'Quedó registrado. Como es un pago único, el servicio se marcó como inactivo.';

            return response()->json([
                'success' => true,
                'header' => 'Pago registrado ✅',
                'message' => $mensaje,
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->errorValidacion($e);
        } catch (\Exception $e) {
            Log::error('Error al registrar pago de servicio: '.$e->getMessage());

            return $this->errorGenerico();
        }
    }

    public function historial($id)
    {
        $servicio = ServicioRecurrente::with('pagos.autor')->findOrFail($id);

        return response()->json([
            'success' => true,
            'servicio' => [
                'nombre' => $servicio->nombre,
                'referencia' => $servicio->referencia,
                'monto_estimado' => $servicio->monto_estimado,
                'promedio' => $servicio->promedioPagado(),
            ],
            'pagos' => $servicio->pagos->map(fn ($p) => [
                'periodo' => $p->periodo,
                'periodo_texto' => Carbon::parse($p->periodo.'-01')->translatedFormat('F Y'),
                'fecha_pago' => optional($p->fecha_pago)->translatedFormat('j M Y'),
                'monto_pagado' => $p->monto_pagado,
                'folio' => $p->folio,
                'notas' => $p->notas,
                'autor' => $p->autor->nombre ?? '—',
            ]),
        ]);
    }

    public function eliminarServicio($id)
    {
        try {
            // Borrado lógico: el historial de pagos sigue siendo parte del
            // expediente de la tesorería aunque el servicio ya no se use.
            ServicioRecurrente::findOrFail($id)->delete();

            return response()->json([
                'success' => true,
                'header' => 'Servicio eliminado ✅',
                'message' => 'Se quitó del catálogo. Su historial de pagos se conserva.',
            ]);
        } catch (\Exception $e) {
            Log::error('Error al eliminar servicio recurrente: '.$e->getMessage());

            return $this->errorGenerico();
        }
    }

    /**
     * Acta de entrega: el catálogo completo en CSV.
     *
     * Es el documento que responde "¿qué se paga aquí y con qué referencia?"
     * sin tener que entrar al sistema, que es justo lo que hace falta el día
     * que cambia la tesorería.
     */
    public function exportar()
    {
        $servicios = ServicioRecurrente::with('pagos')->get()
            ->sortBy([['activo', 'desc'], ['nombre', 'asc']]);

        $filas = [
            ['SERVICIOS Y PAGOS RECURRENTES — CONDOMINIO ALAMEDA'],
            ['Generado el', now()->translatedFormat('j \d\e F \d\e Y')],
            [],
            [
                'Servicio', 'Proveedor', 'Referencia de pago', 'Categoría',
                'Monto estimado', 'Periodicidad', 'Próximo vencimiento',
                'Forma de pago', 'Contacto', 'Último pago', 'Monto del último pago',
                'Promedio pagado', 'Pagos registrados', 'Estado', 'Notas',
            ],
        ];

        foreach ($servicios as $s) {
            $ultimo = $s->pagos->first();

            $filas[] = [
                $s->nombre,
                $s->proveedor ?: '—',
                $s->referencia ?: '—',
                $s->etiquetaCategoria(),
                number_format($s->monto_estimado, 2),
                $s->etiquetaPeriodicidad(),
                optional($s->proximo_vencimiento)->format('d/m/Y') ?: 'Sin fecha',
                ServicioRecurrente::FORMAS_PAGO[$s->forma_pago] ?? '—',
                $s->contacto ?: '—',
                $ultimo ? optional($ultimo->fecha_pago)->format('d/m/Y') : 'Sin registro',
                $ultimo ? number_format($ultimo->monto_pagado, 2) : '—',
                $s->promedioPagado() !== null ? number_format($s->promedioPagado(), 2) : '—',
                $s->pagos->count(),
                $s->activo ? 'Activo' : 'Inactivo',
                $s->notas ?: '',
            ];
        }

        $activos = $servicios->where('activo', true);

        $filas[] = [];
        $filas[] = ['RESUMEN'];
        $filas[] = ['Servicios activos', $activos->count()];
        $filas[] = ['Carga fija mensual estimada', number_format($activos->sum(fn ($s) => $s->costoMensualizado()), 2)];
        $filas[] = ['Equivalente anual', number_format($activos->sum(fn ($s) => $s->costoMensualizado()) * 12, 2)];

        return $this->csv($filas, 'servicios-recurrentes-'.now()->format('Y-m-d').'.csv');
    }

    /**
     * CSV con BOM para que Excel respete los acentos al abrirlo de doble clic.
     */
    private function csv(array $filas, string $nombre)
    {
        $salida = fopen('php://temp', 'r+');

        fwrite($salida, "\xEF\xBB\xBF");

        foreach ($filas as $fila) {
            fputcsv($salida, $fila);
        }

        rewind($salida);
        $contenido = stream_get_contents($salida);
        fclose($salida);

        return response($contenido, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="'.$nombre.'"',
        ]);
    }

    private function validar(Request $request): array
    {
        return $request->validate([
            'nombre' => 'required|string|max:120',
            'proveedor' => 'nullable|string|max:120',
            'referencia' => 'nullable|string|max:120',
            'categoria' => 'required|in:'.implode(',', array_keys(ServicioRecurrente::CATEGORIAS)),
            'monto_estimado' => 'required|numeric|min:0',
            'periodicidad' => 'required|in:'.implode(',', array_keys(ServicioRecurrente::PERIODICIDADES)),
            'proximo_vencimiento' => 'required|date',
            'dias_aviso' => 'required|integer|min:1|max:30',
            'forma_pago' => 'required|in:'.implode(',', array_keys(ServicioRecurrente::FORMAS_PAGO)),
            'contacto' => 'nullable|string|max:150',
            'notas' => 'nullable|string|max:1000',
            'activo' => 'nullable|boolean',
        ], [
            'nombre.required' => 'Ponle nombre al servicio.',
            'proximo_vencimiento.required' => 'Indica cuándo vence el próximo pago.',
            'dias_aviso.min' => 'El aviso debe ser de al menos 1 día antes.',
        ]);
    }

    private function errorValidacion(\Illuminate\Validation\ValidationException $e)
    {
        return response()->json([
            'success' => false,
            'header' => '⚠️ Revisa los datos',
            'message' => implode(' ', $e->validator->errors()->all()),
        ], 422);
    }

    private function errorGenerico()
    {
        return response()->json([
            'success' => false,
            'header' => '❌ Ocurrió un error',
            'message' => 'Por favor inténtalo más tarde y repórtalo al desarrollador de la aplicación Christian Martínez',
        ], 500);
    }
}
