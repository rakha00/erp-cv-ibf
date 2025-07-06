<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KaryawanResource\Pages;
use App\Filament\Resources\KaryawanResource\RelationManagers;
use App\Models\Karyawan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\RawJs;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

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
                Forms\Components\TextInput::make('nama')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('jabatan')
                    ->options([
                        'Staff' => 'Staff',
                        'Teknisi' => 'Teknisi',
                        'Sales' => 'Sales',
                        'Helper' => 'Helper',
                        'Gudang' => 'Gudang',
                    ])
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
                Tables\Columns\TextColumn::make('nama')
                    ->searchable(),
                Tables\Columns\TextColumn::make('jabatan')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('no_hp')
                    ->label('No HP')
                    ->searchable(),
                Tables\Columns\TextColumn::make('gaji_pokok')
                    ->label('Gaji Pokok')
                    ->numeric()
                    ->prefix('Rp ')
                    ->sortable(),
                Tables\Columns\TextColumn::make('lembur')
                    ->numeric()
                    ->prefix('Rp ')
                    ->sortable()
                    ->getStateUsing(fn ($record) => $record->penghasilan_karyawan_details_sum_lembur ?? 0),
                Tables\Columns\TextColumn::make('bonus')
                    ->numeric()
                    ->prefix('Rp ')
                    ->sortable()
                    ->getStateUsing(fn ($record) => $record->penghasilan_karyawan_details_sum_bonus ?? 0),
                Tables\Columns\TextColumn::make('kasbon')
                    ->numeric()
                    ->prefix('Rp ')
                    ->sortable()
                    ->getStateUsing(fn ($record) => $record->penghasilan_karyawan_details_sum_kasbon ?? 0),
                Tables\Columns\TextColumn::make('total_gaji')
                    ->label('Total Gaji')
                    ->numeric()
                    ->prefix('Rp ')
                    ->state(function (Karyawan $record) {
                        $lembur = $record->penghasilan_karyawan_details_sum_lembur ?? 0;
                        $bonus = $record->penghasilan_karyawan_details_sum_bonus ?? 0;
                        return $record->gaji_pokok + $lembur + $bonus;
                    }),
                Tables\Columns\TextColumn::make('gaji_diterima')
                    ->label('Gaji Diterima')
                    ->numeric()
                    ->prefix('Rp ')
                    ->state(function (Karyawan $record) {
                        $lembur = $record->penghasilan_karyawan_details_sum_lembur ?? 0;
                        $bonus = $record->penghasilan_karyawan_details_sum_bonus ?? 0;
                        $kasbon = $record->penghasilan_karyawan_details_sum_kasbon ?? 0;
                        $totalGaji = $record->gaji_pokok + $lembur + $bonus;
                        return $totalGaji - $kasbon;
                    }),
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
                        range(date('Y') - 5, date('Y') + 5),
                        range(date('Y') - 5, date('Y') + 5)
                    ))
                    ->default(date('Y'))
                    ->query(fn (Builder $query, array $data) => $query),

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
                    ->query(fn (Builder $query, array $data) => $query),
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
            RelationManagers\PenghasilanKaryawanDetailsRelationManager::class,
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
}
