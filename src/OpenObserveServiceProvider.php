<?php

namespace Weijukeji\LaravelOpenObserve;

use Illuminate\Support\ServiceProvider;
use Weijukeji\LaravelOpenObserve\Console\TestConnectionCommand;

class OpenObserveServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Publish configuration file
        $this->publishes([
            __DIR__.'/../config/openobserve.php' => config_path('openobserve.php'),
        ], 'openobserve-config');
    }

    /**
     * Register services.
     */
    public function register(): void
    {
        // Merge configuration
        $this->mergeConfigFrom(
            __DIR__.'/../config/openobserve.php',
            'openobserve'
        );

        // Register OpenObserve client as singleton
        $this->app->singleton(OpenObserveClient::class, function ($app) {
            return new OpenObserveClient($app['config']['openobserve']);
        });

        // Register alias
        $this->app->alias(OpenObserveClient::class, 'openobserve');

        // Register console command
        if ($this->app->runningInConsole()) {
            $this->commands([
                TestConnectionCommand::class,
            ]);
        }
    }
}
