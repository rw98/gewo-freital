<div class="space-y-6">
    <div class="flex items-center justify-between">
        <flux:heading size="xl">{{ __('Properties') }}</flux:heading>
        <flux:button variant="primary" icon="plus" :href="route('rental-objects.create')" wire:navigate>
            {{ __('Add Property') }}
        </flux:button>
    </div>

    <div class="flex items-center gap-4">
        <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass" :placeholder="__('Search properties...')" class="max-w-sm" />
    </div>

    @if ($rentalObjects->isEmpty())
        <flux:card class="text-center py-12">
            <flux:icon name="building-office-2" class="mx-auto size-12 text-zinc-400" />
            <flux:heading size="lg" class="mt-4">{{ __('No properties yet') }}</flux:heading>
            <flux:text class="mt-2">{{ __('Get started by adding your first property.') }}</flux:text>
            <flux:button variant="primary" icon="plus" :href="route('rental-objects.create')" wire:navigate class="mt-4">
                {{ __('Add Property') }}
            </flux:button>
        </flux:card>
    @else
        <flux:table>
            <flux:table.columns>
                <flux:table.column>{{ __('Address') }}</flux:table.column>
                <flux:table.column>{{ __('City') }}</flux:table.column>
                <flux:table.column>{{ __('Flats') }}</flux:table.column>
                <flux:table.column>{{ __('Elevator') }}</flux:table.column>
                <flux:table.column></flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @foreach ($rentalObjects as $rentalObject)
                    <flux:table.row wire:key="rental-object-{{ $rentalObject->id }}">
                        <flux:table.cell variant="strong">
                            {{ $rentalObject->street }} {{ $rentalObject->number }}
                        </flux:table.cell>
                        <flux:table.cell>
                            {{ $rentalObject->postal_code }} {{ $rentalObject->city }}
                        </flux:table.cell>
                        <flux:table.cell>
                            <flux:badge size="sm">{{ $rentalObject->flats_count }}</flux:badge>
                        </flux:table.cell>
                        <flux:table.cell>
                            @if ($rentalObject->has_elevator)
                                <flux:badge color="green" size="sm">{{ __('Yes') }}</flux:badge>
                            @else
                                <flux:badge color="zinc" size="sm">{{ __('No') }}</flux:badge>
                            @endif
                        </flux:table.cell>
                        <flux:table.cell>
                            <flux:button variant="ghost" size="sm" icon="eye" :href="route('rental-objects.show', $rentalObject)" wire:navigate />
                        </flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>

        <div class="mt-4">
            {{ $rentalObjects->links() }}
        </div>
    @endif
</div>
