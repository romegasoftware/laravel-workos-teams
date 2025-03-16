<?php

namespace RomegaSoftware\WorkOSTeams\Providers;

use Illuminate\Support\ServiceProvider;
use RomegaSoftware\WorkOSTeams\Contracts\TeamContract;

class RepositoryServiceProvider extends ServiceProvider
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

        // Bind the team model to the TeamContract interface
        $this->app->bind(TeamContract::class, function () {
            $teamModel = config('workos-teams.models.team');

            return new $teamModel;
        });
    }
}
