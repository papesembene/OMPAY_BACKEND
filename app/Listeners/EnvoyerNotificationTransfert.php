<?php

namespace App\Listeners;

use App\Events\TransfertEffectue;
use Illuminate\Support\Facades\Log;

/**
 * Listener pour envoyer des notifications après un transfert
 */
class EnvoyerNotificationTransfert
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
    public function handle(TransfertEffectue $event): void
    {
        // Log de l'événement
        Log::info('Transfert effectué', [
            'transaction_id' => $event->transaction->id,
            'expediteur_id' => $event->transaction->user_id,
            'montant' => $event->transaction->amount,
            'destinataire' => $event->numeroDestinataire,
            'ancien_solde_expediteur' => $event->ancienSoldeExpediteur,
            'nouveau_solde_expediteur' => $event->nouveauSoldeExpediteur,
            'reference' => $event->transaction->orange_tx_id,
        ]);

        // Ici vous pourriez envoyer :
        // - Un SMS à l'expéditeur
        // - Un SMS au destinataire
        // - Des emails de confirmation
        // - Des notifications push
    }
}