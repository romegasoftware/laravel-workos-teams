<?php

use Flux\Flux;
use Livewire\Volt\Component;
use Illuminate\Support\Facades\Auth;
use RomegaSoftware\WorkOSTeams\Contracts\ExternalId;
use RomegaSoftware\WorkOSTeams\Models\TeamInvitation;
use RomegaSoftware\WorkOSTeams\Contracts\TeamContract;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

new class extends Component {
    use AuthorizesRequests;

    public TeamContract&ExternalId $team;

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
        $emailExists = $this->team->members()->where('email', $this->email)->exists();

        if ($emailExists) {
            Flux::toast(heading: __('Already a Member'), text: __('This user is already a member of the team.'), variant: 'danger');

            return;
        }

        // Check if there's already a pending invitation for this email
        $existingInvitation = $this->team->invitations()->where('email', $this->email)->first();

        if ($existingInvitation) {
            Flux::toast(heading: __('Invitation Exists'), text: __('An invitation has already been sent to this email address.'), variant: 'danger');

            return;
        }

        // Create local invitation
        config('workos-teams.models.team_invitation', TeamInvitation::class)::create([
            'team_id' => $this->team->getKey(),
            'email' => $this->email,
            'role' => $this->role,
            'invited_by' => Auth::id(),
        ]);

        // Reset the form
        $this->reset('email', 'role');

        Flux::toast(heading: __('Invitation Sent'), text: __('Invitation sent successfully!'), variant: 'success');

        // Dispatch events to refresh the UI
        $this->dispatch('invitation-sent');
    }
};
?>
<div wire:key="invite-team-member-{{ $team->getKey() }}">
    <form
        class="space-y-6"
        wire:submit="inviteMember"
    >
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
            <flux:input
                :label="__('Email')"
                required
                type="email"
                wire:model="email"
            />

            <flux:field>
                <flux:label>{{ __('Role') }}</flux:label>
                <flux:select wire:model="role">
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
