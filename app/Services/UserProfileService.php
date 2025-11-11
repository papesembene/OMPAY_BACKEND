<?php

namespace App\Services;

use App\Models\User;
use App\Http\Resources\TransactionResource;
use Illuminate\Http\Request;

class UserProfileService
{
    /**
     * Récupérer le profil complet de l'utilisateur
     */
    public function getCompleteProfile(User $user): array
    {
        return [
            'user' => $this->getUserData($user),
            'wallets' => $this->getWalletsData($user),
            'recent_transactions' => $this->getRecentTransactions($user)
        ];
    }

    /**
     * Récupérer les données de l'utilisateur
     */
    private function getUserData(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'phone' => $user->phone,
        ];
    }

    /**
     * Récupérer les données des wallets
     */
    private function getWalletsData(User $user): array
    {
        return $user->wallets->map(function($wallet) {
            return [
                'reference' => $wallet->reference,
                'label' => $wallet->label,
                'balance' => $wallet->balance,
                'currency' => $wallet->currency,
                'is_primary' => $wallet->is_primary
            ];
        })->toArray();
    }

    /**
     * Récupérer les transactions récentes
     */
    private function getRecentTransactions(User $user): array
    {
        return $user->transactions()
            ->with(['marchant', 'user'])
            ->latest()
            ->limit(5)
            ->get()
            ->map(function($transaction) {
                return (new TransactionResource($transaction))->toArray(request());
            })
            ->toArray();
    }
}