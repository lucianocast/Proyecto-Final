<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Compras</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 11px; color: #333; padding: 20px; }
        h1 { font-size: 18px; margin-bottom: 5px; color: #2563eb; }
        h2 { font-size: 14px; margin-top: 15px; margin-bottom: 10px; color: #1e40af; border-bottom: 2px solid #2563eb; padding-bottom: 3px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 3px solid #2563eb; padding-bottom: 10px; }
        .info-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px; margin-bottom: 15px; }
        .info-box { background: #f3f4f6; padding: 8px; border-radius: 4px; }
        .info-label { font-weight: bold; color: #6b7280; font-size: 9px; text-transform: uppercase; }
        .info-value { font-size: 12px; color: #111827; margin-top: 2px; }
        .metrics { display: grid; grid-template-columns: repeat(4, 1fr); gap: 10px; margin: 20px 0; }
        .metric-card { background: #dbeafe; padding: 10px; border-radius: 4px; text-align: center; border-left: 4px solid #2563eb; }
        .metric-label { font-size: 9px; color: #1e40af; font-weight: bold; text-transform: uppercase; }
        .metric-value { font-size: 16px; font-weight: bold; color: #1e3a8a; margin-top: 3px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        thead th { background: #2563eb; color: white; padding: 8px; text-align: left; font-size: 10px; font-weight: bold; }
        tbody td { padding: 6px 8px; border-bottom: 1px solid #e5e7eb; }
        tbody tr:nth-child(even) { background: #f9fafb; }
        tbody tr:hover { background: #f3f4f6; }
        tfoot td { padding: 8px; background: #1e40af; color: white; font-weight: bold; }
        .text-right { text-align: right; }
        .footer { margin-top: 30px; padding-top: 15px; border-top: 2px solid #e5e7eb; text-align: center; font-size: 9px; color: #6b7280; }
    </style>
</head>
<body>
    <div class="header">
        <h1>🍰 Sistema de Gestión de Pastelería</h1>
        <p style="color: #6b7280; margin-top: 5px;">Reporte de Compras por Período</p>
    </div>

    <div class="info-grid">
        <div class="info-box">
            <div class="info-label">Período</div>
            <div class="info-value">{{ $reporte['periodo']['desde'] }} - {{ $reporte['periodo']['hasta'] }}</div>
        </div>
        <div class="info-box">
            <div class="info-label">Generado</div>
            <div class="info-value">{{ $reporte['generado_en'] }}</div>
        </div>
        <div class="info-box">
            <div class="info-label">Agrupado Por</div>
            <div class="info-value">{{ ucfirst($reporte['criterios']['agrupar_por']) }}</div>
        </div>
        <div class="info-box">
            <div class="info-label">Estados Incluidos</div>
            <div class="info-value">{{ implode(', ', $reporte['criterios']['estado_oc']) }}</div>
        </div>
    </div>

    <h2>Métricas Consolidadas</h2>
    <div class="metrics">
        <div class="metric-card">
            <div class="metric-label">Costo Total</div>
            <div class="metric-value">${{ number_format($reporte['metricas']['costo_total'], 2) }}</div>
        </div>
        <div class="metric-card">
            <div class="metric-label">Total Órdenes</div>
            <div class="metric-value">{{ $reporte['metricas']['total_ordenes'] }}</div>
        </div>
        <div class="metric-card">
            <div class="metric-label">Insumos Únicos</div>
            <div class="metric-value">{{ $reporte['metricas']['insumos_unicos'] }}</div>
        </div>
        <div class="metric-card">
            <div class="metric-label">Promedio/Orden</div>
            <div class="metric-value">${{ number_format($reporte['metricas']['costo_promedio_orden'], 2) }}</div>
        </div>
    </div>

    <h2>Detalle por {{ ucfirst($reporte['criterios']['agrupar_por']) }}</h2>
    <table>
        <thead>
            <tr>
                <th>{{ ucfirst($reporte['criterios']['agrupar_por']) }}</th>
                <th class="text-right">Órdenes</th>
                <th class="text-right">Costo Total</th>
                @if(isset($reporte['datos_agrupados'][0]['costo_promedio']))
                    <th class="text-right">Costo Promedio</th>
                @endif
                @if(isset($reporte['datos_agrupados'][0]['cantidad_total']))
                    <th class="text-right">Cantidad Total</th>
                @endif
                <th class="text-right">% Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reporte['datos_agrupados'] as $fila)
                <tr>
                    <td>{{ $fila['nombre'] }}</td>
                    <td class="text-right">{{ $fila['total_ordenes'] ?? '-' }}</td>
                    <td class="text-right">${{ number_format($fila['costo_total'], 2) }}</td>
                    @if(isset($fila['costo_promedio']))
                        <td class="text-right">${{ number_format($fila['costo_promedio'], 2) }}</td>
                    @endif
                    @if(isset($fila['cantidad_total']))
                        <td class="text-right">{{ number_format($fila['cantidad_total'], 2) }}</td>
                    @endif
                    <td class="text-right">
                        {{ number_format(($fila['costo_total'] / $reporte['metricas']['costo_total']) * 100, 1) }}%
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td>TOTAL</td>
                <td class="text-right">{{ $reporte['metricas']['total_ordenes'] }}</td>
                <td class="text-right">${{ number_format($reporte['metricas']['costo_total'], 2) }}</td>
                <td colspan="10"></td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        <p>Reporte generado automáticamente por el Sistema de Gestión de Pastelería</p>
        <p>{{ $reporte['generado_en'] }}</p>
    </div>
</body>
</html>
