<?php

namespace RomegaSoftware\WorkOSTeams\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use RomegaSoftware\WorkOSTeams\Contracts\ExternalId;
use Illuminate\Foundation\Auth\User;

class UserDeleted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public User&ExternalId $user,
    ) {}
}
