<?php

/*
 * Tokens de las rutas de mantenimiento y del cron.
 *
 * Van aquí (y no con env() dentro de routes/web.php) porque en producción la
 * configuración está en caché: con la caché activa, env() fuera de config/
 * devuelve null y el token del .env nunca se leería.
 */
return [
    'token' => env('MANTENIMIENTO_TOKEN', ''),
    'cron' => env('CRON_TOKEN', ''),
];