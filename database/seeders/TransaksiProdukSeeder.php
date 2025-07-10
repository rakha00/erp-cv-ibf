<?php

namespace Database\Seeders;

use App\Models\TransaksiProduk;
use App\Models\TransaksiProdukDetail;
use App\Models\UnitProduk;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class TransaksiProdukSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $unitProduks = UnitProduk::all();
        $now = now();

        if ($unitProduks->isEmpty()) {
            $this->command->warn('Skipping TransaksiProdukSeeder: No UnitProduks found. Please run UnitProdukSeeder first.');
            return;
        }

        $noInvoiceCounter = 1;
        $noSuratJalanCounter = 1;

        // Create multiple TransaksiProduk entries
        for ($i = 0; $i < 5; $i++) { // Create 5 transaction records
            $tanggal = Carbon::now()->subDays(rand(1, 60));
            $transaksi = TransaksiProduk::create([
                'no_invoice' => 'INV/' . $tanggal->format('Ymd') . '-' . str_pad($noInvoiceCounter++, 3, '0', STR_PAD_LEFT),
                'no_surat_jalan' => 'SJ/' . $tanggal->format('Ymd') . '-' . str_pad($noSuratJalanCounter++, 3, '0', STR_PAD_LEFT),
                'tanggal' => $tanggal,
                'remarks' => 'Penjualan produk elektronik.',
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            // Attach 2-4 random UnitProduk to each TransaksiProduk
            $selectedUnitProduks = $unitProduks->random(rand(2, 4));
            foreach ($selectedUnitProduks as $product) {
                $jumlahKeluar = rand(1, 5);
                $hargaJual = $product->harga_modal * (1 + (rand(10, 30) / 100)); // Mark up price by 10-30%
                $keuntungan = ($hargaJual - $product->harga_modal) * $jumlahKeluar;

                TransaksiProdukDetail::create([
                    'transaksi_produk_id' => $transaksi->id,
                    'unit_produk_id' => $product->id,
                    'nama_unit' => $product->nama_unit,
                    'harga_jual' => round($hargaJual),
                    'harga_modal' => $product->harga_modal,
                    'jumlah_keluar' => $jumlahKeluar,
                    'total_keuntungan' => round($keuntungan),
                    'remarks' => 'Detail produk ' . $product->nama_unit,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }
}
