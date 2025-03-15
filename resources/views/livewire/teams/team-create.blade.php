<x-page-layout
    title="{{ __('Create Team') }}"
    subtitle="{{ __('Create a new team to collaborate with others.') }}"
    :hasBackButton="true"
    backRoute="{{ route('teams.index') }}"
>
    <flux:card>
        <form wire:submit="save" class="space-y-6">
            <flux:input
                label="{{ __('Team Name') }}"
                id="name"
                wire:model="name"
                placeholder="{{ __('Enter team name') }}"
                required
            />

            <flux:textarea
                label="{{ __('Description') }}"
                id="description"
                wire:model="description"
                placeholder="{{ __('Enter team description (optional)') }}"
                rows="3"
            />

            <div class="flex justify-end space-x-2 pt-4">
                <flux:button
                    tag="a"
                    href="{{ route('teams.index') }}"
                    variant="outline"
                >
                    {{ __('Cancel') }}
                </flux:button>
                <flux:button
                    type="submit"
                >
                    {{ __('Create Team') }}
                </flux:button>
            </div>
        </form>
    </flux:card>
</x-page-layout>