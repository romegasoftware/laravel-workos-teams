<?php

namespace RomegaSoftware\WorkOSTeams\Livewire\Teams;

use Flux\Flux;
use Livewire\Attributes\Title;
use Livewire\Component;
use RomegaSoftware\WorkOSTeams\Models\Team;

#[Title('Team Details')]
class TeamShow extends Component
{
    public $team;

    public function mount($team)
    {
        $teamModel = config('workos-teams.models.team', Team::class);

        $this->team = $teamModel::find($team);

        $this->authorize('view', $this->team);

        // Eager load members with their pivot data
        $this->team->load(['members' => function ($query) {
            $query->withPivot('role', 'created_at');
        }]);
    }

    public function deleteTeam()
    {
        $this->authorize('delete', $this->team);

        $this->team->delete();

        Flux::toast(
            heading: __('Team Deleted'),
            text: __('Team deleted successfully!'),
            variant: 'success',
        );

        return $this->redirectRoute('teams.index');
    }

    public function render()
    {
        return view('workos-teams::livewire.teams.team-show');
    }
}
