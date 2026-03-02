<x-layouts.public
    :title="__('pages.landing.meta.title')"
    :nav-items="[
        ['href' => '#wohnungen', 'icon' => 'home', 'label' => __('pages.landing.header.nav.rent')],
        ['href' => '#service', 'icon' => 'wrench-screwdriver', 'label' => __('pages.landing.header.nav.service')],
        ['href' => '#ueber-uns', 'icon' => 'building-office-2', 'label' => __('pages.landing.header.nav.about')],
        ['href' => '#kontakt', 'icon' => 'phone', 'label' => __('pages.landing.header.nav.contact')],
    ]"
>
    {{-- Hero Section --}}
    <section class="relative bg-linear-to-br from-gewo-blue-50 to-white overflow-hidden">
        <div class="absolute inset-0 opacity-5">
            <svg class="w-full h-full" viewBox="0 0 100 100" preserveAspectRatio="none">
                <pattern id="dots" x="0" y="0" width="10" height="10" patternUnits="userSpaceOnUse">
                    <circle cx="2" cy="2" r="1" fill="#00a3d9"/>
                </pattern>
                <rect width="100" height="100" fill="url(#dots)"/>
            </svg>
        </div>
        <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-16 lg:py-24">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <div>
                    <flux:badge color="sky" class="mb-4">{{ __('pages.landing.hero.badge') }}</flux:badge>
                    <flux:heading size="xl" level="1" class="text-4xl lg:text-5xl mb-6">
                        {{ __('pages.landing.hero.title') }}<br>
                        <span class="text-accent">{{ __('pages.landing.hero.title_highlight') }}</span>
                    </flux:heading>
                    <flux:text size="lg" class="mb-8 max-w-lg">
                        {{ __('pages.landing.hero.description') }}
                    </flux:text>
                    <div class="flex flex-wrap gap-4">
                        <flux:button variant="primary" href="#wohnungen" icon="magnifying-glass" class="px-6! py-3!">
                            {{ __('pages.landing.hero.cta_primary') }}
                        </flux:button>
                        <flux:button variant="outline" href="#ueber-uns" icon="information-circle" class="px-6! py-3!">
                            {{ __('pages.landing.hero.cta_secondary') }}
                        </flux:button>
                    </div>
                </div>
                <div class="relative">
                    <div class="bg-white rounded-2xl shadow-xl p-6 border border-gewo-grey-100">
                        <div class="aspect-video bg-linear-to-br from-gewo-blue-100 to-gewo-blue-50 rounded-lg flex items-center justify-center">
                            <flux:icon name="home-modern" class="size-24 text-accent" />
                        </div>
                        <div class="mt-4 flex items-center justify-between">
                            <div>
                                <flux:text size="sm">{{ __('pages.landing.hero.rent_label') }}</flux:text>
                                <flux:heading size="lg" class="text-2xl text-accent">{{ __('pages.landing.hero.rent_value') }}</flux:heading>
                            </div>
                            <flux:badge color="emerald" size="lg">{{ __('pages.landing.hero.rent_badge') }}</flux:badge>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Quick Stats --}}
    <section class="bg-gewo-blue-800 py-12">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-8">
                <div class="text-center">
                    <flux:heading class="text-4xl font-bold text-white">{{ __('pages.landing.stats.experience.value') }}</flux:heading>
                    <flux:text class="text-gewo-blue-200 mt-1">{{ __('pages.landing.stats.experience.label') }}</flux:text>
                </div>
                <div class="text-center">
                    <flux:heading class="text-4xl font-bold text-white">{{ __('pages.landing.stats.apartments.value') }}</flux:heading>
                    <flux:text class="text-gewo-blue-200 mt-1">{{ __('pages.landing.stats.apartments.label') }}</flux:text>
                </div>
                <div class="text-center">
                    <flux:heading class="text-4xl font-bold text-white">{{ __('pages.landing.stats.locations.value') }}</flux:heading>
                    <flux:text class="text-gewo-blue-200 mt-1">{{ __('pages.landing.stats.locations.label') }}</flux:text>
                </div>
                <div class="text-center">
                    <flux:heading class="text-4xl font-bold text-white">{{ __('pages.landing.stats.dividend.value') }}</flux:heading>
                    <flux:text class="text-gewo-blue-200 mt-1">{{ __('pages.landing.stats.dividend.label') }}</flux:text>
                </div>
            </div>
        </div>
    </section>

    {{-- Apartment Search Section --}}
    <section id="wohnungen" class="py-16 lg:py-24 bg-white">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <flux:heading size="xl" level="2">{{ __('pages.landing.search.title') }}</flux:heading>
                <flux:text class="mt-4 max-w-2xl mx-auto">
                    {{ __('pages.landing.search.description') }}
                </flux:text>
            </div>

            {{-- Search Form --}}
            <div class="bg-gewo-grey-50 rounded-2xl p-6 lg:p-8 max-w-4xl mx-auto">
                <form class="grid md:grid-cols-4 gap-4">
                    <flux:field>
                        <flux:label>{{ __('pages.landing.search.form.location') }}</flux:label>
                        <flux:select placeholder="{{ __('pages.landing.search.form.location_placeholder') }}">
                            <flux:select.option>{{ __('pages.landing.search.form.location_placeholder') }}</flux:select.option>
                            @foreach(__('pages.landing.search.locations') as $location)
                                <flux:select.option>{{ $location }}</flux:select.option>
                            @endforeach
                        </flux:select>
                    </flux:field>
                    <flux:field>
                        <flux:label>{{ __('pages.landing.search.form.rooms') }}</flux:label>
                        <flux:select placeholder="{{ __('pages.landing.search.form.rooms_placeholder') }}">
                            <flux:select.option>{{ __('pages.landing.search.form.rooms_placeholder') }}</flux:select.option>
                            @foreach(__('pages.landing.search.form.rooms_options') as $option)
                                <flux:select.option>{{ $option }}</flux:select.option>
                            @endforeach
                        </flux:select>
                    </flux:field>
                    <flux:field>
                        <flux:label>{{ __('pages.landing.search.form.max_rent') }}</flux:label>
                        <flux:input type="number" placeholder="{{ __('pages.landing.search.form.max_rent_placeholder') }}" />
                    </flux:field>
                    <div class="flex items-end">
                        <flux:button variant="primary" class="w-full" icon="magnifying-glass">
                            {{ __('pages.landing.search.form.submit') }}
                        </flux:button>
                    </div>
                </form>
            </div>

            {{-- Featured Apartments --}}
            <div class="mt-12 grid md:grid-cols-3 gap-6">
                @foreach([
                    ['location' => 'Freital-Döhlen', 'rooms' => '2 Zimmer', 'size' => '58 m²', 'rent' => '320'],
                    ['location' => 'Freital-Potschappel', 'rooms' => '3 Zimmer', 'size' => '72 m²', 'rent' => '410'],
                    ['location' => 'Bannewitz', 'rooms' => '4 Zimmer', 'size' => '95 m²', 'rent' => '520'],
                ] as $apartment)
                    <flux:card>
                        <div class="aspect-video bg-linear-to-br from-gewo-grey-100 to-gewo-grey-50 rounded-lg mb-4 flex items-center justify-center">
                            <flux:icon name="home" class="size-12 text-gewo-grey-400" />
                        </div>
                        <flux:heading size="lg">{{ $apartment['location'] }}</flux:heading>
                        <flux:text size="sm" class="flex items-center gap-4 mt-2">
                            <span>{{ $apartment['rooms'] }}</span>
                            <span>•</span>
                            <span>{{ $apartment['size'] }}</span>
                        </flux:text>
                        <div class="mt-4 flex items-center justify-between">
                            <flux:heading class="text-xl text-accent">{{ $apartment['rent'] }} €<flux:text inline size="sm" class="font-normal">{{ __('pages.landing.search.per_month') }}</flux:text></flux:heading>
                            <flux:button variant="outline" size="sm">{{ __('pages.landing.search.details') }}</flux:button>
                        </div>
                    </flux:card>
                @endforeach
            </div>

            <div class="text-center mt-8">
                <flux:button variant="ghost" icon-trailing="arrow-right">
                    {{ __('pages.landing.search.show_all') }}
                </flux:button>
            </div>
        </div>
    </section>

    {{-- Services Section --}}
    <section id="service" class="py-16 lg:py-24 bg-gewo-grey-50">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <flux:heading size="xl" level="2">{{ __('pages.landing.services.title') }}</flux:heading>
                <flux:text class="mt-4 max-w-2xl mx-auto">
                    {{ __('pages.landing.services.description') }}
                </flux:text>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                @php
                    $serviceIcons = [
                        'repair' => 'wrench-screwdriver',
                        'dividend' => 'banknotes',
                        'community' => 'user-group',
                        'portal' => 'clipboard-document-list',
                        'security' => 'shield-check',
                        'modernization' => 'arrow-trending-up',
                    ];
                @endphp
                @foreach(__('pages.landing.services.items') as $key => $service)
                    <flux:card class="text-center p-8">
                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gewo-blue-100 text-accent mb-4">
                            <flux:icon name="{{ $serviceIcons[$key] ?? 'star' }}" class="size-8" />
                        </div>
                        <flux:heading size="lg">{{ $service['title'] }}</flux:heading>
                        <flux:text class="mt-2">
                            {{ $service['description'] }}
                        </flux:text>
                    </flux:card>
                @endforeach
            </div>
        </div>
    </section>

    {{-- News Section --}}
    <section class="py-16 lg:py-24 bg-white">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-8">
                <flux:heading size="xl" level="2">{{ __('pages.landing.news.title') }}</flux:heading>
                <flux:button variant="ghost" icon-trailing="arrow-right">
                    {{ __('pages.landing.news.show_all') }}
                </flux:button>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                @php
                    $badgeColors = [
                        'careers' => 'sky',
                        'dividend' => 'emerald',
                        'construction' => 'amber',
                    ];
                    $ctaKeys = [
                        'careers' => 'read_more',
                        'dividend' => 'view_details',
                        'construction' => 'view_project',
                    ];
                @endphp
                @foreach(__('pages.landing.news.items') as $key => $news)
                    <flux:card>
                        <flux:badge color="{{ $badgeColors[$key] ?? 'zinc' }}" class="mb-3">{{ $news['badge'] }}</flux:badge>
                        <flux:heading size="lg">{{ $news['title'] }}</flux:heading>
                        <flux:text class="mt-2">
                            {{ $news['description'] }}
                        </flux:text>
                        <flux:button variant="ghost" size="sm" class="mt-4" icon-trailing="arrow-right">
                            {{ __('pages.landing.news.' . ($ctaKeys[$key] ?? 'read_more')) }}
                        </flux:button>
                    </flux:card>
                @endforeach
            </div>
        </div>
    </section>

    {{-- About Section --}}
    <section id="ueber-uns" class="py-16 lg:py-24 bg-gewo-grey-900 text-white">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <div>
                    <flux:heading size="xl" level="2" class="text-white">{{ __('pages.landing.about.title') }}</flux:heading>
                    <flux:text size="lg" class="mt-6 text-gewo-grey-300">
                        {{ __('pages.landing.about.paragraphs.intro') }}
                    </flux:text>
                    <flux:text class="mt-4 text-gewo-grey-300">
                        {!! str_replace(
                            __('pages.landing.about.paragraphs.mission_highlight'),
                            '<strong class="text-white">' . __('pages.landing.about.paragraphs.mission_highlight') . '</strong>',
                            __('pages.landing.about.paragraphs.mission')
                        ) !!}
                    </flux:text>
                    <flux:text class="mt-4 text-gewo-grey-300">
                        {{ __('pages.landing.about.paragraphs.membership') }}
                    </flux:text>
                    <div class="mt-8">
                        <flux:button variant="primary" href="#kontakt" class="px-6! py-3!">
                            {{ __('pages.landing.about.cta') }}
                        </flux:button>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    @foreach(__('pages.landing.about.stats') as $stat)
                        <div class="bg-gewo-grey-800 rounded-xl p-6">
                            <flux:heading class="text-3xl text-accent">{{ $stat['value'] }}</flux:heading>
                            <flux:text class="text-gewo-grey-300 mt-1">{{ $stat['label'] }}</flux:text>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    {{-- Contact Section --}}
    <section id="kontakt" class="py-16 lg:py-24 bg-white">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-2 gap-12">
                <div>
                    <flux:heading size="xl" level="2">{{ __('pages.landing.contact.title') }}</flux:heading>
                    <flux:text class="mt-4">
                        {{ __('pages.landing.contact.description') }}
                    </flux:text>

                    <div class="mt-8 space-y-6">
                        @php
                            $contactIcons = [
                                'address' => 'map-pin',
                                'phone' => 'phone',
                                'email' => 'envelope',
                                'hours' => 'clock',
                            ];
                        @endphp
                        @foreach(__('pages.landing.contact.info') as $key => $info)
                            <div class="flex items-start gap-4">
                                <div class="shrink-0 w-12 h-12 rounded-full bg-gewo-blue-100 flex items-center justify-center">
                                    <flux:icon name="{{ $contactIcons[$key] ?? 'information-circle' }}" class="size-6 text-accent" />
                                </div>
                                <div>
                                    <flux:heading size="base" class="font-semibold">{{ $info['label'] }}</flux:heading>
                                    <flux:text>{!! nl2br(e($info['value'])) !!}</flux:text>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div>
                    <flux:card class="p-6 lg:p-8">
                        <flux:heading size="lg">{{ __('pages.landing.contact.form.title') }}</flux:heading>
                        <form class="mt-6 space-y-4">
                            <div class="grid sm:grid-cols-2 gap-4">
                                <flux:field>
                                    <flux:label>{{ __('pages.landing.contact.form.first_name') }}</flux:label>
                                    <flux:input placeholder="{{ __('pages.landing.contact.form.first_name_placeholder') }}" />
                                </flux:field>
                                <flux:field>
                                    <flux:label>{{ __('pages.landing.contact.form.last_name') }}</flux:label>
                                    <flux:input placeholder="{{ __('pages.landing.contact.form.last_name_placeholder') }}" />
                                </flux:field>
                            </div>
                            <flux:field>
                                <flux:label>{{ __('pages.landing.contact.form.email') }}</flux:label>
                                <flux:input type="email" placeholder="{{ __('pages.landing.contact.form.email_placeholder') }}" />
                            </flux:field>
                            <flux:field>
                                <flux:label>{{ __('pages.landing.contact.form.subject') }}</flux:label>
                                <flux:select>
                                    @foreach(__('pages.landing.contact.form.subject_options') as $option)
                                        <flux:select.option>{{ $option }}</flux:select.option>
                                    @endforeach
                                </flux:select>
                            </flux:field>
                            <flux:field>
                                <flux:label>{{ __('pages.landing.contact.form.message') }}</flux:label>
                                <flux:textarea rows="4" placeholder="{{ __('pages.landing.contact.form.message_placeholder') }}" />
                            </flux:field>
                            <flux:button variant="primary" class="w-full" icon="paper-airplane">
                                {{ __('pages.landing.contact.form.submit') }}
                            </flux:button>
                        </form>
                    </flux:card>
                </div>
            </div>
        </div>
    </section>
</x-layouts.public>
