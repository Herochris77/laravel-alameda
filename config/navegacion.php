<?php

/*
 * Mapa de navegación del sistema: UNA sola fuente para el menú superior, la
 * barra inferior del móvil y las zonas de la pantalla de inicio.
 *
 * Cuatro zonas, pensadas como las áreas de un condominio:
 *   Mi casa · Comunidad · Servicios · Mesa directiva.
 *
 * Solo datos (nada de funciones): este archivo se guarda en la caché de
 * configuración de producción.
 *
 * 'solo' limita quién ve el elemento: 'inquilino' o 'admin'.
 * 'buscar' son las palabras con las que el buscador de inicio lo encuentra.
 */
return [
    'zonas' => [
        [
            'clave' => 'casa',
            'titulo' => 'Mi casa',
            'icono' => 'home',
            'items' => [
                ['titulo' => 'Pagos', 'desc' => 'Tus recibos, comprobantes y estado de cuenta', 'ruta' => 'usuario.pago.index', 'activa' => 'usuario.pago.*', 'icono' => 'money bill alternate outline', 'buscar' => 'pagos pago cuotas mantenimiento dinero recibos comprobante'],
                ['titulo' => 'Mi perfil', 'desc' => 'Tus datos, foto y privacidad', 'ruta' => 'usuario.perfil.index', 'activa' => 'usuario.perfil.*', 'icono' => 'user outline', 'buscar' => 'perfil mi perfil cuenta usuario datos directorio contraseña'],
                ['titulo' => 'Mascotas', 'desc' => 'Registra a tus mascotas', 'ruta' => 'usuario.mascota.index', 'activa' => 'usuario.mascota.*', 'icono' => 'paw', 'buscar' => 'mascotas mascota perro gato animal'],
                ['titulo' => 'Sanciones', 'desc' => 'Multas y su estado', 'ruta' => 'usuario.sancion.index', 'activa' => 'usuario.sancion.*', 'icono' => 'exclamation triangle', 'buscar' => 'sanciones sancion multa multas'],
                ['titulo' => 'Solicitar permisos', 'desc' => 'Pide autorización a tu propietario', 'ruta' => 'usuario.solicitudes.index', 'activa' => 'usuario.solicitudes.*', 'icono' => 'key', 'buscar' => 'solicitar permisos solicitudes permiso inquilino', 'solo' => 'inquilino'],
            ],
        ],
        [
            'clave' => 'comunidad',
            'titulo' => 'Comunidad',
            'icono' => 'users',
            'items' => [
                ['titulo' => 'Comunicados', 'desc' => 'Avisos de la mesa directiva', 'ruta' => 'usuario.comunicados.index', 'activa' => 'usuario.comunicados.*', 'icono' => 'bullhorn', 'buscar' => 'comunicados avisos noticias mesa'],
                ['titulo' => 'Asambleas', 'desc' => 'Convocatorias, votos y actas', 'ruta' => 'usuario.asamblea.index', 'activa' => 'usuario.asamblea.*', 'icono' => 'gavel', 'buscar' => 'asambleas asamblea votar voto acuerdos'],
                ['titulo' => 'Encuestas', 'desc' => 'Participa en las decisiones', 'ruta' => 'usuario.encuesta.index', 'activa' => 'usuario.encuesta.*', 'icono' => 'poll', 'buscar' => 'encuestas encuesta votar opinion'],
                ['titulo' => 'Proyectos', 'desc' => 'Obras y mejoras del condominio', 'ruta' => 'usuario.proyectos.index', 'activa' => 'usuario.proyectos.*', 'icono' => 'tools', 'buscar' => 'proyectos obras mejoras avances'],
                ['titulo' => 'Vecinos', 'desc' => 'Directorio de la comunidad', 'ruta' => 'usuario.vecino.index', 'activa' => 'usuario.vecino.*', 'icono' => 'users', 'buscar' => 'vecinos directorio comunidad telefono'],
                ['titulo' => 'Contactos', 'desc' => 'Teléfonos útiles', 'ruta' => 'usuario.contacto.index', 'activa' => 'usuario.contacto.*', 'icono' => 'phone', 'buscar' => 'contactos telefonos emergencia'],
                ['titulo' => 'Reportes', 'desc' => 'Ingresos y egresos, con transparencia', 'ruta' => 'usuario.reportes.index', 'activa' => 'usuario.reportes.*', 'icono' => 'chart bar outline', 'buscar' => 'reportes ingresos egresos gastos transparencia'],
            ],
        ],
        [
            'clave' => 'servicios',
            'titulo' => 'Servicios',
            'icono' => 'key',
            'items' => [
                ['titulo' => 'Reservar', 'desc' => 'Áreas comunes', 'ruta' => 'usuario.reserva.index', 'activa' => 'usuario.reserva.index', 'icono' => 'calendar plus outline', 'buscar' => 'reservar reserva salon areas comunes'],
                ['titulo' => 'Mis reservaciones', 'desc' => 'Lo que ya reservaste', 'ruta' => 'usuario.reserva.misReservas', 'activa' => 'usuario.reserva.misReservas', 'icono' => 'calendar check outline', 'buscar' => 'reservaciones mis reservas'],
                ['titulo' => 'Estacionamiento', 'desc' => 'Cajones y escalera comunitaria', 'ruta' => 'usuario.estacionamiento.index', 'activa' => 'usuario.estacionamiento.*', 'icono' => 'parking', 'buscar' => 'estacionamiento cajon escalera auto'],
                ['titulo' => 'Documentos', 'desc' => 'Reglamento, actas y comprobantes', 'ruta' => 'usuario.documentos.index', 'activa' => 'usuario.documentos.*', 'icono' => 'file alternate outline', 'buscar' => 'documentos reglamento actas archivos'],
            ],
        ],
        [
            'clave' => 'mesa',
            'titulo' => 'Mesa directiva',
            'icono' => 'shield alternate',
            'solo' => 'admin',
            'items' => [
                ['titulo' => 'Administración', 'desc' => 'Panel de la mesa directiva', 'ruta' => 'admin.inicio.index', 'activa' => 'admin.*', 'icono' => 'shield alternate', 'buscar' => 'administracion administrador configuracion mesa', 'solo' => 'admin'],
            ],
        ],
    ],
];
