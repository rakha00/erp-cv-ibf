<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Karyawan>
 */
class KaryawanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nik' => $this->faker->unique()->randomNumber(9, true),
            'nama' => $this->faker->name(),
            'jabatan' => $this->faker->jobTitle(),
            'status' => $this->faker->randomElement(['aktif', 'non-aktif']),
            'no_hp' => $this->faker->phoneNumber(),
            'alamat' => $this->faker->address(),
            'gaji_pokok' => $this->faker->numberBetween(3000000, 10000000),
            'remarks' => $this->faker->sentence(),
        ];
    }
}
