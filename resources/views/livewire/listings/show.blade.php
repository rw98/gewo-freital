<div>
    {{-- Breadcrumb --}}
    <section class="bg-white border-b border-gewo-grey-200">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-4">
            <flux:breadcrumbs>
                <flux:breadcrumbs.item href="{{ route('home') }}" icon="home" />
                <flux:breadcrumbs.item href="{{ route('listings.index') }}">{{ __('listings.show.breadcrumb.listings') }}</flux:breadcrumbs.item>
                <flux:breadcrumbs.item>{{ $listing->title }}</flux:breadcrumbs.item>
            </flux:breadcrumbs>
        </div>
    </section>

    {{-- Main Content --}}
    <section class="py-8 lg:py-12 bg-gewo-grey-50">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-3 gap-8">
                {{-- Left Column: Images and Details --}}
                <div class="lg:col-span-2 space-y-6">
                    {{-- Image Gallery --}}
                    <flux:card class="p-0 overflow-hidden">
                        @if ($this->images->isNotEmpty())
                            <div class="aspect-video">
                                <img
                                    src="{{ Storage::url($this->images->first()->path) }}"
                                    alt="{{ $listing->title }}"
                                    class="w-full h-full object-cover"
                                />
                            </div>
                            @if ($this->images->count() > 1)
                                <div class="p-4 grid grid-cols-4 gap-2">
                                    @foreach ($this->images->skip(1)->take(4) as $image)
                                        <div class="aspect-video rounded-lg overflow-hidden">
                                            <img
                                                src="{{ Storage::url($image->path) }}"
                                                alt="{{ $listing->title }}"
                                                class="w-full h-full object-cover"
                                            />
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        @else
                            <div class="aspect-video bg-linear-to-br from-gewo-grey-100 to-gewo-grey-50 flex items-center justify-center">
                                <flux:icon name="home" class="size-24 text-gewo-grey-300" />
                            </div>
                        @endif
                    </flux:card>

                    {{-- Title and Location --}}
                    <flux:card>
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <flux:heading size="xl" level="1">{{ $listing->title }}</flux:heading>
                                <flux:text class="mt-2 flex items-center gap-2 text-gewo-grey-600">
                                    <flux:icon name="map-pin" class="size-5" />
                                    {{ $listing->fullAddress() }}
                                </flux:text>
                            </div>
                            @if ($listing->available_from && $listing->available_from->isFuture())
                                <flux:badge color="amber" size="lg">
                                    {{ __('listings.show.available_from') }} {{ $listing->available_from->format('d.m.Y') }}
                                </flux:badge>
                            @else
                                <flux:badge color="green" size="lg">{{ __('listings.show.available_now') }}</flux:badge>
                            @endif
                        </div>
                    </flux:card>

                    {{-- Description --}}
                    @if ($listing->description)
                        <flux:card>
                            <flux:heading size="lg" class="mb-4">{{ __('listings.show.description') }}</flux:heading>
                            <flux:text class="whitespace-pre-line">{{ $listing->description }}</flux:text>
                        </flux:card>
                    @endif

                    {{-- Features Grid --}}
                    <flux:card>
                        <flux:heading size="lg" class="mb-6">{{ __('listings.show.features') }}</flux:heading>
                        <div class="grid sm:grid-cols-2 gap-6">
                            {{-- Apartment Details --}}
                            <div class="space-y-4">
                                <flux:heading size="base">{{ __('listings.show.apartment_details') }}</flux:heading>
                                <dl class="space-y-3">
                                    <div class="flex items-center justify-between">
                                        <dt class="text-gewo-grey-600">{{ __('listings.show.rooms') }}</dt>
                                        <dd class="font-medium">{{ $listing->rooms }}</dd>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <dt class="text-gewo-grey-600">{{ __('listings.show.size') }}</dt>
                                        <dd class="font-medium">{{ number_format($listing->size_sqm, 2, ',', '.') }} m&sup2;</dd>
                                    </div>
                                    @if ($listing->floor !== null)
                                        <div class="flex items-center justify-between">
                                            <dt class="text-gewo-grey-600">{{ __('listings.show.floor') }}</dt>
                                            <dd class="font-medium">
                                                @if ($listing->floor === 0)
                                                    {{ __('listings.show.ground_floor') }}
                                                @else
                                                    {{ $listing->floor }}. {{ __('listings.show.floor_suffix') }}
                                                @endif
                                            </dd>
                                        </div>
                                    @endif
                                    @if ($listing->flat_number)
                                        <div class="flex items-center justify-between">
                                            <dt class="text-gewo-grey-600">{{ __('listings.show.flat_number') }}</dt>
                                            <dd class="font-medium">{{ $listing->flat_number }}</dd>
                                        </div>
                                    @endif
                                </dl>
                            </div>

                            {{-- Building Details --}}
                            <div class="space-y-4">
                                <flux:heading size="base">{{ __('listings.show.building_details') }}</flux:heading>
                                <dl class="space-y-3">
                                    @if ($listing->year_built)
                                        <div class="flex items-center justify-between">
                                            <dt class="text-gewo-grey-600">{{ __('listings.show.year_built') }}</dt>
                                            <dd class="font-medium">{{ $listing->year_built }}</dd>
                                        </div>
                                    @endif
                                    <div class="flex items-center justify-between">
                                        <dt class="text-gewo-grey-600">{{ __('listings.show.elevator') }}</dt>
                                        <dd>
                                            @if ($listing->has_elevator)
                                                <flux:badge color="green" size="sm">{{ __('Yes') }}</flux:badge>
                                            @else
                                                <flux:badge color="zinc" size="sm">{{ __('No') }}</flux:badge>
                                            @endif
                                        </dd>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <dt class="text-gewo-grey-600">{{ __('listings.show.wheelchair_accessible') }}</dt>
                                        <dd>
                                            @if ($listing->is_wheelchair_accessible)
                                                <flux:badge color="green" size="sm">{{ __('Yes') }}</flux:badge>
                                            @else
                                                <flux:badge color="zinc" size="sm">{{ __('No') }}</flux:badge>
                                            @endif
                                        </dd>
                                    </div>
                                </dl>
                            </div>
                        </div>
                    </flux:card>

                    {{-- Amenities --}}
                    <flux:card>
                        <flux:heading size="lg" class="mb-6">{{ __('listings.show.amenities') }}</flux:heading>
                        <div class="flex flex-wrap gap-3">
                            @if ($listing->has_balcony)
                                <flux:badge size="lg" color="sky" icon="sun">{{ __('listings.show.balcony') }}</flux:badge>
                            @endif
                            @if ($listing->has_terrace)
                                <flux:badge size="lg" color="sky" icon="sun">{{ __('listings.show.terrace') }}</flux:badge>
                            @endif
                            @if ($listing->pets_allowed === true)
                                <flux:badge size="lg" color="green">{{ __('listings.show.pets_allowed') }}</flux:badge>
                            @elseif ($listing->pets_allowed === false)
                                <flux:badge size="lg" color="red">{{ __('listings.show.pets_not_allowed') }}</flux:badge>
                            @endif
                            @if ($listing->amenities)
                                @foreach ($listing->amenities as $amenity)
                                    <flux:badge size="lg" variant="outline">{{ $amenity }}</flux:badge>
                                @endforeach
                            @endif
                        </div>
                    </flux:card>
                </div>

                {{-- Right Column: Price and Contact --}}
                <div class="space-y-6">
                    {{-- Price Card --}}
                    <flux:card class="sticky top-4">
                        <flux:heading size="lg" class="mb-4">{{ __('listings.show.rent') }}</flux:heading>

                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <flux:text>{{ __('listings.show.cold_rent') }}</flux:text>
                                <flux:text class="font-medium">{{ number_format($listing->rent_cold, 2, ',', '.') }} &euro;</flux:text>
                            </div>
                            <div class="flex items-center justify-between">
                                <flux:text>{{ __('listings.show.utility_cost') }}</flux:text>
                                <flux:text class="font-medium">{{ number_format($listing->utility_cost, 2, ',', '.') }} &euro;</flux:text>
                            </div>
                            <div class="border-t border-gewo-grey-200 pt-3 flex items-center justify-between">
                                <flux:text class="font-semibold">{{ __('listings.show.total_rent') }}</flux:text>
                                <flux:heading class="text-2xl text-accent">{{ number_format($listing->totalRent(), 2, ',', '.') }} &euro;</flux:heading>
                            </div>
                        </div>

                        <div class="mt-6 space-y-3">
                            <flux:button
                                href="{{ route('listing-requests.create', $listing) }}"
                                variant="primary"
                                class="w-full"
                                icon="paper-airplane"
                                wire:navigate
                            >
                                {{ __('listings.show.request_apartment') }}
                            </flux:button>
                            <flux:button variant="outline" class="w-full" icon="phone">
                                {{ __('listings.show.call_us') }}
                            </flux:button>
                        </div>
                    </flux:card>

                    {{-- Location Info --}}
                    <flux:card>
                        <flux:heading size="lg" class="mb-4">{{ __('listings.show.location') }}</flux:heading>
                        @php
                            $mapAddress = e($listing->street . ' ' . $listing->street_number . ', ' . $listing->postal_code . ' ' . $listing->city . ', Germany');
                            $mapNotAvailable = e(__('listings.show.map_not_available'));
                        @endphp
                        <div
                            x-data="listingMap('{{ $mapAddress }}', '{{ $mapNotAvailable }}')"
                            class="aspect-video bg-gewo-grey-100 rounded-lg overflow-hidden"
                            x-ref="mapContainer"
                        >
                            <div class="flex items-center justify-center h-full">
                                <flux:icon name="arrow-path" class="size-8 text-gewo-grey-400 animate-spin" />
                            </div>
                        </div>
                        <flux:text class="mt-4">
                            {{ $listing->street }} {{ $listing->street_number }}<br>
                            {{ $listing->postal_code }} {{ $listing->city }}
                        </flux:text>
                    </flux:card>

                    <script>
                        document.addEventListener('alpine:init', () => {
                            Alpine.data('listingMap', (address, notAvailableText) => ({
                                map: null,
                                address: address,
                                notAvailableText: notAvailableText,
                                init() {
                                    this.loadMap();
                                },
                                async loadMap() {
                                    // Load Leaflet CSS
                                    if (!document.querySelector('link[href*="leaflet"]')) {
                                        const link = document.createElement('link');
                                        link.rel = 'stylesheet';
                                        link.href = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css';
                                        document.head.appendChild(link);
                                    }

                                    // Load Leaflet JS
                                    if (!window.L) {
                                        await new Promise((resolve) => {
                                            const script = document.createElement('script');
                                            script.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js';
                                            script.onload = resolve;
                                            document.head.appendChild(script);
                                        });
                                    }

                                    // Geocode address using Nominatim
                                    try {
                                        const response = await fetch(
                                            `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(this.address)}&limit=1`
                                        );
                                        const data = await response.json();

                                        if (data.length > 0) {
                                            const lat = parseFloat(data[0].lat);
                                            const lon = parseFloat(data[0].lon);

                                            this.map = L.map(this.$refs.mapContainer, {
                                                zoomControl: false
                                            }).setView([lat, lon], 16);

                                            // Add zoom control to bottom right
                                            L.control.zoom({ position: 'bottomright' }).addTo(this.map);

                                            // Use CartoDB Positron for a clean, modern look
                                            L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
                                                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> &copy; <a href="https://carto.com/attributions">CARTO</a>',
                                                subdomains: 'abcd',
                                                maxZoom: 19
                                            }).addTo(this.map);

                                            // Custom marker with accent color
                                            const markerIcon = L.divIcon({
                                                className: 'custom-marker',
                                                html: `<div style="background-color: #00a3d9; width: 24px; height: 24px; border-radius: 50% 50% 50% 0; transform: rotate(-45deg); border: 3px solid white; box-shadow: 0 2px 8px rgba(0,0,0,0.3);"></div>`,
                                                iconSize: [24, 24],
                                                iconAnchor: [12, 24]
                                            });
                                            L.marker([lat, lon], { icon: markerIcon }).addTo(this.map);
                                        } else {
                                            this.showNotAvailable();
                                        }
                                    } catch (error) {
                                        this.showNotAvailable();
                                    }
                                },
                                showNotAvailable() {
                                    this.$refs.mapContainer.innerHTML = `<div class="flex items-center justify-center h-full text-gewo-grey-500">${this.notAvailableText}</div>`;
                                }
                            }));
                        });
                    </script>
                </div>
            </div>

            {{-- Similar Listings --}}
            @if ($this->similarListings->isNotEmpty())
                <div class="mt-12">
                    <flux:heading size="xl" class="mb-6">{{ __('listings.show.similar_listings') }}</flux:heading>
                    <div class="grid md:grid-cols-3 gap-6">
                        @foreach ($this->similarListings as $similar)
                            <a href="{{ route('listings.show', $similar) }}" class="group">
                                <flux:card class="h-full transition-shadow hover:shadow-lg">
                                    <div class="aspect-video bg-linear-to-br from-gewo-grey-100 to-gewo-grey-50 rounded-lg mb-4 overflow-hidden">
                                        @if ($similar->images->first())
                                            <img
                                                src="{{ Storage::url($similar->images->first()->path) }}"
                                                alt="{{ $similar->title }}"
                                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                            />
                                        @else
                                            <div class="w-full h-full flex items-center justify-center">
                                                <flux:icon name="home" class="size-12 text-gewo-grey-400" />
                                            </div>
                                        @endif
                                    </div>
                                    <flux:heading size="lg" class="line-clamp-1 group-hover:text-accent transition-colors">
                                        {{ $similar->title }}
                                    </flux:heading>
                                    <div class="mt-2 flex items-baseline justify-between">
                                        <flux:text size="sm">{{ $similar->rooms }} {{ __('listings.index.rooms') }} &bull; {{ number_format($similar->size_sqm, 0, ',', '.') }} m&sup2;</flux:text>
                                        <flux:heading class="text-accent">{{ number_format($similar->totalRent(), 0, ',', '.') }} &euro;</flux:heading>
                                    </div>
                                </flux:card>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </section>
</div>
