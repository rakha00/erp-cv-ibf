<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TransaksiProdukDetail>
 */
class TransaksiProdukDetailFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'transaksi_produk_id' => \App\Models\TransaksiProduk::factory(),
            'unit_produk_id' => \App\Models\UnitProduk::factory(),
            'nama_unit' => $this->faker->word(),
            'harga_jual' => $this->faker->numberBetween(100000, 2000000),
            'harga_modal' => $this->faker->numberBetween(50000, 1000000),
            'jumlah_keluar' => $this->faker->numberBetween(1, 50),
            'total_keuntungan' => $this->faker->numberBetween(10000, 1000000),
            'remarks' => $this->faker->sentence(),
        ];
    }
}
