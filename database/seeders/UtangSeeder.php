<?php

namespace Database\Seeders;

use App\Models\BarangMasuk;
use App\Models\Utang;
use Illuminate\Database\Seeder;

class UtangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $barangMasuks = BarangMasuk::with('barangMasukDetails')->get();
        $now = now();

        if ($barangMasuks->isEmpty()) {
            $this->command->warn('Skipping UtangSeeder: No BarangMasuks found. Please run BarangMasukSeeder first.');

            return;
        }

        // Create 15 Utang entries
        for ($i = 0; $i < 15; $i++) {
            $barangMasuk = $barangMasuks->random(); // Select a random BarangMasuk
            $totalModal = 0;
            foreach ($barangMasuk->barangMasukDetails as $detail) {
                $totalModal += $detail->total_harga_modal;
            }

            // Randomly set payment status
            $statusPembayaran = ['belum lunas', 'tercicil', 'sudah lunas'][array_rand(['belum lunas', 'tercicil', 'sudah lunas'])];
            $sudahDibayar = 0;

            if ($statusPembayaran === 'sudah lunas') {
                $sudahDibayar = $totalModal;
            } elseif ($statusPembayaran === 'tercicil') {
                $sudahDibayar = rand(1, (int) ($totalModal * 0.9)); // Pay some portion
            }

            Utang::create([
                'barang_masuk_id' => $barangMasuk->id,
                'jatuh_tempo' => $barangMasuk->tanggal->addDays(rand(30, 90)), // Due date 30-90 days after purchase
                'status_pembayaran' => $statusPembayaran,
                'sudah_dibayar' => $sudahDibayar,
                'total_harga_modal' => $totalModal,
                'remarks' => 'Pembayaran utang untuk barang masuk '.$barangMasuk->nomor_barang_masuk,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
}
