<?php

namespace App\Listeners;

use Illuminate\Support\Str;
use RomegaSoftware\WorkOSTeams\Contracts\UserRepository;
use RomegaSoftware\WorkOSTeams\Domain\DTOs\UpdateUserDTO;
use RomegaSoftware\WorkOSTeams\Events\UserUpdated;

class SyncUserWithWorkOS
{
    public function __construct(
        protected UserRepository $userRepository,
    ) {}

    /**
     * Handle the event.
     */
    public function handle(UserUpdated $event): void
    {
        $user = $event->user;

        // Get the changed attributes
        $changedAttributes = $user->getChanges();

        if (empty($changedAttributes)) {
            return;
        }

        $this->userRepository->update($user, new UpdateUserDTO(
            id: $user->getExternalId(),
            firstName: Str::before($user->name, ' '),
            lastName: Str::after($user->name, ' '),
        ));
    }
}
