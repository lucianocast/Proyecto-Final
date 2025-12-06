<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Orden de Compra #{{ $orden->id }}</title>
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
            width: 180px;
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
            font-size: 14px;
        }
        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
        }
        .badge-pendiente { background-color: #ffc107; color: #000; }
        .badge-aprobada { background-color: #007bff; color: #fff; }
        .badge-rechazada { background-color: #dc3545; color: #fff; }
        .badge-recibida_parcial { background-color: #17a2b8; color: #fff; }
        .badge-recibida_total { background-color: #28a745; color: #fff; }
        .badge-cancelada { background-color: #6c757d; color: #fff; }
    </style>
</head>
<body>
    <div class="header">
        <h1>ContuCocina Pastelería</h1>
        <p>Orden de Compra #{{ $orden->id }}</p>
    </div>

    <div class="info-section">
        <h3>Información del Proveedor</h3>
        <div class="info-row">
            <strong>Empresa:</strong> {{ $orden->proveedor->nombre_empresa }}
        </div>
        <div class="info-row">
            <strong>Contacto:</strong> {{ $orden->proveedor->nombre_contacto ?? 'N/A' }}
        </div>
        <div class="info-row">
            <strong>Email:</strong> {{ $orden->proveedor->email ?? 'N/A' }}
        </div>
        <div class="info-row">
            <strong>Teléfono:</strong> {{ $orden->proveedor->telefono ?? 'N/A' }}
        </div>
    </div>

    <div class="info-section">
        <h3>Detalles de la Orden</h3>
        <div class="info-row">
            <strong>Estado:</strong> 
            <span class="badge badge-{{ $orden->status }}">
                {{ ucfirst(str_replace('_', ' ', $orden->status)) }}
            </span>
        </div>
        <div class="info-row">
            <strong>Fecha de Emisión:</strong> {{ $orden->fecha_emision->format('d/m/Y') }}
        </div>
        <div class="info-row">
            <strong>Fecha Entrega Esperada:</strong> {{ $orden->fecha_entrega_esperada ? $orden->fecha_entrega_esperada->format('d/m/Y') : 'N/A' }}
        </div>
        <div class="info-row">
            <strong>Creada por:</strong> {{ $orden->user->name ?? 'N/A' }}
        </div>
        <div class="info-row">
            <strong>Fecha de Creación:</strong> {{ $orden->created_at->format('d/m/Y H:i') }}
        </div>
    </div>

    <div class="info-section">
        <h3>Items de la Orden</h3>
        <table>
            <thead>
                <tr>
                    <th>Insumo</th>
                    <th>Cantidad</th>
                    <th>Precio Unitario</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($orden->items as $item)
                <tr>
                    <td>{{ $item->insumo->nombre }}</td>
                    <td>{{ number_format($item->cantidad, 2) }} {{ $item->insumo->unidad_de_medida->value }}</td>
                    <td>${{ number_format($item->precio_unitario, 2) }}</td>
                    <td>${{ number_format($item->subtotal, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="totals">
            <strong>TOTAL:</strong> ${{ number_format($orden->total_calculado, 2) }}
        </div>
    </div>

    <div style="text-align: center; margin-top: 40px; font-size: 10px; color: #666;">
        <p>Documento generado el {{ now()->format('d/m/Y H:i') }}</p>
    </div>
</body>
</html>
