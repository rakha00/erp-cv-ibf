<?php

namespace App\Filament\Widgets;

use App\Models\Piutang;
use App\Models\Utang;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class PiutangUtangOverview extends BaseWidget
{
    protected static ?int $sort = 2;

    protected function getStats(): array
    {
        $totalPiutang = Piutang::sum(DB::raw('total_harga_modal - sudah_dibayar'));
        $totalUtang = Utang::sum(DB::raw('total_harga_modal - sudah_dibayar'));

        return [
            Stat::make('Total Piutang', 'Rp '.number_format($totalPiutang, 0, ',', '.'))
                ->icon('heroicon-o-arrow-trending-up')
                ->color('warning'),
            Stat::make('Total Utang', 'Rp '.number_format($totalUtang, 0, ',', '.'))
                ->icon('heroicon-o-arrow-trending-down')
                ->color('danger'),
        ];
    }

    public function getColumns(): int
    {
        return 2;
    }
}
