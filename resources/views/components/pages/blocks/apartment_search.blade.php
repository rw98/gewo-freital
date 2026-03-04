@props(['block', 'preview' => false])

@php
    $content = $block->content ?? [];
    $title = $content['title'] ?? __('pages.landing.search.title');
    $description = $content['description'] ?? __('pages.landing.search.description');
    $showFeatured = $content['show_featured'] ?? true;
    $featuredCount = $content['featured_count'] ?? 3;
@endphp

@if ($preview)
    {{-- Preview mode: show static preview --}}
    <div class="bg-white rounded-lg">
        <div class="text-center mb-6">
            <flux:heading size="xl" level="2">{{ $title ?: __('pages.landing.search.title') }}</flux:heading>
            <flux:text class="mt-2">{{ $description ?: __('pages.landing.search.description') }}</flux:text>
        </div>

        {{-- Search Form Preview --}}
        <div class="bg-zinc-50 rounded-xl p-4 max-w-3xl mx-auto">
            <div class="grid md:grid-cols-4 gap-3">
                <flux:field>
                    <flux:label>{{ __('pages.landing.search.form.location') }}</flux:label>
                    <flux:select disabled>
                        <flux:select.option>{{ __('pages.landing.search.form.location_placeholder') }}</flux:select.option>
                    </flux:select>
                </flux:field>
                <flux:field>
                    <flux:label>{{ __('pages.landing.search.form.rooms') }}</flux:label>
                    <flux:select disabled>
                        <flux:select.option>{{ __('pages.landing.search.form.rooms_placeholder') }}</flux:select.option>
                    </flux:select>
                </flux:field>
                <flux:field>
                    <flux:label>{{ __('pages.landing.search.form.max_rent') }}</flux:label>
                    <flux:select disabled>
                        <flux:select.option>{{ __('pages.landing.search.form.max_rent_placeholder') }}</flux:select.option>
                    </flux:select>
                </flux:field>
                <div class="flex items-end">
                    <flux:button variant="primary" class="w-full" icon="magnifying-glass" disabled>
                        {{ __('pages.landing.search.form.submit') }}
                    </flux:button>
                </div>
            </div>
        </div>

        @if ($showFeatured)
            {{-- Featured Apartments Preview --}}
            <div class="mt-8 grid md:grid-cols-3 gap-4">
                @for ($i = 0; $i < min($featuredCount, 3); $i++)
                    <div class="bg-zinc-50 rounded-lg p-4">
                        <div class="aspect-video bg-zinc-200 rounded-lg mb-3 flex items-center justify-center">
                            <flux:icon name="home" class="size-8 text-zinc-400" />
                        </div>
                        <div class="h-4 bg-zinc-200 rounded w-2/3 mb-2"></div>
                        <div class="h-3 bg-zinc-100 rounded w-1/2"></div>
                    </div>
                @endfor
            </div>
        @endif
    </div>
@else
    {{-- Public mode: use Livewire component with custom settings --}}
    <livewire:pages.apartment-search-block
        :title="$title"
        :description="$description"
        :show-featured="$showFeatured"
        :featured-count="$featuredCount"
    />
@endif
