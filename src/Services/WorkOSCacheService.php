<?php

namespace RomegaSoftware\WorkOSTeams\Services;

use Illuminate\Support\Facades\Cache;

class WorkOSCacheService
{
    /**
     * Cache TTL in seconds (5 minutes)
     */
    protected const CACHE_TTL = 300;

    /**
     * Set a cached value
     *
     * @param \RomegaSoftware\WorkOSTeams\Domain\Organization|\RomegaSoftware\WorkOSTeams\Domain\User $value
     */
    public function set(string $key, \RomegaSoftware\WorkOSTeams\Domain\User|\RomegaSoftware\WorkOSTeams\Domain\Organization $value): void
    {
        Cache::put($key, $value, self::CACHE_TTL);
    }

    /**
     * Forget a cached value
     */
    public function forget(string $key): void
    {
        Cache::forget($key);
    }

    /**
     * Remember a value for a given key
     */
    public function remember(string $key, \Closure $callback): mixed
    {
        return Cache::remember($key, self::CACHE_TTL, $callback);
    }

    /**
     * Get the cache key for a WorkOS organization
     */
    public function getOrganizationKey(string $organizationId): string
    {
        return "workos_organization_{$organizationId}";
    }

    /**
     * Get the cache key for a WorkOS user
     */
    public function getUserKey(string $userId): string
    {
        return "workos_user_{$userId}";
    }

    /**
     * Get the cache key for a user's WorkOS memberships
     */
    public function getUserMembershipsKey(string $userId): string
    {
        return "workos_user_memberships_{$userId}";
    }
}
