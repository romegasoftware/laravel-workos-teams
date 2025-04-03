<?php

namespace RomegaSoftware\WorkOSTeams\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event handler mappings for the application.
     *
     * @var array<string, array<int, string>>
     */
    protected $listen = [
        // Team events
        \RomegaSoftware\WorkOSTeams\Events\TeamCreated::class => [
            \RomegaSoftware\WorkOSTeams\Listeners\SyncTeamWithWorkOS::class,
        ],
        \RomegaSoftware\WorkOSTeams\Events\TeamUpdated::class => [
            \RomegaSoftware\WorkOSTeams\Listeners\SyncTeamWithWorkOS::class,
        ],
        \RomegaSoftware\WorkOSTeams\Events\TeamDeleting::class => [
            \RomegaSoftware\WorkOSTeams\Listeners\SyncTeamWithWorkOS::class,
            \RomegaSoftware\WorkOSTeams\Listeners\UnsetCurrentTeamIdWhenTeamIsDeleted::class,
        ],

        // Team member events
        \RomegaSoftware\WorkOSTeams\Events\TeamMemberAdded::class => [
            \RomegaSoftware\WorkOSTeams\Listeners\SyncTeamMemberWithWorkOS::class,
        ],
        \RomegaSoftware\WorkOSTeams\Events\TeamMemberUpdated::class => [
            \RomegaSoftware\WorkOSTeams\Listeners\SyncTeamMemberWithWorkOS::class,
        ],
        \RomegaSoftware\WorkOSTeams\Events\TeamMemberRemoved::class => [
            \RomegaSoftware\WorkOSTeams\Listeners\SyncTeamMemberWithWorkOS::class,
        ],

        // Team invitation events
        \RomegaSoftware\WorkOSTeams\Events\TeamInvitationCreated::class => [
            \RomegaSoftware\WorkOSTeams\Listeners\SyncTeamInvitationWithWorkOS::class,
        ],
        \RomegaSoftware\WorkOSTeams\Events\TeamInvitationDeleting::class => [
            \RomegaSoftware\WorkOSTeams\Listeners\SyncTeamInvitationWithWorkOS::class,
        ],

        // User events
        \RomegaSoftware\WorkOSTeams\Events\UserDeleted::class => [
            \RomegaSoftware\WorkOSTeams\Listeners\DeleteUserFromWorkOS::class,
        ],
    ];
}
