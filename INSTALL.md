# Installation Guide

This guide will help you integrate the WorkOS Teams package into your Laravel application.

## Prerequisites

- Laravel 11.0+ application
- PHP 8.2+
- WorkOS account and API keys
- Laravel WorkOS package installed

## Step 1: Install the Package

```bash
composer require romegasoftware/workos-teams
```

## Step 2: Publish and Run Migrations

```bash
php artisan vendor:publish --tag="workos-teams-migrations"
php artisan migrate
```

## Step 3: Publish Configuration

```bash
php artisan vendor:publish --tag="workos-teams-config"
```

## Step 4: Configure Your Models

### User Model
Update your User model to use the HasTeams and HasWorkOSExternalId traits:

```php
<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use RomegaSoftware\WorkOSTeams\Traits\HasTeams;
use RomegaSoftware\WorkOSTeams\Traits\HasWorkOSExternalId;

class User extends Authenticatable
{
    use HasTeams, HasWorkOSExternalId;

    // ...
}
```

### Team Model

You may utilize the Team model already part of this package. Alternatively, you can extend the `AbstractTeam` class which already implements the necessary interfaces and traits:

```php
<?php

namespace App\Models;

use RomegaSoftware\WorkOSTeams\Models\AbstractTeam;

class Team extends AbstractTeam
{
    // Add any additional functionality here
}
```

For full functionality, we recommend either:
1. Extending `AbstractTeam` (easiest)
2. Implementing the `WorkOSTeams` interface and using the `ImplementsWorkOSTeams` trait
3. Implementing the `TeamContract` interface and using the `ImplementsTeamContract` trait

The `AbstractTeam` class provides all necessary functionality including:
- Team contract methods (name, description, etc.)
- External ID management for WorkOS integration
- Team relationship methods
- Event handling for team operations

### Team Invitation Model

You may utilize the TeamInvitation model already part of this package. Alternatively, you can extend the `AbstractTeamInvitation` class which already implements the necessary interfaces and traits:

```php
<?php

namespace App\Models;

use RomegaSoftware\WorkOSTeams\Models\AbstractTeamInvitation;

class TeamInvitation extends AbstractTeamInvitation
{
    // Add any additional functionality here
}
```

The `AbstractTeamInvitation` class provides all necessary functionality including:
- Team invitation contract methods
- External ID management for WorkOS integration
- Team relationship methods
- Event handling for invitation operations

## Step 5: Update Your Authentication Flow

Replace the standard Laravel WorkOS authenticate route Request typehint with this package's Request type hint containing team support:

```php
// routes/auth.php
use RomegaSoftware\WorkOSTeams\Http\Requests\AuthKitTeamAuthenticationRequest;
// ...

Route::get('authenticate', function (AuthKitTeamAuthenticationRequest $request) {
    return tap(to_route('dashboard'), fn() => $request->authenticate());
})->middleware(['guest']);

// ...
```

## Step 6: Configure WorkOS Webhooks

Add the webhoook route group.

```php
// bootstrap/app.php
// ...
    ->withRouting(
        // ...
        then: function () {
            Route::middleware('api')
                ->prefix('webhooks')
                ->group(__DIR__ . '/../routes/webhooks.php');
        },
    )
```

```php
// routes/webhooks.php
use RomegaSoftware\WorkOSTeams\WorkOSTeams;

WorkOSTeams::webhooks()->register();
```

In your WorkOS dashboard, set up a webhook endpoint for user registration actions:

```
https://your-domain.com/webhooks/work-os/user-registration-action
```

Make sure to set the webhook secret in your `.env` file:

```
WORKOS_WEBHOOK_SECRET=your_webhook_secret
```

## Step 7: Protect Your Routes

You'll want to protect any routes that must have team relationships using the `EnsureHasTeam` middleware.

```php
// routes/web.php
use RomegaSoftware\WorkOSTeams\Http\Middleware\EnsureHasTeam;

Route::middleware(['auth', 'workos.session', EnsureHasTeam::class])->group(function () {
    Route::get('/protected-route', function () {
        $currentTeam = auth()->user()->currentTeam;
        // ..
    });
});
```

## Optional: Livewire Components

If you're using Livewire, you can publish the views and register the routes:

### Publish Views
```bash
php artisan vendor:publish --tag="workos-teams-views"
```

### Register Routes

```php
// routes/web.php
use RomegaSoftware\WorkOSTeams\Http\Middleware\EnsureHasTeam;
use RomegaSoftware\WorkOSTeams\WorkOSTeams;

Route::middleware(['auth', 'workos.session', EnsureHasTeam::class])->group(function () {
    WorkOSTeams::web()->register();
});
```

By default, the `teams.create` route does not require a User have a team to access by removing the middleware `EnsureHasTeam`. This is a sensible default for the instance where a new user is registering with your app and needs to create their first team. You may override this default if you need.

```php
use RomegaSoftware\WorkOSTeams\WorkOSTeams;
use RomegaSoftware\WorkOSTeams\Http\Middleware\EnsureHasTeam;

Route::middleware(['web', 'auth', EnsureHasTeam::class, SomeOtherMiddleware::class])->group(function () {
    WorkOSTeams::web()
        ->withoutDefaultMiddleware()
        // Optionally define another middleware to remove
        ->withoutMiddlewareFor('teams.create', [SomeOtherMiddleware::class])
        ->register();
});
```

## \[Work in Progress\] Console Commands

You can test the synchronization between your teams and WorkOS organizations using the provided command:

```bash
php artisan workos:sync-organizations
```

To sync a specific team:

```bash
php artisan workos:sync-organizations --team-id=1
```