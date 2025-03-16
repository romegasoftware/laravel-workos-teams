<?php

namespace RomegaSoftware\WorkOSTeams\Livewire\Teams;

use Flux\Flux;
use Livewire\Component;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Auth;
use RomegaSoftware\WorkOSTeams\Models\Team;

#[Title('Create Team')]
class TeamCreate extends Component
{
    public string $name = '';

    public string $description = '';

    protected $rules = [
        'name' => 'required|string|max:255',
        'description' => 'nullable|string|max:1000',
    ];

    public function save()
    {
        $this->validate();

        // Create the team in the database
        $team = config('workos-teams.models.team', Team::class)::create([
            'name' => $this->name,
            'description' => $this->description,
        ]);

        $team->addMember(Auth::user(), 'owner');

        // Show success toast notification
        Flux::toast(
            heading: __('Team Created'),
            text: __('Your team has been created successfully!'),
            variant: 'success',
        );

        return $this->redirectRoute('teams.show', $team);
    }

    public function render()
    {
        return view('workos-teams::livewire.teams.team-create');
    }
}
