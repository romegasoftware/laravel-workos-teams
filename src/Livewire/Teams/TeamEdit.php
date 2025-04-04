<?php

namespace RomegaSoftware\WorkOSTeams\Livewire\Teams;

use Flux\Flux;
use Livewire\Attributes\Title;
use Livewire\Component;
use RomegaSoftware\WorkOSTeams\Models\Team;

#[Title('Edit Team')]
class TeamEdit extends Component
{
    public $team;

    public string $name = '';

    /**
     * @var array<string, string>
     */
    protected array $rules = [
        'name' => 'required|string|max:255',
    ];

    public function mount($team): void
    {
        $teamModel = config('workos-teams.models.team', Team::class);

        $this->team = $teamModel::find($team);

        $this->authorize('update', $this->team);

        $this->name = $this->team->getName();
    }

    public function save(): void
    {
        $this->validate();

        // Update the team in the database
        $this->team->update([
            'name' => $this->name,
        ]);

        Flux::toast(
            heading: __('Team Updated'),
            text: __('Team updated successfully!'),
            variant: 'success',
        );

        $this->redirectRoute('teams.show', $this->team);

        $this->dispatch('team-updated');
    }

    public function render()
    {
        return view('workos-teams::livewire.teams.team-edit');
    }
}
