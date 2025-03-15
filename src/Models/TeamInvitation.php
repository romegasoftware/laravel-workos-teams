<?php

namespace RomegaSoftware\WorkOSTeams\Models;

use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use RomegaSoftware\WorkOSTeams\Contracts\ExternalId;
use RomegaSoftware\WorkOSTeams\Events\TeamInvitationCancelled;
use RomegaSoftware\WorkOSTeams\Events\TeamInvitationCreated;
use RomegaSoftware\WorkOSTeams\Traits\HasExternalId;

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
     */
    public function inviter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'invited_by');
    }
}
