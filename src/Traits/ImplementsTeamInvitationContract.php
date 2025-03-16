<?php

namespace RomegaSoftware\WorkOSTeams\Traits;

use Illuminate\Foundation\Auth\User;
use RomegaSoftware\WorkOSTeams\Models\Team;
use RomegaSoftware\WorkOSTeams\Traits\HasExternalId;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use RomegaSoftware\WorkOSTeams\Events\TeamInvitationCreated;
use RomegaSoftware\WorkOSTeams\Events\TeamInvitationDeleted;

trait ImplementsTeamInvitationContract
{
    use HasExternalId;

    public const EXTERNAL_ID_COLUMN = 'workos_invitation_id';

    public static function bootImplementsTeamInvitationContract(): void
    {
        static::created(function (self $model) {
            event(new TeamInvitationCreated($model));
        });

        static::deleted(function (self $model) {
            event(new TeamInvitationDeleted($model));
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

    /**
     * Get the user that sent the invitation.
     *
     * @api
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<User>
     */
    public function inviter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'invited_by');
    }
}
