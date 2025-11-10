<?php

namespace App\Providers;

use App\Models\Transaction;
use App\Models\Wallet;
use App\Observers\TransactionObserver;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{

    /**
     * Enregistrer tous les services d'authentification/autorisation.
     */
    public function boot(): void
    {
        // Enregistrer les observers
        Transaction::observe(TransactionObserver::class);
    }

}
