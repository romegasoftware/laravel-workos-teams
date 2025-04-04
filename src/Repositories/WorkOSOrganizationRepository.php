<?php

namespace RomegaSoftware\WorkOSTeams\Repositories;

use Laravel\WorkOS\WorkOS as LaravelWorkOS;
use Ramsey\Uuid\Uuid;
use RomegaSoftware\WorkOSTeams\Contracts\ExternalId;
use RomegaSoftware\WorkOSTeams\Contracts\OrganizationRepository;
use RomegaSoftware\WorkOSTeams\Domain\DTOs\CreateOrganizationDTO;
use RomegaSoftware\WorkOSTeams\Domain\DTOs\FindOrganizationDTO;
use RomegaSoftware\WorkOSTeams\Domain\DTOs\UpdateOrganizationDTO;
use RomegaSoftware\WorkOSTeams\Domain\Organization;
use RomegaSoftware\WorkOSTeams\Domain\OrganizationMembership;
use RomegaSoftware\WorkOSTeams\Events\WorkOSOrganizationDeleted;
use RomegaSoftware\WorkOSTeams\Events\WorkOSOrganizationUpdated;
use RomegaSoftware\WorkOSTeams\Services\WorkOSCacheService;
use RomegaSoftware\WorkOSTeams\Services\WorkOSLogService;
use WorkOS\Organizations;
use WorkOS\UserManagement;

/**
 * @api
 */
class WorkOSOrganizationRepository implements OrganizationRepository
{
    /**
     * The WorkOS Organizations instance.
     */
    protected Organizations $organizations;

    /**
     * The WorkOS UserManagement instance.
     */
    protected UserManagement $userManagement;

    /**
     * Create a new WorkOS organization repository instance.
     *
     * @api
     */
    public function __construct(
        protected WorkOSCacheService $cache,
        protected WorkOSLogService $logger,
    ) {
        // Configure WorkOS
        LaravelWorkOS::configure();

        // Initialize the SDK classes
        $this->organizations = new Organizations;
        $this->userManagement = new UserManagement;
    }

    /**
     * Get an organization by ID
     */
    #[\Override]
    public function find(FindOrganizationDTO $dto): ?Organization
    {
        $cacheKey = $this->cache->getOrganizationKey($dto->id);

        return $this->cache->remember($cacheKey, function () use ($dto) {
            try {
                $organization = $this->organizations->getOrganization($dto->id);

                return Organization::fromArray($organization->toArray());
            } catch (\Exception $e) {
                $this->logger->exception($e, [
                    'organization_id' => $dto->id,
                ]);

                return null;
            }
        });
    }

    /**
     * Create a new organization
     */
    #[\Override]
    public function create(CreateOrganizationDTO $dto): ?Organization
    {
        try {
            $organization = $this->organizations->createOrganization(
                $dto->name,
                $dto->domains,
                $dto->allowProfilesOutsideOrganization,
                (string) Uuid::uuid4(), // idempotency key
                $dto->domainData
            );

            $organizationDomain = Organization::fromArray($organization->toArray());

            // Cache the organization
            $cacheKey = $this->cache->getOrganizationKey($organizationDomain->id);
            $this->cache->set($cacheKey, $organizationDomain);

            return $organizationDomain;
        } catch (\Exception $e) {
            $this->logger->exception($e, [
                'name' => $dto->name,
            ]);

            return null;
        }
    }

    /**
     * Update an organization
     */
    #[\Override]
    public function update(ExternalId $organization, UpdateOrganizationDTO $dto): ?Organization
    {
        try {
            $workosId = $organization->getExternalId();
            assert($workosId !== null);

            $workosOrg = $this->organizations->updateOrganization(
                $workosId,
                $dto->domains,
                $dto->name,
                $dto->allowProfilesOutsideOrganization,
                $dto->domainData
            );

            $organizationDomain = Organization::fromArray($workosOrg->toArray());

            // Update the cache
            $cacheKey = $this->cache->getOrganizationKey($workosId);
            $this->cache->set($cacheKey, $organizationDomain);

            // Dispatch event
            event(new WorkOSOrganizationUpdated($organization, $organizationDomain));

            return $organizationDomain;
        } catch (\Exception $e) {
            $this->logger->exception($e, [
                'organization' => $organization->toArray(),
            ]);

            return null;
        }
    }

    /**
     * Delete an organization
     */
    #[\Override]
    public function delete(ExternalId $organization): bool
    {
        try {
            $workosId = $organization->getExternalId();
            assert($workosId !== null);

            $this->organizations->deleteOrganization($workosId);

            // Clear the cache
            $cacheKey = $this->cache->getOrganizationKey($workosId);
            $this->cache->forget($cacheKey);

            // Dispatch event
            event(new WorkOSOrganizationDeleted($organization));

            return true;
        } catch (\Exception $e) {
            $this->logger->exception($e, [
                'organization' => $organization->toArray(),
            ]);

            return false;
        }
    }

    /**
     * Add a user to an organization
     */
    #[\Override]
    public function addUser(ExternalId $organization, ExternalId $user, string $role = 'member'): ?OrganizationMembership
    {
        try {
            $workosOrganizationId = $organization->getExternalId();
            $workosUserId = $user->getExternalId();
            assert($workosOrganizationId !== null);
            assert($workosUserId !== null);

            /**
             * IDE type hint until resolved in WorkOS PHP SDK
             * https://github.com/workos/workos-php/pull/264
             *
             * @var \WorkOS\Resource\OrganizationMembership&object{
             *   id: string,
             * } $membership */
            $membership = $this->userManagement->createOrganizationMembership(
                $workosUserId,
                $workosOrganizationId
            );

            $updatedMembership = $this->userManagement->updateOrganizationMembership(
                $membership->id,
                $role
            );

            return OrganizationMembership::fromArray($updatedMembership->toArray());
        } catch (\Exception $e) {
            $this->logger->exception($e, [
                'organization' => $organization->toArray(),
                'user' => $user->toArray(),
                'role' => $role,
            ]);

            return null;
        }
    }

    /**
     * Remove a user from an organization
     */
    #[\Override]
    public function removeUser(ExternalId $organization, ExternalId $user): bool
    {
        try {
            $memberships = $this->getUserMembership($organization, $user);

            if (empty($memberships)) {
                return false;
            }

            return true;
        } catch (\Exception $e) {
            $this->logger->exception($e, [
                'organization' => $organization->toArray(),
                'user' => $user->toArray(),
            ]);

            return false;
        }
    }

    /**
     * Get a user's organization memberships
     *
     * @return array<OrganizationMembership>
     */
    #[\Override]
    public function getUserMemberships(ExternalId $user): array
    {
        $workosId = $user->getExternalId();
        assert($workosId !== null);

        $cacheKey = $this->cache->getUserMembershipsKey($workosId);

        return $this->cache->remember($cacheKey, function () use ($user, $workosId) {
            try {
                [,, $memberships] = $this->userManagement->listOrganizationMemberships($workosId);

                return $memberships;
            } catch (\Exception $e) {
                $this->logger->exception($e, [
                    'user' => $user->toArray(),
                ]);

                return [];
            }
        });
    }

    /**
     * Get a user's organization membership
     */
    #[\Override]
    public function getUserMembership(ExternalId $organization, ExternalId $user): ?OrganizationMembership
    {
        $memberships = $this->getUserMemberships($user);

        foreach ($memberships as $membership) {
            if ($membership->organizationId === $organization->getExternalId()) {
                return OrganizationMembership::fromArray($membership->toArray());
            }
        }

        return null;
    }
}
