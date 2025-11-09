<?php

namespace App\Contracts;

interface AuthorizationServiceInterface
{
    /**
     * Vérifier les autorisations pour un paiement
     *
     * @param array $data
     * @return void
     * @throws \Exception
     */
    public function checkPaymentAuthorization(array $data): void;

    /**
     * Vérifier les autorisations pour un transfert
     *
     * @param array $data
     * @return void
     * @throws \Exception
     */
    public function checkTransferAuthorization(array $data): void;
}