<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PrincipleSubdealer>
 */
class PrincipleSubdealerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama' => $this->faker->company(),
            'sales' => $this->faker->name(),
            'no_hp' => $this->faker->phoneNumber(),
            'remarks' => $this->faker->sentence(),
        ];
    }
}
