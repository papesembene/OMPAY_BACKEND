<?php

namespace App\Services;

use App\Contracts\TransactionServiceInterface;
use App\Models\Transaction;

class TransactionService implements TransactionServiceInterface
{
    /**
     * Créer une transaction de paiement
     *
     * @param array $data
     * @return Transaction
     */
    public function createPayment(array $data): Transaction
    {
        return Transaction::create([
            'user_id' => auth()->id(),
            'wallet_id' => auth()->user()->wallet->id,
            'amount' => $data['amount'],
            'type' => 'payment',
            'status' => 'success',
            'marchant_id' => $data['merchant_id'],
            'orange_tx_id' => $this->generateReference(),
            'description' => $data['description'] ?? null,
        ]);
    }

    /**
     * Créer une transaction de transfert
     *
     * @param array $data
     * @return Transaction
     */
    public function createTransfer(array $data): Transaction
    {
        return Transaction::create([
            'user_id' => auth()->id(),
            'wallet_id' => auth()->user()->wallet->id,
            'amount' => $data['amount'],
            'type' => 'transfer',
            'status' => 'success',
            'recipient_phone' => $data['recipient_phone'],
            'orange_tx_id' => $this->generateReference(),
            'description' => $data['description'] ?? null,
        ]);
    }

    /**
     * Générer une référence de transaction unique
     *
     * @return string
     */
    public function generateReference(): string
    {
        do {
            $reference = 'TX' . date('YmdHis') . rand(1000, 9999);
        } while (Transaction::where('orange_tx_id', $reference)->exists());

        return $reference;
    }

    /**
     * Mettre à jour le statut d'une transaction
     *
     * @param Transaction $transaction
     * @param string $status
     * @return bool
     */
    public function updateStatus(Transaction $transaction, string $status): bool
    {
        return $transaction->update(['status' => $status]);
    }
}