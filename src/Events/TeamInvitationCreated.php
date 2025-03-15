<?php

namespace RomegaSoftware\WorkOSTeams\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Auth\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use RomegaSoftware\WorkOSTeams\Contracts\ExternalId;
use RomegaSoftware\WorkOSTeams\Models\TeamInvitation;

class TeamInvitationCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The inviter user.
     */
    public ?ExternalId $inviter;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public TeamInvitation $invitation
    ) {
        $this->inviter = User::find($invitation->invited_by);
    }
}
