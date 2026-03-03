<div class="min-h-screen bg-gewo-grey-50 py-8">
    <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
        {{-- Header --}}
        <div class="mb-6">
            <flux:button
                href="{{ route('listing-requests.portal', $listingRequest->access_token) }}"
                variant="ghost"
                icon="arrow-left"
                size="sm"
                wire:navigate
            >
                {{ __('listing_requests.back_to_request') }}
            </flux:button>
        </div>

        <flux:card>
            <div class="mb-6">
                <flux:heading size="xl">{{ __('listing_requests.self_disclosure.title') }}</flux:heading>
                <flux:text class="mt-2 text-gewo-grey-600">
                    {{ __('listing_requests.self_disclosure.description') }}
                </flux:text>
                <flux:text size="sm" class="mt-2 text-gewo-grey-500">
                    {{ __('listing_requests.self_disclosure.for_listing', ['title' => $listingRequest->listing->title]) }}
                </flux:text>
            </div>

            @if (session('success'))
                <flux:callout variant="success" icon="check-circle" class="mb-6" dismissible>
                    {{ session('success') }}
                </flux:callout>
            @endif

            <form wire:submit="save" class="space-y-8">
                {{-- Household / Tenants --}}
                <div class="space-y-4">
                    <flux:heading size="lg" class="border-b border-gewo-grey-200 pb-2">
                        {{ __('listing_requests.self_disclosure.household') }}
                    </flux:heading>

                    <flux:text size="sm" class="text-gewo-grey-600">
                        {{ __('listing_requests.self_disclosure.tenants_description') }}
                    </flux:text>

                    {{-- Tenants List --}}
                    <div class="space-y-4">
                        @foreach ($tenants as $index => $tenant)
                            <div wire:key="tenant-{{ $index }}" class="rounded-lg border border-gewo-grey-200 bg-gewo-grey-50 p-4">
                                <div class="flex items-center justify-between mb-4">
                                    <flux:heading size="sm">
                                        {{ __('listing_requests.self_disclosure.tenant_number', ['number' => $index + 1]) }}
                                    </flux:heading>
                                    @if (count($tenants) > 1)
                                        <flux:button
                                            variant="ghost"
                                            size="xs"
                                            icon="trash"
                                            wire:click="removeTenant({{ $index }})"
                                        />
                                    @endif
                                </div>

                                <div class="space-y-4">
                                    {{-- Pays Rent Toggle --}}
                                    <flux:field>
                                        <flux:label>{{ __('listing_requests.self_disclosure.pays_rent') }} *</flux:label>
                                        <flux:select wire:model.live="tenants.{{ $index }}.pays_rent">
                                            <flux:select.option value="1">{{ __('listing_requests.self_disclosure.pays_rent_yes') }}</flux:select.option>
                                            <flux:select.option value="0">{{ __('listing_requests.self_disclosure.pays_rent_no') }}</flux:select.option>
                                        </flux:select>
                                        <flux:description>
                                            {{ $tenant['pays_rent'] ? __('listing_requests.self_disclosure.pays_rent_yes_hint') : __('listing_requests.self_disclosure.pays_rent_no_hint') }}
                                        </flux:description>
                                        <flux:error name="tenants.{{ $index }}.pays_rent" />
                                    </flux:field>

                                    {{-- Basic Info (required for all) --}}
                                    <div class="grid sm:grid-cols-2 gap-4">
                                        <flux:field>
                                            <flux:label>{{ __('listing_requests.self_disclosure.tenant_first_name') }} *</flux:label>
                                            <flux:input wire:model="tenants.{{ $index }}.first_name" />
                                            <flux:error name="tenants.{{ $index }}.first_name" />
                                        </flux:field>

                                        <flux:field>
                                            <flux:label>{{ __('listing_requests.self_disclosure.tenant_last_name') }} *</flux:label>
                                            <flux:input wire:model="tenants.{{ $index }}.last_name" />
                                            <flux:error name="tenants.{{ $index }}.last_name" />
                                        </flux:field>
                                    </div>

                                    <div class="grid sm:grid-cols-2 gap-4">
                                        <flux:field>
                                            <flux:label>{{ __('listing_requests.self_disclosure.tenant_date_of_birth') }} *</flux:label>
                                            <flux:input type="date" wire:model="tenants.{{ $index }}.date_of_birth" />
                                            <flux:error name="tenants.{{ $index }}.date_of_birth" />
                                        </flux:field>

                                        <flux:field>
                                            <flux:label>{{ __('listing_requests.self_disclosure.tenant_relationship') }}</flux:label>
                                            <flux:input wire:model="tenants.{{ $index }}.relationship" placeholder="{{ __('listing_requests.self_disclosure.tenant_relationship_hint') }}" />
                                            <flux:error name="tenants.{{ $index }}.relationship" />
                                        </flux:field>
                                    </div>

                                    {{-- Additional Info (required only for paying tenants) --}}
                                    @if ($tenant['pays_rent'])
                                        <div class="space-y-4 pt-2 border-t border-gewo-grey-200">
                                            {{-- Contact Info --}}
                                            <div class="grid sm:grid-cols-2 gap-4">
                                                <flux:field>
                                                    <flux:label>{{ __('listing_requests.self_disclosure.tenant_email') }} *</flux:label>
                                                    <flux:input type="email" wire:model="tenants.{{ $index }}.email" />
                                                    <flux:error name="tenants.{{ $index }}.email" />
                                                </flux:field>

                                                <flux:field>
                                                    <flux:label>{{ __('listing_requests.self_disclosure.tenant_phone') }}</flux:label>
                                                    <flux:input type="tel" wire:model="tenants.{{ $index }}.phone" />
                                                    <flux:error name="tenants.{{ $index }}.phone" />
                                                </flux:field>
                                            </div>

                                            {{-- Employment Info --}}
                                            <div class="grid sm:grid-cols-2 gap-4">
                                                <flux:field>
                                                    <flux:label>{{ __('listing_requests.self_disclosure.tenant_employment_status') }} *</flux:label>
                                                    <flux:select wire:model="tenants.{{ $index }}.employment_status">
                                                        <flux:select.option value="">{{ __('Select...') }}</flux:select.option>
                                                        @foreach ($this->employmentStatuses as $status)
                                                            <flux:select.option value="{{ $status->value }}">{{ $status->label() }}</flux:select.option>
                                                        @endforeach
                                                    </flux:select>
                                                    <flux:error name="tenants.{{ $index }}.employment_status" />
                                                </flux:field>

                                                <flux:field>
                                                    <flux:label>{{ __('listing_requests.self_disclosure.tenant_monthly_net_income') }} *</flux:label>
                                                    <flux:input type="number" wire:model="tenants.{{ $index }}.monthly_net_income" step="0.01" min="0" suffix="€" />
                                                    <flux:error name="tenants.{{ $index }}.monthly_net_income" />
                                                </flux:field>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Add Tenant Buttons --}}
                    <div class="flex flex-wrap gap-2">
                        <flux:button variant="ghost" icon="plus" size="sm" wire:click="addTenant(true)">
                            {{ __('listing_requests.self_disclosure.add_paying_tenant') }}
                        </flux:button>
                        <flux:button variant="ghost" icon="plus" size="sm" wire:click="addTenant(false)">
                            {{ __('listing_requests.self_disclosure.add_occupant') }}
                        </flux:button>
                    </div>

                    <flux:field class="mt-4">
                        <flux:label>{{ __('listing_requests.self_disclosure.desired_move_in_date') }} *</flux:label>
                        <flux:input type="date" wire:model="desired_move_in_date" />
                        <flux:error name="desired_move_in_date" />
                    </flux:field>

                    <div class="grid sm:grid-cols-2 gap-4">
                        <flux:field>
                            <flux:label>{{ __('listing_requests.self_disclosure.has_pets') }} *</flux:label>
                            <flux:select wire:model.live="has_pets">
                                <flux:select.option value="">{{ __('Select...') }}</flux:select.option>
                                <flux:select.option value="1">{{ __('Yes') }}</flux:select.option>
                                <flux:select.option value="0">{{ __('No') }}</flux:select.option>
                            </flux:select>
                            <flux:error name="has_pets" />
                        </flux:field>

                        <flux:field>
                            <flux:label>{{ __('listing_requests.self_disclosure.is_smoker') }} *</flux:label>
                            <flux:select wire:model="is_smoker">
                                <flux:select.option value="">{{ __('Select...') }}</flux:select.option>
                                <flux:select.option value="1">{{ __('Yes') }}</flux:select.option>
                                <flux:select.option value="0">{{ __('No') }}</flux:select.option>
                            </flux:select>
                            <flux:error name="is_smoker" />
                        </flux:field>
                    </div>

                    @if ($has_pets)
                        <flux:field>
                            <flux:label>{{ __('listing_requests.self_disclosure.pets_details') }} *</flux:label>
                            <flux:input wire:model="pets_details" placeholder="{{ __('listing_requests.self_disclosure.pets_details_hint') }}" />
                            <flux:error name="pets_details" />
                        </flux:field>
                    @endif
                </div>

                {{-- Current Tenancy --}}
                <div class="space-y-4">
                    <flux:heading size="lg" class="border-b border-gewo-grey-200 pb-2">
                        {{ __('listing_requests.self_disclosure.current_tenancy') }}
                    </flux:heading>

                    <flux:field>
                        <flux:label>{{ __('listing_requests.self_disclosure.reason_for_moving') }} *</flux:label>
                        <flux:textarea wire:model="reason_for_moving" rows="3" />
                        <flux:error name="reason_for_moving" />
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ __('listing_requests.self_disclosure.current_landlord_name') }}</flux:label>
                        <flux:input wire:model="current_landlord_name" />
                        <flux:description>{{ __('listing_requests.self_disclosure.current_landlord_hint') }}</flux:description>
                        <flux:error name="current_landlord_name" />
                    </flux:field>

                    <div class="grid sm:grid-cols-2 gap-4">
                        <flux:field>
                            <flux:label>{{ __('listing_requests.self_disclosure.current_landlord_phone') }}</flux:label>
                            <flux:input type="tel" wire:model="current_landlord_phone" />
                            <flux:error name="current_landlord_phone" />
                        </flux:field>

                        <flux:field>
                            <flux:label>{{ __('listing_requests.self_disclosure.current_landlord_email') }}</flux:label>
                            <flux:input type="email" wire:model="current_landlord_email" />
                            <flux:error name="current_landlord_email" />
                        </flux:field>
                    </div>
                </div>

                {{-- Financial Declarations --}}
                <div class="space-y-4">
                    <flux:heading size="lg" class="border-b border-gewo-grey-200 pb-2">
                        {{ __('listing_requests.self_disclosure.financial_declarations') }}
                    </flux:heading>

                    <flux:callout variant="warning" icon="exclamation-triangle">
                        {{ __('listing_requests.self_disclosure.financial_warning') }}
                    </flux:callout>

                    <div class="space-y-3">
                        <flux:field>
                            <flux:label>{{ __('listing_requests.self_disclosure.has_insolvency') }} *</flux:label>
                            <flux:select wire:model="has_insolvency">
                                <flux:select.option value="">{{ __('Select...') }}</flux:select.option>
                                <flux:select.option value="0">{{ __('No') }}</flux:select.option>
                                <flux:select.option value="1">{{ __('Yes') }}</flux:select.option>
                            </flux:select>
                            <flux:error name="has_insolvency" />
                        </flux:field>

                        <flux:field>
                            <flux:label>{{ __('listing_requests.self_disclosure.has_eviction_history') }} *</flux:label>
                            <flux:select wire:model="has_eviction_history">
                                <flux:select.option value="">{{ __('Select...') }}</flux:select.option>
                                <flux:select.option value="0">{{ __('No') }}</flux:select.option>
                                <flux:select.option value="1">{{ __('Yes') }}</flux:select.option>
                            </flux:select>
                            <flux:error name="has_eviction_history" />
                        </flux:field>

                        <flux:field>
                            <flux:label>{{ __('listing_requests.self_disclosure.has_rental_debt') }} *</flux:label>
                            <flux:select wire:model="has_rental_debt">
                                <flux:select.option value="">{{ __('Select...') }}</flux:select.option>
                                <flux:select.option value="0">{{ __('No') }}</flux:select.option>
                                <flux:select.option value="1">{{ __('Yes') }}</flux:select.option>
                            </flux:select>
                            <flux:error name="has_rental_debt" />
                        </flux:field>
                    </div>
                </div>

                {{-- Additional Notes --}}
                <div class="space-y-4">
                    <flux:heading size="lg" class="border-b border-gewo-grey-200 pb-2">
                        {{ __('listing_requests.self_disclosure.additional_info') }}
                    </flux:heading>

                    <flux:field>
                        <flux:label>{{ __('listing_requests.self_disclosure.additional_notes') }}</flux:label>
                        <flux:textarea wire:model="additional_notes" rows="4" placeholder="{{ __('listing_requests.self_disclosure.additional_notes_hint') }}" />
                        <flux:error name="additional_notes" />
                    </flux:field>
                </div>

                {{-- Submit --}}
                <div class="border-t border-gewo-grey-200 pt-6 flex items-center justify-between">
                    <flux:text size="sm" class="text-gewo-grey-500">
                        * {{ __('listing_requests.self_disclosure.required_fields') }}
                    </flux:text>
                    <div class="flex items-center gap-3">
                        <flux:button
                            href="{{ route('listing-requests.portal', $listingRequest->access_token) }}"
                            variant="ghost"
                            wire:navigate
                        >
                            {{ __('Cancel') }}
                        </flux:button>
                        <flux:button type="submit" variant="primary" icon="check">
                            {{ __('listing_requests.self_disclosure.submit') }}
                        </flux:button>
                    </div>
                </div>
            </form>
        </flux:card>
    </div>
</div>
