{{--
    Términos de Uso de la plataforma.

    Complementa al aviso de privacidad: aquel explica qué se hace con los datos,
    este explica qué se espera de quien usa el sistema y qué alcance tiene lo
    que ahí se registra.
--}}
@extends('legal.layout')

@section('titulo', 'Términos de Uso')

@section('contenido')

    <h1 class="lg-titulo">Términos de Uso</h1>
    <p class="lg-fecha">
        Última actualización:
        {{ \Carbon\Carbon::parse(config('privacidad.ultima_actualizacion'))->translatedFormat('j \d\e F \d\e Y') }}
    </p>

    <div class="lg-indice">
        <div class="lg-indice-titulo">Contenido</div>
        <ol>
            <li><a href="#que-es">Qué es esta plataforma y qué no</a></li>
            <li><a href="#quien">Quién puede usarla</a></li>
            <li><a href="#cuenta">Tu cuenta</a></li>
            <li><a href="#pagos">Pagos y comprobantes</a></li>
            <li><a href="#contenido">Lo que subes</a></li>
            <li><a href="#conducta">Uso debido de la información de otros</a></li>
            <li><a href="#disponibilidad">Disponibilidad del servicio</a></li>
            <li><a href="#cambios">Cambios y suspensión</a></li>
        </ol>
    </div>

    <h2 id="que-es">1. Qué es esta plataforma y qué no</h2>

    <p>
        La {{ config('privacidad.plataforma') }} es una herramienta administrativa de la
        {{ config('privacidad.responsable') }} para la gestión interna del condominio:
        cuotas, comprobantes, comunicados, áreas comunes, asambleas y transparencia
        de ingresos y egresos.
    </p>

    <p><strong>Lo que no es:</strong></p>

    <ul>
        <li><strong>No es una pasarela de pagos.</strong> El sistema no cobra ni procesa
            dinero. Los pagos se hacen por los medios que la Tesorería indique
            —transferencia o depósito— y en la plataforma únicamente se registra el
            comprobante para su validación.</li>
        <li><strong>No sustituye al reglamento interno ni a las actas de asamblea.</strong>
            Ante una discrepancia, prevalecen los documentos oficiales del condominio.</li>
        <li><strong>No es un servicio comercial.</strong> No tiene costo para el vecino ni
            genera lucro; es infraestructura administrativa del propio condominio.</li>
    </ul>

    <h2 id="quien">2. Quién puede usarla</h2>

    <p>
        Pueden tener cuenta los propietarios e inquilinos de las viviendas del
        condominio, así como los integrantes de la mesa directiva en funciones. El
        alta y la baja de cuentas las administra la mesa directiva.
    </p>

    <p>
        Los niveles de acceso son tres: <strong>vecino</strong>, que consulta y
        gestiona lo propio; <strong>mesa directiva</strong>, que administra los módulos
        del condominio; y <strong>administrador del sistema</strong>, que atiende la
        operación técnica. Dentro de la mesa, las operaciones que mueven dinero están
        reservadas a quien tiene el cargo de Tesorería.
    </p>

    <h2 id="cuenta">3. Tu cuenta</h2>

    <ul>
        <li>La cuenta es <strong>personal e intransferible</strong>. No compartas tu
            contraseña: lo que se haga desde tu sesión se registra a tu nombre.</li>
        <li>Mantén tu correo y tu celular actualizados. Son los medios por los que te
            llegan los avisos de vencimiento y el resultado de la validación de tus
            pagos.</li>
        <li>Si sospechas que alguien más entró a tu cuenta, cambia la contraseña y
            avisa a la mesa directiva.</li>
        <li>La información que registras debe ser veraz. Datos falsos en un
            comprobante o en un registro pueden derivar en las consecuencias que
            prevea el reglamento interno.</li>
    </ul>

    <h2 id="pagos">4. Pagos y comprobantes</h2>

    <ul>
        <li>Subir un comprobante <strong>no equivale a que el pago esté aprobado</strong>.
            La Tesorería lo revisa y lo marca como pagado o rechazado. Mientras tanto
            el recibo sigue pendiente.</li>
        <li>Si se rechaza, recibirás el motivo por correo y en la plataforma, y podrás
            volver a subirlo corregido.</li>
        <li>El <strong>recargo por pago tardío</strong> se calcula contra la fecha en que
            realizaste la transferencia, no contra la fecha en que subiste el
            comprobante. Por eso es importante que captures la fecha real del
            movimiento.</li>
        <li>Si pagas de más, el excedente queda como <strong>saldo a favor</strong> y se
            aplica automáticamente a tus siguientes cuotas de mantenimiento. No se
            usa para derramas ni gastos extraordinarios, salvo que así se indique.</li>
        <li>Las cifras que muestra la plataforma son un reflejo del registro
            administrativo. Ante cualquier diferencia con tu estado de cuenta
            bancario, plantéalo a la Tesorería: se revisa y se corrige.</li>
    </ul>

    <h2 id="contenido">5. Lo que subes</h2>

    <p>
        Eres responsable de los archivos e información que cargas: comprobantes,
        fotografías de vehículos, mascotas y perfil.
    </p>

    <ul>
        <li>Sube únicamente archivos de los que tengas derecho a disponer.</li>
        <li><strong>Al subir un comprobante, cubre o recorta lo que no sea necesario.</strong>
            Para validar un pago basta con el monto, la fecha, el destinatario y el
            folio. No hace falta tu número de cuenta completo ni tu saldo.</li>
        <li>No subas fotografías en las que aparezcan terceros sin su consentimiento.</li>
        <li>La mesa directiva puede retirar contenido que sea ofensivo, ajeno a los
            fines del condominio o que exponga datos de otras personas.</li>
    </ul>

    <h2 id="conducta">6. Uso debido de la información de otros</h2>

    <p>
        Dentro de la plataforma tendrás acceso a información de tus vecinos: el
        directorio con nombre, casa, celular y fotografía, y el registro de vehículos
        con sus placas.
    </p>

    <p><strong>Esa información se te comparte para la convivencia del condominio, y para nada más.</strong> En particular, no está permitido:</p>

    <ul>
        <li>Extraerla, copiarla en listas o difundirla fuera del condominio.</li>
        <li>Usarla con fines comerciales, publicitarios o de proselitismo.</li>
        <li>Emplearla para hostigar, presionar o exhibir a otro vecino.</li>
        <li>Publicar en redes sociales o grupos de mensajería capturas de pantalla que
            muestren datos de terceros.</li>
    </ul>

    <p>
        El uso indebido de datos personales de otras personas puede tener
        consecuencias legales para quien lo comete, independientemente de las medidas
        que adopte la mesa directiva.
    </p>

    <h2 id="disponibilidad">7. Disponibilidad del servicio</h2>

    <p>
        La plataforma se ofrece tal como está. Se procura que esté disponible de
        forma continua, pero puede haber interrupciones por mantenimiento, fallas del
        proveedor de alojamiento o causas ajenas a la mesa directiva.
    </p>

    <p>
        Una interrupción del sistema <strong>no suspende ni prorroga los plazos de pago</strong>
        establecidos por la asamblea. Si el sistema no está disponible cuando venza tu
        cuota, comunícate con la Tesorería por los medios habituales.
    </p>

    <h2 id="cambios">8. Cambios y suspensión</h2>

    <p>
        Estos términos pueden actualizarse cuando cambien las funciones de la
        plataforma o las reglas internas del condominio. La versión vigente es
        siempre la publicada en esta dirección, con su fecha de actualización.
    </p>

    <p>
        La mesa directiva puede suspender una cuenta que incumpla estos términos,
        que haga un uso indebido de la información de otros vecinos o cuando la
        persona deje de residir en el condominio.
    </p>

    <div class="lg-nota">
        Para saber qué datos se recaban y cómo ejercer tus derechos, consulta el
        <a href="{{ route('legal.privacidad') }}">Aviso de Privacidad</a>. Cualquier duda:
        <strong>{{ config('privacidad.correo') }}</strong>.
    </div>

@endsection
