<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Utang;
use App\Models\BarangMasuk;
use Carbon\Carbon;

class UtangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $barangMasuk = BarangMasuk::with('barangMasukDetails')->first();
        $totalModal = $barangMasuk->barangMasukDetails->sum(function ($detail) {
            return $detail->harga_modal * $detail->jumlah_barang_masuk;
        });

        Utang::create([
            'barang_masuk_id' => $barangMasuk->id,
            'jatuh_tempo' => Carbon::now()->addDays(30),
            'total_harga_modal' => $totalModal,
        ]);
    }
}
