<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SettingResource\Pages;
use App\Models\Setting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SettingResource extends Resource
{
    protected static ?string $model = Setting::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationGroup = 'Pengaturan';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('key')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255)
                    ->hidden(fn (string $operation): bool => $operation === 'edit' || $operation === 'create'),
                Forms\Components\TextInput::make('key_label')
                    ->label('Setting Name')
                    ->required()
                    ->maxLength(255)
                    ->disabled(fn (string $operation): bool => $operation === 'edit'),
                Forms\Components\Select::make('type')
                    ->options([
                        'string' => 'String',
                        'json' => 'JSON',
                        'array' => 'Array (Comma Separated)',
                    ])
                    ->required()
                    ->native(false)
                    ->live()
                    ->hidden(fn (string $operation): bool => $operation === 'edit' || $operation === 'create')
                    ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get) {
                        if ($state === 'array') {
                            $currentValue = $get('value');
                            if (is_string($currentValue) && ! empty($currentValue)) {
                                $arrayValues = explode(',', $currentValue);
                                $set('value', collect($arrayValues)->map(fn ($item) => ['item' => trim($item)])->toArray());
                            } else {
                                $set('value', []);
                            }
                        } elseif ($state === 'json') {
                            $currentValue = $get('value');
                            if (is_array($currentValue)) {
                                $set('value', json_encode(collect($currentValue)->pluck('item')->toArray()));
                            }
                        } else {
                            $currentValue = $get('value');
                            if (is_array($currentValue)) {
                                $set('value', implode(',', collect($currentValue)->pluck('item')->toArray()));
                            } elseif (is_string($currentValue) && ($decoded = json_decode($currentValue, true)) !== null) {
                                $set('value', $currentValue);
                            }
                        }
                    }),
                Forms\Components\TextInput::make('value')
                    ->label('Value')
                    ->required()
                    ->hidden(fn (Forms\Get $get) => in_array($get('type'), ['json', 'array']) || $get('key') === 'bank_accounts')
                    ->maxLength(255),
                Forms\Components\Textarea::make('value')
                    ->label('Value')
                    ->required()
                    ->hidden(fn (Forms\Get $get) => $get('type') !== 'json' || $get('key') === 'bank_accounts')
                    ->columnSpanFull()
                    ->hint('Enter valid JSON (e.g., {"key": "value"})')
                    ->rules(['json'])
                    ->rows(5),
                Forms\Components\Repeater::make('value')
                    ->label('Values')
                    ->schema([
                        Forms\Components\TextInput::make('item')
                            ->required()
                            ->maxLength(255),
                    ])
                    ->hidden(fn (Forms\Get $get) => $get('type') !== 'array' || $get('key') === 'bank_accounts')
                    ->minItems(1)
                    ->columnSpanFull()
                    ->defaultItems(1)
                    ->createItemButtonLabel('Add new item')
                    ->itemLabel(fn (array $state): ?string => $state['item'] ?? null),
                Forms\Components\Repeater::make('value')
                    ->label('Bank Accounts')
                    ->schema([
                        Forms\Components\TextInput::make('account_name')
                            ->label('Account Name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('bank_name')
                            ->label('Bank Name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('account_number')
                            ->label('Account Number')
                            ->required()
                            ->maxLength(255),
                    ])
                    ->hidden(fn (Forms\Get $get) => $get('key') !== 'bank_accounts')
                    ->minItems(1)
                    ->columnSpanFull()
                    ->defaultItems(1)
                    ->createItemButtonLabel('Add new bank account')
                    ->itemLabel(fn (array $state): ?string => $state['account_name'] ?? null),
                Forms\Components\Textarea::make('description')
                    ->columnSpanFull()
                    ->rows(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('key_label')
                    ->label('Setting Name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('value')
                    ->label('Setting Value')
                    ->formatStateUsing(function (string $state, Setting $record): string {
                        if ($record->type === 'array') {
                            $decoded = json_decode($state, true);
                            if (is_array($decoded)) {
                                return implode(', ', collect($decoded)->pluck('item')->toArray());
                            }

                            return $state; // Fallback if decoded is not an array
                        } elseif ($record->type === 'json') {
                            // For JSON, try to format bank accounts specifically
                            if ($record->key === 'bank_accounts') {
                                $bankAccounts = json_decode($state, true);
                                if (is_array($bankAccounts)) {
                                    if (count($bankAccounts) > 0) {
                                        $formatted = collect($bankAccounts)->map(function ($account) {
                                            // Ensure $account is an array before accessing keys
                                            if (is_array($account) && isset($account['account_name'], $account['bank_name'], $account['account_number'])) {
                                                return "{$account['account_name']} ({$account['bank_name']} - {$account['account_number']})";
                                            }

                                            return json_encode($account); // Fallback if sub-array is malformed
                                        })->implode('; ');

                                        return $formatted;
                                    } else {
                                        return 'No bank accounts configured.';
                                    }
                                }
                            }
                            // Fallback for general JSON or malformed bank_accounts JSON
                            $decodedState = json_decode($state, true);

                            return $decodedState ? json_encode($decodedState, JSON_PRETTY_PRINT) : $state;
                        }

                        return $state;
                    })
                    ->searchable()
                    ->sortable()
                    ->limit(50),
            ])
            ->filters([

            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn (Setting $record): bool => ! in_array($record->key, ['karyawan_jabatan_options', 'karyawan_status_options', 'bank_accounts'])),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn (Setting $record): bool => ! in_array($record->key, ['karyawan_jabatan_options', 'karyawan_status_options', 'bank_accounts'])),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [

        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSettings::route('/'),
            'create' => Pages\CreateSetting::route('/create'),
            'edit' => Pages\EditSetting::route('/{record}/edit'),
        ];
    }
}
