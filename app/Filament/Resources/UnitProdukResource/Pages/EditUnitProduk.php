<?php

namespace App\Filament\Resources\UnitProdukResource\Pages;

use App\Filament\Resources\UnitProdukResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUnitProduk extends EditRecord
{
    protected static string $resource = UnitProdukResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
