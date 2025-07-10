<?php

namespace Database\Seeders;

use App\Models\BarangMasuk;
use App\Models\BarangMasukDetail;
use App\Models\PrincipleSubdealer;
use App\Models\UnitProduk;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class BarangMasukSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $principle = PrincipleSubdealer::first();
        $products = UnitProduk::all();

        $barangMasuk = BarangMasuk::create([
            'principle_subdealer_id' => $principle->id,
            'nomor_barang_masuk' => 'BM/'.Carbon::now()->format('dmY').'-1',
            'tanggal' => Carbon::now(),
        ]);

        foreach ($products as $product) {
            $jumlah_barang = rand(5, 20);
            BarangMasukDetail::create([
                'barang_masuk_id' => $barangMasuk->id,
                'unit_produk_id' => $product->id,
                'nama_unit' => $product->nama_unit,
                'harga_modal' => $product->harga_modal,
                'jumlah_barang_masuk' => $jumlah_barang,
                'total_harga_modal' => $jumlah_barang * $product->harga_modal,
            ]);
        }
    }
}
