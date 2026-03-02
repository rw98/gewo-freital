<div>
    {{-- Breadcrumb --}}
    <section class="bg-white border-b border-gewo-grey-200">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-4">
            <flux:breadcrumbs>
                <flux:breadcrumbs.item href="{{ route('home') }}" icon="home" />
                <flux:breadcrumbs.item href="{{ route('listings.index') }}">{{ __('listings.show.breadcrumb.listings') }}</flux:breadcrumbs.item>
                <flux:breadcrumbs.item href="{{ route('listings.show', $listingRequest->listing) }}">{{ $listingRequest->listing->title }}</flux:breadcrumbs.item>
                <flux:breadcrumbs.item>{{ __('listing_requests.portal_title') }}</flux:breadcrumbs.item>
            </flux:breadcrumbs>
        </div>
    </section>

    {{-- Flash Messages --}}
    @if (session('success'))
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 pt-6">
            <flux:callout variant="success" icon="check-circle" dismissible>
                {{ session('success') }}
            </flux:callout>
        </div>
    @endif

    @if (session('info'))
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 pt-6">
            <flux:callout variant="info" icon="information-circle" dismissible>
                {{ session('info') }}
            </flux:callout>
        </div>
    @endif

    {{-- Main Content --}}
    <section class="py-8 lg:py-12 bg-gewo-grey-50">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-3 gap-6">
                {{-- Left Column: Main Content --}}
                <div class="lg:col-span-2 space-y-6">
                    {{-- Status Card --}}
                    <flux:card>
                        <div class="flex items-start gap-4">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 rounded-full bg-{{ $listingRequest->status->color() }}-100 flex items-center justify-center">
                                    @switch($listingRequest->status->value)
                                        @case('pending_email_confirmation')
                                            <flux:icon name="envelope" class="size-6 text-{{ $listingRequest->status->color() }}-600" />
                                            @break
                                        @case('confirmed')
                                        @case('approved')
                                            <flux:icon name="check" class="size-6 text-{{ $listingRequest->status->color() }}-600" />
                                            @break
                                        @case('appointment_pending')
                                            <flux:icon name="calendar" class="size-6 text-{{ $listingRequest->status->color() }}-600" />
                                            @break
                                        @case('waiting_for_information')
                                            <flux:icon name="document-text" class="size-6 text-{{ $listingRequest->status->color() }}-600" />
                                            @break
                                        @case('waiting_for_approval')
                                        @case('waiting_for_signature')
                                            <flux:icon name="clock" class="size-6 text-{{ $listingRequest->status->color() }}-600" />
                                            @break
                                        @case('signed')
                                        @case('closed')
                                            <flux:icon name="check-circle" class="size-6 text-{{ $listingRequest->status->color() }}-600" />
                                            @break
                                        @case('rejected')
                                            <flux:icon name="x-circle" class="size-6 text-{{ $listingRequest->status->color() }}-600" />
                                            @break
                                        @default
                                            <flux:icon name="arrow-path" class="size-6 text-{{ $listingRequest->status->color() }}-600" />
                                    @endswitch
                                </div>
                            </div>
                            <div class="flex-1">
                                <flux:heading size="lg">{{ __('listing_requests.current_status') }}</flux:heading>
                                <div class="mt-2">
                                    <flux:badge size="lg" color="{{ $listingRequest->status->color() }}">
                                        {{ $listingRequest->status->label() }}
                                    </flux:badge>
                                </div>
                                <flux:text size="sm" class="mt-2 text-gewo-grey-600">
                                    {{ __('listing_requests.requested_at') }}: {{ $listingRequest->requested_at->format('d.m.Y H:i') }}
                                </flux:text>
                            </div>
                        </div>

                        @if ($listingRequest->status->value === 'pending_email_confirmation')
                            <div class="mt-6 p-4 bg-amber-50 rounded-lg border border-amber-200">
                                <flux:text size="sm" class="text-amber-800">
                                    {{ __('listing_requests.confirm_email_description') }}
                                </flux:text>
                            </div>
                        @endif

                        @if ($listingRequest->status->value === 'rejected' && $listingRequest->rejection_reason)
                            <div class="mt-6 p-4 bg-red-50 rounded-lg border border-red-200">
                                <flux:heading size="sm" class="text-red-800 mb-1">{{ __('listing_requests.rejection_reason') }}</flux:heading>
                                <flux:text size="sm" class="text-red-700">{{ $listingRequest->rejection_reason }}</flux:text>
                            </div>
                        @endif
                    </flux:card>

                    {{-- Quick Actions --}}
                    <flux:card>
                        <flux:heading size="lg" class="mb-4">{{ __('Actions') }}</flux:heading>
                        <div class="grid sm:grid-cols-3 gap-3">
                            <flux:button
                                href="{{ route('listing-requests.documents', $listingRequest->access_token) }}"
                                variant="outline"
                                icon="document-arrow-up"
                                class="justify-center"
                                wire:navigate
                            >
                                {{ __('listing_requests.documents') }}
                                @if ($listingRequest->documents->count() > 0)
                                    <flux:badge size="sm" color="blue" class="ml-2">{{ $listingRequest->documents->count() }}</flux:badge>
                                @endif
                            </flux:button>

                            @if (in_array($listingRequest->status->value, ['appointment_pending', 'confirmed']))
                                <flux:button
                                    href="{{ route('listing-requests.appointments', $listingRequest->access_token) }}"
                                    variant="outline"
                                    icon="calendar-days"
                                    class="justify-center"
                                    wire:navigate
                                >
                                    {{ __('listing_requests.appointments') }}
                                </flux:button>
                            @endif

                            <flux:button
                                href="{{ route('listing-requests.messages', $listingRequest->access_token) }}"
                                variant="outline"
                                icon="chat-bubble-left-right"
                                class="justify-center"
                                wire:navigate
                            >
                                {{ __('listing_requests.messages') }}
                            </flux:button>
                        </div>
                    </flux:card>

                    {{-- Upcoming Appointments --}}
                    @php
                        $upcomingAppointments = $listingRequest->appointments
                            ->filter(fn($a) => $a->timeslot && $a->timeslot->starts_at->isFuture() && !$a->isCancelled())
                            ->sortBy(fn($a) => $a->timeslot->starts_at);
                    @endphp
                    @if ($upcomingAppointments->isNotEmpty())
                        <flux:card>
                            <flux:heading size="lg" class="mb-4">{{ __('listing_requests.your_appointment') }}</flux:heading>
                            @foreach ($upcomingAppointments as $appointment)
                                <div class="flex items-center gap-4 p-4 bg-gewo-grey-50 rounded-lg">
                                    <div class="flex-shrink-0">
                                        <div class="w-14 h-14 bg-accent text-white rounded-lg flex flex-col items-center justify-center">
                                            <span class="text-xs font-medium">{{ $appointment->timeslot->starts_at->format('M') }}</span>
                                            <span class="text-lg font-bold leading-none">{{ $appointment->timeslot->starts_at->format('d') }}</span>
                                        </div>
                                    </div>
                                    <div class="flex-1">
                                        <flux:heading size="base">{{ $appointment->timeslot->starts_at->format('l, d.m.Y') }}</flux:heading>
                                        <flux:text size="sm" class="text-gewo-grey-600">
                                            {{ $appointment->timeslot->starts_at->format('H:i') }} - {{ $appointment->timeslot->ends_at->format('H:i') }} Uhr
                                        </flux:text>
                                        @if ($appointment->timeslot->location)
                                            <flux:text size="sm" class="text-gewo-grey-600 mt-1">
                                                <flux:icon name="map-pin" class="size-4 inline" /> {{ $appointment->timeslot->location }}
                                            </flux:text>
                                        @endif
                                    </div>
                                    <div>
                                        <flux:badge color="{{ $appointment->status->color() }}">
                                            {{ $appointment->status->label() }}
                                        </flux:badge>
                                    </div>
                                </div>
                            @endforeach
                        </flux:card>
                    @endif
                </div>

                {{-- Right Column: Listing Info --}}
                <div class="space-y-6">
                    {{-- Listing Card --}}
                    <flux:card>
                        <div class="aspect-video bg-gewo-grey-200 rounded-lg overflow-hidden mb-4">
                            @if ($listingRequest->listing->images->first())
                                <img
                                    src="{{ Storage::url($listingRequest->listing->images->first()->path) }}"
                                    alt="{{ $listingRequest->listing->title }}"
                                    class="w-full h-full object-cover"
                                />
                            @else
                                <div class="w-full h-full flex items-center justify-center">
                                    <flux:icon name="home" class="size-12 text-gewo-grey-400" />
                                </div>
                            @endif
                        </div>
                        <flux:heading size="base">{{ $listingRequest->listing->title }}</flux:heading>
                        <flux:text size="sm" class="text-gewo-grey-600 mt-1">
                            {{ $listingRequest->listing->fullAddress() }}
                        </flux:text>
                        <div class="mt-3 flex items-center gap-3 text-sm">
                            <span>{{ $listingRequest->listing->rooms }} Zimmer</span>
                            <span>&bull;</span>
                            <span>{{ number_format($listingRequest->listing->size_sqm, 0, ',', '.') }} m&sup2;</span>
                        </div>
                        <div class="mt-3 pt-3 border-t border-gewo-grey-200">
                            <div class="flex items-center justify-between">
                                <flux:text size="sm" class="text-gewo-grey-600">Warmmiete</flux:text>
                                <flux:heading class="text-accent">{{ number_format($listingRequest->listing->totalRent(), 0, ',', '.') }} &euro;</flux:heading>
                            </div>
                        </div>
                        <div class="mt-4">
                            <flux:button
                                href="{{ route('listings.show', $listingRequest->listing) }}"
                                variant="outline"
                                size="sm"
                                class="w-full"
                                wire:navigate
                            >
                                {{ __('View listing') }}
                            </flux:button>
                        </div>
                    </flux:card>

                    {{-- Contact Info --}}
                    @if ($listingRequest->assignedTo)
                        <flux:card>
                            <flux:heading size="base" class="mb-3">{{ __('listing_requests.assigned_employee') }}</flux:heading>
                            <div class="flex items-center gap-3">
                                <flux:avatar size="md" name="{{ $listingRequest->assignedTo->first_name }} {{ $listingRequest->assignedTo->last_name }}" />
                                <div>
                                    <flux:text class="font-medium">{{ $listingRequest->assignedTo->first_name }} {{ $listingRequest->assignedTo->last_name }}</flux:text>
                                </div>
                            </div>
                        </flux:card>
                    @endif
                </div>
            </div>
        </div>
    </section>
</div>
