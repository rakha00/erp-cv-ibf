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

class PenghasilanKaryawanDetailsRelationManager extends RelationManager
{
    protected static string $relationship = 'penghasilanKaryawanDetails';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('kasbon')
                    ->mask(RawJs::make('$money($input)'))
                    ->stripCharacters(',')
                    ->numeric()
                    ->prefix('Rp ')
                    ->default(0)
                    ->maxLength(50),
                Forms\Components\TextInput::make('lembur')
                    ->mask(RawJs::make('$money($input)'))
                    ->stripCharacters(',')
                    ->numeric()
                    ->prefix('Rp ')
                    ->default(0)
                    ->maxLength(50),
                Forms\Components\TextInput::make('bonus')
                    ->mask(RawJs::make('$money($input)'))
                    ->stripCharacters(',')
                    ->numeric()
                    ->prefix('Rp ')
                    ->default(0)
                    ->maxLength(50),
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
                Tables\Columns\TextColumn::make('kasbon')
                    ->prefix('Rp ')
                    ->numeric()
                    ->summarize(Tables\Columns\Summarizers\Sum::make()->label('Total Kasbon')->money('IDR')),
                Tables\Columns\TextColumn::make('lembur')
                    ->prefix('Rp ')
                    ->numeric()
                    ->summarize(Tables\Columns\Summarizers\Sum::make()->label('Total Lembur')->money('IDR')),
                Tables\Columns\TextColumn::make('bonus')
                    ->prefix('Rp ')
                    ->numeric()
                    ->summarize(Tables\Columns\Summarizers\Sum::make()->label('Total Bonus')->money('IDR')),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('tahun')
                    ->label('Filter Tahun')
                    ->options(array_combine(
                        range(date('Y') - 5, date('Y') + 5),
                        range(date('Y') - 5, date('Y') + 5)
                    ))
                    ->default(date('Y'))
                    ->query(fn (Builder $query, array $data) => $query->when(
                        $data['value'],
                        fn (Builder $query, $value) => $query->whereYear('tanggal', $value)
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
                    ->query(fn (Builder $query, array $data) => $query->when(
                        $data['value'],
                        fn (Builder $query, $value) => $query->whereMonth('tanggal', $value)
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
