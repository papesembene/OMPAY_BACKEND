<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Wallet;

/**
 * Policy pour contrôler l'accès aux wallets
 */
class WalletPolicy
{
    /**
     * Déterminer si l'utilisateur peut voir le wallet
     */
    public function view(User $user, Wallet $wallet): bool
    {
        // L'utilisateur peut voir son propre wallet
        return $user->id === $wallet->user_id;
    }

    /**
     * Déterminer si l'utilisateur peut modifier le wallet
     */
    public function update(User $user, Wallet $wallet): bool
    {
        // L'utilisateur peut modifier son propre wallet
        return $user->id === $wallet->user_id;
    }

    /**
     * Déterminer si l'utilisateur peut créditer le wallet
     */
    public function credit(User $user, Wallet $wallet): bool
    {
        // Seuls les administrateurs peuvent créditer les wallets
        return $user->hasRole('admin');
    }

    /**
     * Déterminer si l'utilisateur peut débiter le wallet
     */
    public function debit(User $user, Wallet $wallet): bool
    {
        // L'utilisateur peut débiter son propre wallet (via les transactions)
        return $user->id === $wallet->user_id;
    }
}