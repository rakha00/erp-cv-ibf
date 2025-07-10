<?php

namespace Database\Seeders;

use App\Models\Karyawan;
use App\Models\PenghasilanKaryawanDetail;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class PenghasilanKaryawanDetailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $karyawans = Karyawan::all();
        $now = now();

        if ($karyawans->isEmpty()) {
            $this->command->warn('Skipping PenghasilanKaryawanDetailSeeder: No Karyawans found. Please run KaryawanSeeder first.');

            return;
        }

        foreach ($karyawans as $karyawan) {
            // Create 2-3 income detail entries for each Karyawan
            $numberOfEntries = rand(2, 3);
            for ($i = 0; $i < $numberOfEntries; $i++) {
                $tanggal = Carbon::now()->subMonths(rand(0, 3))->startOfMonth(); // Data for last 3-4 months
                $gajiPokok = $karyawan->gaji_pokok;

                PenghasilanKaryawanDetail::create([
                    'karyawan_id' => $karyawan->id,
                    'bonus_target' => rand(0, 1) ? rand(500000, 1500000) : 0, // 50% chance of bonus
                    'uang_makan' => rand(200000, 500000),
                    'tunjangan_transportasi' => rand(300000, 700000),
                    'thr' => $tanggal->month == 12 ? $gajiPokok : 0, // THR in December
                    'keterlambatan' => rand(0, 1) ? rand(50000, 200000) : 0, // 50% chance of lateness deduction
                    'tanpa_keterangan' => rand(0, 1) ? rand(100000, 300000) : 0, // 50% chance of absence deduction
                    'pinjaman' => rand(0, 1) ? rand(100000, 500000) : 0, // 50% chance of loan deduction
                    'tanggal' => $tanggal,
                    'remarks' => 'Detail penghasilan bulan '.$tanggal->format('F Y'),
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }
}
