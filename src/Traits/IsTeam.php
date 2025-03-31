<?php

namespace RomegaSoftware\WorkOSTeams\Traits;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User;
use RomegaSoftware\WorkOSTeams\Contracts\ExternalId;
use RomegaSoftware\WorkOSTeams\Events\TeamMemberAdded;
use RomegaSoftware\WorkOSTeams\Events\TeamMemberRemoved;
use RomegaSoftware\WorkOSTeams\Events\TeamMemberUpdated;
use RomegaSoftware\WorkOSTeams\Models\TeamInvitation;

trait IsTeam
{
    /**
     * Get the invitations for the team.
     *
     * @api
     */
    public function invitations(): HasMany
    {
        return $this->hasMany(config('workos-teams.models.team_invitation', TeamInvitation::class));
    }

    /**
     * Get the members of the team.
     *
     * @api
     */
    public function members(): BelongsToMany
    {
        return $this->belongsToMany(config('auth.providers.users.model'), 'team_user')->withPivot('role');
    }

    /**
     * Determine if the given user belongs to the team.
     *
     * @api
     */
    public function hasUser(User $user): bool
    {
        return $this->members()->where($user->getForeignKey(), $user->getKey())->exists();
    }

    /**
     * Add a member to the team.
     *
     * @api
     */
    public function addMember(User&ExternalId $user, string $role = 'member'): void
    {
        $this->members()->attach($user, ['role' => $role]);

        // Use the observer to handle the event
        event(new TeamMemberAdded($this, $user, $role));
    }

    /**
     * Update a member's role in the team.
     *
     * @api
     */
    public function updateMember(User&ExternalId $user, string $role = 'member'): void
    {
        $this->members()->updateExistingPivot($user, ['role' => $role]);

        // Use the observer to handle the event
        event(new TeamMemberUpdated($this, $user, $role));
    }

    /**
     * Remove a member from the team.
     *
     * @api
     */
    public function removeMember(User&ExternalId $user): void
    {
        $this->members()->detach($user);

        // Use the observer to handle the event
        event(new TeamMemberRemoved($this, $user));
    }
}
