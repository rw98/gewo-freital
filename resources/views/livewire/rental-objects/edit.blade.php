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

            {{-- Energy Certificate Section --}}
            <div class="border-t border-gewo-grey-200 pt-6 mt-6">
                <flux:heading size="lg" class="mb-4">{{ __('Energy Certificate') }}</flux:heading>

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <flux:field>
                        <flux:label>{{ __('Certificate Type') }}</flux:label>
                        <flux:select wire:model="energy_certificate_type">
                            <flux:select.option value="">{{ __('Select...') }}</flux:select.option>
                            @foreach ($this->energyCertificateTypes as $type)
                                <flux:select.option value="{{ $type->value }}">{{ $type->label() }}</flux:select.option>
                            @endforeach
                        </flux:select>
                        <flux:error name="energy_certificate_type" />
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ __('Energy Consumption') }} (kWh/m²a)</flux:label>
                        <flux:input wire:model="energy_consumption_kwh" type="number" step="0.01" min="0" max="999.99" placeholder="z.B. 125.50" />
                        <flux:error name="energy_consumption_kwh" />
                    </flux:field>
                </div>

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 mt-6">
                    <flux:field>
                        <flux:label>{{ __('Energy Source') }}</flux:label>
                        <flux:select wire:model="energy_source">
                            <flux:select.option value="">{{ __('Select...') }}</flux:select.option>
                            @foreach ($this->energySources as $source)
                                <flux:select.option value="{{ $source->value }}">{{ $source->label() }}</flux:select.option>
                            @endforeach
                        </flux:select>
                        <flux:error name="energy_source" />
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ __('Valid Until') }}</flux:label>
                        <flux:input wire:model="energy_certificate_valid_until" type="date" />
                        <flux:error name="energy_certificate_valid_until" />
                    </flux:field>
                </div>

                {{-- Energy Label Preview --}}
                @if ($energy_consumption_kwh)
                    <div class="mt-6 p-4 bg-gewo-grey-50 rounded-lg">
                        <flux:text size="sm" class="text-gewo-grey-600 mb-3">{{ __('Preview') }}</flux:text>
                        <x-energy-label :kwh="$energy_consumption_kwh" size="sm" />
                    </div>
                @endif
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
