<?php

namespace RomegaSoftware\WorkOSTeams\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Auth\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use RomegaSoftware\WorkOSTeams\Contracts\ExternalId;
use RomegaSoftware\WorkOSTeams\Contracts\TeamContract;

final class TeamMemberUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public TeamContract&ExternalId $team,
        public User&ExternalId $user,
        public string $role,
    ) {}
}
