<?php

namespace App\Observers;

use App\Models\Transaction;
use Illuminate\Support\Facades\Log;

/**
 * Observer pour le modèle Transaction
 * Permet de logger automatiquement les changements sur les transactions
 */
class TransactionObserver
{
    /**
     * Gérer l'événement "created".
     */
    public function created(Transaction $transaction): void
    {
        Log::info('Nouvelle transaction créée', [
            'id' => $transaction->id,
            'type' => $transaction->type,
            'montant' => $transaction->amount,
            'statut' => $transaction->status,
            'utilisateur_id' => $transaction->user_id,
            'reference' => $transaction->orange_tx_id,
        ]);
    }

    /**
     * Gérer l'événement "updated".
     */
    public function updated(Transaction $transaction): void
    {
        // Vérifier si le statut a changé
        if ($transaction->wasChanged('status')) {
            Log::info('Statut de transaction modifié', [
                'id' => $transaction->id,
                'ancien_statut' => $transaction->getOriginal('status'),
                'nouveau_statut' => $transaction->status,
                'reference' => $transaction->orange_tx_id,
            ]);
        }
    }

    /**
     * Gérer l'événement "deleted".
     */
    public function deleted(Transaction $transaction): void
    {
        Log::warning('Transaction supprimée', [
            'id' => $transaction->id,
            'type' => $transaction->type,
            'montant' => $transaction->amount,
            'reference' => $transaction->orange_tx_id,
        ]);
    }
}