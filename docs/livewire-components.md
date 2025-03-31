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
