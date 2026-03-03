@props(['block', 'preview' => false])

@php
    $style = $block->getContent('style', 'solid');
    $styleClass = match ($style) {
        'dashed' => 'border-dashed',
        'dotted' => 'border-dotted',
        default => 'border-solid',
    };
@endphp

<hr class="border-t border-zinc-200 dark:border-zinc-600 {{ $styleClass }}" />
