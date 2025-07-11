<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UnitProduk>
 */
class UnitProdukFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'sku' => $this->faker->unique()->ean8(),
            'nama_unit' => $this->faker->word(),
            'harga_modal' => $this->faker->numberBetween(100000, 1000000),
            'stok_awal' => $this->faker->numberBetween(1, 100),
            'remarks' => $this->faker->sentence(),
        ];
    }
}
