<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Marchant;
use App\Models\Wallet;
use App\Models\Transaction;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create specific test users
        $user1 = User::create([
            'id' => (string) Str::uuid(),
            'name' => 'Dr. Brendon Windler I',
            'phone' => '+221754579919',
            'secret_code' => bcrypt('1234'),
            'is_dark_mode' => false,
            'language' => 'fr',
        ]);

        Wallet::create([
            'id' => (string) Str::uuid(),
            'user_id' => $user1->id,
            'balance' => 15000.00,
        ]);

        $user2 = User::create([
            'id' => (string) Str::uuid(),
            'name' => 'Test User 2',
            'phone' => '+221788888888',
            'secret_code' => bcrypt('1234'),
            'is_dark_mode' => false,
            'language' => 'fr',
        ]);

        Wallet::create([
            'id' => (string) Str::uuid(),
            'user_id' => $user2->id,
            'balance' => 12500.00,
        ]);

        // Create specific test merchant
        $merchant = Marchant::create([
            'id' => (string) Str::uuid(),
            'code' => 'MCH123456',
            'name' => 'Test Merchant',
            'phone' => '+221799999999',
        ]);

        // Create sample transactions
        Transaction::create([
            'id' => (string) Str::uuid(),
            'user_id' => $user1->id,
            'wallet_id' => $user1->wallet->id,
            'type' => 'payment',
            'amount' => 5000.00,
            'status' => 'success',
            'marchant_id' => $merchant->id,
            'recipient_phone' => null,
            'orange_tx_id' => 'TX202511091234567890',
        ]);

        Transaction::create([
            'id' => (string) Str::uuid(),
            'user_id' => $user2->id,
            'wallet_id' => $user2->wallet->id,
            'type' => 'transfer',
            'amount' => 2500.00,
            'status' => 'success',
            'marchant_id' => null,
            'recipient_phone' => '+221754579919',
            'orange_tx_id' => 'TX202511091234567891',
        ]);
    }
}