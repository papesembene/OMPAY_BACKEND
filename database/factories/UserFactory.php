<?php

namespace Database\Factories;

use App\Models\Wallet;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id' => (string) Str::uuid(),
            'name' => $this->faker->name(),
            'phone' => '+221' . $this->faker->unique()->numerify('7########'),
            'secret_code' => bcrypt('1234'), 
            'is_dark_mode' => $this->faker->boolean(30),
            'language' => $this->faker->randomElement(['fr', 'en']),
        ];
    }

    public function withWallet()
    {
        return $this->afterCreating(function ($user) {
            Wallet::factory()->create(['user_id' => $user->id]);
        });
    }
}
