<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Artisan;
use RomegaSoftware\WorkOSTeams\Contracts\OrganizationRepository;
use RomegaSoftware\WorkOSTeams\Events\TeamCreated;

/**
 * @psalm-suppress UnusedClass This class is used as an event listener for TeamCreated through Laravel's event system
 */
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
