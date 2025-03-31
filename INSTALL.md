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

Create a Team model that implements the ExternalId interface:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use RomegaSoftware\WorkOSTeams\Contracts\ExternalId;
use RomegaSoftware\WorkOSTeams\Traits\HasExternalId;

class Team extends Model implements ExternalId
{
    use HasExternalId;

    public const EXTERNAL_ID_COLUMN = 'workos_organization_id';

    protected $fillable = [
        'name',
        'workos_organization_id',
        'description',
    ];

    // ...
}
```

Create a TeamInvitation model:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use RomegaSoftware\WorkOSTeams\Contracts\ExternalId;
use RomegaSoftware\WorkOSTeams\Traits\HasExternalId;

class TeamInvitation extends Model implements ExternalId
{
    use HasExternalId;

    public const EXTERNAL_ID_COLUMN = 'workos_invitation_id';

    protected $fillable = [
        'team_id',
        'email',
        'role',
        'workos_invitation_id',
    ];

    // ...
}
```

## Step 5: Update Your Authentication Flow

Replace the standard WorkOS authentication with team support:

```php
<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RomegaSoftware\WorkOSTeams\Http\Requests\AuthKitTeamAuthenticationRequest;

class WorkOSController extends Controller
{
    public function callback(AuthKitTeamAuthenticationRequest $request)
    {
        $user = $request->authenticate();

        Auth::login($user);

        return redirect('/dashboard');
    }
}
```

## Step 6: Configure WorkOS Webhooks

In your WorkOS dashboard, set up a webhook endpoint for user registration actions:

```
https://your-domain.com/webhooks/work-os/user-registration-action
```

Make sure to set the webhook secret in your `.env` file:

```
WORKOS_WEBHOOK_SECRET=your_webhook_secret
```

## Step 7: Test the Integration

You can test the synchronization between your teams and WorkOS organizations using the provided command:

```bash
php artisan workos:sync-organizations
```

To sync a specific team:

```bash
php artisan workos:sync-organizations --team-id=1
```

## Optional: Livewire Components

If you're using Livewire, you can publish the views:

```bash
php artisan vendor:publish --tag="workos-teams-views"
```

And set up routes for the team management components:

```php
// routes/web.php
use \RomegaSoftware\WorkOSTeams\WorkOSTeams;
use RomegaSoftware\WorkOSTeams\Http\Middleware\EnsureHasTeam;

Route::middleware(['web', 'auth', EnsureHasTeam::class])->group(function () {
    WorkOSTeams::web()
        ->register();
});
```

By default, the `teams.created` route does not require a User have a team to access by removing the middleware `EnsureHasTeam`. This is a sensible default for the instance where a new user is registering with your app and needs to create their first team. You may override this default if you need.

```php
use \RomegaSoftware\WorkOSTeams\WorkOSTeams;
use RomegaSoftware\WorkOSTeams\Http\Middleware\EnsureHasTeam;

Route::middleware(['web', 'auth', EnsureHasTeam::class])->group(function () {
    WorkOSTeams::web()
        ->withoutDefaultMiddleware()
        ->register();
});
```