<?php

namespace RomegaSoftware\WorkOSTeams\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use RomegaSoftware\WorkOSTeams\Contracts\OrganizationRepository;
use RomegaSoftware\WorkOSTeams\Events\TeamMemberAdded;
use RomegaSoftware\WorkOSTeams\Events\TeamMemberRemoved;
use RomegaSoftware\WorkOSTeams\Events\TeamMemberUpdated;

/**
 * @psalm-suppress UnusedClass This class is used as an event listener for TeamMemberAdded, TeamMemberRemoved, or TeamMemberUpdated through Laravel's event system
 */
final class SyncTeamMemberWithWorkOS implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct(
        protected OrganizationRepository $organizationRepository,
    ) {}

    /**
     * Handle the team member event.
     */
    public function handle(TeamMemberAdded|TeamMemberRemoved|TeamMemberUpdated $event): void
    {
        if ($event instanceof TeamMemberAdded || $event instanceof TeamMemberUpdated) {
            // Add the user to the WorkOS organization
            $this->organizationRepository->addUser(
                $event->team,
                $event->user,
                $event->role
            );
        }
        if ($event instanceof TeamMemberRemoved) {
            // Otherwise, find the membership ID and remove the user
            $this->organizationRepository->removeUser(
                $event->team,
                $event->user
            );
        }
    }
}
