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
