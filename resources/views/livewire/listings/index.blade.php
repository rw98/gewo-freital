<div>
    {{-- Hero Section --}}
    <section class="bg-linear-to-br from-gewo-blue-50 to-white py-12">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <flux:heading size="xl" level="1" class="text-3xl lg:text-4xl">
                    {{ __('listings.index.title') }}
                </flux:heading>
                <flux:text class="mt-4 max-w-2xl mx-auto">
                    {{ __('listings.index.subtitle') }}
                </flux:text>
            </div>
        </div>
    </section>

    {{-- Search and Filters --}}
    <section class="bg-white border-b border-gewo-grey-200 sticky top-0 z-10">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-4 space-y-4">
            {{-- Search Bar --}}
            <div class="relative">
                <flux:input
                    wire:model.live.debounce.300ms="search"
                    icon="magnifying-glass"
                    :placeholder="__('listings.index.search_placeholder')"
                    class="w-full"
                />
                @if ($search)
                    <button
                        wire:click="clearSearch"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gewo-grey-400 hover:text-gewo-grey-600"
                    >
                        <flux:icon name="x-mark" class="size-5" />
                    </button>
                @endif
            </div>

            {{-- Filters Row --}}
            <div class="flex flex-wrap items-center gap-3">
                <flux:select wire:model.live="city" class="min-w-40">
                    <flux:select.option value="">{{ __('listings.index.all_cities') }}</flux:select.option>
                    @foreach ($cities as $cityOption)
                        <flux:select.option value="{{ $cityOption }}">{{ $cityOption }}</flux:select.option>
                    @endforeach
                </flux:select>

                <flux:select wire:model.live="minRooms" class="min-w-36">
                    <flux:select.option value="">{{ __('listings.index.rooms_from') }}</flux:select.option>
                    @foreach ([1, 2, 3, 4, 5] as $rooms)
                        <flux:select.option value="{{ $rooms }}">{{ $rooms }}+ {{ __('listings.index.rooms') }}</flux:select.option>
                    @endforeach
                </flux:select>

                <flux:select wire:model.live="maxRent" class="min-w-40">
                    <flux:select.option value="">{{ __('listings.index.max_rent') }}</flux:select.option>
                    @foreach ([300, 400, 500, 600, 700, 800, 1000] as $rent)
                        <flux:select.option value="{{ $rent }}">{{ __('listings.index.up_to') }} {{ $rent }} &euro;</flux:select.option>
                    @endforeach
                </flux:select>

                <flux:select wire:model.live="minSize" class="min-w-40">
                    <flux:select.option value="">{{ __('listings.index.min_size') }}</flux:select.option>
                    @foreach ([30, 40, 50, 60, 70, 80, 100] as $size)
                        <flux:select.option value="{{ $size }}">{{ __('listings.index.from') }} {{ $size }} m&sup2;</flux:select.option>
                    @endforeach
                </flux:select>

                <div class="ml-auto">
                    <flux:select wire:model.live="sortBy" class="min-w-44">
                        <flux:select.option value="newest">{{ __('listings.index.sort.newest') }}</flux:select.option>
                        <flux:select.option value="rent_asc">{{ __('listings.index.sort.rent_asc') }}</flux:select.option>
                        <flux:select.option value="rent_desc">{{ __('listings.index.sort.rent_desc') }}</flux:select.option>
                        <flux:select.option value="size_asc">{{ __('listings.index.sort.size_asc') }}</flux:select.option>
                        <flux:select.option value="size_desc">{{ __('listings.index.sort.size_desc') }}</flux:select.option>
                    </flux:select>
                </div>
            </div>

            {{-- Active Filters --}}
            @if ($this->hasActiveFilters)
                <div class="flex flex-wrap items-center gap-2 pt-2 border-t border-gewo-grey-100">
                    <flux:text size="sm" class="text-gewo-grey-500">{{ __('listings.index.active_filters') }}:</flux:text>

                    @if ($search)
                        <flux:badge color="sky" size="sm" class="gap-1">
                            "{{ Str::limit($search, 20) }}"
                            <button wire:click="clearSearch" class="hover:text-white">
                                <flux:icon name="x-mark" class="size-3" />
                            </button>
                        </flux:badge>
                    @endif

                    @if ($city)
                        <flux:badge color="sky" size="sm" class="gap-1">
                            {{ $city }}
                            <button wire:click="clearCity" class="hover:text-white">
                                <flux:icon name="x-mark" class="size-3" />
                            </button>
                        </flux:badge>
                    @endif

                    @if ($minRooms)
                        <flux:badge color="sky" size="sm" class="gap-1">
                            {{ $minRooms }}+ {{ __('listings.index.rooms') }}
                            <button wire:click="clearMinRooms" class="hover:text-white">
                                <flux:icon name="x-mark" class="size-3" />
                            </button>
                        </flux:badge>
                    @endif

                    @if ($maxRent)
                        <flux:badge color="sky" size="sm" class="gap-1">
                            {{ __('listings.index.up_to') }} {{ $maxRent }} &euro;
                            <button wire:click="clearMaxRent" class="hover:text-white">
                                <flux:icon name="x-mark" class="size-3" />
                            </button>
                        </flux:badge>
                    @endif

                    @if ($minSize)
                        <flux:badge color="sky" size="sm" class="gap-1">
                            {{ __('listings.index.from') }} {{ $minSize }} m&sup2;
                            <button wire:click="clearMinSize" class="hover:text-white">
                                <flux:icon name="x-mark" class="size-3" />
                            </button>
                        </flux:badge>
                    @endif

                    <flux:button variant="ghost" size="sm" wire:click="resetFilters" class="text-gewo-grey-500">
                        {{ __('listings.index.reset_all') }}
                    </flux:button>
                </div>
            @endif
        </div>
    </section>

    {{-- Listings Grid --}}
    <section class="py-8 lg:py-12 bg-gewo-grey-50">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            @if ($listings->isEmpty())
                <flux:card class="text-center py-16">
                    <flux:icon name="home" class="mx-auto size-16 text-gewo-grey-300" />
                    <flux:heading size="lg" class="mt-6">{{ __('listings.index.no_results.title') }}</flux:heading>
                    <flux:text class="mt-2 max-w-md mx-auto">
                        {{ __('listings.index.no_results.description') }}
                    </flux:text>
                    @if ($this->hasActiveFilters)
                        <flux:button variant="primary" wire:click="resetFilters" class="mt-6">
                            {{ __('listings.index.no_results.reset') }}
                        </flux:button>
                    @endif
                </flux:card>
            @else
                <div class="flex items-center justify-between mb-6">
                    <flux:text>
                        <strong>{{ $listings->total() }}</strong> {{ __('listings.index.results_found') }}
                    </flux:text>
                </div>

                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($listings as $listing)
                        <a href="{{ route('listings.show', $listing) }}" wire:key="listing-{{ $listing->id }}" class="group">
                            <flux:card class="h-full transition-shadow hover:shadow-lg">
                                {{-- Image --}}
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

                                {{-- Content --}}
                                <div class="space-y-3">
                                    <div class="flex items-start justify-between gap-2">
                                        <flux:heading size="lg" class="line-clamp-1 group-hover:text-accent transition-colors">
                                            {{ $listing->title }}
                                        </flux:heading>
                                        @if ($listing->available_from && $listing->available_from->isFuture())
                                            <flux:badge color="amber" size="sm">
                                                {{ __('listings.index.available_from') }} {{ $listing->available_from->format('d.m.') }}
                                            </flux:badge>
                                        @else
                                            <flux:badge color="green" size="sm">{{ __('listings.index.available_now') }}</flux:badge>
                                        @endif
                                    </div>

                                    <flux:text size="sm" class="flex items-center gap-1 text-gewo-grey-600">
                                        <flux:icon name="map-pin" class="size-4" />
                                        {{ $listing->city }}
                                    </flux:text>

                                    {{-- Features --}}
                                    <div class="flex flex-wrap gap-2">
                                        <flux:badge variant="outline" size="sm">
                                            {{ $listing->rooms }} {{ __('listings.index.rooms') }}
                                        </flux:badge>
                                        <flux:badge variant="outline" size="sm">
                                            {{ number_format($listing->size_sqm, 0, ',', '.') }} m&sup2;
                                        </flux:badge>
                                        @if ($listing->floor !== null)
                                            <flux:badge variant="outline" size="sm">
                                                @if ($listing->floor === 0)
                                                    {{ __('listings.index.ground_floor') }}
                                                @else
                                                    {{ $listing->floor }}. {{ __('listings.index.floor') }}
                                                @endif
                                            </flux:badge>
                                        @endif
                                        @if ($listing->has_balcony)
                                            <flux:badge variant="outline" size="sm" color="sky">
                                                {{ __('listings.index.balcony') }}
                                            </flux:badge>
                                        @endif
                                        @if ($listing->has_terrace)
                                            <flux:badge variant="outline" size="sm" color="sky">
                                                {{ __('listings.index.terrace') }}
                                            </flux:badge>
                                        @endif
                                    </div>

                                    {{-- Price --}}
                                    <div class="pt-3 border-t border-gewo-grey-100 flex items-baseline justify-between">
                                        <div>
                                            <flux:heading class="text-xl text-accent">
                                                {{ number_format($listing->totalRent(), 0, ',', '.') }} &euro;
                                            </flux:heading>
                                            <flux:text size="xs" class="text-gewo-grey-500">
                                                {{ __('listings.index.total_rent') }}
                                            </flux:text>
                                        </div>
                                        <flux:text size="sm" class="text-gewo-grey-500">
                                            {{ number_format($listing->rent_cold, 0, ',', '.') }} &euro; {{ __('listings.index.cold_rent') }}
                                        </flux:text>
                                    </div>
                                </div>
                            </flux:card>
                        </a>
                    @endforeach
                </div>

                <div class="mt-8">
                    {{ $listings->links() }}
                </div>
            @endif
        </div>
    </section>
</div>
