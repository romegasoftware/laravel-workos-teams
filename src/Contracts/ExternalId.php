<?php

namespace RomegaSoftware\WorkOSTeams\Contracts;

/**
 * @mixin \Illuminate\Database\Eloquent\Model
 *
 * @property string $id
 */
interface ExternalId
{
    /**
     * Get the external ID for the model.
     */
    public function getExternalId(): ?string;

    /**
     * Set the external ID for the model.
     */
    public function setExternalId(string $externalId): void;

    /**
     * Convert the model instance to an array.
     *
     * @return array<string, mixed>
     */
    public function toArray();
}
