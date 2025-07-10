<?php

namespace App\Filament\Resources\UtangResource\Pages;

use App\Filament\Resources\UtangResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUtang extends CreateRecord
{
    protected static string $resource = UtangResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
