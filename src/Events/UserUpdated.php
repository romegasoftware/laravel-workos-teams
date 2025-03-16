<?php

namespace RomegaSoftware\WorkOSTeams\Events;

use Illuminate\Foundation\Auth\User;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use RomegaSoftware\WorkOSTeams\Contracts\ExternalId;

class UserUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public User&ExternalId $user,
    ) {}
}
