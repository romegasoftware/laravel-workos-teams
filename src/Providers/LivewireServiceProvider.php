<?php

namespace RomegaSoftware\WorkOSTeams\Providers;

use Override;
use Livewire\Livewire;
use Illuminate\Support\Str;
use Livewire\LivewireManager;
use Symfony\Component\Finder\Finder;
use Illuminate\Support\Facades\Facade;
use Illuminate\Support\ServiceProvider;

class LivewireServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    #[Override]
    public function register(): void
    {
        if (class_exists(Livewire::class)) {
            $this->app->booted(function () {
                $this->bindLivewireManager();
                $this->registerLivewireComponents();
            });
        }
    }

    /**
     * Register Livewire components.
     */
    protected function registerLivewireComponents(): void
    {
        $finder = new Finder;
        $finder->files()->in(__DIR__ . '/../Livewire')->name('*.php');

        foreach ($finder as $file) {
            $componentName = str_replace('.php', '', $file->getRelativePathname());

            $componentClass = 'RomegaSoftware\\WorkOSTeams\\Livewire\\' . str_replace('/', '\\', $componentName);

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

    /**
     * Bind the custom Livewire manager in the container.
     */
    protected function bindLivewireManager(): void
    {
        $this->app->singleton(LivewireManager::class);
        $this->app->alias(LivewireManager::class, 'livewire');

        Facade::clearResolvedInstance('livewire');
    }
}
