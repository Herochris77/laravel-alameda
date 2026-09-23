{{--
    Guía de la mesa directiva provisional.

    Pensada para entregarse junto con la tesorería: quien llega nuevo debería
    poder operar el sistema leyendo esto, sin tener que preguntarle a quien
    sale. Por eso incluye los porqués, no solo los pasos.
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

    <div class="portada-titulo">Guía de la<br>Mesa Directiva Provisional</div>
    <div class="portada-sub">Operación de la plataforma y entrega de la tesorería</div>

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
        <td class="membrete-doc">Guía de la Mesa Directiva Provisional</td>
    </tr>
</table>

<div class="indice">
    <div class="indice-titulo">Contenido</div>
    <table>
        <tr><td class="num">1</td><td>Quién puede hacer qué</td></tr>
        <tr><td class="num">2</td><td>Publicar un recibo de cobro</td></tr>
        <tr><td class="num">3</td><td>Validar y rechazar comprobantes</td></tr>
        <tr><td class="num">4</td><td>Cobros en efectivo</td></tr>
        <tr><td class="num">5</td><td>Tu firma y los dos recibos</td></tr>
        <tr><td class="num">6</td><td>Saldo a favor</td></tr>
        <tr><td class="num">7</td><td>Estado de cuenta y constancia de no adeudo</td></tr>
        <tr><td class="num">8</td><td>Registrar gastos</td></tr>
        <tr><td class="num">9</td><td>Reporte mensual y cierre de caja</td></tr>
        <tr><td class="num">10</td><td>Servicios y pagos recurrentes</td></tr>
        <tr><td class="num">11</td><td>Reportes en Excel</td></tr>
        <tr><td class="num">12</td><td>Comunicados, documentos y encuestas</td></tr>
        <tr><td class="num">13</td><td>Asambleas</td></tr>
        <tr><td class="num">14</td><td>Multas y expediente de la vivienda</td></tr>
        <tr><td class="num">15</td><td>Usuarios y cargos</td></tr>
        <tr><td class="num">16</td><td>Correos: cuidar la cuota</td></tr>
        <tr><td class="num">17</td><td>Mantenimiento del sistema</td></tr>
        <tr><td class="num">18</td><td>Al entregar la tesorería</td></tr>
        <tr><td class="num">19</td><td>Datos personales: lo que la mesa debe cuidar</td></tr>
    </table>
</div>

{{-- ================= 1 ================= --}}
<h2><span class="h2-num">1</span> Quién puede hacer qué</h2>

<p>El sistema distingue el <strong>rol</strong> del <strong>cargo</strong>:</p>

<table class="tabla">
    <tr>
        <th width="26%">Nivel</th>
        <th>Alcance</th>
    </tr>
    <tr>
        <td><strong>Vecino</strong></td>
        <td>Consulta y gestiona lo suyo: sus pagos, sus mascotas, sus reservaciones.</td>
    </tr>
    <tr>
        <td><strong>Mesa directiva provisional</strong></td>
        <td>Entra a todos los módulos administrativos: comunicados, documentos,
            asambleas, encuestas, sanciones, proyectos, usuarios, reportes.</td>
    </tr>
    <tr>
        <td><strong>Administrador</strong></td>
        <td>Además, mantenimiento técnico del sistema.</td>
    </tr>
</table>

<p>
    Dentro de la mesa, el <strong>cargo</strong> decide quién mueve dinero. Se asigna en
    <strong>Usuarios</strong>: Tesorero, Presidente, Secretario o Vocal.
</p>

<div class="ojo">
    <strong>Todo lo que toca dinero es exclusivo del Tesorero:</strong> publicar recibos,
    validar comprobantes, eliminarlos, ajustar saldos, capturar el cierre de caja,
    registrar pagos de servicios y extender constancias de no adeudo.<br><br>
    El resto de la mesa <strong>sí puede consultarlo todo</strong>. La idea no es esconder
    información, es que quede claro quién decide sobre el dinero.
</div>

<div class="nota">
    Mientras <strong>no haya nadie con el cargo de Tesorero</strong>, el módulo de pagos
    queda abierto a toda la mesa, como antes. La restricción empieza en el momento en que
    asignas el cargo. Avisa a tus compañeros el día que lo hagas.
</div>

{{-- ================= 2 ================= --}}
<h2><span class="h2-num">2</span> Publicar un recibo de cobro</h2>

<p>
    En <strong>Pagos → Nuevo pago</strong>. Un "concepto" genera un recibo para cada
    vecino que selecciones.
</p>

<table class="tabla">
    <tr><th width="30%">Campo</th><th>Qué considerar</th></tr>
    <tr>
        <td>Concepto</td>
        <td>El nombre que verá el vecino. Sé específico: "Cuota de mantenimiento octubre
            2026" en vez de "Cuota".</td>
    </tr>
    <tr>
        <td>Cantidad</td>
        <td>El monto por vivienda.</td>
    </tr>
    <tr>
        <td>Vencimiento</td>
        <td>Fecha límite. Es contra esta fecha que se mide la mora.</td>
    </tr>
    <tr>
        <td>Recargo por pago tardío</td>
        <td>Porcentaje. <strong>Déjalo vacío</strong> si ese concepto no lleva recargo.</td>
    </tr>
    <tr>
        <td>Cubrir con saldo a favor</td>
        <td><strong>Marcado</strong> para la cuota de mantenimiento.
            <strong>Desmarcado</strong> para derramas, proyectos y gastos
            extraordinarios.</td>
    </tr>
    <tr>
        <td>Usuarios</td>
        <td>Todos, o solo algunos.</td>
    </tr>
</table>

<div class="ojo">
    <strong>La casilla de saldo a favor importa.</strong> Lo que un vecino adelantó fue
    <em>su cuota</em>. Si dejas la casilla marcada en una derrama, se la cobras con ese
    adelanto y en la práctica se la cobras dos veces. Los conceptos que no usan saldo se
    marcan con una etiqueta ámbar en la lista.
</div>

<p>
    Al publicar, a cada vecino le llega notificación y correo. A quien tenga saldo
    suficiente, el recibo se le liquida solo y recibe un aviso distinto.
</p>

{{-- ================= 3 ================= --}}
<h2><span class="h2-num">3</span> Validar y rechazar comprobantes</h2>

<p>
    Abre el concepto desde <strong>Pagos</strong> y verás una tarjeta por vivienda, con su
    comprobante. Los filtros de arriba te dejan ver solo los que tienen comprobante, los
    que pagaron tarde, o los que faltan.
</p>

<table class="pasos">
    <tr>
        <td class="paso-num"><div class="paso-bolita">1</div></td>
        <td class="paso-texto">Abre el comprobante y compáralo con tu estado de cuenta.</td>
    </tr>
    <tr>
        <td class="paso-num"><div class="paso-bolita">2</div></td>
        <td class="paso-texto">
            Pulsa <span class="boton claro">Editar</span> y elige el estado.
        </td>
    </tr>
    <tr>
        <td class="paso-num"><div class="paso-bolita">3</div></td>
        <td class="paso-texto">
            Verifica la <strong>fecha real del pago</strong>. Es la que decide si hay
            recargo, así que corrígela si el vecino se equivocó.
        </td>
    </tr>
    <tr>
        <td class="paso-num"><div class="paso-bolita">4</div></td>
        <td class="paso-texto">Guarda. El vecino recibe aviso de inmediato.</td>
    </tr>
</table>

<div class="ojo">
    <strong>Al rechazar, el motivo es obligatorio.</strong> Se le manda por correo y por
    notificación, y lo ve en pantalla al volver a subir. Sé concreto: "la transferencia es
    del 25 y el vencimiento fue el 18, falta el recargo" le sirve; "comprobante incorrecto"
    no le dice nada y te va a costar tres mensajes más.
</div>

<div class="tip">
    Un recibo rechazado <strong>no se vuelve a crear</strong>: el vecino sube el
    comprobante corregido sobre el mismo. Al aprobarlo, el motivo del rechazo se limpia
    solo.
</div>

{{-- ================= 4 ================= --}}
<h2><span class="h2-num">4</span> Cobros en efectivo</h2>

<p>
    Cuando un vecino te paga en mano no hay comprobante que subir. El flujo es:
</p>

<table class="pasos">
    <tr>
        <td class="paso-num"><div class="paso-bolita">1</div></td>
        <td class="paso-texto">Abre el recibo de esa vivienda y pulsa Editar.</td>
    </tr>
    <tr>
        <td class="paso-num"><div class="paso-bolita">2</div></td>
        <td class="paso-texto">
            Estado <strong>Pagado</strong>, y en Forma de pago elige
            <strong>Efectivo</strong>.
        </td>
    </tr>
    <tr>
        <td class="paso-num"><div class="paso-bolita">3</div></td>
        <td class="paso-texto">
            Al guardar se genera un <strong>folio</strong> tipo <strong>EF-2026-0001</strong>,
            consecutivo y reiniciado cada año, como un talonario.
        </td>
    </tr>
    <tr>
        <td class="paso-num"><div class="paso-bolita">4</div></td>
        <td class="paso-texto">
            Pulsa <span class="boton claro">Recibo</span> en la tarjeta, imprímelo,
            <strong>fírmalo</strong> y entrégaselo.
        </td>
    </tr>
</table>

<div class="ojo">
    Ese papel firmado <strong>es el único respaldo</strong> del vecino: no hay
    transferencia que lo demuestre. No omitas la entrega, y conserva tú una copia o anota
    el folio.
</div>

<div class="nota">
    El efectivo que recibas y aún no deposites va en el campo <strong>"Efectivo en caja"</strong>
    del cierre mensual. Si no manejas efectivo, ese campo se deja vacío.
</div>

{{-- ================= 5 ================= --}}
<h2><span class="h2-num">5</span> Tu firma y los dos recibos</h2>

<p>
    El sistema genera <strong>el mismo recibo en dos versiones</strong>:
</p>

<table class="tabla">
    <tr><th width="34%">Versión</th><th>Cuándo se usa</th></tr>
    <tr>
        <td><strong>El del vecino</strong><br>
            <span class="etq ve">con tu firma</span></td>
        <td>Lo descarga él desde su pantalla de Pagos. Lleva tu firma digitalizada.</td>
    </tr>
    <tr>
        <td><strong>El de tesorería</strong><br>
            <span class="etq gr">línea en blanco</span></td>
        <td>Botón "Recibo" en la tarjeta. Sale sin firma para que la pongas de puño.
            Es el que entregas en mano.</td>
    </tr>
</table>

<h3>Cargar tu firma</h3>
<p>
    Menú de administrador → <strong>Mi Firma</strong>. Solo aparece si tienes el cargo de
    Tesorero.
</p>

<ul>
    <li>Firma en una hoja blanca con tinta negra y tómale foto de frente.</li>
    <li>Recorta justo alrededor del trazo.</li>
    <li>Súbela en <strong>JPG</strong>. Si intentas un PNG que el servidor no pueda
        procesar, el sistema te avisa al guardar y no toca tu firma anterior.</li>
</ul>

<div class="tip">
    El recibo lleva la firma de <strong>quien validó ese pago</strong>, no la del tesorero
    en turno. Cuando entregues el cargo, los recibos viejos seguirán mostrando tu firma y
    los nuevos la de quien entre. No hay que hacer nada para eso.
</div>

{{-- ================= 6 ================= --}}
<h2><span class="h2-num">6</span> Saldo a favor</h2>

<p>
    Cuando un vecino paga de más, el excedente queda como saldo a su favor. No es un
    número guardado: se calcula sumando movimientos, así que siempre se puede rastrear de
    dónde salió cada peso.
</p>

<ul>
    <li>Se aplica <strong>solo</strong> a conceptos marcados como "cubrir con saldo".</li>
    <li>Se aplica <strong>completo o nada</strong>: si el saldo no alcanza para la cuota
        entera, el recibo queda pendiente y el saldo intacto. No se hacen abonos
        parciales, porque un recibo a medias no lo sabe representar el resto del
        sistema.</li>
    <li>Cuando se aplica, al vecino le llega aviso con su saldo restante.</li>
</ul>

<div class="ojo">
    <strong>Al leer el reporte, no confundas el fondo con lo disponible.</strong> El saldo
    a favor de los vecinos es dinero que está en la cuenta pero <em>ya está comprometido</em>:
    corresponde a cuotas futuras que ya pagaron. No alcanza para proyectos.
</div>

{{-- ================= 7 ================= --}}
<h2><span class="h2-num">7</span> Estado de cuenta y constancia de no adeudo</h2>

<p>
    Menú → <strong>Estado de Cuenta</strong>. Una tabla con todas las viviendas: cuánto
    debe cada una, cuánto saldo a favor tiene y el neto. Se puede filtrar por deudores.
</p>

<p>De cada vivienda salen dos PDF:</p>

<ul>
    <li><strong>Estado de cuenta:</strong> todos sus recibos con fecha, cuota, recargo y
        estado; sus multas; sus movimientos de saldo; y el resumen. Es la respuesta a
        cualquier aclaración.</li>
    <li><strong>Constancia de no adeudo:</strong> documento formal con firma de Tesorería
        y Presidencia, para cuando alguien vende o renta.</li>
</ul>

<div class="nota">
    El sistema <strong>solo extiende la constancia si la vivienda está realmente al
    corriente</strong>. Si debe, se niega y te dice cuánto. Esa protección es deliberada:
    firmar una constancia falsa por descuido es justo lo que no debe poder pasar.
</div>

{{-- ================= 8 ================= --}}
<h2><span class="h2-num">8</span> Registrar gastos</h2>

<p>
    Los gastos se cargan en <strong>Documentos</strong>, eligiendo el tipo
    <strong>"Comprobante de Gasto"</strong>. Además del archivo y el monto, se pide:
</p>

<ul>
    <li><strong>Fecha del movimiento bancario</strong> (obligatoria): el día en que el
        dinero salió de la cuenta, <em>no</em> el día en que subes el PDF.</li>
    <li>Categoría, proveedor y forma de pago.</li>
</ul>

<div class="ojo">
    <strong>Por qué importa la fecha.</strong> Es lo que hace que el reporte mensual cuadre
    contra tu estado de cuenta. Si pagas el jardín el 28 y subes el comprobante el 3 del
    mes siguiente, el gasto debe contar en el mes en que salió el dinero.
</div>

<p>
    Los gastos cargados antes de que existiera este campo salen en el reporte con la
    etiqueta <span class="etq am">fecha aproximada</span>. Se corrigen con el botón azul
    junto a cada gasto: solo cambia la fecha, el proveedor y la forma de pago; el monto, la
    categoría y el archivo no se tocan.
</p>

{{-- ================= 9 ================= --}}
<h2><span class="h2-num">9</span> Reporte mensual y cierre de caja</h2>

<p>
    Menú → <strong>Reporte Mensual</strong>. Reproduce el documento que la mesa entrega
    cada mes: Saldo Inicial → Ingresos → Egresos → Saldo Final, con anexos y tres firmas.
</p>

<table class="pasos">
    <tr>
        <td class="paso-num"><div class="paso-bolita">1</div></td>
        <td class="paso-texto">Elige el periodo.</td>
    </tr>
    <tr>
        <td class="paso-num"><div class="paso-bolita">2</div></td>
        <td class="paso-texto">Revisa ingresos y egresos: salen solos del sistema.</td>
    </tr>
    <tr>
        <td class="paso-num"><div class="paso-bolita">3</div></td>
        <td class="paso-texto">
            Captura el <strong>cierre</strong>: saldo en cuenta y efectivo, tal como los
            reporta tu estado de cuenta.
        </td>
    </tr>
    <tr>
        <td class="paso-num"><div class="paso-bolita">4</div></td>
        <td class="paso-texto">Descarga el PDF y súbelo como documento.</td>
    </tr>
</table>

<div class="tip">
    <strong>El saldo inicial no se captura:</strong> se arrastra del cierre del mes
    anterior. Solo capturas el cierre, porque eso es lo único que el sistema no puede
    saber.
</div>

<p>
    Mientras capturas, la pantalla compara lo que escribes contra lo que resulta de los
    movimientos registrados y te avisa si difiere, diciendo si parece faltar un cobro o un
    gasto. <strong>Una diferencia no es un error</strong>: normalmente es algo que aún no
    se captura. Pero la ves antes de firmar el reporte, no después.
</p>

<p>
    Las firmas del pie salen de los <strong>cargos</strong> de la mesa. Si un cargo no está
    asignado, el renglón sale en blanco para firmarse a mano.
</p>

{{-- ================= 10 ================= --}}
<h2><span class="h2-num">10</span> Servicios y pagos recurrentes</h2>

<p>
    Menú → <strong>Servicios</strong>. El catálogo de obligaciones fijas del condominio:
    agua, basura, internet, jardinería.
</p>

<p>
    De cada uno se guarda la <strong>referencia de pago</strong>, el proveedor, el monto
    estimado, la periodicidad, el próximo vencimiento, la forma de pago, el contacto y
    notas.
</p>

<div class="tip">
    <strong>El cronjob te avisa antes de cada vencimiento</strong>, por correo, en el
    centro de notificaciones y al celular. Cada servicio define con cuántos días de
    anticipación. Lo vencido se recuerda todos los días hasta que registres el pago.
</div>

<p>
    Al pagar, registras el <strong>monto real</strong> y el folio. El vencimiento avanza
    solo al siguiente periodo y el aviso se apaga. El historial guarda lo que de verdad se
    pagó cada vez, para contrastarlo con el estimado.
</p>

<p>
    El botón <span class="boton claro">Descargar catálogo</span> saca todo en Excel. Ese es
    el documento que le dejas a quien reciba la tesorería.
</p>

{{-- ================= 11 ================= --}}
<h2><span class="h2-num">11</span> Reportes en Excel</h2>

<p>Hay dos, con el mismo formato para poder pegarlos en una misma hoja:</p>

<ul>
    <li><strong>Consolidado</strong> de todos los conceptos, desde Pagos.</li>
    <li><strong>De un solo concepto</strong>, desde la pantalla de ese concepto. Útil para
        el corte mes a mes.</li>
</ul>

<p>Ambos cierran con un bloque de totales que responde cuánto dinero debió entrar:</p>

<table class="tabla">
    <tr><td width="45%"><strong>Esperado</strong></td><td>Cuotas más recargos: lo que se debió cobrar.</td></tr>
    <tr><td><strong>Entró a la cuenta</strong></td><td>Dinero real recibido.</td></tr>
    <tr><td><strong>Cubierto con saldo</strong></td><td>No entró dinero: el vecino ya lo había adelantado.</td></tr>
    <tr><td><strong>Pendiente de cobro</strong></td><td>Lo que falta.</td></tr>
</table>

{{-- ================= 12 ================= --}}
<h2><span class="h2-num">12</span> Comunicados, documentos y encuestas</h2>

<ul>
    <li><strong>Comunicados:</strong> avisos a todos los vecinos. Salen por correo y como
        notificación.</li>
    <li><strong>Documentos:</strong> reglamento, actas, reportes. Los tipo "General" son
        para consulta; los "Comprobante de Gasto" alimentan el reporte mensual.</li>
    <li><strong>Encuestas:</strong> para consultar un tema sin convocar asamblea. Se
        publican, se vota desde el celular y queda el historial.</li>
    <li><strong>Contactos:</strong> el directorio de teléfonos útiles que ven los
        vecinos.</li>
    <li><strong>Proyectos:</strong> obras y derramas, con su costo y sus avances con
        fotografías. Los vecinos siguen el progreso.</li>
</ul>

{{-- ================= 13 ================= --}}
<h2><span class="h2-num">13</span> Asambleas</h2>

<p>
    Se convoca con fecha, hora, lugar y quórum requerido; se captura el orden del día
    punto por punto; se registra asistencia y cartas poder; y se levanta el acta en PDF con
    los resultados.
</p>

<div class="nota">
    El quórum se calcula solo, contando presentes y representados. El acta sale con el
    resultado de cada punto.
</div>

{{-- ================= 14 ================= --}}
<h2><span class="h2-num">14</span> Multas y expediente de la vivienda</h2>

<p>
    En <strong>Sanciones</strong> se levanta una multa con motivo, monto y fotografía de la
    evidencia. El vecino la ve y sube su comprobante de pago.
</p>

<p>
    El <strong>Expediente</strong> de cada vivienda concentra su historial y permite dejar
    notas administrativas, útiles para dar seguimiento a un caso entre una mesa y la
    siguiente.
</p>

{{-- ================= 15 ================= --}}
<h2><span class="h2-num">15</span> Usuarios y cargos</h2>

<p>En <strong>Usuarios</strong> se administra el padrón:</p>

<ul>
    <li>Alta de vecinos y reenvío del correo de verificación.</li>
    <li>Cambiar a alguien entre vecino y mesa directiva provisional.</li>
    <li><strong>Asignar cargo:</strong> Tesorero, Presidente, Secretario, Vocal.</li>
    <li>Bloquear y desbloquear el acceso.</li>
    <li>Marcar si una vivienda entra o no en los cobros.</li>
</ul>

<div class="ojo">
    Al asignar el cargo de <strong>Tesorero</strong>, el resto de la mesa pierde
    inmediatamente la capacidad de registrar y validar pagos. Es el comportamiento
    correcto, pero avísales antes para que no se lleven la sorpresa.
</div>

{{-- ================= 16 ================= --}}
<h2><span class="h2-num">16</span> Correos: cuidar la cuota</h2>

<p>
    El plan de correo del hosting tiene un límite diario. El sistema está hecho para
    cuidarlo: un aviso que va a los 42 vecinos sale como <strong>un solo mensaje</strong>
    con copia oculta, no 42.
</p>

<p>
    En <strong>Correos Enviados</strong> hay un tablero con los mensajes de hoy, del mes,
    el día más alto de los últimos 14 y los fallidos.
</p>

<div class="ojo">
    Lo que se cuenta son <strong>mensajes, no destinatarios</strong>, porque así los cuenta
    el proveedor. Si ves envíos fallidos, lo primero a revisar es el límite del plan.
</div>

{{-- ================= 17 ================= --}}
<h2><span class="h2-num">17</span> Mantenimiento del sistema</h2>

<p>Reservado al administrador del sistema:</p>

<table class="tabla">
    <tr><th width="34%">Acción</th><th>Cuándo</th></tr>
    <tr>
        <td><code>/migrar/TOKEN</code></td>
        <td>Después de subir una actualización que cambie la base. Es idempotente:
            correrlo dos veces no hace daño.</td>
    </tr>
    <tr>
        <td><code>/limpiar-cache/TOKEN</code></td>
        <td>Siempre después de migrar, y ante cualquier comportamiento raro tras una
            actualización.</td>
    </tr>
    <tr>
        <td>Cron diario</td>
        <td>Manda los recordatorios de vencimiento, reservaciones, estacionamiento y
            servicios. Si se desconfigura, las notificaciones dejan de salir
            <strong>en silencio</strong>.</td>
    </tr>
</table>

<div class="ojo">
    Los tokens de esas rutas son lo único que las protege: no piden sesión. Quien los
    tenga puede migrar la base o disparar correos a todo el condominio. Guárdalos como una
    contraseña y cámbialos al entregar el cargo.
</div>

{{-- ================= 18 ================= --}}
<h2><span class="h2-num">18</span> Al entregar la tesorería</h2>

<p>Lista de lo que conviene dejar listo:</p>

<table class="pasos">
    <tr>
        <td class="paso-num"><div class="paso-bolita">1</div></td>
        <td class="paso-texto">
            <strong>Captura el cierre del último mes</strong> con el saldo real de tu
            estado de cuenta. Ese será el punto de partida de quien entre.
        </td>
    </tr>
    <tr>
        <td class="paso-num"><div class="paso-bolita">2</div></td>
        <td class="paso-texto">
            <strong>Descarga el catálogo de servicios</strong> en Excel: referencias,
            montos y vencimientos. Es lo que evita que el siguiente ande preguntando cómo
            se paga el agua.
        </td>
    </tr>
    <tr>
        <td class="paso-num"><div class="paso-bolita">3</div></td>
        <td class="paso-texto">
            <strong>Descarga el consolidado de cobranza</strong> y el reporte del último
            mes.
        </td>
    </tr>
    <tr>
        <td class="paso-num"><div class="paso-bolita">4</div></td>
        <td class="paso-texto">
            <strong>Revisa que no queden comprobantes sin validar</strong> ni gastos sin
            su fecha bancaria.
        </td>
    </tr>
    <tr>
        <td class="paso-num"><div class="paso-bolita">5</div></td>
        <td class="paso-texto">
            <strong>Transfiere el saldo</strong> y entrega el comprobante. Quien entra lo
            captura como cierre del mes de la entrega.
        </td>
    </tr>
    <tr>
        <td class="paso-num"><div class="paso-bolita">6</div></td>
        <td class="paso-texto">
            <strong>Reasigna el cargo</strong> de Tesorero en Usuarios, y que el nuevo
            suba su firma en Mi Firma.
        </td>
    </tr>
    <tr>
        <td class="paso-num"><div class="paso-bolita">7</div></td>
        <td class="paso-texto">
            <strong>Cambia los tokens</strong> de mantenimiento y del cron.
        </td>
    </tr>
</table>

<div class="tip">
    <strong>Elimina tu firma</strong> de Mi Firma al entregar. Los recibos que validaste
    seguirán mostrándola porque quedó incrustada en su momento; lo que evitas es que se
    use en recibos nuevos que tú ya no validaste.
</div>

{{-- ================= 19 ================= --}}
<h2><span class="h2-num">19</span> Datos personales: lo que la mesa debe cuidar</h2>

<p>
    La plataforma guarda datos personales de los vecinos. Quien administra esos datos
    responde por ellos, y esa responsabilidad no se transfiere con un texto ni con una
    firma. Lo que sí se puede hacer es manejarlos bien. Estas son las reglas de la casa:
</p>

<table class="tabla">
    <tr>
        <th width="34%">Regla</th>
        <th>Por qué</th>
    </tr>
    <tr>
        <td>Solo se pide lo que se usa</td>
        <td>Si un dato no sirve para cobrar, avisar o convivir, no se pide. Por eso se
            retiró el módulo de vehículos: las placas no hacían falta para administrar
            el condominio.</td>
    </tr>
    <tr>
        <td>La lista de deudores no se publica</td>
        <td>Vive en el panel de la mesa y en ningún otro lado. El módulo de
            transparencia muestra montos y conceptos, nunca nombres.</td>
    </tr>
    <tr>
        <td>Nada de capturas en el grupo</td>
        <td>Una captura del panel con nombres y adeudos, mandada a un grupo de
            WhatsApp, es una difusión de datos personales. No importa que el grupo sea
            del condominio.</td>
    </tr>
    <tr>
        <td>El directorio es voluntario</td>
        <td>Cada vecino decide si aparece. Si alguien se salió, no se le vuelve a
            activar «para que se pueda contactar»: es su decisión.</td>
    </tr>
    <tr>
        <td>La desvinculación no se discute</td>
        <td>Si un vecino pide retirar sus datos, el sistema lo hace solo y al momento.
            La mesa no autoriza ni condiciona esa salida, ni siquiera si el vecino debe
            dinero: el adeudo se conserva ligado a la casa.</td>
    </tr>
    <tr>
        <td>Los accesos se dan de baja</td>
        <td>Cuando alguien deja la mesa, se le quita el rol el mismo día y se borra su
            firma. Un ex integrante con acceso administrativo es el escenario que hay
            que evitar.</td>
    </tr>
</table>

<div class="ojo">
    <strong>Usar los datos de la plataforma para un asunto personal es la línea que no se
    cruza.</strong> Los datos se te confían por el cargo, no son tuyos. Sacar el nombre
    completo, el teléfono o el domicilio de un vecino para un pleito, un trámite o una
    demanda ajena al condominio es un uso indebido y la responsabilidad recae en quien
    lo hizo.
</div>

<div class="tip">
    Ante una solicitud de datos de un tercero —una autoridad, un abogado, una
    aseguradora— no se entrega nada por iniciativa propia. Se pide el requerimiento por
    escrito y se consulta antes de responder.
</div>

<div class="cierre">
    Condominio Alameda · Guía de la Mesa Directiva Provisional ·
    {{ $generado->translatedFormat('F \d\e Y') }}<br>
    Dudas sobre la plataforma: <strong>{{ config('privacidad.correo') }}</strong>
</div>

@include('pdf.pie', ['fecha' => $generado, 'fijo' => false])

</body>
</html>
