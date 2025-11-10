<?php

namespace App\Listeners;

use App\Events\TransfertEffectue;
use App\Services\OrangeSmsService;
use Illuminate\Support\Facades\Log;

/**
 * Listener pour envoyer des notifications après un transfert
 */
class EnvoyerNotificationTransfert
{
    protected $smsService;

    /**
     * Créer le listener d'événement.
     */
    public function __construct(OrangeSmsService $smsService)
    {
        $this->smsService = $smsService;
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

        // Envoyer SMS à l'expéditeur
        $expediteur = $event->transaction->user;
        // Trouver le destinataire par numéro de téléphone
        $destinataire = \App\Models\User::where('phone', $event->numeroDestinataire)->first();

        if ($destinataire) {
            $messageExpediteur = "OM Pay: Transfert de {$event->transaction->amount} FCFA envoyé à {$destinataire->name}. Nouveau solde: {$event->nouveauSoldeExpediteur} FCFA. Ref: {$event->transaction->orange_tx_id}";

            // Envoi réel du SMS via Orange API
            $this->smsService->sendSms($expediteur->phone, $messageExpediteur);

            // Envoyer SMS au destinataire
            $messageDestinataire = "OM Pay: Vous avez reçu {$event->transaction->amount} FCFA de {$expediteur->name}. Ref: {$event->transaction->orange_tx_id}";

            // Envoi réel du SMS via Orange API
            $this->smsService->sendSms($destinataire->phone, $messageDestinataire);
        }
    }
}