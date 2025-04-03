<?php

namespace RomegaSoftware\WorkOSTeams;

/**
 * @psalm-api
 */
class WorkOSTeams
{
    public static function web(): RouteRegistrar
    {
        return new RouteRegistrar(group: 'web');
    }

    public static function webhooks(): RouteRegistrar
    {
        if (! config('workos-teams.webhook_secret')) {
            throw new \RuntimeException('WorkOS Webhook secret is not configured');
        }

        return new RouteRegistrar(group: 'webhooks');
    }
}
