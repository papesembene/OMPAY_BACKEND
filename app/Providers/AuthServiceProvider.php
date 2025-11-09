<?php

namespace App\Providers;

use App\Models\Transaction;
use App\Models\Wallet;
use App\Observers\TransactionObserver;
use App\Policies\TransactionPolicy;
use App\Policies\WalletPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Les mappages de politiques pour l'application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Transaction::class => TransactionPolicy::class,
        Wallet::class => WalletPolicy::class,
    ];

    /**
     * Enregistrer tous les services d'authentification/autorisation.
     */
    public function boot(): void
    {
        // Enregistrer les politiques
        $this->registerPolicies();

        // Enregistrer les observers
        Transaction::observe(TransactionObserver::class);

        // Définir les Gates personnalisés
        $this->defineGates();
    }

    /**
     * Définir les Gates personnalisés
     */
    private function defineGates(): void
    {
        // Gate pour vérifier si l'utilisateur peut effectuer un paiement
        Gate::define('effectuer-paiement', function ($user, $montant) {
            // Vérifier le solde disponible
            $soldeDisponible = $user->wallet->balance;

            // Vérifier les limites de montant
            if ($montant < 100 || $montant > 1000000) {
                return false;
            }

            // Vérifier si l'utilisateur a assez d'argent
            return $soldeDisponible >= $montant;
        });

        // Gate pour vérifier si l'utilisateur peut effectuer un transfert
        Gate::define('effectuer-transfert', function ($user, $montant, $numeroDestinataire) {
            // Vérifier le solde disponible
            $soldeDisponible = $user->wallet->balance;

            // Vérifier les limites de montant
            if ($montant < 100 || $montant > 1000000) {
                return false;
            }

            // Vérifier si l'utilisateur a assez d'argent
            if ($soldeDisponible < $montant) {
                return false;
            }

            // Empêcher le transfert vers soi-même
            if ($numeroDestinataire === $user->phone) {
                return false;
            }

            // Vérifier que le destinataire existe
            $destinataireExiste = \App\Models\User::where('phone', $numeroDestinataire)->exists();
            return $destinataireExiste;
        });

        // Gate pour vérifier les limites de transaction journalière
        Gate::define('limite-transaction-journaliere', function ($user, $montant) {
            // Calculer le total des transactions du jour
            $totalAujourdhui = Transaction::where('user_id', $user->id)
                ->whereDate('created_at', today())
                ->where('status', 'success')
                ->sum('amount');

            // Limite journalière : 5 000 000 FCFA
            $limiteJournaliere = 5000000;

            return ($totalAujourdhui + $montant) <= $limiteJournaliere;
        });
    }
}
