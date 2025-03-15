<?php

namespace RomegaSoftware\WorkOSTeams\Traits;

use App\Models\TeamInvitation;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User;
use RomegaSoftware\WorkOSTeams\Events\TeamCreated;
use RomegaSoftware\WorkOSTeams\Events\TeamDeleted;
use RomegaSoftware\WorkOSTeams\Events\TeamMemberAdded;
use RomegaSoftware\WorkOSTeams\Events\TeamMemberRemoved;
use RomegaSoftware\WorkOSTeams\Events\TeamMemberUpdated;
use RomegaSoftware\WorkOSTeams\Events\TeamUpdated;

trait IsTeam
{
    /**
     * Boot the trait.
     *
     * @return void
     */
    public static function bootIsTeam()
    {
        static::created(function ($model) {
            event(new TeamCreated($model));
        });

        static::updated(function ($model) {
            event(new TeamUpdated($model));
        });

        static::deleted(function ($model) {
            event(new TeamDeleted($model));
        });
    }

    /**
     * Get the invitations for the team.
     */
    public function invitations(): HasMany
    {
        return $this->hasMany(config('workos-teams.models.team_invitation', TeamInvitation::class));
    }

    /**
     * Determine if the given user belongs to the team.
     */
    public function hasUser(User $user): bool
    {
        return $this->members()->where('user_id', $user->id)->exists();
    }

    /**
     * Add a member to the team.
     */
    public function addMember(User $user, string $role = 'member'): void
    {
        $this->members()->attach($user, ['role' => $role]);

        // Use the observer to handle the event
        event(new TeamMemberAdded($this, $user, $role));
    }

    /**
     * Update a member's role in the team.
     */
    public function updateMember(User $user, string $role = 'member'): void
    {
        $this->members()->updateExistingPivot($user, ['role' => $role]);

        // Use the observer to handle the event
        event(new TeamMemberUpdated($this, $user, $role));
    }

    /**
     * Remove a member from the team.
     */
    public function removeMember(User $user): void
    {
        $this->members()->detach($user);

        // Use the observer to handle the event
        event(new TeamMemberRemoved($this, $user));
    }
}
