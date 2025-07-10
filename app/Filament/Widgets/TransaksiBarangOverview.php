<?php

namespace App\Filament\Widgets;

use App\Models\BarangMasukDetail;
use App\Models\TransaksiProdukDetail;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class TransaksiBarangOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $totalTransaksiProduk = TransaksiProdukDetail::sum(DB::raw('harga_jual * jumlah_keluar'));
        $totalBarangMasuk = BarangMasukDetail::sum(DB::raw('harga_modal * jumlah_barang_masuk'));

        return [
            Stat::make('Total Transaksi Produk', 'Rp '.number_format($totalTransaksiProduk, 0, ',', '.'))
                ->icon('heroicon-o-currency-dollar')
                ->color('success'),
            Stat::make('Total Barang Masuk', 'Rp '.number_format($totalBarangMasuk, 0, ',', '.'))
                ->icon('heroicon-o-archive-box')
                ->color('info'),
        ];
    }

    public function getColumns(): int
    {
        return 2;
    }
}
