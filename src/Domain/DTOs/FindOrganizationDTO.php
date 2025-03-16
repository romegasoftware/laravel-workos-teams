<?php

namespace RomegaSoftware\WorkOSTeams\Domain\DTOs;

final class FindOrganizationDTO
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
