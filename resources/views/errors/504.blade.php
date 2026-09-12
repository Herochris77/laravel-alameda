<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso no autorizado</title>
    <link rel="icon" href="{{ URL::to('/') }}/img/logo-header.png">
    <!-- Fomantic UI CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fomantic-ui@2.9.2/dist/semantic.min.css">
    <!-- Lottie Player -->
    <script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            min-height: 100vh;
            background-color: #f4f4f4;
            font-family: Arial, sans-serif;
        }

        .container {
            text-align: center;
        }

        .message {
            margin-top: 20px;
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Lottie Animation -->
        <lottie-player
            src='{{ asset("lottie/bloqueo.json") }}'
            background="transparent"
            speed="1"
            style="width: 300px; height: 300px; margin: 0 auto;"
            loop
            autoplay>
        </lottie-player>

        <!-- Mensaje -->
        <h2 class="ui red header message">Cuenta Suspendida</h2>
        <p class="message">La administración suspendió tu cuenta, por favor contacta con alguien de la mesa directiva para cualquier aclaración</p>
    </div>
</body>

</html>