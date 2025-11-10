<?php

namespace App\Services;

use App\Contracts\NotificationServiceInterface;
use App\Events\PaiementEffectue;
use App\Events\TransfertEffectue;
use App\Models\Transaction;
use Illuminate\Support\Facades\Log;

class NotificationService implements NotificationServiceInterface
{
    /**
     * Notifier après un paiement réussi
     *
     * @param Transaction $transaction
     * @return void
     */
    public function notifyPayment(Transaction $transaction): void
    {
        // Charger la relation wallet si elle n'est pas chargée
        $transaction->load('wallet');

        if (!$transaction->wallet) {
            Log::error('Transaction sans wallet pour paiement', ['transaction_id' => $transaction->id]);
            return;
        }

        // Calculer les soldes pour l'événement
        $ancienSolde = $transaction->wallet->balance + $transaction->amount;
        $nouveauSolde = $transaction->wallet->fresh()->balance;

        // Déclencher l'événement
        event(new PaiementEffectue($transaction, $ancienSolde, $nouveauSolde));
    }

    /**
     * Notifier après un transfert réussi
     *
     * @param Transaction $transaction
     * @return void
     */
    public function notifyTransfer(Transaction $transaction): void
    {
       
        $transaction->load('wallet');
       

        if (!$transaction->wallet) {
            Log::error('Transaction sans wallet pour transfert', ['transaction_id' => $transaction->id]);
            return;
        }

        // Calculer les soldes pour l'événement
        $ancienSoldeExpediteur = $transaction->wallet->balance + $transaction->amount;
        
        $nouveauSoldeExpediteur = $transaction->wallet->fresh()->balance;
         
        // Déclencher l'événement
        event(new TransfertEffectue(
            $transaction,
            $ancienSoldeExpediteur,
            $nouveauSoldeExpediteur,
            $transaction->recipient_phone
        ));
    }
}