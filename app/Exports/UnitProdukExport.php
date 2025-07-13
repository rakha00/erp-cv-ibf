<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;

class UnitProdukExport extends BaseExport implements FromCollection, WithColumnFormatting, WithHeadings, WithMapping, WithStrictNullComparison
{
    protected $query;

    public function __construct($query, $resourceTitle = 'Unit Produk')
    {
        parent::__construct($resourceTitle);
        $this->query = $query;
    }

    /**
     * Defines column formatting for the Excel sheet.
     */
    public function columnFormats(): array
    {
        return [
            'C' => '"Rp "#,##0',
        ];
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

    /**
     * Maps a unit produk to an array for the Excel row.
     *
     * @param  \App\Models\UnitProduk  $unitProduk
     */
    public function map($unitProduk): array
    {
        $stokMasuk = $unitProduk->barangMasukDetails->sum('jumlah_barang_masuk');
        $stokKeluar = $unitProduk->transaksiProdukDetails->sum('jumlah_keluar');
        $stokAkhir = $unitProduk->stok_awal + $stokMasuk - $stokKeluar;

        return [
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
