<?php

namespace RomegaSoftware\WorkOSTeams\Providers;

use Override;
use Illuminate\Support\ServiceProvider;
use RomegaSoftware\WorkOSTeams\Services\WorkOSLogService;
use RomegaSoftware\WorkOSTeams\Services\WorkOSCacheService;
use RomegaSoftware\WorkOSTeams\Services\WorkOSSessionService;

class WorkOSServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    #[Override]
    public function register(): void
    {
        // Register WorkOS services
        $this->app->singleton(WorkOSCacheService::class);
        $this->app->singleton(WorkOSLogService::class);
        $this->app->singleton(WorkOSSessionService::class);

        // Register configurations
        $this->mergeConfigFrom(
            __DIR__ . '/../../config/workos-teams.php',
            'workos-teams'
        );
    }
}
