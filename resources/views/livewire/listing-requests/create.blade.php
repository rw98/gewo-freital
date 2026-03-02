<div>
    {{-- Breadcrumb --}}
    <section class="bg-white border-b border-gewo-grey-200">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-4">
            <flux:breadcrumbs>
                <flux:breadcrumbs.item href="{{ route('home') }}" icon="home" />
                <flux:breadcrumbs.item href="{{ route('listings.index') }}">{{ __('listings.show.breadcrumb.listings') }}</flux:breadcrumbs.item>
                <flux:breadcrumbs.item href="{{ route('listings.show', $listing) }}">{{ $listing->title }}</flux:breadcrumbs.item>
                <flux:breadcrumbs.item>{{ __('listing_requests.create_title') }}</flux:breadcrumbs.item>
            </flux:breadcrumbs>
        </div>
    </section>

    {{-- Main Content --}}
    <section class="py-8 lg:py-12 bg-gewo-grey-50">
        <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
            @if ($submitted)
                {{-- Success State --}}
                <flux:card>
                    <div class="text-center py-8">
                        <div class="mx-auto w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mb-6">
                            <flux:icon name="check" class="size-8 text-green-600" />
                        </div>
                        <flux:heading size="xl" class="mb-2">{{ __('listing_requests.request_submitted') }}</flux:heading>
                        <flux:text class="text-gewo-grey-600 max-w-md mx-auto">
                            {{ __('listing_requests.request_submitted_description') }}
                        </flux:text>
                        <div class="mt-8">
                            <flux:button href="{{ route('listings.show', $listing) }}" variant="outline" wire:navigate>
                                {{ __('Back to listing') }}
                            </flux:button>
                        </div>
                    </div>
                </flux:card>
            @else
                {{-- Request Form --}}
                <flux:card>
                    <flux:heading size="xl" class="mb-2">{{ __('listing_requests.create_title') }}</flux:heading>
                    <flux:text class="text-gewo-grey-600 mb-6">{{ __('listing_requests.create_description') }}</flux:text>

                    {{-- Listing Summary --}}
                    <div class="bg-gewo-grey-50 rounded-lg p-4 mb-6">
                        <div class="flex items-start gap-4">
                            <div class="w-20 h-20 bg-gewo-grey-200 rounded-lg flex-shrink-0 overflow-hidden">
                                @if ($listing->images->first())
                                    <img
                                        src="{{ Storage::url($listing->images->first()->path) }}"
                                        alt="{{ $listing->title }}"
                                        class="w-full h-full object-cover"
                                    />
                                @else
                                    <div class="w-full h-full flex items-center justify-center">
                                        <flux:icon name="home" class="size-8 text-gewo-grey-400" />
                                    </div>
                                @endif
                            </div>
                            <div>
                                <flux:heading size="base">{{ $listing->title }}</flux:heading>
                                <flux:text size="sm" class="text-gewo-grey-600">{{ $listing->fullAddress() }}</flux:text>
                                <div class="mt-1 flex items-center gap-3 text-sm">
                                    <span>{{ $listing->rooms }} Zimmer</span>
                                    <span>&bull;</span>
                                    <span>{{ number_format($listing->size_sqm, 0, ',', '.') }} m&sup2;</span>
                                    <span>&bull;</span>
                                    <span class="font-semibold text-accent">{{ number_format($listing->totalRent(), 0, ',', '.') }} &euro;</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <form wire:submit="submit" class="space-y-6">
                        {{-- Name Fields --}}
                        <div class="grid sm:grid-cols-2 gap-4">
                            <flux:field>
                                <flux:label>{{ __('listing_requests.first_name') }} *</flux:label>
                                <flux:input wire:model="first_name" />
                                <flux:error name="first_name" />
                            </flux:field>

                            <flux:field>
                                <flux:label>{{ __('listing_requests.last_name') }} *</flux:label>
                                <flux:input wire:model="last_name" />
                                <flux:error name="last_name" />
                            </flux:field>
                        </div>

                        <flux:field>
                            <flux:label>{{ __('listing_requests.middle_name') }}</flux:label>
                            <flux:input wire:model="middle_name" />
                            <flux:error name="middle_name" />
                        </flux:field>

                        {{-- Contact Fields --}}
                        <flux:field>
                            <flux:label>{{ __('listing_requests.email') }} *</flux:label>
                            <flux:input type="email" wire:model="email" />
                            <flux:error name="email" />
                        </flux:field>

                        <flux:field>
                            <flux:label>{{ __('listing_requests.phone') }}</flux:label>
                            <flux:input type="tel" wire:model="phone" />
                            <flux:error name="phone" />
                        </flux:field>

                        {{-- Message --}}
                        <flux:field>
                            <flux:label>{{ __('listing_requests.message') }}</flux:label>
                            <flux:textarea
                                wire:model="message"
                                rows="4"
                                placeholder="{{ __('listing_requests.message_placeholder') }}"
                            />
                            <flux:error name="message" />
                        </flux:field>

                        {{-- Submit --}}
                        <div class="flex items-center justify-end gap-3 pt-4 border-t border-gewo-grey-200">
                            <flux:button href="{{ route('listings.show', $listing) }}" variant="ghost" wire:navigate>
                                {{ __('Cancel') }}
                            </flux:button>
                            <flux:button type="submit" variant="primary" icon="paper-airplane">
                                {{ __('listing_requests.submit_request') }}
                            </flux:button>
                        </div>
                    </form>
                </flux:card>
            @endif
        </div>
    </section>
</div>
