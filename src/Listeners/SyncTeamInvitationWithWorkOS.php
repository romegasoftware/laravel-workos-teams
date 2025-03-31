<?php

namespace RomegaSoftware\WorkOSTeams\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use RomegaSoftware\WorkOSTeams\Contracts\UserRepository;
use RomegaSoftware\WorkOSTeams\Events\TeamInvitationCreated;
use RomegaSoftware\WorkOSTeams\Events\TeamInvitationDeleting;

/**
 * @psalm-suppress UnusedClass This class is used as an event listener for TeamInvitationCreated or TeamInvitationDeleting through Laravel's event system
 */
final class SyncTeamInvitationWithWorkOS implements ShouldQueue
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
    public function handle(TeamInvitationCreated|TeamInvitationDeleting $event): void
    {
        if ($event instanceof TeamInvitationCreated) {
            $invitation = $event->invitation;

            // Send the invitation via WorkOS
            $this->userRepository->sendInvitation(
                $invitation->team,
                $invitation->email,
                expiresInDays: null,
                roleSlug: $invitation->role
            );
        }
        if ($event instanceof TeamInvitationDeleting) {
            // Revoke the invitation in WorkOS
            $this->userRepository->revokeInvitation(
                $event->invitation->team,
                $event->invitation
            );
        }
    }
}
