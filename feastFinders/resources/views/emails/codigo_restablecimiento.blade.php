<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperación de Contraseña</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background-color: #f9f9f9;
        }
        .header {
            background-color: #007bff;
            color: white;
            padding: 10px 0;
            text-align: center;
            border-top-left-radius: 8px;
            border-top-right-radius: 8px;
        }
        .content {
            padding: 20px;
        }
        .code-box {
            background-color: #e9ecef;
            padding: 15px;
            font-size: 1.5em;
            text-align: center;
            border-radius: 5px;
            margin: 20px 0;
            font-weight: bold;
        }
        .footer {
            text-align: center;
            font-size: 0.9em;
            color: #777;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Restablecimiento de Contraseña</h2>
        </div>
        <div class="content">
            <p>Hola,</p>
            <p>Has solicitado restablecer tu contraseña. Utiliza el siguiente código para completar el proceso:</p>

            <div class="code-box">
                {{-- Aquí es donde se inyectará el código dinámicamente --}}
                {{ $codigo }}
            </div>

            <p>Este código es válido por {{ $expiracion }} horas.</p>
            <p>Si tú no solicitaste este cambio, puedes ignorar este correo de forma segura.</p>
            <p>Gracias,<br>
            El equipo de FeastFinders</p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} FeastFinders. Todos los derechos reservados.</p>
        </div>
    </div>
</body>
</html>