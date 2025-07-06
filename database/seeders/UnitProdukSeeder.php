<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\UnitProduk;

class UnitProdukSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $products = [
            ['sku' => 'AC-PAN-01', 'nama_unit' => 'AC Panasonic 1/2 PK', 'harga_modal' => 2500000, 'stok_awal' => 15],
            ['sku' => 'AC-PAN-02', 'nama_unit' => 'AC Panasonic 1 PK', 'harga_modal' => 3500000, 'stok_awal' => 10],
            ['sku' => 'AC-DAI-01', 'nama_unit' => 'AC Daikin 1/2 PK', 'harga_modal' => 2800000, 'stok_awal' => 20],
            ['sku' => 'AC-DAI-02', 'nama_unit' => 'AC Daikin 1 PK', 'harga_modal' => 3800000, 'stok_awal' => 12],
            ['sku' => 'AC-SHA-01', 'nama_unit' => 'AC Sharp 1/2 PK', 'harga_modal' => 2300000, 'stok_awal' => 25],
            ['sku' => 'AC-SHA-02', 'nama_unit' => 'AC Sharp 1 PK', 'harga_modal' => 3200000, 'stok_awal' => 18],
            ['sku' => 'TV-SAM-32', 'nama_unit' => 'TV Samsung 32 inch', 'harga_modal' => 2000000, 'stok_awal' => 30],
            ['sku' => 'TV-SAM-43', 'nama_unit' => 'TV Samsung 43 inch', 'harga_modal' => 4000000, 'stok_awal' => 20],
            ['sku' => 'TV-LG-32', 'nama_unit' => 'TV LG 32 inch', 'harga_modal' => 1900000, 'stok_awal' => 35],
            ['sku' => 'TV-LG-43', 'nama_unit' => 'TV LG 43 inch', 'harga_modal' => 3800000, 'stok_awal' => 22],
            ['sku' => 'KUL-SHA-01', 'nama_unit' => 'Kulkas Sharp 1 Pintu', 'harga_modal' => 1500000, 'stok_awal' => 40],
            ['sku' => 'KUL-SHA-02', 'nama_unit' => 'Kulkas Sharp 2 Pintu', 'harga_modal' => 2500000, 'stok_awal' => 25],
            ['sku' => 'KUL-LG-01', 'nama_unit' => 'Kulkas LG 1 Pintu', 'harga_modal' => 1600000, 'stok_awal' => 38],
            ['sku' => 'KUL-LG-02', 'nama_unit' => 'Kulkas LG 2 Pintu', 'harga_modal' => 2600000, 'stok_awal' => 28],
            ['sku' => 'MC-SAM-01', 'nama_unit' => 'Mesin Cuci Samsung 1 Tabung', 'harga_modal' => 2200000, 'stok_awal' => 15],
            ['sku' => 'MC-SAM-02', 'nama_unit' => 'Mesin Cuci Samsung 2 Tabung', 'harga_modal' => 1800000, 'stok_awal' => 20],
            ['sku' => 'MC-LG-01', 'nama_unit' => 'Mesin Cuci LG 1 Tabung', 'harga_modal' => 2100000, 'stok_awal' => 18],
            ['sku' => 'MC-LG-02', 'nama_unit' => 'Mesin Cuci LG 2 Tabung', 'harga_modal' => 1700000, 'stok_awal' => 22],
        ];

        foreach ($products as $product) {
            UnitProduk::create($product);
        }
    }
}
