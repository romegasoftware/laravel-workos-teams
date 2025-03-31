<?php

namespace RomegaSoftware\WorkOSTeams\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use RomegaSoftware\WorkOSTeams\Contracts\OrganizationRepository;
use RomegaSoftware\WorkOSTeams\Domain\DTOs\CreateOrganizationDTO;
use RomegaSoftware\WorkOSTeams\Domain\DTOs\UpdateOrganizationDTO;
use RomegaSoftware\WorkOSTeams\Events\TeamCreated;
use RomegaSoftware\WorkOSTeams\Events\TeamDeleted;
use RomegaSoftware\WorkOSTeams\Events\TeamUpdated;

/**
 * @psalm-suppress UnusedClass This class is used as an event listener for TeamCreated, TeamUpdated, or TeamDeleted through Laravel's event system
 */
final class SyncTeamWithWorkOS implements ShouldQueue
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
        }
        if ($event instanceof TeamUpdated) {
            $this->organizationRepository->update($team, new UpdateOrganizationDTO(
                name: $team->getAttribute('name'),
            ));
        }
        if ($event instanceof TeamCreated) {
            $this->organizationRepository->create(new CreateOrganizationDTO(
                name: $team->getAttribute('name'),
            ));
        }
    }
}
