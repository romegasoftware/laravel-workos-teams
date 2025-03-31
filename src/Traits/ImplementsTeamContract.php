<?php

namespace RomegaSoftware\WorkOSTeams\Traits;

use RomegaSoftware\WorkOSTeams\Events\TeamCreated;
use RomegaSoftware\WorkOSTeams\Events\TeamDeleting;
use RomegaSoftware\WorkOSTeams\Events\TeamUpdated;

/**
 * @property string $name
 */
trait ImplementsTeamContract
{
    /**
     * Boot the trait.
     *
     * @api
     */
    public static function bootImplementsTeamContract(): void
    {
        static::created(function (self $model) {
            event(new TeamCreated($model));
        });

        static::updated(function (self $model) {
            event(new TeamUpdated($model));
        });

        static::deleting(function (self $model) {
            event(new TeamDeleting($model));
        });
    }

    /**
     * Get the name of the team.
     */
    public function getName(): string
    {
        return $this->name;
    }
}
