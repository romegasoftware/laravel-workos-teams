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
    | Note: The `team` model class must implement the
    | `RomegaSoftware\WorkOSTeams\Contracts\TeamContract` interface.
    |
    | For full functionality, we recommend implementing the
    | `RomegaSoftware\WorkOSTeams\Contracts\WorkOSTeams` interface and using the
    | `RomegaSoftware\WorkOSTeams\Traits\ImplementsWorkOSTeams` trait.
    |
    */
    'models' => [
        'team' => \RomegaSoftware\WorkOSTeams\Models\Team::class,
        'team_invitation' => \RomegaSoftware\WorkOSTeams\Models\TeamInvitation::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | WorkOS Teams Webhook Secret
    |--------------------------------------------------------------------------
    |
    | The secret used to verify the WorkOS webhook. Find this in the WorkOS
    | dashboard under the "Webhooks" section. You must also register the webhook
    | route in your application.
    |
    */
    'webhook_secret' => env('WORKOS_TEAMS_WEBHOOK_SECRET', null),

    /*
    |--------------------------------------------------------------------------
    | WorkOS Teams Feature Flags
    |--------------------------------------------------------------------------
    |
    | Here you can enable or disable specific features of the package.
    |
    */
    'features' => [
        'team_switching' => env('WORKOS_TEAMS_TEAM_SWITCHING_ENABLED', true),
        'automatic_organization_sync' => env('WORKOS_TEAMS_AUTOMATIC_ORGANIZATION_SYNC_ENABLED', true),
    ],
];
