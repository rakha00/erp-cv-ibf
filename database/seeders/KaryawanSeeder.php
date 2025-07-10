<?php

namespace Database\Seeders;

use App\Models\Karyawan;
use Illuminate\Database\Seeder;

class KaryawanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $now = now();
        $karyawans = [
            [
                'nik' => '3273010101900001',
                'nama' => 'Budi Santoso',
                'jabatan' => 'Manager Operasional',
                'status' => 'Karyawan Tetap',
                'no_hp' => '081234567890',
                'alamat' => 'Jl. Merdeka Raya No. 10, Bandung',
                'gaji_pokok' => 8000000,
                'remarks' => 'Manager yang bertanggung jawab atas seluruh operasional harian.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nik' => '3273010202920002',
                'nama' => 'Siti Aminah',
                'jabatan' => 'Staff Administrasi',
                'status' => 'Karyawan Tetap',
                'no_hp' => '081345678901',
                'alamat' => 'Jl. Asia Afrika No. 20, Bandung',
                'gaji_pokok' => 4500000,
                'remarks' => 'Bertanggung jawab untuk urusan administrasi dan kearsipan.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nik' => '3273010303950003',
                'nama' => 'Joko Susilo',
                'jabatan' => 'Teknisi Lapangan',
                'status' => 'Karyawan Kontrak',
                'no_hp' => '081456789012',
                'alamat' => 'Jl. Sudirman No. 30, Cimahi',
                'gaji_pokok' => 5500000,
                'remarks' => 'Spesialis instalasi dan perbaikan AC.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nik' => '3273010404980004',
                'nama' => 'Dewi Lestari',
                'jabatan' => 'Staff Penjualan',
                'status' => 'Karyawan Tetap',
                'no_hp' => '081567890123',
                'alamat' => 'Jl. Gatot Subroto No. 40, Bandung',
                'gaji_pokok' => 6000000,
                'remarks' => 'Mencapai target penjualan bulanan dengan baik.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nik' => '3273010505000005',
                'nama' => 'Rizky Pratama',
                'jabatan' => 'Supir Pengiriman',
                'status' => 'Karyawan Kontrak',
                'no_hp' => '081678901234',
                'alamat' => 'Jl. Buah Batu No. 50, Bandung',
                'gaji_pokok' => 4000000,
                'remarks' => 'Bertanggung jawab untuk pengiriman barang ke pelanggan.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        Karyawan::query()->insert($karyawans);
    }
}
