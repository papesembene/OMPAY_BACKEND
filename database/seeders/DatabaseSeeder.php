<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Marchant;
use App\Models\Transaction;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::factory(3)->withWallet()->create();
        $merchants = Marchant::factory(2)->create();

        foreach (range(1, 4) as $i) {
            $type = fake()->randomElement(['payment', 'transfer']);
            $user = $users->random();

            Transaction::factory()->create([
                'user_id' => $user->id,
                'wallet_id' => $user->wallet->id,
                'marchant_id' => $type === 'payment' ? $merchants->random()->id : null,
                'type' => $type,
            ]);
        }
    }
}