<?php

namespace App\Listeners;

use RomegaSoftware\WorkOSTeams\Contracts\UserRepository;
use RomegaSoftware\WorkOSTeams\Events\UserDeleted;

class DeleteUserFromWorkOS
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
