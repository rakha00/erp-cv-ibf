<?php

namespace App\Filament\Resources\UnitProdukResource\Pages;

use App\Filament\Resources\UnitProdukResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateUnitProduk extends CreateRecord
{
    protected static string $resource = UnitProdukResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
