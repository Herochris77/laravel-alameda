<?php

/*
 * Mapa del panel de la mesa directiva (/administrador/inicio).
 *
 * Las opciones se agrupan por LO QUE QUIERE HACER la persona, no por módulo
 * técnico. Todas las que existían antes siguen aquí; solo cambia dónde viven.
 *
 * 'solo': 'pagos' = quien puede mover dinero; 'super' = super-administrador.
 * 'buscar': palabras con las que el buscador del panel la encuentra.
 * Solo datos: este archivo entra en la caché de configuración de producción.
 */
return [
    'frecuentes' => [
        ['ruta' => 'admin.comunicado.nuevoComunicado', 'titulo' => 'Publicar un aviso', 'desc' => 'Comunicado para los vecinos', 'icono' => 'bullhorn'],
        ['ruta' => 'admin.pago.nuevoPago', 'titulo' => 'Crear un cobro', 'desc' => 'Cuota o concepto de pago', 'icono' => 'money bill alternate outline'],
        ['ruta' => 'admin.estadoCuenta.index', 'titulo' => 'Quién debe', 'desc' => 'Estado de cuenta por casa', 'icono' => 'file invoice dollar'],
        ['ruta' => 'admin.dashboard', 'titulo' => 'Cómo va todo', 'desc' => 'Cobranza y participación', 'icono' => 'chart bar'],
    ],

    'grupos' => [
        [
            'clave' => 'dinero',
            'titulo' => 'Dinero y cobranza',
            'icono' => 'money bill alternate outline',
            'items' => [
                ['ruta' => 'admin.pago.nuevoPago', 'titulo' => 'Nuevo pago', 'desc' => 'Genera recibos y conceptos de pago.', 'icono' => 'plus circle', 'buscar' => 'pago pagos cobro cuota recibo concepto'],
                ['ruta' => 'admin.estadoCuenta.index', 'titulo' => 'Estado de cuenta', 'desc' => 'Quién debe por vivienda, con PDF y constancia de no adeudo.', 'icono' => 'file invoice dollar', 'buscar' => 'estado cuenta adeudo deuda constancia morosos'],
                ['ruta' => 'admin.servicio.index', 'titulo' => 'Servicios', 'desc' => 'Pagos recurrentes con su referencia y aviso antes de vencer.', 'icono' => 'redo alternate', 'buscar' => 'servicios recurrentes luz agua referencia'],
                ['ruta' => 'admin.reporteMensual.index', 'titulo' => 'Reporte mensual', 'desc' => 'Ingresos y egresos del mes en PDF, para entregar a la mesa directiva provisional.', 'icono' => 'file pdf outline', 'buscar' => 'reporte mensual ingresos egresos pdf'],
                ['ruta' => 'admin.documento.nuevoDocumento', 'titulo' => 'Documentos y gastos', 'desc' => 'Sube documentos, reglamentos y archivos relevantes.', 'icono' => 'file alternate outline', 'buscar' => 'documentos gastos reglamento archivos facturas'],
                ['ruta' => 'admin.firma.index', 'titulo' => 'Mi firma', 'desc' => 'Firma que llevan los recibos que descargan los vecinos.', 'icono' => 'signature', 'buscar' => 'firma recibo tesoreria', 'solo' => 'pagos'],
            ],
        ],
        [
            'clave' => 'vecinos',
            'titulo' => 'Vecinos y casas',
            'icono' => 'users',
            'items' => [
                ['ruta' => 'admin.usuarios.listaUsuarios', 'titulo' => 'Usuarios', 'desc' => 'Administra cuentas, perfiles y vecinos.', 'icono' => 'users', 'buscar' => 'usuarios vecinos cuentas bloquear perfiles'],
                ['ruta' => 'admin.expediente.index', 'titulo' => 'Expediente por casa', 'desc' => 'Toda la información de cada casa en un solo lugar.', 'icono' => 'folder open outline', 'buscar' => 'expediente casa vivienda ficha'],
                ['ruta' => 'admin.solicitudes.index', 'titulo' => 'Solicitudes de permiso', 'desc' => 'Revisa solicitudes de permiso de inquilinos.', 'icono' => 'key', 'buscar' => 'solicitudes permisos inquilinos'],
                ['ruta' => 'admin.sancion.nuevaSancion', 'titulo' => 'Sanciones', 'desc' => 'Registra multas e infracciones.', 'icono' => 'exclamation triangle', 'buscar' => 'sanciones multas infracciones'],
            ],
        ],
        [
            'clave' => 'comunicacion',
            'titulo' => 'Avisos y comunicación',
            'icono' => 'bullhorn',
            'items' => [
                ['ruta' => 'admin.comunicado.nuevoComunicado', 'titulo' => 'Comunicados', 'desc' => 'Publica avisos importantes para los vecinos.', 'icono' => 'bullhorn', 'buscar' => 'comunicado aviso publicar noticias'],
                ['ruta' => 'admin.correos.index', 'titulo' => 'Correos enviados', 'desc' => 'Historial de todos los correos enviados por el sistema.', 'icono' => 'envelope outline', 'buscar' => 'correos email enviados historial', 'solo' => 'super'],
                ['ruta' => 'admin.contacto.nuevoContacto', 'titulo' => 'Contactos', 'desc' => 'Gestiona contactos de emergencia.', 'icono' => 'phone', 'buscar' => 'contactos telefonos emergencia'],
                ['ruta' => 'admin.encuesta.index', 'titulo' => 'Encuestas', 'desc' => 'Crea encuestas para consultar a los vecinos.', 'icono' => 'poll', 'buscar' => 'encuestas votar consulta'],
            ],
        ],
        [
            'clave' => 'reuniones',
            'titulo' => 'Reuniones y obras',
            'icono' => 'gavel',
            'items' => [
                ['ruta' => 'admin.asamblea.index', 'titulo' => 'Asambleas', 'desc' => 'Convoca asambleas, registra asistencia y toma votaciones.', 'icono' => 'gavel', 'buscar' => 'asambleas asamblea convocatoria asistencia votacion actas'],
                ['ruta' => 'admin.proyectos.index', 'titulo' => 'Proyectos', 'desc' => 'Publica proyectos con costo y registra su avance.', 'icono' => 'tools', 'buscar' => 'proyectos obras mejoras avance costo'],
                ['ruta' => 'admin.dashboard', 'titulo' => 'Panel del comité', 'desc' => 'Indicadores de cobranza, morosidad y participación.', 'icono' => 'chart bar', 'buscar' => 'panel comite dashboard indicadores morosidad participacion'],
            ],
        ],
        [
            'clave' => 'sistema',
            'titulo' => 'Sistema',
            'icono' => 'cogs',
            'solo' => 'super',
            'items' => [
                ['tipo' => 'logs', 'url' => '/admin/logs', 'titulo' => 'Logs', 'desc' => 'Revisa logs del sistema.', 'icono' => 'bug', 'buscar' => 'logs errores sistema bitacora'],
                ['tipo' => 'cron', 'titulo' => 'Cronjob', 'desc' => 'Ejecuta notificaciones diarias (pagos, reservas, estacionamientos).', 'icono' => 'clock', 'buscar' => 'cron cronjob notificaciones diarias recordatorios'],
            ],
        ],
    ],
];
