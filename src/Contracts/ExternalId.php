<?php

namespace RomegaSoftware\WorkOSTeams\Contracts;

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
}
