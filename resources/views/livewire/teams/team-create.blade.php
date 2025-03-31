<x-page-layout
    :hasBackButton="true"
    backRoute="{{ route('teams.index') }}"
    subtitle="{{ __('Create a new team to collaborate with others.') }}"
    title="{{ __('Create Team') }}"
>
    <flux:card>
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

            <div class="flex justify-end space-x-2 pt-4">
                <flux:button
                    href="{{ route('teams.index') }}"
                    tag="a"
                    variant="outline"
                >
                    {{ __('Cancel') }}
                </flux:button>
                <flux:button type="submit">
                    {{ __('Create Team') }}
                </flux:button>
            </div>
        </form>
    </flux:card>
</x-page-layout>
