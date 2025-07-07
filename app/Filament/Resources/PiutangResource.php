<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PiutangResource\Pages;
use App\Filament\Resources\PiutangResource\RelationManagers;
use App\Models\Piutang;
use App\Models\TransaksiProduk;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;


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
                    ->afterStateUpdated(fn(Set $set, $state) => self::updateTransaksiProdukDetails($set, $state)),

                Forms\Components\TextInput::make('total_harga_modal')
                    ->label('Total Harga Modal')
                    ->required()
                    ->prefix('Rp ')
                    ->readOnly(),

                Forms\Components\TextInput::make('pembayaran_baru')
                    ->label('Pembayaran Baru')
                    ->numeric()
                    ->prefix('Rp')
                    ->default(0)
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (Set $set, Get $get, $state, $record = null) {
                        $sudahDibayarLama = $record?->sudah_dibayar ?? 0;
                        $pembayaranBaru = (float) ($state ?? 0);
                        $totalBaru = $sudahDibayarLama + $pembayaranBaru;
                        $set('sudah_dibayar', $totalBaru);
                    }),

                Forms\Components\TextInput::make('sudah_dibayar')
                    ->label('Sudah Dibayar')
                    ->readOnly()
                    ->numeric()
                    ->prefix('Rp')
                    ->stripCharacters(',')
                    ->formatStateUsing(fn($state, $record) => $record?->sudah_dibayar ?? 0),

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

    private static function updateTransaksiProdukDetails(Set $set, $state): void
    {
        if (!$state) {
            $set('total_harga_modal', '');
            return;
        }

        $transaksiProduk = TransaksiProduk::with(['transaksiProdukDetails'])->find($state);

        if (!$transaksiProduk) {
            return;
        }

        $totalHargaJual = self::calculateTotalHargaJual($transaksiProduk);

        $set('total_harga_modal', $totalHargaJual);
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
                    ->formatStateUsing(fn($state) => ucwords($state)),
                Tables\Columns\TextColumn::make('sudah_dibayar')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_harga_modal')
                    ->label('Total Harga Modal')
                    ->prefix('Rp ')
                    ->numeric(
                        decimalPlaces: 0,
                        decimalSeparator: ',',
                        thousandsSeparator: '.'
                    )
                    ->sortable(),
                Tables\Columns\TextColumn::make('sisa_piutang')
                    ->label('Sisa Piutang')
                    ->prefix('Rp ')
                    ->numeric()
                    ->state(function ($record) {
                        $totalPiutang = (float) $record->total_harga_modal;
                        $sudahDibayar = (float) $record->sudah_dibayar;
                        return $totalPiutang - $sudahDibayar;
                    })
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
            ]);
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
