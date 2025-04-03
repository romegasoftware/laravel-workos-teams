<?php

namespace RomegaSoftware\WorkOSTeams\Contracts;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

interface TeamInvitationContract extends ExternalId
{
    /**
     * Get the team that the invitation belongs to.
     */
    public function team(): BelongsTo;
}
