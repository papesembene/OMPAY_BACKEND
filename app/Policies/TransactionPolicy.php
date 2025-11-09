<?php

namespace App\Policies;

use App\Models\Transaction;
use App\Models\User;

/**
 * Policy pour contrôler l'accès aux transactions
 */
class TransactionPolicy
{
    /**
     * Déterminer si l'utilisateur peut voir toutes les transactions
     */
    public function viewAny(User $user): bool
    {
        // Seuls les administrateurs peuvent voir toutes les transactions
        return $user->hasRole('admin');
    }

    /**
     * Déterminer si l'utilisateur peut voir la transaction
     */
    public function view(User $user, Transaction $transaction): bool
    {
        // L'utilisateur peut voir ses propres transactions
        return $user->id === $transaction->user_id;
    }

    /**
     * Déterminer si l'utilisateur peut créer des transactions
     */
    public function create(User $user): bool
    {
        // Tous les utilisateurs authentifiés peuvent créer des transactions
        return true;
    }

    /**
     * Déterminer si l'utilisateur peut modifier la transaction
     */
    public function update(User $user, Transaction $transaction): bool
    {
        // Seuls les administrateurs peuvent modifier les transactions
        return $user->hasRole('admin');
    }

    /**
     * Déterminer si l'utilisateur peut supprimer la transaction
     */
    public function delete(User $user, Transaction $transaction): bool
    {
        // Seuls les administrateurs peuvent supprimer les transactions
        return $user->hasRole('admin');
    }

    /**
     * Déterminer si l'utilisateur peut restaurer la transaction
     */
    public function restore(User $user, Transaction $transaction): bool
    {
        // Seuls les administrateurs peuvent restaurer les transactions
        return $user->hasRole('admin');
    }

    /**
     * Déterminer si l'utilisateur peut supprimer définitivement la transaction
     */
    public function forceDelete(User $user, Transaction $transaction): bool
    {
        // Seuls les administrateurs peuvent supprimer définitivement
        return $user->hasRole('admin');
    }
}