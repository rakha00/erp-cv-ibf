<?php

namespace Database\Seeders;

use App\Models\BarangMasuk;
use App\Models\BarangMasukDetail;
use App\Models\PrincipleSubdealer;
use App\Models\UnitProduk;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class BarangMasukSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $principleSubdealers = PrincipleSubdealer::all();
        $unitProduks = UnitProduk::all();
        $now = now();

        if ($principleSubdealers->isEmpty() || $unitProduks->isEmpty()) {
            $this->command->warn('Skipping BarangMasukSeeder: No PrincipleSubdealers or UnitProduks found. Please run their seeders first.');

            return;
        }

        $nomorBarangMasukCounter = 1;

        foreach ($principleSubdealers as $principleSubdealer) {
            // Create 2-3 BarangMasuk entries for each PrincipleSubdealer
            $numberOfEntries = rand(2, 3);
            for ($i = 0; $i < $numberOfEntries; $i++) {
                $tanggal = Carbon::now()->subDays(rand(1, 30));
                $barangMasuk = BarangMasuk::create([
                    'principle_subdealer_id' => $principleSubdealer->id,
                    'nomor_barang_masuk' => 'BM/'.$tanggal->format('Ymd').'-'.str_pad($nomorBarangMasukCounter++, 3, '0', STR_PAD_LEFT),
                    'tanggal' => $tanggal,
                    'remarks' => 'Pembelian rutin dari '.$principleSubdealer->nama,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);

                // Attach 3-5 random UnitProduk to each BarangMasuk
                $selectedUnitProduks = $unitProduks->random(rand(3, 5));
                foreach ($selectedUnitProduks as $unitProduk) {
                    $jumlahBarang = rand(5, 20);
                    BarangMasukDetail::create([
                        'barang_masuk_id' => $barangMasuk->id,
                        'unit_produk_id' => $unitProduk->id,
                        'nama_unit' => $unitProduk->nama_unit,
                        'harga_modal' => $unitProduk->harga_modal,
                        'jumlah_barang_masuk' => $jumlahBarang,
                        'total_harga_modal' => $jumlahBarang * $unitProduk->harga_modal,
                        'remarks' => 'Detail barang masuk untuk '.$unitProduk->nama_unit,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                }
            }
        }
    }
}
