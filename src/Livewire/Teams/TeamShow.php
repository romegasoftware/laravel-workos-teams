<?php

namespace RomegaSoftware\WorkOSTeams\Livewire\Teams;

use Flux\Flux;
use Livewire\Attributes\Title;
use Livewire\Component;
use RomegaSoftware\WorkOSTeams\Contracts\TeamContract;

#[Title('Team Details')]
class TeamShow extends Component
{
    public TeamContract $team;

    public function mount()
    {
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
            heading: 'Team Deleted',
            text: 'Team deleted successfully!',
            variant: 'success',
        );

        return $this->redirectRoute('teams.index');
    }
}
