<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use Illuminate\Support\Facades\Route;

/*
 * Rutas de autenticación.
 *
 * Quedan solo las que el sistema usa de verdad: registro, inicio y cierre de
 * sesión. La recuperación de contraseña y la verificación del correo tienen su
 * propio flujo en routes/web.php (App\Http\Controllers\invitado\PasswordController).
 *
 * Se retiraron las rutas de la plantilla original (forgot-password,
 * reset-password, verify-email, confirm-password, cambio de contraseña): nada
 * las enlazaba, duplicaban funciones que ya existen y apuntaban a columnas
 * (`email`, `password`) que esta base de datos no tiene, así que solo servían
 * para devolver errores y ampliar la superficie expuesta.
 *
 * Los límites de intentos son POR IP. El de inicio de sesión por correo+IP ya
 * vive en LoginRequest; este lo complementa contra quien prueba muchos
 * correos distintos desde un mismo lugar.
 */
Route::middleware('guest')->group(function () {
    Route::get('register', [RegisteredUserController::class, 'create'])
        ->name('register');

    Route::post('register', [RegisteredUserController::class, 'store'])
        ->middleware('throttle:6,1');

    Route::get('login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');

    Route::post('login', [AuthenticatedSessionController::class, 'store'])
        ->middleware('throttle:20,1');
});

Route::middleware('auth')->group(function () {
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});
