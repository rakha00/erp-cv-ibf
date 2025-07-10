<?php

namespace Database\Seeders;

use App\Models\PrincipleSubdealer;
use Illuminate\Database\Seeder;

class PrincipleSubdealerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();
        $principleSubdealers = [
            [
                'nama' => 'PT. Sinar Jaya Elektronik',
                'sales' => 'Agus Setiawan',
                'no_hp' => '081234567890',
                'remarks' => 'Principle resmi produk Panasonic dan Sharp.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nama' => 'CV. Mitra Abadi',
                'sales' => 'Budi Hartono',
                'no_hp' => '082345678901',
                'remarks' => 'Subdealer untuk area Jakarta Timur.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nama' => 'UD. Cahaya Terang',
                'sales' => 'Citra Lestari',
                'no_hp' => '083456789012',
                'remarks' => 'Fokus pada penjualan produk LG.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nama' => 'PT. Global Elektronik',
                'sales' => 'Dewi Anggraini',
                'no_hp' => '084567890123',
                'remarks' => 'Principle resmi Daikin dan Samsung.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        PrincipleSubdealer::query()->insert($principleSubdealers);
    }
}
