<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Karyawan;

class KaryawanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Karyawan::create([
            'nik' => '1234567890',
            'nama' => 'Budi Santoso',
            'jabatan' => 'Staff',
            'status' => 'Karyawan Tetap',
            'no_hp' => '081234567890',
            'alamat' => 'Jl. Merdeka No. 10, Jakarta',
            'gaji_pokok' => 5000000,
        ]);

        Karyawan::create([
            'nik' => '9876543210',
            'nama' => 'Siti Aminah',
            'jabatan' => 'Teknisi',
            'status' => 'Karyawan Magang',
            'no_hp' => '087654321098',
            'alamat' => 'Jl. Pahlawan No. 20, Surabaya',
            'gaji_pokok' => 6000000,
        ]);
    }
}
