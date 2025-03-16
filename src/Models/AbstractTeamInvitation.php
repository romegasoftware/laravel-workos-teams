<?php

namespace RomegaSoftware\WorkOSTeams\Models;

use Illuminate\Foundation\Auth\User;
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
 * @property-read User $inviter
 * @property-read Team $team
 * @method static \Illuminate\Database\Eloquent\Builder|\RomegaSoftware\WorkOSTeams\Models\TeamInvitation query()
 */
abstract class AbstractTeamInvitation extends Model implements ExternalId
{
    use HasExternalId;

    /** @use HasFactory<\Illuminate\Database\Eloquent\Factories\Factory> */
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
