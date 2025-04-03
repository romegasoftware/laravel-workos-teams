<?php

namespace RomegaSoftware\WorkOSTeams\Policies;

use App\Models\User;
use RomegaSoftware\WorkOSTeams\Models\Team;

/**
 * @psalm-api
 */
class TeamPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Team $team): bool
    {
        return $user->belongsToTeam($team);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Team $team): bool
    {
        return $user->ownsTeam($team);
    }

    /**
     * Determine whether the user can add team members.
     */
    public function addTeamMember(User $user, Team $team): bool
    {
        return $user->hasAnyTeamRole($team, ['owner', 'admin']);
    }

    /**
     * Determine whether the user can update team members.
     */
    public function updateTeamMember(User $user, Team $team): bool
    {
        return $user->hasAnyTeamRole($team, ['owner', 'admin']);
    }

    /**
     * Determine whether the user can invite team members.
     */
    public function inviteTeamMember(User $user, Team $team): bool
    {
        return $user->hasAnyTeamRole($team, ['owner', 'admin']);
    }

    /**
     * Determine whether the user can cancel team invitations.
     */
    public function cancelTeamInvitation(User $user, Team $team): bool
    {
        return $user->hasAnyTeamRole($team, ['owner', 'admin']);
    }

    /**
     * Determine whether the user can remove team members.
     */
    public function removeTeamMember(User $user, Team $team): bool
    {
        return $user->hasAnyTeamRole($team, ['owner', 'admin']);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Team $team): bool
    {
        return $user->ownsTeam($team);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Team $team): bool
    {
        return $user->ownsTeam($team);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Team $team): bool
    {
        return $user->ownsTeam($team);
    }
}
