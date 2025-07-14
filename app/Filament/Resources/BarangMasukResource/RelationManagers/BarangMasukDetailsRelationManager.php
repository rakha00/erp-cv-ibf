<?php

namespace App\Filament\Resources\BarangMasukResource\RelationManagers;

use App\Models\UnitProduk;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\RawJs;
use Filament\Tables;
use Filament\Tables\Columns\Summarizers\Summarizer;
use Filament\Tables\Table;
use Illuminate\Database\Query\Builder as QueryBuilder;

class BarangMasukDetailsRelationManager extends RelationManager
{
    protected static string $relationship = 'barangMasukDetails';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('unit_produk_id')
                    ->label('SKU')
                    ->options(fn(callable $get): array => self::getUnitProdukOptions($get))
                    ->searchable()
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(fn($state, callable $set, callable $get) => self::updateUnitProdukDetails($state, $set, $get)),
                Forms\Components\TextInput::make('nama_unit')
                    ->label('Nama Unit')
                    ->disabled()
                    ->dehydrated()
                    ->required(),
                Forms\Components\TextInput::make('jumlah_barang_masuk')
                    ->label('Jumlah Barang Masuk')
                    ->numeric()
                    ->live(true)
                    ->afterStateUpdated(fn(callable $set, callable $get) => self::updateTotals($set, $get))
                    ->required(),
                Forms\Components\TextInput::make('harga_modal')
                    ->label('Harga Modal/Unit')
                    ->prefix('Rp ')
                    ->mask(RawJs::make('$money($input)'))
                    ->stripCharacters(',')
                    ->numeric()
                    ->live(true)
                    ->afterStateUpdated(fn(callable $set, callable $get) => self::updateTotals($set, $get))
                    ->required(),
                Forms\Components\TextInput::make('total_harga_modal')
                    ->label('Total Harga Modal')
                    ->prefix('Rp ')
                    ->mask(RawJs::make('$money($input)'))
                    ->stripCharacters(',')
                    ->numeric()
                    ->required()
                    ->disabled()
                    ->dehydrated(true),
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
                Tables\Columns\TextColumn::make('unit_produk_id')
                    ->label('SKU')
                    ->getStateUsing(fn($record) => $record->unitProduk()->withTrashed()->first()?->sku)
                    ->sortable()
                    ->icon(fn($record) => $record->unitProduk()->withTrashed()->first()->trashed() ? 'heroicon-s-trash' : null)
                    ->color(fn($record) => $record->unitProduk()->withTrashed()->first()->trashed() ? 'danger' : null)
                    ->tooltip(fn($record) => $record->unitProduk()->withTrashed()->first()->trashed() ? 'Data master unit produk ini telah dihapus' : null),
                Tables\Columns\TextColumn::make('nama_unit')
                    ->label('Nama Unit')
                    ->sortable(),
                Tables\Columns\TextColumn::make('jumlah_barang_masuk')
                    ->label('Jumlah')
                    ->sortable(),
                Tables\Columns\TextColumn::make('harga_modal')
                    ->label('Harga Modal/Unit')
                    ->prefix('Rp ')
                    ->numeric()
                    ->sortable()
                    ->icon(function ($record) {
                        $unitProduk = $record->unitProduk()->withTrashed()->first();
                        if (!$unitProduk || (float) $unitProduk->harga_modal === (float) $record->harga_modal) {
                            return null;
                        }

                        return 'heroicon-s-exclamation-circle';
                    })
                    ->color(function ($record) {
                        $unitProduk = $record->unitProduk()->withTrashed()->first();

                        if (!$unitProduk || (float) $unitProduk->harga_modal !== (float) $record->harga_modal) {
                            return 'warning';
                        }

                        return null;
                    })
                    ->tooltip(function ($record) {
                        $unitProduk = $record->unitProduk()->withTrashed()->first();
                        if (!$unitProduk || (float) $unitProduk->harga_modal === (float) $record->harga_modal) {
                            return null;
                        }

                        return 'Harga modal saat ini: Rp ' . number_format($unitProduk->harga_modal, 0, ',', '.');
                    }),
                Tables\Columns\TextColumn::make('total_harga_modal')
                    ->label('Total Harga Modal')
                    ->prefix('Rp ')
                    ->numeric()
                    ->summarize(
                        Summarizer::make()
                            ->label('Total Harga Modal')
                            ->numeric()
                            ->prefix('Rp ')
                            ->using(fn(QueryBuilder $query): float => $query->sum('total_harga_modal'))
                    ),
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

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return self::mutateFormData($data);
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        return self::mutateFormData($data);
    }

    private static function getUnitProdukOptions(callable $get): array
    {
        $units = UnitProduk::query()->get();
        $selectedUnitId = $get('unit_produk_id');

        if ($selectedUnitId && !$units->contains('id', $selectedUnitId)) {
            $deletedUnit = UnitProduk::withTrashed()->find($selectedUnitId);
            if ($deletedUnit) {
                $units->add($deletedUnit);
            }
        }

        return $units->mapWithKeys(function ($unit) {
            $label = "{$unit->sku}";
            if ($unit->trashed()) {
                $label .= ' (Dihapus)';
            }

            return [$unit->id => $label];
        })->all();
    }

    private static function updateUnitProdukDetails($state, callable $set, callable $get): void
    {
        $unit = UnitProduk::withTrashed()->find($state);
        if ($unit) {
            $set('nama_unit', $unit->nama_unit);
            $set('harga_modal', number_format($unit->harga_modal, 0, ',', ','));
            self::updateTotals($set, $get);

        } else {
            $set('nama_unit', null);
            $set('harga_modal', null);
            $set('total_harga_modal', null);
        }
    }

    private static function updateTotals(callable $set, callable $get): void
    {
        $hargaModal = (float) str_replace(',', '', $get('harga_modal')) ?? null;
        $jumlahBarangMasuk = (int) $get('jumlah_barang_masuk') ?? null;
        $set('harga_modal', number_format($hargaModal, 0, ',', ','));
        $set('total_harga_modal', number_format($hargaModal * $jumlahBarangMasuk, 0, ',', ','));
    }

    private static function mutateFormData(array $data): array
    {
        $unitProduk = UnitProduk::withTrashed()->find($data['unit_produk_id']);
        $data['nama_unit'] = $unitProduk?->nama_unit;

        $hargaModal = (float) $data['harga_modal'];
        $jumlahBarangMasuk = (int) $data['jumlah_barang_masuk'];
        $data['total_harga_modal'] = $hargaModal * $jumlahBarangMasuk;

        return $data;
    }
}
