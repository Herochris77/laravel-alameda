{{--
    Guía del vecino.

    Cada cosa que aquí se describe corresponde a una pantalla que existe. Si se
    agrega o se quita un módulo, esta guía se actualiza: una guía que promete
    lo que el sistema no hace genera más llamadas que la que no existe.
--}}
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    @include('guias.estilos')
</head>
<body>

{{-- ================= PORTADA ================= --}}
<div class="portada">
    <div class="portada-logo">@include('pdf.logo', ['alto' => 110])</div>

    <div class="portada-condominio">CONDOMINIO ALAMEDA</div>
    <div class="portada-linea"></div>

    <div class="portada-titulo">Guía del Vecino</div>
    <div class="portada-sub">Todo lo que puedes hacer en la plataforma</div>

    <div class="portada-pie">
        alameda-condominio.com.mx<br>
        Av. Los Arados No. 1, Fracc. Hacienda del Bosque · Los Ángeles, Qro. C.P. 76902<br><br>
        Documento generado el {{ $generado->translatedFormat('j \d\e F \d\e Y') }}
    </div>
</div>

{{-- ================= ÍNDICE ================= --}}
<div class="salto"></div>

<table class="membrete">
    <tr>
        <td>@include('pdf.logo', ['alto' => 26])</td>
        <td class="membrete-nombre">CONDOMINIO ALAMEDA</td>
        <td class="membrete-doc">Guía del Vecino</td>
    </tr>
</table>

<div class="indice">
    <div class="indice-titulo">Contenido</div>
    <table>
        <tr><td class="num">1</td><td>Entrar a la plataforma</td></tr>
        <tr><td class="num">2</td><td>Mis pagos: la pantalla más usada</td></tr>
        <tr><td class="num">3</td><td>Subir tu comprobante, paso a paso</td></tr>
        <tr><td class="num">4</td><td>Recargo por pago tardío: cómo se calcula</td></tr>
        <tr><td class="num">5</td><td>Saldo a favor</td></tr>
        <tr><td class="num">6</td><td>Tus recibos y tu estado de cuenta</td></tr>
        <tr><td class="num">7</td><td>Multas</td></tr>
        <tr><td class="num">8</td><td>Reservar áreas comunes</td></tr>
        <tr><td class="num">9</td><td>Estacionamiento</td></tr>
        <tr><td class="num">10</td><td>Vehículos y mascotas</td></tr>
        <tr><td class="num">11</td><td>Comunicados y documentos</td></tr>
        <tr><td class="num">12</td><td>Encuestas y asambleas</td></tr>
        <tr><td class="num">13</td><td>Transparencia: a dónde va el dinero</td></tr>
        <tr><td class="num">14</td><td>Directorio vecinal</td></tr>
        <tr><td class="num">15</td><td>Si rentas o si eres inquilino</td></tr>
        <tr><td class="num">16</td><td>Tu perfil y tus notificaciones</td></tr>
        <tr><td class="num">17</td><td>Dudas frecuentes</td></tr>
    </table>
</div>

{{-- ================= 1 ================= --}}
<h2><span class="h2-num">1</span> Entrar a la plataforma</h2>

<p>
    Entra a <strong>alameda-condominio.com.mx</strong> desde el celular o la
    computadora. No hay que instalar nada.
</p>

<table class="pasos">
    <tr>
        <td class="paso-num"><div class="paso-bolita">1</div></td>
        <td class="paso-texto">Escribe tu <strong>correo electrónico</strong> y tu contraseña.</td>
    </tr>
    <tr>
        <td class="paso-num"><div class="paso-bolita">2</div></td>
        <td class="paso-texto">
            Marca <strong>"Recordar sesión"</strong> si es tu equipo personal. Así no
            tendrás que escribir la contraseña cada vez.
        </td>
    </tr>
    <tr>
        <td class="paso-num"><div class="paso-bolita">3</div></td>
        <td class="paso-texto">
            ¿Olvidaste la contraseña? Usa <strong>"¿Olvidaste tu contraseña?"</strong> y
            te llegará un correo para cambiarla.
        </td>
    </tr>
</table>

@include('guias.captura', ['archivo' => 'login.jpg', 'pie' => 'Pantalla de acceso'])

<div class="nota">
    <strong>La primera vez</strong> te aparecerá una ventana pidiéndote aceptar el
    Aviso de Privacidad y los Términos de Uso. Tómate un minuto para leerlos: explican
    qué datos tuyos se guardan y quién puede verlos. Los tienes siempre a la mano al
    pie de cualquier pantalla.
</div>

{{-- ================= 2 ================= --}}
<h2><span class="h2-num">2</span> Mis pagos: la pantalla más usada</h2>

<p>
    Aquí vives el día a día. Tus recibos se agrupan en cuatro pestañas:
</p>

<div class="maqueta no-romper">
    <div class="maqueta-titulo">Así se ven tus recibos</div>

    <div class="tarjeta pendiente">
        <table width="100%"><tr>
            <td>
                <div class="tarjeta-concepto">Cuota de mantenimiento octubre</div>
                <div class="tarjeta-meta">Vence el 18 de octubre · <span class="etq am">Pendiente</span></div>
            </td>
            <td width="80" class="tarjeta-monto">$1,300.00</td>
        </tr></table>
    </div>

    <div class="tarjeta vencido">
        <table width="100%"><tr>
            <td>
                <div class="tarjeta-concepto">Derrama portón peatonal</div>
                <div class="tarjeta-meta">Venció el 30 de septiembre · <span class="etq ro">Vencido</span></div>
            </td>
            <td width="80" class="tarjeta-monto">$427.00</td>
        </tr></table>
    </div>

    <div class="tarjeta pagado">
        <table width="100%"><tr>
            <td>
                <div class="tarjeta-concepto">Cuota de mantenimiento septiembre</div>
                <div class="tarjeta-meta">Pagado el 14 de septiembre · <span class="etq ve">Aprobado</span></div>
            </td>
            <td width="80" class="tarjeta-monto">$1,300.00</td>
        </tr></table>
    </div>
</div>

<table class="tabla">
    <tr>
        <th width="22%">Pestaña</th>
        <th>Qué contiene</th>
    </tr>
    <tr>
        <td><span class="etq am">Pendientes</span></td>
        <td>Lo que todavía no vence, y lo que ya subiste y está esperando revisión
            de Tesorería.</td>
    </tr>
    <tr>
        <td><span class="etq ro">Vencidos</span></td>
        <td>Pasó la fecha límite y no has subido comprobante. Súbelo cuanto antes:
            algunos conceptos cobran recargo.</td>
    </tr>
    <tr>
        <td><span class="etq ve">Aprobados</span></td>
        <td>Tesorería ya validó tu pago. Desde aquí descargas tu recibo en PDF.</td>
    </tr>
    <tr>
        <td><span class="etq gr">Rechazados</span></td>
        <td>Algo no cuadró con tu comprobante. <strong>Trae el motivo escrito</strong>:
            corrige y vuelve a subirlo.</td>
    </tr>
</table>

@include('guias.captura', ['archivo' => 'mis-pagos.jpg', 'pie' => 'Pantalla "Mis pagos"'])

{{-- ================= 3 ================= --}}
<h2><span class="h2-num">3</span> Subir tu comprobante, paso a paso</h2>

<table class="pasos">
    <tr>
        <td class="paso-num"><div class="paso-bolita">1</div></td>
        <td class="paso-texto">Haz la transferencia por tu banco, como siempre.</td>
    </tr>
    <tr>
        <td class="paso-num"><div class="paso-bolita">2</div></td>
        <td class="paso-texto">
            Entra a <strong>Pagos</strong> y abre el recibo que vas a cubrir.
        </td>
    </tr>
    <tr>
        <td class="paso-num"><div class="paso-bolita">3</div></td>
        <td class="paso-texto">
            Sube la <strong>imagen del comprobante</strong> y, muy importante, captura la
            <strong>fecha real en que hiciste la transferencia</strong>.
        </td>
    </tr>
    <tr>
        <td class="paso-num"><div class="paso-bolita">4</div></td>
        <td class="paso-texto">
            Listo. Tesorería lo revisa y te llega aviso cuando quede aprobado.
        </td>
    </tr>
</table>

<div class="tip">
    <strong>La fecha es a tu favor.</strong> Si transferiste el día 17 y subes el
    comprobante el 20, el sistema toma el <strong>17</strong>. No te cobra recargo por
    haber tardado en subirlo.
</div>

<div class="ojo">
    <strong>Tapa lo que no haga falta.</strong> Para validar tu pago solo se necesita el
    monto, la fecha, a quién se pagó y el folio de la operación. No hace falta tu número
    de cuenta completo ni tu saldo: cúbrelos o recorta la imagen antes de subirla.
</div>

<p>
    ¿La imagen pesa más de 2 MB? En la misma pantalla hay un botón
    <span class="boton claro">Comprimir imagen</span> que te lleva a una herramienta para
    reducirla.
</p>

{{-- ================= 4 ================= --}}
<h2><span class="h2-num">4</span> Recargo por pago tardío: cómo se calcula</h2>

<p>
    No todos los conceptos llevan recargo; solo aquellos donde la asamblea lo aprobó.
    Cuando aplica, funciona así:
</p>

<ul>
    <li>Se mide contra la <strong>fecha en que hiciste la transferencia</strong>, no
        contra el día en que subiste el comprobante ni el día en que Tesorería revisó.</li>
    <li>Si transferiste a tiempo, <strong>no hay recargo</strong>, aunque el comprobante
        se revise días después.</li>
    <li>El recibo te muestra el desglose: cuota, recargo y total.</li>
</ul>

<div class="nota">
    Si crees que un recargo no te corresponde, escribe a Tesorería antes de pagar.
    Se revisa la fecha de tu transferencia y se corrige si hace falta.
</div>

{{-- ================= 5 ================= --}}
<h2><span class="h2-num">5</span> Saldo a favor</h2>

<p>
    Si pagas de más, o si pagas cuotas por adelantado, la diferencia
    <strong>no se pierde</strong>: queda registrada como tu saldo a favor.
</p>

<ul>
    <li>Lo ves en tu pantalla de Pagos, con el detalle de cómo se formó.</li>
    <li>Cuando se publica una <strong>cuota de mantenimiento</strong> y tu saldo alcanza
        para cubrirla completa, el recibo se liquida solo y te llega el aviso. No tienes
        que hacer nada.</li>
    <li>El saldo <strong>no</strong> se usa para derramas ni gastos extraordinarios, salvo
        que la mesa lo indique. Lo que adelantaste fue tu cuota.</li>
</ul>

<div class="maqueta no-romper">
    <div class="maqueta-titulo">Aviso que recibes cuando se aplica</div>
    <div class="tarjeta pagado">
        <div class="tarjeta-concepto">Recibo cubierto con tu saldo a favor</div>
        <div class="tarjeta-meta">
            Se cargó el recibo <strong>Cuota de mantenimiento octubre</strong> por $1,300.00
            y se liquidó automáticamente con tu saldo. No tienes que hacer nada.<br>
            Saldo restante: <strong>$650.00</strong>
        </div>
    </div>
</div>

{{-- ================= 6 ================= --}}
<h2><span class="h2-num">6</span> Tus recibos y tu estado de cuenta</h2>

<h3>Recibo de un pago</h3>
<p>
    En la pestaña <span class="etq ve">Aprobados</span>, cada pago tiene su botón para
    descargar el <strong>recibo en PDF</strong>, con el membrete del condominio y la
    firma de quien validó el pago. Guárdalo.
</p>

<h3>Estado de cuenta completo</h3>
<p>
    En la parte superior de Pagos hay un botón
    <span class="boton">Mi estado de cuenta</span>. Descarga un PDF con
    <strong>todo tu historial</strong>: cada recibo con su fecha, cuota, recargo y estado;
    tus multas; los movimientos de tu saldo a favor; y al final el resumen que dice si
    debes algo o tienes saldo.
</p>

<div class="tip">
    Ese documento es la respuesta a cualquier "¿yo ya pagué?". Antes de escribir a
    Tesorería, descárgalo: casi siempre ahí está la respuesta.
</div>

{{-- ================= 7 ================= --}}
<h2><span class="h2-num">7</span> Multas</h2>

<p>
    Si el reglamento interno derivó en una multa, la ves en la sección
    <strong>Sanciones</strong> con su motivo y monto. Ahí mismo subes tu comprobante de
    pago, igual que con las cuotas.
</p>

{{-- ================= 8 ================= --}}
<h2><span class="h2-num">8</span> Reservar áreas comunes</h2>

<table class="pasos">
    <tr>
        <td class="paso-num"><div class="paso-bolita">1</div></td>
        <td class="paso-texto">Entra a <strong>Reservaciones</strong> y elige el espacio.</td>
    </tr>
    <tr>
        <td class="paso-num"><div class="paso-bolita">2</div></td>
        <td class="paso-texto">Selecciona fecha y horario. Verás lo que ya está apartado.</td>
    </tr>
    <tr>
        <td class="paso-num"><div class="paso-bolita">3</div></td>
        <td class="paso-texto">
            Confirma. En <strong>Mis reservas</strong> puedes editarla o cancelarla.
        </td>
    </tr>
</table>

<p>
    El día anterior a tu reservación te llega un recordatorio por correo y notificación.
</p>

{{-- ================= 9 ================= --}}
<h2><span class="h2-num">9</span> Estacionamiento</h2>

<p>
    La sección de <strong>Estacionamiento</strong> muestra la disponibilidad de los
    cajones de visita y de la escalera comunitaria. Puedes ocupar un lugar y
    <strong>liberarlo cuando termines</strong>.
</p>

<div class="ojo">
    Si dejas un cajón ocupado más de un día, el sistema te manda un recordatorio.
    Libéralo en cuanto puedas: son pocos y los usamos todos.
</div>

{{-- ================= 10 ================= --}}
<h2><span class="h2-num">10</span> Vehículos y mascotas</h2>

<h3>Vehículos</h3>
<p>
    Registra marca, modelo, año, color y placas. Sirve para identificar vehículos dentro
    del condominio y para que la vigilancia sepa cuáles son de casa.
</p>

<div class="nota">
    <strong>Ten presente:</strong> el listado de vehículos lo pueden ver todos los vecinos
    registrados. Es a propósito, para poder identificar un coche cuando hace falta.
</div>

<h3>Mascotas</h3>
<p>
    Registra nombre, especie, edad, características, esquema de vacunas y si es amistosa.
    Ayuda muchísimo cuando una mascota se sale: con la foto y la descripción aparece
    mucho más rápido.
</p>

{{-- ================= 11 ================= --}}
<h2><span class="h2-num">11</span> Comunicados y documentos</h2>

<ul>
    <li><strong>Comunicados:</strong> los avisos de la mesa directiva. Te llegan también
        por correo y como notificación.</li>
    <li><strong>Documentos:</strong> reglamento interno, actas, reportes mensuales y
        cualquier archivo que la mesa publique. Están siempre disponibles para
        descargar.</li>
</ul>

{{-- ================= 12 ================= --}}
<h2><span class="h2-num">12</span> Encuestas y asambleas</h2>

<h3>Encuestas</h3>
<p>
    Cuando la mesa quiere tomarle el pulso a un tema, publica una encuesta. Votas desde tu
    celular y puedes ver el historial de las anteriores.
</p>

<h3>Asambleas</h3>
<p>
    Consultas la convocatoria y el orden del día, registras tu asistencia y
    <strong>votas cada punto</strong>.
</p>

<p>
    Si no puedes asistir, puedes <strong>delegar tu voto</strong> en otro condómino
    —una carta poder— y revocarla después si cambias de opinión.
</p>

<div class="ojo">
    <strong>La votación no es secreta.</strong> Tu voto queda asociado a tu número de casa,
    igual que en una votación a mano alzada. Lo decimos claro para que nadie se lleve una
    sorpresa.
</div>

{{-- ================= 13 ================= --}}
<h2><span class="h2-num">13</span> Transparencia: a dónde va el dinero</h2>

<p>
    La sección de <strong>Reportes</strong> está abierta a todos los vecinos. Ahí ves:
</p>

<ul>
    <li>Cuánto se recaudó y cuánto se gastó, mes a mes.</li>
    <li>Los gastos por categoría: agua, jardinería, mantenimiento, seguridad, limpieza.</li>
    <li>El detalle de cada movimiento de egreso, con su concepto y su monto.</li>
    <li>El avance de los proyectos y derramas en curso.</li>
</ul>

<div class="nota">
    Las cifras se muestran <strong>completas, con centavos</strong>, y son las mismas que
    la Tesorería reporta a la asamblea. Lo que <strong>no</strong> se muestra es el detalle
    individual de nadie: quién debe y quién no es información que solo ve la mesa
    directiva.
</div>

{{-- ================= 14 ================= --}}
<h2><span class="h2-num">14</span> Directorio vecinal</h2>

<p>
    Un directorio con el nombre, la casa, el teléfono y la foto de los vecinos registrados,
    para poder contactarse entre nosotros.
</p>

<div class="ojo">
    <strong>Esa información se comparte para la convivencia del condominio y para nada
    más.</strong> No se vale copiarla en listas, difundirla fuera, usarla con fines
    comerciales ni publicar capturas en redes o grupos de mensajería. Si prefieres que tu
    teléfono o tu foto no aparezcan, escríbenos y se retiran.
</div>

{{-- ================= 15 ================= --}}
<h2><span class="h2-num">15</span> Si rentas o si eres inquilino</h2>

<p>
    La plataforma contempla a los inquilinos. Desde <strong>Solicitudes</strong>, el
    inquilino pide autorización al propietario para determinados trámites, y el propietario
    la aprueba o la rechaza desde su propio perfil.
</p>

{{-- ================= 16 ================= --}}
<h2><span class="h2-num">16</span> Tu perfil y tus notificaciones</h2>

<p>En <strong>Perfil</strong> controlas:</p>

<table class="tabla">
    <tr>
        <th width="34%">Opción</th>
        <th>Para qué sirve</th>
    </tr>
    <tr>
        <td>Datos y foto</td>
        <td>Mantén tu correo y celular al día: por ahí te llegan los avisos de
            vencimiento y el resultado de tus pagos.</td>
    </tr>
    <tr>
        <td>Contraseña</td>
        <td>Cámbiala cuando quieras. Nadie de la mesa puede verla.</td>
    </tr>
    <tr>
        <td>Correos</td>
        <td>Puedes apagarlos. Seguirás viendo todo dentro de la plataforma.</td>
    </tr>
    <tr>
        <td>Notificaciones al celular</td>
        <td>Avisos directos en el teléfono. Se activan solo si tú lo autorizas en el
            navegador, y hay un botón para probarlas.</td>
    </tr>
</table>

{{-- ================= 17 ================= --}}
<h2><span class="h2-num">17</span> Dudas frecuentes</h2>

<h3>Subí mi comprobante y sigue apareciendo como pendiente</h3>
<p>
    Es normal. Subirlo no lo aprueba: Tesorería lo revisa y entonces pasa a
    <span class="etq ve">Aprobado</span>. Te llega aviso cuando eso ocurre.
</p>

<h3>Me rechazaron el comprobante</h3>
<p>
    El rechazo <strong>siempre trae el motivo escrito</strong>. Lo ves en la plataforma y
    te llega por correo. Corrige lo que se indica y vuelve a subirlo: no se genera un
    recibo nuevo, es el mismo.
</p>

<h3>Pagué en efectivo a Tesorería</h3>
<p>
    Tesorería lo registra por ti y te entrega un <strong>recibo impreso y firmado</strong>
    con un folio. Ese papel es tu comprobante. En la plataforma verás el pago ya marcado.
</p>

<h3>No me llegan los correos</h3>
<p>
    Revisa la carpeta de spam y que tu correo esté bien escrito en tu perfil. Comprueba
    también que no tengas apagada la opción de correos.
</p>

<h3>¿Quién ve mi información de pagos?</h3>
<p>
    Solo tú y la mesa directiva. Ningún otro vecino puede ver tu estado de cuenta, tus
    recibos ni tus adeudos.
</p>

<h3>Quiero que borren mis datos</h3>
<p>
    Escríbenos al correo del Aviso de Privacidad. Ten en cuenta que los recibos y
    comprobantes ya registrados se conservan, porque respaldan las cuentas del condominio
    ante la asamblea.
</p>

<div class="cierre">
    ¿Algo no funciona o no se entiende? Escríbenos a
    <strong>{{ config('privacidad.correo') }}</strong>.<br>
    Esta plataforma la hicimos entre vecinos: cualquier comentario para mejorarla es
    bienvenido.<br><br>
    Condominio Alameda · Guía del Vecino ·
    {{ $generado->translatedFormat('F \d\e Y') }}
</div>

</body>
</html>
