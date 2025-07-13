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
use Filament\Tables\Columns\Summarizers\Summarizer;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;

class TransaksiProdukDetailsRelationManager extends RelationManager
{
    protected static string $relationship = 'transaksiProdukDetails';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('unit_produk_id')
                    ->label('SKU')
                    ->options(fn(Get $get): array => self::getUnitProdukOptions($get))
                    ->searchable()
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(fn(Set $set, Get $get, $state) => self::updateUnitProdukDetails($set, $get, $state)),
                Forms\Components\TextInput::make('nama_unit')
                    ->label('Nama Unit')
                    ->disabled()
                    ->dehydrated()
                    ->required(),
                Forms\Components\TextInput::make('harga_jual')
                    ->label('Harga Jual/Unit')
                    ->prefix('Rp')
                    ->mask(RawJs::make('$money($input)'))
                    ->stripCharacters(',')
                    ->numeric()
                    ->required()
                    ->live(true)
                    ->afterStateUpdated(fn(Get $get, Set $set) => self::updateTotals($get, $set)),
                Forms\Components\TextInput::make('jumlah_keluar')
                    ->label('Jumlah Keluar')
                    ->numeric()
                    ->required()
                    ->live(true)
                    ->afterStateUpdated(fn(Get $get, Set $set) => self::updateTotals($get, $set)),
                Forms\Components\TextInput::make('harga_modal')
                    ->label('Harga Modal')
                    ->prefix('Rp')
                    ->mask(RawJs::make('$money($input)'))
                    ->stripCharacters(',')
                    ->numeric()
                    ->disabled()
                    ->dehydrated(),
                Forms\Components\TextInput::make('total_keuntungan')
                    ->label('Total Keuntungan')
                    ->prefix('Rp')
                    ->mask(RawJs::make('$money($input)'))
                    ->stripCharacters(',')
                    ->numeric()
                    ->disabled()
                    ->dehydrated(),
                Forms\Components\Textarea::make('remarks')
                    ->label('Remarks')
                    ->columnSpanFull(),
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

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('nama_unit')
            ->modifyQueryUsing(fn(Builder $query) => $query->with('unitProduk', function ($query) {
                $query->withTrashed();
            }))
            ->columns([
                Tables\Columns\TextColumn::make('unitProduk.sku')
                    ->label('SKU')
                    ->sortable()
                    ->icon(function ($record) {
                        $unitProduk = $record->unitProduk()->withTrashed()->first();
                        return $unitProduk && $unitProduk->trashed() ? 'heroicon-s-trash' : null;
                    })
                    ->color(function ($record) {
                        $unitProduk = $record->unitProduk()->withTrashed()->first();
                        if ($unitProduk && $unitProduk->trashed()) {
                            return 'danger';
                        }
                        return null;
                    })
                    ->tooltip(function ($record) {
                        $unitProduk = $record->unitProduk()->withTrashed()->first();
                        if ($unitProduk && $unitProduk->trashed()) {
                            return 'Data master unit produk ini telah dihapus';
                        }
                        return null;
                    }),
                Tables\Columns\TextColumn::make('nama_unit')
                    ->label('Nama Unit')
                    ->sortable()
                    ->icon(function ($record) {
                        $unitProduk = $record->unitProduk()->withTrashed()->first();
                        return $unitProduk && $unitProduk->trashed() ? 'heroicon-s-trash' : null;
                    })
                    ->color(function ($record) {
                        $unitProduk = $record->unitProduk()->withTrashed()->first();
                        if ($unitProduk && $unitProduk->trashed()) {
                            return 'danger';
                        }
                        return null;
                    })
                    ->tooltip(function ($record) {
                        $unitProduk = $record->unitProduk()->withTrashed()->first();
                        if ($unitProduk && $unitProduk->trashed()) {
                            return 'Data master unit produk ini telah dihapus';
                        }
                        return null;
                    }),
                Tables\Columns\TextColumn::make('jumlah_keluar')
                    ->label('Qty')
                    ->numeric()
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
                Tables\Columns\TextColumn::make('harga_jual')
                    ->label('Harga Jual/Unit')
                    ->prefix('Rp ')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('total_keuntungan')
                    ->label('Keuntungan')
                    ->prefix('Rp ')
                    ->numeric()
                    ->sortable()
                    ->summarize(
                        Summarizer::make()
                            ->label('Total Keuntungan')
                            ->numeric()
                            ->prefix('Rp ')
                            ->using(fn(QueryBuilder $query): float => $query->sum('total_keuntungan'))
                    ),
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

    private static function getUnitProdukOptions(Get $get): array
    {
        $selectedUnitId = $get('unit_produk_id');
        $units = UnitProduk::all();

        if ($selectedUnitId) {
            $selectedUnit = UnitProduk::withTrashed()->find($selectedUnitId);
            if ($selectedUnit && $selectedUnit->trashed() && !$units->contains('id', $selectedUnitId)) {
                $units->add($selectedUnit);
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

    private static function updateUnitProdukDetails(Set $set, Get $get, $state): void
    {
        $unit = UnitProduk::withTrashed()->find($state);
        if ($unit) {
            $set('nama_unit', $unit->nama_unit);
            $set('harga_modal', number_format($unit->harga_modal, 0, '.', ','));
            self::updateTotals($get, $set);
        } else {
            $set('nama_unit', null);
            $set('harga_modal', null);
            $set('total_keuntungan', null);
        }
    }

    private static function updateTotals(Get $get, Set $set): void
    {
        $hargaJual = (float) str_replace(',', '', $get('harga_jual') ?? null);
        $hargaModal = (float) str_replace(',', '', $get('harga_modal') ?? null);
        $jumlahKeluar = (int) ($get('jumlah_keluar') ?? 0);
        $keuntungan = ($hargaJual - $hargaModal) * $jumlahKeluar;
        $set('harga_jual', number_format($hargaJual, 0, '.', ','));
        $set('total_keuntungan', number_format($keuntungan, 0, '.', ','));
    }

    private static function mutateFormData(array $data): array
    {
        $unitProduk = UnitProduk::withTrashed()->find($data['unit_produk_id']);
        $data['nama_unit'] = $unitProduk?->nama_unit;
        $data['harga_modal'] = $unitProduk?->harga_modal;

        $hargaJual = (float) $data['harga_jual'];
        $hargaModal = (float) $data['harga_modal'];
        $jumlahKeluar = (int) $data['jumlah_keluar'];
        $data['total_keuntungan'] = ($hargaJual - $hargaModal) * $jumlahKeluar;

        return $data;
    }
}
