<?php

namespace RomegaSoftware\WorkOSTeams;

use Illuminate\Support\ServiceProvider;
use RomegaSoftware\WorkOSTeams\Console\Commands\SyncWorkOSOrganizations;
use RomegaSoftware\WorkOSTeams\Contracts\TeamContract;
use RomegaSoftware\WorkOSTeams\Services\WorkOSCacheService;
use RomegaSoftware\WorkOSTeams\Services\WorkOSLogService;
use RomegaSoftware\WorkOSTeams\Services\WorkOSSessionService;

class WorkOSTeamsServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register repositories
        $this->app->bind(
            \RomegaSoftware\WorkOSTeams\Contracts\OrganizationRepository::class,
            \RomegaSoftware\WorkOSTeams\Repositories\WorkOSOrganizationRepository::class
        );

        $this->app->bind(
            \RomegaSoftware\WorkOSTeams\Contracts\UserRepository::class,
            \RomegaSoftware\WorkOSTeams\Repositories\WorkOSUserRepository::class
        );

        // Register WorkOS services
        $this->app->singleton(WorkOSCacheService::class);
        $this->app->singleton(WorkOSLogService::class);
        $this->app->singleton(WorkOSSessionService::class);

        // Register configurations
        $this->mergeConfigFrom(
            __DIR__.'/../config/workos-teams.php',
            'workos-teams'
        );

        // Bind the team model to the TeamContract interface
        $this->app->bind(TeamContract::class, function ($app) {
            $teamModel = config('workos-teams.models.team');

            return new $teamModel;
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                SyncWorkOSOrganizations::class,
            ]);
        }

        // Publish configuration
        $this->publishes([
            __DIR__.'/../config/workos-teams.php' => config_path('workos-teams.php'),
        ], 'workos-teams-config');

        // Publish migrations
        $this->publishes([
            __DIR__.'/../database/migrations' => database_path('migrations'),
        ], 'workos-teams-migrations');

        // Load routes
        $this->loadRoutesFrom(__DIR__.'/../routes/webhooks.php');
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');

        // Load views
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'workos-teams');

        // Publish views
        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/workos-teams'),
        ], 'workos-teams-views');
    }
}
