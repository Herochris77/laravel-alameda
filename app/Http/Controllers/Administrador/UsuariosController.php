<?php

namespace App\Http\Controllers\Administrador;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\MailService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class UsuariosController extends Controller
{
    public function listaUsuarios()
    {
        return view('administrador.usuarios');
    }

    public function obtenerUsuarios(Request $request)
    {
        if ($request->ajax()) {
            $columnas = [
                'id',
                'nombre',
                'correo',
                'casa',
                'tipo',
                'rol',
                // Sin esta columna en el select, $c->cargo llega siempre null
                // y el modal reabría en "Sin cargo" aunque hubiera uno guardado.
                'cargo',
                'estado',
                'celular',
                'pago',
                'deleted_at',
            ];

            // Van condicionales porque pueden no existir si todavía no se
            // corre /migrar. Y van en el select porque, si no, llegan null
            // aunque estén en la base: es el mismo descuido que ya pasó con
            // 'cargo'.
            if (Schema::hasColumn('users', 'acepto_aviso_en')) {
                $columnas[] = 'acepto_aviso_en';
                $columnas[] = 'acepto_aviso_version';
            }

            $usuarios = User::withTrashed()->select($columnas);
    
            return datatables()->of($usuarios)
    
                ->editColumn('rol', function ($c) {
                    if ($c->rol == 'usuario') {
                        $base = '<div class="ui blue horizontal label">usuario</div>';
                    } elseif ($c->rol == 'administrador') {
                        $base = '<div class="ui green horizontal label">administrador</div>';
                    } elseif ($c->rol == 'super-administrador') {
                        $base = '<div class="ui purple horizontal label">super-administrador</div>';
                    } else {
                        $base = '<div class="ui grey horizontal label">'.$c->rol.'</div>';
                    }

                    // El cargo va junto al rol: es lo que define quién mueve el
                    // dinero dentro de la mesa directiva.
                    if ($c->cargo && isset(User::CARGOS[$c->cargo])) {
                        $color = $c->cargo === 'tesorero' ? 'teal' : 'grey';
                        $base .= ' <div class="ui '.$color.' horizontal label" title="Cargo en la mesa directiva">'
                            .'<i class="briefcase icon"></i> '.User::CARGOS[$c->cargo].'</div>';
                    }

                    return $base;
                })
    
                ->editColumn('estado', function ($c) {
                    if ($c->trashed()) {
                        return '<div class="ui grey horizontal label"><i class="trash icon"></i> Eliminado</div>';
                    } elseif ($c->estado == 1) {
                        return '<div class="ui green horizontal label">Activo</div>';
                    } elseif ($c->estado == 2) {
                        return '<div class="ui red horizontal label">Inactivo</div>';
                    } elseif ($c->estado == 3) {
                        return '<div class="ui yellow horizontal label">Pendiente dueño</div>';
                    } elseif ($c->estado == 0) {
                        return '<div class="ui grey horizontal label">Sin verificar</div>';
                    }

                    return '<div class="ui grey horizontal label">Sin estado</div>';
                })
    
                ->editColumn('pago', function ($c) {
                    if ($c->pago == 1) {
                        return '<div class="ui green horizontal label">Realiza pagos</div>';
                    }
    
                    return '<div class="ui red horizontal label">No realiza pagos</div>';
                })
    
                /*
                |--------------------------------------------------------------------------
                | Filtros exactos
                |--------------------------------------------------------------------------
                | Esto evita problemas con columnas que se renderizan como HTML.
                */
                ->filterColumn('rol', function ($query, $keyword) {
                    if ($keyword !== null && $keyword !== '') {
                        $query->where('rol', $keyword);
                    }
                })
    
                ->filterColumn('estado', function ($query, $keyword) {
                    if ($keyword !== null && $keyword !== '') {
                        $query->where('estado', $keyword);
                    }
                })
    
                ->filterColumn('pago', function ($query, $keyword) {
                    if ($keyword !== null && $keyword !== '') {
                        $query->where('pago', $keyword);
                    }
                })
    
                ->filterColumn('casa', function ($query, $keyword) {
                    if ($keyword !== null && $keyword !== '') {
                        $query->where('casa', 'like', "%{$keyword}%");
                    }
                })
    
                /*
                |--------------------------------------------------------------------------
                | Orden correcto para casa
                |--------------------------------------------------------------------------
                | Esto hace que casa ordene como número:
                | 1, 2, 3, 4, 5, 10, 11...
                | en vez de:
                | 1, 10, 11, 12, 2...
                */
                ->orderColumn('casa', function ($query, $order) {
                    $query->orderByRaw('CAST(casa AS UNSIGNED) '.$order);
                })
    
                ->addColumn('acciones', function ($c) {
                    if (auth()->user()->rol == 'administrador') {
                        if ($c->trashed()) {
                            return '<div class="ui grey horizontal label"><i class="trash icon"></i> Eliminado</div>';
                        } elseif ($c->estado == 1) {
                            return '<div class="ui center aligned">
                                        <button class="ui orange icon button btn-block" data-id="'.$c->id.'">
                                            <i class="ban icon"></i> Bloquear
                                        </button>
                                    </div>';
                        } elseif ($c->estado == 2) {
                            return '<div class="ui center aligned">
                                        <button class="ui green icon button btn-unblock" data-id="'.$c->id.'">
                                            <i class="check icon"></i> Desbloquear
                                        </button>
                                    </div>';
                        } elseif ($c->estado == 3) {
                            return '<div class="ui red horizontal label">Pendiente dueño</div>';
                        }
                    } elseif (auth()->user()->rol == 'super-administrador') {
                        if ($c->trashed()) {
                            return '<div class="ui grey horizontal label"><i class="trash icon"></i> Eliminado</div>';
                        }

                        $button = '<div class="ui center aligned">
                                    <div class="ui tiny buttons">';

                        $button .= '
                                <button class="ui red icon button btn-eliminar" data-id="'.$c->id.'">
                                    <i class="trash icon"></i> Eliminar
                                </button>';

                        if ($c->estado == 1) {
                            $button .= '
                                <button class="ui orange icon button btn-block" data-id="'.$c->id.'">
                                    <i class="ban icon"></i> Bloquear
                                </button>';
                        } elseif ($c->estado == 2) {
                            $button .= '
                                <button class="ui green icon button btn-unblock" data-id="'.$c->id.'">
                                    <i class="check icon"></i> Desbloquear
                                </button>';
                        } elseif ($c->estado == 3) {
                            $button .= '
                                <button class="ui yellow disabled button">
                                    <i class="clock outline icon"></i> Pendiente dueño
                                </button>';
                        } elseif ($c->estado == 0 && $c->tipo == 'dueño') {
                            $button .= '
                                <button class="ui teal icon button btn-enviar-verificacion" data-id="'.$c->id.'">
                                    <i class="mail icon"></i> Verificar
                                </button>';
                        }

                        // Inquilino que actualmente recibe los pagos: permitir
                        // regresar el pago al dueño de su casa.
                        if ($c->tipo == 'inquilino' && $c->pago == 1) {
                            $button .= '
                                <button class="ui teal icon button btn-regresar-pago" data-id="'.$c->id.'">
                                    <i class="undo icon"></i> Pago al dueño
                                </button>';
                        }

                        if ($c->rol == 'usuario') {
                            $button .= '
                                <button class="ui green icon button btn-admin" data-id="'.$c->id.'">
                                    <i class="user shield icon"></i> Administrador
                                </button>';
                        } elseif ($c->rol == 'administrador') {
                            $button .= '
                                <button class="ui blue icon button btn-user" data-id="'.$c->id.'">
                                    <i class="user icon"></i> Usuario
                                </button>';
                        }

                        // El cargo solo aplica a quien forma parte de la mesa.
                        if (in_array($c->rol, ['administrador', 'super-administrador'], true)) {
                            $button .= '
                                <button class="ui violet icon button btn-cargo"
                                    data-id="'.$c->id.'"
                                    data-nombre="'.e($c->nombre).'"
                                    data-cargo="'.e($c->cargo ?? '').'"
                                    title="Define si esta persona es quien maneja el dinero">
                                    <i class="briefcase icon"></i> Cargo
                                </button>';
                        }

                        $button .= '</div></div>';

                        return $button;
                    }

                    return '';
                })
    
                // Campos explícitos para el frontend: la vista arma sus propias
                // tarjetas y no reutiliza los botones que manda el backend, así
                // que necesita el cargo como dato, no dentro de una etiqueta.
                /*
                 * Aceptación del aviso de privacidad.
                 *
                 * Es el dato que respalda el consentimiento expreso: para una
                 * auditoría hay que poder decir quién aceptó, cuándo y qué
                 * versión del documento. Aquí se ve de un vistazo a quién le
                 * falta.
                 */
                ->addColumn('aviso', function ($c) {
                    if (! Schema::hasColumn('users', 'acepto_aviso_en')) {
                        return null;
                    }

                    if (! $c->acepto_aviso_en) {
                        return ['acepto' => false, 'fecha' => null, 'version' => null, 'vigente' => false];
                    }

                    $vigente = (string) $c->acepto_aviso_version
                        === (string) config('privacidad.version_aviso', '1.0');

                    return [
                        'acepto' => true,
                        'fecha' => \Carbon\Carbon::parse($c->acepto_aviso_en)->format('d/m/Y H:i'),
                        'version' => $c->acepto_aviso_version,
                        // Si el aviso cambió de versión, la aceptación anterior
                        // ya no ampara el documento vigente.
                        'vigente' => $vigente,
                    ];
                })
                ->addColumn('cargo_actual', fn ($c) => $c->cargo ?? '')
                ->addColumn('puede_tener_cargo', fn ($c) => in_array($c->rol, ['administrador', 'super-administrador'], true))
                ->rawColumns(['rol', 'acciones', 'estado', 'pago'])
                ->make(true);
        }
    }

    /**
     * ¿Quitar a este usuario dejaría al sistema sin ningún super-administrador?
     */
    private function esUltimoSuperAdmin(User $usuario): bool
    {
        if ($usuario->rol !== 'super-administrador') {
            return false;
        }

        return User::where('rol', 'super-administrador')
            ->where('estado', 1)
            ->where('id', '!=', $usuario->id)
            ->doesntExist();
    }

    public function bloquearUsuario($id)
    {
        try {
            $usuario = User::findOrFail($id);

            // Bloquearse a uno mismo cierra el propio acceso sin vuelta atrás
            // desde la plataforma.
            if ($usuario->id === auth()->user()->id) {
                return response()->json([
                    'header' => '🔒 Acción no permitida',
                    'success' => false,
                    'message' => 'No puedes bloquear tu propia cuenta.',
                ], 422);
            }

            if ($this->esUltimoSuperAdmin($usuario)) {
                return response()->json([
                    'header' => '🔒 Es el último super-administrador',
                    'success' => false,
                    'message' => 'Si lo bloqueas, nadie podrá volver a repartir roles. Nombra antes a otro super-administrador.',
                ], 422);
            }

            // Si es un inquilino que trae el pago, se lo regresamos al dueño.
            $dueno = $this->devolverPagoAlDueno($usuario);

            $usuario->update([
                'estado' => 2,
            ]);

            $mensaje = 'El usuario a partir de ahora no tendrá acceso';
            if ($dueno) {
                $mensaje .= '. El pago de la casa '.$usuario->casa.' se regresó al dueño '.$dueno->nombre.'.';
            }

            return response()->json(
                [
                    'header' => 'Usuario bloqueado ✅',
                    'success' => true,
                    'message' => $mensaje,
                ]
            );
        } catch (Exception $e) {
            return response()->json(
                [
                    'header' => 'Ups...',
                    'success' => false,
                    'message' => 'Problemas al bloquear el usuario: '.$e,
                ]
            );
        }
    }

    public function desbloquearUsuario($id)
    {
        try {
            $usuario = User::findOrFail($id);

            $usuario->update([
                'estado' => 1,
            ]);

            return response()->json(
                [
                    'header' => 'Usuario desbloqueado ✅',
                    'success' => true,
                    'message' => 'El usuario a partir de ahora tendrá acceso',
                ]
            );
        } catch (Exception $e) {
            return response()->json(
                [
                    'header' => 'Ups...',
                    'success' => false,
                    'message' => 'Problemas al desbloquear el usuario: '.$e,
                ]
            );
        }
    }

    public function eliminarUsuario($id)
    {
        try {
            $usuario = User::findOrFail($id);

            // Nadie se elimina a sí mismo: cerraría su propia sesión y, si es
            // el último super-administrador, dejaría la plataforma sin quien
            // pueda repartir roles.
            if ($usuario->id === auth()->user()->id) {
                return response()->json([
                    'header' => '🔒 Acción no permitida',
                    'success' => false,
                    'message' => 'No puedes eliminar tu propia cuenta.',
                ], 422);
            }

            if ($this->esUltimoSuperAdmin($usuario)) {
                return response()->json([
                    'header' => '🔒 Es el último super-administrador',
                    'success' => false,
                    'message' => 'Si lo eliminas, nadie podrá volver a repartir roles. Nombra antes a otro super-administrador.',
                ], 422);
            }

            // Si es un inquilino que trae el pago, se lo regresamos al dueño ANTES de borrar.
            $dueno = $this->devolverPagoAlDueno($usuario);

            $usuario->delete();

            $mensaje = 'El usuario fue eliminado correctamente. Los datos históricos se mantendrán.';
            if ($dueno) {
                $mensaje .= ' El pago de la casa '.$usuario->casa.' se regresó al dueño '.$dueno->nombre.'.';
            }

            return response()->json(
                [
                    'header' => 'Usuario Eliminado ✅',
                    'success' => true,
                    'message' => $mensaje,
                ]
            );
        } catch (Exception $e) {
            return response()->json(
                [
                    'header' => 'Ups...',
                    'success' => false,
                    'message' => 'Problemas al eliminar el usuario: '.$e,
                ]
            );
        }
    }

    /**
     * Asigna o quita el cargo dentro de la mesa directiva.
     *
     * El cargo es independiente del rol: define quién mueve el dinero. Solo
     * el Tesorero puede crear recibos y validar pagos; mientras nadie tenga
     * ese cargo, el módulo sigue abierto a toda la mesa (ver
     * User::puedeGestionarPagos()).
     */
    public function asignarCargo(Request $request, $id)
    {
        try {
            $request->validate([
                'cargo' => 'nullable|in:'.implode(',', array_keys(User::CARGOS)),
            ]);

            $usuario = User::findOrFail($id);

            if ($usuario->rol === 'usuario') {
                return response()->json([
                    'header' => '❌ No aplica',
                    'success' => false,
                    'message' => 'Los cargos son para integrantes de la mesa directiva. Primero haz administrador a este usuario.',
                ], 422);
            }

            $usuario->update([
                'cargo' => $request->filled('cargo') ? $request->cargo : null,
            ]);

            $nombreCargo = $usuario->cargo
                ? User::CARGOS[$usuario->cargo]
                : 'sin cargo';

            return response()->json([
                'header' => 'Cargo actualizado ✅',
                'success' => true,
                'message' => $usuario->nombre.' quedó como '.$nombreCargo.'.',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'header' => '⚠️ Datos inválidos',
                'success' => false,
                'message' => implode(' ', $e->validator->errors()->all()),
            ], 422);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error al asignar cargo: '.$e->getMessage());

            return response()->json([
                'header' => '❌ Error',
                'success' => false,
                'message' => 'No se pudo actualizar el cargo.',
            ], 500);
        }
    }

    public function administradorUsuario($id)
    {
        try {
            $usuario = User::findOrFail($id);

            $usuario->update([
                'rol' => 'administrador',
            ]);

            return response()->json(
                [
                    'header' => 'Usuario actualizado a administrador ✅',
                    'success' => true,
                    'message' => 'El usuario a partir de ahora tendrá acceso como administrador',
                ]
            );
        } catch (Exception $e) {
            return response()->json(
                [
                    'header' => 'Ups...',
                    'success' => false,
                    'message' => 'Problemas al actualizar el usuario: '.$e,
                ]
            );
        }
    }

    public function usuarioUsuario($id)
    {
        try {
            $usuario = User::findOrFail($id);

            /*
             * Nadie puede quitarse a sí mismo el acceso, ni dejar al sistema
             * sin ningún super-administrador.
             *
             * Solo un super-administrador puede repartir roles, así que si el
             * último se degrada no queda nadie capaz de revertirlo: habría que
             * entrar a la base de datos a mano para recuperar la plataforma.
             */
            if ($usuario->id === auth()->user()->id) {
                return response()->json([
                    'header' => '🔒 Acción no permitida',
                    'success' => false,
                    'message' => 'No puedes quitarte a ti mismo el acceso de administrador. Pídele a otro administrador que lo haga.',
                ], 422);
            }

            if ($usuario->rol === 'super-administrador') {
                $otros = User::where('rol', 'super-administrador')
                    ->where('estado', 1)
                    ->where('id', '!=', $usuario->id)
                    ->count();

                if ($otros === 0) {
                    return response()->json([
                        'header' => '🔒 Es el último super-administrador',
                        'success' => false,
                        'message' => 'Si le quitas el acceso, nadie podrá volver a repartir roles y habría que corregirlo desde la base de datos. Nombra antes a otro super-administrador.',
                    ], 422);
                }
            }

            $usuario->update([
                'rol' => 'usuario',
                // El cargo pertenece a la mesa directiva: al salir de ella no
                // tiene sentido conservarlo.
                'cargo' => null,
            ]);

            return response()->json(
                [
                    'header' => 'Usuario actualizado a usuario ✅',
                    'success' => true,
                    'message' => 'El usuario a partir de ahora tendrá acceso como usuario',
                ]
            );
        } catch (Exception $e) {
            return response()->json(
                [
                    'header' => 'Ups...',
                    'success' => false,
                    'message' => 'Problemas al actualizar el usuario: '.$e,
                ]
            );
        }
    }

    /**
     * Regresa la responsabilidad del pago al dueño de la casa del inquilino.
     * Útil cuando un inquilino dejó de vivir ahí pero su cuenta sigue activa:
     * así los próximos recibos se generan para el dueño y no para el inquilino.
     */
    /**
     * Si el usuario es un inquilino que trae el pago, regresa la responsabilidad
     * del pago al dueño de su casa. Devuelve el dueño si se hizo el cambio, o null.
     */
    private function devolverPagoAlDueno(User $inquilino): ?User
    {
        if ($inquilino->tipo !== 'inquilino' || $inquilino->pago != 1) {
            return null;
        }

        $dueno = User::where('casa', $inquilino->casa)->where('tipo', 'dueño')->first();

        if (! $dueno) {
            return null;
        }

        $dueno->update(['pago' => 1]);
        $inquilino->update(['pago' => 0]);

        return $dueno;
    }

    public function regresarPagoDueno($id)
    {
        try {
            $inquilino = User::findOrFail($id);

            if ($inquilino->tipo !== 'inquilino') {
                return response()->json([
                    'header' => '❌ No aplica',
                    'success' => false,
                    'message' => 'Esta acción solo aplica a inquilinos.',
                ]);
            }

            $dueno = User::where('casa', $inquilino->casa)->where('tipo', 'dueño')->first();

            if (! $dueno) {
                return response()->json([
                    'header' => '❌ Sin dueño',
                    'success' => false,
                    'message' => 'No se encontró un dueño registrado para la casa '.$inquilino->casa.'.',
                ]);
            }

            $dueno->update(['pago' => 1]);
            $inquilino->update(['pago' => 0]);

            return response()->json([
                'header' => '✅ Pago devuelto al dueño',
                'success' => true,
                'message' => 'A partir de ahora los recibos de la casa '.$inquilino->casa.' se generarán para '.$dueno->nombre.' (dueño), no para '.$inquilino->nombre.'.',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'header' => '❌ Error',
                'success' => false,
                'message' => 'No se pudo regresar el pago al dueño: '.$e->getMessage(),
            ]);
        }
    }

    public function enviarVerificacion($id)
    {
        try {
            $usuario = User::findOrFail($id);

            if ($usuario->estado != 0) {
                return response()->json([
                    'header' => '❌ No es necesario',
                    'success' => false,
                    'message' => 'Este usuario ya verificó su correo o no está pendiente de verificación.',
                ]);
            }

            if ($usuario->tipo != 'dueño') {
                return response()->json([
                    'header' => '❌ No aplica',
                    'success' => false,
                    'message' => 'El correo de verificación solo aplica para propietarios.',
                ]);
            }

            if (empty($usuario->token_auth)) {
                return response()->json([
                    'header' => '❌ Sin token',
                    'success' => false,
                    'message' => 'El usuario no tiene un token de verificación válido.',
                ]);
            }

            $subject = '📧 Recordatorio: Verifica tu correo electrónico';
            $titulo = '¡Hola '.$usuario->nombre.'!';
            $url = url('/verificación/'.$usuario->id.'/'.$usuario->token_auth);
            $mensaje = 'Te recordamos que tienes pendiente verificar tu correo electrónico para acceder a la plataforma de Alameda Condominio. <br><br>
                Para <strong>verificar</strong> tu cuenta, haz clic en el siguiente botón:
                <br><br>
                <a href="'.$url.'" style="display: inline-block; padding: 12px 28px; background: linear-gradient(135deg, #667eea, #764ba2); color: #fff; text-decoration: none; border-radius: 8px; font-weight: 600;">Verificar mi cuenta</a>
                <br><br>
                Si el botón no funciona, copia y pega este enlace en tu navegador:
                <br>
                <small>'.$url.'</small>
                <br><br><br>
                <strong>Si no fuiste tú quien creó esta cuenta, por favor ignora este correo.</strong>';

            MailService::enviar(
                $usuario->correo,
                subject: $subject,
                titulo: $titulo,
                mensaje: $mensaje,
                ignorarPreferencia: true,
                origen: 'verificacion'
            );

            return response()->json([
                'header' => '✅ Correo enviado',
                'success' => true,
                'message' => 'Recordatorio de verificación enviado al correo del usuario.',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'header' => '❌ Error',
                'success' => false,
                'message' => 'Error al enviar el correo: '.$e->getMessage(),
            ]);
        }
    }
}
