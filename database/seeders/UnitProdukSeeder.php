<?php

namespace Database\Seeders;

use App\Models\UnitProduk;
use Illuminate\Database\Seeder;

class UnitProdukSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();
        $products = [
            ['sku' => 'AC-PAN-01', 'nama_unit' => 'AC Panasonic 1/2 PK', 'harga_modal' => 25000000, 'stok_awal' => 15, 'remarks' => 'Unit AC Split Panasonic.', 'created_at' => $now, 'updated_at' => $now],
            ['sku' => 'AC-DAI-01', 'nama_unit' => 'AC Daikin 1/2 PK', 'harga_modal' => 28000000, 'stok_awal' => 20, 'remarks' => 'Unit AC Split Daikin.', 'created_at' => $now, 'updated_at' => $now],
            ['sku' => 'TV-SAM-32', 'nama_unit' => 'TV Samsung 32 inch', 'harga_modal' => 20000000, 'stok_awal' => 30, 'remarks' => 'Smart TV LED Samsung.', 'created_at' => $now, 'updated_at' => $now],
            ['sku' => 'KUL-SHA-01', 'nama_unit' => 'Kulkas Sharp 1 Pintu', 'harga_modal' => 15000000, 'stok_awal' => 40, 'remarks' => 'Kulkas satu pintu Sharp.', 'created_at' => $now, 'updated_at' => $now],
            ['sku' => 'MC-SAM-01', 'nama_unit' => 'Mesin Cuci Samsung 1 Tabung', 'harga_modal' => 22000000, 'stok_awal' => 15, 'remarks' => 'Mesin cuci top loading Samsung.', 'created_at' => $now, 'updated_at' => $now],
            ['sku' => 'HP-XIA-01', 'nama_unit' => 'Handphone Xiaomi Redmi', 'harga_modal' => 10000000, 'stok_awal' => 50, 'remarks' => 'Smartphone Android Xiaomi.', 'created_at' => $now, 'updated_at' => $now],
            ['sku' => 'LAP-HP-01', 'nama_unit' => 'Laptop HP Pavilion', 'harga_modal' => 30000000, 'stok_awal' => 10, 'remarks' => 'Laptop untuk kebutuhan komputasi.', 'created_at' => $now, 'updated_at' => $now],
        ];

        UnitProduk::query()->insert($products);
    }
}
