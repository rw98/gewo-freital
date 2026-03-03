@props(['block', 'preview' => false])

@php
    $features = $block->getContent('features', []);
@endphp

@if (empty($features) && $preview)
    <div class="grid md:grid-cols-3 gap-6">
        @for ($i = 0; $i < 3; $i++)
            <div class="text-center p-6 rounded-lg bg-zinc-50 dark:bg-zinc-800">
                <div class="w-12 h-12 mx-auto mb-4 rounded-full bg-accent/10 flex items-center justify-center">
                    <flux:icon name="star" class="size-6 text-accent" />
                </div>
                <flux:heading size="lg" class="mb-2">{{ __('pages.blocks.feature_grid.feature') }} {{ $i + 1 }}</flux:heading>
                <flux:text class="text-zinc-500">{{ __('pages.blocks.feature_grid.description_placeholder') }}</flux:text>
            </div>
        @endfor
    </div>
@else
    <div class="grid md:grid-cols-3 gap-6">
        @foreach ($features as $feature)
            <div class="text-center p-6 rounded-lg bg-zinc-50 dark:bg-zinc-800">
                @if (!empty($feature['icon']))
                    <div class="w-12 h-12 mx-auto mb-4 rounded-full bg-accent/10 flex items-center justify-center">
                        <flux:icon :name="$feature['icon']" class="size-6 text-accent" />
                    </div>
                @endif
                <flux:heading size="lg" class="mb-2">{{ $feature['title'] ?? '' }}</flux:heading>
                <flux:text class="text-zinc-500">{{ $feature['description'] ?? '' }}</flux:text>
            </div>
        @endforeach
    </div>
@endif
