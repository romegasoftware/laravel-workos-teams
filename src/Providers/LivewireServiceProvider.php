<?php

namespace RomegaSoftware\WorkOSTeams\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Symfony\Component\Finder\Finder;

class LivewireServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (class_exists(Livewire::class)) {
            $this->registerLivewireComponents();
        }
    }

    /**
     * Register Livewire components.
     */
    protected function registerLivewireComponents(): void
    {
        $finder = new Finder;
        $finder->files()->in(__DIR__.'/../Livewire')->name('*.php');

        foreach ($finder as $file) {
            $componentName = str_replace('.php', '', $file->getRelativePathname());

            $componentClass = 'RomegaSoftware\\WorkOSTeams\\Livewire\\'.str_replace('/', '\\', $componentName);

            // Convert component name to kebab case for Livewire registration
            $componentName = Str::of($componentName)
                ->replace('/', '.')
                ->replaceMatches('/(?<!^|[\.])[A-Z]/', '-$0')
                ->lower()
                ->value();

            if (class_exists($componentClass)) {
                Livewire::component($componentName, $componentClass);
            }
        }
    }
}
