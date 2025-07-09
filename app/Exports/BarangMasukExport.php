<?php

namespace App\Exports;

use App\Models\BarangMasuk;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Carbon\Carbon;

class BarangMasukExport extends BaseExport implements FromCollection, WithHeadings, WithMapping
{
	protected $query;
	protected $includeDetails;
	protected $lastBarangMasukId = null; // To track the last Barang Masuk ID for de-duplication

	public function __construct($query, $resourceTitle = 'Barang Masuk', $includeDetails = false)
	{
		parent::__construct($resourceTitle);
		$this->query = $query;
		$this->includeDetails = $includeDetails;
	}

	/**
	 * @return \Illuminate\Support\Collection
	 */
	public function collection()
	{
		if ($this->includeDetails) {
			$barangMasuks = $this->query->with('principleSubdealer', 'barangMasukDetails.unitProduk')->get();
			$flattenedData = collect();

			foreach ($barangMasuks as $barangMasuk) {
				if ($barangMasuk->barangMasukDetails->isEmpty()) {
					$flattenedData->push([
						'barang_masuk_id' => $barangMasuk->id,
						'nomor_barang_masuk' => $barangMasuk->nomor_barang_masuk,
						'principle_subdealer_nama' => $barangMasuk->principleSubdealer->nama ?? '',
						'tanggal_barang_masuk' => Carbon::parse($barangMasuk->tanggal)->format('Y-m-d'),
						'total_harga_modal_main' => $barangMasuk->barangMasukDetails->reduce(function ($carry, $detail) {
							return $carry + ($detail->harga_modal * $detail->jumlah_barang_masuk);
						}, 0),
						'remarks_main' => $barangMasuk->remarks,
						'id_detail' => null,
						'sku' => null,
						'nama_unit' => null,
						'jumlah_barang_masuk_detail' => null,
						'harga_modal_detail' => null,
						'remarks_detail' => null,
					]);
				} else {
					foreach ($barangMasuk->barangMasukDetails as $detail) {
						$flattenedData->push([
							'barang_masuk_id' => $barangMasuk->id,
							'nomor_barang_masuk' => $barangMasuk->nomor_barang_masuk,
							'principle_subdealer_nama' => $barangMasuk->principleSubdealer->nama ?? '',
							'tanggal_barang_masuk' => Carbon::parse($barangMasuk->tanggal)->format('Y-m-d'),
							'total_harga_modal_main' => $barangMasuk->barangMasukDetails->reduce(function ($carry, $item) {
								return $carry + ($item->harga_modal * $item->jumlah_barang_masuk);
							}, 0),
							'remarks_main' => $barangMasuk->remarks,
							'id_detail' => $detail->id,
							'sku' => $detail->unitProduk->sku ?? '',
							'nama_unit' => $detail->nama_unit,
							'jumlah_barang_masuk_detail' => $detail->jumlah_barang_masuk,
							'harga_modal_detail' => $detail->harga_modal,
							'remarks_detail' => $detail->remarks,
						]);
					}
				}
			}
			return $flattenedData;
		} else {
			return $this->query->with('principleSubdealer', 'barangMasukDetails')->get();
		}
	}

	public function headings(): array
	{
		if ($this->includeDetails) {
			return [
				'ID Barang Masuk',
				'No. Barang Masuk',
				'Principle/Subdealer',
				'Tanggal',
				'Total Harga Modal (Main)',
				'Remarks (Main)',
				'ID Detail',
				'SKU',
				'Nama Unit',
				'Jumlah Barang Masuk (Detail)',
				'Harga Modal (Detail)',
				'Remarks (Detail)',
			];
		} else {
			return [
				'ID',
				'No. Barang Masuk',
				'Principle/Subdealer',
				'Tanggal',
				'Total Harga Modal',
				'Remarks',
			];
		}
	}

	public function map($row): array
	{
		if ($this->includeDetails) {
			$currentBarangMasukId = $row['barang_masuk_id'];
			$isNewParent = ($this->lastBarangMasukId !== $currentBarangMasukId);
			$this->lastBarangMasukId = $currentBarangMasukId;

			return [
				$isNewParent ? $row['barang_masuk_id'] : '',
				$isNewParent ? $row['nomor_barang_masuk'] : '',
				$isNewParent ? $row['principle_subdealer_nama'] : '',
				$isNewParent ? $row['tanggal_barang_masuk'] : '',
				$isNewParent ? $row['total_harga_modal_main'] : '',
				$isNewParent ? $row['remarks_main'] : '',
				$row['id_detail'],
				$row['sku'],
				$row['nama_unit'],
				$row['jumlah_barang_masuk_detail'],
				$row['harga_modal_detail'],
				$row['remarks_detail'],
			];
		} else {
			$totalHargaModal = $row->barangMasukDetails->reduce(function ($carry, $detail) {
				return $carry + ($detail->harga_modal * $detail->jumlah_barang_masuk);
			}, 0);

			return [
				$row->id,
				$row->nomor_barang_masuk,
				$row->principleSubdealer->nama,
				Carbon::parse($row->tanggal)->format('Y-m-d'),
				$totalHargaModal,
				$row->remarks,
			];
		}
	}
}