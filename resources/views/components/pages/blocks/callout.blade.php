@props(['block', 'preview' => false])

@php
    $type = $block->getContent('type', 'info');
    $title = $block->getContent('title', '');
    $content = $block->getContent('content', '');

    $icon = match ($type) {
        'success' => 'check-circle',
        'warning' => 'exclamation-triangle',
        'danger' => 'x-circle',
        default => 'information-circle',
    };

    $color = match ($type) {
        'success' => 'green',
        'warning' => 'amber',
        'danger' => 'red',
        default => 'blue',
    };
@endphp

<flux:callout :icon="$icon" :color="$color">
    @if ($title)
        <flux:callout.heading>{{ $title }}</flux:callout.heading>
    @endif
    <flux:callout.text>
        {{ $content ?: ($preview ? __('pages.blocks.callout.placeholder') : '') }}
    </flux:callout.text>
</flux:callout>
