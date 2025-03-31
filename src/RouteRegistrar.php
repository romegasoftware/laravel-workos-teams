<?php

namespace RomegaSoftware\WorkOSTeams;

use Illuminate\Support\Facades\Route;
use RomegaSoftware\WorkOSTeams\Http\Controllers\WebhookController;
use RomegaSoftware\WorkOSTeams\Http\Middleware\EnsureHasTeam;
use RomegaSoftware\WorkOSTeams\Livewire\Teams\TeamCreate;
use RomegaSoftware\WorkOSTeams\Livewire\Teams\TeamEdit;
use RomegaSoftware\WorkOSTeams\Livewire\Teams\TeamShow;
use RomegaSoftware\WorkOSTeams\Livewire\Teams\TeamsIndex;

class RouteRegistrar
{
    protected string $prefix = '';

    protected array $middleware = [];

    protected array $withoutMiddleware = [];

    protected array $defaultWithoutMiddleware = [
        'teams.create' => [EnsureHasTeam::class],
    ];

    protected bool $withoutDefaultMiddleware = false;

    public function __construct(protected string $group) {}

    public function withoutMiddlewareFor(string $routeName, array $middleware): self
    {
        $this->withoutMiddleware[$routeName] = $middleware;

        return $this;
    }

    public function withoutDefaultMiddleware(): self
    {
        $this->withoutDefaultMiddleware = true;

        return $this;
    }

    public function register(): void
    {
        match ($this->group) {
            'web' => $this->registerWebRoutes(),
            'webhooks' => $this->registerWebhookRoutes(),
        };
    }

    protected function registerWebRoutes(): void
    {
        $this->addRoute(
            method: 'get',
            name: 'teams.index',
            uri: '/teams',
            componentOrController: TeamsIndex::class
        );
        $this->addRoute(
            method: 'get',
            name: 'teams.create',
            uri: '/teams/create',
            componentOrController: TeamCreate::class
        );
        $this->addRoute(
            method: 'get',
            name: 'teams.show',
            uri: '/teams/{team}',
            componentOrController: TeamShow::class
        );
        $this->addRoute(
            method: 'get',
            name: 'teams.edit',
            uri: '/teams/{team}/edit',
            componentOrController: TeamEdit::class
        );
    }

    protected function registerWebhookRoutes(): void
    {
        $this->addRoute(
            method: 'post',
            name: 'webhooks.user-registration-action',
            uri: '/user-registration-action',
            componentOrController: [WebhookController::class, 'handle']
        );
    }

    protected function addRoute(string $method, \BackedEnum|string $name, string $uri, array|string|callable|null $componentOrController): void
    {
        $route = Route::$method($uri, $componentOrController)->name($name);

        if (! $this->withoutDefaultMiddleware) {
            $excludedMiddleware = array_merge(
                $this->defaultWithoutMiddleware[$name] ?? [],
                $this->withoutMiddleware[$name] ?? []
            );
        }

        if (! empty($excludedMiddleware)) {
            $route->withoutMiddleware($excludedMiddleware);
        }
    }
}
