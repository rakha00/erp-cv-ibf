<?php

namespace App\Filament\Widgets;

use App\Models\Aset;
use App\Models\Karyawan;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AsetKaryawanOverview extends BaseWidget
{
    protected static ?int $sort = 4;

    protected function getStats(): array
    {
        $totalAset = Aset::sum('harga');
        $jumlahKaryawan = Karyawan::count();

        return [
            Stat::make('Total Nilai Aset', 'Rp ' . number_format($totalAset, 0, ',', '.'))
                ->icon('heroicon-o-building-office')
                ->color('primary'),
            Stat::make('Jumlah Karyawan', $jumlahKaryawan)
                ->icon('heroicon-o-users')
                ->color('success'),
        ];
    }

    public function getColumns(): int
    {
        return 2;
    }
}
