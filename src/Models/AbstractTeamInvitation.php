<?php

namespace RomegaSoftware\WorkOSTeams\Models;

use Illuminate\Foundation\Auth\User;
use Illuminate\Database\Eloquent\Model;
use RomegaSoftware\WorkOSTeams\Models\Team;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use RomegaSoftware\WorkOSTeams\Contracts\TeamInvitationContract;
use RomegaSoftware\WorkOSTeams\Traits\ImplementsTeamInvitationContract;

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
abstract class AbstractTeamInvitation extends Model implements TeamInvitationContract
{
    use ImplementsTeamInvitationContract;

    /** @use HasFactory<\Illuminate\Database\Eloquent\Factories\Factory> */
    use HasFactory;

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
    ];
}
