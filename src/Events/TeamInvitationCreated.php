<?php

namespace RomegaSoftware\WorkOSTeams\Events;

use App\Models\User;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use RomegaSoftware\WorkOSTeams\Contracts\ExternalId;
use RomegaSoftware\WorkOSTeams\Models\AbstractTeamInvitation;

final class TeamInvitationCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The inviter user.
     */
    public ?User $inviter;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public AbstractTeamInvitation $invitation
    ) {
        $this->inviter = User::find($invitation->invited_by);
    }
}
