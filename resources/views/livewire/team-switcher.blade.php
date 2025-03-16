<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\Auth;
use RomegaSoftware\WorkOSTeams\Contracts\ExternalId;
use RomegaSoftware\WorkOSTeams\Contracts\TeamContract;

new class extends Component {
    /**
     * Switch to the given team.
     */
    public function switchTeam(TeamContract&ExternalId $team)
    {
        $user = Auth::user();

        // Use the User model's switchTeam method which handles WorkOS authentication
        $user->switchTeam($team);

        // Refresh the page to apply the team scope
        return $this->redirect(request()->header('Referer'));
    }

    #[Computed]
    public function teams()
    {
        $user = Auth::user();

        return $user->allTeams();
    }

    #[Computed]
    public function currentTeam()
    {
        $user = Auth::user();

        return $user->currentTeam;
    }
}; ?>


<div>
    @if ($this->teams->isEmpty())
        <flux:menu.item disabled>
            <span class="text-sm text-gray-500 dark:text-gray-400">
                {{ __('No teams available') }}
            </span>
        </flux:menu.item>
    @else
        @foreach ($this->teams as $team)
            <flux:menu.item
                :active="{{ $this->currentTeam && $this->currentTeam->getKey() === $team->getKey() }}"
                wire:click="switchTeam({{ $team->getKey() }})"
            >
                <div class="flex items-center">
                    <div
                        class="mr-2 flex h-6 w-6 flex-shrink-0 items-center justify-center rounded-sm bg-zinc-200 dark:bg-zinc-700">
                        <span class="text-xs">{{ Str::substr($team->name, 0, 1) }}</span>
                    </div>
                    <span class="flex-1">{{ $team->name }}</span>
                    @if ($this->currentTeam && $this->currentTeam->getKey() === $team->getKey())
                        <flux:icon
                            class="ml-2 h-4 w-4 text-green-500"
                            name="check"
                        />
                    @endif
                </div>
            </flux:menu.item>
        @endforeach

        <flux:menu.item
            href="{{ route('teams.index') }}"
            tag="a"
        >
            <div class="flex items-center">
                <flux:icon
                    class="mr-2 h-4 w-4"
                    name="users"
                />
                <span>{{ __('Manage Teams') }}</span>
            </div>
        </flux:menu.item>
    @endif
</div>
