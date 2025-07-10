<?php

namespace App\Exports;

use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class UtangExport extends BaseExport implements FromCollection, WithHeadings, WithMapping
{
    protected $query;

    public function __construct($query, $resourceTitle = 'Utang')
    {
        parent::__construct($resourceTitle);
        $this->query = $query;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return $this->query->with('barangMasuk.principleSubdealer')->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'No. Barang Masuk',
            'Tanggal Barang Masuk',
            'Jatuh Tempo',
            'Status Pembayaran',
            'Sudah Dibayar',
            'Total Harga Modal',
            'Sisa Hutang',
            'Remarks',
        ];
    }

    public function map($utang): array
    {
        $totalHutang = (float) str_replace(',', '', $utang->total_harga_modal ?? '0');
        $sudahDibayar = (float) ($utang->sudah_dibayar ?? '0');
        $sisaHutang = $totalHutang - $sudahDibayar;

        return [
            $utang->id,
            $utang->barangMasuk->nomor_barang_masuk ?? '',
            Carbon::parse($utang->barangMasuk->tanggal ?? '')->format('Y-m-d'),
            Carbon::parse($utang->jatuh_tempo)->format('Y-m-d'),
            ucwords($utang->status_pembayaran),
            $utang->sudah_dibayar,
            $utang->total_harga_modal,
            $sisaHutang,
            $utang->remarks,
        ];
    }
}
