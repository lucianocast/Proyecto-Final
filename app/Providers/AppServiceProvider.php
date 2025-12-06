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
        // Binding de PaymentGateway
        // Por defecto, usar Google Pay como pasarela de pago
        $this->app->bind(
            \App\Contracts\PaymentGateway::class,
            \App\Services\Gateways\GooglePayGateway::class
        );

        // Registrar PaymentService como singleton para reutilización
        $this->app->singleton(\App\Services\PaymentService::class, function ($app) {
            return new \App\Services\PaymentService(
                $app->make(\App\Contracts\PaymentGateway::class)
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
