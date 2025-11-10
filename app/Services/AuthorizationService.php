<?php

namespace App\Services;

use App\Contracts\AuthorizationServiceInterface;
use Exception;

class AuthorizationService implements AuthorizationServiceInterface
{
    /**
     * Vérifier les autorisations pour un paiement
     *
     * @param array $data
     * @return void
     * @throws Exception
     */
    public function checkPaymentAuthorization(array $data): void
    {
        // Vérifier que le marchand existe
        $merchant = null;
        if (isset($data['merchant_code'])) {
            $merchant = \App\Models\Marchant::where('code', $data['merchant_code'])->first();
        } elseif (isset($data['merchant_phone'])) {
            $merchant = \App\Models\Marchant::where('phone', $data['merchant_phone'])->first();
        }

        if (!$merchant) {
            throw new Exception('Marchand introuvable');
        }

        // Vérifications de base pour le paiement
        $this->validatePayment($data);
    }

    /**
     * Vérifier les autorisations pour un transfert
     *
     * @param array $data
     * @return void
     * @throws Exception
     */
    public function checkTransferAuthorization(array $data): void
    {
        // Vérifications de base pour le transfert
        $this->validateTransfer($data);
    }

    /**
     * Valider les conditions de base pour un paiement
     */
    private function validatePayment(array $data): void
    {
        $user = auth()->user();
        $amount = $data['amount'];

        // Vérifier les limites de montant
        if ($amount < 100 || $amount > 1000000) {
            throw new Exception('Montant invalide');
        }

        // Vérifier le solde
        if (!$user->wallet || $user->wallet->balance < $amount) {
            throw new Exception('Solde insuffisant');
        }

        // Vérifier la limite journalière
        $this->checkDailyLimit($user, $amount);
    }

    /**
     * Valider les conditions de base pour un transfert
     */
    private function validateTransfer(array $data): void
    {
        $user = auth()->user();
         
        $amount = $data['amount'];
        $recipientPhone = $data['recipient_phone'];
        
        // Vérifier les limites de montant
        if ($amount < 100 || $amount > 1000000) {
            throw new Exception('Montant invalide');
        }

        // Vérifier le solde
        if (!$user->wallet || $user->wallet->balance < $amount) {
            throw new Exception('Solde insuffisant');
        }

        // Empêcher le transfert vers soi-même
        if ($recipientPhone === $user->phone) {
            throw new Exception('Transfert vers soi-même non autorisé');
        }

        // Vérifier que le destinataire existe
        if (!\App\Models\User::where('phone', $recipientPhone)->exists()) {
            throw new Exception('Destinataire introuvable');
        }

        // Vérifier la limite journalière
        $this->checkDailyLimit($user, $amount);
    }

    /**
     * Vérifier la limite de transaction journalière
     */
    private function checkDailyLimit($user, $amount): void
    {
        $totalToday = \App\Models\Transaction::where('user_id', $user->id)
            ->whereDate('created_at', today())
            ->where('status', 'success')
            ->sum('amount');

        $dailyLimit = 5000000; // 5 000 000 FCFA

        if (($totalToday + $amount) > $dailyLimit) {
            throw new Exception('Limite de transaction journalière dépassée');
        }
    }
}