<?php

namespace RomegaSoftware\WorkOSTeams\Events;

use Illuminate\Foundation\Auth\User;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use RomegaSoftware\WorkOSTeams\Contracts\ExternalId;
use RomegaSoftware\WorkOSTeams\Models\AbstractTeamInvitation;

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
        public AbstractTeamInvitation $invitation
    ) {
        $this->inviter = User::find($invitation->invited_by);
    }
}
