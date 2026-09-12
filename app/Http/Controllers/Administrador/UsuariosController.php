<?php

namespace App\Http\Controllers\Administrador;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\MailService;
use Exception;
use Illuminate\Http\Request;

class UsuariosController extends Controller
{
    public function listaUsuarios()
    {
        return view('administrador.usuarios');
    }

    public function obtenerUsuarios(Request $request)
    {
        if ($request->ajax()) {
            $usuarios = User::withTrashed()->select([
                'id',
                'nombre',
                'correo',
                'casa',
                'tipo',
                'rol',
                'estado',
                'celular',
                'pago',
                'deleted_at'
            ]);
    
            return datatables()->of($usuarios)
    
                ->editColumn('rol', function ($c) {
                    if ($c->rol == 'usuario') {
                        return '<div class="ui blue horizontal label">usuario</div>';
                    } elseif ($c->rol == 'administrador') {
                        return '<div class="ui green horizontal label">administrador</div>';
                    } elseif ($c->rol == 'super-administrador') {
                        return '<div class="ui purple horizontal label">super-administrador</div>';
                    }
    
                    return '<div class="ui grey horizontal label">'.$c->rol.'</div>';
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

                        $button .= '</div></div>';

                        return $button;
                    }

                    return '';
                })
    
                ->rawColumns(['rol', 'acciones', 'estado', 'pago'])
                ->make(true);
        }
    }

    public function bloquearUsuario($id)
    {
        try {
            $usuario = User::findOrFail($id);

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

            $usuario->update([
                'rol' => 'usuario',
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
