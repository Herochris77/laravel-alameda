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

/*
 * Token de las rutas de mantenimiento.
 *
 * Estas rutas no llevan sesión porque el hosting no da acceso a terminal y hay
 * que ejecutarlas desde el navegador. El token era 'admin123' escrito en el
 * código: quien lo adivinara podía migrar la base, disparar correos a todos
 * los vecinos o acreditar saldos a favor. Ahora sale del .env.
 *
 * Define MANTENIMIENTO_TOKEN en producción con algo largo y privado.
 * El valor por omisión se conserva solo para no dejar las rutas inservibles
 * si la variable falta tras desplegar.
 */
if (! function_exists('tokenMantenimientoValido')) {
    function tokenMantenimientoValido($token): bool
    {
        return hash_equals(
            (string) env('MANTENIMIENTO_TOKEN', 'admin123'),
            (string) $token
        );
    }
}

Route::get('/limpiar-cache/{token}', function ($token) {
    if (! tokenMantenimientoValido($token)) {
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
    if (! tokenMantenimientoValido($token)) {
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

    // Fecha real del pago en detallepagos ----------------------------------
    //
    // Hasta ahora el sistema deducía "cuándo se pagó" a partir de updated_at,
    // que en realidad es la fecha del último cambio de la fila. Eso marcaba
    // como "Tarde" a quien pagó puntual pero se validó después del venci-
    // miento, y descuadraba los reportes por mes.
    //
    // Esta columna es ADITIVA y nullable: no altera ni borra ningún dato.
    if ($schema::hasTable('detallepagos')) {
        if (! $schema::hasColumn('detallepagos', 'fecha_pago')) {
            $schema::table('detallepagos', function (\Illuminate\Database\Schema\Blueprint $t) {
                $t->date('fecha_pago')->nullable()->after('cantidad_pago');
            });
            $pasos[] = "✅ Columna 'fecha_pago' agregada a 'detallepagos' (nullable, sin tocar datos).";
        } else {
            $pasos[] = "ℹ️  Columna 'detallepagos.fecha_pago' ya existía. No se tocó.";
        }

        // NO se rellenan los registros históricos.
        //
        // Se consideró usar created_at, pero en esta tabla esa columna es la
        // fecha en que la administración CREÓ EL CARGO (idéntica para todos los
        // vecinos del mismo concepto), no la fecha del comprobante de cada uno.
        // Rellenar con ella inventaría un dato: quedaría siempre antes del
        // vencimiento y todo pago aparecería puntual.
        //
        // updated_at tampoco sirve: es la fecha de la última edición, que es
        // justo el error que esta columna viene a corregir.
        //
        // Para los pagos anteriores a esta columna el dato sencillamente no
        // existe. Se quedan en NULL y la interfaz los muestra como
        // "Sin registrar", sin marcarlos tarde ni puntuales. Tesorería puede
        // capturar la fecha a mano en los casos que le interesen.
        $historicos = $db::table('detallepagos')
            ->whereNull('fecha_pago')
            ->whereNotNull('path_pago')
            ->count();

        $pasos[] = "ℹ️  {$historicos} pago(s) histórico(s) quedan sin fecha (se muestran como 'Sin registrar'). No se inventó ninguna fecha.";
    } else {
        $pasos[] = "ℹ️  Tabla 'detallepagos' no encontrada (se omite).";
    }

    // Misma fecha real para las multas, para que los reportes puedan sumar
    // recibos y sanciones con un criterio único.
    if ($schema::hasTable('sanciones')) {
        if (! $schema::hasColumn('sanciones', 'fecha_pago')) {
            $schema::table('sanciones', function (\Illuminate\Database\Schema\Blueprint $t) {
                $t->date('fecha_pago')->nullable()->after('pago_path');
            });
            $pasos[] = "✅ Columna 'fecha_pago' agregada a 'sanciones' (nullable, sin tocar datos).";
        } else {
            $pasos[] = "ℹ️  Columna 'sanciones.fecha_pago' ya existía. No se tocó.";
        }

        // Igual que en detallepagos: no se inventa fecha para lo histórico.
        // pago_path se creó NOT NULL con cadena vacía, así que para contar los
        // que sí tienen comprobante hay que excluir también la cadena vacía.
        $historicosS = $db::table('sanciones')
            ->whereNull('fecha_pago')
            ->whereNotNull('pago_path')
            ->where('pago_path', '!=', '')
            ->count();

        $pasos[] = "ℹ️  {$historicosS} multa(s) histórica(s) quedan sin fecha. No se inventó ninguna fecha.";
    } else {
        $pasos[] = "ℹ️  Tabla 'sanciones' no encontrada (se omite).";
    }

    // Cargo dentro de la mesa directiva -------------------------------------
    //
    // El rol dice a qué módulos entra alguien; el cargo dice qué le toca hacer
    // ahí dentro. Hasta ahora toda la mesa podía crear recibos, validar pagos
    // y borrarlos por igual. Nace vacío a propósito: mientras ningún usuario
    // tenga cargo de tesorero, nada cambia para nadie.
    if ($schema::hasTable('users') && ! $schema::hasColumn('users', 'cargo')) {
        $schema::table('users', function (\Illuminate\Database\Schema\Blueprint $t) {
            $t->string('cargo', 30)->nullable()->after('rol');
        });
        $pasos[] = "✅ Columna 'cargo' agregada a 'users' (nullable, nace vacía: nadie pierde permisos).";
    } else {
        $pasos[] = "ℹ️  Columna 'users.cargo' ya existía. No se tocó.";
    }

    // Motivo del rechazo ----------------------------------------------------
    //
    // Cuando tesorería rechaza un comprobante, el vecino solo veía el estado
    // "rechazado" sin saber qué corregir. Aquí se guarda la explicación, que
    // viaja al correo, a la notificación y a su pantalla al reintentar.
    if ($schema::hasTable('detallepagos') && ! $schema::hasColumn('detallepagos', 'comentario_rechazo')) {
        $schema::table('detallepagos', function (\Illuminate\Database\Schema\Blueprint $t) {
            $t->text('comentario_rechazo')->nullable()->after('fecha_pago');
        });
        $pasos[] = "✅ Columna 'comentario_rechazo' agregada a 'detallepagos' (nullable, sin tocar datos).";
    } else {
        $pasos[] = "ℹ️  Columna 'detallepagos.comentario_rechazo' ya existía. No se tocó.";
    }

    // Recargo por mora, configurable por concepto ---------------------------
    //
    // Hasta ahora el 10 % de recargo no existía en el sistema: el vecino lo
    // sumaba a mano y quedaba indistinguible de un adelanto. Con esta columna
    // cada concepto declara su propio porcentaje (NULL = sin recargo), y el
    // recargo se calcula contra la FECHA REAL de la transferencia, no contra
    // la fecha en que se sube el comprobante.
    if ($schema::hasTable('pagos') && ! $schema::hasColumn('pagos', 'recargo_pct')) {
        $schema::table('pagos', function (\Illuminate\Database\Schema\Blueprint $t) {
            $t->decimal('recargo_pct', 5, 2)->nullable()->after('vencimiento');
        });
        $pasos[] = "✅ Columna 'recargo_pct' agregada a 'pagos' (nullable = sin recargo, no altera los recibos existentes).";
    } else {
        $pasos[] = "ℹ️  Columna 'pagos.recargo_pct' ya existía. No se tocó.";
    }

    // Cierre mensual de caja -------------------------------------------------
    //
    // Guarda lo único que la plataforma no puede saber del reporte mensual:
    // cuánto quedó en el banco y cuánto en efectivo al cerrar el mes. El saldo
    // inicial del mes siguiente se arrastra de aquí.
    if (! $schema::hasTable('cortes_mensuales')) {
        $schema::create('cortes_mensuales', function (\Illuminate\Database\Schema\Blueprint $t) {
            $t->id();
            $t->string('periodo', 7)->unique();   // Y-m
            $t->double('saldo_banco')->default(0);
            $t->double('saldo_efectivo')->default(0);
            $t->text('notas')->nullable();
            $t->unsignedBigInteger('created_by')->nullable();
            $t->timestamps();
        });
        $pasos[] = "✅ Tabla 'cortes_mensuales' creada (nueva, no altera nada existente).";
    } else {
        $pasos[] = "ℹ️  Tabla 'cortes_mensuales' ya existía. No se tocó.";
    }

    // Saldo a favor de los vecinos -----------------------------------------
    //
    // Tabla NUEVA: no toca ninguna de las existentes. El saldo no se guarda
    // como un número, se calcula sumando estos movimientos, de modo que
    // siempre quede el rastro de de dónde salió cada peso.
    if (! $schema::hasTable('saldo_movimientos')) {
        $schema::create('saldo_movimientos', function (\Illuminate\Database\Schema\Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('user_id');
            $t->string('tipo', 20);                  // abono | aplicacion | ajuste
            $t->double('monto');
            $t->unsignedBigInteger('detallepago_id')->nullable();
            $t->string('descripcion')->nullable();
            $t->unsignedBigInteger('created_by')->nullable();
            $t->softDeletes();
            $t->timestamps();
            $t->index('user_id');
            $t->index('detallepago_id');
        });
        $pasos[] = "✅ Tabla 'saldo_movimientos' creada (nueva, no altera nada existente).";
    } else {
        $pasos[] = "ℹ️  Tabla 'saldo_movimientos' ya existía. No se tocó.";
    }

    // Control del vecino sobre su propia exposición --------------------------
    //
    // `visible_directorio` nace en 1 para que nadie desaparezca del directorio
    // de un día para otro sin haberlo pedido; lo que cambia es que ahora puede
    // salirse cuando quiera, desde su perfil.
    //
    // `desvinculado_en` marca a quien decidió no aceptar el aviso y pidió que
    // sus datos personales se retiren. No se borra la cuenta: se anonimiza y
    // se conserva el historial de pagos, que respalda las cuentas del
    // condominio.
    if (! $schema::hasColumn('users', 'visible_directorio')) {
        $schema::table('users', function (\Illuminate\Database\Schema\Blueprint $t) {
            $t->boolean('visible_directorio')->default(1);
        });
        $pasos[] = "✅ Columna 'users.visible_directorio' agregada (default 1: nadie desaparece del directorio sin pedirlo).";
    } else {
        $pasos[] = "ℹ️  Columna 'users.visible_directorio' ya existía. No se tocó.";
    }

    if (! $schema::hasColumn('users', 'desvinculado_en')) {
        $schema::table('users', function (\Illuminate\Database\Schema\Blueprint $t) {
            $t->timestamp('desvinculado_en')->nullable();
        });
        $pasos[] = "✅ Columna 'users.desvinculado_en' agregada (nullable, no toca ningún registro).";
    } else {
        $pasos[] = "ℹ️  Columna 'users.desvinculado_en' ya existía. No se tocó.";
    }

    // Control de recordatorios de cobro --------------------------------------
    //
    // Guarda el día en que se mandó el último aviso de un concepto, para que
    // el cron —o el botón de ejecución manual, que se puede pulsar varias
    // veces— no dispare el mismo correo dos veces a los 42 vecinos.
    if (! $schema::hasColumn('pagos', 'ultimo_aviso')) {
        $schema::table('pagos', function (\Illuminate\Database\Schema\Blueprint $t) {
            $t->date('ultimo_aviso')->nullable();
        });
        $pasos[] = "✅ Columna 'pagos.ultimo_aviso' agregada (nullable, no toca ningún registro).";
    } else {
        $pasos[] = "ℹ️  Columna 'pagos.ultimo_aviso' ya existía. No se tocó.";
    }

    // Aceptación del aviso de privacidad -------------------------------------
    //
    // Para los datos patrimoniales —los comprobantes de pago— la ley pide
    // consentimiento EXPRESO, no basta con publicar el aviso. Aquí queda el
    // rastro: quién aceptó, cuándo y qué versión del documento.
    //
    // Se guarda la versión y no solo la fecha para poder responder "¿aceptó
    // el aviso que estaba vigente entonces?" sin adivinar.
    foreach ([
        ['acepto_aviso_en', fn ($t) => $t->timestamp('acepto_aviso_en')->nullable()],
        ['acepto_aviso_version', fn ($t) => $t->string('acepto_aviso_version', 20)->nullable()],
    ] as [$columna, $definir]) {
        if (! $schema::hasColumn('users', $columna)) {
            $schema::table('users', function (\Illuminate\Database\Schema\Blueprint $t) use ($definir) {
                $definir($t);
            });
            $pasos[] = "✅ Columna 'users.{$columna}' agregada (nullable, no toca ningún registro).";
        } else {
            $pasos[] = "ℹ️  Columna 'users.{$columna}' ya existía. No se tocó.";
        }
    }

    $pasos[] = '⚠️  NADIE queda como que ya aceptó: la columna nace vacía. A todos les '
        .'aparecerá la ventana de aceptación la próxima vez que entren. Es a propósito, '
        .'porque dar por aceptado lo que nadie aceptó sería justo lo contrario de lo que '
        .'se busca.';

    // Pagos en efectivo y firma del recibo -----------------------------------
    //
    // `forma_pago` distingue el pago en efectivo, que no tiene comprobante que
    // subir porque el dinero se entregó en mano. NULL significa transferencia,
    // que es como se venían registrando todos.
    //
    // `validado_por` guarda quién aprobó el pago, para que el recibo del vecino
    // lleve la firma de quien realmente lo validó y no la del tesorero en turno
    // años después.
    //
    // `folio_recibo` es el consecutivo del recibo entregado en mano: es lo que
    // permite rastrear un papel firmado hasta el registro del sistema.
    foreach ([
        ['forma_pago', fn ($t) => $t->string('forma_pago', 20)->nullable()],
        ['validado_por', fn ($t) => $t->unsignedBigInteger('validado_por')->nullable()],
        ['folio_recibo', fn ($t) => $t->string('folio_recibo', 30)->nullable()],
    ] as [$columna, $definir]) {
        if (! $schema::hasColumn('detallepagos', $columna)) {
            $schema::table('detallepagos', function (\Illuminate\Database\Schema\Blueprint $t) use ($definir) {
                $definir($t);
            });
            $pasos[] = "✅ Columna 'detallepagos.{$columna}' agregada (nullable, no toca ningún registro).";
        } else {
            $pasos[] = "ℹ️  Columna 'detallepagos.{$columna}' ya existía. No se tocó.";
        }
    }

    // Firma digitalizada del tesorero, para los recibos que descarga el vecino.
    if (! $schema::hasColumn('users', 'firma')) {
        $schema::table('users', function (\Illuminate\Database\Schema\Blueprint $t) {
            $t->string('firma')->nullable();
        });
        $pasos[] = "✅ Columna 'users.firma' agregada (nullable, no toca ningún registro).";
    } else {
        $pasos[] = "ℹ️  Columna 'users.firma' ya existía. No se tocó.";
    }

    // Fecha real del egreso ---------------------------------------------------
    //
    // Los gastos se registran como documentos con monto, y hasta hoy el
    // reporte los fechaba por `created_at`, que es cuando se subió el PDF, no
    // cuando salió el dinero del banco. Es el mismo defecto que ya se corrigió
    // del lado de los ingresos con `detallepagos.fecha_pago`.
    //
    // NO se rellena con created_at: inventar la fecha haría que el reporte
    // pareciera cuadrado sin estarlo. Lo que no se sabe se muestra como
    // "sin fecha bancaria" y se corrige a mano cuando el tesorero quiera.
    foreach ([
        ['fecha_gasto', fn ($t) => $t->date('fecha_gasto')->nullable()],
        ['proveedor', fn ($t) => $t->string('proveedor', 150)->nullable()],
        ['forma_pago', fn ($t) => $t->string('forma_pago', 20)->nullable()],
        ['servicio_id', fn ($t) => $t->unsignedBigInteger('servicio_id')->nullable()],
    ] as [$columna, $definir]) {
        if (! $schema::hasColumn('documentos', $columna)) {
            $schema::table('documentos', function (\Illuminate\Database\Schema\Blueprint $t) use ($definir) {
                $definir($t);
            });
            $pasos[] = "✅ Columna 'documentos.{$columna}' agregada (nullable, no toca ningún registro).";
        } else {
            $pasos[] = "ℹ️  Columna 'documentos.{$columna}' ya existía. No se tocó.";
        }
    }

    $gastosSinFecha = \Illuminate\Support\Facades\DB::table('documentos')
        ->whereNotNull('cantidad')
        ->whereNull('fecha_gasto')
        ->whereNull('deleted_at')
        ->count();

    $pasos[] = "ℹ️  {$gastosSinFecha} gasto(s) histórico(s) quedan sin fecha bancaria (se muestran como 'Sin registrar'). No se inventó ninguna fecha.";

    // ¿Este concepto puede liquidarse con el saldo a favor del vecino? -------
    //
    // La cuota de mantenimiento sí: es justo lo que adelantó. Una derrama o un
    // gasto extraordinario no, porque ese dinero está comprometido para otra
    // cosa. Nace en 1 para que los conceptos que ya existen sigan
    // comportándose exactamente igual que hasta hoy.
    if (! $schema::hasColumn('pagos', 'aplica_saldo')) {
        $schema::table('pagos', function (\Illuminate\Database\Schema\Blueprint $t) {
            $t->boolean('aplica_saldo')->default(1);
        });
        $pasos[] = "✅ Columna 'pagos.aplica_saldo' agregada (default 1: no cambia el comportamiento de los conceptos existentes).";
    } else {
        $pasos[] = "ℹ️  Columna 'pagos.aplica_saldo' ya existía. No se tocó.";
    }

    // Servicios y pagos recurrentes de la tesorería --------------------------
    //
    // Catálogo de obligaciones del condominio (agua, basura, internet) con su
    // referencia de pago y su vencimiento, más el historial de lo pagado. Dos
    // tablas NUEVAS: no tocan ninguna existente.
    if (! $schema::hasTable('servicios_recurrentes')) {
        $schema::create('servicios_recurrentes', function (\Illuminate\Database\Schema\Blueprint $t) {
            $t->id();
            $t->string('nombre', 120);
            $t->string('proveedor', 120)->nullable();
            $t->string('referencia', 120)->nullable();
            $t->string('categoria', 30)->default('otro');
            $t->double('monto_estimado')->default(0);
            $t->string('periodicidad', 20)->default('mensual');
            $t->date('proximo_vencimiento')->nullable();
            $t->unsignedSmallInteger('dias_aviso')->default(1);
            $t->string('forma_pago', 20)->default('transferencia');
            $t->string('contacto', 150)->nullable();
            $t->text('notas')->nullable();
            $t->boolean('activo')->default(true);
            $t->date('ultimo_aviso')->nullable();
            $t->unsignedBigInteger('created_by')->nullable();
            $t->softDeletes();
            $t->timestamps();
            $t->index('proximo_vencimiento');
            $t->index('activo');
        });
        $pasos[] = "✅ Tabla 'servicios_recurrentes' creada (nueva, no altera nada existente).";
    } else {
        $pasos[] = "ℹ️  Tabla 'servicios_recurrentes' ya existía. No se tocó.";
    }

    if (! $schema::hasTable('servicio_pagos')) {
        $schema::create('servicio_pagos', function (\Illuminate\Database\Schema\Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('servicio_id');
            $t->string('periodo', 7);              // Y-m
            $t->date('fecha_pago');
            $t->double('monto_pagado');
            $t->string('folio', 100)->nullable();
            $t->text('notas')->nullable();
            $t->unsignedBigInteger('created_by')->nullable();
            $t->timestamps();
            $t->index('servicio_id');
        });
        $pasos[] = "✅ Tabla 'servicio_pagos' creada (nueva, no altera nada existente).";
    } else {
        $pasos[] = "ℹ️  Tabla 'servicio_pagos' ya existía. No se tocó.";
    }

    return response('<pre>'.e(implode("\n", $pasos))."\n\nListo. No se modificó ni eliminó ningún dato existente.</pre>");
});

/*
 * Retiro del módulo de vehículos.
 *
 * Borra los registros y las fotografías de los autos. Es IRREVERSIBLE y no se
 * dispara sola: por omisión solo muestra qué se eliminaría, y hay que
 * confirmar explícitamente.
 *
 *   Ver qué se borraría : /purgar-vehiculos/TOKEN
 *   Borrar de verdad    : /purgar-vehiculos/TOKEN?aplicar=CONFIRMAR
 */
Route::get('/purgar-vehiculos/{token}', function ($token) {
    if (! tokenMantenimientoValido($token)) {
        abort(403, 'No autorizado');
    }

    if (! \Illuminate\Support\Facades\Schema::hasTable('vehiculos')) {
        return response('<pre>La tabla vehiculos no existe. No hay nada que borrar.</pre>');
    }

    $aplicar = request('aplicar') === 'CONFIRMAR';
    $disco = \Illuminate\Support\Facades\Storage::disk('public');

    $vehiculos = \Illuminate\Support\Facades\DB::table('vehiculos')
        ->leftJoin('users', 'users.id', '=', 'vehiculos.user_id')
        ->get(['vehiculos.id', 'vehiculos.placas', 'vehiculos.marca', 'vehiculos.modelo',
            'vehiculos.foto', 'users.casa', 'users.nombre']);

    $lineas = [];
    $lineas[] = $aplicar ? '=== BORRADO APLICADO ===' : '=== VISTA PREVIA (no se ha borrado nada) ===';
    $lineas[] = '';
    $lineas[] = 'Registros de vehículos: '.$vehiculos->count();
    $lineas[] = '';

    $archivos = 0;
    $borrados = 0;

    foreach ($vehiculos as $v) {
        $tieneFoto = $v->foto && $disco->exists('vehiculos/'.$v->foto);

        $lineas[] = sprintf('  casa %-5s %-26s %-12s %s',
            $v->casa ?? '?',
            mb_substr(($v->marca ?? '').' '.($v->modelo ?? ''), 0, 25),
            $v->placas ?? 'sin placas',
            $tieneFoto ? '[con fotografía]' : '[sin fotografía]');

        if ($tieneFoto) {
            $archivos++;

            if ($aplicar) {
                $disco->delete('vehiculos/'.$v->foto);
            }
        }
    }

    if ($aplicar) {
        $borrados = \Illuminate\Support\Facades\DB::table('vehiculos')->delete();

        // Fotos huérfanas que quedaron de registros ya eliminados antes.
        foreach ($disco->files('vehiculos') as $archivo) {
            $disco->delete($archivo);
            $archivos++;
        }
    }

    $lineas[] = '';
    $lineas[] = 'Fotografías: '.$archivos;
    $lineas[] = '';

    if ($aplicar) {
        $lineas[] = "✅ Se eliminaron {$borrados} registro(s) y {$archivos} fotografía(s).";
        $lineas[] = '';
        $lineas[] = 'No se tocó ninguna otra tabla. Los pagos, usuarios y documentos';
        $lineas[] = 'quedaron exactamente como estaban.';
    } else {
        $lineas[] = 'Esto es SOLO una vista previa. Para borrarlo de verdad, agrega al final';
        $lineas[] = 'de la dirección:  ?aplicar=CONFIRMAR';
        $lineas[] = '';
        $lineas[] = 'No se puede deshacer. Respalda la base antes si quieres vuelta atrás.';
    }

    return response('<pre>'.e(implode("\n", $lineas)).'</pre>');
});

/*
 * Limpieza de avisos viejos que nombran a un vecino.
 *
 * El módulo de estacionamiento dejó de mandar estos avisos hace tiempo, pero
 * los que se mandaron entonces siguen en el centro de notificaciones de todos:
 * cada uno dice "el cajón X está ahora ocupado por <nombre completo> #<casa>".
 * Es el nombre de un vecino a la vista de los demás, sin que aporte nada hoy.
 *
 * Solo toca notificaciones. Ni un pago, ni un usuario, ni un documento.
 *
 *   Ver qué se borraría : /purgar-avisos-viejos/TOKEN
 *   Borrar de verdad    : /purgar-avisos-viejos/TOKEN?aplicar=CONFIRMAR
 */
Route::get('/purgar-avisos-viejos/{token}', function ($token) {
    if (! tokenMantenimientoValido($token)) {
        abort(403, 'No autorizado');
    }

    $aplicar = request('aplicar') === 'CONFIRMAR';
    $db = \Illuminate\Support\Facades\DB::class;

    // Los textos que nombran a alguien. Se buscan literales para no barrer de
    // más: un aviso de pago o de comunicado no entra aquí.
    $patrones = [
        '%ocupado por%',
        '%lo ocupaba%',
        '%ya está en uso por%',
    ];

    $consulta = fn () => $db::table('notifications')->where(function ($q) use ($patrones) {
        foreach ($patrones as $p) {
            $q->orWhere('data', 'like', $p);
        }
    });

    $total = $db::table('notifications')->count();
    $afectadas = $consulta()->count();
    $destinatarios = $consulta()->distinct()->count('notifiable_id');

    $lineas = [];
    $lineas[] = $aplicar ? '=== BORRADO APLICADO ===' : '=== VISTA PREVIA (no se ha borrado nada) ===';
    $lineas[] = '';
    $lineas[] = 'Notificaciones en total      : '.$total;
    $lineas[] = 'Que nombran a un vecino      : '.$afectadas;
    $lineas[] = 'Vecinos que las están viendo : '.$destinatarios;
    $lineas[] = '';
    $lineas[] = 'Ejemplos:';

    foreach ($consulta()->limit(5)->get(['data']) as $n) {
        $d = json_decode($n->data, true);
        $lineas[] = '  · '.mb_substr($d['mensaje'] ?? $n->data, 0, 90);
    }

    $lineas[] = '';

    if ($aplicar) {
        $borradas = $consulta()->delete();
        $lineas[] = "✅ Se eliminaron {$borradas} notificación(es).";
        $lineas[] = '';
        $lineas[] = 'Quedan '.$db::table('notifications')->count().' notificaciones.';
        $lineas[] = 'No se tocó ninguna otra tabla.';
    } else {
        $lineas[] = 'Esto es SOLO una vista previa. Para borrarlo de verdad, agrega al final';
        $lineas[] = 'de la dirección:  ?aplicar=CONFIRMAR';
        $lineas[] = '';
        $lineas[] = 'Se borran únicamente notificaciones. Nada de pagos ni de usuarios.';
    }

    return response('<pre>'.e(implode("\n", $lineas)).'</pre>');
});

/*
 * Conversión de sobrepagos históricos en saldo a favor.
 *
 * Reconoce formalmente, como saldo del vecino, todo lo que ya pagó por encima
 * de su cuota antes de que existiera el módulo de saldos. Es dinero real de
 * los condóminos, así que NO se aplica sola: por defecto solo muestra el
 * detalle de a quién y cuánto, y hay que confirmar explícitamente.
 *
 *   Ver el detalle : /saldos-historicos/admin123
 *   Aplicar        : /saldos-historicos/admin123?aplicar=CONFIRMAR
 *
 * Es idempotente: registrarExcedente() no duplica abonos de un recibo que ya
 * los generó, así que volver a ejecutarla no infla ningún saldo.
 */
Route::get('/saldos-historicos/{token}', function ($token) {
    if (! tokenMantenimientoValido($token)) {
        abort(403, 'No autorizado');
    }

    if (! \Illuminate\Support\Facades\Schema::hasTable('saldo_movimientos')) {
        return response('<pre>Falta la tabla saldo_movimientos. Corre primero /migrar/admin123</pre>');
    }

    $aplicar = request('aplicar') === 'CONFIRMAR';
    $servicio = app(\App\Services\SaldoService::class);

    // Conceptos que el tesorero puede declarar como anticipo íntegro: ahí el
    // pago COMPLETO es saldo a favor, no solo lo que exceda de la cuota.
    // Hace falta porque algunos anticipos se cargaron con la cuota igual al
    // monto anticipado (p. ej. "Pago Anticipado Junio 2026" de $1,300), y por
    // esa vía el excedente sale en cero aunque todo el dinero sea adelanto.
    $idsAnticipo = collect(explode(',', (string) request('anticipos', '')))
        ->map(fn ($x) => (int) trim($x))
        ->filter()
        ->all();

    // Conceptos que se dejan fuera del cálculo de excedentes.
    //
    // Hace falta para las derramas cobradas en parcialidades: en el proyecto
    // de la puerta peatonal la cuota se registró por parcialidad ($427) y
    // varios vecinos pagaron las dos juntas ($854). Ese doble no es dinero a
    // favor suyo, es la misma derrama liquidada de una sola vez.
    $idsExcluidos = collect(explode(',', (string) request('excluir', '')))
        ->map(fn ($x) => (int) trim($x))
        ->filter()
        ->all();

    $abonar = function ($detalle, $monto, $descripcion) use ($aplicar) {
        $previo = (float) \App\Models\SaldoMovimiento::where('detallepago_id', $detalle->id)
            ->where('tipo', \App\Models\SaldoMovimiento::TIPO_ABONO)
            ->sum('monto');

        $pendiente = round($monto - $previo, 2);

        if ($pendiente <= 0) {
            return 'ya registrado';
        }

        if (! $aplicar) {
            return 'por aplicar';
        }

        \App\Models\SaldoMovimiento::create([
            'user_id' => $detalle->user_id,
            'tipo' => \App\Models\SaldoMovimiento::TIPO_ABONO,
            'monto' => $pendiente,
            'detallepago_id' => $detalle->id,
            'descripcion' => $descripcion,
            'created_by' => optional(\Illuminate\Support\Facades\Auth::user())->id,
        ]);

        return 'APLICADO';
    };

    $fmt = fn ($d, $monto, $estado) => sprintf(
        '  %-30s casa %-5s %-28s $%10s  (%s)',
        mb_substr($d->user->nombre ?? 'Usuario eliminado', 0, 28),
        $d->user->casa ?? '?',
        mb_substr($d->pago->concepto, 0, 26),
        number_format($monto, 2),
        $estado
    );

    $pagados = \App\Models\Detallepago::with(['pago', 'user'])
        ->where('estado', 'pagado')
        ->whereNotNull('cantidad_pago')
        ->get()
        ->filter(fn ($d) => $d->pago);

    /* ---------- Bloque A: lo pagado por encima de la cuota ----------
     *
     * No todo excedente es saldo a favor. Quien paga tarde cubre un recargo
     * del 10 %, y ese dinero es ingreso del condominio, no crédito del vecino.
     * Como el recargo nunca se registró aparte, aquí se reconoce por su
     * proporción: un excedente que cae dentro del margen del recargo se
     * clasifica como mora y se deja FUERA del saldo.
     *
     * El porcentaje es ajustable con ?recargo=10 y el margen con ?margen=1.5
     * (en puntos porcentuales), por si algún concepto usó otra regla.
     */
    $pctRecargo = (float) request('recargo', 10);
    $margen = (float) request('margen', 1.5);

    $lineasA = [];
    $lineasRecargo = [];
    $lineasDudosas = [];
    $totalA = 0.0;
    $totalRecargo = 0.0;
    $totalDudoso = 0.0;

    $lineasExcluidas = [];
    $totalExcluido = 0.0;
    $saldoProyectado = [];

    foreach ($pagados as $d) {
        if (in_array($d->pago_id, $idsAnticipo, true)) {
            continue; // se trata completo en el bloque B
        }

        // Concepto excluido a mano: se reporta para dejar constancia, pero
        // no genera saldo a favor.
        if (in_array($d->pago_id, $idsExcluidos, true)) {
            $sobra = round($d->cantidad_pago - (float) $d->pago->cantidad, 2);

            if ($sobra > 0) {
                $totalExcluido += $sobra;
                $lineasExcluidas[] = $fmt($d, $sobra, 'excluido - no genera saldo');
            }

            continue;
        }

        $cuota = (float) $d->pago->cantidad;
        $excedente = round($d->cantidad_pago - $cuota, 2);

        if ($excedente <= 0) {
            continue;
        }

        $pct = $cuota > 0 ? ($excedente / $cuota) * 100 : null;

        // Un excedente que no alcanza a cubrir otra cuota completa no es
        // adelanto: es recargo por mora. Cubre tanto el 10 % exacto como los
        // montos irregulares que se cobraron por el mismo motivo.
        if ($pct !== null && $pct < 95) {
            $totalRecargo += $excedente;

            $etiqueta = abs($pct - $pctRecargo) <= $margen
                ? 'recargo '.round($pct, 1).'% - NO es saldo'
                : 'recargo irregular '.round($pct, 1).'% - NO es saldo';

            $lineasRecargo[] = $fmt($d, $excedente, $etiqueta);

            continue;
        }

        $totalA += $excedente;
        // En vista previa los abonos todavía no existen en la base, así que se
        // lleva aparte el saldo que tendría cada vecino para poder simular el
        // bloque C sin escribir nada.
        $saldoProyectado[$d->user_id] = ($saldoProyectado[$d->user_id] ?? 0) + $excedente;
        $lineasA[] = $fmt($d, $excedente, $abonar($d, $excedente, 'Pago de más en "'.$d->pago->concepto.'"'));
    }

    /* ---------- Bloque B: conceptos declarados como anticipo ---------- */

    $lineasB = [];
    $totalB = 0.0;

    foreach ($pagados as $d) {
        if (! in_array($d->pago_id, $idsAnticipo, true)) {
            continue;
        }

        $monto = round((float) $d->cantidad_pago, 2);

        if ($monto <= 0) {
            continue;
        }

        $totalB += $monto;
        $saldoProyectado[$d->user_id] = ($saldoProyectado[$d->user_id] ?? 0) + $monto;
        $lineasB[] = $fmt($d, $monto, $abonar($d, $monto, 'Anticipo: "'.$d->pago->concepto.'"'));
    }

    /* ---------- Conceptos que parecen anticipo y no fueron incluidos ---------- */

    $sugerencias = \App\Models\Pago::where(function ($q) {
        $q->where('concepto', 'like', '%anticip%')
            ->orWhere('concepto', 'like', '%adelant%');
    })
        ->get()
        ->reject(fn ($p) => in_array($p->id, $idsAnticipo, true) || (float) $p->cantidad == 0.0);

    /* ---------- Salida ---------- */

    $txt = $aplicar
        ? "SALDOS HISTORICOS - APLICADO\n"
        : "SALDOS HISTORICOS - VISTA PREVIA (no se ha cambiado nada)\n";

    $txt .= "\nA) ADELANTOS - se convierten en saldo a favor\n";
    $txt .= $lineasA ? implode("\n", $lineasA)."\n" : "  (ninguno)\n";
    $txt .= sprintf("  %-68s $%10s\n", '   subtotal A ('.count($lineasA).' recibos)', number_format($totalA, 2));

    if ($idsExcluidos) {
        $txt .= "\nX) CONCEPTOS EXCLUIDOS A MANO - NO se convierten\n";
        $txt .= "   (derramas cobradas en parcialidades: pagar dos juntas no es saldo a favor)\n";
        $txt .= $lineasExcluidas ? implode("\n", $lineasExcluidas)."\n" : "  (ninguno con excedente)\n";
        $txt .= sprintf("  %-68s $%10s\n", '   subtotal excluido ('.count($lineasExcluidas).' recibos)', number_format($totalExcluido, 2));
    }

    $txt .= "\nR) RECARGOS POR MORA (~".$pctRecargo."%) - NO se convierten, son ingreso del condominio\n";
    $txt .= $lineasRecargo ? implode("\n", $lineasRecargo)."\n" : "  (ninguno)\n";
    $txt .= sprintf("  %-68s $%10s\n", '   subtotal recargos ('.count($lineasRecargo).' recibos)', number_format($totalRecargo, 2));


    $txt .= "\nB) CONCEPTOS DECLARADOS COMO ANTICIPO INTEGRO\n";

    if ($idsAnticipo) {
        $txt .= $lineasB ? implode("\n", $lineasB)."\n" : "  (ninguno)\n";
        $txt .= sprintf("  %-68s $%10s\n", '   subtotal B ('.count($lineasB).' recibos)', number_format($totalB, 2));
    } else {
        $txt .= "  (no declaraste ningun concepto como anticipo integro)\n";
    }

    $txt .= "\n".str_repeat('-', 100)."\n";
    $txt .= sprintf("  %-68s $%10s\n", 'TOTAL A CONVERTIR EN SALDO A FAVOR', number_format($totalA + $totalB, 2));

    /* ---------- Completar las mensualidades que nunca se cargaron ----------
     *
     * Quien pagó por adelantado normalmente no recibió los recibos de esos
     * meses: la mesa simplemente no se los generó porque ya estaban cubiertos.
     * Eso deja su estado de cuenta incompleto y hace parecer que el anticipo
     * sigue entero cuando en realidad ya se consumió.
     *
     * Con ?completar_meses=1 se le crean esos recibos y el saldo a favor los
     * liquida solo, dejando el expediente completo y el sobrante correcto.
     *
     * Se limita a quien TIENE saldo: a un vecino sin anticipo, crearle el
     * recibo faltante le generaría una deuda, y esa es una decisión de
     * tesorería que no corresponde tomar aquí.
     */
    /* ---------- Modo neto: descontar los meses ya consumidos ----------
     *
     * Quien pagó por adelantado normalmente no recibió los recibos de esos
     * meses, así que su anticipo parece intacto cuando en realidad ya se
     * gastó. Aquí se le descuenta lo consumido con un ajuste negativo, sin
     * crear esos recibos: el histórico y el reporte no se tocan, y el vecino
     * queda con el saldo que de verdad le sobra.
     *
     * Es la alternativa a ?completar_meses=1, que sí crea los recibos pero
     * mete al reporte un ingreso que ya estaba contado.
     */
    if (request('neto') == '1') {
        $mensuales = \App\Models\Pago::where('concepto', 'like', 'MANTENIMIENTO%')
            ->where('concepto', 'not like', '%INQ%')
            ->where('cantidad', '>', 0)
            ->orderBy('vencimiento')
            ->get();

        $txt .= "\nN) MESES QUE EL ANTICIPO YA CUBRIO (se descuentan del saldo)\n";
        $txt .= "   No se crea ningun recibo: el reporte historico no se altera.\n";

        $conSaldo = $aplicar
            ? \App\Models\SaldoMovimiento::select('user_id')->distinct()->pluck('user_id')->all()
            : array_keys($saldoProyectado);

        $lineasN = [];
        $totalN = 0.0;

        // Lo descontado a cada vecino, para poder mostrar su saldo final
        // también en la vista previa (ahí los movimientos aún no existen).
        $descontadoPorUsuario = [];

        // Cuota mensual vigente, solo para traducir el saldo a "meses".
        $cuotaReferencia = (float) ($mensuales->last()->cantidad ?? 0);

        foreach ($conSaldo as $uid) {
            $disponible = $aplicar
                ? $servicio->saldo($uid)
                : ($saldoProyectado[$uid] ?? 0);

            if ($disponible <= 0) {
                continue;
            }

            $usuario = \App\Models\User::withTrashed()->find($uid);
            $yaTiene = \App\Models\Detallepago::where('user_id', $uid)->pluck('pago_id')->all();

            foreach ($mensuales as $mes) {
                if (in_array($mes->id, $yaTiene, true)) {
                    continue;
                }

                $cuota = (float) $mes->cantidad;

                if ($disponible + 0.001 < $cuota) {
                    continue;
                }

                $disponible -= $cuota;
                $totalN += $cuota;
                $descontadoPorUsuario[$uid] = ($descontadoPorUsuario[$uid] ?? 0) + $cuota;

                if ($aplicar) {
                    $servicio->ajustar(
                        $uid,
                        -$cuota,
                        'Ya cubierto por su anticipo: '.$mes->concepto.' (sin recibo generado)'
                    );
                }

                $lineasN[] = sprintf('  %-30s casa %-5s %-28s -$%9s  (%s)',
                    mb_substr($usuario->nombre ?? '?', 0, 28), $usuario->casa ?? '?',
                    mb_substr($mes->concepto, 0, 26), number_format($cuota, 2),
                    $aplicar ? 'DESCONTADO' : 'se descontaria');
            }
        }

        $txt .= $lineasN ? implode("\n", $lineasN)."\n" : "  (ninguno)\n";
        $txt .= sprintf("  %-68s -$%9s\n", '   subtotal descontado ('.count($lineasN).' meses)', number_format($totalN, 2));

        $txt .= "\n".str_repeat('-', 100)."\n";
        $txt .= sprintf("  %-68s $%10s\n", 'SALDO NETO QUE QUEDA A FAVOR DE LOS VECINOS', number_format(($totalA + $totalB) - $totalN, 2));

        /*
         * Detalle final por vecino: es la tabla que tesorería necesita revisar
         * ANTES de confirmar, así que se arma también en la vista previa
         * restando al saldo proyectado lo que se descontaría en este bloque.
         */
        $txt .= "\n  SALDO FINAL POR VECINO:\n";

        $filas = [];

        foreach ($conSaldo as $uid) {
            $usuario = \App\Models\User::withTrashed()->find($uid);

            $final = $aplicar
                ? $servicio->saldo($uid)
                : round(($saldoProyectado[$uid] ?? 0) - ($descontadoPorUsuario[$uid] ?? 0), 2);

            $meses = $final > 0 && $cuotaReferencia > 0
                ? '  ('.round($final / $cuotaReferencia, 1).' meses de cuota)'
                : '';

            $filas[] = sprintf('    casa %-5s %-32s $%10s%s',
                $usuario->casa ?? '?',
                mb_substr($usuario->nombre ?? '?', 0, 30),
                number_format(max($final, 0), 2),
                $meses);
        }

        $txt .= $filas ? implode("\n", $filas)."\n" : "    (ninguno)\n";
    }

    if (request('completar_meses') == '1') {
        $mensuales = \App\Models\Pago::where('concepto', 'like', 'MANTENIMIENTO%')
            ->where('concepto', 'not like', '%INQ%')
            ->where('cantidad', '>', 0)
            ->orderBy('vencimiento')
            ->get();

        $txt .= "\nC) MENSUALIDADES QUE NUNCA SE CARGARON (se liquidan con el saldo)\n";

        // Al aplicar, el saldo ya está en la base. En vista previa se usa el
        // proyectado, que es lo que tendrían tras los bloques A y B.
        $conSaldo = $aplicar
            ? \App\Models\SaldoMovimiento::select('user_id')->distinct()->pluck('user_id')->all()
            : array_keys($saldoProyectado);

        $lineasC = [];
        $totalC = 0.0;

        foreach ($conSaldo as $uid) {
            $disponible = $aplicar
                ? $servicio->saldo($uid)
                : ($saldoProyectado[$uid] ?? 0);

            if ($disponible <= 0) {
                continue;
            }

            $usuario = \App\Models\User::withTrashed()->find($uid);
            $yaTiene = \App\Models\Detallepago::where('user_id', $uid)->pluck('pago_id')->all();

            foreach ($mensuales as $mes) {
                if (in_array($mes->id, $yaTiene, true)) {
                    continue;
                }

                $disponible = $aplicar
                    ? $servicio->saldo($uid)
                    : $disponible;

                // Solo si el saldo alcanza para cubrirlo completo.
                if ($disponible + 0.001 < (float) $mes->cantidad) {
                    continue;
                }

                $totalC += (float) $mes->cantidad;

                // En la simulación hay que ir descontando a mano lo que se
                // consumiría, o un saldo chico parecería cubrir todos los meses.
                if (! $aplicar) {
                    $disponible -= (float) $mes->cantidad;
                }

                if (! $aplicar) {
                    $lineasC[] = sprintf('  %-30s casa %-5s %-28s $%10s  (se crearia y liquidaria)',
                        mb_substr($usuario->nombre ?? '?', 0, 28), $usuario->casa ?? '?',
                        mb_substr($mes->concepto, 0, 26), number_format($mes->cantidad, 2));

                    continue;
                }

                $nuevo = \App\Models\Detallepago::create([
                    'pago_id' => $mes->id,
                    'user_id' => $uid,
                ]);
                $nuevo->setRelation('pago', $mes);

                $ok = $servicio->aplicarA($nuevo);

                $lineasC[] = sprintf('  %-30s casa %-5s %-28s $%10s  (%s)',
                    mb_substr($usuario->nombre ?? '?', 0, 28), $usuario->casa ?? '?',
                    mb_substr($mes->concepto, 0, 26), number_format($mes->cantidad, 2),
                    $ok ? 'CREADO Y LIQUIDADO' : 'creado, sin saldo suficiente');
            }
        }

        $txt .= $lineasC ? implode("\n", $lineasC)."\n" : "  (ninguna)\n";
        $txt .= sprintf("  %-68s $%10s\n", '   subtotal C ('.count($lineasC).' recibos)', number_format($totalC, 2));

        $txt .= "\n".str_repeat('-', 100)."\n";
        $txt .= sprintf("  %-68s $%10s\n", 'SALDO QUE QUEDA DISPONIBLE DESPUES DE LIQUIDAR', number_format(($totalA + $totalB) - $totalC, 2));

        if (! $aplicar) {
            $txt .= "\n  Los recibos de arriba NO existen todavia. Al confirmar se crean y el\n";
            $txt .= "  saldo los paga automaticamente, sin enviar correos a los vecinos.\n";
        }
    }

    if ($sugerencias->isNotEmpty()) {
        $txt .= "\nCONCEPTOS QUE PARECEN ANTICIPO Y NO ESTAS INCLUYENDO:\n";

        foreach ($sugerencias as $p) {
            $cobrado = \App\Models\Detallepago::where('pago_id', $p->id)->where('estado', 'pagado')->sum('cantidad_pago');
            $txt .= sprintf("  id %-4s %-36s cuota $%10s   cobrado $%10s\n",
                $p->id, mb_substr($p->concepto, 0, 34), number_format($p->cantidad, 2), number_format($cobrado, 2));
        }

        $txt .= "\n  Si ese dinero es adelanto que el vecino aun no consume, agregalo asi:\n";
        $txt .= '     ?anticipos='.$sugerencias->pluck('id')->implode(',')."\n";
        $txt .= "  Revisa antes que no se haya aplicado ya a recibos posteriores.\n";
    }

    $txt .= $aplicar
        ? "\nListo. Los vecinos ya ven su saldo y se aplicara solo a su proxima cuota.\n  No se modifico ningun recibo existente.\n"
        : "\nEsto es solo una VISTA PREVIA. No se guardo nada.\n  Para aplicarlo agrega  &aplicar=CONFIRMAR  a la URL.\n";

    return response('<pre>'.e($txt).'</pre>');
});

/*
 * Logs del sistema.
 *
 * Estas dos rutas estaban SIN autenticación: el menú solo las mostraba al
 * super-administrador, pero la URL respondía a cualquiera en internet, que
 * podía leer los logs (con correos y datos de los vecinos) y hasta borrarlos
 * con /admin/logs/clear. Ahora exigen sesión y rol de super-administrador.
 */
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
    })->name('admin.logs')->middleware(['auth', 'rol:super-administrador']);


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
    })->name('admin.logs.clear')->middleware(['auth', 'rol:super-administrador']);

Route::get('/', function () {
    return Auth::check()
        ? redirect()->route('inicio')
        : view('auth.login');
});

/*
 * Documentos legales.
 *
 * Son públicos a propósito: quien todavía no tiene cuenta —o quien no quiere
 * darla de alta hasta saber qué se hace con sus datos— debe poder leerlos sin
 * iniciar sesión. También permite entregarle la liga directa a un auditor.
 */
Route::view('/aviso-de-privacidad', 'legal.privacidad')->name('legal.privacidad');
Route::view('/terminos-de-uso', 'legal.terminos')->name('legal.terminos');

/*
 * Guías de uso en PDF.
 *
 * Se generan al vuelo en vez de guardarse como archivo suelto: así una guía
 * nunca queda describiendo un módulo que ya cambió. La del vecino es pública
 * para poder compartirla por WhatsApp sin pedir que inicien sesión.
 */
Route::get('/guia-vecinos', [App\Http\Controllers\GuiaController::class, 'vecinos'])
    ->name('guia.vecinos');

Route::get('/guia-mesa-directiva', [App\Http\Controllers\GuiaController::class, 'mesa'])
    ->middleware(['auth', 'rol:administrador,super-administrador'])
    ->name('guia.mesa');

// Registro de la aceptación. Va fuera del grupo 'aviso' por razones obvias:
// es la única acción que alguien que no ha aceptado tiene que poder hacer.
Route::post('/aceptar-aviso', [App\Http\Controllers\AvisoController::class, 'aceptar'])
    ->middleware('auth')->name('legal.aceptar');

// La otra salida: no aceptar y pedir que se retiren los datos personales.
// Va fuera del grupo 'aviso' por lo mismo que la anterior.
Route::post('/desvincular-datos', [App\Http\Controllers\AvisoController::class, 'desvincular'])
    ->middleware('auth')->name('legal.desvincular');

/*
 * Enlace simbólico de storage, con diagnóstico.
 *
 * El caso real que se presentó: `public/storage` existía como CARPETA de
 * verdad, no como enlace. Pasa cada vez que el proyecto se sube por FTP o
 * comprimido, porque ni el zip ni el FTP conservan los enlaces: los copian
 * como directorios con archivos dentro. La versión anterior de esta ruta se
 * limitaba a responder "ya existe" y no resolvía nada.
 *
 *   Diagnóstico : /storage-link/admin123
 *   Arreglar    : /storage-link/admin123?arreglar=CONFIRMAR
 *
 * Al arreglar, primero copia al origen los archivos que sólo estén en esa
 * carpeta (comprobantes que se perderían), luego la renombra —no la borra— y
 * recién entonces crea el enlace.
 */
Route::get('/storage-link/{token}', function ($token) {
    if (! tokenMantenimientoValido($token)) {
        abort(403, 'No autorizado');
    }

    $target = storage_path('app/public');
    $link = public_path('storage');
    $arreglar = request('arreglar') === 'CONFIRMAR';

    $out = [];
    $out[] = 'Origen  : '.$target;
    $out[] = 'Enlace  : '.$link;
    $out[] = '';

    if (! is_dir($target)) {
        $out[] = '❌ No existe la carpeta origen. Créala antes de continuar.';

        return response('<pre>'.e(implode("\n", $out)).'</pre>');
    }

    // Lista de rutas relativas de un directorio.
    $listar = function ($base) {
        if (! is_dir($base)) {
            return [];
        }

        $items = [];

        // FOLLOW_SYMLINKS es necesario para poder contar a través del propio
        // enlace; sin él el recorrido devuelve cero archivos.
        $it = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator(
                $base,
                FilesystemIterator::SKIP_DOTS | FilesystemIterator::FOLLOW_SYMLINKS
            )
        );

        foreach ($it as $f) {
            if ($f->isFile()) {
                $items[] = ltrim(str_replace($base, '', $f->getPathname()), '/\\');
            }
        }

        return $items;
    };

    // No basta con is_link(): en Windows los "junction" que crea artisan no
    // lo satisfacen y la ruta los confundía con una carpeta normal. Comparar
    // la ruta resuelta funciona igual en Windows y en el hosting Linux.
    $yaEnlazado = file_exists($link)
        && realpath($link) !== false
        && realpath($link) === realpath($target);

    if ($yaEnlazado || is_link($link)) {
        $out[] = '✅ "storage" ya apunta al origen correcto.';
        $out[] = 'Tipo: '.(is_link($link) ? 'enlace simbólico' : 'enlace de directorio (junction)');
        // Se cuenta el origen y no el enlace: recorrer un enlace da resultados
        // distintos según el sistema, y el contenido es el mismo.
        $out[] = 'Archivos disponibles: '.count($listar($target));
        $out[] = '';
        $out[] = 'No hay nada que hacer.';

        return response('<pre>'.e(implode("\n", $out)).'</pre>');
    }

    if (! file_exists($link)) {
        if (! $arreglar) {
            $out[] = 'ℹ️  No existe el enlace. Se puede crear sin riesgo.';
            $out[] = 'Para crearlo agrega  ?arreglar=CONFIRMAR  a la URL.';

            return response('<pre>'.e(implode("\n", $out)).'</pre>');
        }

        $ok = @symlink($target, $link);
        $out[] = $ok
            ? '✅ Enlace creado correctamente.'
            : '❌ El hosting no permitió crear el enlace (symlink deshabilitado).';

        return response('<pre>'.e(implode("\n", $out)).'</pre>');
    }

    // Existe como carpeta real: el caso problemático.
    $enCarpeta = $listar($link);
    $enOrigen = $listar($target);
    $soloEnCarpeta = array_values(array_diff($enCarpeta, $enOrigen));

    $out[] = '⚠️  "storage" existe como CARPETA REAL, no como enlace.';
    $out[] = 'Por eso artisan storage:link responde "link already exists".';
    $out[] = '';
    $out[] = 'Archivos en esa carpeta      : '.count($enCarpeta);
    $out[] = 'Archivos en el origen        : '.count($enOrigen);
    $out[] = 'Solo en la carpeta (en riesgo): '.count($soloEnCarpeta);

    foreach (array_slice($soloEnCarpeta, 0, 20) as $f) {
        $out[] = '   - '.$f;
    }

    if (count($soloEnCarpeta) > 20) {
        $out[] = '   ... y '.(count($soloEnCarpeta) - 20).' más';
    }

    if (! $arreglar) {
        $out[] = '';
        $out[] = 'VISTA PREVIA: no se ha modificado nada.';
        $out[] = 'Al confirmar se hará, en este orden:';
        $out[] = '  1. Copiar al origen los archivos que solo están en la carpeta.';
        $out[] = '  2. Renombrar la carpeta a "storage_carpeta_vieja" (no se borra).';
        $out[] = '  3. Crear el enlace simbólico.';
        $out[] = '';
        $out[] = 'Para aplicarlo agrega  ?arreglar=CONFIRMAR  a la URL.';

        return response('<pre>'.e(implode("\n", $out)).'</pre>');
    }

    // 1. Rescatar lo que solo vive en la carpeta.
    $copiados = 0;

    foreach ($soloEnCarpeta as $rel) {
        $origen = $link.DIRECTORY_SEPARATOR.$rel;
        $destino = $target.DIRECTORY_SEPARATOR.$rel;

        if (! is_dir(dirname($destino))) {
            @mkdir(dirname($destino), 0755, true);
        }

        if (@copy($origen, $destino)) {
            $copiados++;
        }
    }

    $out[] = '';
    $out[] = '✅ Archivos rescatados: '.$copiados.' de '.count($soloEnCarpeta);

    if ($copiados < count($soloEnCarpeta)) {
        $out[] = '❌ No se pudieron copiar todos. Se detiene aquí para no perder nada.';

        return response('<pre>'.e(implode("\n", $out)).'</pre>');
    }

    // 2. Apartar la carpeta en vez de borrarla.
    $viejo = dirname($link).DIRECTORY_SEPARATOR.'storage_carpeta_vieja';

    if (file_exists($viejo)) {
        $viejo .= '_'.date('YmdHis');
    }

    if (! @rename($link, $viejo)) {
        $out[] = '❌ No se pudo renombrar la carpeta. Revisa permisos.';

        return response('<pre>'.e(implode("\n", $out)).'</pre>');
    }

    $out[] = '✅ Carpeta apartada como: '.$viejo;

    // 3. Crear el enlace.
    if (@symlink($target, $link)) {
        $out[] = '✅ Enlace simbólico creado.';
        $out[] = '';
        $out[] = 'Revisa que las imágenes se vean en la plataforma y, cuando confirmes';
        $out[] = 'que todo está bien, puedes borrar "'.basename($viejo).'" por FTP.';
    } else {
        @rename($viejo, $link);
        $out[] = '❌ El hosting no permitió crear el enlace. Se restauró la carpeta original.';
        $out[] = 'Opción: en cPanel, apunta el document root a la carpeta public del proyecto.';
    }

    return response('<pre>'.e(implode("\n", $out)).'</pre>');
});
/*

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


/*
 * Notificaciones diarias (cron de cPanel).
 *
 * No lleva sesión a propósito: la dispara el cron del hosting, no una persona.
 * Pero antes tampoco llevaba NINGUNA protección, así que cualquiera en
 * internet podía ejecutarla y provocar un envío de correos a todos los
 * vecinos cuantas veces quisiera. Ahora exige un token en la ruta.
 *
 * El token sale de CRON_TOKEN en el .env; si no está definido usa el mismo de
 * las rutas de mantenimiento, para no dejar la ruta inservible tras desplegar.
 */
Route::get('/cronjob/notificaciones-diarias/{token}', function ($token, CronjobController $controller) {
    if (! hash_equals((string) env('CRON_TOKEN', 'admin123'), (string) $token)) {
        abort(403, 'No autorizado');
    }

    /*
     * El cron de cPanel lo llama con ?json=1 y recibe el JSON de siempre.
     * Abierto en el navegador devuelve un resumen legible: un JSON con ceros
     * no dice si el sistema falló o si hoy no había nada que mandar.
     */
    return $controller->enviarNotificacionesDiarias(
        comoJson: request()->boolean('json') || request()->expectsJson()
    );
})
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

// El middleware 'aviso' bloquea toda operación de escritura mientras el
// usuario no haya aceptado el aviso de privacidad. Consultar sigue abierto:
// tiene que poder leer el documento antes de decidir.
Route::middleware(['auth', 'aviso'])->group(function () {
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
            // Consulta: abierta a toda la mesa directiva. Que el dinero lo
            // maneje el tesorero no significa esconderle los números al resto.
            Route::get('/nuevo-pago', 'nuevoPago')->name('nuevoPago');
            Route::get('/obtener-pago', 'obtenerPagos')->name('obtenerPagos');
            Route::get('/exportar-consolidado', 'exportarConsolidado')->name('exportarConsolidado');
            Route::get('/exportar-concepto/{id}', 'exportarConcepto')->name('exportarConcepto');
        });

        // Reporte mensual de ingresos y egresos, en el formato que la mesa
        // directiva entrega cada mes. Consultarlo y descargarlo queda abierto
        // a toda la mesa; capturar el cierre de caja es de tesorería.
        Route::controller(App\Http\Controllers\Administrador\ReporteMensualController::class)
            ->prefix('reporte-mensual')->name('reporteMensual.')->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/pdf', 'pdf')->name('pdf');
                Route::post('/cierre', 'guardarCierre')->middleware('tesoreria')->name('cierre');
            });

        // Estado de cuenta por vivienda.
        //
        // Consultarlo lo puede toda la mesa. La constancia de no adeudo es un
        // documento que sale del condominio hacia terceros (notarios,
        // compradores, inquilinos), así que la extiende tesorería.
        Route::controller(App\Http\Controllers\Administrador\EstadoCuentaController::class)
            ->prefix('estado-cuenta')->name('estadoCuenta.')->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/ver/{id}', 'ver')->name('ver');
                Route::get('/pdf/{id}', 'pdf')->name('pdf');
                Route::get('/constancia/{id}', 'constancia')
                    ->middleware('tesoreria')->name('constancia');
            });

        // Servicios y pagos recurrentes de la tesorería.
        //
        // Toda la mesa puede consultar el catálogo y exportarlo: es el acta de
        // entrega de la tesorería. Darlos de alta, editarlos y registrar sus
        // pagos es de quien maneja el dinero.
        Route::controller(App\Http\Controllers\Administrador\ServicioController::class)
            ->prefix('servicio')->name('servicio.')->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/obtener-servicios', 'obtenerServicios')->name('obtenerServicios');
                Route::get('/historial/{id}', 'historial')->name('historial');
                Route::get('/exportar', 'exportar')->name('exportar');

                Route::middleware('tesoreria')->group(function () {
                    Route::post('/crear-servicio', 'crearServicio')->name('crearServicio');
                    Route::post('/actualizar-servicio/{id}', 'actualizarServicio')->name('actualizarServicio');
                    Route::post('/registrar-pago/{id}', 'registrarPago')->name('registrarPago');
                    Route::delete('/eliminar-servicio/{id}', 'eliminarServicio')->name('eliminarServicio');
                });
            });

        // Firma digitalizada de quien valida los pagos. Es un dato personal
        // del tesorero y sale impresa en recibos, así que nadie más la sube.
        Route::controller(App\Http\Controllers\Administrador\FirmaController::class)
            ->prefix('firma')->name('firma.')->middleware('tesoreria')->group(function () {
                Route::get('/', 'index')->name('index');
                Route::post('/guardar', 'guardar')->name('guardar');
                Route::delete('/eliminar', 'eliminar')->name('eliminar');
            });

        Route::controller(PagoController::class)->prefix('pago')->name('pago.')->group(function () {
            Route::get('/detalle-pagos/{id}', 'DetallePagos')->name('DetallePagos');

            // Recibo para imprimir y firmar a mano. Sale SIN firma a propósito.
            Route::get('/recibo/{id}', 'reciboPdf')->name('reciboPdf');
            Route::get('/obtenerdetalle-pagos', 'obtenerDetallepagos')->name('obtenerDetallepagos');

            // Movimientos de dinero: solo el Tesorero (y el super-administrador).
            // Mientras no haya tesorero nombrado, sigue abierto a toda la mesa.
            Route::middleware('tesoreria')->group(function () {
                Route::post('/crear-pago', 'crearPago')->name('crearPago');
                Route::delete('/eliminar-pago/{id}', 'eliminarPago')->name('eliminarPago');
                Route::delete('/eliminar-mainpago/{id}', 'eliminarMainPago')->name('eliminarMainPago');
                Route::post('/actualizar-pago/{id}', 'actualizarPago')->name('actualizarPago');
                Route::post('/agregar-usuario', 'agregarUsuario')->name('agregarUsuario');
            });
        });
        // Documentos
        Route::controller(DocumentoController::class)->prefix('documento')->name('documento.')->group(function () {
            Route::get('/nuevo-documento', 'nuevoDocumento')->name('nuevoDocumento');
            Route::post('/crear-documento', 'crearDocumento')->name('crearDocumento');
            Route::get('/obtener-documentos', 'obtenerDocumentos')->name('obtenerDocumentos');
            Route::delete('/eliminar-documento/{id}', 'eliminarDocumento')->name('eliminarDocumento');
            Route::post('/actualizar-documento/{id}', 'actualizarDocumento')->name('actualizarDocumento');
            Route::get('/resumen-gastos', 'resumenGastos')->name('resumenGastos');

            // Completar la fecha bancaria de un gasto es una corrección
            // contable: queda del lado de tesorería.
            Route::post('/actualizar-gasto/{id}', 'actualizarGasto')
                ->middleware('tesoreria')->name('actualizarGasto');
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
            // Eliminar una cuenta y repartir roles son decisiones de quien
            // administra el sistema, no de la mesa directiva en turno. El
            // botón de eliminar ya solo se le mostraba al super-administrador;
            // esto lo hace valer también en el backend. Sin ello, cualquier
            // administrador podía nombrar administrador a otro vecino.
            Route::middleware('rol:super-administrador')->group(function () {
                Route::get('/eliminar-usuario/{id}', 'eliminarUsuario')->name('eliminarUsuario');
                Route::get('/administrador-usuario/{id}', 'administradorUsuario')->name('administradorUsuario');
                Route::get('/usuario-usuario/{id}', 'usuarioUsuario')->name('usuarioUsuario');
                Route::post('/asignar-cargo/{id}', 'asignarCargo')->name('asignarCargo');
            });
            Route::post('/enviar-verificacion/{id}', 'enviarVerificacion')->name('enviarVerificacion');
            Route::post('/regresar-pago/{id}', 'regresarPagoDueno')->name('regresarPagoDueno');
        });
        // Vehículos
        /*
         * MODULO DE VEHICULOS RETIRADO.
         *
         * Guardaba placas y fotografias visibles para TODOS los vecinos, y
         * eso es exposicion innecesaria: el condominio puede operar sin ese
         * dato. Las rutas se quitan para que no queden accesibles ni por URL
         * directa.
         *
         * Los registros y las fotos se eliminan con /purgar-vehiculos.
         */
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
        // Historial de correos: contiene la correspondencia enviada a todos
        // los vecinos. El menú solo se lo mostraba al super-administrador,
        // pero cualquier administrador podía entrar escribiendo la URL.
        Route::controller(CorreoController::class)->prefix('correos')->name('correos.')
            ->middleware('rol:super-administrador')->group(function () {
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

            // Aparecer o no en el directorio vecinal.
            Route::post('/directorio', 'actualizarDirectorio')->name('actualizarDirectorio');
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

            // Su propio estado de cuenta. No recibe id: siempre es el del
            // vecino que inició sesión, para que nadie consulte el de otro.
            Route::get('/estado-cuenta', 'estadoCuenta')->name('estadoCuenta');
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

            // Desglose de un mes: lo que se abre al pulsar una barra.
            Route::get('/egresos-mes', 'egresosDelMes')->name('egresosMes');
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
