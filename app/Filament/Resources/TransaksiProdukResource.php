<?php

namespace App\Filament\Resources;

use App\Exports\TransaksiProdukExport;
use App\Filament\Resources\TransaksiProdukResource\Pages;
use App\Filament\Resources\TransaksiProdukResource\RelationManagers;
use App\Models\TransaksiProduk;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class TransaksiProdukResource extends Resource
{
    protected static ?string $model = TransaksiProduk::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    protected static ?string $navigationLabel = 'Transaksi Produk';

    protected static ?string $pluralModelLabel = 'Transaksi Produk';

    protected static ?string $navigationGroup = 'Transaksi';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\DatePicker::make('tanggal')
                    ->label('Tanggal')
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(fn (?string $state, Forms\Set $set) => self::generateInvoiceAndDeliveryNoteNumbers($state, $set)),
                Forms\Components\TextInput::make('no_invoice')
                    ->label('No Invoice')
                    ->required()
                    ->readOnly()
                    ->reactive()
                    ->maxLength(50),
                Forms\Components\TextInput::make('no_surat_jalan')
                    ->label('No Surat Jalan')
                    ->required()
                    ->readOnly()
                    ->reactive()
                    ->maxLength(50),
                Forms\Components\Textarea::make('remarks')
                    ->label('Remarks')
                    ->columnSpanFull(),
            ]);
    }

    private static function generateInvoiceAndDeliveryNoteNumbers(?string $state, Forms\Set $set): void
    {
        if ($state) {
            \Illuminate\Support\Facades\DB::transaction(function () use ($state, $set) {
                $date = Carbon::parse($state);
                $formatDate = $date->format('dmY');
                $latestRecord = TransaksiProduk::whereDate('tanggal', $state)
                    ->withTrashed()
                    ->orderBy('created_at', 'desc')
                    ->lockForUpdate()
                    ->first();

                $nextNumber = 1;
                if ($latestRecord) {
                    $parts = explode('-', $latestRecord->no_invoice);
                    $lastId = end($parts);
                    if (is_numeric($lastId)) {
                        $nextNumber = (int) $lastId + 1;
                    }
                }

                // Set both invoice and delivery note numbers
                $set('no_invoice', "INV/{$formatDate}-{$nextNumber}");
                $set('no_surat_jalan', "SJ/{$formatDate}-{$nextNumber}");
            });
        }
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('no_invoice')
                    ->label('No Invoice')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('no_surat_jalan')
                    ->label('No Surat Jalan')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('tanggal')
                    ->label('Tanggal')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_harga_jual')
                    ->label('Total Harga Jual')
                    ->prefix('Rp ')
                    ->numeric()
                    ->getStateUsing(fn (TransaksiProduk $record) => self::calculateTotalHargaJual($record)),
                Tables\Columns\TextColumn::make('total_keuntungan')
                    ->label('Total Keuntungan')
                    ->prefix('Rp ')
                    ->numeric()
                    ->getStateUsing(fn (TransaksiProduk $record) => self::calculateTotalKeuntungan($record)),
                Tables\Columns\TextColumn::make('remarks')
                    ->label('Remarks')
                    ->toggleable(isToggledHiddenByDefault: true),
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
                Tables\Filters\SelectFilter::make('tahun')
                    ->label('Tahun')
                    ->options(function () {
                        $years = TransaksiProduk::selectRaw('extract(year from tanggal) as year')
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
                Tables\Actions\Action::make('download')
                    ->label('Download PDF')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('info')
                    ->form([
                        Forms\Components\Select::make('type')
                            ->label('Dokumen')
                            ->required()
                            ->options([
                                'invoice' => 'Invoice',
                                'surat_jalan' => 'Surat Jalan',
                            ]),
                    ])
                    ->action(fn (TransaksiProduk $record, array $data) => self::downloadDocument($record, $data)),
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
                    ->action(fn (Table $table) => self::exportTransaksiProdukSummary($table)),
                Action::make('exportExcelWithDetails')
                    ->label('Export All (Details) to Excel')
                    ->color('info')
                    ->icon('heroicon-o-document-text')
                    ->action(fn (Table $table) => self::exportTransaksiProdukDetails($table)),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\TransaksiProdukDetailsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTransaksiProduks::route('/'),
            'create' => Pages\CreateTransaksiProduk::route('/create'),
            'edit' => Pages\EditTransaksiProduk::route('/{record}/edit'),
        ];
    }

    private static function calculateTotalHargaJual(TransaksiProduk $record): float
    {
        return $record->transaksiProdukDetails->reduce(function ($carry, $detail) {
            return $carry + ($detail->harga_jual * $detail->jumlah_keluar);
        }, 0);
    }

    private static function calculateTotalKeuntungan(TransaksiProduk $record): float
    {
        return $record->transaksiProdukDetails->reduce(function ($carry, $detail) {
            $unitProduk = $detail->unitProduk()->withTrashed()->first();
            $hargaModal = $unitProduk->harga_modal ?? 0;

            return $carry + ($detail->harga_jual - $hargaModal) * $detail->jumlah_keluar;
        }, 0);
    }

    private static function downloadDocument(TransaksiProduk $record, array $data): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $filename = '';
        $view = '';

        if ($data['type'] === 'invoice') {
            $filename = 'invoice-'.str_replace(['/', '\\'], '-', $record->no_invoice).'.pdf';
            $view = 'pdf.invoice';
        } elseif ($data['type'] === 'surat_jalan') {
            $filename = 'surat-jalan-'.str_replace(['/', '\\'], '-', $record->no_surat_jalan).'.pdf';
            $view = 'pdf.surat-jalan';
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView($view, ['transaksi' => $record])->setPaper('a4', 'portrait');

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, $filename);
    }

    private static function exportTransaksiProdukSummary(Table $table): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $livewire = $table->getLivewire();
        $query = $livewire->getFilteredTableQuery();
        $resourceTitle = static::$pluralModelLabel;
        $tahun = $livewire->tableFilters['tahun']['value'] ?? null;
        $bulan = $livewire->tableFilters['bulan']['value'] ?? null;

        return \Maatwebsite\Excel\Facades\Excel::download(new TransaksiProdukExport($query, $resourceTitle, false, $tahun, $bulan), 'transaksi_produk_summary.xlsx');
    }

    private static function exportTransaksiProdukDetails(Table $table): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $livewire = $table->getLivewire();
        $query = $livewire->getFilteredTableQuery();
        $resourceTitle = static::$pluralModelLabel;
        $tahun = $livewire->tableFilters['tahun']['value'] ?? null;
        $bulan = $livewire->tableFilters['bulan']['value'] ?? null;

        return \Maatwebsite\Excel\Facades\Excel::download(new TransaksiProdukExport($query, $resourceTitle, true, $tahun, $bulan), 'transaksi_produk_details.xlsx');
    }
}
