<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Piutang>
 */
class PiutangFactory extends Factory
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
            'jatuh_tempo' => $this->faker->date(),
            'status_pembayaran' => $this->faker->randomElement(['belum lunas', 'tercicil', 'sudah lunas']),
            'sudah_dibayar' => $this->faker->numberBetween(0, 10000000),
            'total_harga_modal' => $this->faker->numberBetween(1000000, 10000000),
            'foto' => [],
            'remarks' => $this->faker->sentence(),
        ];
    }
}
