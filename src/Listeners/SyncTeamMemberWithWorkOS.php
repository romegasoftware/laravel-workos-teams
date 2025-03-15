<?php

namespace App\Listeners;

use RomegaSoftware\WorkOSTeams\Contracts\OrganizationRepository;
use RomegaSoftware\WorkOSTeams\Events\TeamMemberAdded;
use RomegaSoftware\WorkOSTeams\Events\TeamMemberRemoved;
use RomegaSoftware\WorkOSTeams\Events\TeamMemberUpdated;

class SyncTeamMemberWithWorkOS
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
                $event->team->workos_organization_id,
                $event->user->workos_id,
                $event->role
            );
        } elseif ($event instanceof TeamMemberRemoved) {
            // Otherwise, find the membership ID and remove the user
            $this->organizationRepository->removeUser(
                $event->team->workos_organization_id,
                $event->user->workos_id
            );
        }
    }
}
