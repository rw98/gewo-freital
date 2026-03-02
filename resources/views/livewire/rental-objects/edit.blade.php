<div class="max-w-2xl">
    <flux:heading size="xl" class="mb-6">{{ __('Edit Property') }}</flux:heading>

    <flux:card>
        <form wire:submit="save" class="space-y-6">
            <flux:field>
                <flux:label>{{ __('Object Number') }}</flux:label>
                <flux:input wire:model="object_number" required />
                <flux:error name="object_number" />
            </flux:field>

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <flux:field class="sm:col-span-1">
                    <flux:label>{{ __('Street') }}</flux:label>
                    <flux:input wire:model="street" required />
                    <flux:error name="street" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Number') }}</flux:label>
                    <flux:input wire:model="number" required />
                    <flux:error name="number" />
                </flux:field>
            </div>

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <flux:field>
                    <flux:label>{{ __('Postal Code') }}</flux:label>
                    <flux:input wire:model="postal_code" required />
                    <flux:error name="postal_code" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('City') }}</flux:label>
                    <flux:input wire:model="city" required />
                    <flux:error name="city" />
                </flux:field>
            </div>

            <flux:field>
                <flux:label>{{ __('Country') }}</flux:label>
                <flux:select wire:model="country">
                    <flux:select.option value="DE">{{ __('Germany') }}</flux:select.option>
                    <flux:select.option value="AT">{{ __('Austria') }}</flux:select.option>
                    <flux:select.option value="CH">{{ __('Switzerland') }}</flux:select.option>
                </flux:select>
                <flux:error name="country" />
            </flux:field>

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <flux:field>
                    <flux:checkbox wire:model="has_elevator" :label="__('Has Elevator')" />
                    <flux:error name="has_elevator" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Year Built') }}</flux:label>
                    <flux:input wire:model="year_built" type="number" min="1800" :max="date('Y')" />
                    <flux:error name="year_built" />
                </flux:field>
            </div>

            <div class="flex items-center justify-end gap-4">
                <flux:button variant="ghost" :href="route('rental-objects.show', $rentalObject)" wire:navigate>
                    {{ __('Cancel') }}
                </flux:button>
                <flux:button variant="primary" type="submit">
                    {{ __('Save Changes') }}
                </flux:button>
            </div>
        </form>
    </flux:card>
</div>
