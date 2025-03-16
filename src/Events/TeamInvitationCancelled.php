<?php

namespace RomegaSoftware\WorkOSTeams\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use RomegaSoftware\WorkOSTeams\Models\AbstractTeamInvitation;

class TeamInvitationCancelled
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public AbstractTeamInvitation $invitation
    ) {}
}
