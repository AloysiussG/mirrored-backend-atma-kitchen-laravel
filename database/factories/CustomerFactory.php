<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Customer>
 */
class CustomerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama' => fake()->name(),
            'password' => fake()->password(6, 10),
            'email' => fake()->unique()->safeEmail(),
            'no_telp' => '08' . fake()->unique()->numerify('##########'),
            'saldo' => fake()->numberBetween(0, 250) * 1000,
            'poin' => fake()->numberBetween(0, 300),
            'tanggal_lahir' => fake()->date(),
        ];
    }
}
