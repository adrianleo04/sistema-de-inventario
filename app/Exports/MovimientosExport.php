<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MovimientosExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $movimientos;

    public function __construct($movimientos)
    {
        $this->movimientos = $movimientos;
    }

    public function collection(): \Illuminate\Support\Enumerable
    {
        return $this->movimientos;
    }

    public function headings(): array
    {
        return [
            'Fecha / Hora',
            'Tipo',
            'SKU',
            'Ítem',
            'Cantidad',
            'Área Origen',
            'Área Destino',
            'Usuario Responsable',
            'Motivo / Observación',
        ];
    }

    public function map($row): array
    {
        return [
            $row->created_at ? $row->created_at->format('d/m/Y H:i:s') : '',
            strtoupper($row->tipo),
            $row->item->sku ?? '',
            $row->item->nombre ?? '',
            number_format($row->cantidad, 2),
            $row->areaOrigen->nombre ?? '-',
            $row->areaDestino->nombre ?? '-',
            $row->usuario->name ?? '',
            $row->motivo ?? '',
        ];
    }

    public function styles(Worksheet $sheet): ?array
    {
        return [
            1 => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']], 'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '1E1B4B']]],
        ];
    }
}
