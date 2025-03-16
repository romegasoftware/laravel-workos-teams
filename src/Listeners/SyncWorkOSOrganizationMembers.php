<?php

namespace App\Listeners;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Contracts\Queue\ShouldQueue;
use RomegaSoftware\WorkOSTeams\Events\TeamCreated;
use RomegaSoftware\WorkOSTeams\Contracts\OrganizationRepository;

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
        Artisan::call('workos:sync-organizations', [
            '--team-id' => $event->team->getKey(),
        ]);
    }
}
