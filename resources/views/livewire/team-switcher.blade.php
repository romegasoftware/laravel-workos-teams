<?php

use App\Models\Team;
use Livewire\Volt\Component;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\Auth;

new class extends Component {
    /**
     * Switch to the given team.
     */
    public function switchTeam(Team $team)
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
    @if($this->teams->isEmpty())
        <flux:menu.item disabled>
            <span class="text-sm text-gray-500 dark:text-gray-400">
                {{ __('No teams available') }}
            </span>
        </flux:menu.item>
    @else
        @foreach($this->teams as $team)
            <flux:menu.item
                wire:click="switchTeam({{ $team->id }})"
                :active="$this->currentTeam && $this->currentTeam->id === $team->id"
            >
                <div class="flex items-center">
                    <div class="flex-shrink-0 w-6 h-6 flex items-center justify-center bg-zinc-200 dark:bg-zinc-700 rounded-sm mr-2">
                        <span class="text-xs">{{ Str::substr($team->name, 0, 1) }}</span>
                    </div>
                    <span class="flex-1">{{ $team->name }}</span>
                    @if($this->currentTeam() && $this->currentTeam()->id === $team->id)
                        <flux:icon name="check" class="ml-2 h-4 w-4 text-green-500" />
                    @endif
                </div>
            </flux:menu.item>
        @endforeach

        <flux:menu.item tag="a" href="{{ route('teams.index') }}">
            <div class="flex items-center">
                <flux:icon name="users" class="mr-2 h-4 w-4" />
                <span>{{ __('Manage Teams') }}</span>
            </div>
        </flux:menu.item>
    @endif
</div>
