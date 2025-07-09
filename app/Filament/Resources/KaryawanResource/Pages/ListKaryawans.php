<?php

namespace App\Filament\Resources\KaryawanResource\Pages;

use App\Filament\Resources\KaryawanResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

use Illuminate\Database\Eloquent\Builder;

class ListKaryawans extends ListRecords
{
    protected static string $resource = KaryawanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getTableQuery(): Builder
    {
        $query = parent::getTableQuery();
        $tahun = $this->tableFilters['tahun']['value'] ?? date('Y');
        $bulan = $this->tableFilters['bulan']['value'] ?? date('n');

        return $query->with(['penghasilanKaryawanDetails' => fn($q) => $q->whereYear('tanggal', $tahun)->whereMonth('tanggal', $bulan)]);
    }
}
