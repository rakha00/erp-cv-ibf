<?php

namespace Database\Seeders;

use App\Models\Piutang;
use App\Models\TransaksiProduk;
use Illuminate\Database\Seeder;

class PiutangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $transaksiProduks = TransaksiProduk::with('transaksiProdukDetails')->get();
        $now = now();

        if ($transaksiProduks->isEmpty()) {
            $this->command->warn('Skipping PiutangSeeder: No TransaksiProduks found. Please run TransaksiProdukSeeder first.');

            return;
        }

        // Create 15 Piutang entries
        for ($i = 0; $i < 15; $i++) {
            $transaksiProduk = $transaksiProduks->random(); // Select a random TransaksiProduk
            $totalJual = 0;
            foreach ($transaksiProduk->transaksiProdukDetails as $detail) {
                $totalJual += $detail->harga_jual * $detail->jumlah_keluar;
            }

            // Randomly set payment status
            $statusPembayaran = ['belum lunas', 'tercicil', 'sudah lunas'][array_rand(['belum lunas', 'tercicil', 'sudah lunas'])];
            $sudahDibayar = 0;

            if ($statusPembayaran === 'sudah lunas') {
                $sudahDibayar = $totalJual;
            } elseif ($statusPembayaran === 'tercicil') {
                $sudahDibayar = rand(1, (int) ($totalJual * 0.9)); // Pay some portion
            }

            Piutang::create([
                'transaksi_produk_id' => $transaksiProduk->id,
                'jatuh_tempo' => $transaksiProduk->tanggal->addDays(rand(30, 90)), // Due date 30-90 days after transaction
                'status_pembayaran' => $statusPembayaran,
                'sudah_dibayar' => $sudahDibayar,
                'total_harga_modal' => $totalJual, // Renamed to total_harga_jual in migration, but model/seeder still uses total_harga_modal
                'remarks' => 'Pembayaran piutang dari transaksi '.$transaksiProduk->no_invoice,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
}
