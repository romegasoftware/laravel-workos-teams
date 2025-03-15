<?php

namespace RomegaSoftware\WorkOSTeams\Services;

use Illuminate\Support\Facades\Session;

class WorkOSSessionService
{
    protected const ACCESS_TOKEN_KEY = 'workos_access_token';

    protected const REFRESH_TOKEN_KEY = 'workos_refresh_token';

    /**
     * Store the access token in the session
     */
    public function storeAccessToken(?string $token): void
    {
        if ($token) {
            Session::put(self::ACCESS_TOKEN_KEY, $token);
        }
    }

    /**
     * Store the refresh token in the session
     */
    public function storeRefreshToken(?string $token): void
    {
        if ($token) {
            Session::put(self::REFRESH_TOKEN_KEY, $token);
        }
    }

    /**
     * Get the access token from the session
     */
    public function getAccessToken(): ?string
    {
        return Session::get(self::ACCESS_TOKEN_KEY);
    }

    /**
     * Get the refresh token from the session
     */
    public function getRefreshToken(): ?string
    {
        return Session::get(self::REFRESH_TOKEN_KEY);
    }

    /**
     * Clear all WorkOS tokens from the session
     */
    public function clearTokens(): void
    {
        Session::forget(self::ACCESS_TOKEN_KEY);
        Session::forget(self::REFRESH_TOKEN_KEY);
    }

    /**
     * Regenerate the session ID
     */
    public function regenerate(): void
    {
        Session::regenerate(true);
    }
}
