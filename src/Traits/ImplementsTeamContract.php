<?php

namespace RomegaSoftware\WorkOSTeams\Traits;

use RomegaSoftware\WorkOSTeams\Events\TeamCreated;
use RomegaSoftware\WorkOSTeams\Events\TeamDeleted;
use RomegaSoftware\WorkOSTeams\Events\TeamUpdated;

/**
 * @property string $name
 * @property string $description
 */
trait ImplementsTeamContract
{
    /**
     * Boot the trait.
     *
     * @api
     * @return void
     */
    public static function bootImplementsTeamContract(): void
    {
        static::created(function (self $model) {
            event(new TeamCreated($model));
        });

        static::updated(function (self $model) {
            event(new TeamUpdated($model));
        });

        static::deleted(function (self $model) {
            event(new TeamDeleted($model));
        });
    }

    /**
     * Get the name of the team.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get the description of the team.
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }
}
