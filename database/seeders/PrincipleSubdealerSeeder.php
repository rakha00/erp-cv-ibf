<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PrincipleSubdealer;

class PrincipleSubdealerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        PrincipleSubdealer::create([
            'nama' => 'PT. Sejahtera Abadi',
            'sales' => 'Bambang',
            'no_hp' => '081234567891',
        ]);

        PrincipleSubdealer::create([
            'nama' => 'CV. Maju Jaya',
            'sales' => 'Joko',
            'no_hp' => '087654321092',
        ]);
    }
}
