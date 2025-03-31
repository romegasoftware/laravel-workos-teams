<?php

namespace RomegaSoftware\WorkOSTeams\Traits;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use RomegaSoftware\WorkOSTeams\Events\TeamInvitationCreated;
use RomegaSoftware\WorkOSTeams\Events\TeamInvitationDeleting;
use RomegaSoftware\WorkOSTeams\Models\Team;

trait ImplementsTeamInvitationContract
{
    use HasExternalId;

    public const EXTERNAL_ID_COLUMN = 'workos_invitation_id';

    public static function bootImplementsTeamInvitationContract(): void
    {
        static::created(function (self $model) {
            event(new TeamInvitationCreated($model));
        });

        static::deleting(function (self $model) {
            event(new TeamInvitationDeleting($model));
        });
    }

    public function initializeImplementsTeamInvitationContract(): void
    {
        $this->mergeFillable([
            'workos_invitation_id',
        ]);
    }

    /**
     * Get the team that the invitation belongs to.
     *
     * @api
     */
    public function team(): BelongsTo
    {
        return $this->belongsTo(config('workos-teams.models.team', Team::class));
    }
}
