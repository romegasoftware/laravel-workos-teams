<?php

namespace RomegaSoftware\WorkOSTeams\Models;

use Override;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use RomegaSoftware\WorkOSTeams\Contracts\TeamInvitationContract;
use RomegaSoftware\WorkOSTeams\Traits\ImplementsTeamInvitationContract;

/**
 * @property string $team_id
 * @property string $email
 * @property string $role
 * @property string $workos_invitation_id
 * @property-read Team $team
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\RomegaSoftware\WorkOSTeams\Models\TeamInvitation query()
 */
abstract class AbstractTeamInvitation extends Model implements TeamInvitationContract
{
    use ImplementsTeamInvitationContract;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'team_id',
        'email',
        'role',
    ];

    /**
     * Get the team that the invitation belongs to.
     */
    #[Override]
    public function team(): BelongsTo
    {
        return $this->belongsTo(config('workos-teams.models.team', Team::class));
    }
}
