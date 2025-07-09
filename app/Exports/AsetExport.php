<?php

namespace App\Exports;

use App\Models\Aset;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Carbon\Carbon;

class AsetExport implements FromCollection, WithHeadings, WithMapping, WithTitle, WithEvents
{
	protected $query;
	protected $resourceTitle;

	public function __construct($query, $resourceTitle = 'Daftar Aset')
	{
		$this->query = $query;
		$this->resourceTitle = $resourceTitle;
	}

	/**
	 * @return \Illuminate\Support\Collection
	 */
	public function collection()
	{
		return $this->query->select('id', 'nama_aset', 'harga', 'jumlah_aset')->get();
	}

	public function headings(): array
	{
		return [
			'ID',
			'Nama Aset',
			'Harga',
			'Jumlah Aset',
		];
	}

	public function map($aset): array
	{
		return [
			$aset->id,
			$aset->nama_aset,
			$aset->harga,
			$aset->jumlah_aset,
		];
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
				$max_widths[$col_index] = max($max_widths[$col_index] ?? 0, strlen((string) $cell_value));
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
	
				// Merge cells for the title
				$sheet->mergeCells('A1:D1');
				$sheet->setCellValue('A1', 'CV. Inti Bintang Fortuna');
				$sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
				$sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

				$sheet->mergeCells('A2:D2');
				$sheet->setCellValue('A2', $this->resourceTitle);
				$sheet->getStyle('A2')->getFont()->setBold(true)->setSize(12);
				$sheet->getStyle('A2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

				// Add print date
				$sheet->setCellValue('A3', 'Tanggal Cetak: ' . Carbon::now()->format('d-m-Y H:i:s'));
				$sheet->mergeCells('A3:D3');
				$sheet->getStyle('A3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

				// Set column widths based on content
				$header_row = $this->headings();
				$data_rows = $this->collection()->toArray();
				$all_rows = array_merge([$header_row], $data_rows);

				$max_widths = self::simple_column_width_calculator($all_rows);

				// Ensure max_widths is not empty before iterating
				if (!empty($max_widths)) {
					foreach ($max_widths as $col_index => $width) {
						$column_letter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex((int) $col_index + 1);
						// Cast width to float for setWidth, and add padding
						$event->sheet->getColumnDimension($column_letter)->setWidth((float) $width + 2);
					}
				}
			},
		];
	}
}