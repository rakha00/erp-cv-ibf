<?php

namespace App\Exports;

use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PiutangExport extends BaseExport implements FromCollection, WithHeadings, WithMapping
{
    protected $query;

    public function __construct($query, $resourceTitle = 'Piutang')
    {
        parent::__construct($resourceTitle);
        $this->query = $query;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return $this->query->with('transaksiProduk')->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'No. Transaksi',
            'Tanggal Transaksi',
            'Jatuh Tempo',
            'Status Pembayaran',
            'Sudah Dibayar',
            'Total Harga Modal',
            'Sisa Piutang',
            'Remarks',
        ];
    }

    public function map($piutang): array
    {
        $totalPiutang = (float) str_replace(',', '', $piutang->total_harga_modal ?? '0');
        $sudahDibayar = (float) ($piutang->sudah_dibayar ?? '0');
        $sisaPiutang = $totalPiutang - $sudahDibayar;

        return [
            $piutang->id,
            $piutang->transaksiProduk->no_invoice ?? '',
            Carbon::parse($piutang->transaksiProduk->tanggal ?? '')->format('Y-m-d'),
            Carbon::parse($piutang->jatuh_tempo)->format('Y-m-d'),
            ucwords($piutang->status_pembayaran),
            $piutang->sudah_dibayar,
            $piutang->total_harga_modal,
            $sisaPiutang,
            $piutang->remarks,
        ];
    }
}
