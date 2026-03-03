@props(['block', 'preview' => false])

@php
    $height = $block->getContent('height', 'md');
    $heightClass = match ($height) {
        'xs' => 'h-4',
        'sm' => 'h-8',
        'md' => 'h-16',
        'lg' => 'h-24',
        'xl' => 'h-32',
        default => 'h-16',
    };
@endphp

<div class="{{ $heightClass }} {{ $preview ? 'bg-zinc-100 dark:bg-zinc-700 rounded border-2 border-dashed border-zinc-300 dark:border-zinc-600' : '' }}">
    @if ($preview)
        <div class="flex items-center justify-center h-full text-zinc-400 text-xs">
            {{ __('pages.blocks.spacer.label', ['height' => $height]) }}
        </div>
    @endif
</div>
