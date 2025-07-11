<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Aset>
 */
class AsetFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama_aset' => $this->faker->word(),
            'harga' => $this->faker->numberBetween(1000000, 100000000),
            'jumlah_aset' => $this->faker->numberBetween(1, 10),
        ];
    }
}
