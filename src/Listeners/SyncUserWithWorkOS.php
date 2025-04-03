<?php

namespace RomegaSoftware\WorkOSTeams\Listeners;

use Illuminate\Support\Str;
use RomegaSoftware\WorkOSTeams\Contracts\UserRepository;
use RomegaSoftware\WorkOSTeams\Domain\DTOs\UpdateUserDTO;
use RomegaSoftware\WorkOSTeams\Events\UserUpdated;
use RuntimeException;

/**
 * @psalm-suppress UnusedClass This class is used as an event listener for UserUpdated through Laravel's event system
 */
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
            id: $user->getExternalId() ?? throw new RuntimeException('User has no external ID'),
            firstName: Str::before($user->getAttribute('name'), ' '),
            lastName: Str::after($user->getAttribute('name'), ' '),
        ));
    }
}
