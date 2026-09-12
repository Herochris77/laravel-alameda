<?php

use App\Http\Controllers\Administrador\AdministradorController;
use App\Http\Controllers\Administrador\ComunicadoController;
use App\Http\Controllers\Administrador\CorreoController;
use App\Http\Controllers\Administrador\ContactoController;
use App\Http\Controllers\Administrador\DocumentoController;
use App\Http\Controllers\Administrador\AsambleaController as AdminAsambleaController;
use App\Http\Controllers\Administrador\DashboardController;
use App\Http\Controllers\Administrador\EncuestaController as AdminEncuestaController;
use App\Http\Controllers\Administrador\PagoController;
use App\Http\Controllers\Administrador\SancionController;
use App\Http\Controllers\Administrador\SolicitudController as AdminSolicitudController;
use App\Http\Controllers\Administrador\UsuariosController;
use App\Http\Controllers\Administrador\VehiculoController;
use App\Http\Controllers\CronjobController;
use App\Http\Controllers\InicioController;
use App\Http\Controllers\SolicitudController;
use App\Http\Controllers\Usuario\EncuestaController;
use App\Http\Controllers\Usuario\AsambleaController;
use App\Http\Controllers\Usuario\EstacionamientoController;
use App\Http\Controllers\invitado\PasswordController;
use App\Http\Controllers\Usuario\PerfilController;
use App\Http\Controllers\Usuario\WebPushController;
use App\Models\Pago;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;

Route::get('/limpiar-cache/{token}', function ($token) {
    if ($token !== 'admin123') {
        abort(403, 'No autorizado');
    }

    Artisan::call('config:clear');
    Artisan::call('cache:clear');
    Artisan::call('config:cache');

    return response()->json([
        'success' => true,
        'message' => 'Cache y configuración limpiadas correctamente.',
        'commands' => [
            'config:clear' => 'OK',
            'cache:clear' => 'OK',
            'config:cache' => 'OK',
        ],
    ]);
});

/*
|--------------------------------------------------------------------------
| Aplicar SOLO los cambios de BD nuevos (cPanel básico, sin terminal)
|--------------------------------------------------------------------------
| Visita en el navegador:  https://tudominio.com/migrar/admin123
|
| IMPORTANTE: NO ejecuta "php artisan migrate" (que intentaría recrear todas
| las tablas porque la tabla `migrations` no tiene historial al haberse
| importado la BD por SQL). En su lugar aplica de forma idempotente y SEGURA
| solo lo nuevo, sin tocar ni borrar ninguna tabla ni dato existente:
|   1) Crea la tabla `push_subscriptions` si no existe.
|   2) Amplía el enum `ubicacion` de `estacionamientos` para aceptar 'escalera'.
| Cambia el token 'admin123' por algo privado si lo deseas.
*/
Route::get('/migrar/{token}', function ($token) {
    if ($token !== 'admin123') {
        abort(403, 'No autorizado');
    }

    $schema = \Illuminate\Support\Facades\Schema::class;
    $db = \Illuminate\Support\Facades\DB::class;
    $pasos = [];

    // 1) Tabla push_subscriptions (solo si no existe) -----------------------
    $tabla = config('webpush.table_name', 'push_subscriptions');

    if (! $schema::hasTable($tabla)) {
        $schema::create($tabla, function (\Illuminate\Database\Schema\Blueprint $table) {
            $table->bigIncrements('id');
            $table->morphs('subscribable', 'push_subscriptions_subscribable_morph_idx');
            $table->string('endpoint', 500)->unique();
            $table->string('public_key')->nullable();
            $table->string('auth_token')->nullable();
            $table->string('content_encoding')->nullable();
            $table->timestamps();
        });
        $pasos[] = "✅ Tabla '{$tabla}' creada.";
    } else {
        $pasos[] = "ℹ️  Tabla '{$tabla}' ya existía. No se tocó.";
    }

    // 2) Ampliar enum ubicacion en estacionamientos (solo si hace falta) -----
    if ($schema::hasTable('estacionamientos') && $db::getDriverName() === 'mysql') {
        $col = $db::selectOne("SHOW COLUMNS FROM estacionamientos LIKE 'ubicacion'");

        if ($col && stripos($col->Type, 'escalera') === false) {
            // ALTER que solo AMPLÍA los valores permitidos; los datos existentes
            // (entrada/central) siguen siendo válidos, no se pierde nada.
            $db::statement("ALTER TABLE estacionamientos MODIFY COLUMN ubicacion ENUM('entrada','central','escalera') NOT NULL DEFAULT 'entrada'");
            $pasos[] = "✅ Enum 'ubicacion' ampliado para aceptar 'escalera'.";
        } else {
            $pasos[] = "ℹ️  El enum 'ubicacion' ya aceptaba 'escalera'. No se tocó.";
        }
    } else {
        $pasos[] = "ℹ️  Tabla 'estacionamientos' no encontrada (se omite el ajuste de enum).";
    }

    // 3) Encuestas de ordenamiento -----------------------------------------
    if ($schema::hasTable('encuestas') && ! $schema::hasColumn('encuestas', 'tipo')) {
        $schema::table('encuestas', function (\Illuminate\Database\Schema\Blueprint $table) {
            $table->string('tipo', 20)->default('opcion')->after('tipo_usuario');
        });
        $pasos[] = "✅ Columna 'tipo' agregada a 'encuestas' (opcion | ordenamiento).";
    } else {
        $pasos[] = "ℹ️  Columna 'encuestas.tipo' ya existía. No se tocó.";
    }

    if ($schema::hasTable('encuesta_respuestas') && ! $schema::hasColumn('encuesta_respuestas', 'posicion')) {
        $schema::table('encuesta_respuestas', function (\Illuminate\Database\Schema\Blueprint $table) {
            $table->unsignedInteger('posicion')->nullable()->after('user_id');
        });
        $pasos[] = "✅ Columna 'posicion' agregada a 'encuesta_respuestas'.";
    } else {
        $pasos[] = "ℹ️  Columna 'encuesta_respuestas.posicion' ya existía. No se tocó.";
    }

    // 4) Módulo de asambleas -----------------------------------------------
    if (! $schema::hasTable('asambleas')) {
        $schema::create('asambleas', function (\Illuminate\Database\Schema\Blueprint $t) {
            $t->id();
            $t->string('titulo');
            $t->text('descripcion')->nullable();
            $t->date('fecha')->nullable();
            $t->time('hora')->nullable();
            $t->string('lugar')->nullable();
            $t->unsignedInteger('quorum_pct')->default(50);
            $t->boolean('control_asistencia')->default(true);
            $t->string('estado')->default('convocada');
            $t->unsignedBigInteger('created_by');
            $t->timestamps();
        });
        $pasos[] = "✅ Tabla 'asambleas' creada.";
    } else {
        $pasos[] = "ℹ️  Tabla 'asambleas' ya existía.";
    }

    if (! $schema::hasTable('asamblea_puntos')) {
        $schema::create('asamblea_puntos', function (\Illuminate\Database\Schema\Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('asamblea_id');
            $t->unsignedInteger('orden')->default(0);
            $t->string('titulo');
            $t->text('descripcion')->nullable();
            $t->string('tipo')->default('si_no');
            $t->string('estado')->default('pendiente');
            $t->timestamps();
            $t->index('asamblea_id');
        });
        $pasos[] = "✅ Tabla 'asamblea_puntos' creada.";
    } else {
        $pasos[] = "ℹ️  Tabla 'asamblea_puntos' ya existía.";
    }

    if (! $schema::hasTable('asamblea_opciones')) {
        $schema::create('asamblea_opciones', function (\Illuminate\Database\Schema\Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('punto_id');
            $t->string('opcion');
            $t->unsignedInteger('orden')->default(0);
            $t->timestamps();
            $t->index('punto_id');
        });
        $pasos[] = "✅ Tabla 'asamblea_opciones' creada.";
    } else {
        $pasos[] = "ℹ️  Tabla 'asamblea_opciones' ya existía.";
    }

    if (! $schema::hasTable('asamblea_poderes')) {
        $schema::create('asamblea_poderes', function (\Illuminate\Database\Schema\Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('asamblea_id');
            $t->string('casa');
            $t->unsignedBigInteger('otorgante_id');
            $t->unsignedBigInteger('representante_id');
            $t->string('tipo');
            $t->string('estado')->default('activo');
            $t->timestamps();
            $t->index(['asamblea_id', 'casa']);
        });
        $pasos[] = "✅ Tabla 'asamblea_poderes' creada.";
    } else {
        $pasos[] = "ℹ️  Tabla 'asamblea_poderes' ya existía.";
    }

    if (! $schema::hasTable('asamblea_votos')) {
        $schema::create('asamblea_votos', function (\Illuminate\Database\Schema\Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('asamblea_id');
            $t->unsignedBigInteger('punto_id');
            $t->string('casa');
            $t->string('valor')->nullable();
            $t->unsignedBigInteger('opcion_id')->nullable();
            $t->unsignedInteger('posicion')->nullable();
            $t->unsignedBigInteger('emitido_por');
            $t->timestamps();
            $t->index(['punto_id', 'casa']);
        });
        $pasos[] = "✅ Tabla 'asamblea_votos' creada.";
    } else {
        $pasos[] = "ℹ️  Tabla 'asamblea_votos' ya existía.";
    }

    if (! $schema::hasTable('asamblea_asistencias')) {
        $schema::create('asamblea_asistencias', function (\Illuminate\Database\Schema\Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('asamblea_id');
            $t->string('casa');
            $t->unsignedBigInteger('user_id');
            $t->boolean('confirmada')->default(true);
            $t->unsignedBigInteger('registrada_por')->nullable();
            $t->timestamps();
            $t->unique(['asamblea_id', 'casa']);
        });
        $pasos[] = "✅ Tabla 'asamblea_asistencias' creada.";
    } else {
        $pasos[] = "ℹ️  Tabla 'asamblea_asistencias' ya existía.";
    }

    // 5) Notas del comité por casa (expediente) ----------------------------
    if (! $schema::hasTable('expediente_notas')) {
        $schema::create('expediente_notas', function (\Illuminate\Database\Schema\Blueprint $t) {
            $t->id();
            $t->string('casa');
            $t->text('nota');
            $t->string('autor')->nullable();
            $t->timestamps();
            $t->index('casa');
        });
        $pasos[] = "✅ Tabla 'expediente_notas' creada.";
    } else {
        $pasos[] = "ℹ️  Tabla 'expediente_notas' ya existía.";
    }

    // 6) Proyectos y sus avances -------------------------------------------
    if (! $schema::hasTable('proyectos')) {
        $schema::create('proyectos', function (\Illuminate\Database\Schema\Blueprint $t) {
            $t->id();
            $t->string('nombre');
            $t->text('descripcion')->nullable();
            $t->double('costo')->default(0);
            $t->string('estado')->default('por_iniciar');
            $t->unsignedInteger('avance')->default(0);
            $t->unsignedBigInteger('created_by')->nullable();
            $t->timestamps();
        });
        $pasos[] = "✅ Tabla 'proyectos' creada.";
    } else {
        $pasos[] = "ℹ️  Tabla 'proyectos' ya existía.";
    }

    if (! $schema::hasTable('proyecto_avances')) {
        $schema::create('proyecto_avances', function (\Illuminate\Database\Schema\Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('proyecto_id');
            $t->unsignedInteger('porcentaje')->default(0);
            $t->text('comentario')->nullable();
            $t->string('autor')->nullable();
            $t->timestamps();
            $t->index('proyecto_id');
        });
        $pasos[] = "✅ Tabla 'proyecto_avances' creada.";
    } else {
        $pasos[] = "ℹ️  Tabla 'proyecto_avances' ya existía.";
    }

    // Evidencia (foto) opcional en los avances
    if ($schema::hasTable('proyecto_avances') && ! $schema::hasColumn('proyecto_avances', 'foto')) {
        $schema::table('proyecto_avances', function (\Illuminate\Database\Schema\Blueprint $t) {
            $t->string('foto')->nullable()->after('comentario');
        });
        $pasos[] = "✅ Columna 'foto' agregada a 'proyecto_avances'.";
    } else {
        $pasos[] = "ℹ️  Columna 'proyecto_avances.foto' ya existía.";
    }

    // Costo opcional (nullable) en proyectos
    if ($schema::hasTable('proyectos') && $db::getDriverName() === 'mysql') {
        $colCosto = $db::selectOne("SHOW COLUMNS FROM proyectos LIKE 'costo'");
        if ($colCosto && strtoupper($colCosto->Null) === 'NO') {
            $db::statement('ALTER TABLE proyectos MODIFY costo DOUBLE NULL');
            $pasos[] = "✅ Columna 'costo' de 'proyectos' ahora es opcional.";
        } else {
            $pasos[] = "ℹ️  Columna 'proyectos.costo' ya era opcional.";
        }
    }

    return response('<pre>'.e(implode("\n", $pasos))."\n\nListo. No se modificó ni eliminó ningún dato existente.</pre>");
});

Route::get('/admin/logs', function () {

        /*
         * Archivos permitidos.
         * Ajusta laravel.log si tu archivo realmente se llama laravel.logs.
         */
        $allowedLogs = [
            'laravel.log' => storage_path('logs/laravel.log'),
            'cron-url.log' => storage_path('logs/cron-url.log'),
        ];

        $selected = request('file', 'laravel.log');
        $search = request('search');
        $level = request('level');

        if (!array_key_exists($selected, $allowedLogs)) {
            abort(404);
        }

        $path = $allowedLogs[$selected];

        $content = File::exists($path) ? File::get($path) : '';

        /*
         * Parser especial para cron-url.log
         * Tu archivo tiene JSONs pegados:
         * {...}{...}{...}
         */
        if ($selected === 'cron-url.log') {

            $jsonLines = preg_split('/(?<=\})(?=\{)/', trim($content));

            $entries = collect($jsonLines)
                ->filter(fn ($line) => trim($line) !== '')
                ->map(function ($line) {
                    $data = json_decode($line, true);

                    if (!is_array($data)) {
                        return [
                            'date' => '',
                            'environment' => 'cron',
                            'level' => 'ERROR',
                            'message' => $line,
                            'full' => $line,

                            'pagos_enviados' => '',
                            'pagos_errores' => '',
                            'reservaciones_enviados' => '',
                            'reservaciones_errores' => '',
                        ];
                    }

                    return [
                        'date' => $data['ejecutado_en'] ?? '',
                        'environment' => 'cron',
                        'level' => !empty($data['success']) ? 'INFO' : 'ERROR',
                        'message' => $data['message'] ?? 'Sin mensaje',
                        'full' => json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),

                        'pagos_enviados' => $data['resultados']['pagos']['enviados'] ?? 0,
                        'pagos_errores' => $data['resultados']['pagos']['errores'] ?? 0,

                        'reservaciones_enviados' => $data['resultados']['reservaciones']['enviados'] ?? 0,
                        'reservaciones_errores' => $data['resultados']['reservaciones']['errores'] ?? 0,
                    ];
                })
                ->reverse()
                ->values();

        } else {

            /*
             * Parser para logs normales de Laravel:
             * [2026-05-06 10:30:00] production.ERROR: mensaje...
             */
            preg_match_all('/^\[(.*?)\]\s+(\w+)\.(\w+):\s+(.*?)(?=^\[|\z)/ms', $content, $matches, PREG_SET_ORDER);

            $entries = collect($matches)
                ->map(function ($match) {
                    return [
                        'date' => $match[1] ?? '',
                        'environment' => $match[2] ?? '',
                        'level' => $match[3] ?? '',
                        'message' => trim($match[4] ?? ''),
                        'full' => trim($match[4] ?? ''),

                        'pagos_enviados' => '',
                        'pagos_errores' => '',
                        'reservaciones_enviados' => '',
                        'reservaciones_errores' => '',
                    ];
                })
                ->reverse()
                ->values();

            /*
             * Fallback por si el archivo no tiene formato Laravel.
             */
            if ($entries->count() === 0 && trim($content) !== '') {
                $entries = collect(explode(PHP_EOL, $content))
                    ->filter(fn ($line) => trim($line) !== '')
                    ->map(function ($line) {
                        return [
                            'date' => '',
                            'environment' => 'custom',
                            'level' => 'INFO',
                            'message' => trim($line),
                            'full' => trim($line),

                            'pagos_enviados' => '',
                            'pagos_errores' => '',
                            'reservaciones_enviados' => '',
                            'reservaciones_errores' => '',
                        ];
                    })
                    ->reverse()
                    ->values();
            }
        }

        /*
         * Filtro por texto.
         */
        if ($search) {
            $entries = $entries->filter(function ($entry) use ($search) {
                return stripos((string) $entry['date'], $search) !== false
                    || stripos((string) $entry['environment'], $search) !== false
                    || stripos((string) $entry['level'], $search) !== false
                    || stripos((string) $entry['message'], $search) !== false
                    || stripos((string) $entry['full'], $search) !== false
                    || stripos((string) ($entry['pagos_enviados'] ?? ''), $search) !== false
                    || stripos((string) ($entry['pagos_errores'] ?? ''), $search) !== false
                    || stripos((string) ($entry['reservaciones_enviados'] ?? ''), $search) !== false
                    || stripos((string) ($entry['reservaciones_errores'] ?? ''), $search) !== false;
            })->values();
        }

        /*
         * Filtro por nivel.
         */
        if ($level) {
            $entries = $entries->filter(function ($entry) use ($level) {
                return strtoupper((string) $entry['level']) === strtoupper((string) $level);
            })->values();
        }

        return view('admin.logs.viewer', [
            'logs' => $allowedLogs,
            'selected' => $selected,
            'entries' => $entries,
            'search' => $search,
            'level' => $level,
        ]);
    })->name('admin.logs');


    Route::delete('/admin/logs/clear', function () {

        $allowedLogs = [
            'laravel.log' => storage_path('logs/laravel.log'),
            'cron-url.log' => storage_path('logs/cron-url.log'),
        ];

        $selected = request('file', 'laravel.log');

        if (!array_key_exists($selected, $allowedLogs)) {
            abort(404);
        }

        File::put($allowedLogs[$selected], '');

        return redirect()
            ->route('admin.logs', ['file' => $selected])
            ->with('success', 'Log eliminado correctamente.');
    })->name('admin.logs.clear');

Route::get('/', function () {
    return Auth::check()
        ? redirect()->route('inicio')
        : view('auth.login');
});

/*Route::get('/storage-link', function () {
    $target = '/home1/alamedac/laravel-alameda/storage/app/public';
    $link = '/home1/alamedac/public_html/storage';

    if (!file_exists($target)) {
        return 'No existe el origen: ' . $target;
    }

    if (file_exists($link) || is_link($link)) {
        return 'Ya existe el enlace o carpeta: ' . $link;
    }

    try {
        symlink($target, $link);

        return 'Enlace simbólico creado correctamente.<br>'
            . 'Origen: ' . $target . '<br>'
            . 'Destino: ' . $link;
    } catch (\Throwable $e) {
        return 'Error al crear symlink: ' . $e->getMessage()
            . '<br>Origen: ' . $target
            . '<br>Destino: ' . $link;
    }
});

Route::get('/storage-link-test', function () {
    $target = storage_path('app/public');
    $link = public_path('storage');

    return [
        'target' => $target,
        'target_exists' => file_exists($target),
        'link' => $link,
        'link_exists' => file_exists($link),
        'public_path' => public_path(),
        'storage_path' => storage_path(),
    ];
});

Route::get('/storage-check', function () {
    $file = '/home1/alamedac/laravel-alameda/storage/app/public/documentos/EgA3qBtX1m3PUViBPixp2k2N7npXLTdRAYzQburI.png';
    $link = '/home1/alamedac/public_html/storage';

    return [
        'file_exists' => file_exists($file),
        'file_readable' => is_readable($file),
        'file_permissions' => file_exists($file) ? substr(sprintf('%o', fileperms($file)), -4) : null,

        'documentos_exists' => is_dir(dirname($file)),
        'documentos_readable' => is_readable(dirname($file)),
        'documentos_permissions' => is_dir(dirname($file)) ? substr(sprintf('%o', fileperms(dirname($file))), -4) : null,

        'storage_link_exists' => file_exists($link),
        'storage_is_link' => is_link($link),
        'storage_link_target' => is_link($link) ? readlink($link) : null,
    ];
});*/

Route::post('/notificaciones/{id}/marcar-leida', function ($id) {
    $notification = auth()->user()
        ->notifications()
        ->where('id', $id)
        ->firstOrFail();

    $notification->markAsRead();

    return response()->json([
        'success' => true,
        'mensaje' => 'Notificación marcada como leída.'
    ]);
})->middleware('auth')->name('notificaciones.marcar-leida');


Route::get('/cronjob/notificaciones-diarias', [CronjobController::class, 'enviarNotificacionesDiarias'])
    ->name('cronjob.notificaciones')
    ->withoutMiddleware(['web', 'auth', 'verified']);

// Verificación de cuenta por correo electrónico
Route::get('/verificación/{id}/{token}', [PasswordController::class, 'verify'])->name('verify');

// Reseterar contraseña
Route::prefix('restablecer')->name('restart.')->group(function () {
    Route::controller(PasswordController::class)->prefix('contraseña')->name('password.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/restablecer', 'restart')->name('restart');
        Route::get('/actualizar/{id}/{token}', 'update')->name('update');
        Route::post('actualizar-contraseña', 'updatePassword')->name('updatePassword');
    });
});

// Notificaciones
Route::post('/notifications/mark-all-read', function () {
    Auth::user()->unreadNotifications->markAsRead();

    return response()->json(['success' => true]);
})->name('notifications.markAllRead')->middleware('auth');

Route::delete('/notificaciones/eliminar-todas', function () {
    $user = Auth::user();
    $user->notifications()->delete();

    return response()->json(['mensaje' => 'Todas las notificaciones han sido eliminadas.']);
})->middleware('auth');

Route::middleware('auth')->group(function () {
    Route::get('/inicio', [InicioController::class, 'index'])->name('inicio')->middleware('rol:administrador,super-administrador,usuario');

    // Administrador
    Route::prefix('administrador')->name('admin.')->middleware('rol:administrador,super-administrador')->group(function () {
        // Inicio
        Route::controller(AdministradorController::class)->prefix('inicio')->name('inicio.')->group(function () {
            Route::get('/', 'index')->name('index');
        });
        // Comunicado
        Route::controller(ComunicadoController::class)->prefix('comunicado')->name('comunicado.')->group(function () {
            Route::get('/nuevo-comunicado', 'nuevoComunicado')->name('nuevoComunicado');
            Route::post('/crear-comunicado', 'crearComunicado')->name('crearComunicado');
            Route::get('/obtener-comunicados', 'obtenerComunicados')->name('obtenerComunicados');
            Route::delete('/eliminar-comunicados/{id}', 'eliminarComunicados')->name('eliminarComunicados');
        });
        // Contactos
        Route::controller(ContactoController::class)->prefix('contacto')->name('contacto.')->group(function () {
            Route::get('/nuevo-contacto', 'nuevoContacto')->name('nuevoContacto');
            Route::post('/crear-contacto', 'crearContacto')->name('crearContacto');
            Route::get('/obtener-contacto', 'obtenerContactos')->name('obtenerContactos');
            Route::delete('/eliminar-contacto/{id}', 'eliminarContacto')->name('eliminarContacto');
            Route::post('/actualizar-contacto/{id}', 'actualizarContacto')->name('actualizarContacto');
        });
        // Sanciones
        Route::controller(SancionController::class)->prefix('sancion')->name('sancion.')->group(function () {
            Route::get('/nueva-sancion', 'nuevaSancion')->name('nuevaSancion');
            Route::post('/crear-sancion', 'crearSancion')->name('crearSancion');
            Route::get('/obtener-sanciones', 'obtenerSanciones')->name('obtenerSanciones');
            Route::delete('/eliminar-sancion/{id}', 'eliminarSancion')->name('eliminarSancion');
            Route::post('/actualizar-sancion/{id}', 'actualizarSancion')->name('actualizarSancion');
        });
        // Pagos
        Route::controller(PagoController::class)->prefix('pago')->name('pago.')->group(function () {
            Route::get('/nuevo-pago', 'nuevoPago')->name('nuevoPago');
            Route::post('/crear-pago', 'crearPago')->name('crearPago');
            Route::get('/obtener-pago', 'obtenerPagos')->name('obtenerPagos');
            Route::delete('/eliminar-pago/{id}', 'eliminarPago')->name('eliminarPago');
            Route::delete('/eliminar-mainpago/{id}', 'eliminarMainPago')->name('eliminarMainPago');
            Route::post('/actualizar-pago/{id}', 'actualizarPago')->name('actualizarPago');
            Route::get('/detalle-pagos/{id}', 'DetallePagos')->name('DetallePagos');
            Route::get('/obtenerdetalle-pagos', 'obtenerDetallepagos')->name('obtenerDetallepagos');
            Route::post('/agregar-usuario', 'agregarUsuario')->name('agregarUsuario');
        });
        // Documentos
        Route::controller(DocumentoController::class)->prefix('documento')->name('documento.')->group(function () {
            Route::get('/nuevo-documento', 'nuevoDocumento')->name('nuevoDocumento');
            Route::post('/crear-documento', 'crearDocumento')->name('crearDocumento');
            Route::get('/obtener-documentos', 'obtenerDocumentos')->name('obtenerDocumentos');
            Route::delete('/eliminar-documento/{id}', 'eliminarDocumento')->name('eliminarDocumento');
            Route::post('/actualizar-documento/{id}', 'actualizarDocumento')->name('actualizarDocumento');
            Route::get('/resumen-gastos', 'resumenGastos')->name('resumenGastos');
            Route::get('/conceptos', function () {
                $conceptos = Pago::select('id', 'concepto')->orderBy('id', 'desc')->limit(20)->get();

                return response()->json($conceptos);
            })->name('listado');
        });
        // Usuarios
        Route::controller(UsuariosController::class)->prefix('usuarios')->name('usuarios.')->group(function () {
            Route::get('/', 'listaUsuarios')->name('listaUsuarios');
            Route::get('/obtener-usuarios', 'obtenerUsuarios')->name('obtenerUsuarios');
            Route::get('/bloquear-usuario/{id}', 'bloquearUsuario')->name('bloquearUsuario');
            Route::get('/desbloquear-usuario/{id}', 'desbloquearUsuario')->name('desbloquearUsuario');
            Route::get('/eliminar-usuario/{id}', 'eliminarUsuario')->name('eliminarUsuario');
            Route::get('/administrador-usuario/{id}', 'administradorUsuario')->name('administradorUsuario');
            Route::get('/usuario-usuario/{id}', 'usuarioUsuario')->name('usuarioUsuario');
            Route::post('/enviar-verificacion/{id}', 'enviarVerificacion')->name('enviarVerificacion');
            Route::post('/regresar-pago/{id}', 'regresarPagoDueno')->name('regresarPagoDueno');
        });
        // Vehículos
        Route::controller(VehiculoController::class)->prefix('vehiculo')->name('vehiculo.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/obtener', 'obtenerVehiculos')->name('obtener');
            Route::delete('/eliminar/{id}', 'eliminarVehiculo')->name('eliminar');
        });
        // Panel / dashboard del comité
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard/datos', [DashboardController::class, 'datos'])->name('dashboard.datos');

        // Expediente por casa
        Route::controller(App\Http\Controllers\Administrador\ExpedienteController::class)->prefix('expediente')->name('expediente.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/casa/{casa}', 'ficha')->name('ficha');
            Route::post('/casa/{casa}/nota', 'guardarNota')->name('nota');
        });

        // Proyectos
        Route::controller(App\Http\Controllers\Administrador\ProyectoController::class)->prefix('proyectos')->name('proyectos.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/guardar', 'guardar')->name('guardar');
            Route::get('/{id}', 'ver')->name('ver');
            Route::post('/{id}/actualizar', 'actualizar')->name('actualizar');
            Route::post('/{id}/avance', 'registrarAvance')->name('avance');
            Route::post('/{id}/avance/{avanceId}/editar', 'editarAvance')->name('avance.editar');
            Route::delete('/{id}/avance/{avanceId}', 'eliminarAvance')->name('avance.eliminar');
            Route::delete('/{id}', 'eliminar')->name('eliminar');
        });

        // Asambleas
        Route::controller(AdminAsambleaController::class)->prefix('asamblea')->name('asamblea.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/listar', 'listar')->name('listar');
            Route::post('/crear', 'crear')->name('crear');
            Route::get('/detalle/{id}', 'detalle')->name('detalle');
            Route::get('/tablero/{id}', 'tablero')->name('tablero');
            Route::post('/marcar-presente/{id}', 'marcarPresente')->name('marcarPresente');
            Route::post('/marcar-grupo/{id}', 'marcarGrupo')->name('marcarGrupo');
            Route::post('/iniciar/{id}', 'iniciar')->name('iniciar');
            Route::post('/cerrar/{id}', 'cerrar')->name('cerrar');
            Route::post('/punto/{puntoId}/toggle', 'togglePunto')->name('togglePunto');
            Route::post('/guardar-minuta/{id}', 'guardarMinuta')->name('guardarMinuta');
            Route::delete('/eliminar/{id}', 'eliminar')->name('eliminar');
            Route::get('/acta/{id}', 'acta')->name('acta');
        });

        // Encuestas
        Route::controller(AdminEncuestaController::class)->prefix('encuesta')->name('encuesta.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/obtener', 'obtenerEncuestas')->name('obtener');
            Route::post('/crear', 'crearEncuesta')->name('crear');
            Route::get('/ver/{id}', 'obtenerEncuesta')->name('ver');
            Route::get('/resultados/{id}', 'obtenerResultados')->name('resultados');
            Route::post('/cerrar/{id}', 'cerrarEncuesta')->name('cerrar');
            Route::post('/abrir/{id}', 'abrirEncuesta')->name('abrir');
            Route::delete('/eliminar/{id}', 'eliminarEncuesta')->name('eliminar');
        });
        // Correos
        Route::controller(CorreoController::class)->prefix('correos')->name('correos.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/listar', 'listar')->name('listar');
            Route::get('/ver/{id}', 'ver')->name('ver');
            Route::delete('/eliminar/{id}', 'eliminar')->name('eliminar');
            Route::post('/eliminar-seleccionados', 'eliminarSeleccionados')->name('eliminarSeleccionados');
        });
        // Solicitudes
        Route::controller(AdminSolicitudController::class)->prefix('solicitudes')->name('solicitudes.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/listar', 'listar')->name('listar');
            Route::get('/ver/{id}', 'ver')->name('ver');
        });
    });

    Route::prefix('usuario')->name('usuario.')->middleware('rol:administrador,super-administrador,usuario')->group(function () {
        Route::controller(PerfilController::class)->prefix('perfil')->name('perfil.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/actualizarFoto', 'actualizarFoto')->name('actualizarFoto');
            Route::post('/actualizarDatosPerfil', 'actualizarDatos')->name('actualizarDatos');
            Route::post('/actualizarPassword', 'actualizarPassword')->name('actualizarPassword');
            Route::post('/aprobarInquilino/{id}', 'aprobarInquilino')->name('aprobarInquilino');
            Route::delete('/eliminarInquilino/{id}', 'eliminarInquilino')->name('eliminarInquilino');
            Route::post('/actualizarPago', 'actualizarPago')->name('actualizarPago');
            Route::get('/solicitudes', [SolicitudController::class, 'indexDueno'])->name('solicitudes');
        });

        // Notificaciones push del navegador (una suscripción por dispositivo)
        Route::controller(WebPushController::class)->prefix('perfil/push')->name('perfil.push.')->group(function () {
            Route::post('/suscribir', 'suscribir')->name('suscribir');
            Route::post('/desuscribir', 'desuscribir')->name('desuscribir');
            Route::post('/probar', 'probar')->name('probar');
        });
        Route::post('/solicitudes/{id}/responder', [SolicitudController::class, 'responder'])->name('solicitudes.responder');
        Route::controller(App\Http\Controllers\Usuario\PagoController::class)->prefix('pago')->name('pago.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/detalle/{id}', 'detalle')->name('detalle');
            Route::post('/subir-comprobante/{id}', 'subirComprobante')->name('subirComprobante');
            Route::get('/descargar/{id}', 'descargarRecibo')->name('descargar');
        });
        Route::controller(App\Http\Controllers\Usuario\ComunicadosController::class)->prefix('comunicados')->name('comunicados.')->group(function () {
            Route::get('/', 'index')->name('index');
        });
        Route::controller(App\Http\Controllers\Usuario\ContactoController::class)->prefix('contacto')->name('contacto.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/obtener-contactos', 'obtenerContactos')->name('obtenerContactos');
        });
        Route::controller(App\Http\Controllers\Usuario\ReservaController::class)->prefix('reserva')->name('reserva.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/crear', 'crear')->name('crear');
            Route::post('/guardar', 'guardar')->name('guardar');
            Route::get('/mis-reservas', 'misReservas')->name('misReservas');
            Route::get('/editar/{id}', 'editar')->name('editar');
            Route::put('/actualizar/{id}', 'actualizar')->name('actualizar');
            Route::delete('/eliminar/{id}', 'eliminar')->name('eliminar');
            Route::get('/obtener-reservas', 'obtenerReservas')->name('obtenerReservas');
        });
        Route::controller(App\Http\Controllers\Usuario\MascotaController::class)->prefix('mascota')->name('mascota.')->group(function () {
            Route::get('/', 'misMascotas')->name('index');
            Route::get('/crear', 'index')->name('crear');
            Route::post('/guardar', 'crear')->name('guardar');
            Route::get('/editar/{id}', 'editar')->name('editar');
            Route::put('/actualizar/{id}', 'actualizar')->name('actualizar');
            Route::delete('/eliminar/{id}', 'eliminar')->name('eliminar');
        });
        Route::controller(App\Http\Controllers\Usuario\VecinoController::class)->prefix('vecino')->name('vecino.')->group(function () {
            Route::get('/', 'index')->name('index');
        });
        Route::controller(App\Http\Controllers\Usuario\ProyectoController::class)->prefix('proyectos')->name('proyectos.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/{id}', 'ver')->name('ver');
        });
        Route::controller(App\Http\Controllers\Usuario\SancionController::class)->prefix('sancion')->name('sancion.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/subir-pago/{id}', 'subirPago')->name('subirPago');
        });
        Route::controller(App\Http\Controllers\Usuario\ReportesController::class)->prefix('reportes')->name('reportes.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/resumen-general', 'resumenGeneral')->name('resumenGeneral');
            Route::get('/ingresos-detalle', 'ingresosDetalle')->name('ingresosDetalle');
            Route::get('/percepciones-mes', 'percepcionesPorMes')->name('percepcionesMes');
            Route::get('/gastos-mes', 'gastosPorMes')->name('gastosMes');
            Route::get('/gastos-categoria', 'gastosPorCategoria')->name('gastosCategoria');
            Route::get('/pagos-concepto', 'pagosPorConcepto')->name('pagosConcepto');
            Route::get('/documentos-recientes', 'documentosRecientes')->name('documentosRecientes');
            Route::get('/multas-mes', 'multasPorMes')->name('multasMes');
            Route::get('/estado-pagos', 'estadoPagos')->name('estadoPagos');
            Route::get('/usuarios-pagos', 'usuariosPagos')->name('usuariosPagos');
        });
        Route::controller(App\Http\Controllers\Usuario\DocumentosController::class)->prefix('documentos')->name('documentos.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/obtener', 'obtenerDocumentos')->name('obtener');
        });
        Route::controller(App\Http\Controllers\Usuario\VehiculoController::class)->prefix('vehiculo')->name('vehiculo.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/guardar', 'guardar')->name('guardar');
            Route::post('/actualizar/{id}', 'actualizar')->name('actualizar');
            Route::delete('/eliminar/{id}', 'eliminar')->name('eliminar');
        });
        Route::controller(EncuestaController::class)->prefix('encuesta')->name('encuesta.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/obtener', 'obtenerEncuestas')->name('obtener');
            Route::post('/votar', 'votar')->name('votar');
            Route::get('/historial', 'obtenerHistorial')->name('historial');
        });
        // Asambleas (vecino)
        Route::controller(AsambleaController::class)->prefix('asamblea')->name('asamblea.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/listar', 'listar')->name('listar');
            Route::get('/detalle/{id}', 'detalle')->name('detalle');
            Route::post('/delegar/{id}', 'delegar')->name('delegar');
            Route::post('/revocar/{id}', 'revocar')->name('revocar');
            Route::post('/votar/{id}', 'votar')->name('votar');
        });

        Route::controller(EstacionamientoController::class)->prefix('estacionamiento')->name('estacionamiento.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/disponibilidad', 'obtenerDisponibilidad')->name('disponibilidad');
            Route::post('/ocupar', 'ocuparCajon')->name('ocupar');
            Route::post('/liberar', 'liberarCajon')->name('liberar');
            Route::post('/inicializar', 'inicializarCajones')->name('inicializar');
        });
        Route::controller(SolicitudController::class)->prefix('solicitudes')->name('solicitudes.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/crear', 'create')->name('create');
            Route::post('/guardar', 'store')->name('store');
            Route::post('/eliminar/{id}', 'eliminar')->name('eliminar');
        });
    });
});

require __DIR__.'/auth.php';
