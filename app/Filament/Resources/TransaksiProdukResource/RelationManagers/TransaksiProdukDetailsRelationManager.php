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
use Filament\Tables\Columns\Summarizers\Summarizer;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Facades\DB;
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
                    ->afterStateUpdated(function (Set $set, $state) {
                        if (!$state) {
                            $set('nama_unit', null);
                            $set('harga_modal', null);
                            $set('keuntungan', null);
                            $set('harga_jual', null);
                            $set('jumlah_keluar', null);
                            return;
                        }
                        $unitProduk = UnitProduk::find($state);
                        $set('nama_unit', $unitProduk?->nama_unit);
                        $set('harga_modal', number_format($unitProduk?->harga_modal, 0, ',', ','));
                        $set('keuntungan', number_format(0, 0, ',', ','));
                    }),
                Forms\Components\TextInput::make('nama_unit')
                    ->label('Nama Unit')
                    ->disabled()
                    ->dehydrated(false)
                    ->afterStateHydrated(function (Set $set, Get $get) {
                        $unitProdukId = $get('unit_produk_id');
                        if ($unitProdukId) {
                            $unitProduk = UnitProduk::find($unitProdukId);
                            $set('nama_unit', $unitProduk?->nama_unit);
                        }
                    }),
                Forms\Components\TextInput::make('harga_modal')
                    ->label('Harga Modal/Unit')
                    ->prefix('Rp ')
                    ->disabled()
                    ->dehydrated(false)
                    ->afterStateHydrated(function (Set $set, Get $get) {
                        $unitProdukId = $get('unit_produk_id');
                        if ($unitProdukId) {
                            $unitProduk = UnitProduk::find($unitProdukId);
                            $set('harga_modal', number_format($unitProduk?->harga_modal, 0, ',', ','));
                        }
                    }),
                Forms\Components\TextInput::make('keuntungan')
                    ->label('Keuntungan')
                    ->prefix('Rp ')
                    ->disabled()
                    ->dehydrated(false)
                    ->afterStateHydrated(function (Set $set, Get $get) {
                        $hargaModal = (float) str_replace(',', '', $get('harga_modal'));
                        $hargaJual = (float) str_replace(',', '', $get('harga_jual'));
                        $jumlahKeluar = (int) $get('jumlah_keluar');
                        $keuntungan = ($hargaJual - $hargaModal) * $jumlahKeluar;
                        $set('keuntungan', number_format($keuntungan, 0, ',', ','));
                    }),
                Forms\Components\TextInput::make('harga_jual')
                    ->label('Harga Jual/Unit')
                    ->prefix('Rp ')
                    ->mask(RawJs::make('$money($input)'))
                    ->stripCharacters(',')
                    ->numeric()
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (callable $get, callable $set) {
                        $hargaJual = (float) str_replace(',', '', $get('harga_jual'));
                        $hargaModal = (float) str_replace(',', '', $get('harga_modal'));
                        $jumlahKeluar = (float) $get('jumlah_keluar');
                        $keuntungan = ($hargaJual - $hargaModal) * $jumlahKeluar;
                        $set('keuntungan', number_format($keuntungan, 0, ',', ','));
                    }),
                Forms\Components\TextInput::make('jumlah_keluar')
                    ->label('Jumlah Keluar')
                    ->numeric()
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (callable $get, callable $set) {
                        $hargaJual = (float) str_replace(',', '', $get('harga_jual'));
                        $hargaModal = (float) str_replace(',', '', $get('harga_modal'));
                        $jumlahKeluar = (float) $get('jumlah_keluar');
                        $keuntungan = ($hargaJual - $hargaModal) * $jumlahKeluar;
                        $set('keuntungan', number_format($keuntungan, 0, ',', ','));
                    }),
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
                    ->label('SKU'),
                Tables\Columns\TextColumn::make('unitProduk.nama_unit')
                    ->label('Unit'),
                Tables\Columns\TextColumn::make('jumlah_keluar')
                    ->label('Qty')
                    ->numeric(),
                Tables\Columns\TextColumn::make('unitProduk.harga_modal')
                    ->label('Harga Modal/Unit')
                    ->prefix('Rp ')
                    ->numeric(),
                Tables\Columns\TextColumn::make('harga_jual')
                    ->label('Harga Jual/Unit')
                    ->prefix('Rp ')
                    ->numeric(),
                Tables\Columns\TextColumn::make('total_modal')
                    ->label('Total Harga Modal')
                    ->prefix('Rp ')
                    ->numeric()
                    ->getStateUsing(fn($record) => $record->unitProduk->harga_modal * $record->jumlah_keluar)
                    ->summarize(
                        Summarizer::make()
                            ->numeric()
                            ->prefix('Rp ')
                            ->using(
                                fn(QueryBuilder $query): float => $query->join('unit_produks', 'transaksi_produk_details.unit_produk_id', '=', 'unit_produks.id')
                                    ->sum(DB::raw('unit_produks.harga_modal * transaksi_produk_details.jumlah_keluar'))
                            )
                    ),

                Tables\Columns\TextColumn::make('total_harga_jual')
                    ->label('Total Harga Jual')
                    ->prefix('Rp ')
                    ->numeric()
                    ->getStateUsing(fn($record) => $record->harga_jual * $record->jumlah_keluar)
                    ->summarize(
                        Summarizer::make()
                            ->numeric()
                            ->prefix('Rp ')
                            ->using(fn(QueryBuilder $query): float => $query->sum(DB::raw('transaksi_produk_details.harga_jual * transaksi_produk_details.jumlah_keluar')))
                    ),


                Tables\Columns\TextColumn::make('keuntungan')
                    ->label('Keuntungan')
                    ->prefix('Rp ')
                    ->numeric()
                    ->getStateUsing(fn($record) => ($record->harga_jual - $record->unitProduk->harga_modal) * $record->jumlah_keluar)
                    ->summarize(
                        Summarizer::make()
                            ->numeric()
                            ->prefix('Rp ')
                            ->using(
                                fn(QueryBuilder $query): float => $query->join('unit_produks', 'transaksi_produk_details.unit_produk_id', '=', 'unit_produks.id')
                                    ->sum(DB::raw('(transaksi_produk_details.harga_jual - unit_produks.harga_modal) * transaksi_produk_details.jumlah_keluar'))
                            )
                    ),

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
