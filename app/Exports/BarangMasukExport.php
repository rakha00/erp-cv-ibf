<?php

namespace App\Exports;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;

class BarangMasukExport extends BaseExport implements FromCollection, WithColumnFormatting, WithHeadings, WithMapping, WithStrictNullComparison
{
    protected $query;

    protected float $totalHargaModalMainAccumulated = 0;

    protected float $totalHargaModalDetailAccumulated = 0;

    protected bool $includeDetails;

    protected ?int $lastBarangMasukId = null; // To track the last Barang Masuk ID for de-duplication

    private const SUMMARY_ROW_INDICATOR = 'is_summary_row';

    private const TOTAL_LABEL = 'TOTAL';

    public function __construct($query, $resourceTitle = 'Barang Masuk', bool $includeDetails = false)
    {
        parent::__construct($resourceTitle);
        $this->query = $query;
        $this->includeDetails = $includeDetails;
    }

    /**
     * Defines column formatting for the Excel sheet.
     */
    public function columnFormats(): array
    {
        if ($this->includeDetails) {
            return [
                'D' => '"Rp "#,##0', // total_harga_modal_main
                'I' => '"Rp "#,##0', // harga_modal_detail
            ];
        }

        return [
            'D' => '"Rp "#,##0', // Total Harga Modal
        ];
    }

    public function collection(): Collection
    {
        $this->totalHargaModalMainAccumulated = 0;
        $this->totalHargaModalDetailAccumulated = 0;

        if ($this->includeDetails) {
            return $this->getDetailedCollectionData();
        }

        return $this->getNonDetailedCollectionData();
    }

    protected function getDetailedCollectionData(): Collection
    {
        $barangMasuks = $this->query->with([
            'principleSubdealer' => fn ($query) => $query->withTrashed(),
            'barangMasukDetails.unitProduk' => fn ($query) => $query->withTrashed(),
        ])->get();

        $flattenedData = collect();

        foreach ($barangMasuks as $barangMasuk) {
            $totalHargaModalMain = $barangMasuk->barangMasukDetails->reduce(function ($carry, $detail) {
                return $carry + ($detail->harga_modal * $detail->jumlah_barang_masuk);
            }, 0);
            $this->totalHargaModalMainAccumulated += $totalHargaModalMain;

            $principleSubdealerName = $barangMasuk->principleSubdealer->nama ?? '';

            if ($barangMasuk->barangMasukDetails->isEmpty()) {
                $flattenedData->push([
                    'barang_masuk_id' => $barangMasuk->id,
                    'nomor_barang_masuk' => $barangMasuk->nomor_barang_masuk,
                    'principle_subdealer_nama' => $principleSubdealerName,
                    'tanggal_barang_masuk' => Carbon::parse($barangMasuk->tanggal)->format('Y-m-d'),
                    'total_harga_modal_main' => $totalHargaModalMain,
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
                    $this->totalHargaModalDetailAccumulated += ($detail->harga_modal * $detail->jumlah_barang_masuk);
                    $flattenedData->push([
                        'barang_masuk_id' => $barangMasuk->id,
                        'nomor_barang_masuk' => $barangMasuk->nomor_barang_masuk,
                        'principle_subdealer_nama' => $principleSubdealerName,
                        'tanggal_barang_masuk' => Carbon::parse($barangMasuk->tanggal)->format('Y-m-d'),
                        'total_harga_modal_main' => $totalHargaModalMain,
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

        // Add summary row for details export
        $flattenedData->push([
            'nomor_barang_masuk' => self::TOTAL_LABEL,
            'principle_subdealer_nama' => '',
            'tanggal_barang_masuk' => '',
            'total_harga_modal_main' => $this->totalHargaModalMainAccumulated,
            'remarks_main' => '',
            'id_detail' => '',
            'sku' => '',
            'nama_unit' => '',
            'jumlah_barang_masuk_detail' => '',
            'harga_modal_detail' => $this->totalHargaModalDetailAccumulated,
            'remarks_detail' => '',
            self::SUMMARY_ROW_INDICATOR => true,
        ]);

        return $flattenedData;
    }

    protected function getNonDetailedCollectionData(): Collection
    {
        $barangMasuks = $this->query->with([
            'principleSubdealer' => fn ($query) => $query->withTrashed(),
            'barangMasukDetails',
        ])->get();

        $processedData = $barangMasuks->map(function ($barangMasuk) {
            $totalHargaModal = $barangMasuk->barangMasukDetails->reduce(function ($carry, $detail) {
                return $carry + ($detail->harga_modal * $detail->jumlah_barang_masuk);
            }, 0);
            $this->totalHargaModalMainAccumulated += $totalHargaModal;

            $principleSubdealerName = $barangMasuk->principleSubdealer->nama ?? '';

            return [
                'nomor_barang_masuk' => $barangMasuk->nomor_barang_masuk,
                'principle_subdealer_nama' => $principleSubdealerName,
                'tanggal_barang_masuk' => Carbon::parse($barangMasuk->tanggal)->format('Y-m-d'),
                'total_harga_modal' => $totalHargaModal,
                'remarks' => $barangMasuk->remarks,
            ];
        });

        // Add summary row for non-details export
        $processedData->push([
            'nomor_barang_masuk' => self::TOTAL_LABEL,
            'principle_subdealer_nama' => '',
            'tanggal_barang_masuk' => '',
            'total_harga_modal' => $this->totalHargaModalMainAccumulated,
            'remarks' => '',
            self::SUMMARY_ROW_INDICATOR => true,
        ]);

        return $processedData;
    }

    public function headings(): array
    {
        if ($this->includeDetails) {
            return [
                'No. Barang Masuk',
                'Principle/Subdealer',
                'Tanggal',
                'Total Harga Modal (Main)',
                'Remarks (Main)',
                'SKU',
                'Nama Unit',
                'Jumlah Barang Masuk (Detail)',
                'Harga Modal (Detail)',
                'Remarks (Detail)',
            ];
        }

        return [
            'No. Barang Masuk',
            'Principle/Subdealer',
            'Tanggal',
            'Total Harga Modal',
            'Remarks',
        ];
    }

    public function map($row): array
    {
        // Handle summary row
        if (isset($row[self::SUMMARY_ROW_INDICATOR]) && $row[self::SUMMARY_ROW_INDICATOR]) {
            return $this->mapSummaryRow($row);
        }

        // Regular data row
        return $this->mapRegularRow($row);
    }

    protected function mapSummaryRow(array $row): array
    {
        if ($this->includeDetails) {
            return [
                self::TOTAL_LABEL, // No. Barang Masuk
                '', // Principle/Subdealer
                '', // Tanggal
                $row['total_harga_modal_main'], // Total Harga Modal (Main)
                '', // Remarks (Main)
                '', // SKU
                '', // Nama Unit
                '', // Jumlah Barang Masuk (Detail)
                '', // Harga Modal (Detail)
                '', // Remarks (Detail)
            ];
        }

        return [
            self::TOTAL_LABEL, // No. Barang Masuk
            '', // Principle/Subdealer
            '', // Tanggal
            $row['total_harga_modal'], // Total Harga Modal
            '', // Remarks
        ];
    }

    protected function mapRegularRow(array $row): array
    {
        if ($this->includeDetails) {
            $currentBarangMasukId = $row['barang_masuk_id'];
            $isNewParent = ($this->lastBarangMasukId !== $currentBarangMasukId);
            $this->lastBarangMasukId = $currentBarangMasukId;

            return [
                $isNewParent ? $row['nomor_barang_masuk'] : '',
                $isNewParent ? $row['principle_subdealer_nama'] : '',
                $isNewParent ? $row['tanggal_barang_masuk'] : '',
                $isNewParent ? $row['total_harga_modal_main'] : '',
                $isNewParent ? $row['remarks_main'] : '',
                $row['sku'],
                $row['nama_unit'],
                $row['jumlah_barang_masuk_detail'],
                $row['harga_modal_detail'],
                $row['remarks_detail'],
            ];
        }

        return [
            $row['nomor_barang_masuk'],
            $row['principle_subdealer_nama'],
            $row['tanggal_barang_masuk'],
            $row['total_harga_modal'],
            $row['remarks'],
        ];
    }
}
