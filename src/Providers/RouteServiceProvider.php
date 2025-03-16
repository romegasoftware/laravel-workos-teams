<?php

namespace RomegaSoftware\WorkOSTeams\Providers;

use Illuminate\Support\ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Load routes
        $this->loadRoutesFrom(__DIR__ . '/../../routes/webhooks.php');
        $this->loadRoutesFrom(__DIR__ . '/../../routes/web.php');
    }
}
