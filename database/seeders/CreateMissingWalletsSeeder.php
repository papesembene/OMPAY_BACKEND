<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Wallet;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CreateMissingWalletsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();

        foreach ($users as $user) {
            // Vérifier si l'utilisateur a déjà un wallet principal
            $hasPrimaryWallet = Wallet::where('user_id', $user->id)
                ->where('is_primary', true)
                ->exists();

            if (!$hasPrimaryWallet) {
                Wallet::create([
                    'id' => Str::uuid(),
                    'user_id' => $user->id,
                    'reference' => 'principal',
                    'label' => 'Compte Principal',
                    'currency' => 'XOF',
                    'is_primary' => true,
                    'balance' => 10000.00 // Solde de test
                ]);

                $this->command->info("Wallet principal créé pour l'utilisateur: {$user->phone}");
            } else {
                $this->command->info("L'utilisateur {$user->phone} a déjà un wallet principal");
            }
        }

        $this->command->info('Vérification et création des wallets terminée.');
    }
}
