<?php

namespace RomegaSoftware\WorkOSTeams\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Auth\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use RomegaSoftware\WorkOSTeams\Contracts\ExternalId;

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
