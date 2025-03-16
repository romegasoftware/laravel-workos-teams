<?php

namespace RomegaSoftware\WorkOSTeams\Contracts;

use RomegaSoftware\WorkOSTeams\Domain\DTOs\CreateOrganizationDTO;
use RomegaSoftware\WorkOSTeams\Domain\DTOs\FindOrganizationDTO;
use RomegaSoftware\WorkOSTeams\Domain\DTOs\UpdateOrganizationDTO;
use RomegaSoftware\WorkOSTeams\Domain\Organization;
use RomegaSoftware\WorkOSTeams\Domain\OrganizationMembership;

interface OrganizationRepository
{
    /**
     * Get an organization by ID
     */
    public function find(FindOrganizationDTO $dto): ?Organization;

    /**
     * Create a new organization
     */
    public function create(CreateOrganizationDTO $dto): ?Organization;

    /**
     * Update an organization
     */
    public function update(ExternalId $organization, UpdateOrganizationDTO $dto): ?Organization;

    /**
     * Delete an organization
     */
    public function delete(ExternalId $organization): bool;

    /**
     * Add a user to an organization
     *
     * @return null|OrganizationMembership
     */
    public function addUser(ExternalId $organization, ExternalId $user, string $role = 'member'): ?OrganizationMembership;

    /**
     * Remove a user from an organization
     */
    public function removeUser(ExternalId $organization, ExternalId $user): bool;

    /**
     * Get a user's organization memberships
     *
     * @return array<OrganizationMembership>
     */
    public function getUserMemberships(ExternalId $user): array;

    /**
     * Get a user's organization membership
     */
    public function getUserMembership(ExternalId $organization, ExternalId $user): ?OrganizationMembership;
}
