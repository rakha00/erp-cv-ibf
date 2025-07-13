<?php

namespace App\Exports;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AsetExport extends BaseExport implements FromCollection, WithColumnFormatting, WithHeadings, WithMapping
{
    /**
     * @var \Illuminate\Database\Eloquent\Builder The Eloquent query builder instance.
     */
    protected Builder $query;

    /**
     * AsetExport constructor.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query  The query to retrieve asset data.
     * @param  string  $resourceTitle  The title for the exported resource.
     */
    public function __construct(Builder $query, string $resourceTitle = 'Daftar Aset')
    {
        parent::__construct($resourceTitle);
        $this->query = $query;
    }

    /**
     * Defines column formatting for the Excel sheet.
     *
     * @return array An associative array where keys are column letters and values are format strings.
     */
    public function columnFormats(): array
    {
        return [
            'B' => '"Rp "#,##0', // Format for 'Harga' column
            'D' => '"Rp "#,##0', // Format for 'Total Harga Aset' column
        ];
    }

    /**
     * Retrieves the data collection for the Excel export.
     *
     * This method fetches the asset data, calculates the total price for each asset,
     * and appends a summary row with the overall total.
     *
     * @return \Illuminate\Support\Collection The processed data collection.
     */
    public function collection(): Collection
    {
        // Fetch only necessary columns to optimize memory usage
        $assets = $this->query->select('nama_aset', 'harga', 'jumlah_aset')->get();

        // Process each asset to calculate total price per item
        $processedData = $assets->map(function ($aset) {
            // Explicitly cast to float to ensure correct numeric operations
            $harga = (float) $aset->harga;
            $jumlah_aset = (float) $aset->jumlah_aset;
            $total_harga_aset_item = $harga * $jumlah_aset;

            return (object) [
                'nama_aset' => $aset->nama_aset,
                'harga' => $harga,
                'jumlah_aset' => $jumlah_aset,
                'total_harga_aset' => $total_harga_aset_item,
            ];
        });

        // Calculate the grand total of all asset prices
        $totalHargaAset = $processedData->sum('total_harga_aset');

        // Add a summary row at the end of the collection
        $processedData->push((object) [
            'nama_aset' => 'TOTAL',
            'harga' => '', // Empty for summary row
            'jumlah_aset' => '', // Empty for summary row
            'total_harga_aset' => $totalHargaAset,
            'is_summary_row' => true, // Flag to identify the summary row in the map method
        ]);

        return $processedData;
    }

    /**
     * Defines the headings for the Excel columns.
     *
     * @return array An array of strings representing the column headings.
     */
    public function headings(): array
    {
        return [
            'Nama Aset',
            'Harga',
            'Jumlah Aset',
            'Total Harga Aset',
        ];
    }

    /**
     * Maps each data row to an array suitable for Excel.
     *
     * @param  object  $aset  The asset object (or summary row object).
     * @return array An array of values for a single row in the Excel sheet.
     */
    public function map($aset): array
    {
        // Check if the current row is the summary row
        if (isset($aset->is_summary_row) && $aset->is_summary_row) {
            return [
                $aset->nama_aset, // Will be 'TOTAL'
                '', // Leave 'Harga' column empty for summary
                '', // Leave 'Jumlah Aset' column empty for summary
                $aset->total_harga_aset, // Display the grand total
            ];
        }

        // For regular data rows
        return [
            $aset->nama_aset,
            $aset->harga,
            $aset->jumlah_aset,
            $aset->total_harga_aset,
        ];
    }
}
