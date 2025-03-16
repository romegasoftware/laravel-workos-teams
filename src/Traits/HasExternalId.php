<?php

namespace RomegaSoftware\WorkOSTeams\Traits;

trait HasExternalId
{
    /**
     * Get the name of the column that stores the external ID.
     */
    public function getExternalIdColumn(): string
    {
        return defined(static::class . '::EXTERNAL_ID_COLUMN')
            ? constant(static::class . '::EXTERNAL_ID_COLUMN')
            : 'external_id';
    }

    /**
     * Get the external ID for the model.
     */
    public function getExternalId(): ?string
    {
        return $this->getAttribute($this->getExternalIdColumn());
    }

    /**
     * Set the external ID for the model.
     */
    public function setExternalId(string $externalId): void
    {
        $this->setAttribute($this->getExternalIdColumn(), $externalId);
    }
}
