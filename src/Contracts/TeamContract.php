<?php

namespace RomegaSoftware\WorkOSTeams\Contracts;

/**
 * @mixin \Illuminate\Database\Eloquent\Model
 */
interface TeamContract
{
    /**
     * Undocumented function
     *
     * @return void
     */
    public static function bootImplementsTeamContract(): void;

    /**
     * Get the name of the team.
     */
    public function getName(): string;

    /**
     * Get the description of the team.
     */
    public function getDescription(): ?string;

    /**
     * Update the model in the database without raising any events.
     *
     * @param  array  $attributes
     * @param  array  $options
     * @return bool
     */
    public function updateQuietly(array $attributes = [], array $options = []);
}
