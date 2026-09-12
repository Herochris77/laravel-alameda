<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $titulo ?? 'Correo de Alameda' }}</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { text-align: center; margin-bottom: 30px; }
        .logo { width: 150px; }
        h2 { color: #2c3e50; text-align: center; }
        .content { background: #f9f9f9; padding: 20px; border-radius: 8px; }
        .footer { text-align: center; margin-top: 30px; font-size: 12px; color: #666; }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ URL::to('/') }}/img/logo-header.png" alt="Logo Alameda" class="logo">
    </div>
    
    <h2>{{ $titulo }}</h2>
    
    <div class="content">
        {!! $mensaje !!}
    </div>
    
    <div class="footer">
        <p>Este correo fue enviado desde sistema condominio Alameda.</p>
        <p>&copy; {{ date('Y') }} Condominio Alameda. Todos los derechos reservados.</p>
    </div>
</body>
</html>