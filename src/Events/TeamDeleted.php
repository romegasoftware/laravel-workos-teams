<?php

namespace RomegaSoftware\WorkOSTeams\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use RomegaSoftware\WorkOSTeams\Contracts\ExternalId;
use RomegaSoftware\WorkOSTeams\Contracts\TeamContract;

final class TeamDeleted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public TeamContract&ExternalId $team
    ) {}
}
