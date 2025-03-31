<?php

namespace RomegaSoftware\WorkOSTeams\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register views
        $this->loadViewsFrom(__DIR__.'/../../resources/views', 'workos-teams');

        // Register Blade components
        $this->bootComponentPath();

        // Optionally publish views
        $this->publishes([
            __DIR__.'/../../resources/views' => resource_path('views/vendor/workos-teams'),
        ], 'workos-teams-views');
    }

    protected function bootComponentPath(): void
    {
        if (file_exists(resource_path('views'))) {
            Blade::anonymousComponentPath(resource_path('views'), 'workos-teams');
        }
    }
}
