<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use RomegaSoftware\WorkOSTeams\Console\Commands\SyncWorkOSOrganizations;
use RomegaSoftware\WorkOSTeams\Contracts\OrganizationRepository;
use RomegaSoftware\WorkOSTeams\Events\TeamCreated;

class SyncWorkOSOrganizationMembers implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct(
        protected OrganizationRepository $organizationRepository,
    ) {}

    /**
     * Handle the event.
     */
    public function handle(TeamCreated $event): void
    {
        SyncWorkOSOrganizations::dispatch($event->team);
    }
}
