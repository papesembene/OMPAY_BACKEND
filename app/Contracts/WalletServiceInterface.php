<?php

namespace App\Contracts;

use App\Models\Wallet;

interface WalletServiceInterface
{
    /**
     * Débiter un montant du wallet
     *
     * @param Wallet $wallet
     * @param float $amount
     * @return float Ancien solde
     */
    public function debit(Wallet $wallet, float $amount): float;

    /**
     * Créditer un montant au wallet
     *
     * @param Wallet $wallet
     * @param float $amount
     * @return float Nouveau solde
     */
    public function credit(Wallet $wallet, float $amount): float;

    /**
     * Vérifier si le wallet a suffisamment de fonds
     *
     * @param Wallet $wallet
     * @param float $amount
     * @return bool
     */
    public function hasSufficientFunds(Wallet $wallet, float $amount): bool;

    /**
     * Obtenir le solde actuel du wallet
     *
     * @param Wallet $wallet
     * @return float
     */
    public function getBalance(Wallet $wallet): float;
}