<?php

namespace RomegaSoftware\WorkOSTeams\Contracts;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

interface TeamInvitationContract extends ExternalId
{
    public function team(): BelongsTo;

    public function inviter(): BelongsTo;
}
