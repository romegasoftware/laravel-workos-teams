<?php

namespace RomegaSoftware\WorkOSTeams\Traits;

trait ImplementsWorkOSTeamsContract
{
    use HasExternalId;
    use ImplementsTeamContract;
    use IsTeam;

    /**
     * The column name for the external ID.
     */
    public const EXTERNAL_ID_COLUMN = 'workos_organization_id';

    public function initializeImplementsWorkOSTeamsContract(): void
    {
        $this->mergeFillable([
            'workos_organization_id',
        ]);
    }
}
