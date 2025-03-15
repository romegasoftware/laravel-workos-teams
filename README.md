# WorkOS Teams for Laravel

This package extends the Laravel WorkOS integration with team/organization functionality. It provides a migration path for users transitioning from Jetstream Teams to WorkOS Organizations.

## Features

- WorkOS Organization integration with Laravel teams
- Team invitation and member management
- Organization and user synchronization with WorkOS
- Livewire components for team management (optional)
- Webhooks for WorkOS events
- Repository pattern for modular implementation

## Requirements

- PHP 8.2 or higher
- Laravel 9.0 or higher
- Laravel WorkOS package
- WorkOS PHP SDK

## Installation

You can install the package via composer:

```bash
composer require romegasoftware/workos-teams
```

## Configuration

Publish the configuration file:

```bash
php artisan vendor:publish --tag="workos-teams-config"
```

Publish the migrations:

```bash
php artisan vendor:publish --tag="workos-teams-migrations"
```

Run the migrations:

```bash
php artisan migrate
```

Optionally, you can publish the views:

```bash
php artisan vendor:publish --tag="workos-teams-views"
```

## Usage

### Authentication with Teams

Replace the standard WorkOS authentication with team support:

```php
use RomegaSoftware\WorkOSTeams\Http\Requests\AuthKitTeamAuthenticationRequest;

// In your controller method:
public function callback(AuthKitTeamAuthenticationRequest $request)
{
    $user = $request->authenticate();

    Auth::login($user);

    return redirect('/dashboard');
}
```

### Team Management

The package provides models and traits for team management:

```php
// Add a user to a team
$team->addMember($user, 'admin');

// Update a member's role
$team->updateMember($user, 'member');

// Remove a member
$team->removeMember($user);

// Switch to a team
$user->switchTeam($team);
```

### WorkOS Webhooks

The package sets up webhooks for WorkOS events. Make sure to configure your WorkOS webhook endpoint to point to:

```
https://your-domain.com/webhooks/work-os/user-registration-action
```

### Using the Traits

To use the package traits in your models:

```php
// User model
use RomegaSoftware\WorkOSTeams\Traits\HasTeams;
use RomegaSoftware\WorkOSTeams\Traits\HasWorkOSExternalId;

class User extends Authenticatable
{
    use HasTeams, HasWorkOSExternalId;
    // ...
}

// Team model
use RomegaSoftware\WorkOSTeams\Traits\HasExternalId;

class Team extends Model
{
    use HasExternalId;

    public const EXTERNAL_ID_COLUMN = 'workos_organization_id';
    // ...
}
```

## Livewire Components

If you're using Livewire, the package includes components for team management:

```php
// Route definition
Route::get('/teams', TeamsIndex::class)->name('teams.index');
Route::get('/teams/create', TeamCreate::class)->name('teams.create');
Route::get('/teams/{team}', TeamShow::class)->name('teams.show');
Route::get('/teams/{team}/edit', TeamEdit::class)->name('teams.edit');
```

## Extending the Package

You can extend the package's functionality by:

1. Creating custom repositories that implement the package's interfaces
2. Overriding the services in your service provider
3. Customizing the models via the configuration file

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
