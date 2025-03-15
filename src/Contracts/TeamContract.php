<?php

namespace RomegaSoftware\WorkOSTeams\Contracts;

interface TeamContract
{
    /**
     * Get the name of the team.
     */
    public function getName(): string;

    /**
     * Get the description of the team.
     */
    public function getDescription(): ?string;

    /**
     * Update the team with the given attributes.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function update(array $attributes = []): bool;

    /**
     * Eager load relations on the model.
     *
     * @param  array|string  $relations
     * @return $this
     */
    public function load($relations);

    /**
     * Delete the model from the database.
     *
     * @return bool|null
     */
    public function delete();

    /**
     * Update the model in the database.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function updateQuietly(array $attributes = []): bool;
}
