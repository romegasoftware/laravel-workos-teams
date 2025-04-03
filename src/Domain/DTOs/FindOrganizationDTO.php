<?php

namespace RomegaSoftware\WorkOSTeams\Domain\DTOs;

class FindOrganizationDTO
{
    /**
     * Create a new FindOrganizationDTO instance.
     *
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function __construct(
        public readonly string $id,
    ) {}
}
