<?php

namespace App\Exports;

use App\Models\TransaksiProduk;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class TransaksiProdukExport extends BaseExport implements FromCollection, WithColumnFormatting, WithHeadings, WithMapping
{
    protected $query;

    protected float $totalHargaJualMainAccumulated = 0;

    protected float $totalKeuntunganMainAccumulated = 0;

    protected $includeDetails;

    protected $tahun;

    protected $bulan;

    protected $lastTransaksiProdukId = null; // To track the last TransaksiProduk ID for de-duplication

    private const SUMMARY_ROW_INDICATOR = 'is_summary_row';

    private const TOTAL_LABEL = 'TOTAL';

    public function __construct($query, $resourceTitle = 'Transaksi Produk', $includeDetails = false, $tahun = null, $bulan = null)
    {
        parent::__construct($resourceTitle);
        $this->query = $query;
        $this->includeDetails = $includeDetails;
        $this->tahun = $tahun;
        $this->bulan = $bulan;
        $this->totalHargaJualMainAccumulated = 0;
        $this->totalKeuntunganMainAccumulated = 0;
    }

    public function columnFormats(): array
    {
        return [
            'D' => '"Rp "#,##0',
            'E' => '"Rp "#,##0',
            'J' => '"Rp "#,##0',
            'K' => '"Rp "#,##0',
            'L' => '"Rp "#,##0',
        ];
    }

    public function collection(): \Illuminate\Support\Collection
    {
        $this->totalHargaJualMainAccumulated = 0;
        $this->totalKeuntunganMainAccumulated = 0;

        // Eager load relationships to prevent N+1 queries
        $transaksiProduks = $this->query->with([
            'transaksiProdukDetails.unitProduk' => function ($query) {
                $query->withTrashed();
            },
        ])->get();

        if ($this->includeDetails) {
            return $this->getDetailedCollectionData($transaksiProduks);
        }

        return $this->getNonDetailedCollectionData($transaksiProduks);
    }

    /**
     * Calculates total sales price and profit for a given TransaksiProduk.
     */
    protected function calculateTransaksiProdukTotals(TransaksiProduk $transaksiProduk): array
    {
        $totalHargaJual = 0;
        $totalKeuntungan = 0;

        foreach ($transaksiProduk->transaksiProdukDetails as $detail) {
            $hargaJual = $detail->harga_jual;
            $jumlahKeluar = $detail->jumlah_keluar;
            $hargaModal = $detail->unitProduk ? ($detail->unitProduk->harga_modal ?? 0) : 0; // Safely access harga_modal

            $totalHargaJual += ($hargaJual * $jumlahKeluar);
            $totalKeuntungan += (($hargaJual - $hargaModal) * $jumlahKeluar);
        }

        return [
            'totalHargaJual' => $totalHargaJual,
            'totalKeuntungan' => $totalKeuntungan,
        ];
    }

    protected function getDetailedCollectionData(\Illuminate\Support\Collection $transaksiProduks): \Illuminate\Support\Collection
    {
        $flattenedData = collect();

        foreach ($transaksiProduks as $transaksiProduk) {
            $totals = $this->calculateTransaksiProdukTotals($transaksiProduk);
            $totalHargaJual = $totals['totalHargaJual'];
            $totalKeuntungan = $totals['totalKeuntungan'];

            $this->totalHargaJualMainAccumulated += $totalHargaJual;
            $this->totalKeuntunganMainAccumulated += $totalKeuntungan;

            if ($transaksiProduk->transaksiProdukDetails->isEmpty()) {
                $flattenedData->push([
                    'no_invoice' => $transaksiProduk->no_invoice,
                    'no_surat_jalan' => $transaksiProduk->no_surat_jalan,
                    'tanggal_main' => Carbon::parse($transaksiProduk->tanggal)->format('Y-m-d'),
                    'total_harga_jual_main' => $totalHargaJual,
                    'total_keuntungan_main' => $totalKeuntungan,
                    'remarks_main' => $transaksiProduk->remarks,
                    'sku' => null,
                    'nama_unit' => null,
                    'jumlah_keluar_detail' => null,
                    'harga_jual_detail' => null,
                    'harga_modal_detail' => null,
                    'keuntungan_detail' => null,
                ]);
            } else {
                foreach ($transaksiProduk->transaksiProdukDetails as $detail) {
                    $hargaModal = $detail->unitProduk ? ($detail->unitProduk->harga_modal ?? 0) : 0;
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

        // Add summary row for detailed export
        $flattenedData->push([
            'no_invoice' => self::TOTAL_LABEL,
            'no_surat_jalan' => '',
            'tanggal_main' => '',
            'total_harga_jual_main' => $this->totalHargaJualMainAccumulated,
            'total_keuntungan_main' => $this->totalKeuntunganMainAccumulated,
            'remarks_main' => '',
            'sku' => null,
            'nama_unit' => null,
            'jumlah_keluar_detail' => null,
            'harga_jual_detail' => null,
            'harga_modal_detail' => null,
            'keuntungan_detail' => null,
            self::SUMMARY_ROW_INDICATOR => true,
        ]);

        return $flattenedData;
    }

    protected function getNonDetailedCollectionData(\Illuminate\Support\Collection $transaksiProduks): \Illuminate\Support\Collection
    {
        $processedData = $transaksiProduks->map(function ($transaksiProduk) {
            $totals = $this->calculateTransaksiProdukTotals($transaksiProduk);
            $totalHargaJual = $totals['totalHargaJual'];
            $totalKeuntungan = $totals['totalKeuntungan'];

            $this->totalHargaJualMainAccumulated += $totalHargaJual;
            $this->totalKeuntunganMainAccumulated += $totalKeuntungan;

            return [
                'no_invoice' => $transaksiProduk->no_invoice,
                'no_surat_jalan' => $transaksiProduk->no_surat_jalan,
                'tanggal' => Carbon::parse($transaksiProduk->tanggal)->format('Y-m-d'),
                'total_harga_jual' => $totalHargaJual,
                'total_keuntungan' => $totalKeuntungan,
                'remarks' => $transaksiProduk->remarks,
            ];
        });

        // Add summary row for non-detailed export
        $processedData->push([
            'no_invoice' => self::TOTAL_LABEL,
            'no_surat_jalan' => '',
            'tanggal' => '',
            'total_harga_jual' => $this->totalHargaJualMainAccumulated,
            'total_keuntungan' => $this->totalKeuntunganMainAccumulated,
            'remarks' => '',
            self::SUMMARY_ROW_INDICATOR => true,
        ]);

        return $processedData;
    }

    public function headings(): array
    {
        if ($this->includeDetails) {
            return [
                'No Invoice',
                'No Surat Jalan',
                'Tanggal',
                'Total Harga Jual (Main)',
                'Total Keuntungan (Main)',
                'Remarks (Main)',
                'SKU',
                'Nama Unit',
                'Jumlah Keluar (Detail)',
                'Harga Jual (Detail)',
                'Harga Modal (Detail)',
                'Keuntungan (Detail)',
            ];
        }

        return [
            'No Invoice',
            'No Surat Jalan',
            'Tanggal',
            'Total Harga Jual',
            'Total Keuntungan',
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
                self::TOTAL_LABEL, // No Invoice
                '', // No Surat Jalan
                '', // Tanggal
                $row['total_harga_jual_main'], // Total Harga Jual (Main)
                $row['total_keuntungan_main'], // Total Keuntungan (Main)
                '', // Remarks (Main)
                '', // SKU
                '', // Nama Unit
                '', // Jumlah Keluar (Detail)
                '', // Harga Jual (Detail)
                '', // Harga Modal (Detail)
                '', // Keuntungan (Detail)
            ];
        }

        return [
            self::TOTAL_LABEL, // No Invoice
            '', // No Surat Jalan
            '', // Tanggal
            $row['total_harga_jual'], // Total Harga Jual
            $row['total_keuntungan'], // Total Keuntungan
            '', // Remarks
        ];
    }

    protected function mapRegularRow(array $row): array
    {
        if ($this->includeDetails) {
            $currentTransaksiProdukId = $row['transaksi_produk_id'];
            $isNewParent = ($this->lastTransaksiProdukId !== $currentTransaksiProdukId);
            $this->lastTransaksiProdukId = $currentTransaksiProdukId;

            return [
                $isNewParent ? $row['no_invoice'] : '',
                $isNewParent ? $row['no_surat_jalan'] : '',
                $isNewParent ? $row['tanggal_main'] : '',
                $isNewParent ? $row['total_harga_jual_main'] : '',
                $isNewParent ? $row['total_keuntungan_main'] : '',
                $isNewParent ? $row['remarks_main'] : '',
                $row['sku'],
                $row['nama_unit'],
                $row['jumlah_keluar_detail'],
                $row['harga_jual_detail'],
                $row['harga_modal_detail'],
                $row['keuntungan_detail'],
            ];
        }

        // For non-detailed export, $row is already an array with calculated totals
        return [
            $row['no_invoice'],
            $row['no_surat_jalan'],
            $row['tanggal'],
            $row['total_harga_jual'],
            $row['total_keuntungan'],
            $row['remarks'],
        ];
    }
}
