<?php

namespace App\Http\Controllers;

use App\Models\Comunicado;
use App\Models\Detallepago;
use App\Models\Encuesta;
use App\Models\User;
use App\Services\SaldoService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class InicioController extends Controller
{
    public function index(SaldoService $saldos)
    {
        $usuario = Auth::user();

        $mesaDirectiva = User::select(['id', 'nombre', 'correo', 'casa', 'foto', 'celular'])
            ->where('rol', 'administrador')
            ->get();

        $casa = $this->estadoDeLaCasa($usuario, $saldos);

        // Comunicados vigentes de la mesa directiva.
        $comunicados = Comunicado::whereDate('vencimiento', '>=', Carbon::today())
            ->orderByDesc('updated_at')
            ->limit(3)
            ->get(['id', 'titulo', 'updated_at']);

        // Avisos: lo mismo que ve en la campana, para que el contador y la tarjeta
        // nunca se contradigan.
        $sinLeer = $usuario->unreadNotifications()->count();
        $avisos = $usuario->unreadNotifications()->limit(3)->get();

        // Solo las encuestas abiertas que esta persona todavía no contesta.
        $encuestaPendiente = Encuesta::with('respuestas')
            ->paraUsuario($usuario->tipo)
            ->orderByDesc('created_at')
            ->get()
            ->filter(fn ($e) => $e->estaActiva() && $e->respuestas->where('user_id', $usuario->id)->isEmpty())
            ->first();

        return view('inicio.index', compact('mesaDirectiva', 'casa', 'comunicados', 'avisos', 'sinLeer', 'encuestaPendiente'));
    }

    /**
     * Lo que el vecino quiere saber al abrir el sistema: ¿estoy al corriente?
     *
     * Solo lee: no cambia nada. Los criterios de "vencido" y "pendiente" son
     * los mismos de la pantalla de pagos, para que las dos nunca se contradigan.
     *
     * @return array{
     *   situacion:string, titulo:string, detalle:string, aplica:bool,
     *   proximo:?array, pagadoAnio:float, saldo:float, rechazados:int
     * }
     */
    private function estadoDeLaCasa(User $usuario, SaldoService $saldos): array
    {
        $recibos = Detallepago::with('pago')
            ->where('user_id', $usuario->id)
            ->get();

        $vencidos = $recibos->filter(fn ($r) => $r->estado === 'pendiente'
            && is_null($r->path_pago)
            && $r->pago?->vencimiento
            && Carbon::parse($r->pago->vencimiento)->endOfDay()->isPast());

        $porPagar = $recibos->filter(fn ($r) => $r->estado === 'pendiente'
            && is_null($r->path_pago)
            && $r->pago?->vencimiento
            && ! Carbon::parse($r->pago->vencimiento)->endOfDay()->isPast())
            ->sortBy(fn ($r) => $r->pago->vencimiento);

        $enRevision = $recibos->filter(fn ($r) => $r->estado === 'pendiente' && ! is_null($r->path_pago));
        $rechazados = $recibos->where('estado', 'rechazado')->count();

        $pagadoAnio = (float) $recibos
            ->where('estado', 'pagado')
            ->filter(fn ($r) => ($r->fechaEfectivaPago() ?? $r->updated_at)?->year === now()->year)
            ->sum(fn ($r) => (float) ($r->cantidad_pago ?: 0));

        $proximo = null;
        $situacion = 'alcorriente';

        if ($vencidos->isNotEmpty()) {
            $situacion = 'atrasada';
            $r = $vencidos->sortBy(fn ($x) => $x->pago->vencimiento)->first();
            $proximo = $this->describir($r, true);
        } elseif ($rechazados > 0) {
            $situacion = 'rechazado';
        } elseif ($porPagar->isNotEmpty()) {
            $situacion = 'porpagar';
            $proximo = $this->describir($porPagar->first(), false);
        } elseif ($enRevision->isNotEmpty()) {
            $situacion = 'revision';
        }

        // Quien no paga la cuota (el inquilino cuyo propietario la cubre) no tiene
        // adeudos propios: se le dice con claridad en vez de mostrar ceros.
        $aplica = (int) $usuario->pago === 1 || $recibos->isNotEmpty();

        [$titulo, $detalle] = match (true) {
            ! $aplica => ['Tu casa está a cargo del propietario', 'Los pagos de mantenimiento los gestiona tu propietario.'],
            $situacion === 'atrasada' => [
                $vencidos->count() === 1 ? 'Tienes 1 pago vencido' : "Tienes {$vencidos->count()} pagos vencidos",
                'Ponte al corriente subiendo tu comprobante.',
            ],
            $situacion === 'rechazado' => [$rechazados === 1 ? 'Tu comprobante fue rechazado' : 'Tienes comprobantes rechazados', 'Revisa el motivo y vuelve a subirlo.'],
            $situacion === 'porpagar' => ['Tienes un pago por realizar', 'Sube tu comprobante antes de la fecha de vencimiento.'],
            $situacion === 'revision' => ['Tu comprobante está en revisión', 'La tesorería lo validará en cuanto pueda.'],
            default => ['Estás al corriente', 'No tienes pagos pendientes. Gracias por cuidar tu comunidad.'],
        };

        return [
            'situacion' => $aplica ? $situacion : 'sinpago',
            'titulo' => $titulo,
            'detalle' => $detalle,
            'aplica' => $aplica,
            'proximo' => $proximo,
            'pagadoAnio' => $pagadoAnio,
            'saldo' => $aplica ? $saldos->saldo($usuario->id) : 0.0,
            'rechazados' => $rechazados,
        ];
    }

    private function describir(Detallepago $recibo, bool $vencido): array
    {
        $vence = Carbon::parse($recibo->pago->vencimiento)->startOfDay();
        $dias = (int) now()->startOfDay()->diffInDays($vence, false);

        return [
            'concepto' => $recibo->pago->concepto,
            'monto' => (float) $recibo->pago->cantidad,
            'vence' => $vence->locale('es')->translatedFormat('j \d\e F'),
            'dias' => $dias,
            'vencido' => $vencido,
        ];
    }
}
