<?php

namespace RomegaSoftware\WorkOSTeams\Events;

use RomegaSoftware\WorkOSTeams\Contracts\ExternalId;

class WorkOSOrganizationDeleted
{
    public function __construct(
        public ExternalId $organization,
    ) {}
}
