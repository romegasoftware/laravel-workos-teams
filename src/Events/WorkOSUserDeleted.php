<?php

namespace RomegaSoftware\WorkOSTeams\Events;

use RomegaSoftware\WorkOSTeams\Contracts\ExternalId;

class WorkOSUserDeleted
{
    public function __construct(
        public ExternalId $user,
    ) {}
}
