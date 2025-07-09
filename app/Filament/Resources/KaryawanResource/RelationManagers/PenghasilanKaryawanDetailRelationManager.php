<?php

namespace App\Filament\Resources\KaryawanResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\RawJs;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PenghasilanKaryawanDetailRelationManager extends RelationManager
{
    protected static string $relationship = 'penghasilanKaryawanDetails';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Fieldset::make('Penerimaan')
                    ->schema([
                        Forms\Components\TextInput::make('bonus_target')
                            ->label('Bonus Target')
                            ->mask(RawJs::make('$money($input)'))
                            ->stripCharacters(',')
                            ->numeric()
                            ->prefix('Rp ')
                            ->maxLength(50),
                        Forms\Components\TextInput::make('uang_makan')
                            ->label('Uang Makan')
                            ->mask(RawJs::make('$money($input)'))
                            ->stripCharacters(',')
                            ->numeric()
                            ->prefix('Rp ')
                            ->maxLength(50),
                        Forms\Components\TextInput::make('tunjangan_transportasi')
                            ->label('Tunjangan Transportasi')
                            ->mask(RawJs::make('$money($input)'))
                            ->stripCharacters(',')
                            ->numeric()
                            ->prefix('Rp ')
                            ->maxLength(50),
                        Forms\Components\TextInput::make('thr')
                            ->label('THR')
                            ->mask(RawJs::make('$money($input)'))
                            ->stripCharacters(',')
                            ->numeric()
                            ->prefix('Rp ')
                            ->maxLength(50),
                    ])->columns(2),
                Forms\Components\Fieldset::make('Potongan')
                    ->schema([
                        Forms\Components\TextInput::make('keterlambatan')
                            ->label('Keterlambatan')
                            ->mask(RawJs::make('$money($input)'))
                            ->stripCharacters(',')
                            ->numeric()
                            ->prefix('Rp ')
                            ->maxLength(50),
                        Forms\Components\TextInput::make('tanpa_keterangan')
                            ->label('Tanpa Keterangan')
                            ->mask(RawJs::make('$money($input)'))
                            ->stripCharacters(',')
                            ->numeric()
                            ->prefix('Rp ')
                            ->maxLength(50),
                        Forms\Components\TextInput::make('pinjaman')
                            ->label('Pinjaman')
                            ->mask(RawJs::make('$money($input)'))
                            ->stripCharacters(',')
                            ->numeric()
                            ->prefix('Rp ')
                            ->maxLength(50),
                    ])->columns(2),
                Forms\Components\DatePicker::make('tanggal')
                    ->required(),
                Forms\Components\Textarea::make('remarks')
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('nama')
            ->columns([
                Tables\Columns\TextColumn::make('tanggal')
                    ->date(),
                Tables\Columns\TextColumn::make('bonus_target')
                    ->label('Bonus Target')
                    ->prefix('Rp ')
                    ->numeric()
                    ->summarize(Tables\Columns\Summarizers\Sum::make()->label('Total Bonus Target')->money('IDR')),
                Tables\Columns\TextColumn::make('uang_makan')
                    ->label('Uang Makan')
                    ->prefix('Rp ')
                    ->numeric()
                    ->summarize(Tables\Columns\Summarizers\Sum::make()->label('Total Uang Makan')->money('IDR')),
                Tables\Columns\TextColumn::make('tunjangan_transportasi')
                    ->label('Tunjangan Transportasi')
                    ->prefix('Rp ')
                    ->numeric()
                    ->summarize(Tables\Columns\Summarizers\Sum::make()->label('Total Tunjangan Transportasi')->money('IDR')),
                Tables\Columns\TextColumn::make('thr')
                    ->label('THR')
                    ->prefix('Rp ')
                    ->numeric()
                    ->summarize(Tables\Columns\Summarizers\Sum::make()->label('Total THR')->money('IDR')),
                Tables\Columns\TextColumn::make('keterlambatan')
                    ->label('Keterlambatan')
                    ->prefix('Rp ')
                    ->numeric()
                    ->summarize(Tables\Columns\Summarizers\Sum::make()->label('Total Keterlambatan')->money('IDR')),
                Tables\Columns\TextColumn::make('tanpa_keterangan')
                    ->label('Tanpa Keterangan')
                    ->prefix('Rp ')
                    ->numeric()
                    ->summarize(Tables\Columns\Summarizers\Sum::make()->label('Total Tanpa Keterangan')->money('IDR')),
                Tables\Columns\TextColumn::make('pinjaman')
                    ->label('Pinjaman')
                    ->prefix('Rp ')
                    ->numeric()
                    ->summarize(Tables\Columns\Summarizers\Sum::make()->label('Total Pinjaman')->money('IDR')),
                Tables\Columns\TextColumn::make('remarks')
                    ->label('Remarks')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('tahun')
                    ->label('Filter Tahun')
                    ->options(array_combine(
                        range(date('Y') - 5, date('Y') + 5),
                        range(date('Y') - 5, date('Y') + 5)
                    ))
                    ->default(date('Y'))
                    ->query(fn(Builder $query, array $data) => $query->when(
                        $data['value'],
                        fn(Builder $query, $value) => $query->whereYear('tanggal', $value)
                    )),

                Tables\Filters\SelectFilter::make('bulan')
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
                    ->query(fn(Builder $query, array $data) => $query->when(
                        $data['value'],
                        fn(Builder $query, $value) => $query->whereMonth('tanggal', $value)
                    )),
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
