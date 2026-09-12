<?php

namespace App\Http\Controllers\Usuario;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebPushController extends Controller
{
    /**
     * Registra (o actualiza) la suscripción push del dispositivo actual.
     * Un mismo usuario puede tener varias suscripciones (una por dispositivo/navegador).
     */
    public function suscribir(Request $request): JsonResponse
    {
        $datos = $request->validate([
            'endpoint' => 'required|string',
            'keys.p256dh' => 'required|string',
            'keys.auth' => 'required|string',
            'contentEncoding' => 'nullable|string|in:aesgcm,aes128gcm',
        ]);

        try {
            $request->user()->updatePushSubscription(
                $datos['endpoint'],
                $datos['keys']['p256dh'],
                $datos['keys']['auth'],
                $datos['contentEncoding'] ?? 'aes128gcm'
            );

            return response()->json([
                'success' => true,
                'message' => 'Notificaciones activadas en este dispositivo.',
            ]);
        } catch (\Throwable $e) {
            Log::error('Error al registrar suscripción push: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'No se pudo activar la notificación.',
            ], 500);
        }
    }

    /**
     * Envía una notificación de prueba al usuario actual.
     * Llega a TODOS sus dispositivos suscritos (y también queda en la campana).
     */
    public function probar(Request $request): JsonResponse
    {
        $user = $request->user();
        $suscripciones = $user->pushSubscriptions()->count();

        try {
            $user->notify(new \App\Notifications\NotificacionGenerica(
                '🔔 Notificación de prueba',
                'Si ves esto en tus dispositivos, ¡las notificaciones funcionan correctamente!',
                'info',
                'usuario/perfil',
                'usuario/perfil',
                '<i class="bell icon"></i>'
            ));

            return response()->json([
                'success' => true,
                'suscripciones' => $suscripciones,
                'message' => $suscripciones > 0
                    ? "Notificación de prueba enviada a {$suscripciones} dispositivo(s). Revísalos."
                    : 'No tienes dispositivos con notificaciones activas. Activa el switch y vuelve a probar.',
            ]);
        } catch (\Throwable $e) {
            Log::error('Error al enviar notificación de prueba: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'No se pudo enviar la notificación de prueba.',
            ], 500);
        }
    }

    /**
     * Elimina la suscripción push del dispositivo actual.
     */
    public function desuscribir(Request $request): JsonResponse
    {
        $datos = $request->validate([
            'endpoint' => 'required|string',
        ]);

        try {
            $request->user()->deletePushSubscription($datos['endpoint']);

            return response()->json([
                'success' => true,
                'message' => 'Notificaciones desactivadas en este dispositivo.',
            ]);
        } catch (\Throwable $e) {
            Log::error('Error al eliminar suscripción push: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'No se pudo desactivar la notificación.',
            ], 500);
        }
    }
}
