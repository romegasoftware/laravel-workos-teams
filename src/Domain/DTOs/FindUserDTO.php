<?php

namespace RomegaSoftware\WorkOSTeams\Domain\DTOs;

class FindUserDTO
{
    public function __construct(
        public readonly string $id,
        public readonly ?string $organizationId = null,
    ) {}
}
