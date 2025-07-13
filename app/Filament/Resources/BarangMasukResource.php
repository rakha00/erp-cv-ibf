<?php

namespace App\Filament\Resources;

use App\Exports\BarangMasukExport;
use App\Filament\Resources\BarangMasukResource\Pages;
use App\Filament\Resources\BarangMasukResource\RelationManagers;
use App\Models\BarangMasuk;
use App\Models\PrincipleSubdealer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\Summarizers\Summarizer;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class BarangMasukResource extends Resource
{
    protected static ?string $model = BarangMasuk::class;

    protected static ?string $navigationIcon = 'heroicon-o-archive-box-arrow-down';

    protected static ?string $navigationLabel = 'Barang Masuk';

    protected static ?string $pluralModelLabel = 'Barang Masuk';

    protected static ?string $navigationGroup = 'Transaksi';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('principle_subdealer_id')
                    ->label('Principle/Subdealer')
                    ->options(fn (callable $get): array => self::getPrincipleSubdealerOptions($get))
                    ->searchable()
                    ->required(),
                Forms\Components\DatePicker::make('tanggal')
                    ->label('Tanggal')
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(fn (?string $state, Forms\Set $set) => self::generateNomorBarangMasuk($state, $set)),
                Forms\Components\TextInput::make('nomor_barang_masuk')
                    ->label('Nomor Barang Masuk')
                    ->required()
                    ->readOnly()
                    ->maxLength(50),
                Forms\Components\Textarea::make('remarks')
                    ->label('Remarks')
                    ->columnSpanFull(),
            ]);
    }

    private static function generateNomorBarangMasuk(?string $state, Forms\Set $set): void
    {
        if ($state) {
            \Illuminate\Support\Facades\DB::transaction(function () use ($state, $set) {
                $date = \Carbon\Carbon::parse($state);
                $latestRecord = BarangMasuk::whereDate('tanggal', $state)
                    ->withTrashed()
                    ->orderBy('created_at', 'desc')
                    ->lockForUpdate()
                    ->first();

                $nextId = 1;
                if ($latestRecord) {
                    $parts = explode('-', $latestRecord->nomor_barang_masuk);
                    $lastId = end($parts);
                    if (is_numeric($lastId)) {
                        $nextId = (int) $lastId + 1;
                    }
                }

                $set('nomor_barang_masuk', sprintf(
                    'BM/%s-%d',
                    $date->format('dmY'),
                    $nextId
                ));
            });
        }
    }

    private static function getPrincipleSubdealerOptions(callable $get): array
    {
        $principles = PrincipleSubdealer::query()->get();
        $selectedPrincipleId = $get('principle_subdealer_id');

        if ($selectedPrincipleId && ! $principles->contains('id', $selectedPrincipleId)) {
            $deletedPrinciple = PrincipleSubdealer::withTrashed()->find($selectedPrincipleId);
            if ($deletedPrinciple) {
                $principles->add($deletedPrinciple);
            }
        }

        return $principles->mapWithKeys(function ($principle) {
            $label = "{$principle->nama}";
            if ($principle->trashed()) {
                $label .= ' (Dihapus)';
            }

            return [$principle->id => $label];
        })->all();
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nomor_barang_masuk')
                    ->label('No. Barang Masuk')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('principleSubdealer.nama')
                    ->label('Principle/Subdealer')
                    ->sortable()
                    ->color(fn ($record) => $record->principleSubdealer()->withTrashed()->first()?->trashed() ? 'danger' : null)
                    ->icon(fn ($record) => $record->principleSubdealer()->withTrashed()->first()?->trashed() ? 'heroicon-s-trash' : null)
                    ->tooltip(fn ($record) => $record->principleSubdealer()->withTrashed()->first()?->trashed() ? 'Data master Principle/Subdealer ini telah dihapus' : null),
                Tables\Columns\TextColumn::make('tanggal')
                    ->label('Tanggal')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_harga_modal')
                    ->label('Total Harga Modal')
                    ->prefix('Rp ')
                    ->state(fn (BarangMasuk $record): string => self::calculateTotalHargaModal($record))
                    ->sortable()
                    ->summarize(
                        Summarizer::make()
                            ->label('Total Harga Modal')
                            ->prefix('Rp ')
                            ->numeric()
                            ->using(fn (\Illuminate\Database\Query\Builder $query): float => $query->join('barang_masuk_details', 'barang_masuks.id', '=', 'barang_masuk_details.barang_masuk_id')
                                ->sum(\Illuminate\Support\Facades\DB::raw('barang_masuk_details.harga_modal * barang_masuk_details.jumlah_barang_masuk')))
                    ),
                Tables\Columns\TextColumn::make('remarks')
                    ->label('Remarks')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Diubah')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
            ])
            ->defaultSort('tanggal', 'desc')
            ->modifyQueryUsing(fn (Builder $query) => $query->with(['barangMasukDetails', 'principleSubdealer' => fn ($query) => $query->withTrashed()]))
            ->filters([
                Tables\Filters\SelectFilter::make('tahun')
                    ->label('Tahun')
                    ->options(function () {
                        $years = BarangMasuk::selectRaw('extract(year from tanggal) as year')
                            ->distinct()
                            ->orderBy('year', 'desc')
                            ->pluck('year')
                            ->mapWithKeys(fn ($year) => [$year => $year]);

                        return $years;
                    })
                    ->query(function (Builder $query, array $data) {
                        return $query->when(
                            $data['value'],
                            fn (Builder $q) => $q->whereYear('tanggal', $data['value'])
                        );
                    }),

                Tables\Filters\SelectFilter::make('bulan')
                    ->label('Bulan')
                    ->options([
                        1 => 'Januari',
                        2 => 'Februari',
                        3 => 'Maret',
                        4 => 'April',
                        5 => 'Mei',
                        6 => 'Juni',
                        7 => 'Juli',
                        8 => 'Agustus',
                        9 => 'September',
                        10 => 'Oktober',
                        11 => 'November',
                        12 => 'Desember',
                    ])
                    ->query(function (Builder $query, array $data) {
                        return $query->when(
                            $data['value'],
                            fn (Builder $q) => $q->whereMonth('tanggal', $data['value'])
                        );
                    }),

                Tables\Filters\Filter::make('rentang_tanggal')
                    ->label('Rentang Tanggal')
                    ->form([
                        Forms\Components\DatePicker::make('from')
                            ->label('Dari'),
                        Forms\Components\DatePicker::make('until')
                            ->label('Sampai'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['from'] && $data['until'],
                            fn (Builder $q) => $q->whereBetween('tanggal', [$data['from'], $data['until']])
                        );
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->headerActions([
                Action::make('exportExcel')
                    ->label('Export All (Summary) to Excel')
                    ->color('success')
                    ->icon('heroicon-o-document-arrow-down')
                    ->action(fn (Table $table) => self::exportAllSummary($table)),
                Action::make('exportExcelWithDetails')
                    ->label('Export All (Details) to Excel')
                    ->color('info')
                    ->icon('heroicon-o-document-text')
                    ->action(fn (Table $table) => self::exportAllDetails($table)),
            ]);
    }

    private static function exportAllSummary(Table $table): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $livewire = $table->getLivewire();
        $query = $livewire->getFilteredTableQuery();
        $resourceTitle = static::$pluralModelLabel;

        return \Maatwebsite\Excel\Facades\Excel::download(new BarangMasukExport($query, $resourceTitle, false), 'barang_masuk_summary.xlsx');
    }

    private static function exportAllDetails(Table $table): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $livewire = $table->getLivewire();
        $query = $livewire->getFilteredTableQuery();
        $resourceTitle = static::$pluralModelLabel;

        return \Maatwebsite\Excel\Facades\Excel::download(new BarangMasukExport($query, $resourceTitle, true), 'barang_masuk_details.xlsx');
    }

    private static function calculateTotalHargaModal(BarangMasuk $record): string
    {
        $total = $record->barangMasukDetails->reduce(function ($carry, $detail) {
            return $carry + ($detail->harga_modal * $detail->jumlah_barang_masuk);
        }, 0);

        return number_format($total, 0, ',', ',');
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\BarangMasukDetailsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBarangMasuks::route('/'),
            'create' => Pages\CreateBarangMasuk::route('/create'),
            'edit' => Pages\EditBarangMasuk::route('/{record}/edit'),
        ];
    }
}
