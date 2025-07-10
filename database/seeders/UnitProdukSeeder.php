<?php

namespace Database\Seeders;

use App\Models\UnitProduk;
use Illuminate\Database\Seeder;

class UnitProdukSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $now = now();
        $products = [
            ['sku' => 'AC-PAN-01', 'nama_unit' => 'AC Panasonic 1/2 PK', 'harga_modal' => 2500000, 'stok_awal' => 15, 'remarks' => 'Unit AC Split Panasonic.', 'created_at' => $now, 'updated_at' => $now],
            ['sku' => 'AC-PAN-02', 'nama_unit' => 'AC Panasonic 1 PK', 'harga_modal' => 3500000, 'stok_awal' => 10, 'remarks' => 'Unit AC Split Panasonic.', 'created_at' => $now, 'updated_at' => $now],
            ['sku' => 'AC-DAI-01', 'nama_unit' => 'AC Daikin 1/2 PK', 'harga_modal' => 2800000, 'stok_awal' => 20, 'remarks' => 'Unit AC Split Daikin.', 'created_at' => $now, 'updated_at' => $now],
            ['sku' => 'AC-DAI-02', 'nama_unit' => 'AC Daikin 1 PK', 'harga_modal' => 3800000, 'stok_awal' => 12, 'remarks' => 'Unit AC Split Daikin.', 'created_at' => $now, 'updated_at' => $now],
            ['sku' => 'AC-SHA-01', 'nama_unit' => 'AC Sharp 1/2 PK', 'harga_modal' => 2300000, 'stok_awal' => 25, 'remarks' => 'Unit AC Split Sharp.', 'created_at' => $now, 'updated_at' => $now],
            ['sku' => 'AC-SHA-02', 'nama_unit' => 'AC Sharp 1 PK', 'harga_modal' => 3200000, 'stok_awal' => 18, 'remarks' => 'Unit AC Split Sharp.', 'created_at' => $now, 'updated_at' => $now],
            ['sku' => 'TV-SAM-32', 'nama_unit' => 'TV Samsung 32 inch', 'harga_modal' => 2000000, 'stok_awal' => 30, 'remarks' => 'Smart TV LED Samsung.', 'created_at' => $now, 'updated_at' => $now],
            ['sku' => 'TV-SAM-43', 'nama_unit' => 'TV Samsung 43 inch', 'harga_modal' => 4000000, 'stok_awal' => 20, 'remarks' => 'Smart TV LED Samsung.', 'created_at' => $now, 'updated_at' => $now],
            ['sku' => 'TV-LG-32', 'nama_unit' => 'TV LG 32 inch', 'harga_modal' => 1900000, 'stok_awal' => 35, 'remarks' => 'Smart TV LED LG.', 'created_at' => $now, 'updated_at' => $now],
            ['sku' => 'TV-LG-43', 'nama_unit' => 'TV LG 43 inch', 'harga_modal' => 3800000, 'stok_awal' => 22, 'remarks' => 'Smart TV LED LG.', 'created_at' => $now, 'updated_at' => $now],
            ['sku' => 'KUL-SHA-01', 'nama_unit' => 'Kulkas Sharp 1 Pintu', 'harga_modal' => 1500000, 'stok_awal' => 40, 'remarks' => 'Kulkas satu pintu Sharp.', 'created_at' => $now, 'updated_at' => $now],
            ['sku' => 'KUL-SHA-02', 'nama_unit' => 'Kulkas Sharp 2 Pintu', 'harga_modal' => 2500000, 'stok_awal' => 25, 'remarks' => 'Kulkas dua pintu Sharp.', 'created_at' => $now, 'updated_at' => $now],
            ['sku' => 'KUL-LG-01', 'nama_unit' => 'Kulkas LG 1 Pintu', 'harga_modal' => 1600000, 'stok_awal' => 38, 'remarks' => 'Kulkas satu pintu LG.', 'created_at' => $now, 'updated_at' => $now],
            ['sku' => 'KUL-LG-02', 'nama_unit' => 'Kulkas LG 2 Pintu', 'harga_modal' => 2600000, 'stok_awal' => 28, 'remarks' => 'Kulkas dua pintu LG.', 'created_at' => $now, 'updated_at' => $now],
            ['sku' => 'MC-SAM-01', 'nama_unit' => 'Mesin Cuci Samsung 1 Tabung', 'harga_modal' => 2200000, 'stok_awal' => 15, 'remarks' => 'Mesin cuci top loading Samsung.', 'created_at' => $now, 'updated_at' => $now],
            ['sku' => 'MC-SAM-02', 'nama_unit' => 'Mesin Cuci Samsung 2 Tabung', 'harga_modal' => 1800000, 'stok_awal' => 20, 'remarks' => 'Mesin cuci twin tub Samsung.', 'created_at' => $now, 'updated_at' => $now],
            ['sku' => 'MC-LG-01', 'nama_unit' => 'Mesin Cuci LG 1 Tabung', 'harga_modal' => 2100000, 'stok_awal' => 18, 'remarks' => 'Mesin cuci top loading LG.', 'created_at' => $now, 'updated_at' => $now],
            ['sku' => 'MC-LG-02', 'nama_unit' => 'Mesin Cuci LG 2 Tabung', 'harga_modal' => 1700000, 'stok_awal' => 22, 'remarks' => 'Mesin cuci twin tub LG.', 'created_at' => $now, 'updated_at' => $now],
        ];

        UnitProduk::query()->insert($products);
    }
}
