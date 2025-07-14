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
        $faker = \Faker\Factory::create('id_ID');

        for ($i = 0; $i < 10; $i++) {
            PrincipleSubdealer::create([
                'nama' => $faker->company,
                'sales' => $faker->name,
                'no_hp' => $faker->phoneNumber,
                'remarks' => $faker->sentence,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
}
