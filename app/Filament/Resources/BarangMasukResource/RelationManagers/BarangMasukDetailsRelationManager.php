<?php

namespace App\Filament\Resources\BarangMasukResource\RelationManagers;

use App\Models\UnitProduk;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\RawJs;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Filament\Tables;
use Filament\Tables\Columns\Summarizers\Summarizer;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\DB;

class BarangMasukDetailsRelationManager extends RelationManager
{
    protected static string $relationship = 'barangMasukDetails';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('unit_produk_id')
                    ->label('SKU')
                    ->options(function (callable $get) {
                        $selectedUnitId = $get('unit_produk_id');
                        $units = UnitProduk::all();

                        if ($selectedUnitId) {
                            $selectedUnit = UnitProduk::withTrashed()->find($selectedUnitId);
                            if ($selectedUnit && $selectedUnit->trashed() && !$units->contains('id', $selectedUnitId)) {
                                $units->add($selectedUnit);
                            }
                        }

                        return $units->mapWithKeys(function ($unit) {
                            $label = $unit->sku;
                            if ($unit->trashed()) {
                                $label .= ' (Data telah dihapus)';
                            }
                            return [$unit->id => $label];
                        });
                    })
                    ->searchable()
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                        $unit = UnitProduk::withTrashed()->find($state);
                        if ($unit) {
                            $set('nama_unit', $unit->nama_unit);
                            $set('harga_modal', number_format($unit->harga_modal, 0, ',', ','));
                            $set('total_harga_modal', $unit->harga_modal);

                            $jumlah = (int) $get('jumlah_barang_masuk');
                            $set('total_harga_modal', number_format($unit->harga_modal * $jumlah, 0, ',', ','));
                        } else {
                            $set('nama_unit', null);
                            $set('harga_modal', 0);
                            $set('total_harga_modal', 0);
                        }
                    }),
                Forms\Components\TextInput::make('nama_unit')
                    ->label('Nama Unit')
                    ->required(),
                Forms\Components\TextInput::make('jumlah_barang_masuk')
                    ->label('Jumlah Barang Masuk')
                    ->numeric()
                    ->live(true)
                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                        $hargaModal = (float) str_replace(',', '', $get('harga_modal')) ?? 0;
                        $set('total_harga_modal', number_format($state * $hargaModal, 0, ',', ','));
                    })
                    ->required(),
                Forms\Components\TextInput::make('harga_modal')
                    ->label('Harga Modal/Unit')
                    ->prefix('Rp ')
                    ->mask(RawJs::make('$money($input)'))
                    ->stripCharacters(',')
                    ->live(true)
                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                        $jumlahBarangMasuk = (int) $get('jumlah_barang_masuk') ?? 0;
                        $hargaModal = (float) str_replace(',', '', $state);
                        $set('total_harga_modal', number_format($jumlahBarangMasuk * $hargaModal, 0, ',', ','));
                    })
                    ->required(),
                Forms\Components\TextInput::make('total_harga_modal')
                    ->label('Total Harga Modal')
                    ->prefix('Rp ')
                    ->mask(RawJs::make('$money($input)'))
                    ->stripCharacters(',')
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
                Tables\Columns\TextColumn::make('unitProduk.sku')
                    ->label('SKU')
                    ->sortable()
                    ->state(function ($record) {
                        return $record->unitProduk()->withTrashed()->first()?->sku ?? '-';
                    })
                    ->color(fn($record) => $record->unitProduk()->withTrashed()->first()?->deleted_at ? 'danger' : null)
                    ->description(fn($record) => $record->unitProduk()->withTrashed()->first()?->deleted_at ? 'Data telah dihapus' : null),
                Tables\Columns\TextColumn::make('nama_unit')
                    ->label('Nama Unit')
                    ->sortable()
                    ->icon(function ($record) {
                        $unitProduk = $record->unitProduk()->withTrashed()->first();
                        if (!$unitProduk || $unitProduk->nama_unit === $record->nama_unit) {
                            return null;
                        }
                        return 'heroicon-s-exclamation-circle';
                    })
                    ->color(function ($record) {
                        $unitProduk = $record->unitProduk()->withTrashed()->first();

                        if ($unitProduk->nama_unit !== $record->nama_unit) {
                            return 'warning';
                        }

                        return null;
                    })
                    ->tooltip(function ($record) {
                        $unitProduk = $record->unitProduk()->withTrashed()->first();
                        if (!$unitProduk || $unitProduk->nama_unit === $record->nama_unit) {
                            return null;
                        }
                        return "Nama unit saat ini: {$unitProduk->nama_unit}";
                    }),
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
                        return "Harga modal saat ini: Rp " . number_format($unitProduk->harga_modal, 0, ',', '.');
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
}
