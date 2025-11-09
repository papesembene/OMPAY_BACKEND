<?php

namespace Database\Factories;

use App\Models\Transaction;
use App\Models\Marchant;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class TransactionFactory extends Factory
{
    /**
     * Le modèle associé à cette factory
     *
     * @var string
     */
    protected $model = Transaction::class;

    

    public function definition(): array
    {
        $type = $this->faker->randomElement(['payment', 'transfer']);
        $user = User::factory()->withWallet(); 

        return [
            'id' => (string) Str::uuid(),
            'user_id' => $user,
            'wallet_id' => fn () => $user->wallet->id, 
            'type' => $type,
            'amount' => $this->faker->randomFloat(2, 500, 50000),
            'status' => $this->faker->randomElement(['success', 'pending', 'failed']),
            'marchant_id' => $type === 'payment' ? Marchant::factory() : null,
            'recipient_phone' => $type === 'transfer'
                ? '+221' . $this->faker->numerify('7########')
                : null,
            'orange_tx_id' => 'TX' . $this->faker->unique()->numberBetween(100000, 999999),
        ];
    }
}
