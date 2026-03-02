<div class="space-y-6">
    <div class="flex items-center justify-between">
        <flux:heading size="xl">{{ __('All Flats') }}</flux:heading>
    </div>

    <div class="flex items-center gap-4">
        <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass" :placeholder="__('Search flats...')" class="max-w-sm" />
    </div>

    @if ($flats->isEmpty())
        <flux:card class="text-center py-12">
            <flux:icon name="home" class="mx-auto size-12 text-zinc-400" />
            <flux:heading size="lg" class="mt-4">{{ __('No flats yet') }}</flux:heading>
            <flux:text class="mt-2">{{ __('Flats will appear here once you add them to your properties.') }}</flux:text>
            <flux:button variant="primary" icon="building-office-2" :href="route('rental-objects.index')" wire:navigate class="mt-4">
                {{ __('View Properties') }}
            </flux:button>
        </flux:card>
    @else
        <flux:table>
            <flux:table.columns>
                <flux:table.column>{{ __('Flat') }}</flux:table.column>
                <flux:table.column>{{ __('Property') }}</flux:table.column>
                <flux:table.column>{{ __('Floor') }}</flux:table.column>
                <flux:table.column>{{ __('Size') }}</flux:table.column>
                <flux:table.column>{{ __('Rent') }}</flux:table.column>
                <flux:table.column></flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @foreach ($flats as $flat)
                    <flux:table.row wire:key="flat-{{ $flat->id }}">
                        <flux:table.cell variant="strong">{{ $flat->number }}</flux:table.cell>
                        <flux:table.cell>
                            {{ $flat->rentalObject->street }} {{ $flat->rentalObject->number }}, {{ $flat->rentalObject->city }}
                        </flux:table.cell>
                        <flux:table.cell>{{ $flat->floor }}</flux:table.cell>
                        <flux:table.cell>{{ number_format($flat->size_sqm, 2) }} m²</flux:table.cell>
                        <flux:table.cell>{{ number_format($flat->totalRent(), 2) }} €</flux:table.cell>
                        <flux:table.cell>
                            <flux:button variant="ghost" size="sm" icon="eye" :href="route('flats.show', $flat)" wire:navigate />
                        </flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>

        <div class="mt-4">
            {{ $flats->links() }}
        </div>
    @endif
</div>
