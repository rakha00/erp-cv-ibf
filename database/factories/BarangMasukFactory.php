<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BarangMasuk>
 */
class BarangMasukFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'principle_subdealer_id' => \App\Models\PrincipleSubdealer::factory(),
            'nomor_barang_masuk' => 'BM-' . $this->faker->unique()->randomNumber(5),
            'tanggal' => $this->faker->date(),
            'remarks' => $this->faker->sentence(),
        ];
    }
}
