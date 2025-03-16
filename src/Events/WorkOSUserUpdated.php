<?php

namespace RomegaSoftware\WorkOSTeams\Events;

use RomegaSoftware\WorkOSTeams\Contracts\ExternalId;
use RomegaSoftware\WorkOSTeams\Domain\User as DomainUser;

final class WorkOSUserUpdated
{
    public function __construct(
        public ExternalId $user,
        public DomainUser $updatedUser,
    ) {}
}
