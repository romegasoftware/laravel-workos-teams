<?php

namespace RomegaSoftware\WorkOSTeams\Events;

use RomegaSoftware\WorkOSTeams\Contracts\ExternalId;
use RomegaSoftware\WorkOSTeams\Domain\Organization;

class WorkOSOrganizationUpdated
{
    public function __construct(
        public ExternalId $organization,
        public Organization $updatedOrganization,
    ) {}
}
