<?php

namespace Database\Seeders;

use App\Models\Karyawan;
use Illuminate\Database\Seeder;

class KaryawanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();
        $faker = \Faker\Factory::create('id_ID');

        for ($i = 0; $i < 10; $i++) {
            Karyawan::create([
                'nik' => $faker->unique()->numerify('##############'),
                'nama' => $faker->name,
                'jabatan' => $faker->randomElement(['Manager Operasional', 'Staff Administrasi', 'Teknisi Lapangan', 'Staff Penjualan', 'Supir Pengiriman']),
                'status' => $faker->randomElement(['Karyawan Tetap', 'Karyawan Kontrak']),
                'no_hp' => $faker->phoneNumber,
                'alamat' => $faker->address,
                'gaji_pokok' => $faker->numberBetween(3000000, 10000000),
                'remarks' => $faker->sentence,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
}
