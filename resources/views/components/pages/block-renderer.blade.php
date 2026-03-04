@props(['block', 'preview' => false])

@php
    $paddingClasses = match ($block->getSetting('padding', 'none')) {
        'sm' => 'p-2',
        'md' => 'p-4',
        'lg' => 'p-8',
        default => '',
    };

    $marginClasses = match ($block->getSetting('margin', 'none')) {
        'sm' => 'my-2',
        'md' => 'my-4',
        'lg' => 'my-8',
        default => '',
    };

    $textAlignClasses = match ($block->getSetting('text_align', 'left')) {
        'center' => 'text-center',
        'right' => 'text-right',
        default => 'text-left',
    };

    $backgroundClasses = match ($block->getSetting('background', 'transparent')) {
        'white' => 'bg-white dark:bg-zinc-800',
        'gray' => 'bg-zinc-100 dark:bg-zinc-700',
        'primary' => 'bg-accent/10',
        default => '',
    };

    $maxWidthClasses = match ($block->getSetting('max_width', 'full')) {
        'xs' => 'max-w-xs',
        'sm' => 'max-w-sm',
        'md' => 'max-w-md',
        'lg' => 'max-w-lg',
        'xl' => 'max-w-xl',
        '2xl' => 'max-w-2xl',
        '3xl' => 'max-w-3xl',
        '4xl' => 'max-w-4xl',
        '5xl' => 'max-w-5xl',
        'prose' => 'max-w-prose',
        default => '',
    };

    // Center the block if max_width is set and not full
    $maxWidthContainer = $block->getSetting('max_width', 'full') !== 'full' ? 'mx-auto' : '';
@endphp

<div class="{{ $paddingClasses }} {{ $marginClasses }} {{ $textAlignClasses }} {{ $backgroundClasses }} {{ $maxWidthClasses }} {{ $maxWidthContainer }}">
    @include('components.pages.blocks.'.$block->type->value, ['block' => $block, 'preview' => $preview])
</div>
