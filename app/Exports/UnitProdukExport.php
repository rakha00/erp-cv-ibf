<?php

namespace App\Exports;

use App\Models\UnitProduk;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Carbon\Carbon;

class UnitProdukExport extends BaseExport implements FromCollection, WithHeadings, WithMapping
{
	protected $query;

	public function __construct($query, $resourceTitle = 'Unit Produk')
	{
		parent::__construct($resourceTitle);
		$this->query = $query;
	}

	/**
	 * @return \Illuminate\Support\Collection
	 */
	public function collection()
	{
		return $this->query->with('barangMasukDetails', 'transaksiProdukDetails')->get();
	}

	public function headings(): array
	{
		return [
			'ID',
			'SKU',
			'Nama Unit',
			'Harga Modal/Unit',
			'Stok Awal',
			'Stok Akhir',
			'Stok Masuk',
			'Stok Keluar',
			'Remarks',
		];
	}

	public function map($unitProduk): array
	{
		$stokMasuk = $unitProduk->barangMasukDetails->sum('jumlah_barang_masuk');
		$stokKeluar = $unitProduk->transaksiProdukDetails->sum('jumlah_keluar');
		$stokAkhir = $unitProduk->stok_awal + $stokMasuk - $stokKeluar;

		return [
			$unitProduk->id,
			$unitProduk->sku,
			$unitProduk->nama_unit,
			$unitProduk->harga_modal,
			$unitProduk->stok_awal,
			$stokAkhir,
			$stokMasuk,
			$stokKeluar,
			$unitProduk->remarks,
		];
	}
}