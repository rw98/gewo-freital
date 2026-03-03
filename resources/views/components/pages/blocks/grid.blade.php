@props(['block', 'preview' => false])

@php
    $columns = $block->getContent('columns', 3);
    $gap = $block->getContent('gap', 4);

    // Responsive grid: single column on mobile, 2 on tablet, full columns on desktop
    $gridClasses = match ((int) $columns) {
        1 => 'grid-cols-1',
        2 => 'grid-cols-1 sm:grid-cols-2',
        3 => 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-3',
        4 => 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-4',
        5 => 'grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5',
        6 => 'grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6',
        default => 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-3',
    };
@endphp

<div
    class="grid {{ $gridClasses }} gap-{{ $gap }} transition-colors"
    @if ($preview)
        data-grid-drop
        x-on:dragover.prevent.stop="dragOverTarget = 'grid-{{ $block->id }}'; $event.dataTransfer.dropEffect = 'copy'"
        x-on:dragleave.stop="if (!$el.contains($event.relatedTarget)) dragOverTarget = null"
        x-on:drop.prevent.stop="if (draggingBlockType) { $wire.addBlockToParent(draggingBlockType, '{{ $block->id }}'); draggingBlockType = null; dragOverTarget = null; }"
        :class="{ 'ring-2 ring-accent ring-inset rounded-lg': draggingBlockType && dragOverTarget === 'grid-{{ $block->id }}' }"
    @endif
>
    @forelse ($block->children as $child)
        @php
            $childSpan = $child->column_span;
            // For grid children, span is relative to parent columns
            $childClasses = $childSpan > 1 ? 'sm:col-span-' . min($childSpan, $columns) : '';
        @endphp
        <div wire:key="child-{{ $child->id }}" class="{{ $childClasses }}">
            <x-pages.block-renderer :block="$child" :preview="$preview" />
        </div>
    @empty
        @if ($preview)
            @for ($i = 0; $i < $columns; $i++)
                <div class="h-24 bg-zinc-100 dark:bg-zinc-700 rounded-lg border-2 border-dashed border-zinc-300 dark:border-zinc-600 flex items-center justify-center">
                    <span class="text-zinc-400 text-sm">{{ __('pages.blocks.grid.column', ['num' => $i + 1]) }}</span>
                </div>
            @endfor
        @endif
    @endforelse
</div>
