<?php

namespace App\Services;

use App\Contracts\NotificationServiceInterface;
use App\Events\PaiementEffectue;
use App\Events\TransfertEffectue;
use App\Models\Transaction;

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