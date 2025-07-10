<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('admin'),
        ]);

        $this->call([
            UserSeeder::class,
            PrincipleSubdealerSeeder::class,
            KaryawanSeeder::class,
            UnitProdukSeeder::class,
            AsetSeeder::class,
            BarangMasukSeeder::class,
            TransaksiProdukSeeder::class,
            PenghasilanKaryawanDetailSeeder::class,
            UtangSeeder::class,
            PiutangSeeder::class,
        ]);
    }
}
