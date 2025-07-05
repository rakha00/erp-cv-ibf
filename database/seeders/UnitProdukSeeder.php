<?php

namespace Database\Seeders;

use App\Models\UnitProduk;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class UnitProdukSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        UnitProduk::insert([
            [
                'sku' => 'AC-LG-SPL12K-STD',
                'nama_unit' => 'AC LG Split 1.5PK Standard',
                'harga_modal' => 3450000,
                'stok_awal' => 10,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'sku' => 'AC-SAM-INV09K-PREM',
                'nama_unit' => 'AC Samsung Inverter 0.75PK Premium',
                'harga_modal' => 4100000,
                'stok_awal' => 8,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'sku' => 'AC-DAIK-SPL18K-HEAT',
                'nama_unit' => 'AC Daikin Split 2PK dengan Pemanas',
                'harga_modal' => 6200000,
                'stok_awal' => 5,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'sku' => 'AC-PAN-WIN10K-BASIC',
                'nama_unit' => 'AC Panasonic Window 1.25PK Basic',
                'harga_modal' => 3100000,
                'stok_awal' => 12,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'sku' => 'AC-MITS-CAS24K-INV',
                'nama_unit' => 'AC Mitsubishi Cassette 2.5PK Inverter',
                'harga_modal' => 7850000,
                'stok_awal' => 4,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}
