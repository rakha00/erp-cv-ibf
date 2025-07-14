<?php

namespace App\Filament\Widgets;

use App\Models\Utang;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class UtangList extends BaseWidget
{
    protected static ?int $sort = 3;

    protected static ?string $heading = 'Daftar Utang Belum Lunas / Tercicil';

    public function getColumns(): int
    {
        return 2;
    }

    protected function getTableQuery(): Builder
    {
        return Utang::query()
            ->join('barang_masuks', 'utangs.barang_masuk_id', '=', 'barang_masuks.id')
            ->whereIn('utangs.status_pembayaran', ['belum lunas', 'tercicil'])
            ->selectRaw('barang_masuks.nomor_barang_masuk as reference, utangs.jatuh_tempo, utangs.status_pembayaran as status, utangs.id');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                $this->getTableQuery()
            )
            ->columns([
                TextColumn::make('reference')
                    ->label('No. Referensi')
                    ->sortable('barang_masuks.nomor_barang_masuk'),
                TextColumn::make('jatuh_tempo')
                    ->label('Jatuh Tempo')
                    ->date()
                    ->sortable(),
                BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'danger' => 'belum lunas',
                        'warning' => 'tercicil',
                        'success' => 'lunas',
                    ])
                    ->formatStateUsing(fn ($state) => ucwords($state))
                    ->sortable('utangs.status_pembayaran'),
            ])
            ->defaultSort('jatuh_tempo', 'asc');
    }
}
