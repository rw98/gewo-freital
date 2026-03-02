<section id="wohnungen" class="py-16 lg:py-24 bg-white">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <flux:heading size="xl" level="2">{{ __('pages.landing.search.title') }}</flux:heading>
            <flux:text class="mt-4 max-w-2xl mx-auto">
                {{ __('pages.landing.search.description') }}
            </flux:text>
        </div>

        {{-- Search Form --}}
        <form wire:submit="search" class="bg-gewo-grey-50 rounded-2xl p-6 lg:p-8 max-w-4xl mx-auto">
            <div class="grid md:grid-cols-4 gap-4">
                <flux:field>
                    <flux:label>{{ __('pages.landing.search.form.location') }}</flux:label>
                    <flux:select wire:model="city">
                        <flux:select.option value="">{{ __('pages.landing.search.form.location_placeholder') }}</flux:select.option>
                        @foreach ($this->cities as $cityOption)
                            <flux:select.option value="{{ $cityOption }}">{{ $cityOption }}</flux:select.option>
                        @endforeach
                    </flux:select>
                </flux:field>
                <flux:field>
                    <flux:label>{{ __('pages.landing.search.form.rooms') }}</flux:label>
                    <flux:select wire:model="minRooms">
                        <flux:select.option value="">{{ __('pages.landing.search.form.rooms_placeholder') }}</flux:select.option>
                        @foreach ([1, 2, 3, 4, 5] as $rooms)
                            <flux:select.option value="{{ $rooms }}">{{ $rooms }}+ {{ __('listings.index.rooms') }}</flux:select.option>
                        @endforeach
                    </flux:select>
                </flux:field>
                <flux:field>
                    <flux:label>{{ __('pages.landing.search.form.max_rent') }}</flux:label>
                    <flux:select wire:model="maxRent">
                        <flux:select.option value="">{{ __('pages.landing.search.form.max_rent_placeholder') }}</flux:select.option>
                        @foreach ([300, 400, 500, 600, 700, 800, 1000] as $rent)
                            <flux:select.option value="{{ $rent }}">{{ __('listings.index.up_to') }} {{ $rent }} &euro;</flux:select.option>
                        @endforeach
                    </flux:select>
                </flux:field>
                <div class="flex items-end">
                    <flux:button type="submit" variant="primary" class="w-full" icon="magnifying-glass">
                        {{ __('pages.landing.search.form.submit') }}
                    </flux:button>
                </div>
            </div>
        </form>

        {{-- Featured Apartments --}}
        @if ($this->featuredListings->isNotEmpty())
            <div class="mt-12 grid md:grid-cols-3 gap-6">
                @foreach ($this->featuredListings as $listing)
                    <a href="{{ route('listings.show', $listing) }}" wire:key="featured-{{ $listing->id }}" class="group">
                        <flux:card class="h-full transition-shadow hover:shadow-lg">
                            <div class="aspect-video bg-linear-to-br from-gewo-grey-100 to-gewo-grey-50 rounded-lg mb-4 overflow-hidden">
                                @if ($listing->images->first())
                                    <img
                                        src="{{ Storage::url($listing->images->first()->path) }}"
                                        alt="{{ $listing->title }}"
                                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                    />
                                @else
                                    <div class="w-full h-full flex items-center justify-center">
                                        <flux:icon name="home" class="size-12 text-gewo-grey-400" />
                                    </div>
                                @endif
                            </div>
                            <flux:heading size="lg" class="group-hover:text-accent transition-colors">{{ $listing->city }}</flux:heading>
                            <flux:text size="sm" class="flex items-center gap-4 mt-2">
                                <span>{{ $listing->rooms }} {{ __('listings.index.rooms') }}</span>
                                <span>&bull;</span>
                                <span>{{ number_format($listing->size_sqm, 0, ',', '.') }} m&sup2;</span>
                            </flux:text>
                            <div class="mt-4 flex items-center justify-between">
                                <flux:heading class="text-xl text-accent">
                                    {{ number_format($listing->totalRent(), 0, ',', '.') }} &euro;
                                    <flux:text inline size="sm" class="font-normal">{{ __('pages.landing.search.per_month') }}</flux:text>
                                </flux:heading>
                                <flux:button variant="outline" size="sm" tabindex="-1">{{ __('pages.landing.search.details') }}</flux:button>
                            </div>
                        </flux:card>
                    </a>
                @endforeach
            </div>
        @else
            <div class="mt-12 text-center py-12">
                <flux:icon name="home" class="mx-auto size-16 text-gewo-grey-300" />
                <flux:heading size="lg" class="mt-4">{{ __('listings.index.no_results.title') }}</flux:heading>
                <flux:text class="mt-2">{{ __('pages.landing.search.no_listings') }}</flux:text>
            </div>
        @endif

        @if ($this->totalCount > 3)
            <div class="text-center mt-8">
                <flux:button variant="ghost" :href="route('listings.index')" icon-trailing="arrow-right">
                    {{ __('pages.landing.search.show_all') }} ({{ $this->totalCount }})
                </flux:button>
            </div>
        @endif
    </div>
</section>
