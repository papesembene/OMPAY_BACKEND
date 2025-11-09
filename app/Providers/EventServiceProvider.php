<?php

namespace App\Providers;

use App\Events\PaiementEffectue;
use App\Events\TransfertEffectue;
use App\Listeners\EnvoyerNotificationPaiement;
use App\Listeners\EnvoyerNotificationTransfert;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * Les écouteurs d'événements pour l'application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        PaiementEffectue::class => [
            EnvoyerNotificationPaiement::class,
        ],

        TransfertEffectue::class => [
            EnvoyerNotificationTransfert::class,
        ],
    ];

    /**
     * Déterminer si les événements et les écouteurs doivent être automatiquement découverts.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
