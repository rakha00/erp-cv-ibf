<?php

namespace App\Filament\Resources;

use App\Exports\KaryawanExport;
use App\Filament\Resources\KaryawanResource\Pages;
use App\Filament\Resources\KaryawanResource\RelationManagers;
use App\Helpers\SettingHelper;
use App\Models\Karyawan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\RawJs;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\Summarizers\Summarizer;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;

class KaryawanResource extends Resource
{
    protected static ?string $model = Karyawan::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationLabel = 'Karyawan';

    protected static ?string $pluralModelLabel = 'Karyawan';

    protected static ?string $navigationGroup = 'Master Data';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nik')
                    ->label('NIK')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('nama')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('jabatan')
                    ->options(SettingHelper::get('karyawan_jabatan_options', []))
                    ->required(),
                Forms\Components\Select::make('status')
                    ->options(SettingHelper::get('karyawan_status_options', []))
                    ->required(),
                Forms\Components\TextInput::make('no_hp')
                    ->label('No HP')
                    ->required()
                    ->tel()
                    ->maxLength(255),
                Forms\Components\TextInput::make('gaji_pokok')
                    ->label('Gaji Pokok')
                    ->required()
                    ->mask(RawJs::make('$money($input)'))
                    ->stripCharacters(',')
                    ->prefix('Rp ')
                    ->numeric(),
                Forms\Components\Textarea::make('alamat')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('remarks')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nik')
                    ->label('NIK')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nama')
                    ->searchable(),
                Tables\Columns\TextColumn::make('jabatan')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('no_hp')
                    ->label('No HP')
                    ->searchable(),
                Tables\Columns\TextColumn::make('gaji_pokok')
                    ->label('Gaji Pokok')
                    ->numeric()
                    ->prefix('Rp ')
                    ->sortable()
                    ->summarize(Sum::make()->label('Total Gaji Pokok')->prefix('Rp ')),
                Tables\Columns\TextColumn::make('total_penerimaan')
                    ->label('Total Penerimaan')
                    ->numeric()
                    ->prefix('Rp ')
                    ->state(fn(Karyawan $record, $livewire) => self::calculateTotalPenerimaan($record, $livewire))
                    ->summarize(
                        Summarizer::make()
                            ->label('Total Penerimaan')
                            ->prefix('Rp ')
                            ->using(function (QueryBuilder $query, $livewire): float {
                                $total = 0;
                                $karyawanIds = $query->pluck('id')->toArray();
                                $karyawans = \App\Models\Karyawan::whereIn('id', $karyawanIds)->get();

                                foreach ($karyawans as $record) {
                                    $total += self::calculateTotalPenerimaan($record, $livewire);
                                }
                                return $total;
                            })
                            ->numeric()
                    ),
                Tables\Columns\TextColumn::make('total_potongan')
                    ->label('Total Potongan')
                    ->numeric()
                    ->prefix('Rp ')
                    ->state(fn(Karyawan $record, $livewire) => self::calculateTotalPotongan($record, $livewire))
                    ->summarize(
                        Summarizer::make()
                            ->label('Total Potongan')
                            ->prefix('Rp ')
                            ->using(function (QueryBuilder $query, $livewire): float {
                                $total = 0;
                                $karyawanIds = $query->pluck('id')->toArray();
                                $karyawans = \App\Models\Karyawan::whereIn('id', $karyawanIds)->get();

                                foreach ($karyawans as $record) {
                                    $total += self::calculateTotalPotongan($record, $livewire);
                                }
                                return $total;
                            })
                            ->numeric()
                    ),
                Tables\Columns\TextColumn::make('pendapatan_bersih')
                    ->label('Pendapatan Bersih')
                    ->numeric()
                    ->prefix('Rp ')
                    ->state(fn(Karyawan $record, $livewire) => self::calculatePendapatanBersih($record, $livewire))
                    ->summarize(
                        Summarizer::make()
                            ->label('Total Pendapatan Bersih')
                            ->prefix('Rp ')
                            ->using(function (QueryBuilder $query, $livewire): float {
                                $total = 0;
                                $karyawanIds = $query->pluck('id')->toArray();
                                $karyawans = \App\Models\Karyawan::whereIn('id', $karyawanIds)->get();

                                foreach ($karyawans as $record) {
                                    $total += self::calculatePendapatanBersih($record, $livewire);
                                }
                                return $total;
                            })
                            ->numeric()
                    ),
                Tables\Columns\TextColumn::make('remarks')
                    ->label('Remarks')
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
                \Filament\Tables\Filters\SelectFilter::make('tahun')
                    ->label('Filter Tahun')
                    ->options(array_combine(
                        range(date('Y') - 0, date('Y') + 5),
                        range(date('Y') - 0, date('Y') + 5)
                    ))
                    ->default(date('Y'))
                    ->query(fn(Builder $query, array $data) => $query),

                \Filament\Tables\Filters\SelectFilter::make('bulan')
                    ->label('Filter Bulan')
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
                    ->default(date('n'))
                    ->query(fn(Builder $query, array $data) => $query),
            ])
            ->actions([
                Tables\Actions\Action::make('downloadSlipGaji')
                    ->label('Slip Gaji')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('info')
                    ->url(fn(Karyawan $record, $livewire) => self::getSlipGajiDownloadUrl($record, $livewire)),
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
                    ->action(fn(Table $table) => self::exportKaryawanSummary($table)),
                Action::make('exportExcelWithDetails')
                    ->label('Export All (Details) to Excel')
                    ->color('info')
                    ->icon('heroicon-o-document-text')
                    ->action(fn(Table $table) => self::exportKaryawanDetails($table)),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\PenghasilanKaryawanDetailRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListKaryawans::route('/'),
            'create' => Pages\CreateKaryawan::route('/create'),
            'edit' => Pages\EditKaryawan::route('/{record}/edit'),
        ];
    }

    private static function exportKaryawanSummary(Table $table): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $livewire = $table->getLivewire();
        $query = $livewire->getFilteredTableQuery();
        $resourceTitle = static::$pluralModelLabel;
        $tahun = $livewire->tableFilters['tahun']['value'] ?? null;
        $bulan = $livewire->tableFilters['bulan']['value'] ?? null;

        return \Maatwebsite\Excel\Facades\Excel::download(new KaryawanExport($query, $resourceTitle, false, $tahun, $bulan), 'karyawan_summary.xlsx');
    }

    private static function exportKaryawanDetails(Table $table): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $livewire = $table->getLivewire();
        $query = $livewire->getFilteredTableQuery();
        $resourceTitle = static::$pluralModelLabel;
        $tahun = $livewire->tableFilters['tahun']['value'] ?? null;
        $bulan = $livewire->tableFilters['bulan']['value'] ?? null;

        return \Maatwebsite\Excel\Facades\Excel::download(new KaryawanExport($query, $resourceTitle, true, $tahun, $bulan), 'karyawan_details.xlsx');
    }

    private static function getPenghasilanDetails(Karyawan $record, $livewire)
    {
        $tahun = $livewire->tableFilters['tahun']['value'] ?? date('Y');
        $bulan = $livewire->tableFilters['bulan']['value'] ?? date('n');

        return $record->penghasilanKaryawanDetails()
            ->whereYear('tanggal', $tahun)
            ->whereMonth('tanggal', $bulan)
            ->get();
    }

    private static function calculateTotalPenerimaan(Karyawan $record, $livewire): float
    {
        $details = self::getPenghasilanDetails($record, $livewire);

        return $details->sum('bonus_target')
            + $details->sum('uang_makan')
            + $details->sum('tunjangan_transportasi')
            + $details->sum('thr');
    }

    private static function calculateTotalPotongan(Karyawan $record, $livewire): float
    {
        $details = self::getPenghasilanDetails($record, $livewire);

        return $details->sum('keterlambatan')
            + $details->sum('tanpa_keterangan')
            + $details->sum('pinjaman');
    }

    private static function calculatePendapatanBersih(Karyawan $record, $livewire): float
    {
        $totalPenerimaan = self::calculateTotalPenerimaan($record, $livewire);
        $totalPotongan = self::calculateTotalPotongan($record, $livewire);

        return $record->gaji_pokok + $totalPenerimaan - $totalPotongan;
    }

    private static function getSlipGajiDownloadUrl(Karyawan $record, $livewire): string
    {
        $tahun = $livewire->tableFilters['tahun']['value'] ?? date('Y');
        $bulan = $livewire->tableFilters['bulan']['value'] ?? date('n');

        return route('karyawan.slip-gaji', ['karyawan' => $record, 'tahun' => $tahun, 'bulan' => $bulan]);
    }
}
