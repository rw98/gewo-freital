@props(['block', 'preview' => false])

@php
    $text = $block->getContent('text', '');
    $url = $block->getContent('url', '#');
    $variant = $block->getContent('variant', 'primary');
    $size = $block->getContent('size', 'base');
@endphp

@if ($preview && empty($text))
    <flux:button :variant="$variant" :size="$size" disabled>
        {{ __('pages.blocks.button.placeholder') }}
    </flux:button>
@else
    <flux:button :variant="$variant" :size="$size" :href="$url">
        {{ $text }}
    </flux:button>
@endif
