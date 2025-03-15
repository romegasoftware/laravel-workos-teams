<?php

namespace App\Listeners;

use RomegaSoftware\WorkOSTeams\Contracts\OrganizationRepository;
use RomegaSoftware\WorkOSTeams\Domain\DTOs\CreateOrganizationDTO;
use RomegaSoftware\WorkOSTeams\Domain\DTOs\UpdateOrganizationDTO;
use RomegaSoftware\WorkOSTeams\Events\TeamCreated;
use RomegaSoftware\WorkOSTeams\Events\TeamDeleted;
use RomegaSoftware\WorkOSTeams\Events\TeamUpdated;

class SyncTeamWithWorkOS
{
    /**
     * Create the event listener.
     */
    public function __construct(
        protected OrganizationRepository $organizationRepository,
    ) {}

    /**
     * Handle the team created event.
     */
    public function handle(TeamCreated|TeamUpdated|TeamDeleted $event): void
    {
        $team = $event->team;

        if ($event instanceof TeamDeleted) {
            $this->organizationRepository->delete($team);
        } elseif ($event instanceof TeamUpdated) {
            $this->organizationRepository->update($team, new UpdateOrganizationDTO(
                name: $team->name,
            ));
        } elseif ($event instanceof TeamCreated) {
            $this->organizationRepository->create(new CreateOrganizationDTO(
                name: $team->name,
            ));
        }
    }
}
