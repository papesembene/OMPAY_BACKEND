<?php

namespace App\Contracts;

use App\Models\Transaction;

interface NotificationServiceInterface
{
    /**
     * Notifier après un paiement réussi
     *
     * @param Transaction $transaction
     * @return void
     */
    public function notifyPayment(Transaction $transaction): void;

    /**
     * Notifier après un transfert réussi
     *
     * @param Transaction $transaction
     * @return void
     */
    public function notifyTransfer(Transaction $transaction): void;
}