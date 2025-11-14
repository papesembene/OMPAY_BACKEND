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
     * @param \App\Models\Wallet|null $wallet
     * @return Transaction
     */
    public function createPayment(array $data, ?\App\Models\Wallet $wallet = null): Transaction
    {
        // Trouver le marchand par code ou téléphone
        $merchant = null;
        if (isset($data['merchant_code'])) {
            $merchant = \App\Models\Marchant::where('code', $data['merchant_code'])->first();
        } elseif (isset($data['merchant_phone'])) {
            $merchant = \App\Models\Marchant::where('phone', $data['merchant_phone'])->first();
        }

        $user = auth()->user();
        if (!$user) {
            throw new \Exception('Utilisateur non trouvé');
        }

        if (!$wallet) {
            throw new \Exception('Wallet non fourni - requis dans le système multi-wallet');
        }

        return Transaction::create([
            'user_id' => $user->id,
            'wallet_id' => $wallet->id,
            'amount' => $data['amount'],
            'type' => 'payment',
            'status' => 'success',
            'marchant_id' => $merchant ? $merchant->id : null,
            'orange_tx_id' => $this->generateReference(),
            'description' => $data['description'] ?? null,
        ]);
    }

    /**
     * Créer une transaction de transfert
     *
     * @param array $data
     * @param \App\Models\Wallet|null $wallet
     * @return Transaction
     */
    public function createTransfer(array $data, ?\App\Models\Wallet $wallet = null): Transaction
    {
        $user = auth()->user();
        if (!$user) {
            throw new \Exception('Utilisateur non trouvé');
        }

        // Le wallet doit être passé en paramètre dans le système multi-wallet

        if (!$wallet) {
            throw new \Exception('Wallet non fourni - requis dans le système multi-wallet');
        }

        return Transaction::create([
            'user_id' => $user->id,
            'wallet_id' => $wallet->id,
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