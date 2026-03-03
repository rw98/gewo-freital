@props(['block', 'preview' => false])

@php
    $title = $block->getContent('title', '');
    $content = $block->getContent('content', '');
    $image = $block->getContent('image', '');
@endphp

<flux:card>
    @if ($image)
        <img src="{{ $image }}" alt="{{ $title }}" class="w-full h-48 object-cover rounded-t-lg -mt-4 -mx-4 mb-4" style="width: calc(100% + 2rem);" />
    @endif

    @if ($title || $preview)
        <flux:heading size="lg" class="mb-2">
            {{ $title ?: __('pages.blocks.card.title_placeholder') }}
        </flux:heading>
    @endif

    @if ($content || $preview)
        <flux:text class="{{ !$content ? 'text-zinc-400 italic' : '' }}">
            {{ $content ?: __('pages.blocks.card.content_placeholder') }}
        </flux:text>
    @endif
</flux:card>
