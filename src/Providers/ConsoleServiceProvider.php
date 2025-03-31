<?php

namespace RomegaSoftware\WorkOSTeams\Providers;

use Illuminate\Support\ServiceProvider;
use RomegaSoftware\WorkOSTeams\Console\Commands\SyncWorkOSOrganizations;

class ConsoleServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            // Register commands
            $this->commands([
                SyncWorkOSOrganizations::class,
            ]);

            // Publish configuration
            $this->publishes([
                __DIR__.'/../../config/workos-teams.php' => config_path('workos-teams.php'),
            ], 'workos-teams-config');

            // Publish migrations
            $this->publishes([
                __DIR__.'/../../database/migrations' => database_path('migrations'),
            ], 'workos-teams-migrations');
        }
    }
}
