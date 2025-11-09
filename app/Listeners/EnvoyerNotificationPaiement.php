<?php

namespace App\Listeners;

use App\Events\PaiementEffectue;
use Illuminate\Support\Facades\Log;

/**
 * Listener pour envoyer des notifications après un paiement
 */
class EnvoyerNotificationPaiement
{
    /**
     * Créer le listener d'événement.
     */
    public function __construct()
    {
        //
    }

    /**
     * Gérer l'événement.
     */
    public function handle(PaiementEffectue $event): void
    {
        // Log de l'événement
        Log::info('Paiement effectué', [
            'transaction_id' => $event->transaction->id,
            'utilisateur_id' => $event->transaction->user_id,
            'montant' => $event->transaction->amount,
            'marchand_id' => $event->transaction->marchant_id,
            'ancien_solde' => $event->ancienSolde,
            'nouveau_solde' => $event->nouveauSolde,
            'reference' => $event->transaction->orange_tx_id,
        ]);

        // Ici vous pourriez envoyer :
        // - Un SMS à l'utilisateur
        // - Un email de confirmation
        // - Une notification push
        // - Une notification au marchand
    }
}