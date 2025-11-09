<?php

namespace App\Services;

use App\Contracts\WalletServiceInterface;
use App\Models\Wallet;

class WalletService implements WalletServiceInterface
{
    /**
     * Débiter un montant du wallet
     *
     * @param Wallet $wallet
     * @param float $amount
     * @return float Ancien solde
     */
    public function debit(Wallet $wallet, float $amount): float
    {
        $ancienSolde = $wallet->balance;
        $wallet->decrement('balance', $amount);
        return $ancienSolde;
    }

    /**
     * Créditer un montant au wallet
     *
     * @param Wallet $wallet
     * @param float $amount
     * @return float Nouveau solde
     */
    public function credit(Wallet $wallet, float $amount): float
    {
        $wallet->increment('balance', $amount);
        return $wallet->fresh()->balance;
    }

    /**
     * Vérifier si le wallet a suffisamment de fonds
     *
     * @param Wallet $wallet
     * @param float $amount
     * @return bool
     */
    public function hasSufficientFunds(Wallet $wallet, float $amount): bool
    {
        return $wallet->balance >= $amount;
    }

    /**
     * Obtenir le solde actuel du wallet
     *
     * @param Wallet $wallet
     * @return float
     */
    public function getBalance(Wallet $wallet): float
    {
        return $wallet->balance;
    }
}