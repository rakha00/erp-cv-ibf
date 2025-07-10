<?php

namespace App\Filament\Resources\SettingResource\Pages;

use App\Filament\Resources\SettingResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSetting extends CreateRecord
{
    protected static string $resource = SettingResource::class;

    protected function handleRecordCreate(array $data): \Illuminate\Database\Eloquent\Model
    {
        // Ensure 'type' is always available in $data for new records
        $type = $data['type'];

        if ($type === 'array' && is_array($data['value'])) {
            $data['value'] = json_encode($data['value']);
        } elseif ($type === 'json' && is_array($data['value'])) {
            $data['value'] = json_encode($data['value']);
        }

        return static::getModel()::create($data);
    }
}
