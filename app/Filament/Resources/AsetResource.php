<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AsetResource\Pages;
use App\Filament\Resources\AsetResource\RelationManagers;
use App\Models\Aset;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\RawJs;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AsetResource extends Resource
{
    protected static ?string $model = Aset::class;

    protected static ?string $navigationIcon = 'heroicon-s-cube';

    protected static ?string $navigationLabel = 'Daftar Aset';

    protected static ?string $pluralModelLabel = 'Daftar Aset';

    protected static ?string $navigationGroup = 'Manajemen Aset';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nama_aset')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('harga')
                    ->required()
                    ->prefix('Rp ')
                    ->mask(RawJs::make('$money($input)'))
                    ->stripCharacters(',')
                    ->numeric()
                    ->live(true)
                    ->afterStateUpdated(function ($state, Forms\Components\TextInput $component, $get, $set) {
                        $harga = (float) str_replace(['.', ','], '', $state);
                        $jumlah_aset = (float) str_replace(['.', ','], '', $get('jumlah_aset'));
                        $total = $harga * $jumlah_aset;
                        $set('total_harga_aset', number_format($total, 0, '.', ','));
                    }),
                Forms\Components\TextInput::make('jumlah_aset')
                    ->required()
                    ->numeric()
                    ->live(true)
                    ->afterStateUpdated(function ($state, Forms\Components\TextInput $component, $get, $set) {
                        $harga = (float) str_replace(['.', ','], '', $get('harga'));
                        $jumlah_aset = (float) str_replace(['.', ','], '', $state);
                        $total = $harga * $jumlah_aset;
                        $set('total_harga_aset', number_format($total, 0, '.', ','));
                    }),
                Forms\Components\TextInput::make('total_harga_aset')
                    ->label('Total Harga Aset')
                    ->prefix('Rp ')
                    ->disabled()
                    ->dehydrated(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama_aset')
                    ->searchable(),
                Tables\Columns\TextColumn::make('harga')
                    ->prefix('Rp ')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('jumlah_aset')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_harga_aset')
                    ->prefix('Rp ')
                    ->label('Total Harga Aset')
                    ->numeric()
                    ->getStateUsing(fn(Aset $record): float => $record->harga * $record->jumlah_aset)
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAsets::route('/'),
            'create' => Pages\CreateAset::route('/create'),
            'edit' => Pages\EditAset::route('/{record}/edit'),
        ];
    }
}
