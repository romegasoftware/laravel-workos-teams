<?php

namespace RomegaSoftware\WorkOSTeams\Domain\DTOs;

class CreateUserDTO
{
    /**
     * Create a new CreateUserDTO instance.
     *
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function __construct(
        public readonly string $id,
        public readonly ?string $organizationId,
        public readonly ?string $firstName,
        public readonly ?string $lastName,
        public readonly string $email,
        public readonly ?string $password = null,
        public readonly ?string $passwordHash = null,
        public readonly ?string $passwordHashType = null,
        public readonly ?bool $emailVerified = null,
    ) {}
}
