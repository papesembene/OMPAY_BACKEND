<?php

namespace App\Contracts;

use App\Models\Transaction;

interface TransactionServiceInterface
{
    /**
     * Créer une transaction de paiement
     *
     * @param array $data
     * @return Transaction
     */
    public function createPayment(array $data): Transaction;

    /**
     * Créer une transaction de transfert
     *
     * @param array $data
     * @return Transaction
     */
    public function createTransfer(array $data): Transaction;

    /**
     * Générer une référence de transaction unique
     *
     * @return string
     */
    public function generateReference(): string;

    /**
     * Mettre à jour le statut d'une transaction
     *
     * @param Transaction $transaction
     * @param string $status
     * @return bool
     */
    public function updateStatus(Transaction $transaction, string $status): bool;
}