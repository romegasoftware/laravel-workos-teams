<?php

namespace App\Livewire\Teams;

use Flux\Flux;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\Computed;
use Livewire\Volt\Component;
use Livewire\WithPagination;
use RomegaSoftware\WorkOSTeams\Contracts\ExternalId;
use RomegaSoftware\WorkOSTeams\Contracts\TeamContract;
use Illuminate\Foundation\Auth\User;

new class extends Component {
    use AuthorizesRequests;
    use WithPagination;

    public $team;

    public ?string $selectedMemberId = null;

    public string $role = 'member';

    protected $rules = [
        'role' => 'required|in:admin,member',
    ];

    #[Computed]
    public function members()
    {
        return $this->team->members()->paginate(10);
    }

    public function updateRole()
    {
        if (!$this->selectedMemberId) {
            return;
        }

        $this->validate();

        // Ensure the user can update team members
        $this->authorize('updateTeamMember', $this->team);

        $member = User::find($this->selectedMemberId);

        // Use the Team model's addMember method to trigger the observer
        // This will handle the WorkOS synchronization
        $this->team->updateMember($member, $this->role);

        Flux::toast(heading: __('Role Updated'), text: __('Team member role updated successfully!'), variant: 'success');

        $this->reset('selectedMemberId', 'role');
    }

    public function removeMember($userId)
    {
        // Ensure the user can remove team members
        $this->authorize('removeTeamMember', $this->team);

        // Get the member before removing them
        $member = User::find($userId);

        // Use the Team model's removeMember method to trigger the observer
        // This will handle the WorkOS synchronization
        $this->team->removeMember($member);

        Flux::toast(heading: __('Member Removed'), text: __('Team member removed successfully!'), variant: 'success');
    }

    public function selectMember($userId, $currentRole)
    {
        $this->selectedMemberId = $userId;
        $this->role = $currentRole;
    }
};
?>
<div wire:key="team-members-{{ $team->getKey() }}">
    @if ($this->members()->isEmpty())
        <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('No members in this team yet.') }}</p>
    @else
        <flux:table :paginate="$this->members">
            <flux:table.columns>
                <flux:table.column>{{ __('Name') }}</flux:table.column>
                <flux:table.column>{{ __('Email') }}</flux:table.column>
                <flux:table.column>{{ __('Role') }}</flux:table.column>
                <flux:table.column class="text-right">{{ __('Actions') }}</flux:table.column>
            </flux:table.columns>
            @foreach ($this->members as $member)
                <flux:table.row>
                    <flux:table.cell class="font-medium">
                        {{ $member->name }}
                    </flux:table.cell>
                    <flux:table.cell>
                        {{ $member->email }}
                    </flux:table.cell>
                    <flux:table.cell>
                        @if ($selectedMemberId == $member->getKey())
                            <div class="flex items-center space-x-2">
                                <div>
                                    <flux:field>
                                        <flux:label class="sr-only">{{ __('Role') }}</flux:label>
                                        <flux:select
                                            class="text-xs"
                                            wire:model="role"
                                        >
                                            <flux:select.option value="admin">{{ __('Admin') }}</flux:select.option>
                                            <flux:select.option value="member">{{ __('Member') }}</flux:select.option>
                                        </flux:select>
                                    </flux:field>
                                </div>
                                <flux:button
                                    color="green"
                                    size="xs"
                                    type="button"
                                    wire:click="updateRole"
                                >
                                    {{ __('Save') }}
                                </flux:button>
                                <flux:button
                                    color="gray"
                                    size="xs"
                                    type="button"
                                    wire:click="$set('selectedMemberId', null)"
                                >
                                    {{ __('Cancel') }}
                                </flux:button>
                            </div>
                        @else
                            <flux:badge color="blue">
                                {{ ucfirst($member->pivot->role) }}
                            </flux:badge>
                        @endif
                    </flux:table.cell>
                    <flux:table.cell class="text-right">
                        @if ($member->pivot->role !== 'owner' && $member->getKey() !== Auth::user()->getKey())
                            @can('updateTeamMember', $team)
                                @if ($selectedMemberId != $member->getKey())
                                    <flux:button
                                        size="xs"
                                        variant="filled"
                                        wire:click="selectMember('{{ $member->getKey() }}', '{{ $member->pivot->role }}')"
                                    >
                                        {{ __('Change Role') }}
                                    </flux:button>
                                @endif
                            @endcan
                            @can('removeTeamMember', $team)
                                <flux:button
                                    size="xs"
                                    variant="danger"
                                    wire:click="removeMember('{{ $member->getKey() }}')"
                                >
                                    {{ __('Remove') }}
                                </flux:button>
                            @endcan
                        @endif
                    </flux:table.cell>
                </flux:table.row>
            @endforeach
        </flux:table>
    @endif
</div>
