<?php

namespace App\Exports;

use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AsetExport extends BaseExport implements FromCollection, WithColumnFormatting, WithHeadings, WithMapping
{
    protected $query;

    public function __construct(Builder $query, string $resourceTitle = 'Daftar Aset')
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
            'B' => '"Rp "#,##0',
        ];
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return $this->query->select('nama_aset', 'harga', 'jumlah_aset')->get();
    }

    public function headings(): array
    {
        return [
            'Nama Aset',
            'Harga',
            'Jumlah Aset',
        ];
    }

    public function map($aset): array
    {
        return [
            $aset->nama_aset,
            $aset->harga,
            $aset->jumlah_aset,
        ];
    }
}
