# Customizing Models

The package comes with some models out of the box, ready to go. You are free to extend or replace these models as desired.


## Team Model

You can extend the `AbstractTeam` class which already implements the necessary interfaces and traits:

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
2. Implementing the `WorkOSTeamsContract` interface and using the `ImplementsWorkOSTeamsContract` trait
3. Implementing the `TeamContract` interface and using the `ImplementsTeamContract` trait

The `AbstractTeam` class provides all necessary functionality including:
- Team contract methods (name, description, etc.)
- External ID management for WorkOS integration
- Team relationship methods
- Event handling for team operations

## Team Invitation Model

You can extend the `AbstractTeamInvitation` class which already implements the necessary interfaces and traits:

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
