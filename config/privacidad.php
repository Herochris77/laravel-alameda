<?php

/*
 * Datos del responsable del tratamiento de datos personales.
 *
 * Viven aquí y no dentro de las vistas porque son los únicos valores del
 * aviso de privacidad que cambian con la mesa directiva. Se pueden editar
 * en este archivo o sobrescribir desde el .env sin tocar código.
 *
 * REVISAR ANTES DE PUBLICAR: el correo y el teléfono de contacto son el
 * canal por el que los vecinos ejercen sus derechos ARCO. Si no son
 * correctos, el aviso no cumple su función.
 */

return [

    'responsable' => env('PRIVACIDAD_RESPONSABLE', 'Mesa Directiva del Condominio Alameda'),

    'domicilio' => env('PRIVACIDAD_DOMICILIO', 'Av. Los Arados No. 1, Fracc. Hacienda del Bosque, Los Ángeles, Querétaro, C.P. 76902'),

    // Canal oficial para solicitudes de acceso, rectificación, cancelación
    // y oposición. Debe ser una cuenta que la mesa directiva revise.
    'correo' => env('PRIVACIDAD_CORREO', 'contacto@alameda-condominio.com.mx'),

    'telefono' => env('PRIVACIDAD_TELEFONO', ''),

    // Fecha de la última modificación del aviso. Se muestra al pie y es lo
    // que permite a un vecino saber si cambió desde la última vez que lo leyó.
    'ultima_actualizacion' => env('PRIVACIDAD_ACTUALIZADO', '2026-09-17'),

    'plataforma' => 'Plataforma del Condominio Alameda (alameda-condominio.com.mx)',

    /*
     * Alto de la firma en el recibo, en puntos.
     *
     * Es ajustable porque las firmas varían muchísimo de proporción: una
     * apaisada se ve bien a 60, una casi cuadrada necesita más para no
     * quedar diminuta. El ancho no se fija: se deja libre para que la
     * imagen nunca se deforme.
     */
    'firma_alto' => (int) env('FIRMA_ALTO', 80),

    /*
     * Versión del aviso que se pide aceptar.
     *
     * Va aparte de 'ultima_actualizacion' a propósito: corregir una coma no
     * debería obligar a 45 vecinos a volver a aceptar. Esto se sube A MANO y
     * solo cuando el cambio sea de fondo —finalidades nuevas, datos nuevos,
     * un módulo que recabe otra cosa—. Al subirlo, a todos les vuelve a
     * aparecer la ventana.
     */
    'version_aviso' => env('PRIVACIDAD_VERSION', '1.0'),

];
