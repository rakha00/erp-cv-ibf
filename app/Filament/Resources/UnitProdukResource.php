<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UnitProdukResource\Pages;
use App\Filament\Resources\UnitProdukResource\RelationManagers;
use App\Models\UnitProduk;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UnitProdukResource extends Resource
{
    protected static ?string $model = UnitProduk::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static ?string $navigationLabel = 'Unit Produk';

    protected static ?string $pluralModelLabel = 'Unit Produk';

    protected static ?string $navigationGroup = 'Manajemen Produk';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('sku')
                    ->label('SKU')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('nama_unit')
                    ->label('Nama Unit')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('harga_modal')
                    ->label('Harga Modal')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('stok_awal')
                    ->label('Stok Awal')
                    ->required()
                    ->numeric()
                    ->default(0),
                // Forms\Components\Textarea::make('notes')
                //     ->label('Catatan')
                //     ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('sku')
                    ->label('SKU')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('nama_unit')
                    ->label('Nama Unit')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('harga_modal')
                    ->label('Harga Modal')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('stok_awal')
                    ->label('Stok Awal')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('stok_akhir')
                    ->label('Stok Akhir')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('stok_masuk')
                    ->label('Stok Masuk')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('stok_keluar')
                    ->label('Stok Keluar')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Diubah')
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
            'index' => Pages\ListUnitProduks::route('/'),
            'create' => Pages\CreateUnitProduk::route('/create'),
            'edit' => Pages\EditUnitProduk::route('/{record}/edit'),
        ];
    }
}
