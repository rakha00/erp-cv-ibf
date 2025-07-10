<?php

namespace App\Filament\Resources\SettingResource\Pages;

use App\Filament\Resources\SettingResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSetting extends EditRecord
{
    protected static string $resource = SettingResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->visible(fn (\App\Models\Setting $record): bool => ! in_array($record->key, ['karyawan_jabatan_options', 'karyawan_status_options', 'bank_accounts'])),
        ];
    }

    protected function handleRecordUpdate(\Illuminate\Database\Eloquent\Model $record, array $data): \Illuminate\Database\Eloquent\Model
    {
        // Ensure 'type' is available in $data, falling back to record's type if not present (i.e., not modified)
        $type = $data['type'] ?? $record->type;

        if ($type === 'array' && is_array($data['value'])) {
            $data['value'] = json_encode($data['value']);
        } elseif ($type === 'json' && is_array($data['value'])) {
            $data['value'] = json_encode($data['value']);
        }
        $record->update($data);

        return $record;
    }
}
