<div class="max-w-2xl">
    <flux:button variant="ghost" icon="arrow-left" :href="route('rental-objects.show', $rentalObject)" wire:navigate class="mb-4">
        {{ __('Back to Property') }}
    </flux:button>

    <flux:heading size="xl" class="mb-2">{{ __('Add Flat') }}</flux:heading>
    <flux:text class="mb-6">{{ $rentalObject->fullAddress() }}</flux:text>

    <flux:card>
        <form wire:submit="save" class="space-y-6">
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <flux:field>
                    <flux:label>{{ __('Flat Number') }}</flux:label>
                    <flux:input wire:model="number" required placeholder="e.g. 1A" />
                    <flux:error name="number" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Floor') }}</flux:label>
                    <flux:input wire:model="floor" type="number" required />
                    <flux:error name="floor" />
                </flux:field>
            </div>

            <flux:field>
                <flux:label>{{ __('Size (m²)') }}</flux:label>
                <flux:input wire:model="size_sqm" type="number" step="0.01" required />
                <flux:error name="size_sqm" />
            </flux:field>

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <flux:field>
                    <flux:label>{{ __('Cold Rent (€)') }}</flux:label>
                    <flux:input wire:model="rent_cold" type="number" step="0.01" required />
                    <flux:error name="rent_cold" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Utility Cost (€)') }}</flux:label>
                    <flux:input wire:model="utility_cost" type="number" step="0.01" required />
                    <flux:error name="utility_cost" />
                </flux:field>
            </div>

            <flux:field>
                <flux:label>{{ __('Description') }}</flux:label>
                <flux:textarea wire:model="description" rows="3" />
                <flux:error name="description" />
            </flux:field>

            <flux:field>
                <flux:checkbox wire:model="is_wheelchair_accessible" :label="__('Wheelchair Accessible')" />
                <flux:error name="is_wheelchair_accessible" />
            </flux:field>

            <div class="flex items-center justify-end gap-4">
                <flux:button variant="ghost" :href="route('rental-objects.show', $rentalObject)" wire:navigate>
                    {{ __('Cancel') }}
                </flux:button>
                <flux:button variant="primary" type="submit">
                    {{ __('Create Flat') }}
                </flux:button>
            </div>
        </form>
    </flux:card>
</div>
