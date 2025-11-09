<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            \App\Contracts\AuthServiceInterface::class,
            \App\Services\AuthService::class
        );

        $this->app->bind(
            \App\Contracts\PaymentServiceInterface::class,
            \App\Services\PaymentService::class
        );

        $this->app->bind(
            \App\Contracts\AuthorizationServiceInterface::class,
            \App\Services\AuthorizationService::class
        );

        $this->app->bind(
            \App\Contracts\WalletServiceInterface::class,
            \App\Services\WalletService::class
        );

        $this->app->bind(
            \App\Contracts\TransactionServiceInterface::class,
            \App\Services\TransactionService::class
        );

        $this->app->bind(
            \App\Contracts\NotificationServiceInterface::class,
            \App\Services\NotificationService::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}

