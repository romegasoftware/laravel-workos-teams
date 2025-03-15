<?php

namespace RomegaSoftware\WorkOSTeams\Livewire\Teams;

use Flux\Flux;
use Livewire\Attributes\Title;
use Livewire\Component;
use RomegaSoftware\WorkOSTeams\Contracts\TeamContract;

#[Title('Edit Team')]
class TeamEdit extends Component
{
    public TeamContract $team;

    public string $name = '';

    public string $description = '';

    /**
     * @var array<string, string>
     */
    protected array $rules = [
        'name' => 'required|string|max:255',
        'description' => 'nullable|string|max:1000',
    ];

    public function mount(): void
    {
        $this->authorize('update', $this->team);

        $this->name = $this->team->getName();
        $this->description = $this->team->getDescription() ?? '';
    }

    public function save(): void
    {
        $this->validate();

        // Update the team in the database
        $this->team->update([
            'name' => $this->name,
            'description' => $this->description,
        ]);

        Flux::toast(
            heading: 'Team Updated',
            text: 'Team updated successfully!',
            variant: 'success',
        );

        $this->redirectRoute('teams.show', $this->team);
    }
}
