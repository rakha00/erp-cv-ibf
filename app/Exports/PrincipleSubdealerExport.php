<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PrincipleSubdealerExport extends BaseExport implements FromCollection, WithHeadings, WithMapping
{
    protected $query;

    public function __construct($query, $resourceTitle = 'Principle/Subdealer')
    {
        parent::__construct($resourceTitle);
        $this->query = $query;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return $this->query->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Nama',
            'Sales',
            'No HP',
            'Remarks',
        ];
    }

    public function map($principleSubdealer): array
    {
        return [
            $principleSubdealer->id,
            $principleSubdealer->nama,
            $principleSubdealer->sales,
            $principleSubdealer->no_hp,
            $principleSubdealer->remarks,
        ];
    }
}
