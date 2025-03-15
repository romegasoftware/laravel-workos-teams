<?php

namespace RomegaSoftware\WorkOSTeams\Traits;

trait HasWorkOSExternalId
{
    use HasExternalId;

    /**
     * The name of the column that stores the external ID.
     */
    public const EXTERNAL_ID_COLUMN = 'workos_id';
}
