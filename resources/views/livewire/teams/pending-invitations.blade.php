<?php

use App\Models\Team;
use App\Models\TeamInvitation;
use Flux\Flux;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Volt\Component;

new class extends Component
{
    use AuthorizesRequests;

    public Team $team;

    public function cancelInvitation(string $invitationId)
    {
        $invitation = TeamInvitation::findOrFail($invitationId);

        $this->authorize('cancelTeamInvitation', $this->team);

        if ($invitation->team_id !== $this->team->id) {
            abort(403);
        }

        $invitation->delete();

        Flux::toast(
            heading: 'Invitation Cancelled',
            text: 'The invitation has been cancelled.',
            variant: 'success',
        );
    }

    #[Computed]
    public function pendingInvitations()
    {
        return $this->team->invitations()
            ->with('inviter')
            ->latest()
            ->get();
    }

    #[On('invitation-sent')]
    public function refreshInvitations()
    {
        unset($this->pendingInvitations);
    }
}
?>
<div wire:key="pending-invitations-{{ $team->id }}">
    @if ($this->pendingInvitations->isNotEmpty())
            <h4 class="text-md font-medium mb-4">{{ __('Pending Invitations') }}</h4>

            <flux:table>
                <flux:table.columns>
                    <flux:table.column>{{ __('Email') }}</flux:table.column>
                    <flux:table.column>{{ __('Role') }}</flux:table.column>
                    <flux:table.column>{{ __('Invited By') }}</flux:table.column>
                    <flux:table.column>{{ __('Invited On') }}</flux:table.column>
                    <flux:table.column class="text-right">{{ __('Actions') }}</flux:table.column>
                </flux:table.columns>
                @foreach ($this->pendingInvitations as $invitation)
                    <flux:table.row>
                        <flux:table.cell class="font-medium">
                            {{ $invitation->email }}
                        </flux:table.cell>
                        <flux:table.cell>
                            <flux:badge color="blue">
                                {{ ucfirst($invitation->role) }}
                            </flux:badge>
                        </flux:table.cell>
                        <flux:table.cell>
                            {{ $invitation->inviter->name }}
                        </flux:table.cell>
                        <flux:table.cell>
                            {{ $invitation->created_at->format('M j, Y') }}
                        </flux:table.cell>
                        <flux:table.cell class="text-right">
                            @can('cancelTeamInvitation', $team)
                                <flux:button
                                    variant="danger"
                                    size="xs"
                                    wire:click="cancelInvitation('{{ $invitation->id }}')"
                                    wire:confirm="{{ __('Are you sure you want to cancel this invitation?') }}"
                                >
                                    {{ __('Cancel') }}
                                </flux:button>
                            @endcan
                        </flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table>
    @endif
</div>