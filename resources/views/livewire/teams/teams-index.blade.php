<x-page-layout
    :hasFilters="true"
    actionIcon="plus"
    actionLabel="{{ __('Create Team') }}"
    actionRoute="{{ route('teams.create') }}"
    subtitle="{{ __('Manage your teams and team memberships') }}"
    title="{{ __('Teams') }}"
>
    <x-slot name="filters">
        <x-filters
            :filterOptions="[
                'member' => __('Member only'),
            ]"
            filterDefaultLabel="{{ __('All Teams') }}"
            filterModel="filter"
            searchModel="search"
            searchPlaceholder="{{ __('Search teams...') }}"
        />
    </x-slot>

    @if ($this->team->isEmpty())
        <x-empty-state
            actionLabel="{{ __('Create Team') }}"
            actionRoute="{{ route('teams.create') }}"
            description="{{ __('Create a team to collaborate with others.') }}"
            message="{{ __('No teams found.') }}"
        />
    @else
        <flux:table :paginate="$this->team">
            <flux:table.columns>
                <flux:table.column>{{ __('Name') }}</flux:table.column>
                <flux:table.column>{{ __('Members') }}</flux:table.column>
                <flux:table.column>{{ __('Created') }}</flux:table.column>
                <flux:table.column align="right">{{ __('Actions') }}</flux:table.column>
            </flux:table.columns>

            @foreach ($this->team as $team)
                <flux:table.row wire:key="team-{{ $team->id }}">
                    <flux:table.cell class="font-medium">
                        @can('view', $team)
                            <a
                                class="hover:underline"
                                href="{{ route('teams.show', $team) }}"
                            >
                                {{ $team->name }}
                            </a>
                        @else
                            {{ $team->name }}
                        @endcan


                        @if ($team->description)
                            <div class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">
                                {{ Str::limit($team->description, 100) }}
                            </div>
                        @endif
                    </flux:table.cell>
                    <flux:table.cell>
                        {{ $team->members_count ?? $team->members()->count() }}
                    </flux:table.cell>
                    <flux:table.cell>
                        {{ $team->created_at->diffForHumans() }}
                    </flux:table.cell>
                    <flux:table.cell align="end">
                        @can('view', $team)
                            <flux:button
                                href="{{ route('teams.show', $team) }}"
                                size="xs"
                                tag="a"
                                variant="outline"
                            >
                                {{ __('View') }}
                            </flux:button>
                        @endcan

                        @can('update', $team)
                            <flux:button
                                href="{{ route('teams.edit', $team) }}"
                                size="xs"
                                tag="a"
                                variant="outline"
                            >
                                {{ __('Edit') }}
                            </flux:button>
                        @endcan

                        @can('delete', $team)
                            <flux:button
                                size="xs"
                                variant="danger"
                                wire:click="deleteTeam({{ $team->id }})"
                                wire:confirm="{{ __('Are you sure you want to delete this team?') }}"
                            >
                                {{ __('Delete') }}
                            </flux:button>
                        @endcan
                    </flux:table.cell>
                </flux:table.row>
            @endforeach
        </flux:table>
    @endif
</x-page-layout>
