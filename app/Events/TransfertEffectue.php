<?php

namespace App\Events;

use App\Models\Transaction;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Événement déclenché lorsqu'un transfert est effectué avec succès
 */
class TransfertEffectue
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public Transaction $transaction,
        public float $ancienSoldeExpediteur,
        public float $nouveauSoldeExpediteur,
        public string $numeroDestinataire
    ) {}
}