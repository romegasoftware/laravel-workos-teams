<?php

namespace RomegaSoftware\WorkOSTeams\Repositories;

use Laravel\WorkOS\WorkOS as LaravelWorkOS;
use RomegaSoftware\WorkOSTeams\Contracts\ExternalId;
use RomegaSoftware\WorkOSTeams\Contracts\UserRepository;
use RomegaSoftware\WorkOSTeams\Domain\DTOs\CreateUserDTO;
use RomegaSoftware\WorkOSTeams\Domain\DTOs\FindUserDTO;
use RomegaSoftware\WorkOSTeams\Domain\DTOs\ListUsersDTO;
use RomegaSoftware\WorkOSTeams\Domain\DTOs\UpdateUserDTO;
use RomegaSoftware\WorkOSTeams\Domain\OrganizationInvitation;
use RomegaSoftware\WorkOSTeams\Domain\User;
use RomegaSoftware\WorkOSTeams\Events\WorkOSUserDeleted;
use RomegaSoftware\WorkOSTeams\Events\WorkOSUserUpdated;
use RomegaSoftware\WorkOSTeams\Services\WorkOSCacheService;
use RomegaSoftware\WorkOSTeams\Services\WorkOSLogService;
use RomegaSoftware\WorkOSTeams\Services\WorkOSSessionService;
use WorkOS\Resource\AuthenticationResponse;
use WorkOS\UserManagement;

/**
 * @api
 */
class WorkOSUserRepository implements UserRepository
{
    /**
     * The WorkOS UserManagement instance.
     */
    protected UserManagement $userManagement;

    /**
     * Create a new WorkOS user repository instance.
     *
     * @api
     */
    public function __construct(
        protected WorkOSCacheService $cache,
        protected WorkOSLogService $logger,
        protected WorkOSSessionService $session,
    ) {
        // Configure WorkOS
        LaravelWorkOS::configure();

        // Initialize the SDK class
        $this->userManagement = new UserManagement;
    }

    /**
     * Get a user by ID
     */
    #[\Override]
    public function find(FindUserDTO $dto): ?User
    {
        $cacheKey = $this->cache->getUserKey($dto->id);

        return $this->cache->remember($cacheKey, function () use ($dto) {
            try {
                $workosUser = $this->userManagement->getUser($dto->id);

                return User::fromArray($workosUser->toArray());
            } catch (\Exception $e) {
                $this->logger->exception($e, [
                    'user_id' => $dto->id,
                ]);

                return null;
            }
        });
    }

    /**
     * Create a new user
     */
    #[\Override]
    public function create(CreateUserDTO $dto): ?User
    {
        try {
            $user = $this->userManagement->createUser(
                $dto->email,
                $dto->password,
                $dto->firstName,
                $dto->lastName,
                $dto->emailVerified,
                $dto->passwordHash,
                $dto->passwordHashType
            );

            $userDomain = User::fromArray($user->toArray());

            // Cache the user
            $cacheKey = $this->cache->getUserKey($userDomain->id);
            $this->cache->set($cacheKey, $userDomain);

            return $userDomain;
        } catch (\Exception $e) {
            $this->logger->exception($e, [
                'email' => $dto->email,
            ]);

            return null;
        }
    }

    /**
     * Update a user
     */
    #[\Override]
    public function update(ExternalId $user, UpdateUserDTO $dto): ?User
    {
        try {
            $workosId = $user->getExternalId();
            assert($workosId !== null);

            $workosUser = $this->userManagement->updateUser(
                $workosId,
                $dto->firstName,
                $dto->lastName,
                $dto->emailVerified,
                $dto->password,
                $dto->passwordHash,
                $dto->passwordHashType
            );

            $userDomain = User::fromArray($workosUser->toArray());

            // Update the cache
            $cacheKey = $this->cache->getUserKey($workosId);
            $this->cache->set($cacheKey, $userDomain);

            // Dispatch event
            event(new WorkOSUserUpdated($user, $userDomain));

            return $userDomain;
        } catch (\Exception $e) {
            $this->logger->exception($e, [
                'user' => $user->toArray(),
            ]);

            return null;
        }
    }

    /**
     * Delete a user
     */
    #[\Override]
    public function delete(ExternalId $user): bool
    {
        try {
            $workosId = $user->getExternalId();
            assert($workosId !== null);

            $this->userManagement->deleteUser($workosId);

            // Clear the cache
            $cacheKey = $this->cache->getUserKey($workosId);
            $this->cache->forget($cacheKey);

            // Dispatch event
            event(new WorkOSUserDeleted($user));

            return true;
        } catch (\Exception $e) {
            $this->logger->exception($e, [
                'user' => $user->toArray(),
                'workos_id' => $user->getExternalId(),
            ]);

            return false;
        }
    }

    /**
     * Authenticate a user with refresh token
     */
    #[\Override]
    public function authenticateWithRefreshToken(string $refreshToken): array
    {
        try {
            /**
             * IDE type hint until resolved in WorkOS PHP SDK
             * https://github.com/workos/workos-php/pull/264
             *
             * @var AuthenticationResponse&object{
             *    access_token: string,
             *    refresh_token: string,
             *    organization_id: ?string
             */
            $result = $this->userManagement->authenticateWithRefreshToken(
                config('services.workos.client_id'),
                $refreshToken
            );

            // Store the tokens in the session
            $this->storeTokensInSession($result);

            return [
                'access_token' => $result->access_token,
                'refresh_token' => $result->refresh_token,
                'organization_id' => $result->organization_id ?? null,
                'success' => true,
            ];
        } catch (\Exception $e) {
            $this->logger->exception($e);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Authenticate a user with an organization
     */
    #[\Override]
    public function authenticateUser(
        ExternalId $organization,
        ExternalId $user,
        ?string $ipAddress = null,
        ?string $userAgent = null
    ): array {
        try {
            /**
             * IDE type hint until resolved in WorkOS PHP SDK
             * https://github.com/workos/workos-php/pull/264
             *
             * @var AuthenticationResponse&object{
             *    access_token: string,
             *    refresh_token: string,
             *    organization_id: string
             * } $result
             */
            $result = $this->userManagement->authenticateWithRefreshToken(
                config('services.workos.client_id'),
                $this->session->getRefreshToken(),
                $ipAddress,
                $userAgent,
                $organization->getExternalId()
            );

            // Store the tokens in the session
            $this->storeTokensInSession($result);

            return [
                'access_token' => $result->access_token,
                'refresh_token' => $result->refresh_token,
                'organization_id' => $result->organization_id,
                'success' => true,
            ];
        } catch (\Exception $e) {
            $this->logger->exception($e, [
                'organization' => $organization->toArray(),
                'user' => $user->toArray(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * IDE type hint until resolved in WorkOS PHP SDK
     * https://github.com/workos/workos-php/pull/264
     *
     * @psalm-param AuthenticationResponse&object{
     *    access_token: string,
     *    refresh_token: string
     * } $tokens
     */
    protected function storeTokensInSession(AuthenticationResponse $tokens): void
    {
        $this->session->storeAccessToken($tokens->access_token);
        $this->session->storeRefreshToken($tokens->refresh_token);
        $this->session->regenerate();
    }

    /**
     * List users
     *
     * @return array{0?: null|string, 1?: null|string, 2?: array<array-key, \WorkOS\Resource\User>}
     */
    #[\Override]
    public function listUsers(ListUsersDTO $dto): array
    {
        try {
            [$before, $after, $users] = $this->userManagement->listUsers(
                $dto->email,
                $dto->organizationId,
                $dto->limit,
                $dto->before,
                $dto->after,
                $dto->order
            );

            return [$before, $after, $users];
        } catch (\Exception $e) {
            $this->logger->exception($e, [
                'dto' => $dto->toArray(),
            ]);

            return [];
        }
    }

    /**
     * Send an invitation to join an organization
     */
    #[\Override]
    public function sendInvitation(
        ExternalId $organization,
        string $email,
        ?int $expiresInDays = null,
        ?string $roleSlug = null
    ): ?array {
        try {
            $invitation = $this->userManagement->sendInvitation(
                $email,
                $organization->getExternalId(),
                $expiresInDays,
                $roleSlug
            );

            return $invitation->toArray();
        } catch (\Exception $e) {
            $this->logger->exception($e, [
                'email' => $email,
                'organization' => $organization->toArray(),
            ]);

            return null;
        }
    }

    /**
     * Revoke an invitation
     */
    #[\Override]
    public function revokeInvitation(ExternalId $organization, ExternalId $invitation): ?OrganizationInvitation
    {
        if ($invitation->getExternalId() === null) {
            return null;
        }

        try {
            $invitationId = $invitation->getExternalId();
            assert($invitationId !== null);
            $response = $this->userManagement->revokeInvitation($invitationId);

            return OrganizationInvitation::fromArray($response->toArray());
        } catch (\Exception $e) {
            $this->logger->exception($e, [
                'organization' => $organization->toArray(),
                'invitation' => $invitation->toArray(),
            ]);

            return null;
        }
    }
}
