<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Pedido #{{ $pedido->id }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #ff8c42;
            padding-bottom: 10px;
        }
        .header h1 {
            color: #ff8c42;
            margin: 0;
        }
        .info-section {
            margin-bottom: 20px;
        }
        .info-section h3 {
            background-color: #ff8c42;
            color: white;
            padding: 5px 10px;
            margin: 0 0 10px 0;
        }
        .info-row {
            margin: 5px 0;
        }
        .info-row strong {
            display: inline-block;
            width: 150px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        table th {
            background-color: #ff8c42;
            color: white;
            padding: 8px;
            text-align: left;
        }
        table td {
            border: 1px solid #ddd;
            padding: 8px;
        }
        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .totals {
            text-align: right;
            margin-top: 20px;
        }
        .totals div {
            margin: 5px 0;
        }
        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
        }
        .badge-pendiente { background-color: #ffc107; color: #000; }
        .badge-en_produccion { background-color: #007bff; color: #fff; }
        .badge-listo { background-color: #17a2b8; color: #fff; }
        .badge-entregado { background-color: #28a745; color: #fff; }
        .badge-cancelado { background-color: #dc3545; color: #fff; }
    </style>
</head>
<body>
    <div class="header">
        <h1>ContuCocina Pastelería</h1>
        <p>Pedido #{{ $pedido->id }}</p>
    </div>

    <div class="info-section">
        <h3>Información del Cliente</h3>
        <div class="info-row">
            <strong>Cliente:</strong> {{ $pedido->cliente->nombre }}
        </div>
        <div class="info-row">
            <strong>Email:</strong> {{ $pedido->cliente->email ?? 'N/A' }}
        </div>
        <div class="info-row">
            <strong>Teléfono:</strong> {{ $pedido->cliente->telefono ?? 'N/A' }}
        </div>
    </div>

    <div class="info-section">
        <h3>Detalles del Pedido</h3>
        <div class="info-row">
            <strong>Estado:</strong> 
            <span class="badge badge-{{ $pedido->status }}">
                {{ ucfirst(str_replace('_', ' ', $pedido->status)) }}
            </span>
        </div>
        <div class="info-row">
            <strong>Fecha de Entrega:</strong> {{ $pedido->fecha_entrega->format('d/m/Y H:i') }}
        </div>
        <div class="info-row">
            <strong>Forma de Entrega:</strong> {{ ucfirst($pedido->forma_entrega) }}
        </div>
        @if($pedido->forma_entrega === 'envio' && $pedido->direccion_envio)
        <div class="info-row">
            <strong>Dirección:</strong> {{ $pedido->direccion_envio }}
        </div>
        @endif
        <div class="info-row">
            <strong>Vendedor:</strong> {{ $pedido->vendedor->name ?? 'N/A' }}
        </div>
        <div class="info-row">
            <strong>Fecha de Creación:</strong> {{ $pedido->created_at->format('d/m/Y H:i') }}
        </div>
    </div>

    <div class="info-section">
        <h3>Items del Pedido</h3>
        <table>
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Variante</th>
                    <th>Cantidad</th>
                    <th>Precio Unit.</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pedido->items as $item)
                <tr>
                    <td>{{ $item->productoVariante->producto->nombre }}</td>
                    <td>{{ $item->productoVariante->descripcion ?? 'Estándar' }}</td>
                    <td>{{ $item->cantidad }}</td>
                    <td>${{ number_format($item->precio_unitario, 2) }}</td>
                    <td>${{ number_format($item->subtotal, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="info-section">
        <h3>Información de Pago</h3>
        <div class="info-row">
            <strong>Método de Pago:</strong> {{ ucfirst($pedido->metodo_pago) }}
        </div>
        <div class="totals">
            <div style="font-size: 14px;">
                <strong>Total:</strong> ${{ number_format($pedido->total_calculado, 2) }}
            </div>
            <div style="color: #28a745;">
                <strong>Monto Abonado:</strong> ${{ number_format($pedido->monto_abonado, 2) }}
            </div>
            <div style="color: {{ $pedido->saldo_pendiente > 0 ? '#dc3545' : '#28a745' }};">
                <strong>Saldo Pendiente:</strong> ${{ number_format($pedido->saldo_pendiente, 2) }}
            </div>
        </div>

        @if($pedido->pagos->count() > 0)
        <h4 style="margin-top: 20px;">Historial de Pagos</h4>
        <table>
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Método</th>
                    <th>Monto</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pedido->pagos as $pago)
                <tr>
                    <td>{{ $pago->fecha_pago ? $pago->fecha_pago->format('d/m/Y H:i') : 'N/A' }}</td>
                    <td>{{ ucfirst($pago->metodo) }}</td>
                    <td>${{ number_format($pago->monto, 2) }}</td>
                    <td>{{ ucfirst($pago->estado) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>

    @if($pedido->observaciones)
    <div class="info-section">
        <h3>Observaciones</h3>
        <p>{{ $pedido->observaciones }}</p>
    </div>
    @endif

    <div style="text-align: center; margin-top: 40px; font-size: 10px; color: #666;">
        <p>Documento generado el {{ now()->format('d/m/Y H:i') }}</p>
    </div>
</body>
</html>
