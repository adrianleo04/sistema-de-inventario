<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Movimientos de Inventario</title>
    <style>
        body { font-family: sans-serif; font-size: 10px; color: #333; }
        .header { text-align: center; margin-bottom: 20px; border-b: 2px solid #1E1B4B; padding-bottom: 10px; }
        .title { font-size: 18px; font-weight: bold; color: #1E1B4B; }
        .subtitle { font-size: 10px; color: #666; margin-top: 4px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 5px; text-align: left; }
        th { background-color: #1E1B4B; color: white; font-weight: bold; text-transform: uppercase; font-size: 8px; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .footer { margin-top: 20px; text-align: right; font-size: 9px; color: #777; }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">{{ $empresaNombre }}</div>
        <div class="subtitle">BITÁCORA E HISTORIAL OFICIAL DE MOVIMIENTOS DE INVENTARIO</div>
        <div class="subtitle">Generado el {{ date('d/m/Y H:i:s') }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Fecha / Hora</th>
                <th>Tipo</th>
                <th>SKU</th>
                <th>Ítem</th>
                <th>Cantidad</th>
                <th>Origen → Destino</th>
                <th>Usuario</th>
                <th>Motivo</th>
            </tr>
        </thead>
        <tbody>
            @foreach($movimientos as $mov)
                <tr>
                    <td>{{ $mov->created_at ? $mov->created_at->format('d/m/Y H:i') : '' }}</td>
                    <td class="font-bold">{{ strtoupper($mov->tipo) }}</td>
                    <td>{{ $mov->item->sku ?? '' }}</td>
                    <td class="font-bold">{{ $mov->item->nombre ?? '' }}</td>
                    <td>{{ number_format($mov->cantidad, 2) }}</td>
                    <td>{{ $mov->areaOrigen->nombre ?? '-' }} → {{ $mov->areaDestino->nombre ?? '-' }}</td>
                    <td>{{ $mov->usuario->name ?? '' }}</td>
                    <td>{{ $mov->motivo ?? '' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Sistema de Control de Inventario para Mipymes — Página 1
    </div>
</body>
</html>
