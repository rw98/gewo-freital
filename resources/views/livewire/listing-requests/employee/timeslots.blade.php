<div class="space-y-6">
    {{-- Flash Messages --}}
    @if (session('success'))
        <flux:callout variant="success" icon="check-circle" dismissible>
            {{ session('success') }}
        </flux:callout>
    @endif

    @if (session('error'))
        <flux:callout variant="danger" icon="exclamation-circle" dismissible>
            {{ session('error') }}
        </flux:callout>
    @endif

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <flux:button href="{{ route('listing-requests.index') }}" variant="ghost" size="sm" icon="arrow-left" class="mb-2" wire:navigate>
                {{ __('listing_requests.all_requests') }}
            </flux:button>
            <flux:heading size="xl">{{ __('listing_requests.manage_timeslots') }}</flux:heading>
            <flux:text class="text-gewo-grey-500">{{ $listing->title }}</flux:text>
        </div>
        <flux:button wire:click="openCreateModal" variant="primary" icon="plus">
            {{ __('listing_requests.create_timeslot') }}
        </flux:button>
    </div>

    {{-- Timeslots --}}
    <flux:card class="p-0">
        @if ($this->timeslots->isEmpty())
            <div class="text-center py-12">
                <flux:icon name="calendar" class="size-12 mx-auto text-gewo-grey-300 mb-3" />
                <flux:heading size="lg">{{ __('listing_requests.no_timeslots') }}</flux:heading>
                <flux:text class="text-gewo-grey-500">{{ __('Create timeslots for appointment scheduling.') }}</flux:text>
            </div>
        @else
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>{{ __('listing_requests.starts_at') }}</flux:table.column>
                    <flux:table.column>{{ __('listing_requests.ends_at') }}</flux:table.column>
                    <flux:table.column>{{ __('listing_requests.location') }}</flux:table.column>
                    <flux:table.column>{{ __('listing_requests.max_attendees') }}</flux:table.column>
                    <flux:table.column>{{ __('listing_requests.booked') }}</flux:table.column>
                    <flux:table.column>{{ __('Status') }}</flux:table.column>
                    <flux:table.column></flux:table.column>
                </flux:table.columns>
                <flux:table.rows>
                    @foreach ($this->timeslots as $timeslot)
                        <flux:table.row :key="$timeslot->id" class="{{ $timeslot->starts_at->isPast() ? 'opacity-50' : '' }}">
                            <flux:table.cell>
                                <flux:text class="font-medium">{{ $timeslot->starts_at->format('d.m.Y') }}</flux:text>
                                <flux:text size="sm" class="text-gewo-grey-500">{{ $timeslot->starts_at->format('H:i') }}</flux:text>
                            </flux:table.cell>
                            <flux:table.cell>
                                <flux:text size="sm">{{ $timeslot->ends_at->format('H:i') }}</flux:text>
                            </flux:table.cell>
                            <flux:table.cell>
                                <flux:text size="sm">{{ $timeslot->location ?? '-' }}</flux:text>
                            </flux:table.cell>
                            <flux:table.cell>
                                <flux:text size="sm">{{ $timeslot->max_attendees }}</flux:text>
                            </flux:table.cell>
                            <flux:table.cell>
                                @php
                                    $booked = $timeslot->appointments->where('status', '!=', 'cancelled')->count();
                                @endphp
                                <flux:badge size="sm" color="{{ $booked >= $timeslot->max_attendees ? 'red' : ($booked > 0 ? 'amber' : 'green') }}">
                                    {{ $booked }} / {{ $timeslot->max_attendees }}
                                </flux:badge>
                            </flux:table.cell>
                            <flux:table.cell>
                                @if ($timeslot->starts_at->isPast())
                                    <flux:badge size="sm" color="zinc">{{ __('Past') }}</flux:badge>
                                @elseif ($timeslot->is_active)
                                    <flux:badge size="sm" color="green">{{ __('listing_requests.active') }}</flux:badge>
                                @else
                                    <flux:badge size="sm" color="zinc">{{ __('listing_requests.inactive') }}</flux:badge>
                                @endif
                            </flux:table.cell>
                            <flux:table.cell>
                                <div class="flex items-center gap-1">
                                    @if (!$timeslot->starts_at->isPast())
                                        <flux:button
                                            wire:click="toggleActive('{{ $timeslot->id }}')"
                                            variant="ghost"
                                            size="sm"
                                            icon="{{ $timeslot->is_active ? 'eye-slash' : 'eye' }}"
                                        />
                                    @endif
                                    <flux:button
                                        wire:click="delete('{{ $timeslot->id }}')"
                                        wire:confirm="{{ __('Are you sure you want to delete this timeslot?') }}"
                                        variant="ghost"
                                        size="sm"
                                        icon="trash"
                                    />
                                </div>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforeach
                </flux:table.rows>
            </flux:table>
        @endif
    </flux:card>

    {{-- Create Modal --}}
    <flux:modal wire:model="showCreateModal" class="max-w-md">
        <form wire:submit="create" class="space-y-4">
            <flux:heading size="lg">{{ __('listing_requests.create_timeslot') }}</flux:heading>

            <div class="grid grid-cols-2 gap-4">
                <flux:field>
                    <flux:label>{{ __('listing_requests.starts_at') }} *</flux:label>
                    <flux:input type="datetime-local" wire:model="starts_at" />
                    <flux:error name="starts_at" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('listing_requests.ends_at') }} *</flux:label>
                    <flux:input type="datetime-local" wire:model="ends_at" />
                    <flux:error name="ends_at" />
                </flux:field>
            </div>

            <flux:field>
                <flux:label>{{ __('listing_requests.max_attendees') }} *</flux:label>
                <flux:input type="number" wire:model="max_attendees" min="1" max="20" />
                <flux:error name="max_attendees" />
            </flux:field>

            <flux:field>
                <flux:label>{{ __('listing_requests.location') }}</flux:label>
                <flux:input wire:model="location" placeholder="{{ $listing->fullAddress() }}" />
                <flux:error name="location" />
            </flux:field>

            <flux:field>
                <flux:label>{{ __('listing_requests.notes') }}</flux:label>
                <flux:textarea wire:model="notes" rows="2" />
                <flux:error name="notes" />
            </flux:field>

            <div class="flex justify-end gap-2">
                <flux:button wire:click="$set('showCreateModal', false)" variant="ghost" type="button">
                    {{ __('Cancel') }}
                </flux:button>
                <flux:button type="submit" variant="primary">
                    {{ __('listing_requests.create_timeslot') }}
                </flux:button>
            </div>
        </form>
    </flux:modal>
</div>
