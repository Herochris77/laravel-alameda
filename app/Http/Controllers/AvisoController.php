<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

/**
 * Registro de la aceptación del aviso de privacidad.
 *
 * Publicar el aviso no basta: para los datos patrimoniales —los comprobantes
 * de pago que suben los vecinos— la ley pide consentimiento expreso. Esto es
 * lo que convierte "lo publiqué" en "puedo demostrar quién lo aceptó y
 * cuándo".
 */
class AvisoController extends Controller
{
    public function aceptar(Request $request)
    {
        try {
            if (! Schema::hasColumn('users', 'acepto_aviso_en')) {
                return response()->json([
                    'success' => false,
                    'header' => '⚠️ Falta migrar',
                    'message' => 'Corre /migrar para habilitar el registro de aceptación.',
                ], 409);
            }

            $usuario = Auth::user();
            $version = (string) config('privacidad.version_aviso', '1.0');

            $usuario->update([
                'acepto_aviso_en' => now(),
                'acepto_aviso_version' => $version,
            ]);

            // Queda también en el log del servidor: si algún día se cuestiona
            // el registro de la base, hay una segunda fuente con su fecha.
            Log::info('Aviso de privacidad aceptado', [
                'user_id' => $usuario->id,
                'casa' => $usuario->casa,
                'version' => $version,
                'ip' => $request->ip(),
            ]);

            return response()->json([
                'success' => true,
                'header' => 'Gracias ✅',
                'message' => 'Tu aceptación quedó registrada.',
            ]);
        } catch (\Exception $e) {
            Log::error('Error al registrar la aceptación del aviso: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'header' => '❌ Error',
                'message' => 'No se pudo registrar tu aceptación. Inténtalo de nuevo.',
            ], 500);
        }
    }
}
