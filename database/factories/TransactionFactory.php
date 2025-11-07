<?php

namespace Database\Factories;

use App\Models\Marchant;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class TransactionFactory extends Factory
{
    public function definition(): array
    {
        $type = $this->faker->randomElement(['payment', 'transfer']);

        return [
            'id' => (string) Str::uuid(),
            'user_id' => null,
            'type' => $type,
            'amount' => $this->faker->randomFloat(2, 500, 50000),
            'status' => $this->faker->randomElement(['success', 'pending', 'failed']),
            // Si payment → merchant_id, sinon null
            'marchant_id' => $type === 'payment' ? Marchant::factory() : null,
            // Si transfer → phone, sinon null
            'recipient_phone' => $type === 'transfer'
                ? '+221' . $this->faker->numerify('7########')
                : null,
            // Simulation : orange_tx_id (ex: TX123456)
            'orange_tx_id' => 'TX' . $this->faker->unique()->numberBetween(100000, 999999),
        ];
    }
}