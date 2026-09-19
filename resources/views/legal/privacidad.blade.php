{{--
    Aviso de Privacidad Integral.

    Cada dato que se enumera aquí corresponde a una columna que la plataforma
    realmente guarda. Si se agrega un módulo que recabe información nueva,
    este documento se actualiza: un aviso que no refleja el sistema no sirve
    ni para la auditoría ni para el vecino.
--}}
@extends('legal.layout')

@section('titulo', 'Aviso de Privacidad')

@section('contenido')

    <h1 class="lg-titulo">Aviso de Privacidad Integral</h1>
    <p class="lg-fecha">
        Última actualización:
        {{ \Carbon\Carbon::parse(config('privacidad.ultima_actualizacion'))->translatedFormat('j \d\e F \d\e Y') }}
    </p>

    <div class="lg-indice">
        <div class="lg-indice-titulo">Contenido</div>
        <ol>
            <li><a href="#responsable">Quién es responsable de tus datos</a></li>
            <li><a href="#datos">Qué datos se recaban</a></li>
            <li><a href="#finalidades">Para qué se usan</a></li>
            <li><a href="#quien-ve">Quién puede ver tu información dentro de la plataforma</a></li>
            <li><a href="#transferencias">Con quién se comparte fuera del condominio</a></li>
            <li><a href="#conservacion">Cuánto tiempo se conserva</a></li>
            <li><a href="#seguridad">Cómo se protege</a></li>
            <li><a href="#derechos">Tus derechos ARCO y cómo ejercerlos</a></li>
            <li><a href="#revocacion">Cómo revocar tu consentimiento</a></li>
            <li><a href="#opciones">Cómo limitar el uso de tus datos</a></li>
            <li><a href="#menores">Datos de menores de edad</a></li>
            <li><a href="#cambios">Cambios a este aviso</a></li>
        </ol>
    </div>

    <p>
        Este documento explica qué información personal recaba la
        {{ config('privacidad.plataforma') }}, para qué se usa, quién puede verla
        y qué puedes hacer al respecto. Está redactado para que cualquier vecino
        lo entienda sin necesidad de asesoría legal.
    </p>

    <h2 id="responsable">1. Quién es responsable de tus datos</h2>

    <p>
        La <strong>{{ config('privacidad.responsable') }}</strong>, con domicilio en
        {{ config('privacidad.domicilio') }}, es responsable del uso y protección de tus
        datos personales, en términos de la Ley Federal de Protección de Datos
        Personales en Posesión de los Particulares y su normativa aplicable.
    </p>

    <p>
        Canal de contacto para cualquier asunto relacionado con tus datos:
        <strong>{{ config('privacidad.correo') }}</strong>@if(config('privacidad.telefono')),
        teléfono {{ config('privacidad.telefono') }}@endif.
    </p>

    <h2 id="datos">2. Qué datos se recaban</h2>

    <p>
        Los datos se obtienen de tres formas: los que tú capturas al registrarte o al
        usar la plataforma, los que la mesa directiva registra por su función
        administrativa, y los que el sistema genera solo al operar.
    </p>

    <div class="lg-tabla-wrap">
        <table class="lg-tabla">
            <thead>
                <tr>
                    <th>Categoría</th>
                    <th>Datos concretos</th>
                    <th>Origen</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong>Identificación y contacto</strong></td>
                    <td>Nombre completo, correo electrónico, número de celular, número de
                        casa, tipo de residente (propietario o inquilino) y fotografía de
                        perfil, si decides subirla.</td>
                    <td>Tú, al registrarte o editar tu perfil.</td>
                </tr>
                <tr>
                    <td><strong>Acceso a la cuenta</strong></td>
                    <td>Contraseña (almacenada cifrada, nunca en texto legible) y
                        preferencia de recibir o no correos.</td>
                    <td>Tú.</td>
                </tr>
                <tr>
                    <td><strong>Patrimoniales y financieros</strong></td>
                    <td>Recibos de cuotas y derramas, montos, fechas de vencimiento y de
                        pago, <strong>imágenes de los comprobantes de transferencia que
                        subes</strong>, recargos por mora, saldo a favor y su historial de
                        movimientos, y el motivo cuando un comprobante es rechazado.</td>
                    <td>Tú (el comprobante) y la Tesorería (la validación).</td>
                </tr>
                <tr>
                    <td><strong>Vehículos</strong></td>
                    <td>Marca, modelo, año, color, <strong>placas</strong>, tipo,
                        observaciones y fotografía del vehículo.</td>
                    <td>Tú.</td>
                </tr>
                <tr>
                    <td><strong>Mascotas</strong></td>
                    <td>Nombre, especie, edad, género, características, esquema de vacunas,
                        si está esterilizada, si es amistosa, y fotografía.</td>
                    <td>Tú.</td>
                </tr>
                <tr>
                    <td><strong>Uso de áreas comunes</strong></td>
                    <td>Reservaciones de espacios con fecha y horario, y ocupación de
                        cajones de estacionamiento y de la escalera comunitaria.</td>
                    <td>Tú.</td>
                </tr>
                <tr>
                    <td><strong>Participación vecinal</strong></td>
                    <td>Asistencia a asambleas, cartas poder, <strong>el sentido de tu voto
                        en cada punto</strong> (queda asociado a tu número de casa) y tus
                        respuestas a encuestas (quedan asociadas a tu usuario).</td>
                    <td>Tú y la mesa directiva.</td>
                </tr>
                <tr>
                    <td><strong>Convivencia y sanciones</strong></td>
                    <td>Motivo de la sanción, comentarios, <strong>fotografía de la
                        evidencia</strong>, monto, estado y comprobante de pago.</td>
                    <td>Mesa directiva.</td>
                </tr>
                <tr>
                    <td><strong>Inquilinos</strong></td>
                    <td>Solicitudes de permiso entre inquilino y propietario, con el mensaje
                        y la respuesta.</td>
                    <td>Tú.</td>
                </tr>
                <tr>
                    <td><strong>Expediente de la vivienda</strong></td>
                    <td>Notas administrativas que la mesa directiva registra sobre una casa.</td>
                    <td>Mesa directiva.</td>
                </tr>
                <tr>
                    <td><strong>Técnicos, generados solos</strong></td>
                    <td>Dirección IP y navegador con los que inicias sesión, fecha de última
                        actividad, suscripción de notificaciones al celular, historial de
                        notificaciones y registro de los correos que el sistema envía
                        (destinatarios, asunto y contenido).</td>
                    <td>El sistema.</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="lg-ojo">
        <strong>Datos patrimoniales y financieros.</strong> Los comprobantes de pago
        que subes son datos patrimoniales y requieren tu consentimiento expreso.
        Al subir un comprobante lo estás otorgando. Te pedimos que, antes de
        subirlo, <strong>cubras o recortes cualquier dato que no sea necesario</strong>
        para verificar el pago: basta que se vean el monto, la fecha, el
        destinatario y el folio de la operación. No necesitamos tu número de
        cuenta completo ni tu saldo.
    </div>

    <p>
        <strong>No se recaban datos personales sensibles</strong> en el sentido de la
        ley: no se pide origen racial o étnico, estado de salud, información
        genética o biométrica, creencias religiosas o filosóficas, afiliación
        sindical, opiniones políticas ni preferencia sexual. Tampoco se solicita
        CURP, RFC, INE ni datos bancarios capturados como texto.
    </p>

    <h2 id="finalidades">3. Para qué se usan</h2>

    <h3>Finalidades necesarias</h3>

    <p>Sin estos usos la plataforma no puede prestar el servicio:</p>

    <ul>
        <li>Crear tu cuenta, autenticarte y darte el nivel de acceso que corresponde
            a tu rol.</li>
        <li>Emitir y darte seguimiento a recibos de cuotas de mantenimiento, derramas
            y proyectos.</li>
        <li>Recibir y validar tus comprobantes de pago, calcular recargos por mora y
            administrar tu saldo a favor.</li>
        <li>Generar tu estado de cuenta, tus recibos en PDF y, cuando estés al
            corriente, tu constancia de no adeudo.</li>
        <li>Elaborar los reportes mensuales de ingresos y egresos que la mesa
            directiva rinde a la asamblea.</li>
        <li>Administrar el registro de vehículos, mascotas, estacionamiento y
            reservación de áreas comunes.</li>
        <li>Convocar asambleas, registrar asistencia y contabilizar votaciones.</li>
        <li>Levantar y dar seguimiento a sanciones conforme al reglamento interno.</li>
        <li>Enviarte avisos operativos: recibo nuevo, vencimiento próximo, resultado
            de la validación de tu comprobante, comunicados y convocatorias.</li>
        <li>Atender obligaciones de rendición de cuentas y responder auditorías de la
            propia asamblea.</li>
    </ul>

    <h3>Finalidades adicionales, que puedes rechazar</h3>

    <p>No son necesarias para el servicio y negarte a ellas no afecta tu cuenta:</p>

    <ul>
        <li><strong>Aparecer en el directorio vecinal.</strong> Tu nombre, número de
            casa, celular y fotografía de perfil son visibles para los demás vecinos
            registrados.</li>
        <li><strong>Correos informativos no esenciales</strong>, como la publicación de
            documentos generales.</li>
        <li><strong>Notificaciones al celular</strong> mediante notificaciones push del
            navegador.</li>
    </ul>

    <p>Más adelante, en el punto 10, se explica cómo rechazar cada una.</p>

    <h2 id="quien-ve">4. Quién puede ver tu información dentro de la plataforma</h2>

    <p>
        Esto es lo que más conviene que quede claro, porque es donde más se suele
        suponer de más:
    </p>

    <div class="lg-tabla-wrap">
        <table class="lg-tabla">
            <thead>
                <tr>
                    <th>Información</th>
                    <th>Quién la ve</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Tu nombre, casa, celular y fotografía de perfil</td>
                    <td><strong>Todos los vecinos registrados</strong>, a través del
                        directorio vecinal.</td>
                </tr>
                <tr>
                    <td>Los vehículos registrados, incluidas placas y fotografía</td>
                    <td><strong>Todos los vecinos registrados</strong>. El listado es
                        común porque sirve para identificar vehículos dentro del
                        condominio.</td>
                </tr>
                <tr>
                    <td>Tus recibos, comprobantes, montos, adeudos y saldo a favor</td>
                    <td><strong>Solo tú</strong> y la mesa directiva. Ningún otro vecino
                        tiene acceso a tu estado de cuenta individual.</td>
                </tr>
                <tr>
                    <td>Registrar pagos, validar comprobantes, mover saldos y extender
                        constancias</td>
                    <td><strong>Solo quien tiene el cargo de Tesorería</strong>, además
                        del administrador del sistema. El resto de la mesa puede
                        consultar, no modificar.</td>
                </tr>
                <tr>
                    <td>Tus mascotas</td>
                    <td>Tú y la mesa directiva.</td>
                </tr>
                <tr>
                    <td>Tus sanciones</td>
                    <td>Tú y la mesa directiva.</td>
                </tr>
                <tr>
                    <td>El sentido de tu voto en asamblea</td>
                    <td>La mesa directiva. <strong>La votación no es secreta:</strong> el
                        voto queda ligado a tu número de casa, igual que en una votación
                        a mano alzada.</td>
                </tr>
                <tr>
                    <td>Totales de ingresos y egresos del condominio</td>
                    <td>Todos los vecinos, en el módulo de transparencia. Se muestran
                        cifras agregadas y conceptos de gasto, <strong>no el detalle
                        individual de nadie</strong>.</td>
                </tr>
            </tbody>
        </table>
    </div>

    <h2 id="transferencias">5. Con quién se comparte fuera del condominio</h2>

    <p>
        <strong>Tus datos no se venden, ni se rentan, ni se comparten con fines
        comerciales o publicitarios.</strong> No hay terceros con acceso a la
        información para usarla por su cuenta.
    </p>

    <p>
        Para operar, la plataforma se apoya en dos proveedores que únicamente
        procesan la información por instrucción del responsable, sin poder usarla
        para otros fines:
    </p>

    <ul>
        <li><strong>Servicio de alojamiento web</strong>, donde residen la base de
            datos y los archivos que se suben.</li>
        <li><strong>Servicio de correo electrónico</strong>, por el que salen los avisos
            y comunicados.</li>
    </ul>

    <p>
        Fuera de eso, tus datos solo se entregarían a una autoridad competente que
        los requiriera por escrito y con fundamento legal, o a la asamblea cuando
        ejerza su facultad de revisar las cuentas de la mesa directiva, caso en el
        cual se entrega la información económica que corresponda.
    </p>

    <h2 id="conservacion">6. Cuánto tiempo se conserva</h2>

    <ul>
        <li><strong>Mientras seas residente</strong>, tus datos permanecen activos en
            la plataforma.</li>
        <li><strong>Al dejar de serlo</strong>, la cuenta se desactiva y deja de tener
            acceso.</li>
        <li><strong>La información económica</strong> —recibos, comprobantes, reportes
            mensuales— se conserva aunque dejes de ser residente, porque forma parte
            de la contabilidad del condominio y de la rendición de cuentas de cada
            mesa directiva ante la asamblea.</li>
        <li><strong>Los datos que no son económicos</strong> —vehículos, mascotas,
            reservaciones, fotografía de perfil— se eliminan cuando lo solicitas o
            cuando dejan de ser necesarios.</li>
    </ul>

    <h2 id="seguridad">7. Cómo se protege</h2>

    <ul>
        <li>El acceso es con usuario y contraseña. Las contraseñas se guardan
            <strong>cifradas con bcrypt</strong>: ni la mesa directiva ni el
            administrador pueden verlas.</li>
        <li>La comunicación con el sitio viaja cifrada mediante HTTPS.</li>
        <li>El sistema separa roles: vecino, mesa directiva y administrador. Las
            operaciones que mueven dinero están reservadas a la Tesorería.</li>
        <li>Las acciones administrativas quedan registradas con el usuario que las
            realizó.</li>
        <li>Los archivos que subes se guardan en el servidor del condominio, no en
            servicios públicos de terceros.</li>
    </ul>

    <div class="lg-nota">
        Ningún sistema es infalible. Si detectas un acceso indebido a tu cuenta o un
        comportamiento extraño, avísanos de inmediato al correo de contacto para
        poder actuar.
    </div>

    <h2 id="derechos">8. Tus derechos ARCO y cómo ejercerlos</h2>

    <p>Sobre tus datos personales tienes cuatro derechos:</p>

    <ul>
        <li><strong>Acceso:</strong> saber qué datos tuyos tenemos y cómo los usamos.</li>
        <li><strong>Rectificación:</strong> corregirlos si son inexactos o están
            incompletos.</li>
        <li><strong>Cancelación:</strong> pedir que se eliminen cuando ya no sean
            necesarios.</li>
        <li><strong>Oposición:</strong> pedir que dejemos de usarlos para un fin
            determinado.</li>
    </ul>

    <p>
        Para ejercerlos, envía un correo a
        <strong>{{ config('privacidad.correo') }}</strong> indicando tu nombre completo,
        tu número de casa, un medio para contactarte, qué derecho quieres ejercer y
        sobre qué datos. Acompáñalo de una identificación que permita comprobar que
        eres tú.
    </p>

    <p>
        La respuesta se dará en un plazo máximo de <strong>veinte días hábiles</strong>
        y, de proceder, se hará efectiva dentro de los quince días hábiles siguientes.
    </p>

    <div class="lg-ojo">
        <strong>Un límite honesto:</strong> la cancelación no aplica a la información
        económica que respalda la contabilidad del condominio. Un recibo pagado, un
        comprobante validado o un reporte mensual ya entregado a la asamblea no
        pueden borrarse, porque son el soporte de cuentas que la mesa directiva debe
        poder acreditar. Tampoco procede cuando exista un adeudo pendiente.
    </div>

    <h2 id="revocacion">9. Cómo revocar tu consentimiento</h2>

    <p>
        Puedes revocar en cualquier momento el consentimiento que nos diste para
        tratar tus datos, con las mismas limitaciones del punto anterior. La
        solicitud se hace por el mismo correo y con los mismos requisitos.
    </p>

    <p>
        Ten presente que revocar el consentimiento sobre datos necesarios implica
        dejar de usar la plataforma, aunque no te libera de tus obligaciones como
        condómino, que nacen del reglamento y no del sistema.
    </p>

    <h2 id="opciones">10. Cómo limitar el uso de tus datos</h2>

    <p>Estas opciones están en la plataforma y las controlas tú:</p>

    <ul>
        <li><strong>Correos.</strong> En tu perfil puedes desactivar el envío de correos.
            Seguirás viendo todo en la plataforma.</li>
        <li><strong>Notificaciones al celular.</strong> Se activan solo si las autorizas
            en el navegador y puedes retirar el permiso cuando quieras.</li>
        <li><strong>Fotografía de perfil.</strong> Es opcional: puedes no subirla o
            eliminarla.</li>
        <li><strong>Vehículos y mascotas.</strong> Solo se registran si tú los das de
            alta.</li>
        <li><strong>Directorio vecinal.</strong> Si no quieres que tu celular o tu
            fotografía sean visibles para los demás vecinos, escríbenos al correo de
            contacto y se retiran.</li>
    </ul>

    <h2 id="menores">11. Datos de menores de edad</h2>

    <p>
        La plataforma está dirigida a condóminos mayores de edad. No se solicitan
        deliberadamente datos de menores. Si consideras que se registró información
        de un menor sin el consentimiento de quien ejerce la patria potestad,
        escríbenos y se eliminará.
    </p>

    <h2 id="cambios">12. Cambios a este aviso</h2>

    <p>
        Este aviso puede modificarse cuando cambien las finalidades, se agreguen
        módulos que recaben información nueva o lo exija la normativa. Cualquier
        cambio se publica en esta misma dirección y la fecha de actualización del
        encabezado cambia. Te recomendamos revisarlo de vez en cuando.
    </p>

    <p>
        Si consideras que tu derecho a la protección de datos fue vulnerado, puedes
        acudir ante la autoridad competente en materia de protección de datos
        personales.
    </p>

    <div class="lg-nota">
        ¿Dudas sobre algo de este documento? Escríbenos a
        <strong>{{ config('privacidad.correo') }}</strong>. También puedes consultar
        los <a href="{{ route('legal.terminos') }}">Términos de Uso</a> de la plataforma.
    </div>

@endsection
