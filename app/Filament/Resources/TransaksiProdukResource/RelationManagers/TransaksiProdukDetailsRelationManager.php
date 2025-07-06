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
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set) {
                        if ($unit = UnitProduk::find($state)) {
                            $set('sku', $unit->sku);
                            $set('nama_unit', $unit->nama_unit);
                            $set('harga_modal', $unit->harga_modal);
                        }
                    }),
                Forms\Components\Hidden::make('sku')
                    ->required(),
                Forms\Components\TextInput::make('nama_unit')
                    ->label('Nama Unit')
                    ->readOnly()
                    ->required(),
                Forms\Components\TextInput::make('harga_modal')
                    ->label('Harga Modal')
                    ->numeric()
                    ->readOnly()
                    ->required(),
                Forms\Components\TextInput::make('harga_jual')
                    ->label('Harga Jual')
                    ->numeric()
                    ->required()
                    ->live(true)
                    ->afterStateUpdated(function ($state, Get $get, Set $set) {
                        $this->calculateTotals($get, $set);
                    }),
                Forms\Components\TextInput::make('jumlah_keluar')
                    ->label('Jumlah Keluar')
                    ->numeric()
                    ->required()
                    ->live(true)
                    ->afterStateUpdated(function ($state, Get $get, Set $set) {
                        $this->calculateTotals($get, $set);
                    }),
                Forms\Components\Hidden::make('total_modal')
                    ->required(),
                Forms\Components\Hidden::make('total_harga_jual')
                    ->required(),
                Forms\Components\Hidden::make('keuntungan')
                    ->required(),
                Forms\Components\Textarea::make('remarks')
                    ->label('Remarks')
                    ->rows(3),
            ]);
    }

    private function calculateTotals(Get $get, Set $set): void
    {
        $set('total_modal', ((float) $get('harga_modal') ?? 0) * ((float) $get('jumlah_keluar') ?? 0));
        $set('total_harga_jual', ((float) $get('harga_jual') ?? 0) * ((float) $get('jumlah_keluar') ?? 0));
        $set('keuntungan', ((float) $get('total_harga_jual') ?? 0) - ((float) $get('total_modal') ?? 0));
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('sku')
            ->columns([
                Tables\Columns\TextColumn::make('sku')
                    ->label('SKU'),
                Tables\Columns\TextColumn::make('nama_unit')
                    ->label('Unit'),
                Tables\Columns\TextColumn::make('jumlah_keluar')
                    ->label('Qty')
                    ->numeric(),
                Tables\Columns\TextColumn::make('harga_modal')
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
                    ->getStateUsing(
                        fn($record) =>
                        $record->total_modal ?? ($record->harga_modal * $record->jumlah_keluar)
                    ),
                Tables\Columns\TextColumn::make('total_harga_jual')
                    ->label('Total Harga Jual')
                    ->prefix('Rp ')
                    ->numeric()
                    ->getStateUsing(
                        fn($record) =>
                        $record->total_harga_jual ?? ($record->harga_jual * $record->jumlah_keluar)
                    ),
                Tables\Columns\TextColumn::make('keuntungan')
                    ->label('Keuntungan')
                    ->prefix('Rp ')
                    ->numeric(),
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
