<?php

namespace Database\Seeders;

use App\Models\Piutang;
use App\Models\TransaksiProduk;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class PiutangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $transaksiProduk = TransaksiProduk::with('transaksiProdukDetails')->first();
        $totalJual = $transaksiProduk->transaksiProdukDetails->sum(function ($detail) {
            return $detail->harga_jual * $detail->jumlah_keluar;
        });

        Piutang::create([
            'transaksi_produk_id' => $transaksiProduk->id,
            'jatuh_tempo' => Carbon::now()->addDays(30),
            'total_harga_modal' => $totalJual,
        ]);
    }
}
