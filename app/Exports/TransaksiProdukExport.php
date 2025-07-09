<?php

namespace App\Exports;

use App\Models\TransaksiProduk;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Carbon\Carbon;

class TransaksiProdukExport extends BaseExport implements FromCollection, WithHeadings, WithMapping
{
	protected $query;
	protected $includeDetails;
	protected $tahun;
	protected $bulan;
	protected $lastTransaksiProdukId = null; // To track the last TransaksiProduk ID for de-duplication

	public function __construct($query, $resourceTitle = 'Transaksi Produk', $includeDetails = false, $tahun = null, $bulan = null)
	{
		parent::__construct($resourceTitle);
		$this->query = $query;
		$this->includeDetails = $includeDetails;
		$this->tahun = $tahun;
		$this->bulan = $bulan;
	}

	/**
	 * @return \Illuminate\Support\Collection
	 */
	public function collection()
	{
		if ($this->includeDetails) {
			$transaksiProduks = $this->query->with('transaksiProdukDetails.unitProduk')->get();
			$flattenedData = collect();

			foreach ($transaksiProduks as $transaksiProduk) {
				$totalHargaJual = $transaksiProduk->transaksiProdukDetails->reduce(function ($carry, $detail) {
					return $carry + ($detail->harga_jual * $detail->jumlah_keluar);
				}, 0);

				$totalKeuntungan = $transaksiProduk->transaksiProdukDetails->reduce(function ($carry, $detail) {
					$unitProduk = $detail->unitProduk()->withTrashed()->first();
					$hargaModal = $unitProduk->harga_modal ?? 0;
					return $carry + (($detail->harga_jual - $hargaModal) * $detail->jumlah_keluar);
				}, 0);

				if ($transaksiProduk->transaksiProdukDetails->isEmpty()) {
					$flattenedData->push([
						'transaksi_produk_id' => $transaksiProduk->id,
						'no_invoice' => $transaksiProduk->no_invoice,
						'no_surat_jalan' => $transaksiProduk->no_surat_jalan,
						'tanggal_main' => Carbon::parse($transaksiProduk->tanggal)->format('Y-m-d'),
						'total_harga_jual_main' => $totalHargaJual,
						'total_keuntungan_main' => $totalKeuntungan,
						'remarks_main' => $transaksiProduk->remarks,
						'id_detail' => null,
						'sku' => null,
						'nama_unit' => null,
						'jumlah_keluar_detail' => null,
						'harga_jual_detail' => null,
						'harga_modal_detail' => null,
						'keuntungan_detail' => null,
					]);
				} else {
					foreach ($transaksiProduk->transaksiProdukDetails as $detail) {
						$unitProduk = $detail->unitProduk()->withTrashed()->first();
						$hargaModal = $unitProduk->harga_modal ?? 0;
						$keuntunganDetail = ($detail->harga_jual - $hargaModal) * $detail->jumlah_keluar;
						$flattenedData->push([
							'transaksi_produk_id' => $transaksiProduk->id,
							'no_invoice' => $transaksiProduk->no_invoice,
							'no_surat_jalan' => $transaksiProduk->no_surat_jalan,
							'tanggal_main' => Carbon::parse($transaksiProduk->tanggal)->format('Y-m-d'),
							'total_harga_jual_main' => $totalHargaJual,
							'total_keuntungan_main' => $totalKeuntungan,
							'remarks_main' => $transaksiProduk->remarks,
							'id_detail' => $detail->id,
							'sku' => $detail->unitProduk->sku ?? '',
							'nama_unit' => $detail->nama_unit,
							'jumlah_keluar_detail' => $detail->jumlah_keluar,
							'harga_jual_detail' => $detail->harga_jual,
							'harga_modal_detail' => $hargaModal,
							'keuntungan_detail' => $keuntunganDetail,
						]);
					}
				}
			}
			return $flattenedData;
		} else {
			return $this->query->with('transaksiProdukDetails.unitProduk')->get();
		}
	}

	public function headings(): array
	{
		if ($this->includeDetails) {
			return [
				'ID Transaksi',
				'No Invoice',
				'No Surat Jalan',
				'Tanggal',
				'Total Harga Jual (Main)',
				'Total Keuntungan (Main)',
				'Remarks (Main)',
				'ID Detail',
				'SKU',
				'Nama Unit',
				'Jumlah Keluar (Detail)',
				'Harga Jual (Detail)',
				'Harga Modal (Detail)',
				'Keuntungan (Detail)',
			];
		} else {
			return [
				'ID',
				'No Invoice',
				'No Surat Jalan',
				'Tanggal',
				'Total Harga Jual',
				'Total Keuntungan',
				'Remarks',
			];
		}
	}

	public function map($row): array
	{
		if ($this->includeDetails) {
			$currentTransaksiProdukId = $row['transaksi_produk_id'];
			$isNewParent = ($this->lastTransaksiProdukId !== $currentTransaksiProdukId);
			$this->lastTransaksiProdukId = $currentTransaksiProdukId;

			return [
				$isNewParent ? $row['transaksi_produk_id'] : '',
				$isNewParent ? $row['no_invoice'] : '',
				$isNewParent ? $row['no_surat_jalan'] : '',
				$isNewParent ? $row['tanggal_main'] : '',
				$isNewParent ? $row['total_harga_jual_main'] : '',
				$isNewParent ? $row['total_keuntungan_main'] : '',
				$isNewParent ? $row['remarks_main'] : '',
				$row['id_detail'],
				$row['sku'],
				$row['nama_unit'],
				$row['jumlah_keluar_detail'],
				$row['harga_jual_detail'],
				$row['harga_modal_detail'],
				$row['keuntungan_detail'],
			];
		} else {
			$totalHargaJual = $row->transaksiProdukDetails->reduce(function ($carry, $detail) {
				return $carry + ($detail->harga_jual * $detail->jumlah_keluar);
			}, 0);

			$totalKeuntungan = $row->transaksiProdukDetails->reduce(function ($carry, $detail) {
				$unitProduk = $detail->unitProduk()->withTrashed()->first();
				$hargaModal = $unitProduk->harga_modal ?? 0;
				return $carry + (($detail->harga_jual - $hargaModal) * $detail->jumlah_keluar);
			}, 0);

			return [
				$row->id,
				$row->no_invoice,
				$row->no_surat_jalan,
				Carbon::parse($row->tanggal)->format('Y-m-d'),
				$totalHargaJual,
				$totalKeuntungan,
				$row->remarks,
			];
		}
	}
}