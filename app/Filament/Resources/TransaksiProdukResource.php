<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransaksiProdukResource\Pages;
use App\Filament\Resources\TransaksiProdukResource\RelationManagers;
use App\Models\TransaksiProduk;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
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
                    ->afterStateUpdated(function ($state, $set) {
                        if ($state) {
                            $date = Carbon::parse($state);
                            $formatDate = $date->format('dmY');
                            $nextNumber = TransaksiProduk::whereDate('tanggal', $state)->count() + 1;

                            // Set both invoice and delivery note numbers
                            $set('no_invoice', "INV/{$formatDate}-{$nextNumber}");
                            $set('no_surat_jalan', "SJ/{$formatDate}-{$nextNumber}");
                        }
                    }),
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
                    ->getStateUsing(
                        fn(TransaksiProduk $record): int =>
                        $record->transaksiProdukDetails()
                            ->selectRaw('SUM(harga_jual * jumlah_keluar) as total')
                            ->value('total') ?? 0
                    ),
                Tables\Columns\TextColumn::make('total_keuntungan')
                    ->label('Total Keuntungan')
                    ->getStateUsing(
                        fn(TransaksiProduk $record): int =>
                        $record->transaksiProdukDetails()
                            ->selectRaw('SUM((harga_jual - harga_modal) * jumlah_keluar) as total')
                            ->value('total') ?? 0
                    ),
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
                        $years = range(date('Y') - 0, date('Y') + 3);
                        return array_combine($years, $years);
                    })
                    ->query(function (Builder $query, array $data) {
                        return $query->when(
                            $data['value'],
                            fn(Builder $q) => $q->whereYear('tanggal', $data['value'])
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
                            fn(Builder $q) => $q->whereMonth('tanggal', $data['value'])
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
                            fn(Builder $q) => $q->whereBetween('tanggal', [$data['from'], $data['until']])
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
}
