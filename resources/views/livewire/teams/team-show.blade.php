<x-page-layout
    :hasActions="true"
    :hasBackButton="true"
    backRoute="{{ route('teams.index') }}"
    subtitle="{{ __('Team details and management') }}"
    title="{{ $team->name }}"
>
    <x-slot name="actions">
        @can('update', $team)
            <div class="flex items-center space-x-2">
                <flux:button
                    href="{{ route('teams.edit', $team) }}"
                    tag="a"
                    variant="outline"
                >
                    {{ __('Edit') }}
                </flux:button>
            </div>
        @endcan
    </x-slot>

    @can('inviteTeamMember', $team)
        <flux:card class="mx-auto mb-12 max-w-4xl">
            <div class="space-y-2">
                <flux:heading size="lg">{{ __('Invite Team Member') }}</flux:heading>
                <livewire:teams.invite-team-member :team="$team" />
                <livewire:teams.pending-invitations :team="$team" />
            </div>
        </flux:card>
    @endcan
    <flux:card class="mb-12">
        <flux:heading size="lg">{{ __('Team Members') }}</flux:heading>
        <livewire:teams.team-members :team="$team" />
    </flux:card>
</x-page-layout>
