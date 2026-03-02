<div>
    {{-- Breadcrumb --}}
    <section class="bg-white border-b border-gewo-grey-200">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-4">
            <flux:breadcrumbs>
                <flux:breadcrumbs.item href="{{ route('home') }}" icon="home" />
                <flux:breadcrumbs.item href="{{ route('listing-requests.portal', $listingRequest->access_token) }}">{{ __('listing_requests.portal_title') }}</flux:breadcrumbs.item>
                <flux:breadcrumbs.item>{{ __('listing_requests.appointments') }}</flux:breadcrumbs.item>
            </flux:breadcrumbs>
        </div>
    </section>

    {{-- Flash Messages --}}
    @if (session('success'))
        <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8 pt-6">
            <flux:callout variant="success" icon="check-circle" dismissible>
                {{ session('success') }}
            </flux:callout>
        </div>
    @endif

    @if (session('error'))
        <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8 pt-6">
            <flux:callout variant="danger" icon="exclamation-circle" dismissible>
                {{ session('error') }}
            </flux:callout>
        </div>
    @endif

    {{-- Main Content --}}
    <section class="py-8 lg:py-12 bg-gewo-grey-50">
        <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8 space-y-6">
            {{-- My Appointments --}}
            @if ($this->bookedAppointments->isNotEmpty())
                <flux:card>
                    <flux:heading size="lg" class="mb-4">{{ __('listing_requests.your_appointment') }}</flux:heading>

                    <div class="space-y-3">
                        @foreach ($this->bookedAppointments as $appointment)
                            <div class="flex items-center justify-between p-4 bg-gewo-grey-50 rounded-lg">
                                <div class="flex items-center gap-4">
                                    <div class="flex-shrink-0">
                                        <div class="w-14 h-14 bg-{{ $appointment->status->color() }}-100 text-{{ $appointment->status->color() }}-700 rounded-lg flex flex-col items-center justify-center">
                                            @if ($appointment->timeslot)
                                                <span class="text-xs font-medium">{{ $appointment->timeslot->starts_at->format('M') }}</span>
                                                <span class="text-lg font-bold leading-none">{{ $appointment->timeslot->starts_at->format('d') }}</span>
                                            @else
                                                <flux:icon name="calendar-x" class="size-6" />
                                            @endif
                                        </div>
                                    </div>
                                    <div>
                                        @if ($appointment->timeslot)
                                            <flux:heading size="base">{{ $appointment->timeslot->starts_at->format('l, d.m.Y') }}</flux:heading>
                                            <flux:text size="sm" class="text-gewo-grey-600">
                                                {{ $appointment->timeslot->starts_at->format('H:i') }} - {{ $appointment->timeslot->ends_at->format('H:i') }} Uhr
                                            </flux:text>
                                            @if ($appointment->timeslot->location)
                                                <flux:text size="sm" class="text-gewo-grey-600">
                                                    <flux:icon name="map-pin" class="size-4 inline" /> {{ $appointment->timeslot->location }}
                                                </flux:text>
                                            @endif
                                        @else
                                            <flux:text class="text-gewo-grey-500">{{ __('Timeslot no longer available') }}</flux:text>
                                        @endif
                                    </div>
                                </div>
                                <div class="flex items-center gap-3">
                                    <flux:badge color="{{ $appointment->status->color() }}">
                                        {{ $appointment->status->label() }}
                                    </flux:badge>
                                    @if ($appointment->isPending() || $appointment->isConfirmed())
                                        @if ($appointment->timeslot && $appointment->timeslot->starts_at->isFuture())
                                            <flux:button
                                                wire:click="cancel('{{ $appointment->id }}')"
                                                wire:confirm="{{ __('Möchten Sie diesen Termin wirklich absagen?') }}"
                                                variant="ghost"
                                                size="sm"
                                                icon="x-mark"
                                            >
                                                {{ __('listing_requests.cancel_appointment') }}
                                            </flux:button>
                                        @endif
                                    @endif
                                </div>
                            </div>

                            @if ($appointment->isCancelled() && $appointment->cancellation_reason)
                                <flux:text size="sm" class="text-gewo-grey-500 ml-18">
                                    {{ __('Reason') }}: {{ $appointment->cancellation_reason }}
                                </flux:text>
                            @endif
                        @endforeach
                    </div>
                </flux:card>
            @endif

            {{-- Available Timeslots --}}
            <flux:card>
                <flux:heading size="lg" class="mb-4">{{ __('listing_requests.available_timeslots') }}</flux:heading>

                @if ($this->availableTimeslots->isEmpty())
                    <div class="text-center py-8">
                        <flux:icon name="calendar" class="size-12 mx-auto text-gewo-grey-300 mb-3" />
                        <flux:text class="text-gewo-grey-500">{{ __('listing_requests.no_timeslots') }}</flux:text>
                    </div>
                @else
                    <div class="space-y-3">
                        @foreach ($this->availableTimeslots as $timeslot)
                            <div class="flex items-center justify-between p-4 bg-gewo-grey-50 rounded-lg hover:bg-gewo-grey-100 transition-colors">
                                <div class="flex items-center gap-4">
                                    <div class="flex-shrink-0">
                                        <div class="w-14 h-14 bg-accent text-white rounded-lg flex flex-col items-center justify-center">
                                            <span class="text-xs font-medium">{{ $timeslot->starts_at->format('M') }}</span>
                                            <span class="text-lg font-bold leading-none">{{ $timeslot->starts_at->format('d') }}</span>
                                        </div>
                                    </div>
                                    <div>
                                        <flux:heading size="base">{{ $timeslot->starts_at->format('l, d.m.Y') }}</flux:heading>
                                        <flux:text size="sm" class="text-gewo-grey-600">
                                            {{ $timeslot->starts_at->format('H:i') }} - {{ $timeslot->ends_at->format('H:i') }} Uhr
                                        </flux:text>
                                        @if ($timeslot->location)
                                            <flux:text size="sm" class="text-gewo-grey-600">
                                                <flux:icon name="map-pin" class="size-4 inline" /> {{ $timeslot->location }}
                                            </flux:text>
                                        @endif
                                    </div>
                                </div>
                                <div class="flex items-center gap-3">
                                    <flux:text size="sm" class="text-gewo-grey-500">
                                        {{ __('listing_requests.remaining_slots', ['count' => $timeslot->remainingSlots()]) }}
                                    </flux:text>
                                    <flux:button
                                        wire:click="book('{{ $timeslot->id }}')"
                                        variant="primary"
                                        size="sm"
                                        icon="calendar-days"
                                    >
                                        {{ __('listing_requests.book_appointment') }}
                                    </flux:button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </flux:card>

            {{-- Back Button --}}
            <div class="flex justify-start">
                <flux:button href="{{ route('listing-requests.portal', $listingRequest->access_token) }}" variant="ghost" icon="arrow-left" wire:navigate>
                    {{ __('Back to overview') }}
                </flux:button>
            </div>
        </div>
    </section>
</div>
