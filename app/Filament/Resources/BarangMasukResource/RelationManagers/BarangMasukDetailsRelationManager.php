<?php

namespace App\Filament\Resources\BarangMasukResource\RelationManagers;

use App\Models\UnitProduk;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\RawJs;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BarangMasukDetailsRelationManager extends RelationManager
{
    protected static string $relationship = 'barangMasukDetails';

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
                            $set('harga_modal', $unit->harga_modal);
                            $set('nama_unit', $unit->nama_unit);
                        }
                    })
                    ->afterStateHydrated(function ($state, callable $set) {
                        if ($unit = UnitProduk::find($state)) {
                            $set('nama_unit', $unit->nama_unit);
                        }
                    }),
                Forms\Components\TextInput::make('nama_unit')
                    ->label('Nama Unit')
                    ->disabled()
                    ->dehydrated(false),
                Forms\Components\TextInput::make('jumlah_barang_masuk')
                    ->label('Jumlah Barang Masuk')
                    ->numeric()
                    ->live(true)
                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                        $hargaModal = $get('harga_modal') ?? 0;
                        $set('total_harga_modal', number_format($state * $hargaModal, 0, ',', ','));
                    })
                    ->required(),
                Forms\Components\TextInput::make('harga_modal')
                    ->label('Harga Modal/Unit')
                    ->prefix('Rp ')
                    ->numeric()
                    ->live(true)
                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                        $jumlahBarangMasuk = $get('jumlah_barang_masuk') ?? 0;
                        $set('total_harga_modal', number_format($state * $jumlahBarangMasuk, 0, ',', ','));
                    })
                    ->required(),
                Forms\Components\TextInput::make('total_harga_modal')
                    ->label('Total Harga Modal')
                    ->disabled()
                    ->prefix('Rp ')
                    ->required(),
                Forms\Components\Textarea::make('remarks')
                    ->label('Remarks')
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('sku')
            ->columns([
                Tables\Columns\TextColumn::make('unitProduk.sku')
                    ->label('SKU')
                    ->sortable(),
                Tables\Columns\TextColumn::make('unitProduk.nama_unit')
                    ->label('Nama Unit')
                    ->sortable(),
                Tables\Columns\TextColumn::make('jumlah_barang_masuk')
                    ->label('Jumlah')
                    ->sortable(),
                Tables\Columns\TextColumn::make('harga_modal')
                    ->label('Harga Modal/Unit')
                    ->prefix('Rp ')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_harga_modal')
                    ->label('Total Harga Modal')
                    ->prefix('Rp ')
                    ->numeric()
                    ->state(function ($record): float {
                        return $record->harga_modal * $record->jumlah_barang_masuk;
                    }),
                Tables\Columns\TextColumn::make('remarks')
                    ->label('Remarks')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Diubah')
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
