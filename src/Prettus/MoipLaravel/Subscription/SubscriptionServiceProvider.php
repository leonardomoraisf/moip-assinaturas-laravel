<?php

namespace Prettus\MoipLaravel\Subscription;

use Illuminate\Support\ServiceProvider;
use Prettus\Moip\Subscription\Api;
use Prettus\Moip\Subscription\MoipClient;

class SubscriptionServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Merge config
        $this->mergeConfigFrom(
            __DIR__ . '/../config/moip-assinaturas.php',
            'moip-assinaturas'
        );

        // Client
        $this->app->singleton('moip-client', function () {
            return new MoipClient(
                config('moip-assinaturas.api_token'),
                config('moip-assinaturas.api_key'),
                config('moip-assinaturas.environment', 'api')
            );
        });

        // API
        $this->app->singleton('moip-api', function ($app) {
            return new Api($app->make('moip-client'));
        });

        // Facade bindings
        $this->app->bind('moip-plans', fn ($app) => $app->make('moip-api')->plans());
        $this->app->bind('moip-subscriptions', fn ($app) => $app->make('moip-api')->subscriptions());
        $this->app->bind('moip-customers', fn ($app) => $app->make('moip-api')->customers());
        $this->app->bind('moip-invoices', fn ($app) => $app->make('moip-api')->invoices());
        $this->app->bind('moip-preferences', fn ($app) => $app->make('moip-api')->preferences());
        $this->app->bind('moip-payments', fn ($app) => $app->make('moip-api')->payments());
    }

    public function boot(): void
    {
        // Publish config
        $this->publishes([
            __DIR__ . '/../config/moip-assinaturas.php' => config_path('moip-assinaturas.php'),
        ], 'moip-config');
    }
}
