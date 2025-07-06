<?php

namespace App\Filament\Resources\TransaksiProdukResource\RelationManagers;

use App\Models\UnitProduk;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\RawJs;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TransaksiProdukDetailsRelationManager extends RelationManager
{
    protected static string $relationship = 'transaksiProdukDetails';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('unit_produk_id')
                    ->label('SKU')
                    ->options(UnitProduk::pluck('sku', 'id'))
                    ->searchable()
                    ->required()
                    ->live()
                    ->afterStateUpdated(function (Set $set, $state) {
                        if ($state) {
                            $unitProduk = UnitProduk::find($state);
                            $set('nama_unit', $unitProduk?->nama_unit);
                        } else {
                            $set('nama_unit', null);
                        }
                    }),
                Forms\Components\TextInput::make('nama_unit')
                    ->label('Nama Unit')
                    ->disabled()
                    ->dehydrated(false),
                Forms\Components\TextInput::make('harga_jual')
                    ->label('Harga Jual')
                    ->numeric()
                    ->required(),
                Forms\Components\TextInput::make('jumlah_keluar')
                    ->label('Jumlah Keluar')
                    ->numeric()
                    ->required(),
                Forms\Components\Textarea::make('remarks')
                    ->label('Remarks')
                    ->rows(3),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('sku')
            ->columns([
                Tables\Columns\TextColumn::make('unitProduk.sku')
                    ->label('SKU'),
                Tables\Columns\TextColumn::make('unitProduk.nama_unit')
                    ->label('Unit'),
                Tables\Columns\TextColumn::make('jumlah_keluar')
                    ->label('Qty')
                    ->numeric(),
                Tables\Columns\TextColumn::make('unitProduk.harga_modal')
                    ->label('Harga Modal')
                    ->prefix('Rp ')
                    ->numeric(),
                Tables\Columns\TextColumn::make('harga_jual')
                    ->label('Harga Jual')
                    ->prefix('Rp ')
                    ->numeric(),
                Tables\Columns\TextColumn::make('total_modal')
                    ->label('Total Harga Modal')
                    ->prefix('Rp ')
                    ->numeric()
                    ->getStateUsing(fn($record) => $record->unitProduk->harga_modal * $record->jumlah_keluar),
                Tables\Columns\TextColumn::make('total_harga_jual')
                    ->label('Total Harga Jual')
                    ->prefix('Rp ')
                    ->numeric()
                    ->getStateUsing(fn($record) => $record->harga_jual * $record->jumlah_keluar),
                Tables\Columns\TextColumn::make('keuntungan')
                    ->label('Keuntungan')
                    ->prefix('Rp ')
                    ->numeric()
                    ->getStateUsing(fn($record) => ($record->harga_jual - $record->unitProduk->harga_modal) * $record->jumlah_keluar),
                Tables\Columns\TextColumn::make('remarks')
                    ->label('Remarks')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
