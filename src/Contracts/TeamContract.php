<?php

namespace RomegaSoftware\WorkOSTeams\Contracts;

/**
 * @mixin \Illuminate\Database\Eloquent\Model
 */
interface TeamContract
{
    /**
     * Undocumented function
     */
    public static function bootImplementsTeamContract(): void;

    /**
     * Get the name of the team.
     */
    public function getName(): string;

    /**
     * Update the model in the database without raising any events.
     *
     * @return bool
     */
    public function updateQuietly(array $attributes = [], array $options = []);
}
