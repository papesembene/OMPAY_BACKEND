<?php

namespace App\Contracts;

interface PaymentServiceInterface
{
    /**
     * Effectuer un paiement vers un marchand
     *
     * @param array $data
     * @return array
     */
    public function makePayment(array $data): array;

    /**
     * Effectuer un transfert vers un autre utilisateur
     *
     * @param array $data
     * @return array
     */
    public function makeTransfer(array $data): array;

    /**
     * Générer une référence de transaction unique
     *
     * @return string
     */
    public function generateTransactionReference(): string;
}