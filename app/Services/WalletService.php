<?php

namespace App\Services;

use App\Contracts\WalletServiceInterface;
use App\Models\Wallet;
use Exception;

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
        if (!$wallet) {
            throw new Exception('Wallet non trouvé');
        }

        if (!isset($wallet->balance)) {
            throw new Exception('Balance non disponible dans le wallet');
        }

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
        if (!$wallet) {
            throw new Exception('Wallet non trouvé');
        }

        if (!isset($wallet->balance)) {
            throw new Exception('Balance non disponible dans le wallet');
        }

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
        if (!$wallet || !isset($wallet->balance)) {
            return false;
        }

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
        if (!$wallet) {
            throw new Exception('Wallet non trouvé');
        }

        if (!isset($wallet->balance)) {
            throw new Exception('Balance non disponible dans le wallet');
        }

        return $wallet->balance;
    }
}