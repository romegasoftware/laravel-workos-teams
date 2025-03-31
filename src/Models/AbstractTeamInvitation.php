<?php

namespace RomegaSoftware\WorkOSTeams\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User;
use RomegaSoftware\WorkOSTeams\Contracts\TeamInvitationContract;
use RomegaSoftware\WorkOSTeams\Traits\ImplementsTeamInvitationContract;

/**
 * @property string $team_id
 * @property string $email
 * @property string $role
 * @property string $workos_invitation_id
 * @property-read User $inviter
 * @property-read Team $team
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\RomegaSoftware\WorkOSTeams\Models\TeamInvitation query()
 */
abstract class AbstractTeamInvitation extends Model implements TeamInvitationContract
{
    /** @use HasFactory<\Illuminate\Database\Eloquent\Factories\Factory> */
    use HasFactory;

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
}
