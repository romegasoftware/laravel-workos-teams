<?php

namespace RomegaSoftware\WorkOSTeams\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use RomegaSoftware\WorkOSTeams\Events\TeamCreated;
use RomegaSoftware\WorkOSTeams\Events\TeamUpdated;
use RomegaSoftware\WorkOSTeams\Domain\Organization;
use RomegaSoftware\WorkOSTeams\Events\TeamDeleting;
use RomegaSoftware\WorkOSTeams\Contracts\OrganizationRepository;
use RomegaSoftware\WorkOSTeams\Domain\DTOs\CreateOrganizationDTO;
use RomegaSoftware\WorkOSTeams\Domain\DTOs\UpdateOrganizationDTO;

/**
 * @psalm-suppress UnusedClass This class is used as an event listener for TeamCreated, TeamUpdated, or TeamDeleting through Laravel's event system
 */
class SyncTeamWithWorkOS implements ShouldQueue
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
    public function handle(TeamCreated|TeamUpdated|TeamDeleting $event): void
    {
        $team = $event->team;

        if ($event instanceof TeamDeleting) {
            $this->organizationRepository->delete($team);
        }
        if ($event instanceof TeamUpdated) {
            $this->organizationRepository->update($team, new UpdateOrganizationDTO(
                name: $team->getAttribute('name'),
            ));
        }
        if ($event instanceof TeamCreated) {
            $organization = $this->organizationRepository->create(new CreateOrganizationDTO(
                name: $team->getAttribute('name'),
            ));

            if ($organization instanceof Organization) {
                $team->setExternalId($organization->id);
                $team->saveQuietly();
            }
        }
    }
}
