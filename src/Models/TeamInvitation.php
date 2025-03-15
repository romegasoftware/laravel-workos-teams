<?php

namespace RomegaSoftware\WorkOSTeams\Models;

use App\Models\User as AppUser;
use Illuminate\Database\Eloquent\Model;
use RomegaSoftware\WorkOSTeams\Models\Team;
use RomegaSoftware\WorkOSTeams\Contracts\ExternalId;
use RomegaSoftware\WorkOSTeams\Traits\HasExternalId;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use RomegaSoftware\WorkOSTeams\Events\TeamInvitationCreated;
use RomegaSoftware\WorkOSTeams\Events\TeamInvitationCancelled;

/**
 * @property string $team_id
 * @property string $email
 * @property string $role
 * @property string $invited_by
 * @property string $workos_invitation_id
 * @property-read AppUser $inviter
 * @property-read Team $team
 * @method static \Illuminate\Database\Eloquent\Builder|\RomegaSoftware\WorkOSTeams\Models\TeamInvitation query()
 */
class TeamInvitation extends Model implements ExternalId
{
    use HasExternalId;
    use HasFactory;

    public const EXTERNAL_ID_COLUMN = 'workos_invitation_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'team_id',
        'email',
        'role',
        'invited_by',
        'workos_invitation_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        //
    ];

    /**
     * The events that should be dispatched for the model.
     *
     * @var array<string, string>
     */
    protected $events = [
        'created' => TeamInvitationCreated::class,
        'deleting' => TeamInvitationCancelled::class,
    ];

    /**
     * Get the team that the invitation belongs to.
     */
    public function team(): BelongsTo
    {
        return $this->belongsTo(config('workos-teams.models.team', Team::class));
    }

    /**
     * Get the user that sent the invitation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\User>
     */
    public function inviter(): BelongsTo
    {
        return $this->belongsTo(AppUser::class, 'invited_by');
    }
}
