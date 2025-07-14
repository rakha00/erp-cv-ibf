<?php

namespace App\Exports;

use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;

class UtangExport extends BaseExport implements FromCollection, WithHeadings, WithMapping, WithColumnFormatting, WithStrictNullComparison
{
    protected $query;

    protected float $totalSudahDibayarAccumulated = 0;
    protected float $totalTotalHargaModalAccumulated = 0;
    protected float $totalSisaHutangAccumulated = 0;

    private const SUMMARY_ROW_INDICATOR = 'is_summary_row';
    private const TOTAL_LABEL = 'TOTAL';

    public function __construct($query, $resourceTitle = 'Utang')
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
        $this->totalSisaHutangAccumulated = 0;

        $utangs = $this->query->with([
            'barangMasuk' => function ($query) {
                $query->withTrashed()->with('principleSubdealer');
            }
        ])->get();

        foreach ($utangs as $utang) {
            $totalHutang = (float) str_replace(',', '', $utang->total_harga_modal ?? '0');
            $sudahDibayar = (float) ($utang->sudah_dibayar ?? '0');
            $sisaHutang = $totalHutang - $sudahDibayar;

            $this->totalSudahDibayarAccumulated += $sudahDibayar;
            $this->totalTotalHargaModalAccumulated += $totalHutang;
            $this->totalSisaHutangAccumulated += $sisaHutang;
        }

        $utangs->push([
            'sudah_dibayar' => $this->totalSudahDibayarAccumulated,
            'total_harga_modal' => $this->totalTotalHargaModalAccumulated,
            'sisa_hutang' => $this->totalSisaHutangAccumulated,
            self::SUMMARY_ROW_INDICATOR => true,
        ]);

        return $utangs;
    }

    public function headings(): array
    {
        return [
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
        if (isset($utang[self::SUMMARY_ROW_INDICATOR]) && $utang[self::SUMMARY_ROW_INDICATOR]) {
            return $this->mapSummaryRow($utang);
        }

        $totalHutang = (float) str_replace(',', '', $utang->total_harga_modal ?? '0');
        $sudahDibayar = (float) ($utang->sudah_dibayar ?? '0');
        $sisaHutang = $totalHutang - $sudahDibayar;

        return [
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

    protected function mapSummaryRow(array $row): array
    {
        return [
            self::TOTAL_LABEL, // No. Barang Masuk
            '', // Tanggal Barang Masuk
            '', // Jatuh Tempo
            '', // Status Pembayaran
            $row['sudah_dibayar'], // Sudah Dibayar
            $row['total_harga_modal'], // Total Harga Modal
            $row['sisa_hutang'], // Sisa Hutang
            '', // Remarks
        ];
    }
}
