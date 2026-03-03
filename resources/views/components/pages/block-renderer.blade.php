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
@endphp

<div class="{{ $paddingClasses }} {{ $marginClasses }} {{ $textAlignClasses }} {{ $backgroundClasses }}">
    @include('components.pages.blocks.'.$block->type->value, ['block' => $block, 'preview' => $preview])
</div>
