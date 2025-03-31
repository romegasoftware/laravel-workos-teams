<?php

namespace RomegaSoftware\WorkOSTeams\Contracts;

use RomegaSoftware\WorkOSTeams\Domain\DTOs\CreateUserDTO;
use RomegaSoftware\WorkOSTeams\Domain\DTOs\FindUserDTO;
use RomegaSoftware\WorkOSTeams\Domain\DTOs\ListUsersDTO;
use RomegaSoftware\WorkOSTeams\Domain\DTOs\UpdateUserDTO;
use RomegaSoftware\WorkOSTeams\Domain\OrganizationInvitation;
use RomegaSoftware\WorkOSTeams\Domain\User;

interface UserRepository
{
    /**
     * Get a user by ID
     */
    public function find(FindUserDTO $dto): ?User;

    /**
     * Create a new user
     */
    public function create(CreateUserDTO $dto): ?User;

    /**
     * Update a user
     */
    public function update(ExternalId $user, UpdateUserDTO $dto): ?User;

    /**
     * Delete a user
     */
    public function delete(ExternalId $user): bool;

    /**
     * Get a list of all of your existing users matching the criteria specified.
     *
     * @return array{0?: null|string, 1?: null|string, 2?: array<array-key, \WorkOS\Resource\User>}
     */
    public function listUsers(ListUsersDTO $dto): array;

    /**
     * Authenticate a user with refresh token
     *
     * @return array{
     *   access_token?: string,
     *   refresh_token?: string,
     *   organization_id?: ?string,
     *   success: bool,
     *   error?: string
     * }
     */
    public function authenticateWithRefreshToken(string $refreshToken): array;

    /**
     * Authenticate a user with an organization
     *
     * @return array{
     *   access_token?: string,
     *   refresh_token?: string,
     *   organization_id?: string,
     *   success: bool,
     *   error?: string
     * }
     */
    public function authenticateUser(
        ExternalId $organization,
        ExternalId $user,
        ?string $ipAddress = null,
        ?string $userAgent = null
    ): array;

    /**
     * Send an invitation to join an organization
     *
     * @return array{
     *   id: string,
     *   email: string,
     *   organization_id?: string,
     *   role_slug?: string,
     *   expires_at?: string
     * }|null
     */
    public function sendInvitation(
        ExternalId $organization,
        string $email,
        ?int $expiresInDays = null,
        ?string $roleSlug = null
    ): ?array;

    /**
     * Revoke an invitation
     */
    public function revokeInvitation(ExternalId $organization, ExternalId $invitation): ?OrganizationInvitation;
}
