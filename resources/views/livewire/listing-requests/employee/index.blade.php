<div class="space-y-6">
    <div class="flex items-center justify-between">
        <flux:heading size="xl">{{ __('listing_requests.manage_requests') }}</flux:heading>
    </div>

    {{-- Filters --}}
    <flux:card>
        <div class="grid sm:grid-cols-4 gap-4">
            <flux:field>
                <flux:label>{{ __('Search') }}</flux:label>
                <flux:input wire:model.live.debounce.300ms="search" placeholder="{{ __('Name, Email...') }}" icon="magnifying-glass" />
            </flux:field>

            <flux:field>
                <flux:label>{{ __('listing_requests.filter_by_status') }}</flux:label>
                <flux:select wire:model.live="status">
                    <flux:select.option value="">{{ __('All') }}</flux:select.option>
                    @foreach ($this->statuses as $statusOption)
                        <flux:select.option value="{{ $statusOption->value }}">{{ $statusOption->label() }}</flux:select.option>
                    @endforeach
                </flux:select>
            </flux:field>

            <flux:field>
                <flux:label>{{ __('listing_requests.filter_by_listing') }}</flux:label>
                <flux:select wire:model.live="listing_id">
                    <flux:select.option value="">{{ __('All') }}</flux:select.option>
                    @foreach ($this->listings as $listing)
                        <flux:select.option value="{{ $listing->id }}">{{ $listing->title }}</flux:select.option>
                    @endforeach
                </flux:select>
            </flux:field>

            <div class="flex items-end">
                <flux:button wire:click="resetFilters" variant="ghost" icon="x-mark">
                    {{ __('Reset') }}
                </flux:button>
            </div>
        </div>
    </flux:card>

    {{-- Results --}}
    <flux:card class="p-0">
        @if ($this->requests->isEmpty())
            <div class="text-center py-12">
                <flux:icon name="inbox" class="size-12 mx-auto text-gewo-grey-300 mb-3" />
                <flux:heading size="lg">{{ __('listing_requests.no_requests') }}</flux:heading>
            </div>
        @else
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>{{ __('Requestee') }}</flux:table.column>
                    <flux:table.column>{{ __('Listing') }}</flux:table.column>
                    <flux:table.column>{{ __('Status') }}</flux:table.column>
                    <flux:table.column>{{ __('listing_requests.assigned_to') }}</flux:table.column>
                    <flux:table.column>{{ __('listing_requests.requested_at') }}</flux:table.column>
                    <flux:table.column></flux:table.column>
                </flux:table.columns>
                <flux:table.rows>
                    @foreach ($this->requests as $request)
                        <flux:table.row :key="$request->id">
                            <flux:table.cell>
                                <div>
                                    <flux:text class="font-medium">{{ $request->fullName() }}</flux:text>
                                    <flux:text size="sm" class="text-gewo-grey-500">{{ $request->email }}</flux:text>
                                </div>
                            </flux:table.cell>
                            <flux:table.cell>
                                <flux:text class="line-clamp-1">{{ $request->listing->title }}</flux:text>
                            </flux:table.cell>
                            <flux:table.cell>
                                <flux:badge color="{{ $request->status->color() }}" size="sm">
                                    {{ $request->status->label() }}
                                </flux:badge>
                            </flux:table.cell>
                            <flux:table.cell>
                                @if ($request->assignedTo)
                                    <div class="flex items-center gap-2">
                                        <flux:avatar size="xs" name="{{ $request->assignedTo->first_name }} {{ $request->assignedTo->last_name }}" />
                                        <flux:text size="sm">{{ $request->assignedTo->first_name }}</flux:text>
                                    </div>
                                @else
                                    <flux:text size="sm" class="text-gewo-grey-400">{{ __('listing_requests.not_assigned') }}</flux:text>
                                @endif
                            </flux:table.cell>
                            <flux:table.cell>
                                <flux:text size="sm">{{ $request->requested_at->format('d.m.Y H:i') }}</flux:text>
                            </flux:table.cell>
                            <flux:table.cell>
                                <flux:button href="{{ route('listing-requests.show', $request) }}" variant="ghost" size="sm" icon="eye" wire:navigate>
                                    {{ __('View') }}
                                </flux:button>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforeach
                </flux:table.rows>
            </flux:table>

            <div class="p-4 border-t border-gewo-grey-200">
                {{ $this->requests->links() }}
            </div>
        @endif
    </flux:card>
</div>
