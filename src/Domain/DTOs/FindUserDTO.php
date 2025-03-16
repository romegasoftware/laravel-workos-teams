<?php

namespace RomegaSoftware\WorkOSTeams\Domain\DTOs;

final class FindUserDTO
{
    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function __construct(
        public readonly string $id,
        public readonly ?string $organizationId = null,
    ) {}
}
