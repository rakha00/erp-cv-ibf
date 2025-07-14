<?php

namespace App\Exports;

use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;

class PiutangExport extends BaseExport implements FromCollection, WithColumnFormatting, WithHeadings, WithMapping, WithStrictNullComparison
{
    protected $query;

    protected float $totalSudahDibayarAccumulated = 0;

    protected float $totalTotalHargaModalAccumulated = 0;

    protected float $totalSisaPiutangAccumulated = 0;

    private const SUMMARY_ROW_INDICATOR = 'is_summary_row';

    private const TOTAL_LABEL = 'TOTAL';

    public function __construct($query, $resourceTitle = 'Piutang')
    {
        parent::__construct($resourceTitle);
        $this->query = $query;
    }

    public function columnFormats(): array
    {
        return [
            'E' => '"Rp "#,##0',
            'F' => '"Rp "#,##0',
            'G' => '"Rp "#,##0',
        ];
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $this->totalSudahDibayarAccumulated = 0;
        $this->totalTotalHargaModalAccumulated = 0;
        $this->totalSisaPiutangAccumulated = 0;

        $piutangs = $this->query->with([
            'transaksiProduk' => function ($query) {
                $query->withTrashed();
            },
        ])->get();

        foreach ($piutangs as $piutang) {
            $totalPiutang = (float) str_replace(',', '', $piutang->total_harga_modal ?? '0');
            $sudahDibayar = (float) ($piutang->sudah_dibayar ?? '0');
            $sisaPiutang = $totalPiutang - $sudahDibayar;

            $this->totalSudahDibayarAccumulated += $sudahDibayar;
            $this->totalTotalHargaModalAccumulated += $totalPiutang;
            $this->totalSisaPiutangAccumulated += $sisaPiutang;
        }

        $piutangs->push([
            'sudah_dibayar' => $this->totalSudahDibayarAccumulated,
            'total_harga_modal' => $this->totalTotalHargaModalAccumulated,
            'sisa_piutang' => $this->totalSisaPiutangAccumulated,
            self::SUMMARY_ROW_INDICATOR => true,
        ]);

        return $piutangs;
    }

    public function headings(): array
    {
        return [
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
        if (isset($piutang[self::SUMMARY_ROW_INDICATOR]) && $piutang[self::SUMMARY_ROW_INDICATOR]) {
            return $this->mapSummaryRow($piutang);
        }

        $totalPiutang = (float) str_replace(',', '', $piutang->total_harga_modal ?? '0');
        $sudahDibayar = (float) ($piutang->sudah_dibayar ?? '0');
        $sisaPiutang = $totalPiutang - $sudahDibayar;

        return [
            $piutang->transaksiProduk->no_invoice ?? 'N/A (Deleted)',
            Carbon::parse($piutang->transaksiProduk->tanggal ?? '')->format('Y-m-d'),
            Carbon::parse($piutang->jatuh_tempo)->format('Y-m-d'),
            ucwords($piutang->status_pembayaran),
            $piutang->sudah_dibayar,
            $piutang->total_harga_modal,
            $sisaPiutang,
            $piutang->remarks,
        ];
    }

    protected function mapSummaryRow(array $row): array
    {
        return [
            self::TOTAL_LABEL, // No. Transaksi
            '', // Tanggal Transaksi
            '', // Jatuh Tempo
            '', // Status Pembayaran
            $row['sudah_dibayar'], // Sudah Dibayar
            $row['total_harga_modal'], // Total Harga Modal
            $row['sisa_piutang'], // Sisa Piutang
            '', // Remarks
        ];
    }
}
