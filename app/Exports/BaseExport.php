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
    protected string $resourceTitle;

    // Constants for better readability and maintainability
    private const HEADER_ROWS_COUNT = 3;

    private const COMPANY_NAME = 'CV. Inti Bintang Fortuna';

    private const PRINT_DATE_PREFIX = 'Tanggal Cetak: ';

    private const COLUMN_WIDTH_PADDING = 2;

    public function __construct(string $resourceTitle = 'Laporan')
    {
        $this->resourceTitle = $resourceTitle;
    }

    public function title(): string
    {
        return $this->resourceTitle;
    }

    /**
     * Calculates optimal column widths based on content.
     */
    public static function calculateColumnWidths(array $data): array
    {
        $max_widths = [];
        foreach ($data as $row) {
            foreach ($row as $col_index => $cell_value) {
                // Ensure cell_value is treated as a string for strlen
                $cell_length = strlen((string) $cell_value);
                $max_widths[$col_index] = max($max_widths[$col_index] ?? 0, $cell_length);
            }
        }

        return $max_widths;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                $header_row_data = $this->headings();
                $numColumns = count($header_row_data);

                // Insert header rows
                $this->addHeaderRows($sheet);

                // Set company and resource title
                $this->setCompanyAndResourceTitle($sheet, $numColumns);

                // Set print date
                $this->setPrintDate($sheet, $numColumns);

                // Set column widths based on content
                $this->setColumnWidths($event->sheet, $header_row_data);
            },
        ];
    }

    /**
     * Inserts new rows at the top of the sheet for headers.
     */
    private function addHeaderRows($sheet): void
    {
        $sheet->insertNewRowBefore(1, self::HEADER_ROWS_COUNT);
    }

    /**
     * Sets the company name and resource title in the header.
     */
    private function setCompanyAndResourceTitle($sheet, int $numColumns): void
    {
        $lastColumn = Coordinate::stringFromColumnIndex($numColumns);

        // Company Name
        $sheet->mergeCells('A1:'.$lastColumn.'1');
        $sheet->setCellValue('A1', self::COMPANY_NAME);
        $sheet->getStyle('A1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 14,
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
        ]);

        // Resource Title
        $sheet->mergeCells('A2:'.$lastColumn.'2');
        $sheet->setCellValue('A2', $this->resourceTitle);
        $sheet->getStyle('A2')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 12,
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
        ]);
    }

    /**
     * Sets the print date in the header.
     */
    private function setPrintDate($sheet, int $numColumns): void
    {
        $lastColumn = Coordinate::stringFromColumnIndex($numColumns);
        $sheet->setCellValue('A3', self::PRINT_DATE_PREFIX.Carbon::now()->format('d-m-Y H:i:s'));
        $sheet->mergeCells('A3:'.$lastColumn.'3');
        $sheet->getStyle('A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    }

    /**
     * Calculates and sets optimal column widths.
     */
    private function setColumnWidths(object $excelSheet, array $headerRowData): void
    {
        $data_rows = $this->collection()->map(function ($item) {
            return $this->map($item);
        })->toArray();

        $all_rows = array_merge([$headerRowData], $data_rows);

        $max_widths = self::calculateColumnWidths($all_rows);

        if (! empty($max_widths)) {
            foreach ($max_widths as $col_index => $width) {
                $column_letter = Coordinate::stringFromColumnIndex((int) $col_index + 1);
                $excelSheet->getColumnDimension($column_letter)->setWidth((float) $width + self::COLUMN_WIDTH_PADDING);
            }
        }
    }

    // Abstract methods to be implemented by child classes
    abstract public function headings(): array;

    abstract public function collection();

    abstract public function map($row): array;
}
