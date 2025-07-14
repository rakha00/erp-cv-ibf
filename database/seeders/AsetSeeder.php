<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class AsetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();
        $asets = [
            [
                'nama_aset' => 'Laptop Kantor',
                'harga' => 12000000,
                'jumlah_aset' => 5,
                'remarks' => 'Digunakan oleh tim administrasi dan penjualan.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nama_aset' => 'Printer Laserjet',
                'harga' => 3500000,
                'jumlah_aset' => 2,
                'remarks' => 'Untuk kebutuhan cetak dokumen kantor.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nama_aset' => 'Meja Kerja',
                'harga' => 800000,
                'jumlah_aset' => 10,
                'remarks' => 'Fasilitas untuk karyawan.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nama_aset' => 'Kursi Ergonomis',
                'harga' => 600000,
                'jumlah_aset' => 10,
                'remarks' => 'Fasilitas untuk karyawan.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nama_aset' => 'Proyektor',
                'harga' => 7000000,
                'jumlah_aset' => 1,
                'remarks' => 'Digunakan untuk presentasi dan rapat.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        // Add 2 more assets to reach 7 entries
        $asets[] = [
            'nama_aset' => 'Whiteboard',
            'harga' => 500000,
            'jumlah_aset' => 3,
            'remarks' => 'Untuk kebutuhan rapat dan brainstorming.',
            'created_at' => $now,
            'updated_at' => $now,
        ];
        $asets[] = [
            'nama_aset' => 'AC Split',
            'harga' => 4500000,
            'jumlah_aset' => 4,
            'remarks' => 'Pendingin ruangan di setiap divisi.',
            'created_at' => $now,
            'updated_at' => $now,
        ];

        \App\Models\Aset::query()->insert($asets);
    }
}
