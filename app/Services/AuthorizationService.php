<?php

namespace App\Services;

use App\Contracts\AuthorizationServiceInterface;
use Illuminate\Support\Facades\Gate;
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
        if (!Gate::allows('effectuer-paiement', $data['amount'])) {
            throw new Exception('Paiement non autorisé');
        }

        if (!Gate::allows('limite-transaction-journaliere', $data['amount'])) {
            throw new Exception('Limite de transaction journalière dépassée');
        }
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
        if (!Gate::allows('effectuer-transfert', [$data['amount'], $data['recipient_phone']])) {
            throw new Exception('Transfert non autorisé');
        }

        if (!Gate::allows('limite-transaction-journaliere', $data['amount'])) {
            throw new Exception('Limite de transaction journalière dépassée');
        }
    }
}