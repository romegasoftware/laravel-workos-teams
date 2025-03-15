<?php

use App\Models\Team;
use App\Models\TeamInvitation;
use App\Models\User;
use Flux\Flux;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;

new class extends Component
{
    use AuthorizesRequests;

    public $team;

    public string $email = '';

    public string $role = 'member';

    protected $rules = [
        'email' => 'required|email',
        'role' => 'required|in:admin,member',
    ];

    public function inviteMember()
    {
        $this->validate();

        // Check if the user can invite team members
        $this->authorize('inviteTeamMember', $this->team);

        // Check if the email is already a team member
        $emailExists = $this->team->members()
            ->where('email', $this->email)
            ->exists();

        if ($emailExists) {
            Flux::toast(
                heading: 'Already a Member',
                text: 'This user is already a member of the team.',
                variant: 'danger',
            );

            return;
        }

        // Check if there's already a pending invitation for this email
        $existingInvitation = $this->team->invitations()
            ->where('email', $this->email)
            ->first();

        if ($existingInvitation) {
            Flux::toast(
                heading: 'Invitation Exists',
                text: 'An invitation has already been sent to this email address.',
                variant: 'danger',
            );

            return;
        }

        // Create local invitation
        TeamInvitation::create([
            'team_id' => $this->team->id,
            'email' => $this->email,
            'role' => $this->role,
            'invited_by' => Auth::id(),
        ]);

        // Reset the form
        $this->reset('email', 'role');

        Flux::toast(
            heading: 'Invitation Sent',
            text: 'Invitation sent successfully!',
            variant: 'success',
        );

        // Dispatch events to refresh the UI
        $this->dispatch('invitation-sent');
    }
}
?>
<div wire:key="invite-team-member-{{ $team->id }}">
    <form
        class="space-y-6"
        wire:submit="inviteMember"
    >
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
            <flux:field>
                <flux:label>{{ __('Email') }}</flux:label>
                <flux:input
                    type="email"
                    required
                    wire:model="email"
                />
                <flux:error name="email" />
            </flux:field>

            <flux:field>
                <flux:label>{{ __('Role') }}</flux:label>
                <flux:select
                    wire:model="role"
                >
                    <flux:select.option value="admin">{{ __('Admin') }}</flux:select.option>
                    <flux:select.option value="member">{{ __('Member') }}</flux:select.option>
                </flux:select>
                <flux:error name="role" />
            </flux:field>
        </div>

        <div class="flex items-center justify-end">
            <flux:button type="submit">
                {{ __('Send Invitation') }}
            </flux:button>
        </div>
    </form>
</div>
