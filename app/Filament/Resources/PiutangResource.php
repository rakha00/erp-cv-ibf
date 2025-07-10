<?php

namespace App\Filament\Resources;

use App\Exports\PiutangExport;
use App\Filament\Resources\PiutangResource\Pages;
use App\Models\Piutang;
use App\Models\TransaksiProduk;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Support\RawJs;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;

class PiutangResource extends Resource
{
    protected static ?string $model = Piutang::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';

    protected static ?string $navigationGroup = 'Reminder';

    protected static ?string $navigationLabel = 'Piutang';

    protected static ?string $pluralModelLabel = 'Piutang';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('transaksi_produk_id')
                    ->label('Transaksi Produk')
                    ->options(TransaksiProduk::pluck('no_invoice', 'id'))
                    ->searchable()
                    ->reactive()
                    ->required()
                    ->afterStateUpdated(fn (Set $set, $state, ?Piutang $record) => self::updateTransaksiProdukDetails($set, $state, $record)),

                Forms\Components\TextInput::make('total_harga_modal')
                    ->label('Total Harga Modal')
                    ->required()
                    ->prefix('Rp ')
                    ->disabled()
                    ->stripCharacters(',')
                    ->formatStateUsing(fn ($state) => number_format((float) $state, 0, '.', ',')),

                Forms\Components\TextInput::make('pembayaran_baru')
                    ->label('Pembayaran Baru')
                    ->numeric()
                    ->prefix('Rp')
                    ->mask(RawJs::make('$money($input)'))
                    ->stripCharacters(',')
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (Set $set, Get $get, $state, $record = null) => self::updatePembayaran($set, $get, $state, $record))
                    ->dehydrateStateUsing(function ($state) {
                        if (! $state) {
                            return null;
                        }

                        return (float) str_replace(',', '', $state);
                    })
                    ->default(null),

                Forms\Components\TextInput::make('sudah_dibayar')
                    ->label('Sudah Dibayar')
                    ->disabled()
                    ->dehydrated()
                    ->prefix('Rp')
                    ->stripCharacters(',')
                    ->formatStateUsing(fn ($state) => number_format((float) $state, 0, '.', ',')),

                Forms\Components\DatePicker::make('jatuh_tempo')
                    ->label('Jatuh Tempo')
                    ->required(),

                Forms\Components\Select::make('status_pembayaran')
                    ->options([
                        'belum lunas' => 'Belum Lunas',
                        'tercicil' => 'Tercicil',
                        'sudah lunas' => 'Sudah Lunas',
                    ])
                    ->required(),

                Forms\Components\FileUpload::make('foto')
                    ->label('Foto Bukti')
                    ->multiple()
                    ->preserveFilenames()
                    ->reorderable()
                    ->directory('piutang-foto')
                    ->openable()
                    ->downloadable(),

                Forms\Components\Textarea::make('remarks')
                    ->columnSpanFull(),
            ]);
    }

    private static function updatePembayaran(Set $set, Get $get, $state, $record = null): void
    {
        $sudahDibayarLama = (float) str_replace(',', '', $record?->sudah_dibayar ?? '0');
        $pembayaranBaru = (float) str_replace(',', '', $state ?? '0');
        $totalBaru = $sudahDibayarLama + $pembayaranBaru;
        $set('sudah_dibayar', number_format($totalBaru, 0, '.', ','));
        $set('pembayaran_baru', number_format($pembayaranBaru, 0, '.', ','));
    }

    private static function updateTransaksiProdukDetails(Set $set, $state, ?Piutang $record = null): void
    {
        if (! $state) {
            if (! $record) {
                $set('total_harga_modal', '');
            }

            return;
        }

        $transaksiProduk = TransaksiProduk::with(['transaksiProdukDetails'])->find($state);

        if (! $transaksiProduk) {
            return;
        }

        if (! $record) {
            $totalHargaJual = self::calculateTotalHargaJual($transaksiProduk);
            $set('total_harga_modal', number_format($totalHargaJual, 0, '.', ','));
        }
    }

    private static function calculateTotalHargaJual(TransaksiProduk $transaksiProduk): float
    {
        return $transaksiProduk->transaksiProdukDetails->reduce(function ($carry, $detail) {
            return $carry + ($detail->harga_jual * $detail->jumlah_keluar);
        }, 0);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('transaksiProduk.no_invoice')
                    ->label('No. Transaksi')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('transaksiProduk.tanggal')
                    ->label('Tanggal Transaksi')
                    ->date(),
                Tables\Columns\TextColumn::make('jatuh_tempo')
                    ->label('Jatuh Tempo')
                    ->date()
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('status_pembayaran')
                    ->colors([
                        'danger' => 'belum lunas',
                        'warning' => 'tercicil',
                        'success' => 'sudah lunas',
                    ])
                    ->label('Status')
                    ->formatStateUsing(fn ($state) => ucwords($state)),
                Tables\Columns\TextColumn::make('sudah_dibayar')
                    ->numeric()
                    ->formatStateUsing(fn ($state) => number_format((float) $state, 0, '.', ','))
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_harga_modal')
                    ->label('Total Harga Modal')
                    ->prefix('Rp ')
                    ->numeric()
                    ->formatStateUsing(fn ($state) => number_format((float) $state, 0, '.', ','))
                    ->sortable(),
                Tables\Columns\TextColumn::make('sisa_piutang')
                    ->label('Sisa Piutang')
                    ->prefix('Rp ')
                    ->state(fn ($record): string => self::calculateSisaPiutang($record))
                    ->sortable(),
                Tables\Columns\TextColumn::make('remarks')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')
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
            ])
            ->headerActions([
                Action::make('exportExcel')
                    ->label('Export to Excel')
                    ->color('success')
                    ->icon('heroicon-o-document-arrow-down')
                    ->action(function (Table $table) {
                        $livewire = $table->getLivewire();
                        $query = $livewire->getFilteredTableQuery();
                        $resourceTitle = static::$pluralModelLabel;

                        return \Maatwebsite\Excel\Facades\Excel::download(new PiutangExport($query, $resourceTitle), 'piutang.xlsx');
                    }),
            ]);
    }

    private static function exportPiutangExcel(Table $table): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $livewire = $table->getLivewire();
        $query = $livewire->getFilteredTableQuery();
        $resourceTitle = static::$pluralModelLabel;

        return \Maatwebsite\Excel\Facades\Excel::download(new PiutangExport($query, $resourceTitle), 'piutang.xlsx');
    }

    private static function calculateSisaPiutang($record): string
    {
        $totalPiutang = (float) str_replace(',', '', $record->total_harga_modal ?? '0');
        $sudahDibayar = (float) ($record->sudah_dibayar ?? '0');

        return number_format($totalPiutang - $sudahDibayar, 0, '.', ',');
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
            'index' => Pages\ListPiutangs::route('/'),
            'create' => Pages\CreatePiutang::route('/create'),
            'edit' => Pages\EditPiutang::route('/{record}/edit'),
        ];
    }
}
