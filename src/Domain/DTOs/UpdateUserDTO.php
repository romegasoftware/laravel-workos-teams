<?php

namespace RomegaSoftware\WorkOSTeams\Domain\DTOs;

class UpdateUserDTO
{
    /**
     * Create a new UpdateUserDTO instance.
     */
    public function __construct(
        public readonly string $id,
        public readonly ?string $firstName = null,
        public readonly ?string $lastName = null,
        public readonly ?bool $emailVerified = null,
        public readonly ?string $password = null,
        public readonly ?string $passwordHash = null,
        public readonly ?string $passwordHashType = null,
    ) {}
}
