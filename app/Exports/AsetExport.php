<?php

namespace App\Exports;

use App\Models\Aset;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AsetExport extends BaseExport implements FromCollection, WithHeadings, WithMapping
{
	protected $query;

	public function __construct($query, $resourceTitle = 'Daftar Aset')
	{
		parent::__construct($resourceTitle);
		$this->query = $query;
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
}