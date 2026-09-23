<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Desvinculación de los datos personales de un vecino.
 *
 * Se ejecuta cuando alguien decide NO aceptar el aviso de privacidad y pide
 * que se retiren sus datos.
 *
 * NO se borra la cuenta. Se anonimiza: el nombre pasa a "Casa 19" y el correo
 * a casa19@<dominio propio>. Así el historial de pagos sigue cuadrando y la
 * mesa sabe de qué vivienda es cada movimiento, que es lo que respalda las
 * cuentas ante la asamblea.
 *
 * Lo que SÍ se elimina es todo lo que no sostiene una cifra: la fotografía de
 * perfil, las mascotas, los vehículos, los comprobantes de transferencia, las
 * notificaciones y las suscripciones del celular.
 *
 * El comprobante de pago es el borrado más delicado: desaparece la imagen
 * pero se conserva el renglón con su monto, su fecha y su estado. La
 * contabilidad no se mueve; lo que se pierde es poder volver a mirar la
 * captura de esa transferencia.
 */
class DesvinculacionService
{
    /**
     * @return array Reporte de lo que se retiró, para mostrarlo y registrarlo.
     */
    public function ejecutar(User $usuario): array
    {
        $casa = trim((string) $usuario->casa) !== '' ? $usuario->casa : $usuario->id;

        $reporte = [
            'casa' => $casa,
            'nombre_anterior' => $usuario->nombre,
            'archivos' => 0,
            'mascotas' => 0,
            'vehiculos' => 0,
            'comprobantes' => 0,
            'notificaciones' => 0,
            'otros' => 0,
        ];

        DB::transaction(function () use ($usuario, $casa, &$reporte) {

            $reporte['archivos'] += $this->borrarFotoPerfil($usuario);
            $reporte['mascotas'] = $this->borrarConFoto('mascotas', 'user_id', $usuario->id, 'foto', 'mascotas', $reporte);
            $reporte['vehiculos'] = $this->borrarConFoto('vehiculos', 'user_id', $usuario->id, 'foto', 'vehiculos', $reporte);
            $reporte['comprobantes'] = $this->borrarComprobantes($usuario, $reporte);
            $reporte['otros'] += $this->borrarImagenesDeSanciones($usuario, $reporte);
            $reporte['notificaciones'] = $this->borrarNotificaciones($usuario);
            $reporte['otros'] += $this->borrarRastroTecnico($usuario);

            $this->anonimizar($usuario, $casa);
        });

        Log::info('Datos personales desvinculados', [
            'user_id' => $usuario->id,
            'casa' => $casa,
            'reporte' => $reporte,
        ]);

        return $reporte;
    }

    /**
     * Anonimiza la cuenta y le corta el acceso.
     *
     * El correo usa el dominio propio del condominio y NUNCA uno ajeno: un
     * "casa19@gmail.com" puede existir y ser de un desconocido, y cualquier
     * mensaje del sistema le llegaría a esa persona.
     */
    private function anonimizar(User $usuario, $casa): void
    {
        $dominio = config('privacidad.dominio_desvinculado', 'alameda-condominio.com.mx');

        $datos = [
            'nombre' => 'Casa '.$casa,
            'correo' => 'casa'.preg_replace('/[^A-Za-z0-9]/', '', (string) $casa).'@'.$dominio,
            // celular es NOT NULL en la base: se vacía con cadena, no con null.
            'celular' => '',
            'foto' => null,
            // Sin correos y fuera del directorio: es justo lo que pidió.
            'emails' => 0,
            // Sin acceso: no aceptó las condiciones de uso de la plataforma.
            'estado' => 0,
            // La contraseña se vuelve irrecuperable y se corta el "recordarme":
            // aunque alguien reactivara la cuenta, no se entra con la anterior.
            'pass' => bcrypt(Str::random(40)),
            'remember_token' => null,
            'token_auth' => null,
        ];

        if (Schema::hasColumn('users', 'visible_directorio')) {
            $datos['visible_directorio'] = 0;
        }

        if (Schema::hasColumn('users', 'desvinculado_en')) {
            $datos['desvinculado_en'] = now();
        }

        // Si dos casas se desvincularan con el mismo número, el correo
        // chocaría contra el índice único. Se le agrega el id para que nunca
        // falle por eso.
        if (User::withTrashed()->where('correo', $datos['correo'])->where('id', '!=', $usuario->id)->exists()) {
            $datos['correo'] = 'casa'.preg_replace('/[^A-Za-z0-9]/', '', (string) $casa)
                .'-'.$usuario->id.'@'.$dominio;
        }

        $usuario->forceFill($datos)->save();
    }

    private function borrarFotoPerfil(User $usuario): int
    {
        return $this->borrarArchivo('perfil/'.$usuario->foto, (bool) $usuario->foto);
    }

    /**
     * Borra los renglones de una tabla y sus imágenes asociadas.
     */
    private function borrarConFoto(string $tabla, string $columnaUsuario, $userId, string $columnaFoto, string $carpeta, array &$reporte): int
    {
        if (! Schema::hasTable($tabla)) {
            return 0;
        }

        $filas = DB::table($tabla)->where($columnaUsuario, $userId)->get();

        foreach ($filas as $f) {
            $reporte['archivos'] += $this->borrarArchivo($carpeta.'/'.($f->{$columnaFoto} ?? ''), (bool) ($f->{$columnaFoto} ?? null));
        }

        DB::table($tabla)->where($columnaUsuario, $userId)->delete();

        return $filas->count();
    }

    /**
     * Quita las capturas de transferencia CONSERVANDO el renglón del pago.
     *
     * Es el punto donde se separa el dato personal del dato contable: el monto
     * y la fecha se quedan porque sostienen el reporte mensual; la imagen del
     * movimiento bancario se va porque es información personal del vecino.
     */
    private function borrarComprobantes(User $usuario, array &$reporte): int
    {
        $conArchivo = DB::table('detallepagos')
            ->where('user_id', $usuario->id)
            ->whereNotNull('path_pago')
            ->where('path_pago', '!=', '')
            ->get(['id', 'path_pago']);

        foreach ($conArchivo as $d) {
            $reporte['archivos'] += $this->borrarArchivo($d->path_pago, true);
        }

        DB::table('detallepagos')->where('user_id', $usuario->id)->update(['path_pago' => null]);

        return $conArchivo->count();
    }

    /**
     * Imágenes de sanciones: la evidencia y el comprobante de su pago.
     *
     * El renglón se conserva porque una multa cobrada es ingreso del
     * condominio; las fotografías no sostienen ninguna cifra.
     */
    private function borrarImagenesDeSanciones(User $usuario, array &$reporte): int
    {
        if (! Schema::hasTable('sanciones')) {
            return 0;
        }

        $filas = DB::table('sanciones')->where('user_id', $usuario->id)->get();
        $tocadas = 0;

        foreach ($filas as $s) {
            foreach (['foto_path', 'pago_path'] as $campo) {
                if (! empty($s->{$campo})) {
                    $reporte['archivos'] += $this->borrarArchivo($s->{$campo}, true);
                    $tocadas++;
                }
            }
        }

        // Las dos columnas son NOT NULL en la base, así que se vacían con
        // cadena vacía. Un null aquí revienta la transacción completa.
        DB::table('sanciones')->where('user_id', $usuario->id)
            ->update(['foto_path' => '', 'pago_path' => '']);

        return $tocadas;
    }

    private function borrarNotificaciones(User $usuario): int
    {
        $n = DB::table('notifications')
            ->where('notifiable_id', $usuario->id)
            ->where('notifiable_type', User::class)
            ->count();

        DB::table('notifications')
            ->where('notifiable_id', $usuario->id)
            ->where('notifiable_type', User::class)
            ->delete();

        return $n;
    }

    /**
     * Rastro que no es contable: suscripciones del celular, reservaciones,
     * solicitudes entre inquilino y propietario, y su nombre en el cajón de
     * estacionamiento.
     */
    private function borrarRastroTecnico(User $usuario): int
    {
        $n = 0;

        foreach ([
            'push_subscriptions' => 'subscribable_id',
            'reservas' => 'user_id',
        ] as $tabla => $columna) {
            if (Schema::hasTable($tabla) && Schema::hasColumn($tabla, $columna)) {
                $n += DB::table($tabla)->where($columna, $usuario->id)->delete();
            }
        }

        if (Schema::hasTable('solicitudes_permisos')) {
            $n += DB::table('solicitudes_permisos')
                ->where('inquilino_id', $usuario->id)
                ->orWhere('dueno_id', $usuario->id)
                ->delete();
        }

        // El cajón se libera, pero NO se toca su columna "nombre": ese es el
        // nombre del propio cajón ("Cajón 3"), no el del vecino, y es NOT NULL.
        // El estado válido es "disponible"; el enum no acepta otra cosa.
        if (Schema::hasTable('estacionamientos') && Schema::hasColumn('estacionamientos', 'user_id')) {
            $n += DB::table('estacionamientos')->where('user_id', $usuario->id)
                ->update([
                    'user_id' => null,
                    'estado' => 'disponible',
                    'fecha_inicio' => null,
                    'fecha_fin' => null,
                    'hora_inicio' => null,
                    'hora_fin' => null,
                ]);
        }

        return $n;
    }

    private function borrarArchivo(?string $ruta, bool $hay): int
    {
        if (! $hay || ! $ruta) {
            return 0;
        }

        try {
            if (Storage::disk('public')->exists($ruta)) {
                Storage::disk('public')->delete($ruta);

                return 1;
            }
        } catch (\Throwable $e) {
            Log::warning('No se pudo borrar el archivo '.$ruta.': '.$e->getMessage());
        }

        return 0;
    }
}
