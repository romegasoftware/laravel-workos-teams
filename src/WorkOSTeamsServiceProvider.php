<?php

namespace RomegaSoftware\WorkOSTeams;

use Illuminate\Support\ServiceProvider;
use RomegaSoftware\WorkOSTeams\Providers\ConsoleServiceProvider;
use RomegaSoftware\WorkOSTeams\Providers\EventServiceProvider;
use RomegaSoftware\WorkOSTeams\Providers\LivewireServiceProvider;
use RomegaSoftware\WorkOSTeams\Providers\RepositoryServiceProvider;
use RomegaSoftware\WorkOSTeams\Providers\RouteServiceProvider;
use RomegaSoftware\WorkOSTeams\Providers\ViewServiceProvider;
use RomegaSoftware\WorkOSTeams\Providers\VoltServiceProvider;
use RomegaSoftware\WorkOSTeams\Providers\WorkOSServiceProvider;

/**
 * @api
 */
final class WorkOSTeamsServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    #[\Override]
    public function register(): void
    {
        // Register all service providers
        $this->app->register(WorkOSServiceProvider::class);
        $this->app->register(RepositoryServiceProvider::class);
        $this->app->register(EventServiceProvider::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register all service providers that need boot functionality
        $this->app->register(ConsoleServiceProvider::class);
        $this->app->register(RouteServiceProvider::class);
        $this->app->register(LivewireServiceProvider::class);
        $this->app->register(ViewServiceProvider::class);
        $this->app->register(VoltServiceProvider::class);
    }
}
