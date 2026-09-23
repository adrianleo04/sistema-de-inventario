<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class InventarioExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $inventarios;

    public function __construct($inventarios)
    {
        $this->inventarios = $inventarios;
    }

    public function collection(): \Illuminate\Support\Enumerable
    {
        return $this->inventarios;
    }

    public function headings(): array
    {
        return [
            'SKU',
            'Ítem',
            'Categoría',
            'Sucursal',
            'Área',
            'Encargado del Área',
            'Cantidad en Área',
            'Unidad de Medida',
            'Costo Unitario ($)',
            'Stock Mínimo',
        ];
    }

    public function map($row): array
    {
        return [
            $row->item->sku ?? '',
            $row->item->nombre ?? '',
            $row->item->categoria->nombre ?? '',
            $row->area->sucursal->nombre ?? '',
            $row->area->nombre ?? '',
            $row->area->encargado->name ?? 'Sin asignar',
            number_format($row->cantidad, 2),
            $row->item->unidadMedida->abreviatura ?? '',
            number_format($row->item->costo_unitario ?? 0, 2),
            $row->item->stock_minimo ?? 0,
        ];
    }

    public function styles(Worksheet $sheet): ?array
    {
        return [
            1 => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']], 'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '312E81']]],
        ];
    }
}
