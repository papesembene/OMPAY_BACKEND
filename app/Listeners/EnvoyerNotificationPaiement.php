<?php

namespace App\Listeners;

use App\Events\PaiementEffectue;
use App\Services\OrangeSmsService;
use Illuminate\Support\Facades\Log;

/**
 * Listener pour envoyer des notifications après un paiement
 */
class EnvoyerNotificationPaiement
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

        // Envoyer SMS à l'utilisateur
        $user = $event->transaction->user;
        $marchand = $event->transaction->marchant;

        $messageUtilisateur = "OM Pay: Paiement de {$event->transaction->amount} FCFA effectué vers {$marchand->name}. Nouveau solde: {$event->nouveauSolde} FCFA. Ref: {$event->transaction->orange_tx_id}";

        // Envoi réel du SMS via Orange API
        $this->smsService->sendSms($user->phone, $messageUtilisateur);

        // Envoyer SMS au marchand (si numéro disponible)
        if ($marchand && $marchand->phone) {
            $messageMarchand = "OM Pay: Nouveau paiement reçu de {$user->name} - {$event->transaction->amount} FCFA. Ref: {$event->transaction->orange_tx_id}";

            // Envoi réel du SMS via Orange API
            $this->smsService->sendSms($marchand->phone, $messageMarchand);
        }
    }
}