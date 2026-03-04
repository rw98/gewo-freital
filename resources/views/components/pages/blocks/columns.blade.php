@props(['block', 'preview' => false])

@php
    $layout = $block->getContent('layout', '1/2-1/2');

    // Use a 12-column grid for more flexible layouts
    $gridClasses = 'grid-cols-1 md:grid-cols-12';

    // Column spans based on 12-column grid
    // Each layout defines spans that add up to 12
    $colSpanClasses = match ($layout) {
        '1/3-2/3' => ['md:col-span-4', 'md:col-span-8'],
        '2/3-1/3' => ['md:col-span-8', 'md:col-span-4'],
        '1/4-3/4' => ['md:col-span-3', 'md:col-span-9'],
        '3/4-1/4' => ['md:col-span-9', 'md:col-span-3'],
        '1/3-1/3-1/3' => ['md:col-span-4', 'md:col-span-4', 'md:col-span-4'],
        '1/4-1/4-1/4-1/4' => ['md:col-span-3', 'md:col-span-3', 'md:col-span-3', 'md:col-span-3'],
        default => ['md:col-span-6', 'md:col-span-6'], // 1/2-1/2
    };

    // For empty state placeholders
    $colCount = count($colSpanClasses);
@endphp

<div
    class="grid {{ $gridClasses }} gap-4 transition-colors"
    @if ($preview)
        data-grid-drop
        x-on:dragover.prevent.stop="dragOverTarget = 'columns-{{ $block->id }}'; $event.dataTransfer.dropEffect = draggingBlockId ? 'move' : 'copy'"
        x-on:dragleave.stop="if (!$el.contains($event.relatedTarget)) dragOverTarget = null"
        x-on:drop.prevent.stop="
            if (draggingBlockId) {
                $wire.moveBlockToParent(draggingBlockId, '{{ $block->id }}');
                draggingBlockId = null;
            } else if (draggingBlockType) {
                $wire.addBlockToParent(draggingBlockType, '{{ $block->id }}');
                draggingBlockType = null;
            }
            dragOverTarget = null;
        "
        :class="{ 'ring-2 ring-accent ring-inset rounded-lg': (draggingBlockType || draggingBlockId) && dragOverTarget === 'columns-{{ $block->id }}' }"
    @endif
>
    @forelse ($block->children as $index => $child)
        <div wire:key="child-{{ $child->id }}" class="col-span-1 {{ $colSpanClasses[$index] ?? 'md:col-span-6' }}">
            <x-pages.block-renderer :block="$child" :preview="$preview" />
        </div>
    @empty
        @if ($preview)
            @foreach ($colSpanClasses as $index => $spanClass)
                <div class="col-span-1 {{ $spanClass }} h-24 rounded-lg border-2 border-dashed border-zinc-300 dark:border-zinc-600 flex items-center justify-center">
                    <span class="text-zinc-400 text-sm">{{ __('pages.blocks.columns.column', ['num' => $index + 1]) }}</span>
                </div>
            @endforeach
        @endif
    @endforelse
</div>
