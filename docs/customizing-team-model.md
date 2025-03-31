# Customizing the Team Model

This package allows you to use your own Team model by specifying it in the `workos-teams.php` config file:

```php
'models' => [
  'team' => \App\Models\Team::class,
  // other models...
],
```

## Option 1: Implementing the WorkOSTeams Interface (Recommended)

The easiest way to implement all the necessary functionality is to use the `WorkOSTeams` interface and `ImplementsWorkOSTeams` trait:

```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use RomegaSoftware\WorkOSTeams\Contracts\WorkOSTeams;
use RomegaSoftware\WorkOSTeams\Traits\ImplementsWorkOSTeams;

class Team extends Model implements WorkOSTeams
{
    use ImplementsWorkOSTeams;

    // Your model code...
}
```

This approach provides all the functionality needed for:
- Team contract methods (name, description, etc.)
- External ID management for WorkOS integration
- Team relationship methods

## Option 2: Implementing the TeamContract Interface

If you only need the basic team functionality, you can implement just the `TeamContract` interface:

```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use RomegaSoftware\WorkOSTeams\Contracts\TeamContract;
use RomegaSoftware\WorkOSTeams\Traits\ImplementsTeamContract;

class Team extends Model implements TeamContract
{
    use ImplementsTeamContract;

    // Your model code...
}
```

## Manual Implementation

If you need more control, you can implement the interfaces manually:

```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use RomegaSoftware\WorkOSTeams\Contracts\TeamContract;

class Team extends Model implements TeamContract
{
    /**
     * Get the name of the team.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Update the team with the given attributes.
     *
     * @param array<string, mixed> $attributes
     * @return bool
     */
    public function update(array $attributes = []): bool
    {
        // The default update method from Eloquent should work,
        // but you can customize it here if needed
        return parent::update($attributes);
    }
}
```