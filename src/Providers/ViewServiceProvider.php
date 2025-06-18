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
        // Check if Livewire is enabled
        if (config('workos-teams.features.livewire', false)) {
            // Register Livewire views
            $this->loadViewsFrom(__DIR__.'/../../resources/views', 'workos-teams');

            // Register Blade components
            $this->bootComponentPath();

            // Optionally publish views
            $this->publishes([
                __DIR__.'/../../resources/views' => resource_path('views/vendor/workos-teams'),
            ], 'workos-teams-views');
        } else {
            // Only register non-Livewire views if any exist
            $nonLivewireViewsPath = __DIR__.'/../../resources/views/non-livewire';
            if (is_dir($nonLivewireViewsPath)) {
                $this->loadViewsFrom($nonLivewireViewsPath, 'workos-teams');
            }
        }
    }

    protected function bootComponentPath(): void
    {
        if (file_exists(resource_path('views'))) {
            Blade::anonymousComponentPath(resource_path('views'), 'workos-teams');
        }
    }
}
