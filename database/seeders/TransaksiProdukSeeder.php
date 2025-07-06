<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TransaksiProduk;
use App\Models\TransaksiProdukDetail;
use App\Models\UnitProduk;
use Carbon\Carbon;

class TransaksiProdukSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $products = UnitProduk::inRandomOrder()->limit(5)->get();

        $transaksi = TransaksiProduk::create([
            'no_invoice' => 'INV/' . Carbon::now()->format('dmY') . '-1',
            'no_surat_jalan' => 'SJ/' . Carbon::now()->format('dmY') . '-1',
            'tanggal' => Carbon::now(),
        ]);

        foreach ($products as $product) {
            TransaksiProdukDetail::create([
                'transaksi_produk_id' => $transaksi->id,
                'unit_produk_id' => $product->id,
                'harga_jual' => $product->harga_modal * 1.2, // 20% markup
                'jumlah_keluar' => rand(1, 5),
            ]);
        }
    }
}
