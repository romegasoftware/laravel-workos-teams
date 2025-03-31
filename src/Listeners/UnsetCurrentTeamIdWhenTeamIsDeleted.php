<?php

namespace RomegaSoftware\WorkOSTeams\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use RomegaSoftware\WorkOSTeams\Contracts\ExternalId;
use RomegaSoftware\WorkOSTeams\Contracts\TeamContract;
use RomegaSoftware\WorkOSTeams\Events\TeamDeleting;
use RomegaSoftware\WorkOSTeams\Models\Team;

final class UnsetCurrentTeamIdWhenTeamIsDeleted implements ShouldQueue
{
    /**
     * Handle the team deletion event.
     */
    public function handle(TeamDeleting $event): void
    {
        /** @var Team&TeamContract&ExternalId $team */
        $team = $event->team;

        // Get all users who have this team as their current team
        $users = $team->members()
            ->where('current_team_id', $team->getKey())
            ->get();

        foreach ($users as $user) {
            // Get all teams the user belongs to
            $teams = $user->allTeams();

            // If there are other teams available, set the first one as current
            if ($teams->isNotEmpty()) {
                $user->updateQuietly(['current_team_id' => $teams->first()->getKey()]);
            } else {
                // If no other teams available, set current_team_id to null
                $user->updateQuietly(['current_team_id' => null]);
            }
        }
    }
}
