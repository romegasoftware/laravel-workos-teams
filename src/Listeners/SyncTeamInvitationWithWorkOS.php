<?php

namespace App\Listeners;

use RomegaSoftware\WorkOSTeams\Contracts\UserRepository;
use RomegaSoftware\WorkOSTeams\Events\TeamInvitationCancelled;
use RomegaSoftware\WorkOSTeams\Events\TeamInvitationCreated;

class SyncTeamInvitationWithWorkOS
{
    /**
     * Create the event listener.
     */
    public function __construct(
        protected UserRepository $userRepository,
    ) {}

    /**
     * Handle the invitation event.
     */
    public function handle(TeamInvitationCreated|TeamInvitationCancelled $event): void
    {
        if ($event instanceof TeamInvitationCreated) {
            $invitation = $event->invitation;

            // Send the invitation via WorkOS
            $this->userRepository->sendInvitation(
                $invitation->organization,
                $invitation->email,
                expiresInDays: null,
                inviter: $event->inviter,
                roleSlug: $invitation->role
            );
        } elseif ($event instanceof TeamInvitationCancelled) {
            // Revoke the invitation in WorkOS
            $this->userRepository->revokeInvitation(
                $event->invitation->organization,
                $event->invitation
            );
        }
    }
}
