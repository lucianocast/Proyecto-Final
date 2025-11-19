<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pedido Confirmado</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #e74c3c;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
        .content {
            background-color: #f9f9f9;
            padding: 30px;
            border: 1px solid #ddd;
            border-radius: 0 0 8px 8px;
        }
        .pedido-id {
            font-size: 24px;
            font-weight: bold;
            color: #e74c3c;
        }
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>¡Pedido Confirmado!</h1>
    </div>
    <div class="content">
        <p>Hola <strong>{{ $clienteNombre }}</strong>,</p>
        
        <p>Tu pedido ha sido confirmado exitosamente.</p>
        
        <p>Número de pedido: <span class="pedido-id">#{{ $pedidoId }}</span></p>
        
        <p>Recibirás una notificación cuando tu pedido esté listo para entrega.</p>
        
        <p>Gracias por confiar en nosotros.</p>
        
        <p>Saludos,<br>El equipo de Pastelería</p>
    </div>
    <div class="footer">
        <p>Este es un correo automático, por favor no responder.</p>
    </div>
</body>
</html>
