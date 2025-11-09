<?php

namespace App\Events;

use App\Models\Transaction;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Événement déclenché lorsqu'un paiement est effectué avec succès
 */
class PaiementEffectue
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public Transaction $transaction,
        public float $ancienSolde,
        public float $nouveauSolde
    ) {}
}