@props(['block', 'preview' => false])

@php
    $images = $block->getContent('images', []);
@endphp

@if (empty($images) && $preview)
    <div class="flex flex-col items-center justify-center h-40 bg-zinc-100 dark:bg-zinc-700 rounded-lg">
        <flux:icon name="squares-2x2" class="size-8 text-zinc-400" />
        <span class="text-zinc-400 text-sm mt-2">{{ __('pages.blocks.image_gallery.placeholder') }}</span>
    </div>
@else
    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
        @foreach ($images as $image)
            <div class="aspect-square overflow-hidden rounded-lg">
                <img src="{{ $image['src'] ?? '' }}" alt="{{ $image['alt'] ?? '' }}" class="w-full h-full object-cover hover:scale-105 transition-transform" />
            </div>
        @endforeach
    </div>
@endif
