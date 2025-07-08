<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UtangResource\Pages;
use App\Filament\Resources\UtangResource\RelationManagers;
use App\Models\BarangMasuk;
use App\Models\Utang;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Support\RawJs;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UtangResource extends Resource
{
    protected static ?string $model = Utang::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';

    protected static ?string $navigationGroup = 'Reminder';

    protected static ?string $navigationLabel = 'Utang';

    protected static ?string $pluralModelLabel = 'Utang';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('barang_masuk_id')
                    ->label('Barang Masuk')
                    ->options(self::getBarangMasukOptions())
                    ->searchable()
                    ->reactive()
                    ->required()
                    ->afterStateUpdated(fn(Set $set, $state, ?Utang $record) => self::updateBarangMasukDetails($set, $state, $record)),
                Forms\Components\TextInput::make('total_harga_modal')
                    ->label('Total Harga Modal')
                    ->required()
                    ->stripCharacters(',')
                    ->prefix('Rp ')
                    ->disabled()
                    ->dehydrated()
                    ->formatStateUsing(fn($state) => number_format((float) $state, 0, '.', ',')),
                Forms\Components\TextInput::make('nama_principle')
                    ->label('Nama Principle')
                    ->disabled()
                    ->dehydrated()
                    ->formatStateUsing(fn($state, $record) => $record?->barangMasuk?->principleSubdealer?->nama ?? null),
                Forms\Components\DatePicker::make('jatuh_tempo')
                    ->label('Jatuh Tempo')
                    ->required(),
                Forms\Components\TextInput::make('pembayaran_baru')
                    ->label('Pembayaran Baru')
                    ->numeric()
                    ->prefix('Rp')
                    ->mask(RawJs::make('$money($input)'))
                    ->stripCharacters(',')
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (Set $set, Get $get, $state, $record = null) {
                        $sudahDibayarLama = (float) str_replace(',', '', $record?->sudah_dibayar ?? '0');
                        $pembayaranBaru = (float) str_replace(',', '', $state ?? '0');
                        $totalBaru = $sudahDibayarLama + $pembayaranBaru;
                        $set('sudah_dibayar', number_format($totalBaru, 0, '.', ','));
                        $set('pembayaran_baru', number_format($pembayaranBaru, 0, '.', ','));
                    })
                    ->dehydrateStateUsing(function ($state) {
                        if (!$state)
                            return null;
                        return (float) str_replace(',', '', $state);
                    })
                    ->default(null),
                Forms\Components\TextInput::make('sudah_dibayar')
                    ->label('Sudah Dibayar')
                    ->disabled()
                    ->stripCharacters(',')
                    ->dehydrated()
                    ->prefix('Rp')
                    ->formatStateUsing(fn($state) => number_format((float) $state, 0, '.', ',')),
                Forms\Components\FileUpload::make('foto')
                    ->label('Foto Bukti')
                    ->multiple()
                    ->preserveFilenames()
                    ->reorderable()
                    ->directory('utang-foto')
                    ->openable()
                    ->downloadable(),
                Forms\Components\Select::make('status_pembayaran')
                    ->options([
                        'belum lunas' => 'Belum Lunas',
                        'tercicil' => 'Tercicil',
                        'sudah lunas' => 'Sudah Lunas',
                    ])
                    ->required(),
                Forms\Components\Textarea::make('remarks')
                    ->columnSpanFull(),
            ]);
    }

    /**
     * Get options for the BarangMasuk select field.
     */
    private static function getBarangMasukOptions(): array
    {
        return BarangMasuk::with('principleSubdealer')
            ->get()
            ->mapWithKeys(function ($barangMasuk) {
                $formattedDate = \Carbon\Carbon::parse($barangMasuk->tanggal)->format('d-m-Y');
                return [
                    $barangMasuk->id => sprintf(
                        '%s | %s - %s',
                        $barangMasuk->nomor_barang_masuk,
                        $formattedDate,
                        $barangMasuk->principleSubdealer->nama
                    )
                ];
            })
            ->all();
    }

    /**
     * Update form fields based on the selected BarangMasuk.
     */
    private static function updateBarangMasukDetails(Set $set, $state, ?Utang $record = null): void
    {
        if (!$state) {
            if (!$record) {
                $set('total_harga_modal', '');
            }
            $set('nama_principle', '');
            return;
        }

        $barangMasuk = BarangMasuk::with(['barangMasukDetails', 'principleSubdealer'])->find($state);

        if (!$barangMasuk) {
            return;
        }

        if (!$record) {
            $totalHargaModal = self::calculateTotalHargaModal($barangMasuk);
            $set('total_harga_modal', number_format($totalHargaModal, 0, '.', ','));
        }
        $set('nama_principle', $barangMasuk->principleSubdealer->nama ?? '');
    }

    /**
     * Calculate the total modal price from BarangMasukDetails.
     */
    private static function calculateTotalHargaModal(BarangMasuk $barangMasuk): float
    {
        return $barangMasuk->barangMasukDetails->reduce(function ($carry, $detail) {
            return $carry + ($detail->harga_modal * $detail->jumlah_barang_masuk);
        }, 0);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('barangMasuk.nomor_barang_masuk')
                    ->label('No. Barang Masuk')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('barangMasuk.tanggal')
                    ->label('Tanggal Barang Masuk')
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
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('sisa_hutang')
                    ->label('Sisa Hutang')
                    ->prefix('Rp ')
                    ->numeric()
                    ->state(function ($record) {
                        $totalHutang = (float) str_replace(',', '', $record->total_harga_modal);
                        $sudahDibayar = (float) $record->sudah_dibayar;
                        return $totalHutang - $sudahDibayar;
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
                Tables\Filters\SelectFilter::make('status_pembayaran')
                    ->options([
                        'belum lunas' => 'Belum Lunas',
                        'tercicil' => 'Tercicil',
                        'sudah lunas' => 'Sudah Lunas',
                    ])
                    ->label('Status Pembayaran'),
                Tables\Filters\Filter::make('jatuh_tempo')
                    ->form([
                        Forms\Components\DatePicker::make('jatuh_tempo_from')
                            ->label('Jatuh Tempo From'),
                        Forms\Components\DatePicker::make('jatuh_tempo_until')
                            ->label('Jatuh Tempo Until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['jatuh_tempo_from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('jatuh_tempo', '>=', $date),
                            )
                            ->when(
                                $data['jatuh_tempo_until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('jatuh_tempo', '<=', $date),
                            );
                    })
                    ->label('Jatuh Tempo'),
                Tables\Filters\SelectFilter::make('principleSubdealer')
                    ->relationship('barangMasuk.principleSubdealer', 'nama')
                    ->label('Principle/Subdealer'),
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
            'index' => Pages\ListUtangs::route('/'),
            'create' => Pages\CreateUtang::route('/create'),
            'edit' => Pages\EditUtang::route('/{record}/edit'),
        ];
    }
}
