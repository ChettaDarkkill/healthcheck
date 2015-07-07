<?php

namespace Egg\Healthcheck;

use Illuminate\Support\ServiceProvider;

class HealthcheckServiceProvider extends ServiceProvider {

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot() {
        $this->loadViewsFrom(__DIR__ . '/views', 'healthcheck');
        $this->publishes([__DIR__ . '/views' => base_path('resources/views/egg/healthcheck')]);
        include __DIR__.'/routes.php';
        $this->publishes([__DIR__.'/config/healthcheck.php' => config_path('healthcheck.php'),
    ]);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register() {
        $this->app->make('Egg\Healthcheck\HealthcheckController');
    }

}
