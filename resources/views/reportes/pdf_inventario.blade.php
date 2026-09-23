<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Inventario Actual</title>
    <style>
        body { font-family: sans-serif; font-size: 11px; color: #333; }
        .header { text-align: center; margin-bottom: 20px; border-b: 2px solid #312E81; padding-bottom: 10px; }
        .title { font-size: 18px; font-weight: bold; color: #312E81; }
        .subtitle { font-size: 11px; color: #666; margin-top: 4px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 6px; text-align: left; }
        th { background-color: #312E81; color: white; font-weight: bold; text-transform: uppercase; font-size: 9px; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .footer { margin-top: 20px; text-align: right; font-size: 9px; color: #777; }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">{{ $empresaNombre }}</div>
        <div class="subtitle">REPORTE OFICIAL DE EXISTENCIAS DE INVENTARIO EN ÁREAS</div>
        <div class="subtitle">Generado el {{ date('d/m/Y H:i:s') }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th>SKU</th>
                <th>Ítem</th>
                <th>Categoría</th>
                <th>Sucursal</th>
                <th>Área</th>
                <th>Encargado</th>
                <th class="text-right">Cantidad</th>
            </tr>
        </thead>
        <tbody>
            @foreach($inventarios as $inv)
                <tr>
                    <td>{{ $inv->item->sku ?? '' }}</td>
                    <td class="font-bold">{{ $inv->item->nombre ?? '' }}</td>
                    <td>{{ $inv->item->categoria->nombre ?? '' }}</td>
                    <td>{{ $inv->area->sucursal->nombre ?? '' }}</td>
                    <td>{{ $inv->area->nombre ?? '' }}</td>
                    <td>{{ $inv->area->encargado->name ?? 'Sin asignar' }}</td>
                    <td class="text-right font-bold">{{ number_format($inv->cantidad, 2) }} {{ $inv->item->unidadMedida->abreviatura ?? '' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Sistema de Control de Inventario para Mipymes — Página 1
    </div>
</body>
</html>
