<?php

return [
    /*
    |--------------------------------------------------------------------------
    | WorkOS Teams Models
    |--------------------------------------------------------------------------
    |
    | These model class names are used to determine which model classes should
    | be used when interacting with WorkOS Teams.
    |
    | Note: The team model class must implement the
    | RomegaSoftware\WorkOSTeams\Contracts\TeamContract interface.
    | For full functionality, we recommend implementing the
    | RomegaSoftware\WorkOSTeams\Contracts\WorkOSTeams interface and using the
    | RomegaSoftware\WorkOSTeams\Traits\ImplementsWorkOSTeams trait.
    |
    */
    'models' => [
        'team' => \App\Models\Team::class,
        'team_invitation' => \App\Models\TeamInvitation::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Routes Prefix & Middleware
    |--------------------------------------------------------------------------
    |
    | Configure the prefix and middleware for the WorkOS Teams webhook routes.
    |
    */
    'routes' => [
        'prefix' => 'webhooks',
        'middleware' => ['api'], // e.g. ['api']
    ],

    /*
    |--------------------------------------------------------------------------
    | WorkOS Teams Webhook Secret
    |--------------------------------------------------------------------------
    |
    | The secret used to verify the WorkOS webhook.
    |
    */
    'webhook_secret' => env('WORKOS_TEAMS_WEBHOOK_SECRET'),

    /*
    |--------------------------------------------------------------------------
    | WorkOS Teams Feature Flags
    |--------------------------------------------------------------------------
    |
    | Here you can enable or disable specific features of the package.
    |
    */
    'features' => [
        'webhooks' => true,
        'team_switching' => true,
        'automatic_organization_sync' => true,
    ],
];
