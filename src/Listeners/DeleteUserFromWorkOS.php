<?php

namespace RomegaSoftware\WorkOSTeams\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use RomegaSoftware\WorkOSTeams\Contracts\UserRepository;
use RomegaSoftware\WorkOSTeams\Events\UserDeleted;

/**
 * @psalm-suppress UnusedClass This class is used as an event listener for UserDeleted through Laravel's event system
 */
final class DeleteUserFromWorkOS implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct(
        protected UserRepository $userRepository,
    ) {}

    /**
     * Handle the event.
     */
    public function handle(UserDeleted $event): void
    {
        $this->userRepository->delete($event->user);
    }
}
