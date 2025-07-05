<?php

namespace App\Filament\Resources\UnitProdukResource\Pages;

use App\Filament\Resources\UnitProdukResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUnitProduks extends ListRecords
{
    protected static string $resource = UnitProdukResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
