<?php

namespace RomegaSoftware\WorkOSTeams\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use RomegaSoftware\WorkOSTeams\Contracts\UserRepository;
use RomegaSoftware\WorkOSTeams\Events\TeamInvitationCreated;
use RomegaSoftware\WorkOSTeams\Events\TeamInvitationDeleted;

/**
 * @psalm-suppress UnusedClass This class is used as an event listener for TeamInvitationCreated or TeamInvitationDeleted through Laravel's event system
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
    public function handle(TeamInvitationCreated|TeamInvitationDeleted $event): void
    {
        if ($event instanceof TeamInvitationCreated) {
            $invitation = $event->invitation;

            /** @var ?\App\Models\User&\RomegaSoftware\WorkOSTeams\Contracts\ExternalId $inviter */
            $inviter = $event->inviter;

            // Send the invitation via WorkOS
            $this->userRepository->sendInvitation(
                $invitation->team,
                $invitation->email,
                expiresInDays: null,
                inviter: $inviter,
                roleSlug: $invitation->role
            );
        }
        if ($event instanceof TeamInvitationDeleted) {
            // Revoke the invitation in WorkOS
            $this->userRepository->revokeInvitation(
                $event->invitation->team,
                $event->invitation
            );
        }
    }
}
