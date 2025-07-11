<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BarangMasukDetail>
 */
class BarangMasukDetailFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'barang_masuk_id' => \App\Models\BarangMasuk::factory(),
            'unit_produk_id' => \App\Models\UnitProduk::factory(),
            'nama_unit' => $this->faker->word(),
            'harga_modal' => $this->faker->numberBetween(100000, 1000000),
            'jumlah_barang_masuk' => $this->faker->numberBetween(1, 100),
            'total_harga_modal' => $this->faker->numberBetween(100000, 10000000),
            'remarks' => $this->faker->sentence(),
        ];
    }
}
