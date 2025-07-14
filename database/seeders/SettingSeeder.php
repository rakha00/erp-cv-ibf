<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();

        $settings = [
            [
                'key' => 'karyawan_jabatan_options',
                'key_label' => 'Opsi Jabatan Karyawan',
                'value' => json_encode([
                    ['item' => 'Staff'],
                    ['item' => 'Teknisi'],
                    ['item' => 'Sales'],
                    ['item' => 'Helper'],
                    ['item' => 'Gudang'],
                    ['item' => 'Manager Operasional'],

                ]),
                'type' => 'array',
                'description' => 'Options for Karyawan Jabatan field.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key' => 'karyawan_status_options',
                'key_label' => 'Opsi Status Karyawan',
                'value' => json_encode([
                    ['item' => 'Karyawan Tetap'],
                    ['item' => 'Karyawan Magang'],
                    ['item' => 'Karyawan Kontrak'],
                ]),
                'type' => 'array',
                'description' => 'Options for Karyawan Status field.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key' => 'bank_accounts',
                'key_label' => 'Rekening Bank',
                'value' => json_encode([
                    ['account_name' => 'CV Inti Bintang Fortuna', 'bank_name' => 'Panin', 'account_number' => '5602308115'],
                    // Add more bank accounts here if needed
                ]),
                'type' => 'json',
                'description' => 'Bank accounts for invoice payments.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        Setting::query()->insert($settings);
    }
}
