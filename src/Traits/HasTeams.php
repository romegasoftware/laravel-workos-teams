<?php

namespace RomegaSoftware\WorkOSTeams\Traits;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use RomegaSoftware\WorkOSTeams\Contracts\TeamContract;
use RomegaSoftware\WorkOSTeams\Contracts\UserRepository;
use RomegaSoftware\WorkOSTeams\Events\UserDeleted;
use RomegaSoftware\WorkOSTeams\Events\UserUpdated;

trait HasTeams
{
    /**
     * Boot the trait.
     *
     * @return void
     */
    public static function bootIsTeam()
    {
        static::updated(function ($model) {
            event(new UserUpdated($model));
        });
        static::deleted(function ($model) {
            event(new UserDeleted($model));
        });
    }

    /**
     * Get all the teams the user belongs to.
     */
    public function teams(): BelongsToMany
    {
        $teamModel = config('workos-teams.models.team', \App\Models\Team::class);

        return $this->belongsToMany($teamModel, 'team_members')
            ->withPivot('role')
            ->withTimestamps();
    }

    /**
     * Get the user's current team.
     */
    public function currentTeam(): BelongsTo
    {
        $teamModel = config('workos-teams.models.team', \App\Models\Team::class);

        return $this->belongsTo($teamModel, 'current_team_id');
    }

    /**
     * Get all teams the user belongs to.
     */
    public function allTeams()
    {
        $teams = $this->teams;

        // Check if the current_team_id exists and is not in the teams collection
        if ($this->current_team_id) {
            $currentTeam = $this->currentTeam()->first();
            if ($currentTeam && ! $teams->contains('id', $currentTeam->id)) {
                $teams = $teams->push($currentTeam);
            }
        }

        return $teams->unique('id')->values();
    }

    /**
     * Get the user's current team instance.
     */
    public function getCurrentTeamAttribute()
    {
        if (! $this->relationLoaded('currentTeam')) {
            $this->load('currentTeam');
        }

        return $this->getRelation('currentTeam');
    }

    /**
     * Switch to the given team.
     */
    public function switchTeam($team): void
    {
        // Ensure the user belongs to the team
        if (! $this->belongsToTeam($team)) {
            abort(403, 'You do not belong to this team.');
        }

        // Handle WorkOS organization authentication if applicable
        if (
            $team->getExternalId() &&
            $this->getExternalId() &&
            config('workos-teams.features.team_switching', true)
        ) {
            $authenticated = $this->authenticateWithTeam($team);

            if (! $authenticated) {
                abort(403, 'User is not a member of this organization.');
            }
        }

        $this->update(['current_team_id' => $team->id]);
        $this->load('currentTeam'); // Reload the currentTeam relationship
    }

    /**
     * Determine if the user belongs to the given team.
     */
    public function belongsToTeam($team): bool
    {
        return $this->teams->contains(fn ($t) => $t->id === $team->id);
    }

    /**
     * Get the role that the user has on the team.
     */
    public function teamRole($team): ?string
    {
        if (! $this->belongsToTeam($team)) {
            return null;
        }

        return $this->teams->find($team->id)?->pivot->role;
    }

    /**
     * Determine if the user has the given role on the given team.
     */
    public function hasTeamRole($team, string $role): bool
    {
        if (! $this->belongsToTeam($team)) {
            return false;
        }

        return $this->teamRole($team) === $role;
    }

    /**
     * Determine if the user has the given role on the given team.
     */
    public function hasAnyTeamRole($team, array $roles): bool
    {
        return in_array($this->teamRole($team), $roles);
    }

    /**
     * Authenticate with a WorkOS organization
     */
    protected function authenticateWithTeam(TeamContract $team): bool
    {
        $userRepository = app(UserRepository::class);

        $result = $userRepository->authenticateUser(
            $team,
            $this
        );

        if (! $result['success']) {
            return false;
        } else {
            return true;
        }
    }
}
