<?php

namespace Database\Seeders;

use App\Models\PrincipleSubdealer;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class PrincipleSubdealerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();
        
        PrincipleSubdealer::insert([
            [
                'nama' => 'PT Sentosa Jaya',
                'sales' => 'Rian Pratama',
                'no_hp' => '081234567890',
                'notes' => 'Partner lama sejak 2019',
                'created_at' => $now,
                'updated_at'=> $now,
            ],
            [
                'nama' => 'CV Mitra Teknik',
                'sales' => 'Lidya Marsha',
                'no_hp' => '082198765432',
                'notes' => 'Fokus pemasaran AC inverter',
                'created_at' => $now,
                'updated_at'=> $now,
            ],
            [
                'nama' => 'UD Cahaya Mandiri',
                'sales' => 'Andri Gunawan',
                'no_hp' => '085712345678',
                'notes' => 'Aktif di wilayah Jawa Barat',
                'created_at' => $now,
                'updated_at'=> $now,
            ],
        ]);

    }
}
