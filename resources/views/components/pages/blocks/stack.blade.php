@props(['block', 'preview' => false])

@php
    $gap = $block->getContent('gap', 4);
@endphp

<div
    class="flex flex-col gap-{{ $gap }} transition-colors"
    @if ($preview)
        data-grid-drop
        x-on:dragover.prevent.stop="dragOverTarget = 'stack-{{ $block->id }}'; $event.dataTransfer.dropEffect = draggingBlockId ? 'move' : 'copy'"
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
        :class="{ 'ring-2 ring-accent ring-inset rounded-lg': (draggingBlockType || draggingBlockId) && dragOverTarget === 'stack-{{ $block->id }}' }"
    @endif
>
    @forelse ($block->children as $child)
        <div wire:key="child-{{ $child->id }}">
            <x-pages.block-renderer :block="$child" :preview="$preview" />
        </div>
    @empty
        @if ($preview)
            <div class="h-24 rounded-lg border-2 border-dashed border-zinc-300 dark:border-zinc-600 flex items-center justify-center">
                <span class="text-zinc-400 text-sm">{{ __('pages.blocks.stack.empty') }}</span>
            </div>
        @endif
    @endforelse
</div>
