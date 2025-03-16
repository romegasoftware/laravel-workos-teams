<?php

namespace RomegaSoftware\WorkOSTeams\Providers;

use Illuminate\Support\ServiceProvider;
use Livewire\Volt\Volt;

class VoltServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (class_exists(Volt::class)) {
            Volt::mount([
                __DIR__ . '/../../resources/views/livewire',
                resource_path('views/vendor/workos-teams'),
            ]);
        }
    }
}
