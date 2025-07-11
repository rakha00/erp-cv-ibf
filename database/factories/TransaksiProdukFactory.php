<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TransaksiProduk>
 */
class TransaksiProdukFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'no_invoice' => 'INV-' . $this->faker->unique()->randomNumber(5),
            'no_surat_jalan' => 'SJ-' . $this->faker->unique()->randomNumber(5),
            'tanggal' => $this->faker->date(),
            'remarks' => $this->faker->sentence(),
        ];
    }
}
