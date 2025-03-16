<x-page-layout
    :hasBackButton="true"
    backRoute="{{ route('teams.show', $team) }}"
    subtitle="{{ __('Update team information') }}"
    title="{{ __('Edit Team') }}"
>
    <flux:card class="mx-auto max-w-2xl">
        <form
            class="space-y-6"
            wire:submit="save"
        >
            <flux:input
                label="{{ __('Team Name') }}"
                placeholder="{{ __('Enter team name') }}"
                required
                wire:model="name"
            />

            <flux:textarea
                label="{{ __('Description') }}"
                placeholder="{{ __('Enter team description') }}"
                rows="3"
                wire:model="description"
            />

            @can('delete', $team)
                <div class="border-t border-gray-200 pt-6 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ __('Delete Team') }}</h3>
                    <div class="mt-4">
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            {{ __('Once a team is deleted, all of its resources and data will be permanently deleted. Before deleting this team, please download any data or information that you wish to retain.') }}
                        </p>
                        <div class="mt-4">
                            <flux:button
                                type="button"
                                variant="danger"
                                wire:click="deleteTeam"
                            >
                                {{ __('Delete Team') }}
                            </flux:button>
                        </div>
                    </div>
                </div>
            @endcan


            <div class="flex justify-end space-x-2 pt-4">
                <flux:button
                    href="{{ route('teams.show', $team) }}"
                    tag="a"
                    variant="outline"
                >
                    {{ __('Cancel') }}
                </flux:button>
                <flux:button type="submit">
                    {{ __('Update Team') }}
                </flux:button>
            </div>
        </form>
    </flux:card>
</x-page-layout>
