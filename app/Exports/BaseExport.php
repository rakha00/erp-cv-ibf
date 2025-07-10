<?php

namespace App\Exports;

use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

abstract class BaseExport implements WithEvents, WithTitle
{
    protected $resourceTitle;

    public function __construct($resourceTitle = 'Laporan')
    {
        $this->resourceTitle = $resourceTitle;
    }

    public function title(): string
    {
        return $this->resourceTitle;
    }

    public static function simple_column_width_calculator($data)
    {
        $max_widths = [];
        foreach ($data as $row) {
            foreach ($row as $col_index => $cell_value) {
                $max_widths[$col_index] = max($max_widths[$col_index] ?? 0, (int) strlen((string) $cell_value));
            }
        }

        return $max_widths;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $sheet->insertNewRowBefore(1, 3); // Insert 3 rows at the top

                $numColumns = count($this->headings()); // This method will be implemented by child classes

                // Merge cells for the title
                $sheet->mergeCells('A1:'.Coordinate::stringFromColumnIndex($numColumns).'1');
                $sheet->setCellValue('A1', 'CV. Inti Bintang Fortuna');
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
                $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $sheet->mergeCells('A2:'.Coordinate::stringFromColumnIndex($numColumns).'2');
                $sheet->setCellValue('A2', $this->resourceTitle);
                $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(12);
                $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Add print date
                $sheet->setCellValue('A3', 'Tanggal Cetak: '.Carbon::now()->format('d-m-Y H:i:s'));
                $sheet->mergeCells('A3:'.Coordinate::stringFromColumnIndex($numColumns).'3');
                $sheet->getStyle('A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Set column widths based on content
                $header_row = $this->headings();
                $data_rows = $this->collection()->map(function ($item) {
                    return $this->map($item);
                })->toArray();
                $all_rows = array_merge([$header_row], $data_rows);

                $max_widths = self::simple_column_width_calculator($all_rows);

                if (! empty($max_widths)) {
                    foreach ($max_widths as $col_index => $width) {
                        $column_letter = Coordinate::stringFromColumnIndex((int) $col_index + 1);
                        $event->sheet->getColumnDimension($column_letter)->setWidth((float) $width + 2);
                    }
                }
            },
        ];
    }

    // Abstract methods to be implemented by child classes
    abstract public function headings(): array;

    abstract public function collection();

    abstract public function map($row): array;
}
