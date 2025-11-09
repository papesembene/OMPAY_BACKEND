<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class WalletFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    // database/factories/WalletFactory.php
    public function definition(): array
    {
        return [
            'id' => \Illuminate\Support\Str::uuid(),
            'user_id' => \App\Models\User::factory(),
            'balance' => $this->faker->randomFloat(2, 1000, 50000),
        ];
    }
}
