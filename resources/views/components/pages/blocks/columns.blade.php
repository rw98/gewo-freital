@props(['block', 'preview' => false])

@php
    $layout = $block->getContent('layout', '1/2-1/2');

    // Responsive grid classes based on layout
    $gridClasses = match ($layout) {
        '1/3-2/3' => 'grid-cols-1 md:grid-cols-3',
        '2/3-1/3' => 'grid-cols-1 md:grid-cols-3',
        '1/4-3/4' => 'grid-cols-1 md:grid-cols-4',
        '3/4-1/4' => 'grid-cols-1 md:grid-cols-4',
        '1/3-1/3-1/3' => 'grid-cols-1 sm:grid-cols-2 md:grid-cols-3',
        '1/4-1/4-1/4-1/4' => 'grid-cols-1 sm:grid-cols-2 md:grid-cols-4',
        default => 'grid-cols-1 sm:grid-cols-2',
    };

    $colSpans = match ($layout) {
        '1/3-2/3' => [1, 2],
        '2/3-1/3' => [2, 1],
        '1/4-3/4' => [1, 3],
        '3/4-1/4' => [3, 1],
        '1/3-1/3-1/3' => [1, 1, 1],
        '1/4-1/4-1/4-1/4' => [1, 1, 1, 1],
        default => [1, 1],
    };
@endphp

<div
    class="grid {{ $gridClasses }} gap-4 transition-colors"
    @if ($preview)
        data-grid-drop
        x-on:dragover.prevent.stop="dragOverTarget = 'columns-{{ $block->id }}'; $event.dataTransfer.dropEffect = 'copy'"
        x-on:dragleave.stop="if (!$el.contains($event.relatedTarget)) dragOverTarget = null"
        x-on:drop.prevent.stop="if (draggingBlockType) { $wire.addBlockToParent(draggingBlockType, '{{ $block->id }}'); draggingBlockType = null; dragOverTarget = null; }"
        :class="{ 'ring-2 ring-accent ring-inset rounded-lg': draggingBlockType && dragOverTarget === 'columns-{{ $block->id }}' }"
    @endif
>
    @forelse ($block->children as $index => $child)
        @php
            $span = $colSpans[$index] ?? 1;
            // On mobile, all columns are full width; on md+, use the defined spans
            $spanClass = $span > 1 ? 'md:col-span-' . $span : '';
        @endphp
        <div wire:key="child-{{ $child->id }}" class="{{ $spanClass }}">
            <x-pages.block-renderer :block="$child" :preview="$preview" />
        </div>
    @empty
        @if ($preview)
            @foreach ($colSpans as $index => $span)
                @php
                    $spanClass = $span > 1 ? 'md:col-span-' . $span : '';
                @endphp
                <div class="{{ $spanClass }} h-24 bg-zinc-100 dark:bg-zinc-700 rounded-lg border-2 border-dashed border-zinc-300 dark:border-zinc-600 flex items-center justify-center">
                    <span class="text-zinc-400 text-sm">{{ __('pages.blocks.columns.column', ['num' => $index + 1]) }}</span>
                </div>
            @endforeach
        @endif
    @endforelse
</div>
